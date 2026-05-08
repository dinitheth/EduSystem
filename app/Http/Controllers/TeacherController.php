<?php

namespace App\Http\Controllers;

use App\Models\Teacher;
use App\Models\Subject;
use Illuminate\Http\Request;

class TeacherController extends Controller
{
    public function index()
    {
        $teachers = Teacher::with('subjects')->latest()->get();
        $subjects = Subject::orderBy('subject_name')->get();
        return view('teachers.index', compact('teachers', 'subjects'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'employee_no'       => 'required|string|max:50|unique:teachers,employee_no',
            'full_name'         => 'required|string|max:255',
            'email'             => 'required|email|max:255|unique:teachers,email',
            'phone'             => 'required|string|max:20',
            'specialization'    => 'required|string|max:255',
            'department'        => 'nullable|string|max:255',
            'employment_status' => 'nullable|in:Full-time,Part-time,Contract',
            'subject_ids'       => 'nullable|array',
            'subject_ids.*'     => 'exists:subjects,id',
        ]);

        $teacher = Teacher::create([
            'employee_no'       => $request->employee_no,
            'full_name'         => $request->full_name,
            'email'             => $request->email,
            'phone'             => $request->phone,
            'specialization'    => $request->specialization,
            'department'        => $request->department,
            'employment_status' => $request->employment_status ?? 'Full-time',
        ]);
        $teacher->subjects()->sync($request->input('subject_ids', []));

        return redirect()->route('teachers.index')->with('success', 'Teacher added successfully!');
    }

    public function update(Request $request, Teacher $teacher)
    {
        $request->validate([
            'employee_no'       => 'required|string|max:50|unique:teachers,employee_no,' . $teacher->id,
            'full_name'         => 'required|string|max:255',
            'email'             => 'required|email|max:255|unique:teachers,email,' . $teacher->id,
            'phone'             => 'required|string|max:20',
            'specialization'    => 'required|string|max:255',
            'department'        => 'nullable|string|max:255',
            'employment_status' => 'nullable|in:Full-time,Part-time,Contract',
            'subject_ids'       => 'nullable|array',
            'subject_ids.*'     => 'exists:subjects,id',
        ]);

        $teacher->update([
            'employee_no'       => $request->employee_no,
            'full_name'         => $request->full_name,
            'email'             => $request->email,
            'phone'             => $request->phone,
            'specialization'    => $request->specialization,
            'department'        => $request->department,
            'employment_status' => $request->employment_status ?? 'Full-time',
        ]);
        $teacher->subjects()->sync($request->input('subject_ids', []));

        return redirect()->route('teachers.index')->with('success', 'Teacher updated successfully!');
    }

    public function destroy(Teacher $teacher)
    {
        $teacher->subjects()->detach();
        $teacher->delete();
        return redirect()->route('teachers.index')->with('success', 'Teacher deleted successfully!');
    }
}
