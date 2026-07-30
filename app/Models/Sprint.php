<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Sprint extends Model
{
    protected $primaryKey = 'uuid';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'uuid',
        'project_uuid',
        'name',
        'start_date',
        'end_date',
        'status',
        'is_archived',
        'archived_at',
        'archived_by',
    ];
    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'archived_at' => 'datetime',
        'is_archived' => 'boolean',
    ];

    public function project(): BelongsTo {
        return $this->belongsTo(Project::class, 'project_uuid', 'uuid');
    }

    public function cards(): HasMany {
        return $this->hasMany(Card::class, 'sprint_uuid', 'uuid');
    }

    public function archivedByUser(): BelongsTo {
        return $this->belongsTo(User::class, 'archived_by', 'uuid');
    }

    public function latestLog(): HasOne {
        return $this->hasOne(Log::class, 'sprint_uuid', 'uuid')->latestOfMany();
    }
}
