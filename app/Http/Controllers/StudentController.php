<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function index()
    {
        $students = Student::latest()->get();
        return view('students.index', compact('students'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'reg_no'    => 'required|string|max:50|unique:students,reg_no',
            'full_name' => 'required|string|max:255',
            'email'     => 'required|email|max:255|unique:students,email',
            'phone'     => 'required|string|max:20',
            'dob'       => 'required|date|before:today',
        ], [
            'reg_no.required'    => 'Registration number is required.',
            'reg_no.unique'      => 'This registration number is already taken.',
            'full_name.required' => 'Full name is required.',
            'email.required'     => 'Email address is required.',
            'email.email'        => 'Please enter a valid email address.',
            'email.unique'       => 'This email is already registered.',
            'phone.required'     => 'Phone number is required.',
            'dob.required'       => 'Date of birth is required.',
            'dob.before'         => 'Date of birth must be in the past.',
        ]);

        Student::create($request->only(['reg_no', 'full_name', 'email', 'phone', 'dob']));

        return redirect()->route('students.index')->with('success', 'Student registered successfully!');
    }

    public function edit(Student $student)
    {
        $students = Student::latest()->get();
        return view('students.index', compact('student', 'students'));
    }

    public function update(Request $request, Student $student)
    {
        $request->validate([
            'reg_no'    => 'required|string|max:50|unique:students,reg_no,' . $student->id,
            'full_name' => 'required|string|max:255',
            'email'     => 'required|email|max:255|unique:students,email,' . $student->id,
            'phone'     => 'required|string|max:20',
            'dob'       => 'required|date|before:today',
        ], [
            'reg_no.required'    => 'Registration number is required.',
            'reg_no.unique'      => 'This registration number is already taken.',
            'full_name.required' => 'Full name is required.',
            'email.required'     => 'Email address is required.',
            'email.email'        => 'Please enter a valid email address.',
            'email.unique'       => 'This email is already registered.',
            'phone.required'     => 'Phone number is required.',
            'dob.required'       => 'Date of birth is required.',
            'dob.before'         => 'Date of birth must be in the past.',
        ]);

        $student->update($request->only(['reg_no', 'full_name', 'email', 'phone', 'dob']));

        return redirect()->route('students.index')->with('success', 'Student updated successfully!');
    }

    public function destroy(Student $student)
    {
        $student->delete();
        return redirect()->route('students.index')->with('success', 'Student deleted successfully!');
    }
}
