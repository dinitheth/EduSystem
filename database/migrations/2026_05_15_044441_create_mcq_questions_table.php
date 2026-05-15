<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('mcq_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mcq_id')->constrained('mcqs')->onDelete('cascade');
            $table->text('question');
            $table->integer('order')->default(0);
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('mcq_questions'); }
};
