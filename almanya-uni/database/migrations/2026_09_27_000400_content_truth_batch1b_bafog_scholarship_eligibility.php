<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Content Truth Sprint — Batch 1B: BAföG burs kaydı (DAAD veritabanı) uygunluk düzeltmesi.
 *
 * Sorun: kayıt TR/EN/DE'de "Eligible countries (211)" listesi ve DE/EN "Eligibility" bölümünde DAAD'ın 211 ülke
 * adını içeren anahtar-kelime yığınını (qDe/qEn) gösteriyordu; TR metni "Türkiye dahil, vatandaşlarının BAföG'den
 * faydalanabileceği ülkeler listesi" diyordu. Doğrusu: uluslararası öğrenciler için uygunluk § 8 BAföG'deki
 * oturum/statü koşullarına bağlıdır; vatandaşlık tek başına belirleyici değildir; § 16b tek başına sayılmaz.
 * Kalıcılık: daad:scholarships:sync bu kayıtta artık origin bağlamıyor ve qDe/qEn'i ezmiyor
 * (DaadScholarshipsSync::CURATED_ELIGIBILITY). Şablon değişmedi: origins boşsa ülke listesi gösterilmez.
 *
 * Kimlik: sap_objid (DAAD kalıcı dış kimliği, tabloda unique) + adın "BAföG" içermesi; tam 1 eşleşme şart.
 * Ön kontrol: eski durum (origins dolu + TR'de ülke listesi + EN/DE'de ülke yığını) ya da tamamen yeni durum;
 * karışık/beklenmeyen durum → RuntimeException, hiçbir şey yazılmaz. Yazma tek transaction; ikinci çalıştırma no-op.
 */
return new class extends Migration
{
    private const SAP_OBJID = 20000162;

    public function up(): void
    {
        $new = [
            'q_tr_json' => <<<'HTML'
<p>BAföG (Bundesausbildungsförderungsgesetz), Almanya'da öğrenimi destekleyen devlet desteğidir; yükseköğretimde genellikle yarısı hibe, yarısı faizsiz kredi olarak verilir.</p>
<p><strong>Uluslararası öğrenciler için uygunluk vatandaşlığa göre değil, oturum statüsüne göre belirlenir.</strong> Hangi yabancı uyruklu öğrencilerin BAföG alabileceğini § 8 BAföG düzenler; örneğin belirli koşullarla AB/AEA vatandaşları, daimi oturum izni (Niederlassungserlaubnis) sahipleri, tanınmış mülteciler ve bazı insani oturum izni sahipleri, belirli aile üyeleri ile Almanya'da belirli bir süre yaşamış veya çalışmış kişiler. Bir ülkenin vatandaşı olmak tek başına BAföG hakkı vermez.</p>
<p>Almanya'ya yalnızca öğrenim amaçlı oturum izniyle (§ 16b AufenthG) gelen öğrenciler, § 8 BAföG'de ayrı bir uygun statü olarak sayılmaz; bu nedenle çoğu zaman BAföG alamaz.</p>
<p>Başvuru, üniversitenin bağlı olduğu Studierendenwerk'teki BAföG ofisine (BAföG-Amt) yapılır. Kendi statünü bafög.de üzerinden ve BAföG ofisiyle kontrol et.</p>
HTML,
            'q_en_json' => <<<'HTML'
<p>BAföG (Federal Training Assistance Act) is Germany's state support for education; in higher education it is generally paid half as a grant and half as an interest-free loan.</p>
<p><strong>For international students, eligibility depends on residence status, not on nationality.</strong> Section 8 of the BAföG sets out which non-German students can qualify, for example EU/EEA citizens under certain conditions, holders of a permanent settlement permit (Niederlassungserlaubnis), recognised refugees and holders of certain humanitarian residence permits, certain family members, and people who have lived or worked in Germany for a specified period. Citizenship of a particular country does not on its own establish eligibility.</p>
<p>A residence permit for study purposes (Section 16b of the Residence Act) on its own is not listed as a qualifying status in Section 8, so most students who come to Germany only to study are not eligible.</p>
<p>Applications go to the BAföG office (BAföG-Amt) of the Studierendenwerk responsible for your university. Check your own status on bafög.de and with the BAföG office.</p>
HTML,
            'q_de_json' => <<<'HTML'
<p>Das BAföG (Bundesausbildungsförderungsgesetz) ist die staatliche Ausbildungsförderung; im Studium wird sie in der Regel zur Hälfte als Zuschuss und zur Hälfte als zinsloses Darlehen gezahlt.</p>
<p><strong>Für internationale Studierende hängt die Förderfähigkeit vom Aufenthaltsstatus ab, nicht von der Staatsangehörigkeit.</strong> § 8 BAföG regelt, welche ausländischen Studierenden gefördert werden können, zum Beispiel Unionsbürgerinnen und Unionsbürger unter bestimmten Voraussetzungen, Inhaberinnen und Inhaber einer Niederlassungserlaubnis, anerkannte Flüchtlinge und Personen mit bestimmten humanitären Aufenthaltstiteln, bestimmte Familienangehörige sowie Personen, die sich eine bestimmte Zeit in Deutschland aufgehalten haben oder hier erwerbstätig waren. Die Staatsangehörigkeit eines bestimmten Landes allein begründet keine Förderfähigkeit.</p>
<p>Eine Aufenthaltserlaubnis zum Studium (§ 16b AufenthG) allein wird in § 8 BAföG nicht als förderfähiger Status genannt; wer nur zum Studium nach Deutschland kommt, ist daher in der Regel nicht förderfähig.</p>
<p>Anträge werden beim BAföG-Amt des für die Hochschule zuständigen Studierendenwerks gestellt. Den eigenen Status auf bafög.de und beim BAföG-Amt prüfen.</p>
HTML,
        ];

        $rows = DB::table('scholarships')->where('sap_objid', self::SAP_OBJID)->get(['id', 'name_de', 'name_en', 'q_tr_json', 'q_en_json', 'q_de_json']);
        $problems = [];
        if ($rows->count() !== 1) {
            $problems[] = "sap_objid ".self::SAP_OBJID." eşleşme sayısı {$rows->count()} (beklenen 1)";
        } elseif (! str_contains($rows[0]->name_de.' '.$rows[0]->name_en, 'BAföG')) {
            $problems[] = 'kayıt adı BAföG içermiyor';
        }

        $pending = false;
        if (! $problems) {
            $row = $rows[0];
            $origins = DB::table('scholarship_origin')->where('scholarship_id', $row->id)->count();
            $cur = [];
            foreach (array_keys($new) as $f) {
                $v = json_decode((string) $row->{$f}, true);
                $cur[$f] = is_string($v) ? $v : (is_array($v) ? implode(' ', $v) : '');
            }
            $isNew = $origins === 0 && $cur['q_tr_json'] === $new['q_tr_json'] && $cur['q_en_json'] === $new['q_en_json'] && $cur['q_de_json'] === $new['q_de_json'];
            $isOld = $origins > 0
                && str_contains($cur['q_tr_json'], 'ülkeler listesi')
                && str_contains(mb_strtolower($cur['q_en_json']), 'afghanistan')
                && str_contains(mb_strtolower($cur['q_de_json']), 'afghanistan');
            if ($isNew) {
                return; // zaten uygulanmış — no-op
            }
            if (! $isOld) {
                $problems[] = "beklenmeyen durum (origins: {$origins}; TR ülke listesi: ".(str_contains($cur['q_tr_json'], 'ülkeler listesi') ? 'var' : 'yok').')';
            } else {
                $pending = true;
            }
        }

        if ($problems) {
            if (app()->runningUnitTests()) {
                return; // Test DB'si sıfırdan kurulur; DAAD burs kaydı orada yok. Hiçbir şey yazma.
            }
            throw new RuntimeException('Content Truth Batch 1B: ön kontrol başarısız, hiçbir şey yazılmadı. '.implode(' | ', $problems));
        }

        if ($pending) {
            DB::transaction(function () use ($rows, $new) {
                $id = $rows[0]->id;
                DB::table('scholarship_origin')->where('scholarship_id', $id)->delete();
                DB::table('scholarships')->where('id', $id)->update(array_map(
                    fn ($html) => json_encode($html, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                    $new
                ) + ['updated_at' => now()]);
            });
        }
    }

    public function down(): void
    {
        // Bilinçli olarak boş: forward-only içerik düzeltmesi.
    }
};
