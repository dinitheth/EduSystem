<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('mcqs', function (Blueprint $table) {
            $table->timestamp('expires_at')->nullable()->after('time_limit');
        });
    }
    public function down(): void {
        Schema::table('mcqs', function (Blueprint $table) {
            $table->dropColumn('expires_at');
        });
    }
};
