<?php

namespace App\Services\Social\Contracts;

use App\Models\ContentAsset;

/**
 * Sosyal yayın sürücüsü arayüzü. Kokpitin "Paylaş" akışı her zaman bunu çağırır;
 * değişen tek şey aktif sürücüdür (manuel-asistan ↔ Ayrshare API).
 */
interface SocialPublisher
{
    /** Benzersiz sürücü anahtarı (ör. 'manual', 'ayrshare'). */
    public function key(): string;

    /** İnsan-okur etiket. */
    public function label(): string;

    /** Bu sürücü gerçek otomatik paylaşım yapar mı (false = elle-asistan)? */
    public function isAutomatic(): bool;

    /** Gerekli kimlik/anahtarlar hazır mı? */
    public function isConfigured(): bool;

    /**
     * Asset'i paylaşır (otomatik) veya paylaşım payload'unu hazırlar (manuel).
     *
     * @return array{
     *   success: bool,
     *   mode: 'manual'|'api',
     *   url?: ?string,
     *   message?: ?string,
     *   share?: array{platform:string,label:string,text:string,media:array<int,string>,intent_url:?string,open_url:string,supports_intent:bool}
     * }
     */
    public function publish(ContentAsset $asset, ?string $pageUrl = null): array;
}
