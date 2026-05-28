<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PostComment extends Model
{
    protected $fillable = [
        'post_id', 'user_id', 'parent_id',
        'author_name', 'author_email',
        'body', 'status', 'is_pinned', 'helpful_count',
        'ip_address', 'user_agent',
        'approved_at', 'approved_by',
    ];

    protected $casts = [
        'is_pinned'     => 'boolean',
        'helpful_count' => 'integer',
        'approved_at'   => 'datetime',
    ];

    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')->where('status', 'approved')->orderBy('created_at');
    }

    public function scopeApproved($q)
    {
        return $q->where('status', 'approved');
    }

    public function scopePending($q)
    {
        return $q->where('status', 'pending');
    }

    public function scopeTopLevel($q)
    {
        return $q->whereNull('parent_id');
    }

    public function getDisplayNameAttribute(): string
    {
        return $this->user?->name ?? ($this->author_name ?: 'Anonim');
    }

    public function getDisplayAvatarAttribute(): ?string
    {
        return $this->user?->avatar_url;
    }
}
