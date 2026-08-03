<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Revision 1.2: موديل الربط بين المهام والاستشاريين المستهدفين.
 */
class TaskConsultantAssignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'task_definition_id',
        'consultant_id',
    ];
}
