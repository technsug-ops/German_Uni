<?php

namespace App\Support;

use App\Models\UserActivity;
use Illuminate\Database\Eloquent\Model;

class ActivityLogger
{
    /**
     * Kullanıcının bir model'i görüntülediğini kaydet.
     * Aynı item 1 saat içinde tekrar görüntülense yeni kayıt OLUŞTURULMAZ — sadece viewed_at güncellenir.
     */
    public static function log(Model $model, ?string $label = null): void
    {
        $user = auth()->user();
        if (! $user) return;

        $existing = UserActivity::where('user_id', $user->id)
            ->where('viewable_type', $model::class)
            ->where('viewable_id', $model->getKey())
            ->first();

        if ($existing) {
            $existing->update([
                'viewed_at' => now(),
                'label'     => $label ?? $existing->label,
            ]);
        } else {
            UserActivity::create([
                'user_id'       => $user->id,
                'viewable_type' => $model::class,
                'viewable_id'   => $model->getKey(),
                'label'         => $label,
                'viewed_at'     => now(),
            ]);
        }

        // last_active_at güncellemesi
        $user->forceFill(['last_active_at' => now()])->saveQuietly();
    }
}
