<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TaskAssignee extends Model
{
    protected $fillable = [
        'task_id',
        'user_uuid',
    ];

    public function task(): BelongsTo {
        return $this->belongsTo(Task::class, 'task_id');
    }

    public function user(): BelongsTo {
        return $this->belongsTo(User::class, 'user_uuid', 'uuid');
    }
}
