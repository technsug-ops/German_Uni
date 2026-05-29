<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;
    use \App\Models\Concerns\LocalizableContent;

    protected $fillable = [
        'type', 'category_id', 'title_tr', 'title_en', 'title_de', 'slug',
        'description_md_tr', 'description_md_en', 'description_md_de', 'host', 'host_user_id',
        'sponsor', 'sponsor_logo_url', 'reward', 'target_audience', 'difficulty', 'duration_minutes', 'tags', 'presentation_language',
        'recurrence_rule', 'parent_event_id',
        'starts_at', 'ends_at', 'timezone',
        'mode', 'online_url', 'location_name', 'location_city',
        'registration_url', 'max_attendees', 'registered_count', 'registration_required', 'price_eur',
        'banner_url', 'banner_color',
        'is_featured', 'is_active',
        'meta_title', 'meta_description',
    ];

    protected $casts = [
        'starts_at'             => 'datetime',
        'ends_at'               => 'datetime',
        'registration_required' => 'boolean',
        'is_featured'           => 'boolean',
        'is_active'             => 'boolean',
        'price_eur'             => 'decimal:2',
        'tags'                  => 'array',
    ];

    public function category()
    {
        return $this->belongsTo(EventCategory::class);
    }

    public function hostUser()
    {
        return $this->belongsTo(User::class, 'host_user_id');
    }

    public function getLanguageLabelAttribute(): ?string
    {
        return match ($this->presentation_language) {
            'tr'    => 'Türkçe',
            'en'    => 'English',
            'de'    => 'Deutsch',
            'multi' => 'Türkçe + Almanca',
            default => null,
        };
    }

    public function getLanguageFlagAttribute(): ?string
    {
        return match ($this->presentation_language) {
            'tr'    => '🇹🇷',
            'en'    => '🇬🇧',
            'de'    => '🇩🇪',
            'multi' => '🌍',
            default => null,
        };
    }

    public function rsvps()
    {
        return $this->hasMany(EventRsvp::class)->orderByDesc('created_at');
    }

    public function parentEvent()
    {
        return $this->belongsTo(Event::class, 'parent_event_id');
    }

    public function childEvents()
    {
        return $this->hasMany(Event::class, 'parent_event_id')->orderBy('starts_at');
    }

    public function isRecurring(): bool
    {
        return ! empty($this->recurrence_rule);
    }

    public function isSeriesChild(): bool
    {
        return ! empty($this->parent_event_id);
    }

    public function isSeriesParent(): bool
    {
        return $this->isRecurring() || $this->childEvents()->exists();
    }

    /**
     * Recurrence rule'a göre next occurrence DateTime'ı.
     * weekly +7gün, biweekly +14gün, monthly +1 ay.
     */
    public function nextOccurrenceFrom(\Illuminate\Support\Carbon $from): ?\Illuminate\Support\Carbon
    {
        if (! $this->isRecurring()) return null;
        return match ($this->recurrence_rule) {
            'weekly'   => $from->copy()->addWeek(),
            'biweekly' => $from->copy()->addWeeks(2),
            'monthly'  => $from->copy()->addMonth(),
            default    => null,
        };
    }

    /**
     * Bu parent event'ten N kopya üret (Filament admin action'dan çağrılır).
     * Her kopya parent'ın verilerini taşır, sadece tarih ileri kayar.
     */
    public function generateSeriesOccurrences(int $count): array
    {
        if (! $this->isRecurring()) {
            throw new \LogicException('Event has no recurrence_rule set.');
        }
        if ($this->parent_event_id) {
            throw new \LogicException('Series children cannot generate further occurrences.');
        }

        $created = [];
        $lastStart = $this->starts_at;
        $duration = $this->ends_at ? $this->starts_at->diffInMinutes($this->ends_at) : ($this->duration_minutes ?: 60);

        for ($i = 0; $i < $count; $i++) {
            $nextStart = $this->nextOccurrenceFrom($lastStart);
            if (! $nextStart) break;

            // Slug çakışmasını önle (parent slug + tarih)
            $childSlug = $this->slug . '-' . $nextStart->format('Y-m-d');
            if (self::where('slug', $childSlug)->exists()) {
                $lastStart = $nextStart;
                continue;
            }

            $child = self::create([
                'type'                  => $this->type,
                'category_id'           => $this->category_id,
                'title_tr'              => $this->title_tr,
                'title_en'              => $this->title_en,
                'title_de'              => $this->title_de,
                'slug'                  => $childSlug,
                'description_md_tr'     => $this->description_md_tr,
                'description_md_en'     => $this->description_md_en,
                'description_md_de'     => $this->description_md_de,
                'host'                  => $this->host,
                'host_user_id'          => $this->host_user_id,
                'target_audience'       => $this->target_audience,
                'difficulty'            => $this->difficulty,
                'duration_minutes'      => $duration,
                'tags'                  => $this->tags,
                'starts_at'             => $nextStart,
                'ends_at'               => $nextStart->copy()->addMinutes($duration),
                'timezone'              => $this->timezone,
                'mode'                  => $this->mode,
                'location_name'         => $this->location_name,
                'location_city'         => $this->location_city,
                'max_attendees'         => $this->max_attendees,
                'registered_count'      => 0,
                'maybe_count'           => 0,
                'registration_required' => $this->registration_required,
                'price_eur'             => $this->price_eur,
                'banner_color'          => $this->banner_color,
                'is_featured'           => false,
                'is_active'             => true,
                'meta_title'            => $this->meta_title,
                'meta_description'      => $this->meta_description,
                'presentation_language' => $this->presentation_language,
                'parent_event_id'       => $this->id,
                // recurrence_rule SADECE parent'ta — child'da boş bırak
            ]);
            $created[] = $child;
            $lastStart = $nextStart;
        }

        return $created;
    }

    public function goingRsvps()
    {
        return $this->hasMany(EventRsvp::class)
            ->where('status', 'going')
            ->with('user:id,name,slug,avatar_url')
            ->orderByDesc('created_at');
    }

    /**
     * 30+ event tipi — 7 kategoriye ayrılmış. Admin event oluştururken
     * tip seçince kategori otomatik atanabilir (helpers altında).
     */
    public const TYPES = [
        // 🤝 Networking
        'speed_networking'     => ['emoji' => '⚡', 'label' => 'Speed Networking',     'color' => '#0F766E', 'category' => 'networking'],
        'founder_dating'       => ['emoji' => '🚀', 'label' => 'Founder Speed Dating', 'color' => '#0F766E', 'category' => 'networking'],
        'tech_meet'            => ['emoji' => '💼', 'label' => 'Tech Şirketle Tanışma','color' => '#0F766E', 'category' => 'networking'],
        'internship_showcase'  => ['emoji' => '📊', 'label' => 'Staj Tanıtım Günü',    'color' => '#0F766E', 'category' => 'networking'],
        'case_competition'     => ['emoji' => '🏆', 'label' => 'Case Competition',     'color' => '#0F766E', 'category' => 'networking'],
        'mentorship_match'     => ['emoji' => '🤝', 'label' => 'Mentor Eşleşme',       'color' => '#0F766E', 'category' => 'networking'],

        // 🛠️ Skill
        'webinar'              => ['emoji' => '🎙️', 'label' => 'Webinar',              'color' => '#7C3AED', 'category' => 'skill'],
        'workshop'             => ['emoji' => '🛠️', 'label' => 'Atölye',               'color' => '#7C3AED', 'category' => 'skill'],
        'bootcamp'             => ['emoji' => '💻', 'label' => 'Bootcamp',             'color' => '#7C3AED', 'category' => 'skill'],
        'hackathon'            => ['emoji' => '⚡', 'label' => 'Hackathon',            'color' => '#7C3AED', 'category' => 'skill'],
        'masterclass'          => ['emoji' => '🎓', 'label' => 'Master Class',         'color' => '#7C3AED', 'category' => 'skill'],
        'sprint'               => ['emoji' => '🏃', 'label' => 'Product Sprint',       'color' => '#7C3AED', 'category' => 'skill'],

        // 🌍 Peer Learning
        'meetup'               => ['emoji' => '👥', 'label' => 'Buluşma',              'color' => '#EA580C', 'category' => 'peer-learning'],
        'exchange_networking'  => ['emoji' => '🌍', 'label' => 'Erasmus Tanışma',      'color' => '#EA580C', 'category' => 'peer-learning'],
        'cultural_night'       => ['emoji' => '🎭', 'label' => 'Kültür Gecesi',        'color' => '#EA580C', 'category' => 'peer-learning'],
        'study_group'          => ['emoji' => '📚', 'label' => 'Çalışma Grubu',        'color' => '#EA580C', 'category' => 'peer-learning'],
        'city_exploration'     => ['emoji' => '🚴', 'label' => 'Şehir Keşif',          'color' => '#EA580C', 'category' => 'peer-learning'],
        'house_party'          => ['emoji' => '🏠', 'label' => 'Uluslararası Parti',   'color' => '#EA580C', 'category' => 'peer-learning'],

        // 🧠 Personal Growth
        'entrepreneurship'     => ['emoji' => '🧠', 'label' => 'Girişimcilik',         'color' => '#DB2777', 'category' => 'personal-growth'],
        'wellbeing_retreat'    => ['emoji' => '💪', 'label' => 'Refah Retreat',        'color' => '#DB2777', 'category' => 'personal-growth'],
        'goal_setting'         => ['emoji' => '🎯', 'label' => 'Hedef Belirleme',      'color' => '#DB2777', 'category' => 'personal-growth'],
        'book_club'            => ['emoji' => '📖', 'label' => 'Kitap Kulübü',         'color' => '#DB2777', 'category' => 'personal-growth'],
        'finance_workshop'     => ['emoji' => '💰', 'label' => 'Finans Atölyesi',      'color' => '#DB2777', 'category' => 'personal-growth'],
        'info_session'         => ['emoji' => '📋', 'label' => 'Bilgilendirme',        'color' => '#DB2777', 'category' => 'personal-growth'],
        'qa_live'              => ['emoji' => '❓', 'label' => 'Canlı Soru-Cevap',     'color' => '#DB2777', 'category' => 'personal-growth'],
        'deadline'             => ['emoji' => '📅', 'label' => 'Deadline Hatırlatma',  'color' => '#DC2626', 'category' => 'personal-growth'],

        // 🏔️ Adventure
        'climbing_trip'        => ['emoji' => '🏔️', 'label' => 'Tırmanış Gezisi',     'color' => '#0891B2', 'category' => 'adventure'],
        'startup_retreat'      => ['emoji' => '🚣', 'label' => 'Startup Retreat',      'color' => '#0891B2', 'category' => 'adventure'],
        'conference_roadtrip'  => ['emoji' => '🏨', 'label' => 'Konferans Road Trip',  'color' => '#0891B2', 'category' => 'adventure'],
        'esports'              => ['emoji' => '🎮', 'label' => 'Esports Turnuva',      'color' => '#0891B2', 'category' => 'adventure'],
        'film_night'           => ['emoji' => '🎬', 'label' => 'Belgesel Gecesi',      'color' => '#0891B2', 'category' => 'adventure'],

        // 🏭 Industry Immersion
        'factory_tour'         => ['emoji' => '🏭', 'label' => 'Fabrika Gezisi',       'color' => '#CA8A04', 'category' => 'industry-immersion'],
        'banking_day'          => ['emoji' => '🏦', 'label' => 'Bankacılık Günü',      'color' => '#CA8A04', 'category' => 'industry-immersion'],
        'studio_visit'         => ['emoji' => '🎮', 'label' => 'Game Studio Ziyareti', 'color' => '#CA8A04', 'category' => 'industry-immersion'],
        'architecture_vr'      => ['emoji' => '🏗️', 'label' => 'Mimari + VR',         'color' => '#CA8A04', 'category' => 'industry-immersion'],
        'media_house'          => ['emoji' => '📺', 'label' => 'Medya Evi Tur',        'color' => '#CA8A04', 'category' => 'industry-immersion'],
        'open_day'             => ['emoji' => '🏛️', 'label' => 'Üni Tanıtım Günü',    'color' => '#CA8A04', 'category' => 'industry-immersion'],

        // 🎤 Special Format
        'ted_talk'             => ['emoji' => '🎤', 'label' => 'TED-Style Talk',       'color' => '#9333EA', 'category' => 'special-format'],
        'popup_festival'       => ['emoji' => '🎪', 'label' => 'Pop-up Festival',      'color' => '#9333EA', 'category' => 'special-format'],
        'film_festival'        => ['emoji' => '🎬', 'label' => 'Film Festivali',       'color' => '#9333EA', 'category' => 'special-format'],
        'pitch_competition'    => ['emoji' => '🏆', 'label' => 'Pitch Yarışması',      'color' => '#9333EA', 'category' => 'special-format'],
        'virtual_summit'       => ['emoji' => '🌐', 'label' => 'Virtual Summit',       'color' => '#9333EA', 'category' => 'special-format'],
        'ama_session'          => ['emoji' => '💬', 'label' => 'AMA (Ask Me Anything)', 'color' => '#9333EA', 'category' => 'special-format'],
        'panel'                => ['emoji' => '🎤', 'label' => 'Panel',                'color' => '#9333EA', 'category' => 'special-format'],
        'conference'           => ['emoji' => '🎯', 'label' => 'Konferans',            'color' => '#9333EA', 'category' => 'special-format'],
    ];

    public function getTypeLabelAttribute(): string
    {
        return self::TYPES[$this->type]['label'] ?? ucfirst($this->type);
    }

    public function getTypeEmojiAttribute(): string
    {
        return self::TYPES[$this->type]['emoji'] ?? '📅';
    }

    public function getTypeColorAttribute(): string
    {
        return $this->banner_color ?: (self::TYPES[$this->type]['color'] ?? '#1E40AF');
    }

    public function getTitleAttribute(): string
    {
        return $this->localized('title') ?: ($this->title_de ?: __('Event'));
    }

    public function getDescriptionMdAttribute(): ?string
    {
        return $this->localized('description_md');
    }

    public function getIsLiveAttribute(): bool
    {
        $now = now();
        return $this->starts_at && $this->starts_at->lte($now)
            && (! $this->ends_at || $this->ends_at->gte($now));
    }

    public function getIsUpcomingAttribute(): bool
    {
        return $this->starts_at && $this->starts_at->isFuture();
    }

    public function getIsPastAttribute(): bool
    {
        $end = $this->ends_at ?: $this->starts_at;
        return $end && $end->isPast();
    }

    public function getCountdownSecondsAttribute(): int
    {
        if (! $this->starts_at) return 0;
        return max(0, $this->starts_at->diffInSeconds(now(), false) * -1);
    }

    // Scopes
    public function scopeActive(Builder $q): Builder
    {
        return $q->where('is_active', true);
    }

    public function scopeFeatured(Builder $q): Builder
    {
        return $q->where('is_featured', true);
    }

    public function scopeUpcoming(Builder $q): Builder
    {
        return $q->where('starts_at', '>=', now());
    }

    public function scopeLive(Builder $q): Builder
    {
        return $q->where('starts_at', '<=', now())
            ->where(function ($w) {
                $w->whereNull('ends_at')->orWhere('ends_at', '>=', now());
            });
    }

    public function scopePast(Builder $q): Builder
    {
        return $q->where(function ($w) {
            $w->whereNotNull('ends_at')->where('ends_at', '<', now())
              ->orWhere(function ($q2) {
                  $q2->whereNull('ends_at')->where('starts_at', '<', now()->subHours(2));
              });
        });
    }

    /**
     * Şu an gösterilecek tek bir öne çıkan etkinlik —
     * 1. Şu an canlı olan (live) is_featured
     * 2. Önümüzdeki 30 gün içinde en yakın is_featured
     * 3. Aksi takdirde null
     */
    public static function currentBanner(): ?self
    {
        // Önce canlı
        $live = self::active()->featured()->live()->orderBy('starts_at')->first();
        if ($live) return $live;

        // Sonra önümüzdeki 30 gün içindeki en yakın
        return self::active()->featured()->upcoming()
            ->where('starts_at', '<=', now()->addDays(30))
            ->orderBy('starts_at')
            ->first();
    }
}
