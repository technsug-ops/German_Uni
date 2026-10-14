<?php

namespace Tests\Unit;

use App\Support\EmojiCleaner;
use PHPUnit\Framework\TestCase;

class EmojiCleanerTest extends TestCase
{
    public function test_consecutive_check_lines_become_a_list(): void
    {
        $out = EmojiCleaner::cleanMarkdown("Giriş.\n\n✅ Anmeldung yapılır\n✅ **Evlilik cüzdanı** var mı?\n\nSon.", 'tr');
        $this->assertSame("Giriş.\n\n- Anmeldung yapılır\n- **Evlilik cüzdanı** var mı?\n\nSon.", $out);
    }

    public function test_label_line_is_not_a_list_item(): void
    {
        $this->assertSame("**Nachteile:**\n- Teuer\n- Langsam", EmojiCleaner::cleanMarkdown("❌ **Nachteile:**\n❌ Teuer\n❌ Langsam", 'de'));
    }

    public function test_mixed_yes_no_block_keeps_meaning(): void
    {
        $this->assertSame("- ✓ Ucuz\n- ✗ Yavaş", EmojiCleaner::cleanMarkdown("✅ Ucuz\n❌ Yavaş", 'tr'));
        $this->assertSame("- ✗ Unter A1\n- ✓ Gut", EmojiCleaner::cleanMarkdown("- ❌ Unter A1\n- ✅ Gut", 'de'));
    }

    public function test_single_warning_line_stays_a_paragraph(): void
    {
        $this->assertSame("**Achtung** — Text\n\nWeiter.", EmojiCleaner::cleanMarkdown("⚠️ **Achtung** — Text\n\nWeiter.", 'de'));
    }

    public function test_labels_headings_and_inline_emoji(): void
    {
        $this->assertSame('**Practical advice:** Check.', EmojiCleaner::cleanMarkdown('💡 **Practical advice:** Check.', 'en'));
        $this->assertSame('**Rechtslage:** Text', EmojiCleaner::cleanMarkdown('**⚖️ Rechtslage:** Text', 'de'));
        $this->assertSame("## Başlık\n\nMetin burada Türkiye.", EmojiCleaner::cleanMarkdown("## 🎓 Başlık\n\nMetin 👉 burada 🇹🇷 Türkiye.", 'tr'));
    }

    public function test_table_cells(): void
    {
        $in = "| **Travel insurance** | ❌ | ✅ (first 90 days) |\n|---|---|---|\n| Use | ✅ Take it once | ❌ Again |\n| Rating | ★★★★★ | ★★★ |";
        $out = EmojiCleaner::cleanMarkdown($in, 'en');
        $this->assertSame("| **Travel insurance** | No | Yes (first 90 days) |\n|---|---|---|\n| Use | Yes – Take it once | No – Again |\n| Rating | 5/5 | 3/5 |", $out);
        $this->assertStringContainsString('| Ja |', EmojiCleaner::cleanMarkdown('| X | ✅ |', 'de'));
    }

    public function test_arrows_and_typographic_marks_are_kept_and_cleaning_is_idempotent(): void
    {
        $plain = 'Ok → arrow ↔ and ✓ stays';
        $this->assertSame($plain, EmojiCleaner::cleanMarkdown($plain, 'en'));
        $once = EmojiCleaner::cleanMarkdown("✅ A\n❌ B\n| x | ✅ |", 'tr');
        $this->assertSame($once, EmojiCleaner::cleanMarkdown($once, 'tr'));
        $this->assertFalse(EmojiCleaner::hasEmoji($once));
        $this->assertSame('Pratik öneri: Tez süreci', EmojiCleaner::cleanInline('💡 Pratik öneri: Tez süreci 🎓'));
    }
}
