<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Backlog extends Model
{
    protected $primaryKey = 'uuid';
    protected $keyType = 'string';
    public $incrementing = false;
    protected $fillable = [
        'uuid',
        'project_uuid',
        'name',
    ];

    public function project(): BelongsTo {
        return $this->belongsTo(Project::class, 'project_uuid', 'uuid');
    }

    public function cards(): HasMany {
        return $this->hasMany(BacklogCard::class, 'backlog_uuid', 'uuid');
    }
}
