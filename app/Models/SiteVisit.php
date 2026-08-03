<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable;

/**
 * BR-022: كل زيارة ترتبط بموقع واحد وسجل يومي واحد.
 * BR-065 & BR-067: تفعيل التدقيق والاحتفاظ بالسجل التاريخي.
 */
class SiteVisit extends Model implements AuditableContract
{
    use HasFactory, SoftDeletes, Auditable;

    protected $fillable = [
        'daily_record_id',
        'site_id',
        'visit_started_at',
        'visit_finished_at',
        'notes',
    ];

    protected $casts = [
        'visit_started_at' => 'datetime',
        'visit_finished_at' => 'datetime',
    ];

    public function dailyRecord(): BelongsTo
    {
        return $this->belongsTo(DailyRecord::class);
    }
}
