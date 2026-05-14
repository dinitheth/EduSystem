<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Teacher;
use App\Models\Subject;

class DashboardController extends Controller
{
    public function index()
    {
        $totalStudents = Student::count();
        $totalTeachers = Teacher::count();
        $totalSubjects = Subject::count();
        $activeStudents = Student::where('status', 'Active')->count();
        $studentsWithoutSubjectsQuery = Student::doesntHave('subjects');
        $teachersWithoutSubjectsQuery = Teacher::doesntHave('subjects');
        $inactiveSubjectsQuery = Subject::where('is_active', false);

        $studentsWithoutSubjects = (clone $studentsWithoutSubjectsQuery)->count();
        $teachersWithoutSubjects = (clone $teachersWithoutSubjectsQuery)->count();

        $genderSummary = [
            'male' => Student::where('gender', 'Male')->count(),
            'female' => Student::where('gender', 'Female')->count(),
            'other' => Student::where('gender', 'Other')->count(),
        ];

        $statusSummary = [
            'active_students' => Student::where('status', 'Active')->count(),
            'inactive_students' => Student::where('status', 'Inactive')->count(),
            'active_subjects' => Subject::where('is_active', true)->count(),
            'inactive_subjects' => Subject::where('is_active', false)->count(),
        ];

        $subjectUsage = Subject::query()
            ->withCount(['students', 'teachers'])
            ->get()
            ->map(function ($subject) {
                $subject->total_assignments = $subject->students_count + $subject->teachers_count;
                return $subject;
            });

        $topSubjects = $subjectUsage
            ->sortByDesc('total_assignments')
            ->take(5)
            ->values();

        $unusedSubjects = $subjectUsage
            ->filter(fn ($subject) => $subject->students_count === 0 && $subject->teachers_count === 0)
            ->count();

        $studentsWithoutSubjectsList = (clone $studentsWithoutSubjectsQuery)
            ->orderBy('full_name')
            ->take(3)
            ->pluck('full_name');

        $studentsWithoutSubjectsFullList = (clone $studentsWithoutSubjectsQuery)
            ->orderBy('full_name')
            ->pluck('full_name');

        $teachersWithoutSubjectsList = (clone $teachersWithoutSubjectsQuery)
            ->orderBy('full_name')
            ->take(3)
            ->pluck('full_name');

        $teachersWithoutSubjectsFullList = (clone $teachersWithoutSubjectsQuery)
            ->orderBy('full_name')
            ->pluck('full_name');

        $inactiveSubjectsList = (clone $inactiveSubjectsQuery)
            ->orderBy('subject_name')
            ->take(3)
            ->pluck('subject_name');

        $inactiveSubjectsFullList = (clone $inactiveSubjectsQuery)
            ->orderBy('subject_name')
            ->pluck('subject_name');

        $unusedSubjectsList = $subjectUsage
            ->filter(fn ($subject) => $subject->students_count === 0 && $subject->teachers_count === 0)
            ->sortBy('subject_name')
            ->take(3)
            ->pluck('subject_name')
            ->values();

        $unusedSubjectsFullList = $subjectUsage
            ->filter(fn ($subject) => $subject->students_count === 0 && $subject->teachers_count === 0)
            ->sortBy('subject_name')
            ->pluck('subject_name')
            ->values();

        return view('dashboard', compact(
            'totalStudents',
            'totalTeachers',
            'totalSubjects',
            'activeStudents',
            'studentsWithoutSubjects',
            'teachersWithoutSubjects',
            'genderSummary',
            'statusSummary',
            'topSubjects',
            'unusedSubjects',
            'studentsWithoutSubjectsList',
            'studentsWithoutSubjectsFullList',
            'teachersWithoutSubjectsList',
            'teachersWithoutSubjectsFullList',
            'inactiveSubjectsList',
            'inactiveSubjectsFullList',
            'unusedSubjectsList',
            'unusedSubjectsFullList'
        ));
    }
}
