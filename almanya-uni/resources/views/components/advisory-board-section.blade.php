@php
    $advisors = \App\Models\Advisor::active()->orderBy('sort_order')->limit(12)->get();
@endphp

{{-- Aktif danışman yoksa bölüm HİÇ görünmez (sahte/boş kurul yok). --}}
@if ($advisors->isNotEmpty())
<section class="bg-white py-12 md:py-16">
    <div class="max-w-3xl mx-auto px-4 text-center">
        <h2 class="text-2xl md:text-4xl font-extrabold text-gray-900 mb-4">{{ __('Our Advisory Board') }}</h2>
        <p class="text-gray-600 leading-relaxed mb-8">
            {{ __('A group of experienced advisors supports our mission to make studying in Germany transparent and accessible for international students.') }}
        </p>

        <div class="flex items-center justify-center flex-wrap gap-3 md:gap-4 mb-8">
            @foreach ($advisors as $a)
                @php $link = $a->linkedin_url ?: $a->profile_url; @endphp
                <a @if ($link) href="{{ $link }}" target="_blank" rel="noopener" @endif
                   class="group relative" title="{{ $a->name }}@if ($a->role_title) — {{ $a->role_title }}@endif">
                    @if ($a->photo_url)
                        <img src="{{ \Illuminate\Support\Str::startsWith($a->photo_url, 'http') ? $a->photo_url : asset('storage/' . $a->photo_url) }}"
                             alt="{{ $a->name }}" loading="lazy"
                             class="w-14 h-14 md:w-16 md:h-16 rounded-full object-cover ring-2 ring-gray-100 group-hover:ring-primary-400 transition shadow-sm">
                    @else
                        <span class="w-14 h-14 md:w-16 md:h-16 rounded-full bg-primary-100 text-primary-700 font-bold flex items-center justify-center ring-2 ring-gray-100">
                            {{ mb_strtoupper(mb_substr($a->name, 0, 1)) }}
                        </span>
                    @endif
                </a>
            @endforeach
        </div>

        <a href="{{ route('advisory-board') }}"
           class="inline-flex items-center gap-2 bg-primary-600 hover:bg-primary-700 text-white font-semibold py-2.5 px-6 rounded-lg transition">
            {{ __('Meet the Advisory Board') }} →
        </a>
    </div>
</section>
@endif
