<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BacklogCardAssignee extends Model
{
    protected $fillable = [
        'backlog_card_id',
        'user_uuid',
    ];

    public function backlogCard(): BelongsTo {
        return $this->belongsTo(BacklogCard::class, 'backlog_card_id');
    }

    public function user(): BelongsTo {
        return $this->belongsTo(User::class, 'user_uuid', 'uuid');
    }
}
