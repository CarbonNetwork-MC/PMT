<?php

namespace App\Helpers;

use App\Models\Project;
use App\Models\User;

class CheckIfUserIsAdmin
{
    /**
     * Check if the user is the owner or a member of the project, if not it checks if the user has the permission 'manage-projects'.
     * @param User $user
     * @param string $projectUuid
     * @return bool
     */
    public static function check($user, $projectUuid) {
        $project = Project::where('uuid', $projectUuid)->first();
        if (!$project) return false;

        $isProjectOwner = $project->owner_uuid === $user->uuid;
        $isProjectMember = $project->members()->where('user_uuid', $user->uuid)->exists();
        $hasManageProjectsPermission = $user->can('manage-projects');

        if ((!$isProjectOwner && !$isProjectMember) && $hasManageProjectsPermission) return true;

        return false;
    }
}
