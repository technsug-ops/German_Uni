<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Üniversite sayfalarındaki "duvar metin" sorununu çözer: content_blocks içindeki
 * uzun tek-paragraf body_md gövdelerini ve description_* fallback'lerini cümle
 * sınırında ~3 cümlelik paragraflara böler (mobil okunabilirlik).
 *
 * Güvenli & idempotent:
 *  - Zaten paragraflanmış (\n\n içeren) metne dokunmaz.
 *  - Blok-seviye markdown (başlık/liste/tablo/alıntı) içereni bölmez.
 *  - Kısaltmalar (z.B., d.h., Dr., ör., vb.) ve ondalık sayılar (1.583) cümle
 *    sonu sanılmaz.
 *  - Sadece WHITESPACE ekler; hiçbir kelime/karakter silinmez/değişmez.
 */
return new class extends Migration
{
    public function up(): void
    {
        $descCols  = ['description_tr', 'description_en', 'description_de'];
        $blockCols = ['content_blocks', 'content_blocks_en', 'content_blocks_de'];

        DB::table('universities')->orderBy('id')->chunkById(200, function ($rows) use ($descCols, $blockCols) {
            foreach ($rows as $r) {
                $update = [];

                foreach ($descCols as $col) {
                    $v = $r->{$col} ?? null;
                    if (is_string($v) && trim($v) !== '') {
                        $n = $this->reparagraph($v);
                        if ($n !== $v && $this->sameContent($v, $n)) {
                            $update[$col] = $n;
                        }
                    }
                }

                foreach ($blockCols as $col) {
                    $raw = $r->{$col} ?? null;
                    if (! is_string($raw) || trim($raw) === '') {
                        continue;
                    }
                    $blocks = json_decode($raw, true);
                    if (! is_array($blocks)) {
                        continue;
                    }
                    $changed = false;
                    foreach ($blocks as &$b) {
                        if (! is_array($b) || ! isset($b['body_md']) || ! is_string($b['body_md'])) {
                            continue;
                        }
                        $body = $b['body_md'];
                        if (trim($body) === '') {
                            continue;
                        }
                        $n = $this->reparagraph($body);
                        if ($n !== $body && $this->sameContent($body, $n)) {
                            $b['body_md'] = $n;
                            $changed = true;
                        }
                    }
                    unset($b);
                    if ($changed) {
                        $update[$col] = json_encode($blocks, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                    }
                }

                if ($update) {
                    DB::table('universities')->where('id', $r->id)->update($update);
                }
            }
        });
    }

    public function down(): void
    {
        // Bilinçli no-op: orijinal "duvar" hâline geri dönmek istemiyoruz (idempotent).
    }

    /** Sadece whitespace farkı olduğunu doğrula — içerik kaybı/değişimi guard'ı. */
    private function sameContent(string $a, string $b): bool
    {
        $norm = fn (string $s) => preg_replace('/\s+/u', ' ', trim($s));

        return $norm($a) === $norm($b);
    }

    /** Uzun tek-paragraf prose'u ~3 cümlelik paragraflara böler. */
    private function reparagraph(string $text): string
    {
        $text = trim(str_replace("\r\n", "\n", $text));
        if ($text === '') {
            return '';
        }

        // Zaten paragraflara ayrılmış → dokunma.
        if (str_contains($text, "\n\n")) {
            return $text;
        }

        // Blok-seviye markdown içeriyorsa bölme; sadece tek satır kırıklarını paragrafa çevir.
        foreach (explode("\n", $text) as $line) {
            if (preg_match('/^\s*(#{1,6}\s|[-*+]\s|\d+\.\s|>|\|)/u', $line)) {
                return $text;
            }
        }

        $sentences = $this->splitSentences($text);
        if (count($sentences) <= 2) {
            return $text; // zaten kısa, bölmeye değmez
        }

        $paras = [];
        $buf   = '';
        $cnt   = 0;
        foreach ($sentences as $s) {
            $buf = $buf === '' ? $s : $buf . ' ' . $s;
            $cnt++;
            if ($cnt >= 3 && mb_strlen($buf) >= 240) {
                $paras[] = $buf;
                $buf     = '';
                $cnt     = 0;
            }
        }
        if (trim($buf) !== '') {
            // Tek cümlelik kuyruğu önceki paragrafa ekle (öksüz satır olmasın).
            if ($paras && $cnt < 2) {
                $paras[count($paras) - 1] .= ' ' . $buf;
            } else {
                $paras[] = $buf;
            }
        }

        return implode("\n\n", $paras);
    }

    /** TR/DE-güvenli cümle bölme: kısaltma + ondalık koruması ile. */
    private function splitSentences(string $text): array
    {
        $abbr = [
            'z.B.', 'u.a.', 'd.h.', 'u.U.', 'ca.', 'bzw.', 'usw.', 'etc.', 'vs.',
            'Dr.', 'Prof.', 'Nr.', 'Mio.', 'Mrd.', 'St.', 'Min.',
            'ör.', 'örn.', 'vb.', 'bkz.', 'Bkz.', 'yakl.', 'yy.',
        ];
        $map = [];
        foreach ($abbr as $i => $a) {
            $k       = "\x01" . $i . "\x01";
            $map[$k] = $a;
            $text    = str_replace($a, $k, $text);
        }
        // Ondalık/sayısal nokta (1.583, 12.000) korunur.
        $text = preg_replace_callback('/\d+\.\d+/', fn ($m) => str_replace('.', "\x02", $m[0]), $text);

        $parts = preg_split('/(?<=[.!?])\s+(?=[A-ZÄÖÜİŞĞÇ0-9"„(])/u', $text) ?: [$text];

        $restore = function (string $s) use ($map) {
            $s = str_replace("\x02", '.', $s);

            return strtr($s, $map);
        };

        return array_values(array_filter(
            array_map(fn ($s) => trim($restore($s)), $parts),
            fn ($s) => $s !== ''
        ));
    }
};
