<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeletedUser extends Model
{
    protected $fillable = [
        'uuid',
        'name',
    ];
}
