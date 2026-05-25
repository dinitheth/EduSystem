<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('organization_profiles', function (Blueprint $table) {
            $table->string('plan')->default('pro')->after('slug');
            $table->string('plan_status')->default('active')->after('plan');
            $table->unsignedInteger('student_limit')->nullable()->after('plan_status');
            $table->timestamp('trial_ends_at')->nullable()->after('student_limit');
            $table->timestamp('subscription_ends_at')->nullable()->after('trial_ends_at');
        });
    }

    public function down(): void
    {
        Schema::table('organization_profiles', function (Blueprint $table) {
            $table->dropColumn([
                'plan',
                'plan_status',
                'student_limit',
                'trial_ends_at',
                'subscription_ends_at',
            ]);
        });
    }
};
