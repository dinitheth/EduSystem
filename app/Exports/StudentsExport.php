<?php

namespace App\Exports;

use App\Models\Student;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class StudentsExport implements FromCollection, WithHeadings, WithMapping
{
    protected $from;
    protected $to;

    public function __construct($from = null, $to = null)
    {
        $this->from = $from;
        $this->to   = $to;
    }

    public function collection()
    {
        $query = Student::with('subjects');
        if ($this->from) $query->whereDate('created_at', '>=', $this->from);
        if ($this->to)   $query->whereDate('created_at', '<=', $this->to);
        return $query->get();
    }

    public function headings(): array
    {
        return ['Reg No', 'Full Name', 'Email', 'Phone', 'Date of Birth', 'Gender', 'Class', 'Status', 'Subjects', 'Registered On'];
    }

    public function map($student): array
    {
        return [
            $student->reg_no,
            $student->full_name,
            $student->email,
            $student->phone,
            $student->dob,
            $student->gender,
            $student->class,
            $student->status,
            $student->subjects->pluck('subject_name')->join(', '),
            $student->created_at->format('Y-m-d'),
        ];
    }
}
