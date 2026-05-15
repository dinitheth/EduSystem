<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('course_contents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subject_id')->constrained()->onDelete('cascade');
            $table->foreignId('teacher_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['text','pdf','video','link','youtube']);
            $table->string('title');
            $table->text('description')->nullable();
            $table->longText('content_text')->nullable();   // for 'text' type
            $table->string('file_path')->nullable();        // for pdf/video
            $table->string('url')->nullable();              // for link/youtube
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('course_contents');
    }
};
