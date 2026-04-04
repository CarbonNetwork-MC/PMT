<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectColumn extends Model
{
    protected $fillable = [
        'project_uuid',
        'name',
        'position',
        'color_id',
    ];

    public function project(): BelongsTo {
        return $this->belongsTo(Project::class, 'project_uuid', 'uuid');
    }

    public function color(): BelongsTo {
        return $this->belongsTo(ColumnColor::class, 'color_id');
    }
}
