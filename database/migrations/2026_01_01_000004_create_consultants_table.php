<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * BR-002: حساب واحد فقط لكل استشاري.
     * BR-003: حالات الاستشاري (Active, Inactive, Vacation).
     * BR-007: تعيين جدول العمل للاستشاري.
     * BR-067: عدم الحذف الفعلي للبيانات (Soft Deletes).
     */
    public function up(): void
    {
        Schema::create('consultants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained('users')->cascadeOnDelete();
            $table->string('employee_number')->unique();
            $table->string('full_name');
            $table->string('phone')->nullable();
            $table->string('specialization')->nullable();
            
            // BR-007: جدول العمل المخصص للاستشاري
            $table->foreignId('work_schedule_template_id')->nullable()->constrained('work_schedule_templates')->nullOnDelete();
            
            // BR-003: حالة الاستشاري الحالية
            $table->enum('status', ['active', 'inactive', 'vacation'])->default('active');
            
            $table->timestamps();
            
            // BR-067: عدم الحذف الفعلي للبيانات التاريخية
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('consultants');
    }
};
