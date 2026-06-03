<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\LanguageCourse;
use App\Models\TranslationOffice;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Herkese açık partner dizinleri (Dil Kursları + Yeminli Tercüme) — lead-gen + affiliate.
 * Lead toplama: detayda ilgi formu (Lead) + "Web sitesi" tık takibi (click_count + /go).
 */
class PartnerController extends Controller
{
    public function languageCourses(Request $request): View
    {
        $type = $request->query('type');
        $q = LanguageCourse::active()->orderByDesc('is_featured')->orderBy('sort_order');
        if ($type && isset(LanguageCourse::TYPES[$type])) {
            $q->where('type', $type);
        }
        $items = $q->get();

        return view('partners.index', [
            'kind'       => 'language_course',
            'items'      => $items,
            'grouped'    => $items->groupBy('type'),
            'types'      => LanguageCourse::TYPES,
            'icon'       => '🗣️',
            'title'      => __('Language Courses in Germany'),
            'intro'      => __('University, private and online German courses — filter by level (A1–C2) and city.'),
            'indexRoute' => 'language-courses.index',
            'showRoute'  => 'language-courses.show',
            'activeType' => $type,
        ]);
    }

    public function languageCourse(string $slug): View
    {
        $item = LanguageCourse::active()->where('slug', $slug)->firstOrFail();
        $related = LanguageCourse::active()->where('type', $item->type)
            ->where('id', '!=', $item->id)->orderBy('sort_order')->take(4)->get();

        return view('partners.show', [
            'kind'       => 'language_course',
            'item'       => $item,
            'related'    => $related,
            'icon'       => '🗣️',
            'indexRoute' => 'language-courses.index',
            'showRoute'  => 'language-courses.show',
            'indexTitle' => __('Language Courses'),
        ]);
    }

    public function translationOffices(Request $request): View
    {
        $type = $request->query('type');
        $q = TranslationOffice::active()->orderByDesc('is_featured')->orderBy('sort_order');
        if ($type && isset(TranslationOffice::TYPES[$type])) {
            $q->where('type', $type);
        }
        $items = $q->get();

        return view('partners.index', [
            'kind'       => 'translation_office',
            'items'      => $items,
            'grouped'    => $items->groupBy('type'),
            'types'      => TranslationOffice::TYPES,
            'icon'       => '📜',
            'title'      => __('Sworn Translation Offices for Germany'),
            'intro'      => __('Certified (sworn) translation of diplomas and documents for your Germany application — by city and language pair.'),
            'indexRoute' => 'translation-offices.index',
            'showRoute'  => 'translation-offices.show',
            'activeType' => $type,
        ]);
    }

    public function translationOffice(string $slug): View
    {
        $item = TranslationOffice::active()->where('slug', $slug)->firstOrFail();
        $related = TranslationOffice::active()->where('type', $item->type)
            ->where('id', '!=', $item->id)->orderBy('sort_order')->take(4)->get();

        return view('partners.show', [
            'kind'       => 'translation_office',
            'item'       => $item,
            'related'    => $related,
            'icon'       => '📜',
            'indexRoute' => 'translation-offices.index',
            'showRoute'  => 'translation-offices.show',
            'indexTitle' => __('Translation Offices'),
        ]);
    }

    /** İlgi formu → Lead kaydı. En az e-posta veya telefon. Honeypot spam koruması. */
    public function storeLead(Request $request): RedirectResponse
    {
        // Honeypot: bot 'website' alanını doldurursa sessizce başarı dön.
        if ($request->filled('website')) {
            return back()->with('lead_success', true);
        }

        $data = $request->validate([
            'source_type' => 'required|in:language_course,translation_office',
            'source_id'   => 'nullable|integer',
            'source_name' => 'nullable|string|max:200',
            'name'        => 'nullable|string|max:120',
            'email'       => 'nullable|email|max:190',
            'phone'       => 'nullable|string|max:40',
            'message'     => 'nullable|string|max:2000',
        ]);

        if (empty($data['email']) && empty($data['phone'])) {
            return back()->withErrors(['email' => __('Please provide an email or phone.')])->withInput();
        }

        Lead::create($data + [
            'locale' => app()->getLocale(),
            'status' => 'new',
            'meta'   => ['ip_hash' => substr(hash('sha256', (string) $request->ip()), 0, 16)],
        ]);

        return back()->with('lead_success', true);
    }

    /** Tık takibi: click_count++ → affiliate/website'e yönlendir. */
    public function click(string $kind, int $id): RedirectResponse
    {
        $model = match ($kind) {
            'language_course'    => LanguageCourse::find($id),
            'translation_office' => TranslationOffice::find($id),
            default              => null,
        };
        abort_unless($model, 404);

        $model->increment('click_count');

        return redirect()->away($model->outbound_url ?: url('/'));
    }
}
