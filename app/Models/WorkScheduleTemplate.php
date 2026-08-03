<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * BR-006: يمتلك كل استشاري جدول عمل خاص به يتم تحديده بواسطة HR.
 * BR-067: الحفاظ على البيانات التاريخية وعدم الحذف الفعلي.
 */
class WorkScheduleTemplate extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
    ];

    /**
     * BR-008: أيام الأسبوع المحددة في القالب
     */
    public function days(): HasMany
    {
        return $this->hasMany(WorkScheduleDay::class);
    }

    /**
     * الاستشاريون المرتبطون بجدول العمل هذا
     */
    public function consultants(): HasMany
    {
        return $this->hasMany(Consultant::class);
    }
}
