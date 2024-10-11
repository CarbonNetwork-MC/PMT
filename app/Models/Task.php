<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'card_id',
        'description',
        'status',
        'task_index',
        'sprint_id',
    ];

    public function sprint(): BelongsTo
    {
        return $this->belongsTo(Sprint::class);
    }

    public function card(): BelongsTo
    {
        return $this->belongsTo(Card::class);
    }

    public function assignees(): HasMany
    {
        return $this->hasMany(TaskAssignee::class, 'task_id', 'id');
    }
}
