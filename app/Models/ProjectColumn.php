<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectColumn extends Model
{
    protected $fillable = [
        'project_uuid',
        'name',
        'color',
    ];

    public function project(): BelongsTo {
        return $this->belongsTo(Project::class, 'project_uuid', 'uuid');
    }
}
