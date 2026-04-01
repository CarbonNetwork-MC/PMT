<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BacklogTask extends Model
{
    protected $fillable = [
        'backlog_card_id',
        'description',
        'status',
        'task_index',
    ];

    public function card(): BelongsTo {
        return $this->belongsTo(BacklogCard::class, 'backlog_card_id');
    }

    public function assignees(): HasMany {
        return $this->hasMany(BacklogTaskAssignee::class, 'backlog_task_id');
    }
}
