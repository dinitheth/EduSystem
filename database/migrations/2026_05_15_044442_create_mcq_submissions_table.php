<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('mcq_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->foreignId('mcq_id')->constrained('mcqs')->onDelete('cascade');
            $table->integer('score')->default(0);
            $table->integer('total')->default(0);
            $table->timestamp('submitted_at')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('mcq_submissions'); }
};
