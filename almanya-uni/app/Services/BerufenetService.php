<?php

namespace App\Services;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;

class BerufenetService
{
    private const BASE_URL = 'https://rest.arbeitsagentur.de/infosysbub/bnet/pc/v1';
    private const API_KEY  = 'infosysbub-berufenet';

    /**
     * Tüm meslekleri sayfa sayfa döndürür (generator).
     * Default 20 sonuç/sayfa, çoğunu denedik size 200 da kabul ediyor.
     */
    public function streamAll(int $pageSize = 100): \Generator
    {
        $page = 0;

        while (true) {
            $resp = $this->client()->get(self::BASE_URL . '/berufe', [
                'page' => $page,
                'size' => $pageSize,
            ]);

            if (! $resp->ok()) {
                throw new \RuntimeException(
                    "BERUFENET list failed: status=" . $resp->status()
                    . ' page=' . $page . ' size=' . $pageSize
                    . ' body=' . mb_substr($resp->body(), 0, 300)
                );
            }

            $data = $resp->json();
            $items = $data['_embedded']['berufSucheList'] ?? [];

            if (empty($items)) {
                break;
            }

            foreach ($items as $item) {
                yield $item;
            }

            // page bilgisi
            $pageInfo = $data['page'] ?? null;
            if (! $pageInfo || $page >= ($pageInfo['totalPages'] ?? 0) - 1) {
                break;
            }
            $page++;
        }
    }

    /**
     * Tek bir mesleğin detayını çek. Yanıt array [0]'da gelir.
     */
    public function getDetail(int $berufenetId): ?array
    {
        $resp = $this->client()->get(self::BASE_URL . '/berufe/' . $berufenetId);

        if (! $resp->ok()) {
            return null;
        }

        $data = $resp->json();
        return $data[0] ?? null;
    }

    /**
     * Detayı veritabanı modeline uygun dizilime dönüştür.
     */
    public function transformDetail(array $detail): array
    {
        // bkgr bazen direkt obj, bazen [obj], bazen [[obj1, obj2]] geliyor — flatten
        $bkgrRaw = $detail['bkgr'] ?? null;
        $bkgr = $bkgrRaw;
        while (is_array($bkgr) && isset($bkgr[0])) {
            $bkgr = $bkgr[0];
        }
        $clusterId = is_array($bkgr) && isset($bkgr['id']) && is_scalar($bkgr['id']) ? (string) $bkgr['id'] : null;

        $typId = null;
        if (is_array($bkgr) && isset($bkgr['typ'])) {
            $typ = $bkgr['typ'];
            while (is_array($typ) && isset($typ[0])) {
                $typ = $typ[0];
            }
            if (is_array($typ) && isset($typ['id'])) {
                $typId = $typ['id'];
            }
        }

        $image = null;
        if (! empty($detail['bilder'][0]['urlNormal'])) {
            $image = $detail['bilder'][0]['urlNormal'];
        }

        $description = $this->extractInfofield($detail['infofelder'] ?? [], 'Beschreibung');

        // steckbrief bazen string, bazen {kurz, lang} object'i
        $steckbriefRaw = $detail['steckbrief'] ?? null;
        $steckbrief = null;
        if (is_string($steckbriefRaw)) {
            $steckbrief = $this->cleanHtml($steckbriefRaw);
        } elseif (is_array($steckbriefRaw)) {
            $kurz = $steckbriefRaw['kurz'] ?? '';
            $steckbrief = $this->cleanHtml($kurz);
            // description boşsa lang'tan da çek
            if (! $description && ! empty($steckbriefRaw['lang'])) {
                $description = $this->cleanHtml($steckbriefRaw['lang']);
            }
        }

        return [
            'berufenet_id'   => (int) $detail['id'],
            'kldb_code'      => $detail['kldb2010'] ?? null,
            'name_de'        => $detail['bezeichnungNeutral'] ?? $detail['kurzBezeichnungNeutral'] ?? 'Unbekannt',
            'short_name'     => $detail['kurzBezeichnungNeutral'] ?? null,
            'cluster'        => $clusterId,
            'cluster_label'  => null, // sonra zenginleştirilebilir
            'type'           => $this->mapType($typId),
            'description_de' => $description,
            'steckbrief'     => $steckbrief,
            'info_fields'    => $this->packInfofields($detail['infofelder'] ?? []),
            'image_url'      => $image,
        ];
    }

    /**
     * BERUFENET tip kodlarını bizim kategorilere map et.
     * 1=Ausbildung, 2=Weiterbildung, 3=Studienberuf, 4=Grundberuf (örnekleme)
     */
    /**
     * BERUFENET tip kodları (typ.id) string olarak gelir.
     * Liste endpoint'inde: a, t, s, w gibi tek karakter kodları gözüküyor.
     */
    private function mapType(mixed $typId): string
    {
        $key = is_scalar($typId) ? (string) $typId : '';
        return match (strtolower($key)) {
            'a'      => 'ausbildung',
            'w'      => 'weiterbildung',
            's'      => 'studienberuf',
            'g', 't' => 'grundberuf',
            default  => 'other',
        };
    }

    private function cleanHtml(string $html): string
    {
        $clean = strip_tags($html);
        $clean = html_entity_decode($clean);
        $clean = preg_replace('/\s+/', ' ', $clean);
        return trim($clean);
    }

    /**
     * infofelder dizilimi bir liste: [{id, ueberschrift, content, infobox}].
     * Tek bir başlığı bulup içeriğini döndürür.
     */
    private function extractInfofield(array $infofelder, string $heading): ?string
    {
        foreach ($infofelder as $field) {
            if (mb_strtolower($field['ueberschrift'] ?? '') === mb_strtolower($heading)) {
                $content = $field['content'] ?? '';
                // HTML olabilir; strip
                $clean = strip_tags($content);
                $clean = html_entity_decode($clean);
                $clean = preg_replace('/\s+/', ' ', $clean);
                return trim($clean);
            }
        }
        return null;
    }

    /**
     * Tüm info field'ları JSON için sade bir dizine pakla.
     */
    private function packInfofields(array $infofelder): array
    {
        $out = [];
        foreach ($infofelder as $field) {
            $key = $field['ueberschrift'] ?? null;
            if (! $key) {
                continue;
            }
            $content = strip_tags($field['content'] ?? '');
            $content = html_entity_decode($content);
            $content = trim(preg_replace('/\s+/', ' ', $content));
            if ($content) {
                $out[$key] = $content;
            }
        }
        return $out;
    }

    private function client(): PendingRequest
    {
        // NOT: BERUFENET API "Accept: application/json" gönderirsek 406 döner.
        // Hiç Accept header göndermezsek HAL/JSON döner — bu kullanıyoruz.
        return Http::timeout(30)
            ->withHeaders([
                'X-API-Key' => self::API_KEY,
            ])
            ->retry(2, 1500, function ($exception) {
                if (! $exception instanceof \Illuminate\Http\Client\RequestException) {
                    return false;
                }
                $status = $exception->response?->status();
                return $status === 429 || ($status >= 500 && $status < 600);
            }, throw: false);
    }
}
