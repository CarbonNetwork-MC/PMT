<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Card extends Model
{
    protected $fillable = [
        'sprint_uuid',
        'name',
        'description',
        'column_id',
        'approval_status',
        'card_index',
    ];

    public function sprint(): BelongsTo {
        return $this->belongsTo(Sprint::class, 'sprint_uuid', 'uuid');
    }

    public function column(): BelongsTo {
        return $this->belongsTo(ProjectColumn::class, 'column_id');
    }

    public function tasks(): HasMany {
        return $this->hasMany(Task::class, 'card_id');
    }

    public function assignees(): HasMany {
        return $this->hasMany(CardAssignee::class, 'card_id');
    }
}
