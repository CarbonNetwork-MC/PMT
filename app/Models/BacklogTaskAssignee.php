<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BacklogTaskAssignee extends Model
{
    use HasFactory;

    protected $fillable = [
        'backlog_task_id',
        'user_id',
    ];

    public function task(): BelongsTo
    {
        return $this->belongsTo(BacklogTask::class, 'backlog_task_id', 'id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
