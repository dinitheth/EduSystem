<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Subject;
use App\Services\StudentAccountService;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function index(StudentAccountService $studentAccountService)
    {
        $students = Student::with('subjects')
            ->orderByRaw("
                CASE
                    WHEN reg_no REGEXP '^REG[0-9]{5}$' THEN CAST(SUBSTRING(reg_no, 4) AS UNSIGNED)
                    ELSE 99999999
                END ASC
            ")
            ->orderBy('reg_no')
            ->get();
        $subjects = Subject::orderBy('subject_name')->get();
        $nextRegNo = $studentAccountService->generateRegNo();
        return view('students.index', compact('students', 'subjects', 'nextRegNo'));
    }

    public function store(Request $request, StudentAccountService $studentAccountService)
    {
        $request->validate([
            'reg_no'        => 'nullable|string|max:50|unique:students,reg_no',
            'full_name'     => 'required|string|max:255',
            'email'         => 'required|email|max:255|unique:students,email',
            'phone'         => 'required|string|max:20',
            'dob'           => 'required|date|before:today',
            'gender'        => 'nullable|in:Male,Female,Other',
            'status'        => 'nullable|in:Active,Inactive',
            'class'         => 'nullable|in:A,B,C,D',
            'subject_ids'   => 'nullable|array',
            'subject_ids.*' => 'exists:subjects,id',
        ]);

        $payload = $request->only([
            'reg_no','full_name','email','phone','dob','gender','status','class'
        ]);

        if (blank($payload['reg_no'] ?? null)) {
            unset($payload['reg_no']);
        }

        $studentAccountService->createStudent($payload, $request->input('subject_ids', []));

        return redirect()->route('students.index')->with('success', 'Student registered successfully!');
    }

    public function update(Request $request, Student $student, StudentAccountService $studentAccountService)
    {
        $request->validate([
            'reg_no'        => 'required|string|max:50|unique:students,reg_no,' . $student->id,
            'full_name'     => 'required|string|max:255',
            'email'         => 'required|email|max:255|unique:students,email,' . $student->id,
            'phone'         => 'required|string|max:20',
            'dob'           => 'required|date|before:today',
            'gender'        => 'nullable|in:Male,Female,Other',
            'status'        => 'nullable|in:Active,Inactive',
            'class'         => 'nullable|in:A,B,C,D',
            'subject_ids'   => 'nullable|array',
            'subject_ids.*' => 'exists:subjects,id',
        ]);

        $studentAccountService->updateStudent($student, $request->only([
            'reg_no','full_name','email','phone','dob','gender','status','class'
        ]), $request->input('subject_ids', []));

        return redirect()->route('students.index')->with('success', 'Student updated successfully!');
    }

    public function destroy(Student $student)
    {
        $student->subjects()->detach();
        $student->delete();
        return redirect()->route('students.index')->with('success', 'Student deleted successfully!');
    }
}
