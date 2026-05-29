<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

#[Fillable([
    'name', 'email', 'password', 'is_admin', 'is_editor',
    'avatar_url', 'role_label', 'social_links', 'is_author', 'is_contributor',
    'high_school_type', 'status', 'german_level', 'english_level',
    'target_field_id', 'target_degree', 'target_semester',
    'monthly_budget_eur', 'preferred_state_id', 'bio', 'last_active_at',
    'expertise', 'education', 'member_of', 'languages_spoken',
    'awards', 'featured_in', 'years_experience',
    'role_label_en', 'role_label_de', 'bio_en', 'bio_de',
])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_active_at'    => 'datetime',
            'password'          => 'hashed',
            'is_admin'          => 'boolean',
            'is_editor'         => 'boolean',
            'is_author'         => 'boolean',
            'is_contributor'    => 'boolean',
            'social_links'      => 'array',
            'expertise'         => 'array',
            'education'         => 'array',
            'member_of'         => 'array',
            'languages_spoken'  => 'array',
            'awards'            => 'array',
            'featured_in'       => 'array',
        ];
    }

    /**
     * Avatar URL veya null. Eğer avatar_url boşsa, isim initialleri için fallback üret.
     */
    /**
     * Locale-aware role label.
     * For 'en' / 'de' prefer the dedicated column; fall back to the TR primary column.
     */
    public function getRoleLabelAttribute(?string $value): ?string
    {
        $locale = app()->getLocale();
        if ($locale === 'en' && ! empty($this->attributes['role_label_en'])) {
            return $this->attributes['role_label_en'];
        }
        if ($locale === 'de' && ! empty($this->attributes['role_label_de'])) {
            return $this->attributes['role_label_de'];
        }
        return $value;
    }

    /**
     * Locale-aware bio (free-text). Same fallback chain as role_label.
     */
    public function getBioAttribute(?string $value): ?string
    {
        $locale = app()->getLocale();
        if ($locale === 'en' && ! empty($this->attributes['bio_en'])) {
            return $this->attributes['bio_en'];
        }
        if ($locale === 'de' && ! empty($this->attributes['bio_de'])) {
            return $this->attributes['bio_de'];
        }
        return $value;
    }

    public function getAvatarOrInitialsAttribute(): array
    {
        $initial = strtoupper(mb_substr($this->name ?: '?', 0, 1));
        return [
            'url'      => $this->avatar_url,
            'initials' => $initial,
        ];
    }

    public function canAccessPanel(Panel $panel): bool
    {
        // Admin (tam yetki) veya Editör (sınırlı — sadece içerik moderasyonu) panele girebilir.
        return $panel->getId() === 'admin' && ($this->is_admin === true || $this->is_editor === true);
    }

    /** Hassas kaynaklar (kullanıcı, sistem, entegrasyon) yalnızca admin'e açık. */
    public function isFullAdmin(): bool
    {
        return $this->is_admin === true;
    }

    // ─────────── Relationships ───────────

    public function favorites(): HasMany
    {
        return $this->hasMany(Favorite::class);
    }

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

    public function contributions(): HasMany
    {
        return $this->hasMany(Contribution::class);
    }

    public function activities(): HasMany
    {
        return $this->hasMany(UserActivity::class)->latest('viewed_at');
    }

    public function quizResults(): HasMany
    {
        return $this->hasMany(UserQuizResult::class)->latest();
    }

    public function applicationTracker(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(ApplicationTracker::class);
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(PremiumSubscription::class)->latest('started_at');
    }

    public function activeSubscription(): ?PremiumSubscription
    {
        return $this->subscriptions()->where('status', 'active')
            ->where(fn ($q) => $q->whereNull('ends_at')->orWhere('ends_at', '>', now()))
            ->first();
    }

    public function isPremium(): bool
    {
        return $this->activeSubscription() !== null;
    }

    public function premiumTier(): ?string
    {
        return $this->activeSubscription()?->tier;
    }

    public function targetField(): BelongsTo
    {
        return $this->belongsTo(FieldOfStudy::class, 'target_field_id');
    }

    public function preferredState(): BelongsTo
    {
        return $this->belongsTo(State::class, 'preferred_state_id');
    }

    // ─────────── Helper'lar ───────────

    /**
     * Bir item favorilerde mi?
     */
    public function hasFavorited($model): bool
    {
        return $this->favorites()
            ->where('favoriteable_type', $model::class)
            ->where('favoriteable_id', $model->getKey())
            ->exists();
    }

    public function favoritesByType(string $modelClass)
    {
        return $this->favorites()->where('favoriteable_type', $modelClass);
    }

    /**
     * Profil tamamlama yüzdesi (0-100).
     */
    public function getProfileCompletionAttribute(): int
    {
        $fields = ['high_school_type', 'status', 'german_level', 'target_field_id',
                   'target_degree', 'target_semester', 'monthly_budget_eur', 'preferred_state_id'];
        $filled = 0;
        foreach ($fields as $f) {
            if (! empty($this->$f)) $filled++;
        }
        return (int) round(100 * $filled / count($fields));
    }

    /**
     * AlmanyaUni Skor (0-100) — engagement göstergesi.
     * Profil tamamlama 30 + favoriler 30 + aktivite 20 + quiz 15 + hesap yaşı 5.
     */
    public function getAlmanyauniScoreAttribute(): int
    {
        $cache = "user_score:{$this->id}";
        return \Illuminate\Support\Facades\Cache::remember($cache, now()->addMinutes(10), function () {
            $profile = $this->profile_completion;
            $favorites = $this->favorites()->count();
            $activities = $this->activities()->count();
            $quizzes = $this->quizResults()->count();
            $months = $this->created_at ? max(0, (int) $this->created_at->diffInMonths(now())) : 0;

            $score = 0;
            $score += round($profile * 0.30);             // 0-30
            $score += min($favorites * 2, 30);            // 0-30
            $score += min($activities * 0.5, 20);          // 0-20
            $score += min($quizzes * 5, 15);               // 0-15
            $score += min($months, 5);                     // 0-5

            return min(100, (int) $score);
        });
    }

    /**
     * Skor seviyesi — sayısal + emoji + etiket.
     */
    public function getAlmanyauniLevelAttribute(): array
    {
        $score = $this->almanyauni_score;
        return match (true) {
            $score >= 80 => ['name' => 'Hazır', 'emoji' => '🏆', 'color' => 'emerald', 'tier' => 5],
            $score >= 60 => ['name' => 'Almanya yolunda', 'emoji' => '✈️', 'color' => 'blue', 'tier' => 4],
            $score >= 40 => ['name' => 'Karar veren', 'emoji' => '🎯', 'color' => 'amber', 'tier' => 3],
            $score >= 20 => ['name' => 'Araştırmacı', 'emoji' => '📚', 'color' => 'violet', 'tier' => 2],
            default      => ['name' => 'Yeni başlayan', 'emoji' => '🌱', 'color' => 'gray', 'tier' => 1],
        };
    }

    /**
     * Kazanılmış rozetler.
     */
    public function getAlmanyauniBadgesAttribute(): array
    {
        $cache = "user_badges:{$this->id}";
        return \Illuminate\Support\Facades\Cache::remember($cache, now()->addMinutes(10), function () {
            $favs = $this->favorites()->count();
            $acts = $this->activities()->count();
            $quizzes = $this->quizResults()->count();
            $profileFull = $this->profile_completion >= 100;
            $citiesViewed = $this->activities()->where('viewable_type', \App\Models\City::class)->count();
            $unisViewed = $this->activities()->where('viewable_type', \App\Models\University::class)->count();

            $badges = [];
            if ($favs >= 1) $badges[] = ['name' => 'İlk Favori', 'emoji' => '❤️', 'desc' => 'İlk favorini eklendiğin gün'];
            if ($favs >= 10) $badges[] = ['name' => 'Koleksiyoner', 'emoji' => '🗂️', 'desc' => '10+ favori eklendi'];
            if ($citiesViewed >= 5) $badges[] = ['name' => 'Şehir Gezgini', 'emoji' => '🏙️', 'desc' => '5+ şehir incelendi'];
            if ($unisViewed >= 10) $badges[] = ['name' => 'Üni Araştırmacısı', 'emoji' => '🎓', 'desc' => '10+ üniversite görüntülendi'];
            if ($quizzes >= 1) $badges[] = ['name' => 'Quiz Çözücü', 'emoji' => '🎯', 'desc' => 'İlk quiz tamamlandı'];
            if ($profileFull) $badges[] = ['name' => 'Tam Profil', 'emoji' => '💯', 'desc' => 'Profilin %100 dolu'];
            if ($acts >= 50) $badges[] = ['name' => 'Aktif Kullanıcı', 'emoji' => '⚡', 'desc' => '50+ sayfa görüntüleme'];

            return $badges;
        });
    }
}
