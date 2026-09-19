<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * hk_catalog'daki (resmi Hochschulkompass kataloğu) programlardan, bizde KARŞILIĞI OLMAYANLARI
 * bir veri dosyasına çıkarır. Dosya migration ile uygulanır — çünkü prod'da hk_catalog boş;
 * tıpkı hk-admission.json akışında olduğu gibi veri repoda taşınır.
 *
 * Neden gerekli: `programs` tablosu ağırlıklı olarak partner/DAAD beslemesinden geliyor
 * (uluslararası odaklı). Almanca öğretim yapan klasik Alman programları büyük ölçüde eksik —
 * ölçüm: HK lisanslarının %86'sının, master'ların %79'unun karşılığı yok. Kullanıcı
 * "Almanca ekonomi nerede okunur" diye sorduğunda site eksik cevap veriyor.
 *
 * Güvenlik: yalnızca ÇIKTI üretir, veritabanına yazmaz. Eşleşmeyen kurumları atlar,
 * mevcut programla adı çakışanı atlar. Uygulama kararı migration'da.
 */
class ExportHkPrograms extends Command
{
    protected $signature = 'programs:export-hk
        {--field=hukuk-ekonomi : hedef alan slug}
        {--out=resources/data/hk-programs-hukuk-ekonomi.json}
        {--limit=0}';

    protected $description = 'hk_catalog\'da olup bizde olmayan programları alan bazında veri dosyasına çıkarır (yazmaz).';

    /**
     * Alan sınıflandırması: HK'da konu grubu alanı YOK, yalnızca program adı var.
     * Bu yüzden anahtar kelimeyle sınıflıyoruz. `exclude` listesi, adı benzeyip BAŞKA alana
     * ait olanları ayıklar (Wirtschaftsinformatik → bilişim, Wirtschaftsingenieur → mühendislik).
     */
    private const FIELD_RULES = [
        'hukuk-ekonomi' => [
            'include' => [
                'wirtschaft', 'ökonom', 'okonom', 'economics', 'betriebswirt', 'volkswirt',
                'management', 'finance', 'finanz', 'rechnungswesen', 'controlling', 'marketing',
                'steuer', 'recht', 'jura', 'law', 'logistik', 'supply chain', 'immobilien',
                'versicherung', 'banking', 'accounting', 'business',
            ],
            'exclude' => [
                'wirtschaftsinformatik', 'wirtschaftsingenieur', 'ingenieur', 'informatik',
                'psycholog', 'medienmanagement', 'sportmanagement', 'gesundheitsmanagement',
                'tourismusmanagement', 'kulturmanagement', 'agrarmanagement',
                'data science', 'datenwissenschaft', 'mathematik', 'architekt', 'design',
                'pflege', 'medizin', 'soziale arbeit', 'bauingenieur', 'maschinenbau',
            ],
        ],
    ];

    public function handle(): int
    {
        $fieldSlug = (string) $this->option('field');
        $rules = self::FIELD_RULES[$fieldSlug] ?? null;
        if (! $rules) {
            $this->error("Bu alan için kural tanımlı değil: {$fieldSlug}");

            return self::FAILURE;
        }

        if (! DB::table('hk_catalog')->exists()) {
            $this->error('hk_catalog boş — önce programs:load-hk-backup çalıştır.');

            return self::FAILURE;
        }

        // Kurum eşleştirme: ad normalizasyonu + elle alias dosyası (slug ile taşınır, ID ile değil).
        $uniBySlugKey = [];
        $uniNameBySlug = [];
        foreach (DB::table('universities')->where('is_active', 1)->get(['id', 'name_de', 'slug']) as $u) {
            $uniBySlugKey[$this->normUni($u->name_de)] = $u->slug;
            $uniNameBySlug[$u->slug] = $u->name_de;
        }
        $aliases = [];
        $aliasFile = base_path('data/hk-uni-aliases.json');
        if (is_file($aliasFile)) {
            $aliases = json_decode((string) file_get_contents($aliasFile), true) ?: [];
        }

        // Mevcut programlar (üni slug + normalize ad) — aynı programı ikinci kez yaratmamak için.
        $existing = [];
        foreach (DB::table('programs as p')->join('universities as u', 'u.id', '=', 'p.university_id')
            ->where('p.is_active', 1)->select('u.slug as uslug', 'p.name_de as name')->get() as $r) {
            $existing[$r->uslug . '|' . $this->normProg((string) $r->name)] = true;
        }

        $stats = ['aday' => 0, 'alan_disi' => 0, 'uni_yok' => 0, 'zaten_var' => 0, 'yeni' => 0];
        $out = [];
        $seen = [];

        foreach (DB::table('hk_catalog')->get(['hochschule', 'fach', 'abschluss', 'zulassung', 'ort', 'form']) as $r) {
            $fach = mb_strtolower((string) $r->fach, 'UTF-8');

            $match = false;
            foreach ($rules['include'] as $k) {
                if (str_contains($fach, $k)) { $match = true; break; }
            }
            if ($match) {
                foreach ($rules['exclude'] as $k) {
                    if (str_contains($fach, $k)) { $match = false; break; }
                }
            }
            if (! $match) {
                $stats['alan_disi']++;
                continue;
            }
            $stats['aday']++;

            $uniSlug = $uniBySlugKey[$this->normUni((string) $r->hochschule)]
                ?? ($aliases[$r->hochschule] ?? null);
            if (! $uniSlug || ! is_string($uniSlug)) {
                $stats['uni_yok']++;
                continue;
            }

            $key = $uniSlug . '|' . $this->normProg((string) $r->fach);
            if (isset($existing[$key]) || isset($seen[$key])) {
                $stats['zaten_var']++;
                continue;
            }
            $seen[$key] = true;

            $degree = $this->degree((string) $r->abschluss);
            $out[] = [
                'university_slug' => $uniSlug,
                // Prod'da slug farklı olabilir (bilinen tuzak) → migration ad üzerinden de eşleştirebilsin.
                'university_name' => $uniNameBySlug[$uniSlug] ?? null,
                'name_de'         => $this->cleanName((string) $r->fach),
                'degree'          => $degree,
                'degree_spec'     => trim((string) $r->abschluss) ?: null,
                // HK öğretim dilini AYRI bir alan olarak vermiyor. Katalog Almanya'nın resmi
                // program kataloğu ve ezici çoğunluğu Almanca; ancak HK'nın kendi
                // "Internationaler Studiengang" işareti varsa dil belirsizdir → NULL bırakılır
                // (yanlış "Almanca" etiketi, dil filtresini güvenilmez yapardı).
                'language'        => str_contains((string) $r->form, 'Internationaler Studiengang') ? null : 'de',
                'admission_mode'  => in_array($r->zulassung, ['zulassungsfrei', 'oertlich', 'bundesweit', 'auswahl'], true)
                    ? $r->zulassung : null,
                'location'        => trim((string) $r->ort) ?: null,
                'study_form'      => trim((string) $r->form) ?: null,
                'slug'            => $this->buildSlug($uniSlug, (string) $r->fach, $degree),
            ];
            $stats['yeni']++;

            if (($lim = (int) $this->option('limit')) > 0 && $stats['yeni'] >= $lim) {
                break;
            }
        }

        $path = base_path((string) $this->option('out'));
        @mkdir(dirname($path), 0775, true);
        file_put_contents($path, json_encode([
            'field'        => $fieldSlug,
            'source'       => 'hochschulkompass (hk_catalog)',
            'generated_at' => now()->toIso8601String(),
            'note'         => 'Bizde karşılığı olmayan HK programları. Migration slug bazlı ve idempotent uygular.',
            'programs'     => $out,
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

        foreach ($stats as $k => $v) {
            $this->line(str_pad($k, 12) . $v);
        }
        $this->info('Yazıldı: ' . $path);

        return self::SUCCESS;
    }

    /** "Bachelor in Betriebswirtschaftslehre" → "Betriebswirtschaftslehre" (derece adı ada karışmasın). */
    private function cleanName(string $name): string
    {
        $n = trim($name);
        $n = preg_replace('/^(bachelor|master)\s+(in|of|der|für)\s+/iu', '', $n);
        $n = preg_replace('/^(bachelor|master)\s+/iu', '', $n);
        $n = preg_replace('/\s+/u', ' ', $n);

        return trim($n);
    }

    private function degree(string $abschluss): string
    {
        $a = mb_strtolower($abschluss, 'UTF-8');
        if (str_contains($a, 'bachelor') || str_contains($a, 'lehramt')) {
            return 'bachelor';
        }
        if (str_contains($a, 'master')) {
            return 'master';
        }
        if (str_contains($a, 'promotion') || str_contains($a, 'doktor')) {
            return 'phd';
        }

        return 'other';
    }

    /** Deterministik slug: aynı girdi her ortamda aynı slug'ı üretmeli (prod ID'lerinden bağımsız). */
    private function buildSlug(string $uniSlug, string $name, string $degree): string
    {
        $base = Str::limit(Str::slug($name), 140, '') . '-' . $degree;
        $hash = substr(md5($uniSlug . '|' . mb_strtolower($name, 'UTF-8') . '|' . $degree), 0, 8);

        return $base . '-hk' . $hash;
    }

    private function normUni(string $s): string
    {
        $s = mb_strtolower($s, 'UTF-8');
        $s = strtr($s, ['ä' => 'ae', 'ö' => 'oe', 'ü' => 'ue', 'ß' => 'ss']);
        $s = preg_replace('/\s[-–:].*$/u', ' ', $s);
        $s = preg_replace('/,.*$/u', ' ', $s);
        $s = preg_replace('/\(.*?\)/', ' ', $s);
        $s = preg_replace('/\b(university of applied sciences|university|universitaet|hochschule|fachhochschule|technische|fh|uni)\b/u', ' ', $s);

        return preg_replace('/[^a-z0-9]+/', '', $s);
    }

    private function normProg(string $s): string
    {
        $s = mb_strtolower($s, 'UTF-8');
        $s = strtr($s, ['ä' => 'ae', 'ö' => 'oe', 'ü' => 'ue', 'ß' => 'ss']);
        $s = preg_replace('/\b(b\.?\s?sc|m\.?\s?sc|b\.?\s?a|m\.?\s?a|bachelor|master|of science|of arts)\b\.?/u', ' ', $s);
        $s = preg_replace('/\(.*?\)/', ' ', $s);

        return preg_replace('/[^a-z0-9]+/', '', $s);
    }
}
