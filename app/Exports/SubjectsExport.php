<?php

namespace App\Exports;

use App\Models\Subject;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class SubjectsExport implements FromCollection, WithHeadings, WithMapping
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
        $query = Subject::query();
        if ($this->from) $query->whereDate('created_at', '>=', $this->from);
        if ($this->to)   $query->whereDate('created_at', '<=', $this->to);
        return $query->get();
    }

    public function headings(): array
    {
        return ['Subject Code', 'Subject Name', 'Description', 'Added On'];
    }

    public function map($subject): array
    {
        return [
            $subject->subject_code,
            $subject->subject_name,
            $subject->description ?? '',
            $subject->created_at->format('Y-m-d'),
        ];
    }
}
