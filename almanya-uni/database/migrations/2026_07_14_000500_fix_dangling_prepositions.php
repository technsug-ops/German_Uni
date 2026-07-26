<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\Post;

/**
 * Yil silindikten sonra sonda kalan bosta edatlari temizler:
 *   "...Cost Guide for"        -> "...Cost Guide"
 *   "...Leitfaden für"         -> "...Leitfaden"
 *   "...Examens im Jahr"       -> "...Examens"
 *   "...the Right Exam in"     -> "...the Right Exam"
 * Hem title hem meta_title uzerinde calisir; yalnizca sondaki bosta
 * edati (for/to/in/im/im Jahr/für/pour/en) hedefler.
 */
return new class extends Migration
{
    // Sirali: once "im Jahr" gibi cok kelimeliler
    private string $pattern = '/\s+(?:im\s+Jahr|for|to|in|im|für|pour|en)\s*$/iu';

    public function up(): void
    {
        $changed = 0;

        foreach (['title', 'meta_title'] as $field) {
            $posts = Post::whereNotNull($field)
                ->where($field, 'REGEXP', '( for| to| in| im| für| im Jahr| pour| en)$')
                ->get();

            foreach ($posts as $p) {
                $new = preg_replace($this->pattern, '', $p->$field);
                $new = preg_replace('/[\s:;,\-–—|]+$/u', '', $new);
                $new = rtrim($new);
                if ($new !== $p->$field && $new !== '') {
                    $p->$field = $new;
                    $p->save();
                    $changed++;
                }
            }
        }
        echo "  duzeltilen bosta edat: {$changed}\n";
    }

    public function down(): void
    {
        // Geri alinamaz.
    }
};
