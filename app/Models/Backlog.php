<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Backlog extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'project_id',
        'name',
        'description',
        'status',
        'priority',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function cards(): HasMany 
    {
        return $this->hasMany(BacklogCard::class, 'backlog_id', 'id');
    }
}
