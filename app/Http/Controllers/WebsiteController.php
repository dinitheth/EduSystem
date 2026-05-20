<?php

namespace App\Http\Controllers;

use App\Models\PendingStudent;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;

class WebsiteController extends Controller
{
    public function home()
    {
        $subjects = Schema::hasTable('subjects')
            ? Subject::where('is_active', true)->orderBy('subject_name')->get()
            : collect();

        return view('website.home', [
            'subjects' => $subjects,
            'featuredSubjects' => $subjects->take(8),
            'subjectDirectory' => $subjects->map(fn ($subject) => [
                'id' => $subject->id,
                'name' => $subject->subject_name,
                'code' => $subject->subject_code,
                'category' => $subject->category,
                'description' => $subject->description,
            ]),
        ]);
    }

    public function subjectSuggestions(Request $request)
    {
        $query = trim((string) $request->query('q'));

        $subjects = Subject::query()
            ->where('is_active', true)
            ->when($query !== '', function ($builder) use ($query) {
                $builder->where(function ($inner) use ($query) {
                    $inner->where('subject_name', 'like', '%'.$query.'%')
                        ->orWhere('subject_code', 'like', '%'.$query.'%');
                });
            })
            ->orderBy('subject_name')
            ->limit(8)
            ->get(['id', 'subject_name', 'subject_code']);

        return response()->json([
            'subjects' => $subjects->map(fn ($subject) => [
                'id' => $subject->id,
                'name' => $subject->subject_name,
                'code' => $subject->subject_code,
            ])->values(),
        ]);
    }

    public function register(Request $request)
    {
        $request->validate([
            'full_name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('students', 'email'),
                Rule::unique('pending_students', 'email')->where(fn ($query) => $query->where('status', 'Pending')),
            ],
            'phone' => 'required|string|max:20',
            'dob' => 'required|date|before:today',
            'gender' => 'required|in:Male,Female,Other',
            'subject_ids' => 'required|array|min:1',
            'subject_ids.*' => 'exists:subjects,id',
        ]);

        $validSubjectIds = Subject::where('is_active', true)
            ->whereIn('id', $request->subject_ids)
            ->pluck('id')
            ->all();

        if (count($validSubjectIds) !== count($request->subject_ids)) {
            return back()->withInput()->withErrors([
                'subject_ids' => 'One or more selected subjects are not available right now.',
            ]);
        }

        PendingStudent::create([
            'full_name' => $request->full_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'dob' => $request->dob,
            'gender' => $request->gender,
            'requested_subject_ids' => $validSubjectIds,
            'status' => 'Pending',
        ]);

        return redirect()->route('website.home')->with('success', 'Registration sent successfully. Our admin team will review your request soon.');
    }
}
