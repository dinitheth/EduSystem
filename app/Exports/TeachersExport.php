<?php

namespace App\Exports;

use App\Models\Teacher;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class TeachersExport implements FromCollection, WithHeadings, WithMapping
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
        $query = Teacher::with('subjects');
        if ($this->from) $query->whereDate('created_at', '>=', $this->from);
        if ($this->to)   $query->whereDate('created_at', '<=', $this->to);
        return $query->get();
    }

    public function headings(): array
    {
        return ['Employee No', 'Full Name', 'Email', 'Phone', 'Specialization', 'Subjects Taught', 'Added On'];
    }

    public function map($teacher): array
    {
        return [
            $teacher->employee_no,
            $teacher->full_name,
            $teacher->email,
            $teacher->phone,
            $teacher->specialization,
            $teacher->subjects->pluck('subject_name')->join(', '),
            $teacher->created_at->format('Y-m-d'),
        ];
    }
}
