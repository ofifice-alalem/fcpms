<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * BR-015: إجازات الاستشاريين المعتمدة من HR.
 * BR-016: لا يتم احتساب الغياب خلال فترة الإجازة.
 */
class ConsultantLeave extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'consultant_id',
        'start_date',
        'end_date',
        'reason',
        'status',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function consultant(): BelongsTo
    {
        return $this->belongsTo(Consultant::class);
    }
}
