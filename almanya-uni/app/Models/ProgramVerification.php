<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Bir programın TEK bir alanının resmî kaynakta doğrulanma kaydı (alan + aday grubu + dönem başına bir satır).
 *
 * Kurallar (V1):
 *  - Varsayılan durum UNVERIFIED; kaynak URL'si eklemek hiçbir şeyi otomatik VERIFIED yapmaz.
 *  - VERIFIED için kaynak URL'si ve kanıt şarttır; verified_at yalnız VERIFIED'a geçişte yazılır, sync yenilemez.
 *  - VERIFIED anında programdaki o alanın değerinin parmak izi saklanır. Değer sonradan değişirse (sync ya da
 *    elle) kayıt NEEDS_REVIEW olur — eski doğrulama yeni değere sessizce taşınmaz. Aynı değer gelirse değişiklik yok.
 *  - Basit alanlarda (dil, uni-assist, VPD, başvuru yöntemi) kaynak değeri programdakinden farklıysa VERIFIED
 *    yerine CONFLICT olur.
 *  - Bir alanın doğrulanması başka alanlara yayılmaz.
 */
class ProgramVerification extends Model
{
    public const UNVERIFIED = 'unverified';
    public const VERIFIED = 'verified';
    public const NEEDS_REVIEW = 'needs_review';
    public const CONFLICT = 'conflict';

    public const STATUSES = [
        self::UNVERIFIED => 'Doğrulanmadı',
        self::VERIFIED => 'Doğrulandı',
        self::NEEDS_REVIEW => 'İnceleme gerekli',
        self::CONFLICT => 'Çelişki',
    ];

    /** İzlenen kritik alanlar (özet "X alanın Y'si doğrulandı" bu listeye göre hesaplanır). */
    public const FIELDS = [
        'identity' => 'Program kimliği (ad + üniversite + derece)',
        'language' => 'Öğretim dili',
        'application_method' => 'Başvuru yöntemi',
        'uni_assist' => 'uni-assist kullanımı',
        'vpd' => 'VPD gerekliliği',
        'deadline' => 'Son başvuru tarihi',
        'tuition' => 'Öğrenim ücreti / dönem katkı payı',
        'requirements' => 'Kabul ve dil şartları',
    ];

    /** Kaynak değeri programdaki değerle doğrudan karşılaştırılabilen alanlar → program sütunu. */
    public const SCALAR_COLUMNS = [
        'language' => 'language',
        'application_method' => 'application_method',
        'uni_assist' => 'uni_assist_required',
        'vpd' => 'vpd_required',
    ];

    /** Aday grubu kodları ('' = tüm adaylar). Etiketler İngilizce anahtardır → sayfada __() ile çevrilir. */
    public const APPLICANT_GROUPS = [
        '' => 'All applicants',
        'non_eu' => 'Non-EU applicants',
        'eu' => 'EU/EEA applicants',
        'foreign_degree' => 'Applicants with a foreign degree or school certificate',
        'german_degree' => 'Applicants with a German degree or school certificate',
        'international' => 'International applicants',
        'visa_required' => 'Applicants who need a student visa',
    ];

    public const APPLICATION_METHODS = [
        'uni_assist' => 'uni-assist',
        'direct_portal' => 'Üniversitenin başvuru portalı',
        'vpd_then_portal' => 'VPD + üniversite portalı',
        'hochschulstart' => 'Hochschulstart (DoSV)',
        'multiple' => 'Aday grubuna göre farklı',
        'other' => 'Diğer',
    ];

    protected $fillable = [
        'program_id', 'field', 'applicant_group', 'term', 'status', 'source_value', 'source_url', 'evidence',
        'checked_at', 'verified_by', 'verified_via', 'review_reason',
    ];

    protected $casts = [
        'checked_at' => 'date',
        'verified_at' => 'datetime',
    ];

    protected $attributes = [
        'status' => self::UNVERIFIED,
        'applicant_group' => '',
        'term' => '',
    ];

    protected static function booted(): void
    {
        static::saving(function (self $v) {
            $v->applicant_group = (string) $v->applicant_group;
            $v->term = (string) $v->term;

            if (! array_key_exists($v->field, self::FIELDS)) {
                throw new \InvalidArgumentException("Bilinmeyen doğrulama alanı: {$v->field}");
            }
            if (! array_key_exists($v->status, self::STATUSES)) {
                throw new \InvalidArgumentException("Bilinmeyen doğrulama durumu: {$v->status}");
            }

            if ($v->status === self::VERIFIED) {
                if (blank($v->source_url) || blank($v->evidence)) {
                    throw new \InvalidArgumentException('VERIFIED için kaynak URL ve kanıt zorunlu.');
                }
                $program = $v->program()->first();
                // Basit alanda kaynak ≠ program değeri → çelişki (sessizce doğrulanmış sayılmaz). Yalnız "tüm adaylar"
                // kaydında: gruba özgü yol (ör. AB dışı → uni-assist) programın tek özet değeriyle kıyaslanamaz.
                if ($program && isset(self::SCALAR_COLUMNS[$v->field]) && filled($v->source_value)
                    && ($v->applicant_group === '' || $v->field === 'language')
                    && ! ($v->field === 'application_method' && $program->application_method === 'multiple')
                    && self::normalizeScalar($v->source_value) !== self::normalizeScalar($program->getAttribute(self::SCALAR_COLUMNS[$v->field]))) {
                    $v->status = self::CONFLICT;
                    $v->review_reason = 'Kaynaktaki değer programdaki değerden farklı';
                }
            }

            if ($v->status === self::VERIFIED && ($v->isDirty('status') || ! $v->exists || $v->isDirty('source_value'))) {
                $v->verified_at = now();
                $v->program_value_fingerprint = ($p = $v->program()->first()) ? self::fingerprint($p, $v->field) : null;
                $v->review_reason = null;
            }
        });
    }

    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public static function normalizeScalar($v): string
    {
        return mb_strtolower(trim((string) $v));
    }

    /** Programdaki ilgili alan(lar)ın normalleştirilmiş değeri — doğrulamanın neye ait olduğunu sabitler. */
    public static function currentValue(Program $p, string $field): array
    {
        $date = fn ($d) => $d ? \Illuminate\Support\Carbon::parse($d)->format('Y-m-d') : null;
        $text = fn (...$xs) => sha1(implode('|', array_map(fn ($x) => preg_replace('/\s+/u', ' ', trim((string) $x)), $xs)));

        return match ($field) {
            'identity' => [trim((string) $p->name_de), (int) $p->university_id, (string) $p->degree],
            'language' => [(string) $p->language],
            'application_method' => [(string) $p->application_method, trim((string) $p->application_url)],
            'uni_assist' => [(string) $p->uni_assist_required],
            'vpd' => [(string) $p->vpd_required],
            // GÜNCEL ham değer (saved olayında getRawOriginal hâlâ eski değeri döndürür)
            'deadline' => [$date($p->getAttributes()['application_deadline_winter'] ?? null),
                $date($p->getAttributes()['application_deadline_summer'] ?? null)],
            'tuition' => [$p->tuition_fee_eur === null ? null : (float) $p->tuition_fee_eur,
                $p->cost_per_semester_eur === null ? null : (float) $p->cost_per_semester_eur],
            'requirements' => [$text($p->qualification_requirements_tr, $p->qualification_requirements_en, $p->language_requirements_tr, $p->language_requirements_en)],
            default => [],
        };
    }

    public static function fingerprint(Program $p, string $field): string
    {
        return sha1(json_encode(self::currentValue($p, $field)));
    }

    /**
     * Sync/düzenleme sonrası uzlaştırma: VERIFIED kayıtlardan programdaki değeri değişmiş olanları NEEDS_REVIEW yapar.
     * verified_at ve kanıt korunur (ne zaman, hangi değer için doğrulandığı görünür kalsın). Döner: işaretlenen sayı.
     */
    public static function reconcile(Program $p): int
    {
        $n = 0;
        foreach (self::where('program_id', $p->id)->where('status', self::VERIFIED)->get() as $v) {
            if ($v->program_value_fingerprint !== self::fingerprint($p, $v->field)) {
                // saving hook'u tetiklemeden yalnız durum + gerekçe güncellenir (verified_at korunur)
                self::whereKey($v->id)->update([
                    'status' => self::NEEDS_REVIEW,
                    'review_reason' => 'Programdaki değer doğrulamadan sonra değişti (sync/düzenleme) — yeniden kontrol edin',
                    'updated_at' => now(),
                ]);
                $n++;
            }
        }

        return $n;
    }
}
