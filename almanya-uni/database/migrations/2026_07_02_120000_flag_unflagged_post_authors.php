<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Str;

/**
 * Yayınlanmış blog yazısı olup blog yazar-filtresine (BlogController::authorsForFilter)
 * GİREMEYEN yazarları düzeltir: filtre is_author|is_editor|is_admin=true + slug dolu ister.
 *
 * Vaka: Filiz Özkan gibi panelden eklenip yazı atanan ama is_author=false kalan yazarlar
 * "YAZARA GÖRE FİLTRELE" chip'lerinde ve (yazar-adı) aramada görünmüyordu. Genel + idempotent.
 */
return new class extends Migration
{
    public function up(): void
    {
        $authors = User::whereHas('posts', fn ($q) => $q->where('is_published', true))->get();

        $fixed = 0;
        foreach ($authors as $u) {
            $filterable = $u->is_author || $u->is_editor || $u->is_admin;
            if ($filterable && ! empty($u->slug)) continue;

            if (! $filterable) $u->is_author = true;
            if (empty($u->slug)) {
                $base = Str::slug((string) $u->name) ?: 'yazar';
                $slug = $base; $i = 2;
                while (User::where('slug', $slug)->where('id', '!=', $u->id)->exists()) {
                    $slug = $base . '-' . $i++;
                }
                $u->slug = $slug;
            }
            $u->save();
            $fixed++;
            echo "  yazar düzeltildi: {$u->name} (slug={$u->slug})\n";
        }
        echo "flag_unflagged_post_authors: {$fixed} yazar düzeltildi\n";
    }

    public function down(): void
    {
        // Geri alma yok — yazar bayrağı/slug korunur.
    }
};
