<?php

namespace App\Helpers;

use App\Models\Project;
use App\Models\User;

class CheckProjectPermissions
{
    public static function isProjectAdminOrOwner(User $user, Project $project): bool
    {
        return $project->members()
            ->where('user_uuid', $user->uuid)
            ->whereHas('role', function ($query) {
                $query->where('slug', 'admin');
            })
            ->exists() || $project->owner_uuid === $user->uuid;
    }

    public static function isProjectAdmin(User $user, Project $project): bool
    {
        return $project->members()
            ->where('user_uuid', $user->uuid)
            ->whereHas('role', function ($query) {
                $query->where('slug', 'admin');
            })
            ->exists();
    }

    public static function isProjectMember(User $user, Project $project): bool
    {
        return $project->members()
            ->where('user_uuid', $user->uuid)
            ->exists() || $project->owner_uuid === $user->uuid;
    }
}