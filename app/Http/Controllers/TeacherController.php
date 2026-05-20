<?php

namespace App\Http\Controllers;

use App\Models\Teacher;
use App\Models\Subject;
use App\Services\TeacherAccountService;
use Illuminate\Http\Request;

class TeacherController extends Controller
{
    public function index(TeacherAccountService $teacherAccountService)
    {
        $teachers = Teacher::with('subjects')->latest()->get();
        $subjects = Subject::orderBy('subject_name')->get();
        $nextEmployeeNo = $teacherAccountService->generateEmployeeNo();
        return view('teachers.index', compact('teachers', 'subjects', 'nextEmployeeNo'));
    }

    public function store(Request $request, TeacherAccountService $teacherAccountService)
    {
        $request->validate([
            'employee_no'       => 'nullable|string|max:50|unique:teachers,employee_no',
            'full_name'         => 'required|string|max:255',
            'email'             => 'required|email|max:255|unique:teachers,email',
            'phone'             => 'required|string|max:20',
            'specialization'    => 'required|string|max:255',
            'department'        => 'nullable|string|max:255',
            'employment_status' => 'nullable|in:Full-time,Part-time,Contract',
            'class'             => 'nullable|in:A,B,C,D',
            'subject_ids'       => 'nullable|array',
            'subject_ids.*'     => 'exists:subjects,id',
        ]);

        $payload = $request->only([
            'employee_no','full_name','email','phone','specialization','department','employment_status','class'
        ]);

        if (blank($payload['employee_no'] ?? null)) {
            unset($payload['employee_no']);
        }

        $teacherAccountService->createTeacher($payload, $request->input('subject_ids', []));

        return redirect()->route('teachers.index')->with('success', 'Teacher added successfully!');
    }

    public function update(Request $request, Teacher $teacher, TeacherAccountService $teacherAccountService)
    {
        $request->validate([
            'employee_no'       => 'required|string|max:50|unique:teachers,employee_no,' . $teacher->id,
            'full_name'         => 'required|string|max:255',
            'email'             => 'required|email|max:255|unique:teachers,email,' . $teacher->id,
            'phone'             => 'required|string|max:20',
            'specialization'    => 'required|string|max:255',
            'department'        => 'nullable|string|max:255',
            'employment_status' => 'nullable|in:Full-time,Part-time,Contract',
            'class'             => 'nullable|in:A,B,C,D',
            'subject_ids'       => 'nullable|array',
            'subject_ids.*'     => 'exists:subjects,id',
        ]);

        $teacherAccountService->updateTeacher($teacher, $request->only([
            'employee_no','full_name','email','phone','specialization','department','employment_status','class'
        ]), $request->input('subject_ids', []));

        return redirect()->route('teachers.index')->with('success', 'Teacher updated successfully!');
    }

    public function destroy(Teacher $teacher)
    {
        $teacher->subjects()->detach();
        $teacher->delete();
        return redirect()->route('teachers.index')->with('success', 'Teacher deleted successfully!');
    }
}
