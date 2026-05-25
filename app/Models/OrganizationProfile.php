<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class OrganizationProfile extends Model
{
    protected $fillable = [
        'organization_name',
        'slug',
        'plan',
        'plan_status',
        'student_limit',
        'trial_ends_at',
        'subscription_ends_at',
        'logo_path',
        'hero_image_path',
        'primary_color',
        'secondary_color',
        'accent_color',
        'theme_mode',
        'website_headline',
        'website_description',
        'contact_email',
        'contact_phone',
        'address',
        'admin_dashboard_title',
        'teacher_dashboard_title',
        'student_dashboard_title',
        'enabled_modules',
        'dashboard_widgets',
        'custom_css',
    ];

    protected $casts = [
        'enabled_modules' => 'array',
        'dashboard_widgets' => 'array',
        'trial_ends_at' => 'datetime',
        'subscription_ends_at' => 'datetime',
    ];

    public const PLAN_LIMITS = [
        'free_trial' => 50,
        'plus' => 1000,
        'pro' => null,
    ];

    public static function defaultProfile(): self
    {
        $defaults = [
            'organization_name' => 'CodeXpress Institute',
            'plan' => 'pro',
            'plan_status' => 'active',
            'student_limit' => null,
            'website_description' => 'A configurable education management platform for students, teachers, administrators, assignments, MCQs, marks, notifications, and institute websites.',
            'contact_email' => 'hello@codexpress.local',
            'contact_phone' => '+94 77 000 0000',
            'address' => 'Colombo, Sri Lanka',
            'enabled_modules' => [
                'students',
                'teachers',
                'subjects',
                'assignments',
                'mcqs',
                'marks',
                'notifications',
                'website_registrations',
            ],
            'dashboard_widgets' => [
                'student_stats',
                'teacher_stats',
                'subject_stats',
                'pending_students',
                'recent_marks',
                'recent_submissions',
            ],
        ];

        foreach (['plan', 'plan_status', 'student_limit', 'trial_ends_at', 'subscription_ends_at'] as $column) {
            if (!Schema::hasColumn('organization_profiles', $column)) {
                unset($defaults[$column]);
            }
        }

        return static::firstOrCreate(
            ['slug' => 'codexpress-institute'],
            $defaults
        );
    }

    public function moduleEnabled(string $key): bool
    {
        $modules = $this->enabled_modules;

        return is_null($modules) || in_array($key, $modules, true);
    }

    public function widgetEnabled(string $key): bool
    {
        $widgets = $this->dashboard_widgets;

        return is_null($widgets) || in_array($key, $widgets, true);
    }

    public function planLabel(): string
    {
        return match ($this->plan ?: 'pro') {
            'free_trial' => 'Free Trial',
            'plus' => 'Plus',
            'pro' => 'Pro',
            default => ucfirst(str_replace('_', ' ', (string) $this->plan)),
        };
    }

    public function planStudentLimitLabel(): string
    {
        return is_null($this->student_limit) ? 'Unlimited students' : number_format($this->student_limit).' students';
    }

    public function planStatusLabel(): string
    {
        return ucfirst(str_replace('_', ' ', (string) ($this->plan_status ?: 'active')));
    }
}
