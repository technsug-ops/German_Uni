{{-- Programs grid partial — also returned standalone for XHR async-filter updates. --}}
@php
    $degreeLabels = [
        'bachelor' => 'Bachelor', 'master' => 'Master', 'phd' => __('PhD'),
        'staatsexamen' => 'Staatsexamen', 'diplom' => 'Diplom', 'magister' => 'Magister', 'other' => __('Other'),
    ];
    $nonEuTuitionStates = $nonEuTuitionStates ?? ['baden-wurttemberg', 'sachsen-anhalt'];
    $hasFilter = $hasFilter ?? false;
@endphp

<div class="flex items-center justify-between mb-4 flex-wrap gap-2">
    <p class="text-sm text-gray-700">
        <strong>{{ number_format($programs->total(), 0, ',', '.') }}</strong> {{ __('results') }}
        @if ($hasFilter)
            <span class="text-gray-500">{{ __('(filtered — :n total)', ['n' => number_format($total_all, 0, ',', '.')]) }}</span>
        @endif
    </p>
    <p class="text-sm text-gray-500">
        {{ __('Page :current / :last', ['current' => $programs->currentPage(), 'last' => max(1, $programs->lastPage())]) }}
    </p>
</div>

@if ($programs->isEmpty())
    <x-empty-state
        icon="📚"
        :title="__('No results found.')"
        :description="__('Try relaxing the filters or change your search term.')"
        :actions="[
            ['label' => __('All Programs'), 'url' => route('programs.index'), 'primary' => true],
            ['label' => __('Browse by field'), 'url' => route('fields.index')],
            ['label' => __('Universities'), 'url' => route('universities.index')],
        ]"
    />
@else
    <div class="space-y-3">
        @foreach ($programs as $p)
            <a href="{{ route('programs.show', $p->slug) }}"
               class="group block bg-white border border-gray-200 hover:border-primary-400 hover:shadow-md transition rounded-xl p-5">
                <div class="flex items-start gap-4">
                    @if ($p->university->logo_url)
                        <img src="{{ $p->university->logo_url }}" alt=""
                             class="w-12 h-12 object-contain bg-gray-50 rounded p-1 flex-shrink-0" loading="lazy" decoding="async">
                    @else
                        <div class="w-12 h-12 bg-primary-100 text-primary-700 rounded flex items-center justify-center font-bold flex-shrink-0">
                            {{ mb_substr($p->university->short_name ?? $p->university->name, 0, 2) }}
                        </div>
                    @endif

                    <div class="flex-1 min-w-0">
                        <div class="flex items-start justify-between gap-2 mb-1">
                            <h3 class="font-bold text-gray-900 leading-snug group-hover:text-primary-700 transition">
                                <x-program-name :program="$p" />
                            </h3>
                            <div class="flex flex-wrap gap-1 flex-shrink-0">
                                @if ($p->degree)
                                    <span class="inline-block text-xs font-semibold px-2 py-0.5 rounded-full
                                        @switch($p->degree)
                                            @case('bachelor') bg-green-100 text-green-700 @break
                                            @case('master')   bg-blue-100 text-blue-700 @break
                                            @case('phd')      bg-purple-100 text-purple-700 @break
                                            @default          bg-gray-100 text-gray-700
                                        @endswitch
                                    ">{{ $degreeLabels[$p->degree] ?? $p->degree }}</span>
                                @endif
                                @if ($p->language)
                                    <span class="inline-block text-xs font-semibold px-2 py-0.5 rounded-full whitespace-nowrap
                                        @switch($p->language)
                                            @case('en')   bg-blue-100 text-blue-700 @break
                                            @case('de')   bg-emerald-100 text-emerald-700 @break
                                            @case('both') bg-amber-100 text-amber-800 @break
                                        @endswitch
                                    ">
                                        @switch($p->language)
                                            @case('en') EN @break
                                            @case('de') DE @break
                                            @case('both') DE+EN @break
                                        @endswitch
                                    </span>
                                @endif
                            </div>
                        </div>

                        <p class="text-sm text-gray-600 mb-2">
                            {{ $p->university->display_name }}
                            @if ($p->university->city)
                                · <span class="text-gray-500">{{ $p->university->city->name }}</span>
                            @endif
                        </p>

                        <div class="flex flex-wrap gap-3 text-xs text-gray-500 mb-2">
                            @if ($p->field)
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-white"
                                      style="background-color: {{ $p->field->color }};">
                                    {!! e_icon($p->field->icon, 'w-3 h-3') !!} {{ $p->field->name }}
                                </span>
                            @endif
                            @if ($p->degree_specification)
                                <span>{{ $p->degree_specification }}</span>
                            @endif
                            @if ($p->duration_semesters)
                                <span class="inline-flex items-center gap-1">
                                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                                    {{ $p->duration_semesters }} {{ __('sem') }}
                                </span>
                            @endif
                            @if (! is_null($p->tuition_fee_eur))
                                <span class="inline-flex items-center gap-1">
                                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M14.25 7.756a4.5 4.5 0 1 0 0 8.488M7.5 10.5h5.25m-5.25 3h5.25M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                                    {{ $p->tuition_fee_eur == 0
                                        ? __('Free')
                                        : number_format($p->tuition_fee_eur, 0, ',', '.') . ' €/' . __('sem') }}
                                </span>
                            @elseif ($p->university?->city?->state && in_array($p->university->city->state->slug, $nonEuTuitionStates))
                                <span class="text-orange-700 inline-flex items-center gap-1">
                                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M14.25 7.756a4.5 4.5 0 1 0 0 8.488M7.5 10.5h5.25m-5.25 3h5.25M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                                    {{ __('Non-EU: ~€1,500/sem') }}
                                </span>
                            @endif
                            @if ($p->application_deadline_winter)
                                <span class="inline-flex items-center gap-1">
                                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5"/></svg>
                                    {{ __('Winter:') }} {{ $p->application_deadline_winter->format('d.m') }}
                                </span>
                            @endif
                            @if ($p->application_deadline_summer)
                                <span class="inline-flex items-center gap-1">
                                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5"/></svg>
                                    {{ __('Summer:') }} {{ $p->application_deadline_summer->format('d.m') }}
                                </span>
                            @endif
                            @if ($p->university?->is_uni_assist_member)
                                <span class="text-blue-700">Uni-Assist</span>
                            @endif
                            @if ($p->admission_mode === 'zulassungsfrei')
                                <span class="inline-flex items-center gap-1 text-emerald-700 font-semibold"><x-svg-icon name="lock-closed" class="w-3 h-3" /> {{ __('NC Frei') }}</span>
                            @elseif ($p->admission_mode === 'oertlich')
                                <span class="inline-flex items-center gap-1 text-orange-700"><x-svg-icon name="exclamation-triangle" class="w-3 h-3" /> {{ __('Local NC') }}</span>
                            @elseif ($p->admission_mode === 'bundesweit')
                                <span class="inline-flex items-center gap-1 text-red-700"><x-svg-icon name="flag" class="w-3 h-3" /> {{ __('Nationwide NC') }}</span>
                            @elseif ($p->admission_mode === 'auswahl')
                                <span class="inline-flex items-center gap-1 text-indigo-700"><span class="w-1.5 h-1.5 rounded-full bg-indigo-500"></span> {{ __('Selection process') }}</span>
                            @endif
                        </div>

                        @if ($p->description && app()->getLocale() === 'tr')
                            <p class="text-sm text-gray-700 line-clamp-2">{{ \Illuminate\Support\Str::limit($p->description, 180) }}</p>
                        @elseif ($p->description_en)
                            <p class="text-sm text-gray-600 line-clamp-2">
                                <span class="text-xs font-bold text-gray-400 uppercase tracking-wider mr-1">EN</span>
                                {{ \Illuminate\Support\Str::limit($p->description_en, 160) }}
                            </p>
                        @endif
                    </div>
                </div>
            </a>
        @endforeach
    </div>

    <div class="mt-8">
        {{ $programs->links() }}
    </div>
@endif
