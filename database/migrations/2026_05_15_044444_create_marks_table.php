<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('marks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->foreignId('subject_id')->nullable()->constrained('subjects')->onDelete('set null');
            $table->foreignId('mcq_submission_id')->nullable()->constrained('mcq_submissions')->onDelete('set null');
            $table->string('type')->default('mcq'); // mcq | assignment | manual
            $table->string('title')->nullable();
            $table->integer('score');
            $table->integer('total');
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('marks'); }
};
