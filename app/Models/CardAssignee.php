<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CardAssignee extends Model
{
    protected $fillable = [
        'card_id',
        'user_uuid',
    ];

    public function card(): BelongsTo {
        return $this->belongsTo(Card::class, 'card_id');
    }

    public function user(): BelongsTo {
        return $this->belongsTo(User::class, 'user_uuid', 'uuid');
    }
}
