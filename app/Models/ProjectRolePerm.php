<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectRolePerm extends Model
{
    protected $fillable = [
        'project_role_id',
        'permission_id',
    ];

    public function role(): BelongsTo {
        return $this->belongsTo(ProjectRole::class, 'project_role_id', 'id');
    }

    public function permission(): BelongsTo {
        return $this->belongsTo(ProjectPermission::class, 'permission_id', 'id');
    }
}
