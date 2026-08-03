<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Revision 1.2: تخصيص المهام لاستشاريين محددين (Many to Many).
     * BR-036-A: إمكانية تخصيص مهمة لاستشاريين محددين.
     */
    public function up(): void
    {
        Schema::create('task_consultant_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_definition_id')->constrained('task_definitions')->cascadeOnDelete();
            $table->foreignId('consultant_id')->constrained('consultants')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['task_definition_id', 'consultant_id'], 'task_consultant_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('task_consultant_assignments');
    }
};
