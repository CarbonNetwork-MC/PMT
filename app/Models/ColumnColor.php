<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ColumnColor extends Model
{
    protected $fillable = [
        'name',
        'text_color',
        'background_color',
    ];
}
