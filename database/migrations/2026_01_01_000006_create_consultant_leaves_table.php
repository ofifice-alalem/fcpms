<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * BR-015: تاريخ بداية ونهاية الإجازة.
     * BR-016: لا يتم احتساب الغياب خلال فترة الإجازة.
     * BR-067: عدم الحذف الفعلي للبيانات (Soft Deletes).
     */
    public function up(): void
    {
        Schema::create('consultant_leaves', function (Blueprint $table) {
            $table->id();
            $table->foreignId('consultant_id')->constrained('consultants')->cascadeOnDelete();
            $table->date('start_date');
            $table->date('end_date');
            $table->string('reason')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('approved');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('consultant_leaves');
    }
};
