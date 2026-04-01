<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectPermission extends Model
{
    protected $fillable = [
        'project_role_id',
        'permission',
    ];

    public function role(): BelongsTo {
        return $this->belongsTo(ProjectRole::class, 'project_role_id', 'id');
    }
}
