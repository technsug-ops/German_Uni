<?php

namespace App\Services\Mail;

/**
 * Panelde yazılan gövdeyi e-posta istemcilerinin anladığı HTML'e çevirir.
 *
 * İki girdi biçimini birden kabul eder:
 *  - RichEditor HTML'i (<p>, <ul>, <a>, <img>, <blockquote> …)
 *  - Eski düz metin şablonları (2026_06_18_095500_seed_email_templates)
 * Böylece mevcut üç partnerlik şablonu hiç dokunmadan çalışmaya devam eder.
 *
 * Neden satır-içi stil: Gmail <head> içindeki <style> bloğunu tamamen atar,
 * Outlook ise Word motoruyla çizdiği için modern CSS'in çoğunu yok sayar.
 * E-postada tek güvenilir yol her etikete style yazmaktır.
 */
class MailBody
{
    /** Etiket => satır-içi stil. Tablo/eklenti etiketleri bilinçli olarak sade. */
    private const STYLES = [
        'p'          => 'margin:0 0 16px;font-size:15px;line-height:1.65;color:#374151;',
        'h1'         => 'margin:26px 0 12px;font-size:21px;line-height:1.3;font-weight:700;color:#111827;',
        'h2'         => 'margin:24px 0 10px;font-size:18px;line-height:1.35;font-weight:700;color:#111827;',
        'h3'         => 'margin:20px 0 8px;font-size:16px;line-height:1.4;font-weight:600;color:#111827;',
        'ul'         => 'margin:0 0 16px;padding-left:22px;',
        'ol'         => 'margin:0 0 16px;padding-left:22px;',
        'li'         => 'margin:0 0 7px;font-size:15px;line-height:1.6;color:#374151;',
        'blockquote' => 'margin:0 0 16px;padding:10px 0 10px 16px;border-left:3px solid #E5E7EB;color:#4B5563;font-size:15px;line-height:1.65;',
        'a'          => 'color:#1E40AF;text-decoration:underline;',
        'img'        => 'display:block;max-width:100%;height:auto;border:0;border-radius:8px;margin:6px 0 18px;',
        'hr'         => 'border:0;border-top:1px solid #E5E7EB;margin:24px 0;',
        'table'      => 'border-collapse:collapse;width:100%;margin:0 0 16px;',
        'td'         => 'padding:8px 10px;border:1px solid #E5E7EB;font-size:14px;line-height:1.5;color:#374151;',
        'th'         => 'padding:8px 10px;border:1px solid #E5E7EB;font-size:14px;line-height:1.5;color:#111827;background:#F9FAFB;text-align:left;',
    ];

    /** Gövdeyi maile basılmaya hazır HTML'e çevirir. */
    public static function toHtml(?string $body): string
    {
        $body = trim((string) $body);

        if ($body === '') {
            return '';
        }

        return self::looksLikeHtml($body)
            ? self::fromHtml($body)
            : self::fromPlainText($body);
    }

    /**
     * Gövdeyi RichEditor'a yüklenebilir sade HTML'e çevirir.
     *
     * Neden gerekli: düz metin şablonu editöre olduğu gibi verilirse TipTap
     * satır sonlarını boşluğa çevirir ve şablonun paragraf/madde yapısı
     * tamamen kaybolur. Burada stil basılmaz — satır-içi stilleri gönderim
     * anında toHtml() ekler.
     */
    public static function toEditorHtml(?string $body): string
    {
        $body = trim((string) $body);

        if ($body === '' || self::looksLikeHtml($body)) {
            return $body;
        }

        // Stilli çıktıdan style özniteliklerini sök: editörde temiz HTML dursun.
        return preg_replace('/\s*style\s*=\s*"[^"]*"/i', '', self::fromPlainText($body));
    }

    /**
     * Gövdenin düz metin karşılığı. Çok parçalı (text + html) mail göndermek
     * spam puanını belirgin şekilde düşürür; HTML'i olup metni olmayan mail
     * birçok filtrede doğrudan şüpheli sayılır.
     */
    public static function toText(?string $body): string
    {
        $body = trim((string) $body);

        if ($body === '') {
            return '';
        }

        if (! self::looksLikeHtml($body)) {
            return $body;
        }

        // HTML tarafıyla aynı temizlikten geçir: aksi hâlde <script> gövdesi
        // strip_tags sonrası düz metin parçasına okunur biçimde sızar.
        $body = self::absolutize(self::sanitize($body));

        // Bağlantıyı "metin (adres)" olarak koru — düz metinde tıklanacak bir şey kalsın.
        $text = preg_replace_callback(
            '/<a\b[^>]*href\s*=\s*("[^"]*"|\'[^\']*\')[^>]*>(.*?)<\/a>/is',
            function ($m) {
                $href  = trim($m[1], '"\'');
                $label = trim(html_entity_decode(strip_tags($m[2]), ENT_QUOTES, 'UTF-8'));

                return $label === '' || $label === $href ? $href : "{$label} ({$href})";
            },
            $body
        );

        // </li> bilinçli olarak yok: <li> zaten satır başı açıyor, ikisi
        // birden maddeler arasına boş satır koyardı.
        $text = preg_replace('/<li\b[^>]*>/i', "\n• ", $text);
        $text = preg_replace('/<(br|\/p|\/h[1-6]|\/tr)\b[^>]*>/i', "\n", $text);
        $text = preg_replace('/<\/(ul|ol|blockquote|table)>/i', "\n", $text);
        $text = strip_tags($text);
        $text = html_entity_decode($text, ENT_QUOTES, 'UTF-8');
        $text = preg_replace("/[ \t]+\n/", "\n", $text);
        $text = preg_replace("/\n{3,}/", "\n\n", $text);

        return trim($text);
    }

    private static function looksLikeHtml(string $body): bool
    {
        return (bool) preg_match('/<(p|div|ul|ol|h[1-6]|br|strong|em|a|img|blockquote|table)\b/i', $body);
    }

    /**
     * Düz metin → paragraf/liste. Boş satır paragrafı böler; "•", "-" veya "*"
     * ile başlayan ardışık satırlar madde listesine dönüşür (mevcut Almanca
     * partnerlik şablonu tam olarak bu biçimde yazılmış).
     */
    private static function fromPlainText(string $body): string
    {
        $body   = str_replace(["\r\n", "\r"], "\n", $body);
        $blocks = preg_split('/\n\s*\n/', $body);
        $out    = '';

        foreach ($blocks as $block) {
            $block = trim($block, "\n");

            if (trim($block) === '') {
                continue;
            }

            $lines     = explode("\n", $block);
            $isBullets = count(array_filter($lines, fn ($l) => (bool) preg_match('/^\s*[•\-\*]\s+/u', $l))) === count($lines);

            if ($isBullets) {
                $out .= '<ul style="' . self::STYLES['ul'] . '">';
                foreach ($lines as $line) {
                    $line = preg_replace('/^\s*[•\-\*]\s+/u', '', $line);
                    $out .= '<li style="' . self::STYLES['li'] . '">' . self::autoLink(e($line)) . '</li>';
                }
                $out .= '</ul>';

                continue;
            }

            $html = self::autoLink(e($block));
            $out .= '<p style="' . self::STYLES['p'] . '">' . nl2br($html) . '</p>';
        }

        return $out;
    }

    /** RichEditor HTML'i: önce temizle, sonra her etikete satır-içi stil bas. */
    private static function fromHtml(string $html): string
    {
        $html = self::sanitize($html);
        $html = self::absolutize($html);

        $tags = implode('|', array_keys(self::STYLES));

        return preg_replace_callback(
            '/<(' . $tags . ')(\s[^>]*)?>/i',
            function ($m) {
                $tag   = strtolower($m[1]);
                $attrs = $m[2] ?? '';

                // Editörde verilen hizalamayı koru, geri kalan style'ı at:
                // istemciye giden tek stil kaynağı bu sınıf olsun.
                $align = '';
                if (preg_match('/style\s*=\s*("[^"]*"|\'[^\']*\')/i', $attrs, $s)
                    && preg_match('/text-align\s*:\s*(left|right|center|justify)/i', $s[1], $a)) {
                    $align = 'text-align:' . strtolower($a[1]) . ';';
                }

                $attrs = preg_replace('/\s*style\s*=\s*("[^"]*"|\'[^\']*\')/i', '', $attrs);
                $attrs = preg_replace('/\s*class\s*=\s*("[^"]*"|\'[^\']*\')/i', '', $attrs);

                $style = self::STYLES[$tag] . $align;

                // Outlook, img'e width verilmediğinde gerçek piksel boyutunu kullanır.
                if ($tag === 'img' && ! preg_match('/\bwidth\s*=/i', $attrs)) {
                    $attrs .= ' width="560"';
                }

                return '<' . $tag . rtrim($attrs) . ' style="' . $style . '">';
            },
            $html
        );
    }

    /**
     * Gövde admin panelinden geliyor ama yine de dışarı gönderilen HTML —
     * script/iframe ve olay öznitelikleri her hâlükârda temizlenir.
     */
    private static function sanitize(string $html): string
    {
        $html = preg_replace('/<(script|style|iframe|object|embed|form)\b[^>]*>.*?<\/\1>/is', '', $html);
        $html = preg_replace('/<\/?(script|style|iframe|object|embed|form)\b[^>]*>/i', '', $html);
        $html = preg_replace('/\s*on\w+\s*=\s*("[^"]*"|\'[^\']*\')/i', '', $html);
        $html = preg_replace('/(href|src)\s*=\s*("|\')\s*javascript:[^"\']*\2/i', '$1="#"', $html);

        return $html;
    }

    /**
     * Göreli adresleri mutlak hâle getirir. Mailde "/storage/..." ya da
     * "/tr/universiteler" çalışmaz — istemcinin kök alan adı yoktur.
     */
    private static function absolutize(string $html): string
    {
        $base = rtrim((string) config('app.url'), '/');

        if ($base === '') {
            return $html;
        }

        return preg_replace(
            '/(href|src)\s*=\s*("|\')\/(?!\/)/i',
            '$1=$2' . $base . '/',
            $html
        );
    }

    /** Düz metindeki çıplak adresleri tıklanabilir yapar. */
    private static function autoLink(string $escaped): string
    {
        return preg_replace(
            '/(?<![">])(https?:\/\/[^\s<]+[^\s<.,;:!?)\]])/i',
            '<a href="$1" style="' . self::STYLES['a'] . '">$1</a>',
            $escaped
        );
    }
}
