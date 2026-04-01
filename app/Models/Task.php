<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Task extends Model
{
    protected $fillable = [
        'card_id',
        'description',
        'column_id',
        'task_index',
    ];

    public function card(): BelongsTo {
        return $this->belongsTo(Card::class, 'card_id');
    }

    public function column(): BelongsTo {
        return $this->belongsTo(ProjectColumn::class, 'column_id');
    }

    public function assignees(): HasMany {
        return $this->hasMany(TaskAssignee::class, 'task_id');
    }
}
