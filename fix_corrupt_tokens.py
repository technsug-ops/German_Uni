#!/usr/bin/env python3
"""
One-shot repair of `$X$p->name` corruption from commit 7f250ef.

Originals were `$X->name_de`; a faulty replace dropped the `->name_de` suffix
and prepended `$p->name`, leaving `$X$p->name`. The fix is universal: every
`$<varname>$p->name` → `$<varname>->name` (locale-aware accessor).

Safe-replace: read → str_replace → write, with size sanity check. Memory rule
`feedback_safe_file_replace` — never use preg_replace null trick that ate
4 view files in 2026-05-29.
"""
import re, sys, pathlib

ROOT = pathlib.Path(r'c:\Users\Yapra\uni finder_Claude\almanya-uni\resources\views')

# Pattern: dollar + varname + $p->name (the literal "$p" suffix that's wrong)
# Captures the OUTER varname so we know what to keep
RE = re.compile(r'\$([A-Za-z_][A-Za-z0-9_]*)\$p->name')

# Files identified by prior grep
TARGETS = [
    'fields/show.blade.php',
    'cities/show.blade.php',
    'admission-free/by-university.blade.php',
    'admission-free/by-subject.blade.php',
    'programs/landing.blade.php',
    'search/index.blade.php',
]

total_fixes = 0
for rel in TARGETS:
    path = ROOT / rel
    text = path.read_text(encoding='utf-8')
    original_size = len(text)

    matches = RE.findall(text)
    fixes_here = len(matches)
    new_text = RE.sub(lambda m: f'${m.group(1)}->name', text)
    new_size = len(new_text)

    # Sanity: each fix removes "$p" (2 chars), so size delta must equal -2 * fixes_here
    expected_delta = -2 * fixes_here
    actual_delta = new_size - original_size
    if actual_delta != expected_delta:
        print(f'!! ABORT {rel}: size delta {actual_delta} != expected {expected_delta} (fixes={fixes_here})')
        sys.exit(1)
    if new_text == text:
        print(f'-- {rel}: no changes needed')
        continue

    path.write_text(new_text, encoding='utf-8')
    print(f'OK {rel}: {fixes_here} fixes  ({original_size} -> {new_size} bytes)')
    total_fixes += fixes_here

print(f'\nTotal fixes: {total_fixes}')
