<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Card extends Model
{
    use HasFactory;

    protected $fillable = [
        'sprint_id',
        'name',
        'description',
        'status',
        'approval_status',
        'card_index',
    ];

    public function sprint(): BelongsTo
    {
        return $this->belongsTo(Sprint::class, 'sprint_id', 'uuid');
    }

    public function assignees(): HasMany
    {
        return $this->hasMany(CardAssignee::class, 'card_id', 'id');
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class, 'card_id', 'id');
    }
}
