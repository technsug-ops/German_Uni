<?php

namespace App\Services\Social;

use App\Services\Social\Contracts\SocialPublisher;
use App\Services\Social\Drivers\AyrsharePublisher;
use App\Services\Social\Drivers\ManualPublisher;

/**
 * Aktif yayın sürücüsünü çözer. Sürücü `setting('social_publisher_driver')`
 * ile seçilir (varsayılan: manual). Kokpit her zaman active()->publish() çağırır.
 */
class PublisherManager
{
    /** key => sürücü sınıfı */
    private const DRIVERS = [
        'manual'   => ManualPublisher::class,
        'ayrshare' => AyrsharePublisher::class,
    ];

    public function active(): SocialPublisher
    {
        $key = (string) setting('social_publisher_driver', 'manual');
        $class = self::DRIVERS[$key] ?? ManualPublisher::class;

        // Ayrshare seçili ama anahtar yoksa güvenli şekilde manuele düş.
        $driver = app($class);
        if (! $driver->isConfigured()) {
            return app(ManualPublisher::class);
        }
        return $driver;
    }

    /** Seçili sürücü gerçekten otomatik (API) mı ve hazır mı? */
    public function isAutomaticActive(): bool
    {
        $driver = $this->active();
        return $driver->isAutomatic() && $driver->isConfigured();
    }

    /** Ayarlar formu için: key => label. */
    public static function options(): array
    {
        return [
            'manual'   => 'Manuel-asistan (API yok, ücretsiz)',
            'ayrshare' => 'Ayrshare API (otomatik, key gerekir)',
        ];
    }
}
