<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
    protected $fillable = [
        'name',
        'description',
        'owner_uuid',
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
}
