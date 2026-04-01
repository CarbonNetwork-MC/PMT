<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Log extends Model
{
    protected $fillable = [
        'user_uuid',
        'project_uuid',
        'sprint_uuid',
        'backlog_uuid',
        'card_id',
        'task_id',
        'backlog_card_id',
        'backlog_task_id',
        'action',
        'table',
        'data',
        'description',
        'environment',
    ];

    public function user(): BelongsTo {
        return $this->belongsTo(User::class, 'user_uuid', 'uuid');
    }

    public function project(): BelongsTo {
        return $this->belongsTo(Project::class, 'project_uuid', 'uuid');
    }

    public function sprint(): BelongsTo {
        return $this->belongsTo(Sprint::class, 'sprint_uuid', 'uuid');
    }

    public function card(): BelongsTo {
        return $this->belongsTo(Card::class, 'card_id');
    }

    public function task(): BelongsTo {
        return $this->belongsTo(Task::class, 'task_id');
    }

    public function backlog(): BelongsTo {
        return $this->belongsTo(Backlog::class, 'backlog_uuid', 'uuid');
    }

    public function backlogCard(): BelongsTo {
        return $this->belongsTo(BacklogCard::class, 'backlog_card_id');
    }

    public function backlogTask(): BelongsTo {
        return $this->belongsTo(BacklogTask::class, 'backlog_task_id');
    }
}
