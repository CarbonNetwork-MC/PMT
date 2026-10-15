<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BugReport extends Model
{
    protected $fillable = [
        'title',
        'description',
        'page',
        'user_uuid',
        'is_resolved',
    ];
    protected $casts = [
        'is_resolved' => 'boolean',
    ];

    public function user(): BelongsTo {
        return $this->belongsTo(User::class, 'user_uuid', 'uuid');
    }

    public function screenshots(): HasMany {
        return $this->hasMany(BugReportScreenshot::class);
    }
}
