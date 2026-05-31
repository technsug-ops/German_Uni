<?php

namespace App\Services\Content;

/**
 * Single source of truth for content REGISTER + native terminology + human
 * (anti-AI-slop) quality, per output language. Mirrors doc/I18N-STYLE-GUIDE.md
 * and doc/MULTILANG-PLAN.md.
 *
 * SCALES TO ANY LANGUAGE: a tailored profile exists for our validated locales
 * (tr/en/de). EVERY other locale in config('locale.locales') works out of the
 * box via a GENERIC profile derived from the registry (native_name) — so adding
 * a language needs NO code change here, just a config/locale.php entry (+ its
 * lang/XX.json). Add a tailored profile later to refine.
 *
 * Decision (2026-05-31): informal register everywhere — DE "du"
 * (study-in-germany.de), EN "you", TR "sen" — native idiomatic SEO terms,
 * minimal "AI feel". German proper nouns stay, glossed once.
 */
class ContentVoice
{
    /** Hand-tuned register + native-terminology profiles for validated locales. */
    public const TAILORED = [
        'tr' => 'DİL & TON (TR): Samimi "sen" dili, doğal Türk öğrenci tonu. Almanca terimleri ilk geçişte parantezle açıkla: "Sperrkonto (bloke hesap)". Almanca özel adlar (DAAD, Studienkolleg, Anabin) çevrilmez.',
        'en' => 'VOICE (EN): Conversational, direct, second person "you". Neutral international English, AMERICAN spelling ("programs", not "programmes"). Use the exact phrases students search: "study in Germany", "English-taught programs", "tuition-free universities", "semester contribution", "blocked account (Sperrkonto)", "cost of living", "application deadline". Keep German proper nouns (DAAD, Studienkolleg, Sperrkonto, Anabin, uni-assist, BAföG) and gloss each once.',
        'de' => 'STIMME (DE): Durchgehend informelles "du" (NIEMALS "Sie") — wie study-in-germany.de: freundlich, direkt, handlungsorientiert. Imperative im du: "Nutze", "Vergleiche", "Beachte", "Bewirb dich". Native, idiomatische SEO-Begriffe: "Studiengänge" (nicht "Programme"), "englischsprachige Studiengänge", "Hochschulzugangsberechtigung", "Finanzierungsnachweis", "Lebenshaltungskosten", "Bewerbungsfrist", "Visakosten". Deutsche Fachbegriffe (Sperrkonto, Studienkolleg, Anabin, BAföG, uni-assist) bleiben unübersetzt. WICHTIG: "sie/Sie" als Pronomen (= they/it, bezogen auf ein Substantiv) bleibt; nur die Leseransprache wird zu "du".',
    ];

    /** Universal human-quality / anti-AI-slop directive (applies in every language). */
    public const HUMAN_QUALITY = <<<'TXT'
HUMAN QUALITY — write like a knowledgeable human editor who has actually lived this, NOT like an AI:
- Be concrete and specific: real numbers, names, steps, dates. Specifics beat vague generalities.
- Vary sentence and paragraph length — natural rhythm, not uniform robotic lists.
- BAN AI-slop clichés & filler (in EVERY language): no equivalents of "in today's fast-paced world", "navigating the complexities", "it's important to note", "when it comes to", "look no further", "dive into", "in conclusion", "rest assured", "unlock your potential".
- No robotic transition stuffing ("Moreover / Furthermore / Additionally / In conclusion" in every paragraph).
- No emoji-stuffing, no em-dash overuse — use them sparingly, like a human would.
- Ground every claim in the real community questions / pain points provided and in official sources; mirror how real students actually phrase things.
- NEVER invent facts, numbers, deadlines or names. If unknown, tell the reader to verify on the official source.
- Goal: indistinguishable from a sharp human guide written by someone who did it — minimal "AI feel".
TXT;

    /** Combined register + quality directive block for the given output language. */
    public static function for(string $lang): string
    {
        $voice = self::TAILORED[$lang] ?? self::generic($lang);

        return $voice . "\n\n" . self::HUMAN_QUALITY;
    }

    /**
     * Generic native-voice directive for any registry locale without a tailored
     * profile. Pulls the native name from config('locale.locales') so a newly
     * added language produces native, informal, gloss-German content with zero
     * code change here.
     */
    private static function generic(string $lang): string
    {
        $native = config("locale.locales.$lang.native_name") ?? $lang;
        $name   = config("locale.locales.$lang.name") ?? strtoupper($lang);
        $rtl    = (config("locale.locales.$lang.direction") === 'rtl')
            ? ' This language is right-to-left; keep punctuation and Latin/German terms correctly placed.'
            : '';

        return "VOICE ({$name} / {$native}): Write natively and idiomatically in {$native} — NOT a literal translation. Address the reader directly and INFORMALLY (use the informal 2nd-person form where the language distinguishes formal vs. informal). Friendly, direct, action-oriented. Use the words and search terms students actually use in {$native}. Keep German proper nouns (DAAD, Sperrkonto, Studienkolleg, Anabin, uni-assist, BAföG) untranslated and gloss each once in {$native}.{$rtl}";
    }

    /**
     * Locales eligible for AI content generation/translation — driven entirely
     * by the registry (config/locale.php). A locale counts as content-enabled
     * when it is active OR explicitly flagged coming_soon (content prepared
     * ahead of UI launch). This is the single list the generators should use.
     *
     * @return array<int, string>
     */
    public static function contentLocales(): array
    {
        $locales = config('locale.locales', []);

        return array_keys(array_filter($locales, fn ($cfg) => ($cfg['active'] ?? false) || ($cfg['coming_soon'] ?? false)));
    }

    /** English display name for a locale, from the registry. */
    public static function languageName(string $lang): string
    {
        return config("locale.locales.$lang.name") ?? strtoupper($lang);
    }

    /** All registry locale codes (for translation targets / loops). */
    public static function allLocales(): array
    {
        return array_keys(config('locale.locales', []));
    }
}
