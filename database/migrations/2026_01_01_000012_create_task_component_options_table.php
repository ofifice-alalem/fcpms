<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * خيارات القوائم المتعددة والخيارات الفردية (Choice options).
     */
    public function up(): void
    {
        Schema::create('task_component_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_component_id')->constrained('task_components')->cascadeOnDelete();
            $table->string('option_label');
            $table->string('option_value');
            $table->integer('order_index')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('task_component_options');
    }
};
