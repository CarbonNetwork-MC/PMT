<?php

namespace App\Helpers;

use App\Models\Permission;
use Exception;

class CheckIfPermissionExists
{
    public static function check($permission)
    {
        $found = Permission::where('name', $permission)->first();
        if (!$found) {
            // throw new Exception("Permission '{$permission}' does not exist in the database. Please create it first.");
            logger()->error("Permission '{$permission}' does not exist in the database. Please create it first.");
            return false;
        }

        return true;
    }
}
