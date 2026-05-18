<?php

namespace App\Exports;

use App\Models\AssignmentSubmission;
use App\Models\Assignment;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AssignmentSubmissionsExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $assignmentId;

    public function __construct($assignmentId)
    {
        $this->assignmentId = $assignmentId;
    }

    public function collection()
    {
        return AssignmentSubmission::with('student')
            ->where('assignment_id', $this->assignmentId)
            ->get();
    }

    public function headings(): array
    {
        return [
            'Student Name',
            'Registration No',
            'Submitted At',
            'Marks Obtained',
            'Max Marks',
            'Grade',
            'Feedback',
            'Student Notes'
        ];
    }

    public function map($submission): array
    {
        return [
            $submission->student ? $submission->student->full_name : 'Unknown',
            $submission->student ? $submission->student->reg_no : 'Unknown',
            $submission->submitted_at ? \Carbon\Carbon::parse($submission->submitted_at)->format('Y-m-d H:i') : 'Not Submitted',
            $submission->marks ?? '-',
            $submission->max_marks ?? '-',
            $submission->grade_letter,
            $submission->feedback ?? '-',
            $submission->notes ?? '-'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
