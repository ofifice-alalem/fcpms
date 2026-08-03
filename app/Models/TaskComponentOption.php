<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TaskComponentOption extends Model
{
    use HasFactory;

    protected $fillable = [
        'task_component_id',
        'option_label',
        'option_value',
        'order_index',
    ];

    protected $casts = [
        'order_index' => 'integer',
    ];

    public function component(): BelongsTo
    {
        return $this->belongsTo(TaskComponent::class, 'task_component_id');
    }
}
