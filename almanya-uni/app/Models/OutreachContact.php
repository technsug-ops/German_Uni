<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Kurumsal iletişim defteri (firma/kurum outreach takibi): CHE gibi veri
 * sağlayıcılar, yurtlar, dil okulları, sigortalar, medya/backlink muhatapları.
 * Panelden atılan her mail contact_id ile buraya bağlanır; gelen yanıt da
 * gönderen adresine göre eşleşir → tek ekranda "kiminle nerede kaldık".
 */
class OutreachContact extends Model
{
    protected $fillable = [
        'organization',
        'contact_name',
        'email',
        'phone',
        'website',
        'category',
        'status',
        'priority',
        'notes',
        'last_contacted_at',
        'next_followup_at',
    ];

    protected $casts = [
        'last_contacted_at' => 'datetime',
        'next_followup_at'  => 'date',
    ];

    public const CATEGORIES = [
        'data_provider'   => 'Veri / Sıralama sağlayıcı',
        'housing'         => 'Konaklama / Yurt',
        'language_school' => 'Dil okulu',
        'insurance'       => 'Sigorta / Banka',
        'university'      => 'Üniversite / Kurum',
        'agency'          => 'Danışmanlık / Ajans',
        'media'           => 'Medya / Backlink',
        'affiliate'       => 'Affiliate programı',
        'other'           => 'Diğer',
    ];

    public const STATUSES = [
        'new'         => 'Yeni',
        'contacted'   => 'Mail atıldı',
        'replied'     => 'Yanıt geldi',
        'negotiating' => 'Görüşülüyor',
        'partner'     => 'Anlaşıldı',
        'declined'    => 'Olumsuz',
        'dormant'     => 'Beklemede',
    ];

    public const PRIORITIES = [
        'high'   => 'Yüksek',
        'normal' => 'Normal',
        'low'    => 'Düşük',
    ];

    /** Bu kontakla yapılan tüm yazışma (giden + gelen). */
    public function messages(): HasMany
    {
        return $this->hasMany(EmailMessage::class, 'contact_id')->latest('created_at');
    }

    /** Mail gidince: durumu ilerlet + son temas tarihini yaz. */
    public function markContacted(): void
    {
        $this->forceFill([
            'status'            => $this->status === 'new' ? 'contacted' : $this->status,
            'last_contacted_at' => now(),
        ])->save();
    }

    /** Yanıt gelince: "yanıt geldi"ye çek (görüşme/anlaşma gibi ileri durumları geri almaz). */
    public function markReplied(): void
    {
        if (in_array($this->status, ['new', 'contacted', 'dormant'], true)) {
            $this->forceFill(['status' => 'replied'])->save();
        }
    }

    /** Takip tarihi geçmiş mi? */
    public function isFollowupDue(): bool
    {
        return $this->next_followup_at !== null
            && $this->next_followup_at->isPast()
            && ! in_array($this->status, ['partner', 'declined'], true);
    }

    /** E-posta adresinden kontak bul (gelen mail eşleştirmesi için). */
    public static function findByEmail(?string $email): ?self
    {
        if (blank($email)) {
            return null;
        }

        return static::whereRaw('LOWER(email) = ?', [mb_strtolower(trim($email))])->first();
    }
}
