<?php

namespace App\Http\Controllers;

use App\Models\Teacher;
use Illuminate\Http\Request;

class TeacherController extends Controller
{
    public function index()
    {
        $teachers = Teacher::latest()->get();
        return view('teachers.index', compact('teachers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'employee_no'    => 'required|string|max:50|unique:teachers,employee_no',
            'full_name'      => 'required|string|max:255',
            'email'          => 'required|email|max:255|unique:teachers,email',
            'phone'          => 'required|string|max:20',
            'specialization' => 'required|string|max:255',
        ], [
            'employee_no.required'    => 'Employee number is required.',
            'employee_no.unique'      => 'This employee number is already taken.',
            'full_name.required'      => 'Full name is required.',
            'email.required'          => 'Email address is required.',
            'email.email'             => 'Please enter a valid email address.',
            'email.unique'            => 'This email is already registered.',
            'phone.required'          => 'Phone number is required.',
            'specialization.required' => 'Specialization is required.',
        ]);

        Teacher::create($request->only(['employee_no', 'full_name', 'email', 'phone', 'specialization']));

        return redirect()->route('teachers.index')->with('success', 'Teacher added successfully!');
    }

    public function edit(Teacher $teacher)
    {
        $teachers = Teacher::latest()->get();
        return view('teachers.index', compact('teacher', 'teachers'));
    }

    public function update(Request $request, Teacher $teacher)
    {
        $request->validate([
            'employee_no'    => 'required|string|max:50|unique:teachers,employee_no,' . $teacher->id,
            'full_name'      => 'required|string|max:255',
            'email'          => 'required|email|max:255|unique:teachers,email,' . $teacher->id,
            'phone'          => 'required|string|max:20',
            'specialization' => 'required|string|max:255',
        ], [
            'employee_no.required'    => 'Employee number is required.',
            'employee_no.unique'      => 'This employee number is already taken.',
            'full_name.required'      => 'Full name is required.',
            'email.required'          => 'Email address is required.',
            'email.email'             => 'Please enter a valid email address.',
            'email.unique'            => 'This email is already registered.',
            'phone.required'          => 'Phone number is required.',
            'specialization.required' => 'Specialization is required.',
        ]);

        $teacher->update($request->only(['employee_no', 'full_name', 'email', 'phone', 'specialization']));

        return redirect()->route('teachers.index')->with('success', 'Teacher updated successfully!');
    }

    public function destroy(Teacher $teacher)
    {
        $teacher->delete();
        return redirect()->route('teachers.index')->with('success', 'Teacher deleted successfully!');
    }
}
