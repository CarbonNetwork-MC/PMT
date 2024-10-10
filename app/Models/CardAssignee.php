<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CardAssignee extends Model
{
    use HasFactory;

    protected $fillable = [
        'card_id',
        'user_id',
    ];

    public function card(): BelongsTo
    {
        return $this->belongsTo(BacklogCard::class, 'card_id', 'id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
