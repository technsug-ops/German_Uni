<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Support\OgCard;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * Dinamik og:image (1200x630) üretici.
 *
 * /og/{type}/{slug}.png[?lang=xx] — ilk istekte üretilir,
 * storage/app/public/og/{brand}/v2/{locale}/'a cache'lenir.
 * Kart metinleri (slogan, rozet, kategori, okuma süresi…) sayfa dilinde: bkz. App\Support\OgCard
 * (post → kaydın locale'i; diğer tipler → ?lang=, yoksa marka varsayılan dili).
 * Cache-Control: public, max-age=7 gün.
 */
class OgImageController extends Controller
{
    private const W = 1200;
    private const H = 630;

    private const FONT_BOLD = 'resources/fonts/Inter-Bold.ttf';
    private const FONT_REG  = 'resources/fonts/Inter-Regular.ttf';

    public function show(Request $request, string $type, string $slug)
    {
        $cleanSlug = preg_replace('/\.png$/', '', $slug);

        if (! file_exists(base_path(self::FONT_BOLD)) || ! file_exists(base_path(self::FONT_REG))) {
            abort(404);
        }

        // Geçersiz/eksik lang yok sayılır → sınırsız cache dosyası üretilemez.
        $requested = OgCard::normalizeLocale($request->query(OgCard::QUERY_PARAM));
        $locale = OgCard::localeFor($type, $cleanSlug, $requested);
        if ($locale === null) {
            abort(404);
        }

        // DUAL-BRAND + sürüm + dil: eski Türkçe-metinli cache dosyaları artık okunmaz.
        $diskPath = storage_path('app/public/' . OgCard::cachePath(brand_key(), $locale, $type, $cleanSlug));

        if (file_exists($diskPath)) {
            return $this->serveFile($diskPath);
        }

        $data = OgCard::data($type, $cleanSlug, $locale);
        if (! $data) {
            abort(404);
        }

        @mkdir(dirname($diskPath), 0775, true);
        $im = $this->renderImage($data);
        imagepng($im, $diskPath, 6);
        imagedestroy($im);

        return $this->serveFile($diskPath);
    }

    private function renderImage(array $d): \GdImage
    {
        $im = imagecreatetruecolor(self::W, self::H);

        [$r, $g, $b] = $this->hexToRgb($d['color']);
        $bg     = imagecolorallocate($im, $r, $g, $b);
        $bgDark = imagecolorallocate($im, max(0, $r - 40), max(0, $g - 40), max(0, $b - 40));

        $this->verticalGradient($im, $bg, $bgDark);

        $white   = imagecolorallocate($im, 255, 255, 255);
        $whiteSoft = imagecolorallocatealpha($im, 255, 255, 255, 50);
        $whiteDim = imagecolorallocatealpha($im, 255, 255, 255, 30);

        $padX = 80;

        // Üst sol: brand-aware branding (request host'una göre AlmanyaUni veya ApplyToGerman)
        $fontBold = base_path(self::FONT_BOLD);
        $fontReg  = base_path(self::FONT_REG);
        // Marka adı / slogan / domain kart dilinde OgCard::data()'dan gelir (global locale'e bakmaz)
        $brandName    = $d['brand'];
        $brandTagline = $d['tagline'] ?? '';
        imagettftext($im, 28, 0, $padX, 100, $white, $fontBold, $brandName);
        if ($brandTagline !== '') {
            imagettftext($im, 16, 0, $padX, 130, $whiteSoft, $fontReg, $brandTagline);
        }

        // Sağ üst: badge
        $badgeText = $d['badge'];
        $badgeFontSize = 18;
        $bbox = imagettfbbox($badgeFontSize, 0, $fontBold, $badgeText);
        $badgeW = $bbox[2] - $bbox[0];
        $badgeBoxX = self::W - $padX - $badgeW - 40;
        $badgeBoxY = 70;
        imagefilledrectangle($im, $badgeBoxX, $badgeBoxY, self::W - $padX, $badgeBoxY + 50, $whiteDim);
        imagettftext($im, $badgeFontSize, 0, $badgeBoxX + 20, $badgeBoxY + 33, $white, $fontBold, $badgeText);

        // Başlık (orta) — uzunluğa göre boyut ayarla
        $title = $d['title'];
        $titleFontSize = $this->fitFontSize($title, $fontBold, self::W - 2 * $padX, 78, 42);
        $titleLines = $this->wrapText($title, $fontBold, $titleFontSize, self::W - 2 * $padX);

        $startY = 290;
        foreach (array_slice($titleLines, 0, 3) as $i => $line) {
            imagettftext($im, $titleFontSize, 0, $padX, $startY + ($i * ($titleFontSize + 18)), $white, $fontBold, $line);
        }

        // Alt başlık (subtitle)
        if (! empty($d['subtitle'])) {
            imagettftext($im, 26, 0, $padX, self::H - 130, $whiteSoft, $fontReg, mb_strimwidth($d['subtitle'], 0, 60, '…'));
        }

        // Alt çubuk
        imagefilledrectangle($im, 0, self::H - 70, self::W, self::H, imagecolorallocatealpha($im, 0, 0, 0, 90));

        imagettftext($im, 18, 0, $padX, self::H - 28, $white, $fontReg, $d['domain']);
        if (! empty($d['meta'])) {
            $metaText = $d['meta'];
            $metaBbox = imagettfbbox(18, 0, $fontReg, $metaText);
            $metaW = $metaBbox[2] - $metaBbox[0];
            imagettftext($im, 18, 0, self::W - $padX - $metaW, self::H - 28, $whiteSoft, $fontReg, $metaText);
        }

        return $im;
    }

    private function fitFontSize(string $text, string $font, int $maxWidth, int $maxSize, int $minSize): int
    {
        for ($size = $maxSize; $size >= $minSize; $size -= 2) {
            $bbox = imagettfbbox($size, 0, $font, $text);
            $w = $bbox[2] - $bbox[0];
            if ($w <= $maxWidth) return $size;
        }
        return $minSize;
    }

    private function wrapText(string $text, string $font, int $size, int $maxWidth): array
    {
        $words = preg_split('/\s+/u', $text);
        $lines = [];
        $current = '';

        foreach ($words as $word) {
            $test = $current === '' ? $word : $current . ' ' . $word;
            $bbox = imagettfbbox($size, 0, $font, $test);
            $w = $bbox[2] - $bbox[0];
            if ($w > $maxWidth && $current !== '') {
                $lines[] = $current;
                $current = $word;
            } else {
                $current = $test;
            }
        }
        if ($current !== '') $lines[] = $current;

        return $lines;
    }

    private function verticalGradient(\GdImage $im, int $top, int $bottom): void
    {
        $topRgb = $this->rgbFromColor($im, $top);
        $botRgb = $this->rgbFromColor($im, $bottom);

        for ($y = 0; $y < self::H; $y++) {
            $t = $y / self::H;
            $r = (int) ($topRgb[0] + ($botRgb[0] - $topRgb[0]) * $t);
            $g = (int) ($topRgb[1] + ($botRgb[1] - $topRgb[1]) * $t);
            $b = (int) ($topRgb[2] + ($botRgb[2] - $topRgb[2]) * $t);
            $c = imagecolorallocate($im, $r, $g, $b);
            imageline($im, 0, $y, self::W, $y, $c);
        }
    }

    private function rgbFromColor(\GdImage $im, int $color): array
    {
        return [
            ($color >> 16) & 0xFF,
            ($color >> 8) & 0xFF,
            $color & 0xFF,
        ];
    }

    private function hexToRgb(string $hex): array
    {
        $hex = ltrim($hex, '#');
        if (strlen($hex) !== 6) return [30, 64, 175];
        return [
            hexdec(substr($hex, 0, 2)),
            hexdec(substr($hex, 2, 2)),
            hexdec(substr($hex, 4, 2)),
        ];
    }

    private function serveFile(string $path): Response
    {
        return response(file_get_contents($path), 200, [
            'Content-Type'  => 'image/png',
            // URL sabitken içerik değişebilir (dil düzeltmesi, og:refresh) → immutable değil
            'Cache-Control' => 'public, max-age=604800',
            'Content-Length' => (string) filesize($path),
        ]);
    }
}
