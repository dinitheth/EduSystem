<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('organization_profiles', function (Blueprint $table) {
            $table->id();
            $table->string('organization_name')->default('CodeXpress Institute');
            $table->string('slug')->default('codexpress-institute')->unique();
            $table->string('logo_path')->nullable();
            $table->string('hero_image_path')->nullable();
            $table->string('primary_color', 20)->default('#2563eb');
            $table->string('secondary_color', 20)->default('#14b8a6');
            $table->string('accent_color', 20)->default('#f59e0b');
            $table->enum('theme_mode', ['light', 'dark', 'system'])->default('light');
            $table->string('website_headline')->default('Manage learning with one connected platform.');
            $table->text('website_description')->nullable();
            $table->string('contact_email')->nullable();
            $table->string('contact_phone')->nullable();
            $table->text('address')->nullable();
            $table->string('admin_dashboard_title')->default('Admin Dashboard');
            $table->string('teacher_dashboard_title')->default('Teacher Dashboard');
            $table->string('student_dashboard_title')->default('Student Dashboard');
            $table->json('enabled_modules')->nullable();
            $table->json('dashboard_widgets')->nullable();
            $table->text('custom_css')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('organization_profiles');
    }
};
