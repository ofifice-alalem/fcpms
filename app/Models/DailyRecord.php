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
 * BR-024: لكل استشاري سجل يومي واحد فقط لكل تاريخ.
 * BR-025: إنشاء السجل تلقائياً عند أول عملية تسجيل.
 * BR-026: الحاوية الرئيسية لجميع زيارات المواقع خلال اليوم.
 * Revision 1.1: تخزين مؤشرات الأداء اليومية (required_daily_tasks, completed_daily_tasks, completion_percentage) وتحديثها فورياً via Performance Calculation Service.
 * BR-065 & BR-067: الحفاظ على البيانات التاريخية وتدقيق جميع التغييرات via Auditing.
 */
class DailyRecord extends Model implements AuditableContract
{
    use HasFactory, SoftDeletes, Auditable;

    protected $fillable = [
        'consultant_id',
        'work_date',
        'required_daily_tasks',
        'completed_daily_tasks',
        'completion_percentage',
    ];

    protected $casts = [
        'work_date' => 'date',
        'required_daily_tasks' => 'integer',
        'completed_daily_tasks' => 'integer',
        'completion_percentage' => 'decimal:2',
    ];

    /**
     * BR-024: الاستشاري صاحب السجل اليومي
     */
    public function consultant(): BelongsTo
    {
        return $this->belongsTo(Consultant::class);
    }

    /**
     * BR-026: مواقع العمل والزيارات المسجلة ضمن هذا السجل اليومي
     */
    public function siteVisits(): HasMany
    {
        return $this->hasMany(SiteVisit::class);
    }
}
