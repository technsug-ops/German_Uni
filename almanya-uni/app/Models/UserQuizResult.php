<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserQuizResult extends Model
{
    protected $fillable = ['user_id', 'quiz_type', 'answers', 'result'];

    protected $casts = [
        'answers' => 'array',
        'result'  => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
