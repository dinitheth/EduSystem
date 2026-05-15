<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::dropIfExists('mcq_options');
        Schema::create('mcq_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('question_id')->constrained('mcq_questions')->onDelete('cascade');
            $table->string('option_text');
            $table->boolean('is_correct')->default(false);
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('mcq_options'); }
};
