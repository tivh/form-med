<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SupportRequest extends Model
{
    protected $fillable = [
        'user_id',
        'requester_name',
        'requester_email',
        'subject',
        'status',
        'source',
        'external_ticket_id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(SupportMessage::class)->orderBy('created_at');
    }

    public function latestMessage()
    {
        return $this->hasOne(SupportMessage::class)->latestOfMany();
    }
}
