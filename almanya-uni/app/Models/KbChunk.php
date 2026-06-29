<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * RAG bilgi tabanı parçası. embedding kolonu float32 LE (L2-normalize) ham binary.
 * Vektör pack/unpack için App\Services\Rag\GeminiEmbedder::pack()/unpack() kullan.
 */
class KbChunk extends Model
{
    protected $fillable = [
        'source_type', 'source_id', 'locale', 'chunk_index',
        'title', 'url', 'content', 'token_estimate',
        'embedding', 'dims', 'model', 'content_hash', 'embedded_at',
    ];

    protected $casts = [
        'source_id'      => 'integer',
        'chunk_index'    => 'integer',
        'token_estimate' => 'integer',
        'dims'           => 'integer',
        'embedded_at'    => 'datetime',
    ];
}
