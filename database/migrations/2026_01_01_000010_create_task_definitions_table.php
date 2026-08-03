<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * BR-036, BR-039: قوالب المهام الديناميكية والأوزان النسبية.
     * BR-067: الحفاظ على البيانات التاريخية (Soft Deletes).
     */
    public function up(): void
    {
        Schema::create('task_definitions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('type', ['daily', 'weekly', 'monthly', 'on_demand'])->default('daily');
            $table->boolean('is_required')->default(true);
            $table->boolean('allow_multiple_responses')->default(false);
            $table->boolean('need_approval')->default(false);
            $table->decimal('performance_weight', 5, 2)->default(1.00);
            $table->enum('status', ['active', 'inactive', 'draft'])->default('active');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('task_definitions');
    }
};
