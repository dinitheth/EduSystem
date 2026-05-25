<?php
namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\{Teacher, Assignment, AssignmentSubmission, Student, Mcq, McqQuestion, McqOption, McqSubmission, Mark, Subject, CourseContent};
use App\Services\McqQuestionImporter;
use App\Services\PortalNotifier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Carbon;

class TeacherPortalController extends Controller
{
    private function teacher() {
        return Teacher::with('subjects')->findOrFail(session('teacher_id'));
    }

    private function syncAssignmentMark(AssignmentSubmission $submission): void {
        if (!$submission->is_graded) return;

        $submission->loadMissing('assignment');
        if (!$submission->assignment) return;

        $mark = Mark::where('assignment_submission_id', $submission->id)->first()
            ?: Mark::where('student_id', $submission->student_id)
                ->where('subject_id', $submission->assignment->subject_id)
                ->where('type', 'assignment')
                ->where('title', $submission->assignment->title)
                ->first();

        ($mark ?: new Mark())->fill([
            'student_id' => $submission->student_id,
            'subject_id' => $submission->assignment->subject_id,
            'assignment_submission_id' => $submission->id,
            'mcq_submission_id' => null,
            'type' => 'assignment',
            'title' => $submission->assignment->title,
            'score' => $submission->marks,
            'total' => $submission->max_marks,
        ])->save();
    }

    private function syncTeacherAssignmentMarks(Teacher $teacher): void {
        AssignmentSubmission::with('assignment')
            ->whereHas('assignment', fn($query) => $query->where('teacher_id', $teacher->id))
            ->where('status', 'graded')
            ->get()
            ->each(fn($submission) => $this->syncAssignmentMark($submission));
    }

    // ── Dashboard ─────────────────────────────────────────────────
    public function dashboard() {
        $teacher     = $this->teacher();
        $assignmentCount = Assignment::where('teacher_id', $teacher->id)->count();
        $mcqCount = Mcq::where('teacher_id', $teacher->id)->count();
        $assignments = Assignment::with('subject')->where('teacher_id', $teacher->id)->latest()->take(5)->get();
        $mcqs        = Mcq::with('subject')->where('teacher_id', $teacher->id)->latest()->take(5)->get();
        $results     = McqSubmission::whereHas('mcq', fn($q) => $q->where('teacher_id', $teacher->id))
            ->with(['student','mcq'])->latest()->take(6)->get();
        return view('portal.teacher.dashboard', compact('teacher','assignments','mcqs','results','assignmentCount','mcqCount'));
    }

    // ── Students list ─────────────────────────────────────────────
    public function students(Request $request) {
        $teacher  = $this->teacher();
        $search = trim((string) $request->query('search', ''));

        $studentQuery = Student::query()
            ->where('class', $teacher->class)
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($inner) use ($search) {
                    $inner->where('full_name', 'like', "%{$search}%")
                        ->orWhere('reg_no', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            });

        $studentCount = (clone $studentQuery)->count();
        $students = $studentQuery
            ->with(['subjects:id,subject_code,subject_name'])
            ->orderBy('full_name')
            ->paginate(18)
            ->withQueryString();

        return view('portal.teacher.students', compact('teacher','students','studentCount','search'));
    }

    // ── Courses / Content ─────────────────────────────────────────
    public function courses() {
        $teacher  = $this->teacher();
        $subjects = $teacher->subjects()->orderBy('subject_name')->get();
        return view('portal.teacher.courses', compact('teacher','subjects'));
    }

    public function courseContent(Subject $subject) {
        $teacher = $this->teacher();
        if (!$teacher->subjects->contains($subject->id)) abort(403);
        $contents = CourseContent::where('subject_id', $subject->id)
            ->with('teacher')->orderBy('sort_order')->orderBy('created_at')
            ->get()->map(function ($c) {
                return [
                    'id'            => $c->id,
                    'type'          => $c->type,
                    'title'         => $c->title,
                    'description'   => $c->description,
                    'content_text'  => $c->content_text,
                    'url'           => $c->url,
                    'file_url'      => $c->file_path ? asset('storage/' . $c->file_path) : null,
                    'youtube_id'    => $c->youtube_id,
                    'youtube_embed' => $c->youtube_embed,
                    'teacher'       => $c->teacher->full_name ?? '',
                    'created_at'    => $c->created_at->format('d M Y'),
                    'mine'          => $c->teacher_id === session('teacher_id'),
                ];
            });
        return response()->json([
            'subject'  => ['name' => $subject->subject_name, 'code' => $subject->subject_code],
            'contents' => $contents,
        ]);
    }

    public function storeCourseContent(Request $request, Subject $subject) {
        $teacher = $this->teacher();
        if (!$teacher->subjects->contains($subject->id)) abort(403);
        $request->validate([
            'type'         => 'required|in:text,pdf,video,link,youtube',
            'title'        => 'required|string|max:255',
            'description'  => 'nullable|string',
            'content_text' => 'nullable|string',
            'url'          => 'nullable|url',
            'file'         => 'nullable|file|max:51200',
        ]);
        $filePath = null;
        if ($request->hasFile('file')) {
            $folder = match($request->type) {
                'pdf'   => 'course-content/pdfs',
                'video' => 'course-content/videos',
                default => 'course-content/files',
            };
            $filePath = $request->file('file')->store($folder, 'public');
        }
        CourseContent::create([
            'subject_id'   => $subject->id,
            'teacher_id'   => $teacher->id,
            'type'         => $request->type,
            'title'        => $request->title,
            'description'  => $request->description,
            'content_text' => $request->content_text,
            'file_path'    => $filePath,
            'url'          => $request->url,
            'sort_order'   => CourseContent::where('subject_id', $subject->id)->count(),
        ]);
        return response()->json(['success' => true, 'message' => 'Content added successfully!']);
    }

    public function deleteCourseContent(Subject $subject, CourseContent $content) {
        $teacher = $this->teacher();
        if ($content->teacher_id !== $teacher->id) abort(403);
        if ($content->file_path) Storage::disk('public')->delete($content->file_path);
        $content->delete();
        return response()->json(['success' => true]);
    }

    // ── Assignments ───────────────────────────────────────────────
    public function assignments() {
        $teacher     = $this->teacher();
        $assignments = Assignment::with('subject')
            ->withCount('submissions')
            ->where('teacher_id', $teacher->id)->latest()->get();
        $subjects    = $teacher->subjects()->orderBy('subject_name')->get();
        return view('portal.teacher.assignments', compact('teacher','assignments','subjects'));
    }

    public function storeAssignment(Request $request) {
        $teacher = $this->teacher();
        $request->validate([
            'title'       => 'required|string|max:255',
            'subject_id'  => 'nullable|exists:subjects,id',
            'description' => 'nullable|string',
            'due_date'    => 'nullable|date',
            'file'        => 'nullable|file|mimes:pdf|max:20480',
        ]);
        if ($request->subject_id && !$teacher->subjects->pluck('id')->contains($request->subject_id))
            return back()->with('error', 'Invalid subject selection.');
        $path = null;
        if ($request->hasFile('file'))
            $path = $request->file('file')->store('assignments', 'public');
        $assignment = Assignment::create([
            'teacher_id'  => $teacher->id,
            'subject_id'  => $request->subject_id,
            'class'       => $teacher->class,
            'title'       => $request->title,
            'description' => $request->description,
            'file_path'   => $path,
            'due_date'    => $request->due_date,
        ]);
        app(PortalNotifier::class)->notifyStudentsAboutAssignment($assignment);
        return back()->with('success', 'Assignment posted successfully!');
    }

    public function updateAssignment(Request $request, Assignment $assignment) {
        $teacher = $this->teacher();
        if ($assignment->teacher_id !== $teacher->id) abort(403);

        $request->validate([
            'title'       => 'required|string|max:255',
            'subject_id'  => 'nullable|exists:subjects,id',
            'description' => 'nullable|string',
            'due_date'    => 'nullable|date',
            'file'        => 'nullable|file|mimes:pdf|max:20480',
        ]);
        if ($request->subject_id && !$teacher->subjects->pluck('id')->contains($request->subject_id))
            return back()->with('error', 'Invalid subject selection.');

        $path = $assignment->file_path;
        if ($request->hasFile('file')) {
            if ($assignment->file_path) Storage::disk('public')->delete($assignment->file_path);
            $path = $request->file('file')->store('assignments', 'public');
        }

        $assignment->update([
            'subject_id'  => $request->subject_id,
            'title'       => $request->title,
            'description' => $request->description,
            'file_path'   => $path,
            'due_date'    => $request->due_date,
        ]);

        $assignment->submissions()->where('status', 'graded')->get()->each(fn($submission) => $this->syncAssignmentMark($submission));

        return back()->with('success', 'Assignment updated successfully!');
    }

    public function deleteAssignment(Assignment $assignment) {
        $teacher = $this->teacher();
        if ($assignment->teacher_id !== $teacher->id) abort(403);

        $assignment->submissions()->get()->each(function ($submission) {
            if ($submission->file_path) Storage::disk('public')->delete($submission->file_path);
            Mark::where('assignment_submission_id', $submission->id)->delete();
        });
        if ($assignment->file_path) Storage::disk('public')->delete($assignment->file_path);
        $assignment->delete();

        return back()->with('success', 'Assignment deleted successfully!');
    }

    public function viewSubmissions(Assignment $assignment) {
        $teacher = $this->teacher();
        if ($assignment->teacher_id !== $teacher->id) abort(403);
        $submissions = AssignmentSubmission::where('assignment_id', $assignment->id)
            ->with('student')->latest('submitted_at')->get();
        return view('portal.teacher.assignment_submissions', compact('teacher','assignment','submissions'));
    }

    public function gradeSubmission(Request $request, Assignment $assignment, AssignmentSubmission $submission) {
        $teacher = $this->teacher();
        if ($assignment->teacher_id !== $teacher->id) abort(403);
        $request->validate([
            'marks'     => 'required|numeric|min:0',
            'max_marks' => 'required|integer|min:1|max:1000',
            'feedback'  => 'nullable|string|max:1000',
        ]);
        $submission->update([
            'marks'     => $request->marks,
            'max_marks' => $request->max_marks,
            'feedback'  => $request->feedback,
            'status'    => 'graded',
            'graded_by' => $teacher->id,
            'graded_at' => now(),
        ]);

        $submission->refresh();
        $this->syncAssignmentMark($submission);

        return back()->with('success', 'Marks saved for '.$submission->student->full_name.'!');
    }

    public function downloadAllSubmissions(Assignment $assignment) {
        $teacher = $this->teacher();
        if ($assignment->teacher_id !== $teacher->id) abort(403);
        $submissions = AssignmentSubmission::where('assignment_id', $assignment->id)
            ->with('student')->whereNotNull('file_path')->get();
        if ($submissions->isEmpty())
            return back()->with('error', 'No file submissions to download.');
        $zipName = 'submissions_'.str()->slug($assignment->title).'_'.now()->format('YmdHis').'.zip';
        $zipPath = storage_path('app/temp/'.$zipName);
        if (!is_dir(storage_path('app/temp'))) mkdir(storage_path('app/temp'), 0755, true);
        $zip = new \ZipArchive();
        if ($zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== true)
            return back()->with('error', 'Could not create ZIP file.');
        foreach ($submissions as $sub) {
            $filePath = Storage::disk('public')->path($sub->file_path);
            if (file_exists($filePath)) {
                $ext  = pathinfo($filePath, PATHINFO_EXTENSION);
                $name = str()->slug($sub->student->full_name).'_'.$sub->student->reg_no.'.'.$ext;
                $zip->addFile($filePath, $name);
            }
        }
        $zip->close();
        return response()->download($zipPath, $zipName)->deleteFileAfterSend();
    }

    public function downloadSubmissionsExcel(Assignment $assignment) {
        $teacher = $this->teacher();
        if ($assignment->teacher_id !== $teacher->id) abort(403);
        
        $fileName = 'submissions_'.str()->slug($assignment->title).'_'.now()->format('YmdHis').'.xlsx';
        return \Excel::download(new \App\Exports\AssignmentSubmissionsExport($assignment->id), $fileName);
    }

    // ── MCQs ──────────────────────────────────────────────────────
    public function mcqs() {
        $teacher = $this->teacher();
        $mcqs    = Mcq::with(['subject','submissions'])->where('teacher_id', $teacher->id)->latest()->get();
        return view('portal.teacher.mcqs', compact('teacher','mcqs'));
    }

    public function createMcq() {
        $teacher  = $this->teacher();
        $subjects = $teacher->subjects()->orderBy('subject_name')->get();
        return view('portal.teacher.mcq_create', compact('teacher','subjects'));
    }

    public function importMcqQuestions(Request $request, McqQuestionImporter $importer) {
        $this->teacher();
        $request->validate([
            'file' => 'required|file|mimes:pdf,docx,xlsx,xls|max:10240',
        ]);

        try {
            $questions = $importer->import($request->file('file'));
        } catch (\Throwable $exception) {
            report($exception);
            return response()->json([
                'message' => 'Could not read questions from this file. Please check the document format and try again.',
                'questions' => [],
            ], 422, [], JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
        }

        if (empty($questions)) {
            return response()->json([
                'message' => 'No valid MCQ questions found. Use clear question lines and option lines such as A), B), C), D).',
                'questions' => [],
            ], 422, [], JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
        }

        return response()->json([
            'message' => count($questions).' question(s) imported. Please select the correct answer for each question before publishing.',
            'questions' => $questions,
        ], 200, [], JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
    }

    public function storeMcq(Request $request) {
        $teacher = $this->teacher();
        $request->validate([
            'title'                 => 'required|string|max:255',
            'subject_id'            => 'nullable|exists:subjects,id',
            'starts_at'             => 'nullable|date',
            'time_limit'            => 'nullable|integer|min:1|max:1440',
            'custom_time_limit'     => 'nullable|integer|min:1|max:1440',
            'questions'             => 'required|array|min:1',
            'questions.*.question'  => 'required|string',
            'questions.*.options'   => 'required|array|min:2',
            'questions.*.options.*' => 'required|string',
            'questions.*.correct'   => 'required|integer',
        ]);
        if ($request->subject_id && !$teacher->subjects->pluck('id')->contains($request->subject_id))
            return back()->with('error', 'Invalid subject selection.');

        $totalMinutes = $request->integer('time_limit') ?: null;
        if ($totalMinutes === -1) {
            $totalMinutes = $request->integer('custom_time_limit') ?: null;
        }
        if ($request->input('time_limit') === '-1' && !$totalMinutes) {
            return back()->withInput()->with('error', 'Enter a custom time limit in minutes.');
        }
        $startsAt = $request->filled('starts_at') ? Carbon::parse($request->starts_at) : now();
        $expiresAt = $totalMinutes ? $startsAt->copy()->addMinutes($totalMinutes) : null;

        $mcq = Mcq::create([
            'teacher_id' => $teacher->id,
            'subject_id' => $request->subject_id,
            'class'      => $teacher->class,
            'title'      => $request->title,
            'time_limit' => $totalMinutes,
            'starts_at'  => $startsAt,
            'expires_at' => $expiresAt,
        ]);
        foreach ($request->questions as $i => $qData) {
            $question = McqQuestion::create(['mcq_id' => $mcq->id, 'question' => $qData['question'], 'order' => $i]);
            foreach ($qData['options'] as $j => $optText)
                McqOption::create(['question_id' => $question->id, 'option_text' => $optText, 'is_correct' => ($j == $qData['correct'])]);
        }
        app(PortalNotifier::class)->notifyStudentsAboutMcq($mcq);
        return redirect()->route('teacher.mcqs')->with('success', 'MCQ published successfully!');
    }

    // ── Results & Marks ──────────────────────────────────────────
    public function results(Mcq $mcq) {
        $teacher = $this->teacher();
        if ($mcq->teacher_id !== $teacher->id) abort(403);
        $submissions = $mcq->submissions()->with('student')->latest()->get();
        return view('portal.teacher.mcq_results', compact('teacher','mcq','submissions'));
    }

    public function marks() {
        $teacher = $this->teacher();
        $this->syncTeacherAssignmentMarks($teacher);
        $myMcqIds = Mcq::where('teacher_id', $teacher->id)->pluck('id');
        $mySubmissionIds = McqSubmission::whereIn('mcq_id', $myMcqIds)->pluck('id');
        $assignmentSubmissionIds = AssignmentSubmission::whereHas('assignment', fn($query) => $query->where('teacher_id', $teacher->id))->pluck('id');
        $marks = Mark::with(['student','subject'])
            ->where(function ($query) use ($mySubmissionIds, $assignmentSubmissionIds) {
                $query->whereIn('mcq_submission_id', $mySubmissionIds)
                    ->orWhereIn('assignment_submission_id', $assignmentSubmissionIds);
            })
            ->latest()->get();
        return view('portal.teacher.marks', compact('teacher','marks'));
    }
}
