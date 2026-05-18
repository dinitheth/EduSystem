<?php
namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\{Student, Assignment, AssignmentSubmission, Mcq, McqSubmission, McqAnswer, McqOption, Mark, CourseContent, Subject};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StudentPortalController extends Controller
{
    private function student() {
        return Student::with('subjects')->findOrFail(session('student_id'));
    }

    public function dashboard() {
        $student     = $this->student();
        $assignments = Assignment::with(['teacher','subject'])
            ->where('class', $student->class)
            ->latest()->take(5)->get();
        $mcqs = Mcq::with(['teacher','subject'])
            ->where('class', $student->class)
            ->latest()->take(5)->get();
        $marks = Mark::with('subject')
            ->where('student_id', $student->id)
            ->latest()->take(10)->get();
        return view('portal.student.dashboard', compact('student','assignments','mcqs','marks'));
    }

    public function courses() {
        $student  = $this->student();
        // Load subjects with their assigned teachers and stats
        $subjects = $student->subjects()->with(['teachers'])->get();
        // Attach per-subject stats
        $subjects = $subjects->map(function ($sub) use ($student) {
            $sub->assignment_count = \App\Models\Assignment::where('subject_id', $sub->id)
                ->where('class', $student->class)->count();
            $sub->mcq_count = \App\Models\Mcq::where('subject_id', $sub->id)
                ->where('class', $student->class)->count();
            $sub->mark_avg = \App\Models\Mark::where('student_id', $student->id)
                ->where('subject_id', $sub->id)
                ->get()
                ->map(fn($m) => $m->total > 0 ? ($m->score / $m->total) * 100 : 0)
                ->avg();
            return $sub;
        });
        return view('portal.student.courses', compact('student','subjects'));
    }

    public function courseContent(Subject $subject) {
        $student = $this->student();
        // Ensure student is enrolled in this subject
        if (!$student->subjects->contains($subject->id)) abort(403);
        $contents = CourseContent::where('subject_id', $subject->id)
            ->with('teacher')
            ->orderBy('sort_order')->orderBy('created_at')
            ->get()
            ->map(function ($c) {
                return [
                    'id'           => $c->id,
                    'type'         => $c->type,
                    'title'        => $c->title,
                    'description'  => $c->description,
                    'content_text' => $c->content_text,
                    'url'          => $c->url,
                    'file_url'     => $c->file_path ? asset('storage/' . $c->file_path) : null,
                    'youtube_id'   => $c->youtube_id,
                    'youtube_embed'=> $c->youtube_embed,
                    'teacher'      => $c->teacher->full_name ?? '',
                    'created_at'   => $c->created_at->format('d M Y'),
                ];
            });
        return response()->json([
            'subject' => ['name' => $subject->subject_name, 'code' => $subject->subject_code],
            'contents' => $contents,
        ]);
    }

    public function assignments() {
        $student     = $this->student();
        $assignments = Assignment::with(['teacher','subject'])
            ->where('class', $student->class)
            ->latest()->get();
        $submissions = AssignmentSubmission::where('student_id', $student->id)
            ->get()->keyBy('assignment_id');
        return view('portal.student.assignments', compact('student','assignments','submissions'));
    }

    public function submitAssignment(Request $request, Assignment $assignment) {
        $student = $this->student();
        if ($student->class !== $assignment->class) abort(403);
        if (AssignmentSubmission::where('student_id',$student->id)->where('assignment_id',$assignment->id)->exists())
            return back()->with('error','You have already submitted this assignment.');
        $request->validate([
            'notes' => 'nullable|string|max:1000',
            'file'  => 'nullable|file|max:20480',
        ]);
        $path = null;
        if ($request->hasFile('file'))
            $path = $request->file('file')->store('assignment-submissions', 'public');
        AssignmentSubmission::create([
            'assignment_id' => $assignment->id,
            'student_id'    => $student->id,
            'notes'         => $request->notes,
            'file_path'     => $path,
            'submitted_at'  => now(),
        ]);
        return back()->with('success','Assignment submitted successfully!');
    }

    public function mcqs() {
        $student = $this->student();
        $mcqs    = Mcq::with(['teacher','subject'])
            ->where('class', $student->class)
            ->latest()->get();
        $done = McqSubmission::where('student_id', $student->id)->pluck('mcq_id')->toArray();
        return view('portal.student.mcqs', compact('student','mcqs','done'));
    }

    public function takeMcq(Mcq $mcq) {
        $student = $this->student();
        if ($student->class !== $mcq->class) abort(403);
        // Check global expiry
        if ($mcq->expires_at && now()->isAfter($mcq->expires_at)) {
            return redirect()->route('student.mcqs')->with('error', 'This MCQ test has expired and is no longer available.');
        }
        $already = McqSubmission::where('student_id', $student->id)->where('mcq_id', $mcq->id)->first();
        if ($already) return redirect()->route('student.mcq.result', $already->id);
        $mcq->load('questions.options');
        return view('portal.student.mcq_take', compact('student','mcq'));
    }

    public function submitMcq(Request $request, Mcq $mcq) {
        $student = $this->student();
        if ($student->class !== $mcq->class) abort(403);
        // Enforce global expiry on submit too
        if ($mcq->expires_at && now()->isAfter($mcq->expires_at)) {
            return redirect()->route('student.mcqs')->with('error', 'Time is up! This MCQ test has expired.');
        }
        if (McqSubmission::where('student_id', $student->id)->where('mcq_id', $mcq->id)->exists()) {
            return redirect()->route('student.mcqs')->with('error', 'Already submitted.');
        }

        $mcq->load('questions.options');
        $score = 0; $total = $mcq->questions->count();

        DB::transaction(function () use ($request, $mcq, $student, &$score, $total) {
            $submission = McqSubmission::create([
                'student_id'   => $student->id,
                'mcq_id'       => $mcq->id,
                'score'        => 0,
                'total'        => $total,
                'submitted_at' => now(),
            ]);

            foreach ($mcq->questions as $q) {
                $optionId  = $request->input("answers.{$q->id}");
                $isCorrect = false;
                if ($optionId) {
                    $option    = McqOption::find($optionId);
                    $isCorrect = $option && $option->is_correct;
                    if ($isCorrect) $score++;
                }
                McqAnswer::create([
                    'submission_id' => $submission->id,
                    'question_id'   => $q->id,
                    'option_id'     => $optionId,
                    'is_correct'    => $isCorrect,
                ]);
            }

            $submission->update(['score' => $score]);

            Mark::create([
                'student_id'        => $student->id,
                'subject_id'        => $mcq->subject_id,
                'mcq_submission_id' => $submission->id,
                'type'              => 'mcq',
                'title'             => $mcq->title,
                'score'             => $score,
                'total'             => $total,
            ]);
        });

        $sub = McqSubmission::where('student_id', $student->id)->where('mcq_id', $mcq->id)->first();
        return redirect()->route('student.mcq.result', $sub->id);
    }

    public function mcqResult(McqSubmission $submission) {
        $student = $this->student();
        if ($submission->student_id !== $student->id) abort(403);
        $submission->load('mcq.questions.options','answers');
        return view('portal.student.mcq_result', compact('student','submission'));
    }

    public function marks() {
        $student = $this->student();
        $marks   = Mark::with('subject')
            ->where('student_id', $student->id)
            ->latest()->get();
        return view('portal.student.marks', compact('student','marks'));
    }
}
