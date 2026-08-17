<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SupportMessage extends Model
{
    protected $fillable = [
        'support_request_id',
        'sender_type',
        'sender_name',
        'message',
    ];

    public function supportRequest(): BelongsTo
    {
        return $this->belongsTo(SupportRequest::class);
    }
}
