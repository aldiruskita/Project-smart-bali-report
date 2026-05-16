<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            $table->string('ai_category')->nullable()->after('priority_score');
            $table->decimal('ai_confidence', 5, 2)->nullable()->after('ai_category');
            $table->boolean('ai_detected')->default(false)->after('ai_confidence');
        });
    }

    public function down(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            $table->dropColumn(['ai_category', 'ai_confidence', 'ai_detected']);
        });
    }
};
