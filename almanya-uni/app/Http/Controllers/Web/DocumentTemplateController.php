<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\DocumentTemplate;
use App\Support\MarkdownRenderer;
use Illuminate\View\View;

/**
 * Profesyonel başvuru belgesi şablonları (premium içerik). Katalog + detay.
 * Şimdilik gating YOK — herkes görebilir ("önce sadece içerik" kararı).
 */
class DocumentTemplateController extends Controller
{
    public function index(): View
    {
        $templates = DocumentTemplate::active()
            ->orderBy('sort_order')->orderBy('id')
            ->get();

        return view('templates.index', [
            'templates' => $templates,
            'byCategory' => $templates->groupBy('category'),
        ]);
    }

    public function show(string $slug, MarkdownRenderer $md): View
    {
        $template = DocumentTemplate::active()->where('slug', $slug)->firstOrFail();
        $body = $template->bodyForLocale();

        $related = DocumentTemplate::active()
            ->where('category', $template->category)
            ->where('id', '!=', $template->id)
            ->orderBy('sort_order')->limit(4)->get();

        return view('templates.show', [
            'template'   => $template,
            'body'       => $body,
            'tokens'     => $this->extractTokens($body, $template),
            'guideHtml'  => $template->guide ? $md->render($template->guide) : null,
            'related'    => $related,
        ]);
    }

    /**
     * Gövdedeki [TOKEN]'ları (görünüm sırasıyla, tekil) çıkar + locale-aware etiket
     * (placeholders dizisinden; yoksa key'i insanca biçimlendir). Doldurulabilir form
     * her boşluğu kapsasın diye legend'a değil GÖVDEYE bakar.
     */
    private function extractTokens(?string $body, DocumentTemplate $template): array
    {
        if (! $body) {
            return [];
        }
        preg_match_all('/\[([A-ZÄÖÜ0-9_\/]+)\]/u', $body, $m);

        $labels = collect($template->placeholders ?? [])->keyBy('key');
        $locale = app()->getLocale();

        $tokens = [];
        foreach (array_unique($m[1]) as $key) {
            $row = $labels->get($key);
            $label = $row['label_' . $locale] ?? $row['label_en'] ?? $row['label_de'] ?? null;
            if (! $label) {
                $label = ucfirst(mb_strtolower(str_replace('_', ' ', $key)));
            }
            $tokens[] = ['key' => $key, 'label' => $label];
        }

        return $tokens;
    }
}
