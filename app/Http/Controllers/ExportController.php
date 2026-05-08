<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Teacher;
use App\Models\Subject;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Http\Request;

class ExportController extends Controller
{
    public function index()
    {
        $counts = [
            'students' => Student::count(),
            'teachers' => Teacher::count(),
            'subjects' => Subject::count(),
        ];
        $subjects = Subject::orderBy('subject_name')->get(['id', 'subject_name']);
        $history  = session('export_history', []);

        return view('export.index', compact('counts', 'subjects', 'history'));
    }

    public function preview(Request $request)
    {
        $type    = $request->data_type ?? 'students';
        $filters = $request->except(['_token', 'data_type']);
        $count   = $this->buildQuery($type, $filters)->count();
        return response()->json(['count' => $count]);
    }

    public function export(Request $request)
    {
        $request->validate([
            'data_type' => 'required|in:students,teachers,subjects',
            'format'    => 'required|in:csv,xlsx',
        ]);

        $type    = $request->data_type;
        $format  = $request->format;
        $columns = $request->input('columns', []);
        $sortBy  = $request->input('sort_by', 'created_at');
        $sortDir = $request->input('sort_dir', 'asc');
        $ids     = $request->input('ids', []);
        $filters = $request->except(['_token','data_type','format','columns','sort_by','sort_dir','ids']);

        $query = $this->buildQuery($type, $filters);
        if (!empty($ids)) $query->whereIn('id', $ids);
        $query->orderBy($sortBy, $sortDir);

        if (in_array($type, ['students', 'teachers'])) {
            $query->with('subjects');
        }

        $records  = $query->get();
        $headings = $this->getHeadings($type, $columns);
        $rows     = $this->mapRows($type, $records, $columns);

        $controller = $this;
        $export = new class($headings, $rows) implements FromCollection, WithHeadings {
            public function __construct(private array $hdrs, private $rws) {}
            public function collection() { return collect($this->rws); }
            public function headings(): array { return $this->hdrs; }
        };

        $filename   = $type . '_export_' . now()->format('Ymd_His') . '.' . $format;
        $writerType = $format === 'csv' ? \Maatwebsite\Excel\Excel::CSV : \Maatwebsite\Excel\Excel::XLSX;

        // Store in history (session)
        $history = session('export_history', []);
        array_unshift($history, [
            'type'     => ucfirst($type),
            'format'   => strtoupper($format),
            'count'    => $records->count(),
            'filename' => $filename,
            'time'     => now()->format('d M Y, H:i'),
        ]);
        session(['export_history' => array_slice($history, 0, 5)]);

        return Excel::download($export, $filename, $writerType);
    }

    private function buildQuery(string $type, array $filters)
    {
        switch ($type) {
            case 'students':
                $q = Student::query();
                if (!empty($filters['search']))      $q->where(fn($x) => $x->where('full_name','like','%'.$filters['search'].'%')->orWhere('reg_no','like','%'.$filters['search'].'%'));
                if (!empty($filters['status']))      $q->where('status', $filters['status']);
                if (!empty($filters['gender']))      $q->where('gender', $filters['gender']);
                if (!empty($filters['subject_id']))  $q->whereHas('subjects', fn($x) => $x->where('subjects.id', $filters['subject_id']));
                if (!empty($filters['date_from']))   $q->whereDate('created_at', '>=', $filters['date_from']);
                if (!empty($filters['date_to']))     $q->whereDate('created_at', '<=', $filters['date_to']);
                if (!empty($filters['has_email']))   $q->whereNotNull('email')->where('email','!=','');
                if (!empty($filters['has_phone']))   $q->whereNotNull('phone')->where('phone','!=','');
                return $q;

            case 'teachers':
                $q = Teacher::query();
                if (!empty($filters['search']))           $q->where(fn($x) => $x->where('full_name','like','%'.$filters['search'].'%')->orWhere('employee_no','like','%'.$filters['search'].'%'));
                if (!empty($filters['department']))       $q->where('department','like','%'.$filters['department'].'%');
                if (!empty($filters['employment_status'])) $q->where('employment_status', $filters['employment_status']);
                if (!empty($filters['subject_id']))       $q->whereHas('subjects', fn($x) => $x->where('subjects.id', $filters['subject_id']));
                if (!empty($filters['date_from']))        $q->whereDate('created_at', '>=', $filters['date_from']);
                if (!empty($filters['date_to']))          $q->whereDate('created_at', '<=', $filters['date_to']);
                return $q;

            case 'subjects':
                $q = Subject::query();
                if (!empty($filters['search']))    $q->where(fn($x) => $x->where('subject_name','like','%'.$filters['search'].'%')->orWhere('subject_code','like','%'.$filters['search'].'%'));
                if (!empty($filters['category']))  $q->where('category','like','%'.$filters['category'].'%');
                if (isset($filters['is_active']) && $filters['is_active'] !== '') $q->where('is_active', $filters['is_active']);
                return $q;
        }
        return Student::query();
    }

    private function getHeadings(string $type, array $columns): array
    {
        $all = [
            'students' => [
                'reg_no'=>'Reg No','full_name'=>'Full Name','email'=>'Email','phone'=>'Phone',
                'dob'=>'Date of Birth','gender'=>'Gender','status'=>'Status',
                'subjects'=>'Subjects','created_at'=>'Registered On',
            ],
            'teachers' => [
                'employee_no'=>'Employee No','full_name'=>'Full Name','email'=>'Email','phone'=>'Phone',
                'specialization'=>'Specialization','department'=>'Department',
                'employment_status'=>'Employment Status','subjects'=>'Subjects Taught','created_at'=>'Added On',
            ],
            'subjects' => [
                'subject_code'=>'Subject Code','subject_name'=>'Subject Name',
                'description'=>'Description','category'=>'Category','is_active'=>'Active','created_at'=>'Added On',
            ],
        ];
        $map = $all[$type] ?? [];
        if (empty($columns)) return array_values($map);
        return array_values(array_intersect_key($map, array_flip($columns)));
    }

    private function mapRows(string $type, $records, array $columns): array
    {
        $rows = [];
        foreach ($records as $r) {
            $row = [];
            $keys = empty($columns) ? array_keys($this->getHeadings($type, []) + []) : $columns;

            // Re-derive keys from headings order
            $allKeys = [
                'students' => ['reg_no','full_name','email','phone','dob','gender','status','subjects','created_at'],
                'teachers' => ['employee_no','full_name','email','phone','specialization','department','employment_status','subjects','created_at'],
                'subjects' => ['subject_code','subject_name','description','category','is_active','created_at'],
            ];
            $useKeys = empty($columns) ? $allKeys[$type] : array_values(array_filter($allKeys[$type], fn($k) => in_array($k, $columns)));

            foreach ($useKeys as $key) {
                if ($key === 'subjects') {
                    $row[] = $r->subjects ? $r->subjects->pluck('subject_name')->join(', ') : '';
                } elseif ($key === 'is_active') {
                    $row[] = $r->is_active ? 'Yes' : 'No';
                } elseif ($key === 'created_at') {
                    $row[] = $r->created_at ? $r->created_at->format('Y-m-d') : '';
                } elseif ($key === 'dob') {
                    $row[] = $r->dob ?? '';
                } else {
                    $row[] = $r->{$key} ?? '';
                }
            }
            $rows[] = $row;
        }
        return $rows;
    }
}
