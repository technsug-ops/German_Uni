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

        $related = DocumentTemplate::active()
            ->where('category', $template->category)
            ->where('id', '!=', $template->id)
            ->orderBy('sort_order')->limit(4)->get();

        return view('templates.show', [
            'template'   => $template,
            'body'       => $template->bodyForLocale(),
            'guideHtml'  => $template->guide ? $md->render($template->guide) : null,
            'related'    => $related,
        ]);
    }
}
