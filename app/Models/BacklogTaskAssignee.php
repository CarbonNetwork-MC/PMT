<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BacklogTaskAssignee extends Model
{
    protected $fillable = [
        'backlog_task_id',
        'user_uuid',
    ];

    public function backlogTask(): BelongsTo {
        return $this->belongsTo(BacklogTask::class, 'backlog_task_id');
    }

    public function user(): BelongsTo {
        return $this->belongsTo(User::class, 'user_uuid', 'uuid');
    }
}
