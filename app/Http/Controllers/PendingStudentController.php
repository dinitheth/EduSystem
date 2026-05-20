<?php

namespace App\Http\Controllers;

use App\Models\PendingStudent;
use App\Models\Subject;
use App\Services\StudentAccountService;
use Illuminate\Http\Request;

class PendingStudentController extends Controller
{
    public function index()
    {
        $pendingStudents = PendingStudent::latest()->get();
        $subjects = Subject::where('is_active', true)->orderBy('subject_name')->get();

        return view('pending_students.index', compact('pendingStudents', 'subjects'));
    }

    public function approve(Request $request, PendingStudent $pendingStudent, StudentAccountService $studentAccountService)
    {
        $request->validate([
            'class' => 'required|in:A,B,C,D',
            'subject_ids' => 'required|array|min:1',
            'subject_ids.*' => 'exists:subjects,id',
        ]);

        $student = $studentAccountService->approvePendingStudent(
            $pendingStudent,
            $request->class,
            $request->subject_ids
        );

        return redirect()->route('pending-students.index')
            ->with('success', $student->full_name.' was approved and added to students with Reg No '.$student->reg_no.'.');
    }

    public function dismiss(PendingStudent $pendingStudent)
    {
        $pendingStudent->update(['status' => 'Dismissed']);

        return redirect()->route('pending-students.index')->with('success', 'Pending student request dismissed.');
    }
}
