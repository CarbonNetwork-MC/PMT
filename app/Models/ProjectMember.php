<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectMember extends Model
{
    protected $primaryKey = 'user_uuid';
    protected $keyType = 'string';
    public $incrementing = false;
    protected $fillable = [
        'project_id',
        'user_uuid',
    ];

    public function project(): BelongsTo {
        return $this->belongsTo(Project::class, 'project_id', 'uuid');
    }

    public function user(): BelongsTo {
        return $this->belongsTo(User::class, 'user_uuid', 'uuid');
    }

    public function role(): BelongsTo {
        return $this->belongsTo(ProjectRole::class, 'project_role_id', 'id');
    }
}
