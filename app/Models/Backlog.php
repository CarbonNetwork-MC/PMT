<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Backlog extends Model
{
    protected $fillable = [
        'uuid',
        'project_uuid',
        'name',
        'description',
        'status',
        'is_archived',
        'archived_at',
        'archived_by',
    ];

    public function project(): BelongsTo {
        return $this->belongsTo(Project::class, 'project_uuid', 'uuid');
    }

    public function cards(): HasMany {
        return $this->hasMany(BacklogCard::class, 'backlog_uuid', 'uuid');
    }

    public function archivedByUser(): BelongsTo {
        return $this->belongsTo(User::class, 'archived_by', 'uuid');
    }
}
