<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectRole extends Model
{
    protected $fillable = [
        'name',
        'slug',
    ];

    public function permissions() {
        return $this->hasMany(ProjectRolePerm::class, 'project_role_id', 'id');
    }
}
