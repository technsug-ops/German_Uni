<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

/**
 * phpBB forumunda çok-dilli kategori şablonu oluşturur (TR + EN + opsiyonel DE).
 *
 * phpBB Nested Set Model + ACL inheritance ile uyumludur:
 *  - left_id / right_id otomatik shift
 *  - Yeni forum, mevcut bir forumun ACL kayıtlarını şablon olarak kopyalar
 *    (yoksa kullanıcılar yeni forum'u göremez)
 */
class ForumSetupMultilang extends Command
{
    protected $signature = 'forum:setup-multilang
        {--dry-run : DB\'ye yazmadan göster}
        {--add-de : Almanca kategori grubu da ekle}';

    protected $description = 'phpBB forumunda TR + EN (+ opsiyonel DE) kategori şablonu oluştur';

    private \PDO $pdo;
    private array $templateCategory;
    private array $templateForum;
    private int $aclTemplateForumId;

    public function handle(): int
    {
        $this->pdo = new \PDO(
            'mysql:host=127.0.0.1;dbname=almanyauni_forum;charset=utf8mb4',
            'root',
            ''
        );
        $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

        $dry = (bool) $this->option('dry-run');
        $addDe = (bool) $this->option('add-de');

        // Şablon olarak mevcut kategori + forum kayıtlarını al
        $this->templateCategory = $this->pdo->query('SELECT * FROM phpbb_forums WHERE forum_type = 0 ORDER BY forum_id LIMIT 1')->fetch(\PDO::FETCH_ASSOC) ?: [];
        $this->templateForum    = $this->pdo->query('SELECT * FROM phpbb_forums WHERE forum_type = 1 ORDER BY forum_id LIMIT 1')->fetch(\PDO::FETCH_ASSOC) ?: [];

        if (! $this->templateCategory || ! $this->templateForum) {
            $this->error('Şablon yok. Önce phpBB installer\'dan en az 1 kategori + 1 forum oluştur.');
            return self::FAILURE;
        }

        $this->aclTemplateForumId = (int) $this->templateForum['forum_id'];
        $this->line('ACL şablonu: forum #' . $this->aclTemplateForumId);

        $existing = $this->pdo->query('SELECT forum_id, forum_name, parent_id, forum_type, left_id, right_id FROM phpbb_forums ORDER BY left_id')->fetchAll(\PDO::FETCH_ASSOC);
        $this->info('Mevcut yapı:');
        foreach ($existing as $f) {
            $this->line(sprintf('  #%d [t=%d] L=%d R=%d %s', $f['forum_id'], $f['forum_type'], $f['left_id'], $f['right_id'], $f['forum_name']));
        }
        $this->newLine();

        if ($dry) {
            $this->warn('DRY-RUN: kayıt yapılmayacak.');
        }

        $groups = [
            'tr' => [
                'category' => ['[TR] Türkçe Forum', 'Türk öğrenciler için ana topluluk'],
                'forums' => [
                    ['[TR] Tanışma & Genel Sohbet', 'Yeni gelenler, hoş geldiniz, off-topic konuşmalar'],
                    ['[TR] Başvuru, Vize & Belgeler', 'Uni-assist, VPD, vize, Sperrkonto, apostil, tercüme'],
                    ['[TR] Üniversiteler & Programlar', 'Spesifik üni soruları, program tercihleri, NC, kabul deneyimleri'],
                    ['[TR] Dil & Sınavlar', 'Almanca öğrenme, TestDaF, DSH, TestAS, IELTS, dil okulları'],
                    ['[TR] Yaşam & Şehirler', 'Yurt, ev (WG), sigorta, şehir deneyimleri, kültür'],
                    ['[TR] Werkstudent, Burs & Mezuniyet', 'İş arama, mini-job, DAAD, mezuniyet sonrası kalış'],
                ],
            ],
            'en' => [
                'category' => ['[EN] English Forum', 'For international students using English'],
                'forums' => [
                    ['[EN] Introductions & General', 'Welcome, off-topic, general chat'],
                    ['[EN] Application & Visa', 'Uni-assist, VPD, student visa, Sperrkonto, documents'],
                    ['[EN] Universities & Programs', 'Specific universities, program selection, NC, admissions'],
                    ['[EN] Language & Tests', 'German learning, TestDaF, DSH, IELTS, language schools'],
                    ['[EN] Living & Cities', 'Student dorm, WG, insurance, city experiences'],
                    ['[EN] Werkstudent, Scholarship & Graduation', 'Job hunt, mini-job, DAAD, post-graduation stay'],
                ],
            ],
        ];

        if ($addDe) {
            $groups['de'] = [
                'category' => ['[DE] Deutsches Forum', 'Für deutschsprachige internationale Studierende'],
                'forums' => [
                    ['[DE] Allgemein & Vorstellung', 'Willkommen, Vorstellung, Off-Topic'],
                    ['[DE] Bewerbung & Visum', 'Uni-assist, VPD, Studentenvisum, Sperrkonto'],
                    ['[DE] Universitäten & Studiengänge', 'Spezifische Unis, NC, Zulassung'],
                    ['[DE] Leben & Städte', 'Studentenwohnheim, WG, Versicherung'],
                ],
            ];
        }

        foreach ($groups as $lang => $g) {
            [$catName, $catDesc] = $g['category'];

            $catId = $this->insertWithNestedSet($catName, $catDesc, 0, 0, $dry);
            if ($catId === 0 && ! $dry) continue; // already exists, skip children too (basic guard)
            if ($catId > 0) $this->copyAcl($catId);

            foreach ($g['forums'] as [$fname, $fdesc]) {
                $fid = $this->insertWithNestedSet($fname, $fdesc, 1, $catId ?: 0, $dry);
                if ($fid > 0) $this->copyAcl($fid);
            }
        }

        if (! $dry) {
            $this->clearPhpbbCache();
            // Auth cache'i version bump et — yeni forumların auth resync olması için
            $this->pdo->exec("UPDATE phpbb_config SET config_value = config_value + 1 WHERE config_name = 'assets_version'");
        }

        $this->newLine();
        $this->info('✅ Tamamlandı. Forum index sayfasını yenile.');
        $this->line('   Sorun olursa: ACP → Permissions → Apply default rights');

        return self::SUCCESS;
    }

    /**
     * Nested Set Model'e uygun yeni forum / kategori ekle.
     * @return int Yeni forum_id, exists ise 0, dry-run'da 0.
     */
    private function insertWithNestedSet(string $name, string $desc, int $type, int $parentId, bool $dry): int
    {
        // Var olan kayıt kontrolü
        $stmt = $this->pdo->prepare('SELECT forum_id FROM phpbb_forums WHERE forum_name = ? AND forum_type = ? LIMIT 1');
        $stmt->execute([$name, $type]);
        $id = $stmt->fetchColumn();
        if ($id) {
            $this->line(sprintf('  ✓ %s mevcut: #%d %s', $type === 0 ? 'Kategori' : 'Forum', $id, $name));
            return 0;
        }

        if ($dry) {
            $this->line(sprintf('  + Yeni %s: %s', $type === 0 ? 'kategori' : 'forum', $name));
            return 0;
        }

        // Insertion point: parent.right_id'nin yerine yeni kayıt gelir
        if ($parentId > 0) {
            $parent = $this->pdo->prepare('SELECT right_id FROM phpbb_forums WHERE forum_id = ?');
            $parent->execute([$parentId]);
            $parentRight = (int) $parent->fetchColumn();
        } else {
            // Root level — tree'nin sonuna ekle
            $parentRight = (int) $this->pdo->query('SELECT MAX(right_id) FROM phpbb_forums')->fetchColumn() + 1;
        }

        // Yeni kayda yer aç: parent.right_id ve sonrası +2 shift
        $this->pdo->prepare('UPDATE phpbb_forums SET left_id = left_id + 2 WHERE left_id >= ?')->execute([$parentRight]);
        $this->pdo->prepare('UPDATE phpbb_forums SET right_id = right_id + 2 WHERE right_id >= ?')->execute([$parentRight]);

        $template = $type === 0 ? $this->templateCategory : $this->templateForum;

        $row = array_merge($template, [
            'parent_id'                => $parentId,
            'forum_type'               => $type,
            'forum_name'               => $name,
            'forum_desc'               => $desc,
            'forum_parents'            => '',
            'left_id'                  => $parentRight,
            'right_id'                 => $parentRight + 1,
            'forum_last_post_id'       => 0,
            'forum_last_poster_id'     => 0,
            'forum_last_post_subject'  => '',
            'forum_last_post_time'     => 0,
            'forum_last_poster_name'   => '',
            'forum_last_poster_colour' => '',
            'forum_posts_approved'     => 0,
            'forum_posts_unapproved'   => 0,
            'forum_posts_softdeleted'  => 0,
            'forum_topics_approved'    => 0,
            'forum_topics_unapproved'  => 0,
            'forum_topics_softdeleted' => 0,
        ]);
        unset($row['forum_id']);

        $cols = array_keys($row);
        $placeholders = array_fill(0, count($cols), '?');
        $sql = 'INSERT INTO phpbb_forums (`' . implode('`, `', $cols) . '`) VALUES (' . implode(', ', $placeholders) . ')';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(array_values($row));

        $newId = (int) $this->pdo->lastInsertId();
        $this->line(sprintf('  + Yeni %s: #%d %s', $type === 0 ? 'kategori' : 'forum', $newId, $name));
        return $newId;
    }

    /**
     * Şablon forum'un ACL kayıtlarını yeni forum'a kopyala.
     * Yoksa kullanıcılar yeni forum'u göremez.
     */
    private function copyAcl(int $newForumId): void
    {
        // phpbb_acl_groups
        $rows = $this->pdo->prepare('SELECT group_id, auth_option_id, auth_role_id, auth_setting FROM phpbb_acl_groups WHERE forum_id = ?');
        $rows->execute([$this->aclTemplateForumId]);
        $records = $rows->fetchAll(\PDO::FETCH_ASSOC);

        if (empty($records)) {
            $this->warn("    ⚠ ACL şablon yok (forum #{$this->aclTemplateForumId}). Yeni forum görünmeyebilir.");
            return;
        }

        $insert = $this->pdo->prepare('INSERT IGNORE INTO phpbb_acl_groups (group_id, forum_id, auth_option_id, auth_role_id, auth_setting) VALUES (?, ?, ?, ?, ?)');
        foreach ($records as $r) {
            $insert->execute([$r['group_id'], $newForumId, $r['auth_option_id'], $r['auth_role_id'], $r['auth_setting']]);
        }

        $this->line("      ACL kopyalandı (" . count($records) . " kayıt)");
    }

    private function clearPhpbbCache(): void
    {
        $cacheDir = base_path('public/forum/cache');
        if (! is_dir($cacheDir)) return;

        $cleared = 0;
        foreach (['production', 'installer'] as $sub) {
            $dir = $cacheDir . '/' . $sub;
            if (! is_dir($dir)) continue;
            foreach (glob($dir . '/*.php') ?: [] as $f) {
                if (@unlink($f)) $cleared++;
            }
            foreach (glob($dir . '/*.lock') ?: [] as $f) {
                @unlink($f);
            }
        }
        $this->line("  phpBB cache: {$cleared} dosya temizlendi");
    }
}
