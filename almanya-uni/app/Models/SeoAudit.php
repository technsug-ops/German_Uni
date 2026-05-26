<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SeoAudit extends Model
{
    protected $fillable = [
        'template', 'sample_url', 'page_title',
        'content_length', 'h1_count', 'h2_count', 'image_count', 'internal_link_count',
        'keywords_found', 'keywords_missing', 'high_value_gaps',
        'opportunity_score', 'ai_suggestions', 'ai_meta',
        'last_audited_at',
    ];

    protected $casts = [
        'keywords_found' => 'array',
        'keywords_missing' => 'array',
        'high_value_gaps' => 'array',
        'ai_meta' => 'array',
        'last_audited_at' => 'datetime',
    ];

    public const TEMPLATES = [
        'home' => '🏠 Ana Sayfa',
        'university_detail' => '🎓 Üniversite Detay',
        'university_index' => '📚 Üniversite Listesi',
        'program_detail' => '📖 Program Detay',
        'program_index' => '📋 Program Listesi',
        'city_detail' => '🌆 Şehir Detay',
        'faq_index' => '❓ FAQ Listesi',
        'faq_detail' => '💬 FAQ Detay',
        'blog_index' => '📝 Blog Listesi',
        'blog_detail' => '📰 Blog Detay',
        'compare' => '⚖️ Karşılaştırma',
        'rankings' => '🏆 Sıralama',
        'map' => '🗺️ Harita',
        'tool_cost' => '💰 Cost-of-Living',
        'tool_grade' => '📊 Grade Converter',
        'tool_recommendation' => '🎯 Recommendation',
        'about' => 'ℹ️ Hakkında',
        'housing' => '🏠 Konaklama',
    ];
}
