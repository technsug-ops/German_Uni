<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\FieldOfStudy;
use App\Models\Post;
use App\Models\Profession;
use App\Models\Program;
use App\Models\Scholarship;
use App\Models\University;
use Illuminate\Http\Response;

/**
 * Dinamik og:image (1200x630) üretici.
 *
 * /og/{type}/{slug}.png — ilk istekte üretilir, storage/app/public/og/'a cache'lenir.
 * Cache-Control: public, max-age=30 gün. Browser+CDN dostu.
 */
class OgImageController extends Controller
{
    private const W = 1200;
    private const H = 630;

    private const FONT_BOLD = 'resources/fonts/Inter-Bold.ttf';
    private const FONT_REG  = 'resources/fonts/Inter-Regular.ttf';

    public function show(string $type, string $slug)
    {
        $cleanSlug = preg_replace('/\.png$/', '', $slug);

        // DUAL-BRAND: cache key brand'e göre ayrı — iki domain farklı OG'ler üretir
        $brandKey = brand_key();
        $cachePath = "og/{$brandKey}/{$type}-{$cleanSlug}.png";
        $diskPath  = storage_path('app/public/' . $cachePath);

        if (file_exists($diskPath)) {
            return $this->serveFile($diskPath);
        }

        $data = $this->fetchData($type, $cleanSlug);
        if (! $data) {
            abort(404);
        }

        @mkdir(dirname($diskPath), 0775, true);
        $im = $this->renderImage($data);
        imagepng($im, $diskPath, 6);
        imagedestroy($im);

        return $this->serveFile($diskPath);
    }

    private function fetchData(string $type, string $slug): ?array
    {
        $fontReg  = base_path(self::FONT_REG);
        $fontBold = base_path(self::FONT_BOLD);
        if (! file_exists($fontBold) || ! file_exists($fontReg)) {
            return null;
        }

        return match ($type) {
            'university' => $this->forUniversity($slug),
            'program'    => $this->forProgram($slug),
            'city'       => $this->forCity($slug),
            'field'      => $this->forField($slug),
            'profession' => $this->forProfession($slug),
            'post'       => $this->forPost($slug),
            'scholarship'=> $this->forScholarship($slug),
            default      => null,
        };
    }

    private function forUniversity(string $slug): ?array
    {
        $u = University::with('city.state')->where('slug', $slug)->first();
        if (! $u) return null;

        $sub = collect([$u->city?->name, $u->city?->state?->name])->filter()->join(' · ');

        return [
            'badge'    => mb_strtoupper(__('University')),
            'title'    => $u->short_name ?: $u->name_de,
            'subtitle' => $sub ?: __('Germany'),
            'meta'     => $u->type_label ?? 'Hochschule',
            'color'    => '#1E40AF',
        ];
    }

    private function forProgram(string $slug): ?array
    {
        $p = Program::with('university')->where('slug', $slug)->first();
        if (! $p) return null;

        return [
            'badge'    => mb_strtoupper($p->degree ?? 'Programm'),
            'title'    => $p->name_de,
            'subtitle' => $p->university?->name_de ?: '',
            'meta'     => $p->teaching_language === 'en' ? __('Taught in English') : __('Taught in German'),
            'color'    => '#0F766E',
        ];
    }

    private function forCity(string $slug): ?array
    {
        $c = City::with('state')->where('slug', $slug)->first();
        if (! $c) return null;

        $uniCount = $c->universities()->where('is_active', true)->count();
        return [
            'badge'    => mb_strtoupper(__('City')),
            'title'    => $c->name,
            'subtitle' => $c->state?->name ?: __('Germany'),
            'meta'     => $uniCount . ' ' . __('universities'),
            'color'    => '#B45309',
        ];
    }

    private function forField(string $slug): ?array
    {
        $f = FieldOfStudy::where('slug', $slug)->first();
        if (! $f) return null;
        return [
            'badge'    => mb_strtoupper(__('Study Field')),
            'title'    => $f->name,
            'subtitle' => $f->name_de,
            'meta'     => ($f->icon ? $f->icon . ' ' : '') . __('Germany'),
            'color'    => $f->color ?: '#7C3AED',
        ];
    }

    private function forProfession(string $slug): ?array
    {
        $p = Profession::with('field')->where('slug', $slug)->first();
        if (! $p) return null;
        return [
            'badge'    => mb_strtoupper(__('Profession')),
            'title'    => $p->name ?: $p->name_de,
            'subtitle' => ($p->name && $p->name !== $p->name_de) ? $p->name_de : ($p->field?->name ?? __('Germany')),
            'meta'     => $p->kldb_code ? "KldB {$p->kldb_code}" : 'BERUFENET',
            'color'    => $p->field?->color ?: '#0369A1',
        ];
    }

    private function forPost(string $slug): ?array
    {
        $p = Post::where('slug', $slug)->first();
        if (! $p) return null;
        return [
            'badge'    => 'BLOG',
            'title'    => $p->title,
            'subtitle' => $p->category?->name ?: brand('name'),
            'meta'     => $p->reading_minutes ? $p->reading_minutes . ' dk okuma' : '',
            'color'    => '#BE185D',
        ];
    }

    private function forScholarship(string $slug): ?array
    {
        $s = Scholarship::where('slug', $slug)->first();
        if (! $s) return null;
        return [
            'badge'    => $s->is_daad ? 'DAAD BURSU' : 'BURS',
            'title'    => $s->name_en ?: $s->name_de,
            'subtitle' => $s->programmname_en ?: ($s->programmname_de ?: 'Almanya bursu'),
            'meta'     => 'Stipendium',
            'color'    => '#15803D',
        ];
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
        $brandName    = brand('name');
        $brandTagline = brand('tagline') ?: '';
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

        imagettftext($im, 18, 0, $padX, self::H - 28, $white, $fontReg, brand('domain'));
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
            'Cache-Control' => 'public, max-age=2592000, immutable',
            'Content-Length' => (string) filesize($path),
        ]);
    }
}
