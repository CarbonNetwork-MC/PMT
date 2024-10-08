<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'project_id',
        'sprint_id',
        'backlog_id',
        'card_id',
        'task_id',
        'action',
        'table',
        'data',
        'description',
        'environment',
    ];
}
