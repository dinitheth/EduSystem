<?php

namespace App\Services;

use App\Models\Assignment;
use App\Models\Mcq;
use App\Models\PortalNotification;
use App\Models\Student;
use App\Models\Teacher;
use Illuminate\Support\Facades\Mail;

class PortalNotifier
{
    public function notifyStudentsAboutAssignment(Assignment $assignment): void
    {
        $assignment->loadMissing(['subject', 'teacher']);
        $url = route('student.assignments');
        $subjectName = $assignment->subject->subject_name ?? 'General';
        $teacherName = $assignment->teacher->full_name ?? 'your teacher';

        $this->targetStudents($assignment->class, $assignment->subject_id)->each(function (Student $student) use ($assignment, $url, $subjectName, $teacherName) {
            $this->create('student', $student->id, 'New assignment published', "{$assignment->title} has been posted for {$subjectName}.", $url);

            $this->sendEmail(
                $student->email,
                'New assignment: '.$assignment->title,
                "Hello {$student->full_name},\n\n{$teacherName} published a new assignment for {$subjectName}.\n\nTitle: {$assignment->title}\nDue date: ".($assignment->due_date ?: 'Not set')."\n\nOpen your student portal to view and submit it."
            );
        });
    }

    public function notifyStudentsAboutMcq(Mcq $mcq): void
    {
        $mcq->loadMissing(['subject', 'teacher']);
        $url = route('student.mcqs');
        $subjectName = $mcq->subject->subject_name ?? 'General';
        $teacherName = $mcq->teacher->full_name ?? 'your teacher';

        $this->targetStudents($mcq->class, $mcq->subject_id)->each(function (Student $student) use ($mcq, $url, $subjectName, $teacherName) {
            $this->create('student', $student->id, 'New MCQ test available', "{$mcq->title} is now available for {$subjectName}.", $url);

            $this->sendEmail(
                $student->email,
                'New MCQ test: '.$mcq->title,
                "Hello {$student->full_name},\n\n{$teacherName} published a new MCQ test for {$subjectName}.\n\nTitle: {$mcq->title}\nTime limit: ".($mcq->time_limit ? $mcq->time_limit.' minutes' : 'Not set')."\n\nOpen your student portal to take the test."
            );
        });
    }

    public function notifyTeacherAboutAssignmentSubmission(Assignment $assignment, Student $student): void
    {
        $assignment->loadMissing('teacher');
        $teacher = $assignment->teacher;
        if (!$teacher) return;

        $url = route('teacher.assignment.submissions', $assignment->id);
        $this->create('teacher', $teacher->id, 'Assignment submitted', "{$student->full_name} submitted {$assignment->title}.", $url);

        $this->sendEmail(
            $teacher->email,
            'Assignment submitted: '.$assignment->title,
            "Hello {$teacher->full_name},\n\n{$student->full_name} submitted an assignment.\n\nAssignment: {$assignment->title}\nStudent: {$student->full_name} ({$student->reg_no})\n\nOpen your teacher portal to review it."
        );
    }

    public function notifyTeacherAboutMcqSubmission(Mcq $mcq, Student $student, int $score, int $total): void
    {
        $mcq->loadMissing('teacher');
        $teacher = $mcq->teacher;
        if (!$teacher) return;

        $url = route('teacher.mcq.results', $mcq->id);
        $this->create('teacher', $teacher->id, 'MCQ submitted', "{$student->full_name} submitted {$mcq->title} ({$score}/{$total}).", $url);

        $this->sendEmail(
            $teacher->email,
            'MCQ submitted: '.$mcq->title,
            "Hello {$teacher->full_name},\n\n{$student->full_name} submitted an MCQ test.\n\nMCQ: {$mcq->title}\nStudent: {$student->full_name} ({$student->reg_no})\nScore: {$score}/{$total}\n\nOpen your teacher portal to view the full result."
        );
    }

    private function create(string $type, int $id, string $title, string $body, string $url): void
    {
        PortalNotification::create([
            'recipient_type' => $type,
            'recipient_id' => $id,
            'title' => $title,
            'body' => $body,
            'url' => $url,
        ]);
    }

    private function targetStudents(string $class, ?int $subjectId)
    {
        return Student::query()
            ->where('class', $class)
            ->when($subjectId, fn($query) => $query->whereHas('subjects', fn($subjectQuery) => $subjectQuery->where('subjects.id', $subjectId)))
            ->whereNotNull('email')
            ->get();
    }

    private function sendEmail(?string $email, string $subject, string $body): void
    {
        if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return;
        }

        try {
            Mail::raw($body, fn($message) => $message->to($email)->subject($subject));
        } catch (\Throwable $exception) {
            report($exception);
        }
    }
}
