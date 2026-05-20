<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use Illuminate\Http\Request;

class SubjectController extends Controller
{
    public function index()
    {
        $subjects = Subject::latest()->get();
        $nextSubjectCode = $this->generateSubjectCode();
        return view('subjects.index', compact('subjects', 'nextSubjectCode'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'subject_code' => 'required|string|max:50|unique:subjects,subject_code',
            'subject_name' => 'required|string|max:255',
            'description'  => 'nullable|string|max:500',
            'category'     => 'nullable|string|max:100',
            'is_active'    => 'nullable|boolean',
        ], [
            'subject_code.required' => 'Subject code is required.',
            'subject_code.unique'   => 'This subject code is already taken.',
            'subject_name.required' => 'Subject name is required.',
        ]);

        Subject::create([
            'subject_code' => $request->subject_code ?: $this->generateSubjectCode(),
            'subject_name' => $request->subject_name,
            'description'  => $request->description,
            'category'     => $request->category,
            'is_active'    => $request->has('is_active') ? 1 : 0,
        ]);

        return redirect()->route('subjects.index')->with('success', 'Subject added successfully!');
    }

    public function update(Request $request, Subject $subject)
    {
        $request->validate([
            'subject_code' => 'required|string|max:50|unique:subjects,subject_code,' . $subject->id,
            'subject_name' => 'required|string|max:255',
            'description'  => 'nullable|string|max:500',
            'category'     => 'nullable|string|max:100',
            'is_active'    => 'nullable|boolean',
        ], [
            'subject_code.required' => 'Subject code is required.',
            'subject_code.unique'   => 'This subject code is already taken.',
            'subject_name.required' => 'Subject name is required.',
        ]);

        $subject->update([
            'subject_code' => $request->subject_code,
            'subject_name' => $request->subject_name,
            'description'  => $request->description,
            'category'     => $request->category,
            'is_active'    => $request->has('is_active') ? 1 : 0,
        ]);

        return redirect()->route('subjects.index')->with('success', 'Subject updated successfully!');
    }

    public function destroy(Subject $subject)
    {
        $subject->delete();
        return redirect()->route('subjects.index')->with('success', 'Subject deleted successfully!');
    }

    private function generateSubjectCode(): string
    {
        $lastSubjectCode = Subject::whereRaw("subject_code REGEXP '^SUB[0-9]{3,5}$'")
            ->selectRaw('MAX(CAST(SUBSTRING(subject_code, 4) AS UNSIGNED)) as max_subject_code')
            ->value('max_subject_code');

        $next = $lastSubjectCode ? ((int) $lastSubjectCode + 1) : 1;

        return 'SUB'.str_pad((string) $next, 3, '0', STR_PAD_LEFT);
    }
}
