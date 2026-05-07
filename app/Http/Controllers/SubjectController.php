<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use Illuminate\Http\Request;

class SubjectController extends Controller
{
    public function index()
    {
        $subjects = Subject::latest()->get();
        return view('subjects.index', compact('subjects'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'subject_code' => 'required|string|max:50|unique:subjects,subject_code',
            'subject_name' => 'required|string|max:255',
            'description'  => 'nullable|string|max:500',
        ], [
            'subject_code.required' => 'Subject code is required.',
            'subject_code.unique'   => 'This subject code is already taken.',
            'subject_name.required' => 'Subject name is required.',
        ]);

        Subject::create($request->only(['subject_code', 'subject_name', 'description']));

        return redirect()->route('subjects.index')->with('success', 'Subject added successfully!');
    }

    public function edit(Subject $subject)
    {
        $subjects = Subject::latest()->get();
        return view('subjects.index', compact('subject', 'subjects'));
    }

    public function update(Request $request, Subject $subject)
    {
        $request->validate([
            'subject_code' => 'required|string|max:50|unique:subjects,subject_code,' . $subject->id,
            'subject_name' => 'required|string|max:255',
            'description'  => 'nullable|string|max:500',
        ], [
            'subject_code.required' => 'Subject code is required.',
            'subject_code.unique'   => 'This subject code is already taken.',
            'subject_name.required' => 'Subject name is required.',
        ]);

        $subject->update($request->only(['subject_code', 'subject_name', 'description']));

        return redirect()->route('subjects.index')->with('success', 'Subject updated successfully!');
    }

    public function destroy(Subject $subject)
    {
        $subject->delete();
        return redirect()->route('subjects.index')->with('success', 'Subject deleted successfully!');
    }
}
