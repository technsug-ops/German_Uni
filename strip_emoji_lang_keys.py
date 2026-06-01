#!/usr/bin/env python3
"""
Strip leading emoji + space from translation keys (and values) in tr/en/de.json
plus update the matching `__('<emoji> Foo')` callsites in blade views.

Size-checked safe replace (memory rule feedback_safe_file_replace — never
preg_replace blind).
"""
from pathlib import Path

ROOT = Path(r'c:\Users\Yapra\uni finder_Claude\almanya-uni')

# (full_old_substring, full_new_substring) — applied as exact-string replaces.
# These cover the keys; values are stripped via a separate sweep that mirrors
# the same emoji prefix.
EMOJI_KEYS = [
    "⚠️ Deadlines may vary by uni and program. Verify on the uni's official site.",
    "🍽️ Food",
    "🎓 Tuition fee",
    "🎭 Leisure / culture / sport",
    "🏠 Rent (utilities included)",
    "🏥 Health insurance + doctor + medicine",
    "👍 Thanks for your feedback!",
    "👕 Clothing",
    "💳 Semester contribution",
    "📚 Study materials",
    "📞 Phone / Internet / TV license",
    "😔 Sorry. We will review your feedback.",
    "🚌 Transport",
    "🛒 Other",
]

# TR translation values that mirror the emoji prefix (need separate stripping)
EMOJI_VALUES_TR = [
    ("🍽️ Yemek", "Yemek"),
    ("🎓 Öğrenim ücreti", "Öğrenim ücreti"),
    ("🎭 Dinlenme / kültür / spor", "Dinlenme / kültür / spor"),
    ("🏠 Kira (faturalar dahil)", "Kira (faturalar dahil)"),
    ("🏥 Sağlık sigortası + doktor + ilaç", "Sağlık sigortası + doktor + ilaç"),
    ("👍 Teşekkürler! Geri bildirim için.", "Teşekkürler! Geri bildirim için."),
    ("👕 Giyim", "Giyim"),
    ("💳 Dönem katkı payı", "Dönem katkı payı"),
    ("📚 Çalışma malzemeleri", "Çalışma malzemeleri"),
    ("📞 Telefon / İnternet / TV lisansı", "Telefon / İnternet / TV lisansı"),
    ("😔 Üzgünüz. Geri bildirimini değerlendireceğiz.", "Üzgünüz. Geri bildirimini değerlendireceğiz."),
    ("🚌 Ulaşım", "Ulaşım"),
    ("🛒 Diğer", "Diğer"),
    ("⚠️ Deadline tarihleri üni ve program bazlı değişebilir. Üni'nin resmi sitesinde doğrula.",
     "Deadline tarihleri üni ve program bazlı değişebilir. Üni'nin resmi sitesinde doğrula."),
]

# Files to touch
TARGETS = [
    ROOT / 'lang' / 'tr.json',
    ROOT / 'lang' / 'en.json',
    ROOT / 'lang' / 'de.json',
    ROOT / 'resources' / 'views' / 'tools' / 'cost-of-living.blade.php',
    ROOT / 'resources' / 'views' / 'tools' / 'deadlines.blade.php',
    ROOT / 'resources' / 'views' / 'blog' / 'show.blade.php',
]

# Build the full replacement list. KEY replacements affect blade callsites + json keys + en/de json keys
# (which are reverse-lookup tables, so the emoji-key may appear in the VALUE there).
all_replacements = []
for old in EMOJI_KEYS:
    # Strip leading emoji + space — assume key is "<emoji> rest..." → "rest..."
    parts = old.split(' ', 1)
    new = parts[1] if len(parts) == 2 else old
    if new != old:
        all_replacements.append((old, new))

# TR-value stripping
for old, new in EMOJI_VALUES_TR:
    all_replacements.append((old, new))

# Apply
total_fixes = 0
for path in TARGETS:
    if not path.exists():
        print(f'SKIP {path.name}: not found')
        continue
    text = path.read_text(encoding='utf-8')
    original_size = len(text)
    file_fixes = 0
    for old, new in all_replacements:
        count = text.count(old)
        if count:
            text = text.replace(old, new)
            file_fixes += count
    new_size = len(text)
    if file_fixes == 0:
        print(f'-- {path.name}: no hits')
        continue

    # Sanity check: total chars removed should match sum of (len_old - len_new) * count
    # For simplicity just verify file shrunk (no replacement adds chars).
    if new_size > original_size:
        print(f'!! ABORT {path.name}: file grew (orig={original_size}, new={new_size}) — unexpected')
        continue

    path.write_text(text, encoding='utf-8')
    print(f'OK {path.name}: {file_fixes} fixes  ({original_size} -> {new_size} bytes)')
    total_fixes += file_fixes

print(f'\nTotal: {total_fixes} replacements across {len(TARGETS)} files')
