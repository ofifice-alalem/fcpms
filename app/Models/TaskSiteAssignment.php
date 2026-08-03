<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskSiteAssignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'task_definition_id',
        'site_id',
    ];
}
