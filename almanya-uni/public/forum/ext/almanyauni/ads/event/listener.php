<?php

namespace almanyauni\ads\event;

use phpbb\template\template;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * AlmanyaUni reklam config'ini Laravel .env'den okur ve template'lere yansıtır.
 * Laravel .env'i Forum'un parent klasöründe ../../.env'de — directly oku.
 */
class listener implements EventSubscriberInterface
{
    protected $template;
    protected $phpbb_root_path;

    public function __construct(template $template, string $phpbb_root_path)
    {
        $this->template = $template;
        $this->phpbb_root_path = $phpbb_root_path;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'core.page_header_after' => 'inject_ad_config',
        ];
    }

    public function inject_ad_config(): void
    {
        $env = $this->load_env();

        $this->template->assign_vars([
            'AU_ADSENSE_CLIENT'              => $env['ADSENSE_CLIENT_ID'] ?? '',
            'AU_ADSENSE_SLOT_FORUM_TOP'      => $env['ADSENSE_SLOT_FORUM_TOP'] ?? '',
            'AU_ADSENSE_SLOT_FORUM_BOTTOM'   => $env['ADSENSE_SLOT_FORUM_BOTTOM'] ?? '',
            'AU_AFFILIATE_TOP_URL'           => $env['AFFILIATE_EXPATRIO_URL'] ?? '',
        ]);
    }

    /**
     * Laravel .env dosyasını parse et. Forum public/forum/ altında,
     * Laravel root forum klasörünün iki üst dizininde.
     */
    private function load_env(): array
    {
        static $cache = null;
        if ($cache !== null) return $cache;

        $envPath = realpath($this->phpbb_root_path . '../../.env');
        if (! $envPath || ! is_readable($envPath)) {
            return $cache = [];
        }

        $vars = [];
        foreach (file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
            if (str_starts_with(trim($line), '#')) continue;
            if (! str_contains($line, '=')) continue;
            [$k, $v] = explode('=', $line, 2);
            $vars[trim($k)] = trim($v, " \t\n\r\0\x0B\"'");
        }

        return $cache = $vars;
    }
}
