<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BacklogTask extends Model
{
    use HasFactory;

    protected $fillable = [
        'backlog_card_id',
        'description',
        'status',
        'task_index',
        'backlog_id',
    ];

    public function backlogCard(): BelongsTo
    {
        return $this->belongsTo(BacklogCard::class);
    }

    public function assignees(): HasMany
    {
        return $this->hasMany(BacklogTaskAssignee::class, 'backlog_task_id', 'id');
    }
}
