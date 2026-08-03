<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * BR-008: أيام الأسبوع وأيام العمل الفعلية.
 */
class WorkScheduleDay extends Model
{
    use HasFactory;

    protected $fillable = [
        'work_schedule_template_id',
        'day_of_week',
        'is_working_day',
    ];

    protected $casts = [
        'is_working_day' => 'boolean',
    ];

    public function template(): BelongsTo
    {
        return $this->belongsTo(WorkScheduleTemplate::class, 'work_schedule_template_id');
    }
}
