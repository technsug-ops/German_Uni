@extends('layouts.app')

@section('title', __('DAAD Scholarships — :n Scholarship Programs — ' . brand('name'), ['n' => number_format($totalActive, 0, ',', '.')]))

<x-seo
    :title="__('DAAD Scholarships — :n Scholarship Programs', ['n' => number_format($totalActive, 0, ',', '.')])"
    :description="__('All records from the DAAD scholarship database. Find the right scholarship for you with target group, country, and field filters.')"
/>

@section('content')

<section class="bg-gradient-to-br from-primary-700 via-primary-600 to-accent-500 text-white">
    <div class="max-w-[1400px] mx-auto px-4 py-10 md:py-14">
        <nav class="text-sm text-primary-100 mb-3">
            <a href="/" class="hover:text-white">{{ __('Home') }}</a>
            <span class="mx-2 opacity-50">›</span>
            <span class="text-white">{{ __('Scholarships') }}</span>
        </nav>
        <div class="flex flex-wrap items-center gap-3">
            <h1 class="text-3xl md:text-4xl font-extrabold leading-tight">🎓 {{ __('DAAD Scholarship Database') }}</h1>
            <a href="{{ route('scholarships.guide') }}"
               class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-white/15 hover:bg-white/25 text-sm font-medium ring-1 ring-white/20 transition">
                📚 {{ __('All scholarship sources guide') }} →
            </a>
        </div>
        <p class="text-primary-100 max-w-3xl mt-3">
            {!! __('<strong>:n</strong> active scholarships. Data source: DAAD scholarship database (official). Synced every 3 months.', ['n' => number_format($totalActive, 0, ',', '.')]) !!}
        </p>
    </div>
</section>

<section class="bg-gray-50 py-8">
    <div class="max-w-[1400px] mx-auto px-4">

        {{-- FİLTRE FORMU --}}
        <form action="{{ route('scholarships.index') }}" method="GET"
              class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-3">
                <div class="md:col-span-5">
                    <label class="block text-xs font-semibold text-gray-600 mb-1">{{ __('Search') }}</label>
                    <input type="text" name="q" value="{{ $filters['q'] }}"
                           placeholder="{{ __('Scholarship name, field, university…') }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:border-primary-500 focus:outline-none">
                </div>

                <div class="md:col-span-3">
                    <label class="block text-xs font-semibold text-gray-600 mb-1">{{ __('Target group') }}</label>
                    <select name="target" class="w-full border border-gray-300 rounded-lg px-3 py-2 bg-white">
                        <option value="0">{{ __('All') }}</option>
                        @foreach ($statuses as $s)
                            <option value="{{ $s->id }}" @selected($filters['target'] === $s->id)>
                                {{ $s->name_en ?? $s->name_de }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold text-gray-600 mb-1">{{ __('Subject group') }}</label>
                    <select name="subject" class="w-full border border-gray-300 rounded-lg px-3 py-2 bg-white">
                        <option value="">{{ __('All') }}</option>
                        @foreach ($subjects as $sub)
                            <option value="{{ $sub->code }}" @selected($filters['subject'] === $sub->code)>
                                {{ $sub->name_en ?? $sub->name_de }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold text-gray-600 mb-1">{{ __('Intent') }}</label>
                    <select name="intention" class="w-full border border-gray-300 rounded-lg px-3 py-2 bg-white">
                        <option value="0">{{ __('All') }}</option>
                        @foreach ($intentions as $i)
                            <option value="{{ $i->id }}" @selected($filters['intention'] === $i->id)>
                                {{ $i->name_en ?? $i->name_de }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="md:col-span-5">
                    <label class="block text-xs font-semibold text-gray-600 mb-1">{{ __('Your nationality (country)') }}</label>
                    <select name="country" class="w-full border border-gray-300 rounded-lg px-3 py-2 bg-white">
                        <option value="0">{{ __('All') }}</option>
                        @foreach ($origins as $o)
                            <option value="{{ $o->id }}" @selected($filters['country'] === $o->id)>
                                {{ $o->name_en ?? $o->name_de }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="md:col-span-3 flex items-end gap-2">
                    <label class="inline-flex items-center gap-2 text-sm text-gray-700">
                        <input type="checkbox" name="is_daad" value="1" @checked($filters['is_daad'] === 1)
                               class="rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                        {{ __('Only DAAD scholarships') }}
                    </label>
                </div>

                <div class="md:col-span-4 flex items-end justify-end gap-2">
                    <a href="{{ route('scholarships.index') }}"
                       class="px-4 py-2 rounded-lg text-gray-600 hover:bg-gray-100 transition">{{ __('Clear') }}</a>
                    <button type="submit"
                            class="px-5 py-2 rounded-lg bg-primary-600 text-white font-semibold hover:bg-primary-700 transition">
                        {{ __('Filter') }}
                    </button>
                </div>
            </div>
        </form>

        {{-- SONUÇLAR --}}
        <div class="flex items-center justify-between mb-4">
            <p class="text-sm text-gray-600">
                {!! __('<strong>:n</strong> scholarships found', ['n' => number_format($scholarships->total(), 0, ',', '.')]) !!}
            </p>
        </div>

        @if ($scholarships->isEmpty())
            <div class="bg-white rounded-xl border border-gray-200 p-12 text-center">
                <p class="text-gray-600">{{ __('No scholarships match this filter.') }}</p>
                <a href="{{ route('scholarships.index') }}" class="inline-block mt-3 text-primary-600 hover:underline">{{ __('Reset filter') }}</a>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach ($scholarships as $sch)
                    <a href="{{ route('scholarships.show', $sch->slug) }}"
                       class="group bg-white rounded-xl border border-gray-200 hover:border-primary-500 hover:shadow-md transition p-5 flex flex-col">
                        <div class="flex items-start justify-between gap-2 mb-2">
                            <h3 class="font-bold text-gray-900 group-hover:text-primary-600 line-clamp-2">
                                {{ $sch->name_en ?? $sch->name_de ?? 'DAAD #' . $sch->sap_objid }}
                            </h3>
                            @if ($sch->is_daad)
                                <span class="shrink-0 px-2 py-0.5 rounded-full bg-blue-50 text-blue-700 text-xs font-semibold">DAAD</span>
                            @else
                                <span class="shrink-0 px-2 py-0.5 rounded-full bg-gray-100 text-gray-600 text-xs font-semibold">{{ __('Partner') }}</span>
                            @endif
                        </div>
                        @if ($sch->programmname_en ?? $sch->programmname_de)
                            <p class="text-xs text-gray-500 mb-3 line-clamp-1">
                                {{ $sch->programmname_en ?? $sch->programmname_de }}
                            </p>
                        @endif
                        @php $intro = $sch->introductionText('en') ?? $sch->introductionText('de'); @endphp
                        @if ($intro)
                            <p class="text-sm text-gray-600 line-clamp-3 mb-4">{{ \Illuminate\Support\Str::limit(strip_tags($intro), 200) }}</p>
                        @endif
                        <div class="mt-auto flex flex-wrap gap-1.5">
                            @foreach ($sch->subjects->take(3) as $sub)
                                <span class="px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 text-xs">{{ $sub->name_en ?? $sub->name_de }}</span>
                            @endforeach
                            @foreach ($sch->statuses->take(2) as $st)
                                <span class="px-2 py-0.5 rounded-full bg-amber-50 text-amber-700 text-xs">{{ $st->name_en ?? $st->name_de }}</span>
                            @endforeach
                        </div>
                    </a>
                @endforeach
            </div>

            <div class="mt-8">
                {{ $scholarships->links() }}
            </div>
        @endif
    </div>
</section>

<div class="max-w-4xl mx-auto px-4">
    <x-featured-snippet
        :question="__('How can I get a scholarship to study in Germany?')"
        :answer="__('Main pathways: DAAD (academic merit + need, ~934 EUR/month + tuition + travel), Deutschlandstipendium (300 EUR/month, merit-based), and political foundations (Heinrich-Böll, Konrad-Adenauer, Friedrich-Ebert, Rosa-Luxemburg) that value social engagement alongside grades. Apply 9–15 months before your programme start.')"
        :steps="[
            ['title' => __('Identify matching scholarships'), 'description' => __('Filter by your field, degree level, and country eligibility.')],
            ['title' => __('Prepare a strong motivation letter'), 'description' => __('Highlight academic plan + social engagement + return contribution.')],
            ['title' => __('Collect required documents'), 'description' => __('Transcripts, language certificate, CV, recommendation letters, project proposal.')],
            ['title' => __('Apply early (9–15 months ahead)'), 'description' => __('Most scholarship deadlines close long before programme start.')],
            ['title' => __('Combine multiple smaller scholarships if needed'), 'description' => __('Deutschlandstipendium + foundation top-ups can stack legally.')],
        ]"
    />
</div>

<x-faq-section
    :title="__('Frequently Asked Questions about Scholarships')"
    :subtitle="__('Funding paths for international students in Germany')"
    :faqs="\App\Support\PageFaq::forScholarships()"
/>

@endsection
