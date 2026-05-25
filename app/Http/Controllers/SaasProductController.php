<?php

namespace App\Http\Controllers;

class SaasProductController extends Controller
{
    public function index()
    {
        $plans = [
            [
                'name' => 'Free Trial',
                'price' => 'LKR 0',
                'period' => '14 days',
                'description' => 'For new institutes that want to explore the platform before moving real academic data.',
                'features' => [
                    'Guided product demo',
                    'Up to 50 student records',
                    'Teacher and student portals',
                    'Assignments and MCQ test workflow',
                    'Basic marks and progress view',
                    'Email-ready notification preview',
                ],
                'highlight' => false,
            ],
            [
                'name' => 'Plus',
                'price' => 'LKR 12,500',
                'period' => 'per month',
                'description' => 'For active academies, tuition centers, and training institutes running daily operations.',
                'features' => [
                    'Up to 1,000 students',
                    'Admin, teacher, and student dashboards',
                    'Online registration and approval flow',
                    'Assignments, MCQs, marks, and notifications',
                    'Document-based MCQ import',
                    'Excel exports and academic reporting',
                    'Branding and dashboard configuration',
                ],
                'highlight' => true,
            ],
            [
                'name' => 'Pro',
                'price' => 'LKR 28,500',
                'period' => 'per month',
                'description' => 'For larger education organizations that need advanced configuration and support.',
                'features' => [
                    'Unlimited student capacity plan',
                    'Advanced portal and dashboard controls',
                    'Custom workflow and module configuration',
                    'Priority setup and migration support',
                    'SMTP email delivery configuration',
                    'Dedicated support and onboarding',
                ],
                'highlight' => false,
            ],
        ];

        $features = [
            'Admissions and student registration',
            'Admin, teacher, and student dashboards',
            'Class, subject, and course management',
            'Assignment publishing and grading',
            'MCQ exams with scheduling and imports',
            'Marks, grades, and academic progress',
            'Portal notifications and email-ready alerts',
            'Exports, reports, and operational records',
            'Branding, module, and workflow configuration',
        ];

        return view('saas.product', compact('plans', 'features'));
    }
}
