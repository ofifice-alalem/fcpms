<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable;

/**
 * BR-036: المهام عامة افتراضياً أو مخصصة لمواقع/استشاريين.
 * BR-066 & BR-067: التدقيق التلقائي الحساس للمهام والحفاظ على التاريخ الفعلي.
 */
class TaskDefinition extends Model implements AuditableContract
{
    use HasFactory, SoftDeletes, Auditable;

    protected $fillable = [
        'name',
        'description',
        'type',
        'is_required',
        'allow_multiple_responses',
        'need_approval',
        'performance_weight',
        'status',
    ];

    protected $casts = [
        'is_required' => 'boolean',
        'allow_multiple_responses' => 'boolean',
        'need_approval' => 'boolean',
        'performance_weight' => 'decimal:2',
    ];

    public function components(): HasMany
    {
        return $this->hasMany(TaskComponent::class)->orderBy('order_index', 'asc');
    }

    public function sites(): BelongsToMany
    {
        return $this->belongsToMany(Site::class, 'task_site_assignments');
    }

    public function consultants(): BelongsToMany
    {
        return $this->belongsToMany(Consultant::class, 'task_consultant_assignments');
    }
}
