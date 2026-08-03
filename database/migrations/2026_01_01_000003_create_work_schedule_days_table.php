<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * BR-008: يتكون جدول العمل من أيام الأسبوع التي يعتبر فيها الاستشاري مطالباً بالعمل.
     * BR-009: إمكانية اختلاف جداول العمل بين الاستشاريين.
     */
    public function up(): void
    {
        Schema::create('work_schedule_days', function (Blueprint $table) {
            $table->id();
            $table->foreignId('work_schedule_template_id')->constrained('work_schedule_templates')->cascadeOnDelete();
            $table->enum('day_of_week', ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday']);
            $table->boolean('is_working_day')->default(true);
            $table->timestamps();
            
            $table->unique(['work_schedule_template_id', 'day_of_week'], 'ws_template_day_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('work_schedule_days');
    }
};
