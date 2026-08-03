<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * BR-006: جدول عمل خاص بكل استشاري عبر قوالب العمل.
     * BR-067: عدم الحذف الفعلي للبيانات (Soft Deletes).
     */
    public function up(): void
    {
        Schema::create('work_schedule_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('work_schedule_templates');
    }
};
