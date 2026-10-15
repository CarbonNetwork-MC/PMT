<?php

namespace App\Helpers;

use App\Models\DeletedUser;

class GetDeletedUser
{
    public static function getDeletedUserByUuid($uuid) {
        return DeletedUser::where('uuid', $uuid)->first();
    }

    public static function getDeletedUserByName($name) {
        return DeletedUser::where('name', $name)->first();
    }
}
