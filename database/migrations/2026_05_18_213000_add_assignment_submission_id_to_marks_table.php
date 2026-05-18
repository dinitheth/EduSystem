<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('marks', function (Blueprint $table) {
            $table->foreignId('assignment_submission_id')
                ->nullable()
                ->after('mcq_submission_id')
                ->constrained('assignment_submissions')
                ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('marks', function (Blueprint $table) {
            $table->dropConstrainedForeignId('assignment_submission_id');
        });
    }
};
