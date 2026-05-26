<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UniversityReviewVote extends Model
{
    protected $fillable = ['review_id', 'user_id', 'session_token', 'vote'];

    public function review(): BelongsTo
    {
        return $this->belongsTo(UniversityReview::class, 'review_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
