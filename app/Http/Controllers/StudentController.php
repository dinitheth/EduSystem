<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Subject;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function index()
    {
        $students = Student::with('subjects')->latest()->get();
        $subjects = Subject::orderBy('subject_name')->get();
        return view('students.index', compact('students', 'subjects'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'reg_no'        => 'required|string|max:50|unique:students,reg_no',
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

        $student = Student::create([
            'reg_no'    => $request->reg_no,
            'full_name' => $request->full_name,
            'email'     => $request->email,
            'phone'     => $request->phone,
            'dob'       => $request->dob,
            'gender'    => $request->gender,
            'status'    => $request->status ?? 'Active',
            'class'     => $request->class,
        ]);
        $student->subjects()->sync($request->input('subject_ids', []));

        return redirect()->route('students.index')->with('success', 'Student registered successfully!');
    }

    public function update(Request $request, Student $student)
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

        $student->update([
            'reg_no'    => $request->reg_no,
            'full_name' => $request->full_name,
            'email'     => $request->email,
            'phone'     => $request->phone,
            'dob'       => $request->dob,
            'gender'    => $request->gender,
            'status'    => $request->status ?? 'Active',
            'class'     => $request->class,
        ]);
        $student->subjects()->sync($request->input('subject_ids', []));

        return redirect()->route('students.index')->with('success', 'Student updated successfully!');
    }

    public function destroy(Student $student)
    {
        $student->subjects()->detach();
        $student->delete();
        return redirect()->route('students.index')->with('success', 'Student deleted successfully!');
    }
}
