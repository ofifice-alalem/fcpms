<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * BR-012: يمكن لـ HR إضافة العطل الرسمية.
     * BR-013: العطل الرسمية تطبق على جميع الاستشاريين.
     * BR-067: عدم الحذف الفعلي للبيانات (Soft Deletes).
     */
    public function up(): void
    {
        Schema::create('official_holidays', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->date('start_date');
            $table->date('end_date');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('official_holidays');
    }
};
