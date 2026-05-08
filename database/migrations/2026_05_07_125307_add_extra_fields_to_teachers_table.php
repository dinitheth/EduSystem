<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('teachers', function (Blueprint $table) {
            $table->string('department')->nullable()->after('specialization');
            $table->enum('employment_status', ['Full-time', 'Part-time', 'Contract'])->default('Full-time')->after('department');
        });
    }

    public function down(): void
    {
        Schema::table('teachers', function (Blueprint $table) {
            $table->dropColumn(['department', 'employment_status']);
        });
    }
};
