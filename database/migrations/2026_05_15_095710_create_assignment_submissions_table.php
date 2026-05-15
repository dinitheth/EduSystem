<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('assignment_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assignment_id')->constrained()->onDelete('cascade');
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->text('notes')->nullable();
            $table->string('file_path')->nullable();
            $table->enum('status', ['submitted','graded'])->default('submitted');
            $table->decimal('marks', 5, 1)->nullable();
            $table->unsignedSmallInteger('max_marks')->default(100);
            $table->text('feedback')->nullable();
            $table->timestamp('submitted_at')->useCurrent();
            $table->foreignId('graded_by')->nullable()->constrained('teachers')->onDelete('set null');
            $table->timestamp('graded_at')->nullable();
            $table->timestamps();
            $table->unique(['assignment_id','student_id']); // one submission per student
        });
    }
    public function down(): void {
        Schema::dropIfExists('assignment_submissions');
    }
};
