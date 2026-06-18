<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailTemplate extends Model
{
    protected $fillable = [
        'key',
        'name',
        'category',
        'locale',
        'subject',
        'body',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Replace {{placeholders}} in a string with $vars (missing keys -> left as-is).
     *
     * NOTE: named apply() (not fill()) because Eloquent\Model::fill() is a
     * non-static instance method and cannot be overridden as static.
     */
    public static function apply(string $text, array $vars): string
    {
        return preg_replace_callback('/\{\{\s*(\w+)\s*\}\}/', fn ($m) => $vars[$m[1]] ?? $m[0], $text);
    }

    public function rendered(array $vars): array
    {
        return ['subject' => self::apply($this->subject, $vars), 'body' => self::apply($this->body, $vars)];
    }
}
