<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Chatbot tur-bazlı 👍/👎 geri bildirimi + kalite günlüğü (RAG Faz 5).
 * Kötü (👎) cevaplar = retrieval/prompt iyileştirme listesi.
 */
class ChatFeedback extends Model
{
    protected $table = 'chat_feedbacks';

    protected $fillable = [
        'vote', 'question', 'answer', 'confidence', 'top_score',
        'sources', 'locale', 'ip_hash', 'user_agent', 'status',
    ];

    protected $casts = [
        'vote'      => 'integer',
        'top_score' => 'float',
        'sources'   => 'array',
    ];

    public const STATUSES = [
        'new'      => 'Yeni',
        'reviewed' => 'İncelendi',
        'fixed'    => 'Düzeltildi',
    ];
}
