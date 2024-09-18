<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BacklogTask extends Model
{
    use HasFactory;

    protected $fillable = [
        'backlog_card_id',
        'description',
        'status',
        'task_index',
        'backlog_id',
        'assignee_id',
    ];

    public function backlogCard(): BelongsTo
    {
        return $this->belongsTo(BacklogCard::class);
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assignee_id');
    }
}
