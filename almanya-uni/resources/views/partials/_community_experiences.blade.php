{{--
    Onaylı öğrenci deneyimleri bloğu.
    Parametre: $experiences (Contribution collection), $shareLabel (CTA için target adı)
--}}
@php
    $experiences = $experiences ?? collect();
    $shareLabel = $shareLabel ?? '';
    $initials = fn($n) => collect(explode(' ', trim($n)))->map(fn($p) => mb_substr($p,0,1))->take(2)->implode('');
@endphp

<section class="mt-10">
    <div class="flex items-baseline justify-between mb-4 flex-wrap gap-2">
        <h2 class="text-2xl font-bold text-gray-900 flex items-center gap-2"><x-svg-icon name="leaf" class="w-6 h-6 text-emerald-600" /> {{ __('Student Experiences') }}</h2>
        <a href="{{ route('contribute') }}" class="text-sm text-emerald-600 hover:underline font-semibold">{{ __('Share yours too') }} →</a>
    </div>

    @if ($experiences->isEmpty())
        {{-- Boş durum — ilk deneyimi davet --}}
        <div class="bg-gradient-to-br from-emerald-50 to-teal-50 border border-emerald-200 rounded-xl p-6 text-center">
            <div class="inline-flex items-center justify-center w-14 h-14 mx-auto mb-2 rounded-full bg-emerald-100 text-emerald-600"><x-svg-icon name="chat-bubble" class="w-8 h-8" /></div>
            <p class="text-gray-700 font-medium mb-1">{{ __('No experience shared yet.') }}</p>
            <p class="text-sm text-gray-500 mb-4">{{ $shareLabel ? __('Be the first to share an experience about :label and help other students.', ['label' => $shareLabel]) : __('Be the first to share an experience on this topic and help other students.') }}</p>
            <a href="{{ route('contribute') }}" class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white px-5 py-2.5 rounded-lg font-semibold transition"><x-svg-icon name="leaf" class="w-4 h-4" /> {{ __('Share your experience') }}</a>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach ($experiences as $exp)
                <article class="bg-white border border-gray-200 rounded-xl p-5">
                    <div class="flex items-center gap-3 mb-3">
                        @if ($exp->user?->avatar_url)
                            <img src="{{ $exp->user->avatar_url }}" alt="{{ $exp->user->name }}" class="w-10 h-10 rounded-full object-cover">
                        @else
                            <div class="w-10 h-10 rounded-full bg-gradient-to-br from-emerald-500 to-teal-500 text-white text-sm font-bold flex items-center justify-center">{{ $initials($exp->user?->name ?? '?') }}</div>
                        @endif
                        <div class="flex-1 min-w-0">
                            <p class="font-semibold text-sm text-gray-900 flex items-center gap-1.5">
                                {{ $exp->user?->name ?? __('Community member') }}
                                <span class="text-emerald-600" title="{{ __('Community Contributor') }}"><x-svg-icon name="leaf" class="w-3 h-3" /></span>
                            </p>
                            <p class="text-[11px] text-gray-400">{{ $exp->type_label }} · {{ $exp->approved_at?->format('d.m.Y') }}</p>
                        </div>
                    </div>
                    <h3 class="font-bold text-gray-900 text-sm mb-1">{{ $exp->title }}</h3>
                    <p class="text-sm text-gray-600 leading-relaxed">{{ \Illuminate\Support\Str::limit($exp->content, 220) }}</p>
                </article>
            @endforeach
        </div>
    @endif
</section>
