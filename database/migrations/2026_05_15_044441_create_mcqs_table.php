<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('mcqs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_id')->constrained('teachers')->onDelete('cascade');
            $table->foreignId('subject_id')->nullable()->constrained('subjects')->onDelete('set null');
            $table->enum('class', ['A','B','C','D']);
            $table->string('title');
            $table->integer('time_limit')->nullable()->comment('Minutes');
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('mcqs'); }
};
