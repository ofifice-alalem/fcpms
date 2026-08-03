<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Task Builder Specification: عناصر النماذج والقواعد الشرطية.
     */
    public function up(): void
    {
        Schema::create('task_components', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_definition_id')->constrained('task_definitions')->cascadeOnDelete();
            $table->string('label');
            $table->string('component_type'); // e.g. text, single_choice, multiple_choice, photo, number
            $table->boolean('is_required')->default(false);
            $table->integer('order_index')->default(0);
            $table->json('validation_rules')->nullable(); // e.g. min, max, allowed_file_types
            $table->json('visibility_rules')->nullable(); // e.g. conditional logic dependent on another component
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('task_components');
    }
};
