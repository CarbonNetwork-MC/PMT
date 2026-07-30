<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Project extends Model
{
    protected $primaryKey = 'uuid';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'uuid',
        'name',
        'description',
        'owner_uuid',
        'is_archived',
        'archived_by',
        'archived_at',
    ];
    protected $casts = [
        'is_archived' => 'boolean',
        'archived_at' => 'datetime',
    ];

    public function owner(): BelongsTo {
        return $this->belongsTo(User::class, 'owner_uuid', 'uuid');
    }

    public function columns(): HasMany {
        return $this->hasMany(ProjectColumn::class, 'project_uuid', 'uuid');
    }

    public function members(): HasMany {
        return $this->hasMany(ProjectMember::class, 'project_uuid', 'uuid');
    }

    public function backlogs(): HasMany {
        return $this->hasMany(Backlog::class, 'project_uuid', 'uuid');
    }

    public function sprints(): HasMany {
        return $this->hasMany(Sprint::class, 'project_uuid', 'uuid');
    }

    public function logs(): HasMany {
        return $this->hasMany(Log::class, 'project_uuid', 'uuid');
    }

    public function archivedBy(): BelongsTo {
        return $this->belongsTo(User::class, 'archived_by', 'uuid');
    }

    public function latestLog(): HasOne {
        return $this->hasOne(Log::class, 'project_uuid', 'uuid')->latestOfMany();
    }
}
