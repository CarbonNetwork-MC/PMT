<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BacklogCardAssignee extends Model
{
    use HasFactory;

    protected $fillable = [
        'backlog_card_id', 
        'user_id'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'uuid');
    }

    public function card(): BelongsTo
    {
        return $this->belongsTo(BacklogCard::class);
    }
}
