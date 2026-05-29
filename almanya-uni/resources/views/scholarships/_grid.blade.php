{{-- Scholarships grid partial — also returned standalone for XHR async-filter updates. --}}
<div class="flex items-center justify-between mb-6">
    <p class="text-sm text-gray-600">
        {!! __('<strong>:n</strong> scholarships found', ['n' => number_format($scholarships->total(), 0, ',', '.')]) !!}
    </p>
</div>

@if ($scholarships->isEmpty())
    <x-empty-state
        icon="🎖️"
        :title="__('No scholarships match this filter.')"
        :description="__('Try removing one of the filters or browse the full DAAD catalog.')"
        :actions="[
            ['label' => __('Reset filter'), 'url' => route('scholarships.daad'), 'primary' => true],
            ['label' => __('Scholarships overview'), 'url' => route('scholarships.index')],
        ]"
    />
@else
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach ($scholarships as $sch)
            <a href="{{ route('scholarships.show', $sch->slug) }}"
               class="group bg-white rounded-xl border border-gray-200 hover:border-primary-500 hover:shadow-md transition p-5 flex flex-col">
                <div class="flex items-start justify-between gap-2 mb-2">
                    <h3 class="font-bold text-gray-900 group-hover:text-primary-600 line-clamp-2">
                        {{ $sch->name }}
                    </h3>
                    @if ($sch->is_daad)
                        <span class="shrink-0 px-2 py-0.5 rounded-full bg-blue-50 text-blue-700 text-xs font-semibold">DAAD</span>
                    @else
                        <span class="shrink-0 px-2 py-0.5 rounded-full bg-gray-100 text-gray-600 text-xs font-semibold">{{ __('Partner') }}</span>
                    @endif
                </div>
                @if ($sch->programmname)
                    <p class="text-xs text-gray-500 mb-3 line-clamp-1">
                        {{ $sch->programmname }}
                    </p>
                @endif
                @php $intro = $sch->introductionText('en') ?? $sch->introductionText('de'); @endphp
                @if ($intro)
                    <p class="text-sm text-gray-600 line-clamp-3 mb-4">{{ \Illuminate\Support\Str::limit(strip_tags($intro), 200) }}</p>
                @endif
                <div class="mt-auto flex flex-wrap gap-1.5">
                    @foreach ($sch->subjects->take(3) as $sub)
                        <span class="px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 text-xs">{{ $sub->name }}</span>
                    @endforeach
                    @foreach ($sch->statuses->take(2) as $st)
                        <span class="px-2 py-0.5 rounded-full bg-amber-50 text-amber-700 text-xs">{{ $st->name }}</span>
                    @endforeach
                </div>
            </a>
        @endforeach
    </div>

    <div class="mt-8">
        {{ $scholarships->links() }}
    </div>
@endif
