<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BacklogCard extends Model
{
    use HasFactory;

    protected $fillable = [
        'backlog_id',
        'name',
        'description',
        'card_index',
    ];

    public function backlog(): BelongsTo
    {
        return $this->belongsTo(Backlog::class);
    }

    public function assignees(): HasMany
    {
        return $this->hasMany(BacklogCardAssignee::class, 'backlog_card_id', 'id');
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(BacklogTask::class, 'backlog_card_id', 'id');
    }
}
