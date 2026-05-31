<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\FieldOfStudy;
use App\Models\Post;
use App\Models\Program;
use App\Models\University;
use Illuminate\View\View;

class FieldController extends Controller
{
    public function index(): View
    {
        $fields = FieldOfStudy::active()
            ->withCount(['programs' => fn ($q) => $q->where('is_active', 1)])
            ->orderByDesc('programs_count')
            ->get();

        return view('fields.index', compact('fields'));
    }

    public function show(string $slug): View
    {
        $field = FieldOfStudy::active()->where('slug', $slug)->firstOrFail();

        $topUnis = University::whereHas('programs', fn ($q) => $q->where('field_of_study_id', $field->id)->where('is_active', 1))
            ->where('is_active', 1)
            ->withCount(['programs' => fn ($q) => $q->where('field_of_study_id', $field->id)->where('is_active', 1)])
            ->orderByDesc('programs_count')
            ->take(12)
            ->get();

        $bachelorPrograms = Program::where('field_of_study_id', $field->id)
            ->where('is_active', 1)
            ->where('degree', 'bachelor')
            ->with('university:id,slug,name_de,logo_url')
            ->orderBy('name_de')
            ->take(12)
            ->get();

        $masterPrograms = Program::where('field_of_study_id', $field->id)
            ->where('is_active', 1)
            ->where('degree', 'master')
            ->with('university:id,slug,name_de,logo_url')
            ->orderBy('name_de')
            ->take(12)
            ->get();

        $totals = [
            'all' => Program::where('field_of_study_id', $field->id)->where('is_active', 1)->count(),
            'bachelor' => Program::where('field_of_study_id', $field->id)->where('is_active', 1)->where('degree', 'bachelor')->count(),
            'master' => Program::where('field_of_study_id', $field->id)->where('is_active', 1)->where('degree', 'master')->count(),
            'english' => Program::where('field_of_study_id', $field->id)->where('is_active', 1)->whereIn('language', ['en', 'both'])->count(),
            'unis' => University::whereHas('programs', fn ($q) => $q->where('field_of_study_id', $field->id)->where('is_active', 1))->count(),
            'professions' => \App\Models\Profession::where('field_of_study_id', $field->id)->count(),
        ];

        // Bu alanda popüler meslekler — description_tr olanlar önce
        $topProfessions = \App\Models\Profession::where('field_of_study_id', $field->id)
            ->orderByRaw('CASE WHEN description_tr IS NOT NULL THEN 0 ELSE 1 END')
            ->orderBy('name_de')
            ->take(12)
            ->get(['id', 'slug', 'name_de', 'name_tr', 'short_name', 'type', 'description_tr', 'steckbrief','name_en','name_de']);

        // Top 3 şehir — bu alanda en çok program sunan şehirler
        $topCities = City::query()
            ->selectRaw('cities.id, cities.slug, cities.name_de, cities.population, COUNT(programs.id) as program_count')
            ->join('universities', 'universities.city_id', '=', 'cities.id')
            ->join('programs', 'programs.university_id', '=', 'universities.id')
            ->where('programs.field_of_study_id', $field->id)
            ->where('programs.is_active', 1)
            ->where('universities.is_active', 1)
            ->groupBy('cities.id', 'cities.slug', 'cities.name_de', 'cities.population')
            ->orderByDesc('program_count')
            ->take(5)
            ->get();

        // İlgili blog yazıları — slug benzerlikle (örnek: muhendislik → "mühendislik" konulu post)
        $relatedPosts = Post::where('is_published', 1)->where('locale', app()->getLocale())
            ->whereNotNull('published_at')
            ->where(function ($q) use ($field) {
                $q->where('title', 'like', '%' . $field->name_tr . '%')
                  ->orWhere('content_md', 'like', '%' . $field->name_tr . '%');
            })
            ->with('category')
            ->orderByDesc('published_at')
            ->take(3)
            ->get(['id', 'slug', 'title', 'excerpt', 'reading_minutes', 'published_at', 'category_id']);

        // Diğer alanlar (program sayısına göre)
        $otherFields = FieldOfStudy::active()
            ->where('id', '!=', $field->id)
            ->withCount(['programs' => fn ($q) => $q->where('is_active', 1)])
            ->orderByDesc('programs_count')
            ->take(8)
            ->get();

        return view('fields.show', compact('field', 'topUnis', 'bachelorPrograms', 'masterPrograms', 'totals', 'topProfessions', 'topCities', 'relatedPosts', 'otherFields'));
    }
}
