<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('predictions', function (Blueprint $table) {
            $table->json('input_metrics')->nullable()->after('confidence_score');
            $table->json('factors')->nullable()->after('input_metrics');
            $table->boolean('ai_generated')->default(false)->after('factors');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('predictions', function (Blueprint $table) {
            $table->dropColumn(['input_metrics', 'factors', 'ai_generated']);
        });
    }
};
