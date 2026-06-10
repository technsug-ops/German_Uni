<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Feedback extends Model
{
    protected $table = 'feedbacks';

    protected $fillable = [
        'user_id', 'name', 'email', 'type', 'subject', 'message',
        'page_url', 'user_agent', 'ip_hash', 'status', 'admin_note', 'resolved_at',
    ];

    protected $casts = [
        'resolved_at' => 'datetime',
    ];

    // Kanonik İngilizce etiketler (emojisiz). Gösterimde __() ile lokalize edilir
    // (public contact + admin). Anahtarlar sabit; değerler lang/{tr,de}.json'da çevrili.
    public const TYPES = [
        'general' => 'General',
        'bug' => 'Bug report',
        'suggestion' => 'Suggestion',
        'content' => 'Content correction',
        'partnership' => 'Partnership',
        'other' => 'Other',
    ];

    public const STATUSES = [
        'new' => 'Yeni',
        'in_progress' => 'İncelemede',
        'resolved' => 'Çözüldü',
        'closed' => 'Kapatıldı',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
