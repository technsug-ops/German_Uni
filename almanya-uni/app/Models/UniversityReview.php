<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UniversityReview extends Model
{
    protected $fillable = [
        'university_id', 'user_id',
        'author_name', 'author_email', 'author_program', 'author_status', 'study_year',
        'rating', 'title', 'body', 'locale',
        'status', 'is_verified', 'verification_token', 'verified_at',
        'moderation_note', 'moderated_by', 'moderated_at',
        'helpful_count', 'unhelpful_count', 'reported_count',
        'ip_address', 'user_agent',
    ];

    protected $casts = [
        'is_verified' => 'boolean',
        'verified_at' => 'datetime',
        'moderated_at' => 'datetime',
        'rating' => 'integer',
        'helpful_count' => 'integer',
        'unhelpful_count' => 'integer',
        'reported_count' => 'integer',
    ];

    public function university(): BelongsTo
    {
        return $this->belongsTo(University::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function moderator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'moderated_by');
    }

    public function votes(): HasMany
    {
        return $this->hasMany(UniversityReviewVote::class, 'review_id');
    }

    public function scopeApproved(Builder $q): Builder
    {
        return $q->where('status', 'approved');
    }

    public function scopePending(Builder $q): Builder
    {
        return $q->where('status', 'pending');
    }

    public function scopeForUniversity(Builder $q, int $uniId): Builder
    {
        return $q->where('university_id', $uniId);
    }

    public function getAuthorDisplayNameAttribute(): string
    {
        return $this->author_name ?: ($this->user?->name ?? __('Anonymous'));
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->author_status) {
            'current_student' => __('Current student'),
            'alumni'          => __('Alumni'),
            'admitted'        => __('Admitted'),
            'applicant'       => __('Applicant'),
            default           => '',
        };
    }
}
