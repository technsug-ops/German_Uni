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

    public const TYPES = [
        'general' => '💬 Genel',
        'bug' => '🐛 Hata bildirimi',
        'suggestion' => '💡 Öneri',
        'content' => '📝 İçerik düzeltmesi',
        'partnership' => '🤝 İşbirliği',
        'other' => '❓ Diğer',
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
