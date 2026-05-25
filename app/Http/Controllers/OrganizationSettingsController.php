<?php

namespace App\Http\Controllers;

use App\Models\OrganizationProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class OrganizationSettingsController extends Controller
{
    public function edit()
    {
        $profile = OrganizationProfile::defaultProfile();

        $modules = [
            'Academic Records' => [
                'students' => 'Student records and admissions',
                'teachers' => 'Teacher records and subject allocation',
                'subjects' => 'Subjects, categories, and active course list',
            ],
            'Teaching Workflow' => [
                'assignments' => 'Assignment publishing, uploads, and grading',
                'mcqs' => 'MCQ tests, scheduling, imports, and result view',
                'marks' => 'Marks, grades, academic summaries, and reports',
                'course_content' => 'Course content, files, videos, links, and lessons',
            ],
            'Communication and Operations' => [
                'notifications' => 'Portal notifications for students and teachers',
                'website_registrations' => 'Public registration and pending approvals',
                'exports' => 'Excel exports and filtered data downloads',
            ],
        ];

        $widgets = [
            'Core Dashboard Cards' => [
                'student_stats' => 'Student count and active registration cards',
                'teacher_stats' => 'Teacher count and class allocation cards',
                'subject_stats' => 'Subject coverage and active subject cards',
                'pending_students' => 'Pending website registration requests',
            ],
            'Activity and Alerts' => [
                'recent_marks' => 'Recent marks and grading activity',
                'recent_submissions' => 'Recent assignment and MCQ submissions',
                'assignment_gaps' => 'Missing subject and assignment setup alerts',
                'course_activity' => 'Course content activity and subject progress',
            ],
        ];

        $workflowControls = [
            'Automatic Numbering' => [
                'auto_reg_no' => 'Auto-generate student registration numbers',
                'auto_employee_no' => 'Auto-generate teacher employee numbers',
                'auto_subject_code' => 'Auto-generate subject codes',
            ],
            'Access Rules' => [
                'pending_approval' => 'Require admin approval for website registrations',
                'teacher_subject_lock' => 'Limit teacher actions to assigned subjects',
                'student_class_lock' => 'Limit student content to assigned class',
            ],
            'Assessment Rules' => [
                'mcq_start_time' => 'Enforce MCQ start time before access',
                'mcq_expiry' => 'Close MCQ tests after the time limit',
                'assignment_grade_sync' => 'Sync graded assignments into marks',
            ],
            'Notifications' => [
                'email_notifications' => 'Enable email-ready notification workflow',
            ],
        ];

        $portalSections = [
            'admin' => [
                'Dashboard analytics',
                'Student management',
                'Teacher management',
                'Subject management',
                'Pending student approvals',
                'Import and export tools',
                'Setup and configuration',
            ],
            'teacher' => [
                'Teacher dashboard',
                'My students',
                'Courses and content',
                'Assignments',
                'MCQ tests',
                'Results and marks',
                'Notifications',
            ],
            'student' => [
                'Student dashboard',
                'Courses',
                'Assignments',
                'MCQ tests',
                'My marks',
                'Notifications',
            ],
        ];

        return view('organization_settings.edit', compact('profile', 'modules', 'widgets', 'workflowControls', 'portalSections'));
    }

    public function update(Request $request)
    {
        $profile = OrganizationProfile::defaultProfile();

        $validated = $request->validate([
            'organization_name' => 'required|string|max:255',
            'primary_color' => 'required|string|max:20',
            'secondary_color' => 'required|string|max:20',
            'accent_color' => 'required|string|max:20',
            'contact_email' => 'nullable|email|max:255',
            'contact_phone' => 'nullable|string|max:40',
            'address' => 'nullable|string|max:800',
            'enabled_modules' => 'nullable|array',
            'dashboard_widgets' => 'nullable|array',
            'workflow_controls' => 'nullable|array',
            'logo' => 'nullable|image|max:4096|dimensions:width=512,height=512',
        ]);

        $validated['admin_dashboard_title'] = $profile->admin_dashboard_title;
        $validated['teacher_dashboard_title'] = $profile->teacher_dashboard_title;
        $validated['student_dashboard_title'] = $profile->student_dashboard_title;
        $validated['enabled_modules'] = array_values($request->input('enabled_modules', []));
        $validated['dashboard_widgets'] = array_values($request->input('dashboard_widgets', []));
        $validated['custom_css'] = json_encode([
            'workflow_controls' => array_values($request->input('workflow_controls', [])),
        ]);

        if ($request->hasFile('logo')) {
            if ($profile->logo_path) {
                Storage::disk('public')->delete($profile->logo_path);
            }
            $validated['logo_path'] = $request->file('logo')->store('organization/branding', 'public');
        }

        unset($validated['logo']);

        $profile->update($validated);

        return redirect()->route('organization-settings.edit')->with('success', 'Organization customization saved successfully.');
    }
}
