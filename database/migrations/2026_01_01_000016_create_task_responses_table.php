<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * BR-027: تخزين إجابات المهام المسجلة في الزيارة.
     */
    public function up(): void
    {
        Schema::create('task_responses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('site_visit_id')->constrained('site_visits')->cascadeOnDelete();
            $table->foreignId('task_definition_id')->constrained('task_definitions')->cascadeOnDelete();
            $table->boolean('is_completed')->default(true);
            $table->json('response_data')->nullable(); // Answers to components
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('task_responses');
    }
};
