<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class TaskComponent extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'task_definition_id',
        'label',
        'component_type',
        'is_required',
        'order_index',
        'validation_rules',
        'visibility_rules',
    ];

    protected $casts = [
        'is_required' => 'boolean',
        'order_index' => 'integer',
        'validation_rules' => 'array',
        'visibility_rules' => 'array',
    ];

    public function taskDefinition(): BelongsTo
    {
        return $this->belongsTo(TaskDefinition::class);
    }

    public function options(): HasMany
    {
        return $this->hasMany(TaskComponentOption::class)->orderBy('order_index', 'asc');
    }
}
