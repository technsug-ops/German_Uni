<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmailMessage extends Model
{
    protected $fillable = [
        'direction',
        'mailbox',
        'provider_id',
        'to_email',
        'to_name',
        'from_email',
        'subject',
        'body',
        'template_key',
        'status',
        'error',
        'message_id',
        'sent_at',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
    ];

    public function provider(): BelongsTo
    {
        return $this->belongsTo(HousingProvider::class, 'provider_id');
    }
}
