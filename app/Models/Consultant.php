<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable;

/**
 * BR-002: ملف الاستشاري المرتبط بالحساب.
 * BR-003: حالة الاستشاري (active, inactive, vacation).
 * BR-065 & BR-067: الحفاظ على البيانات التاريخية وتدقيق التغييرات عبر owen-it/laravel-auditing.
 */
class Consultant extends Model implements AuditableContract
{
    use HasFactory, SoftDeletes, Auditable;

    protected $fillable = [
        'user_id',
        'employee_number',
        'full_name',
        'phone',
        'specialization',
        'work_schedule_template_id',
        'status',
    ];

    /**
     * BR-002: الحساب الأساسي للإستشاري
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * BR-006 & BR-007: جدول العمل المخصص للاستشاري
     */
    public function workScheduleTemplate(): BelongsTo
    {
        return $this->belongsTo(WorkScheduleTemplate::class, 'work_schedule_template_id');
    }

    /**
     * BR-024: السجلات اليومية للأعمال المنفذة
     */
    public function dailyRecords(): HasMany
    {
        return $this->hasMany(DailyRecord::class);
    }

    /**
     * BR-015: إجازات الاستشاري
     */
    public function leaves(): HasMany
    {
        return $this->hasMany(ConsultantLeave::class);
    }
}
