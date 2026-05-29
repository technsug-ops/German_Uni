<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\FieldOfStudy;
use App\Models\JobPosting;
use App\Models\University;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class JobController extends Controller
{
    public function index(Request $request): View
    {
        $query = JobPosting::active()
            ->with(['university:id,slug,name_de,short_name', 'city:id,slug,name_de,name_tr', 'field:id,slug,name_tr,name_en,icon']);

        if ($type = $request->query('type')) {
            if (isset(JobPosting::POSITION_TYPES[$type])) {
                $query->where('position_type', $type);
            }
        }

        if ($lang = $request->query('lang')) {
            if (in_array($lang, ['en', 'de', 'both'], true)) {
                $query->where(fn ($q) => $q->where('language', $lang)->orWhere('language', 'both'));
            }
        }

        if ($fieldSlug = $request->query('field')) {
            $field = FieldOfStudy::where('slug', $fieldSlug)->first();
            if ($field) $query->where('field_of_study_id', $field->id);
        }

        if ($citySlug = $request->query('city')) {
            $city = City::where('slug', $citySlug)->first();
            if ($city) $query->where('city_id', $city->id);
        }

        if ($request->boolean('remote_only')) {
            $query->where('is_remote', true);
        }

        if ($q = trim((string) $request->query('q'))) {
            $query->where(function ($qq) use ($q) {
                $qq->where('title', 'like', "%{$q}%")
                    ->orWhere('excerpt', 'like', "%{$q}%")
                    ->orWhere('description', 'like', "%{$q}%");
            });
        }

        $jobs = $query
            ->orderByDesc('is_featured')
            ->orderByDesc('posted_at')
            ->paginate(20)
            ->withQueryString();

        $stats = [
            'total'    => JobPosting::active()->count(),
            'phd'      => JobPosting::active()->where('position_type', 'phd')->count(),
            'postdoc'  => JobPosting::active()->where('position_type', 'postdoc')->count(),
            'expiring' => JobPosting::active()->expiringSoon(14)->count(),
        ];

        // Filter options
        $positionTypes = JobPosting::POSITION_TYPES;
        $fields = FieldOfStudy::active()
            ->whereIn('id', JobPosting::active()->whereNotNull('field_of_study_id')->distinct()->pluck('field_of_study_id'))
            ->orderBy('name_tr')
            ->get(['id', 'slug', 'name_tr', 'name_en', 'name_de', 'icon']);

        return view('jobs.index', [
            'jobs'          => $jobs,
            'stats'         => $stats,
            'positionTypes' => $positionTypes,
            'fields'        => $fields,
            'filters'       => [
                'type'        => $request->query('type'),
                'lang'        => $request->query('lang'),
                'field'       => $request->query('field'),
                'city'        => $request->query('city'),
                'remote_only' => $request->boolean('remote_only'),
                'q'           => $request->query('q'),
            ],
        ]);
    }

    public function show(string $slug): View
    {
        $job = JobPosting::active()
            ->with(['university', 'city.state', 'field'])
            ->where('slug', $slug)
            ->first();

        if (! $job) {
            throw new NotFoundHttpException();
        }

        $job->increment('view_count');

        $related = JobPosting::active()
            ->where('id', '!=', $job->id)
            ->where(function ($q) use ($job) {
                if ($job->field_of_study_id) $q->orWhere('field_of_study_id', $job->field_of_study_id);
                if ($job->position_type)     $q->orWhere('position_type', $job->position_type);
                if ($job->university_id)     $q->orWhere('university_id', $job->university_id);
            })
            ->with(['university:id,slug,name_de,short_name', 'city:id,slug,name_de,name_tr'])
            ->orderByDesc('is_featured')
            ->orderByDesc('posted_at')
            ->limit(6)
            ->get();

        return view('jobs.show', [
            'job'     => $job,
            'related' => $related,
        ]);
    }
}
