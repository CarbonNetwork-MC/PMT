<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DatabaseSession extends Model
{
    protected $table = 'sessions';
    protected $primaryKey = 'id';

    protected $keyType = 'string';

    public $incrementing = false;
    public $timestamps = false;
    protected $guarded = [];
}
