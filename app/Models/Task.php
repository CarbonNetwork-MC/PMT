<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Task extends Model
{
    protected $fillable = [
        'card_id',
        'description',
        'status',
        'task_index',
        'deadline',
        'estimated_time',
        'actual_time',
    ];

    public function card(): BelongsTo {
        return $this->belongsTo(Card::class, 'card_id');
    }

    public function assignees(): HasMany {
        return $this->hasMany(TaskAssignee::class, 'task_id');
    }
}
