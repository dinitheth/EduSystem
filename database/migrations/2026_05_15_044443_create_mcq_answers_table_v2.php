<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::dropIfExists('mcq_answers');
        Schema::create('mcq_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('submission_id')->constrained('mcq_submissions')->onDelete('cascade');
            $table->foreignId('question_id')->constrained('mcq_questions')->onDelete('cascade');
            $table->foreignId('option_id')->nullable()->constrained('mcq_options')->onDelete('set null');
            $table->boolean('is_correct')->default(false);
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('mcq_answers'); }
};
