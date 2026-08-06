<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BacklogCard extends Model
{
    protected $fillable = [
        'backlog_uuid',
        'title',
        'description',
        'approval_status',
        'card_index',
    ];

    public function backlog(): BelongsTo {
        return $this->belongsTo(Backlog::class, 'backlog_uuid', 'uuid');
    }

    public function tasks(): HasMany {
        return $this->hasMany(BacklogTask::class, 'backlog_card_id');
    }

    public function assignees(): HasMany {
        return $this->hasMany(BacklogCardAssignee::class, 'backlog_card_id');
    }
}
