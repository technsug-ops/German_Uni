<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Profession extends Model
{
    use HasFactory;
    use \App\Models\Concerns\LocalizableContent;

    public function getNameAttribute(): ?string
    {
        return $this->localized('name') ?: $this->attributes['name_de'] ?? '';
    }

    protected $fillable = [
        'berufenet_id',
        'kldb_code',
        'name_de',
        'short_name',
        'slug',
        'name_tr',
        'cluster',
        'cluster_label',
        'field_of_study_id',
        'type',
        'description_de',
        'description_tr',
        'steckbrief',
        'info_fields',
        'image_url',
        'last_synced_at',
        'is_active',
    ];

    protected $casts = [
        'info_fields'    => 'array',
        'last_synced_at' => 'datetime',
        'is_active'      => 'boolean',
    ];

    public function field(): BelongsTo
    {
        return $this->belongsTo(FieldOfStudy::class, 'field_of_study_id');
    }

    public function favorites(): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        return $this->morphMany(Favorite::class, 'favoriteable');
    }

    public function scopeActive($q)
    {
        return $q->where('is_active', true);
    }

    /**
     * BERUFENET HTML extraction'da label+value bitişik gelir.
     * "StudientypGrundständiges Studium..." → "Studientyp: Grundständiges Studium..."
     */
    public function getCleanSteckbriefAttribute(): ?string
    {
        if (empty($this->steckbrief)) return null;

        $labels = [
            'Studientyp', 'Studieninhalte', 'Studienmöglichkeiten', 'Studienfach',
            'Studienform', 'Studienbeginn', 'Studienrichtung',
            'Abschluss', 'Abschlüsse', 'Abschluss-Bezeichnung',
            'Regelstudienzeit', 'Studiendauer', 'Ausbildungsdauer',
            'Berufstyp', 'Berufsbezeichnung', 'Berufsbereich',
            'Weiterbildungsart', 'Weiterbildungsdauer', 'Weiterbildungsbeginn',
            'Teilnahme', 'Voraussetzungen', 'Zugangsvoraussetzungen',
            'Tätigkeitsfeld', 'Tätigkeitsfelder', 'Aufgaben',
            'Verdienst', 'Gehalt', 'Vergütung',
            'Arbeitsorte', 'Arbeitsbedingungen', 'Arbeitsgebiet',
            'Universität', 'Fachhochschule', 'Berufsakademie', 'Berufsfachschule',
        ];

        $text = $this->steckbrief;
        foreach ($labels as $label) {
            // Label başında veya değerden sonra boşluksuz gelmişse boşluk ekle
            $text = preg_replace('/(\S)' . preg_quote($label, '/') . '/u', '$1 ' . $label . ': ', $text);
            $text = preg_replace('/^' . preg_quote($label, '/') . '(\S)/u', $label . ': $1', $text);
        }

        // Çoklu boşlukları teke indir
        return trim(preg_replace('/\s+/u', ' ', $text));
    }

    /**
     * info_fields JSON'undan AI için en faydalı alanları seçip
     * okunabilir bir Almanca özet metni döndürür (description_de yoksa kaynak olur).
     */
    public function getInfoSummaryAttribute(): ?string
    {
        $info = $this->info_fields;
        if (! is_array($info) || empty($info)) return null;

        // Öncelik sırası: meslek tanımı için en değerli alanlar
        $priorityKeys = [
            'Die Tätigkeit im Überblick',
            'Aufgaben und Tätigkeiten kompakt',
            'Aufgaben und Tätigkeiten im Einzelnen',
            'Arbeitsbereiche/Branchen',
            'Arbeitsorte',
            'Arbeitsbedingungen im Einzelnen',
            'Kompetenzen',
            'Zugang zur Tätigkeit',
            'Zugangsberufe/Zugangstätigkeiten',
            'Verdienst/Einkommen',
            'Weiterbildung (beruflicher Aufstieg)',
        ];

        $parts = [];
        foreach ($priorityKeys as $key) {
            $val = $info[$key] ?? null;
            if (is_string($val) && mb_strlen(trim($val)) > 15) {
                // Kelimeler bitişikse araya boşluk ekle (BERUFENET HTML strip artefaktı)
                $clean = preg_replace('/([a-zäöüß])([A-ZÄÖÜ])/u', '$1 $2', $val);
                $parts[] = $key . ': ' . trim($clean);
            }
            // 2500 karakteri geçince dur (token tasarrufu)
            if (mb_strlen(implode("\n", $parts)) > 2500) break;
        }

        return $parts ? mb_substr(implode("\n", $parts), 0, 2800) : null;
    }
}
