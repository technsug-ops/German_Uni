<?php

use App\Models\City;
use App\Models\State;
use Illuminate\Database\Migrations\Migration;

/**
 * Eyalet kartlarına görsel: her eyaletin image_url'ini, o eyaletteki EN BÜYÜK şehrin
 * (nüfusa göre) zaten zenginleştirilmiş gerçek Wikipedia görseliyle doldur. Tanınır
 * skyline/landmark; halüsinasyon yok, dış çağrı yok. Idempotent: image_url doluysa
 * dokunma (admin'den elle seçim korunur).
 */
return new class extends Migration
{
    public function up(): void
    {
        foreach (State::all() as $state) {
            if ($state->image_url) {
                continue; // zaten var
            }
            $img = City::where('state_id', $state->id)
                ->whereNotNull('image_url')
                ->orderByDesc('population')
                ->value('image_url');

            if ($img) {
                State::whereKey($state->id)->update(['image_url' => $img]);
            }
        }
    }

    public function down(): void
    {
        // Geri alınmaz — görseller şehir kaynaklı, kalıcı.
    }
};
