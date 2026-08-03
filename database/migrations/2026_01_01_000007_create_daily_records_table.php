<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * BR-024: لكل استشاري سجل يومي واحد فقط لكل تاريخ.
     * BR-025: إنشاء السجل تلقائياً عند أول عملية تسجيل.
     * Revision 1.1: إضافة الحقول المشتقة المخزنة لتسريع التقارير (required_daily_tasks, completed_daily_tasks, completion_percentage).
     * BR-067: عدم الحذف الفعلي للبيانات (Soft Deletes).
     */
    public function up(): void
    {
        Schema::create('daily_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('consultant_id')->constrained('consultants')->cascadeOnDelete();
            $table->date('work_date');
            
            // Revision 1.1: الحقول المشتقة المخزنة (Performance Derived Metrics)
            $table->unsignedInteger('required_daily_tasks')->default(0);
            $table->unsignedInteger('completed_daily_tasks')->default(0);
            $table->decimal('completion_percentage', 5, 2)->default(0.00);
            
            $table->timestamps();
            $table->softDeletes();

            // BR-024: ضابط عدم تكرار السجل اليومي لنفس الاستشاري في نفس اليوم
            $table->unique(['consultant_id', 'work_date'], 'consultant_work_date_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_records');
    }
};
