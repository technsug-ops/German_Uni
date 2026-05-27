@props([
    'university' => null,
    'reviews' => [],
    'stats' => null,
])

@php
    $total = (int) ($stats?->total ?? 0);
    $avg = $stats?->avg_rating ? round((float) $stats->avg_rating, 1) : null;
    $uniSlug = $university?->slug ?? '';
    $uniName = $university?->display_name ?? $university?->name_de ?? '';

    // Schema.org AggregateRating for SEO/AIO
    $schemaAgg = $total > 0 && $avg ? [
        '@context' => 'https://schema.org',
        '@type' => 'EducationalOrganization',
        'name' => $uniName,
        'aggregateRating' => [
            '@type' => 'AggregateRating',
            'ratingValue' => $avg,
            'reviewCount' => $total,
            'bestRating' => 5,
            'worstRating' => 1,
        ],
    ] : null;
@endphp

@if ($schemaAgg)
    <x-json-ld :data="$schemaAgg" />
@endif

<section class="py-12 bg-gray-50 border-t border-gray-200" id="reviews">
    <div class="max-w-4xl mx-auto px-4">
        <header class="mb-6">
            <h2 class="text-2xl md:text-3xl font-bold text-gray-900">
                💬 {{ __('Student Reviews') }}
            </h2>
            <p class="text-gray-600 mt-1">
                {{ __('Verified reviews from current students, alumni and applicants.') }}
            </p>
        </header>

        {{-- AGGREGATE RATING (header) --}}
        @if ($total > 0)
            <div class="bg-white border border-gray-200 rounded-2xl p-6 mb-8 shadow-sm">
                <div class="flex flex-wrap items-center gap-6">
                    <div class="text-center">
                        <div class="text-5xl font-extrabold text-amber-500 leading-none">{{ number_format($avg, 1, ',', '') }}</div>
                        <div class="text-amber-400 text-xl mt-1">
                            @for ($i = 1; $i <= 5; $i++)
                                {{ $i <= round($avg) ? '★' : '☆' }}
                            @endfor
                        </div>
                        <p class="text-xs text-gray-500 mt-1">{{ __(':n reviews', ['n' => $total]) }}</p>
                    </div>
                    <div class="flex-1 min-w-[240px] space-y-1.5">
                        @foreach ([5, 4, 3, 2, 1] as $star)
                            @php
                                $countForStar = (int) ($stats->{'r' . $star} ?? 0);
                                $pct = $total > 0 ? round(($countForStar / $total) * 100) : 0;
                            @endphp
                            <div class="flex items-center gap-2 text-xs">
                                <span class="w-8 text-right text-gray-600">{{ $star }}★</span>
                                <div class="flex-1 bg-gray-200 rounded-full h-2 overflow-hidden">
                                    <div class="h-full bg-amber-400" style="width: {{ $pct }}%"></div>
                                </div>
                                <span class="w-10 text-right text-gray-500">{{ $countForStar }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif

        {{-- REVIEWS LIST --}}
        @if (count($reviews) > 0)
            <div class="space-y-4 mb-10">
                @foreach ($reviews as $rev)
                    <article class="bg-white border border-gray-200 rounded-xl p-5 shadow-sm"
                             itemscope itemtype="https://schema.org/Review">
                        <header class="flex flex-wrap items-start justify-between gap-3 mb-3">
                            <div>
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="text-amber-400 text-lg"
                                          itemprop="reviewRating" itemscope itemtype="https://schema.org/Rating">
                                        @for ($i = 1; $i <= 5; $i++){{ $i <= $rev->rating ? '★' : '☆' }}@endfor
                                        <meta itemprop="ratingValue" content="{{ $rev->rating }}">
                                        <meta itemprop="bestRating" content="5">
                                    </span>
                                    @if ($rev->status_label)
                                        <span class="text-[10px] font-bold uppercase tracking-wider bg-emerald-100 text-emerald-800 px-2 py-0.5 rounded">
                                            {{ $rev->status_label }}
                                        </span>
                                    @endif
                                </div>
                                <h3 class="font-bold text-gray-900" itemprop="name">{{ $rev->title }}</h3>
                                <p class="text-xs text-gray-500 mt-0.5">
                                    <span itemprop="author">{{ $rev->author_display_name }}</span>
                                    @if ($rev->author_program) · {{ $rev->author_program }} @endif
                                    @if ($rev->study_year) · {{ __('since :y', ['y' => $rev->study_year]) }} @endif
                                    · <time itemprop="datePublished" datetime="{{ $rev->created_at->toIso8601String() }}">{{ $rev->created_at->translatedFormat('d M Y') }}</time>
                                </p>
                            </div>
                        </header>

                        <p class="text-sm text-gray-800 leading-relaxed whitespace-pre-line" itemprop="reviewBody">{{ $rev->body }}</p>

                        {{-- Helpful / report buttons --}}
                        <footer class="flex items-center gap-3 mt-4 pt-3 border-t border-gray-100"
                                x-data="reviewVoter({{ $rev->id }}, {{ $rev->helpful_count }}, {{ $rev->unhelpful_count }})">
                            <button type="button" @click="vote('helpful')"
                                    class="inline-flex items-center gap-1.5 text-xs text-gray-600 hover:text-emerald-700 hover:bg-emerald-50 px-2 py-1 rounded transition"
                                    :disabled="voted">
                                👍 <span x-text="counts.helpful"></span>
                                <span class="hidden sm:inline">{{ __('Helpful') }}</span>
                            </button>
                            <button type="button" @click="vote('unhelpful')"
                                    class="inline-flex items-center gap-1.5 text-xs text-gray-600 hover:text-gray-800 hover:bg-gray-50 px-2 py-1 rounded transition"
                                    :disabled="voted">
                                👎 <span x-text="counts.unhelpful"></span>
                            </button>
                            <button type="button" @click="vote('report')"
                                    class="ml-auto inline-flex items-center gap-1.5 text-xs text-gray-400 hover:text-red-600 transition"
                                    :disabled="voted"
                                    title="{{ __('Report this review') }}">
                                🚩 <span class="hidden sm:inline">{{ __('Report') }}</span>
                            </button>
                            <span x-show="voted" x-cloak class="text-xs text-emerald-700">✓ {{ __('Thanks!') }}</span>
                        </footer>
                    </article>
                @endforeach
            </div>
        @else
            <div class="bg-white border-2 border-dashed border-gray-200 rounded-xl p-8 text-center mb-8">
                <p class="text-gray-500 mb-1">{{ __('No reviews yet. Be the first!') }}</p>
                <p class="text-xs text-gray-400">{{ __('Verified students and alumni can share their experience below.') }}</p>
            </div>
        @endif

        {{-- SUBMIT FORM --}}
        @if ($uniSlug)
        <details class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm" {{ session('errors') && session('errors')->any() ? 'open' : '' }}>
            <summary class="cursor-pointer font-bold text-gray-900 flex items-center justify-between">
                <span>✍️ {{ __('Write a review about :uni', ['uni' => $uniName]) }}</span>
                <span class="text-primary-600 text-sm font-semibold">{{ __('Open form') }} ↓</span>
            </summary>

            @if (session('status'))
                <div class="mt-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-lg p-3 text-sm">
                    ✓ {{ session('status') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mt-4 bg-red-50 border border-red-200 text-red-800 rounded-lg p-3 text-sm">
                    @foreach ($errors->all() as $err)
                        <p>• {{ $err }}</p>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('universities.reviews.store', $uniSlug) }}" class="mt-5 space-y-4">
                @csrf

                {{-- Rating star picker --}}
                <div x-data="{ rating: {{ old('rating', 0) }} }">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">{{ __('Your rating') }} <span class="text-red-500">*</span></label>
                    <div class="flex gap-1 text-3xl">
                        @for ($i = 1; $i <= 5; $i++)
                            <button type="button" @click="rating = {{ $i }}"
                                    class="transition focus:outline-none"
                                    :class="rating >= {{ $i }} ? 'text-amber-400' : 'text-gray-300 hover:text-amber-300'">
                                ★
                            </button>
                        @endfor
                    </div>
                    <input type="hidden" name="rating" :value="rating" required>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">{{ __('Title') }} <span class="text-red-500">*</span></label>
                        <input type="text" name="title" required maxlength="200" value="{{ old('title') }}"
                               placeholder="{{ __('Summarize your experience in one line') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:border-primary-500 focus:ring-1 focus:ring-primary-500">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">{{ __('Programme') }} ({{ __('optional') }})</label>
                        <input type="text" name="author_program" maxlength="150" value="{{ old('author_program') }}"
                               placeholder="{{ __('e.g. Informatik Bachelor') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:border-primary-500 focus:ring-1 focus:ring-primary-500">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">{{ __('Your review') }} <span class="text-red-500">*</span></label>
                    <textarea name="body" required rows="5" minlength="30" maxlength="2500"
                              placeholder="{{ __('What did you experience? Application, study environment, professors, social life, city, costs... Min 30 characters.') }}"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:border-primary-500 focus:ring-1 focus:ring-primary-500">{{ old('body') }}</textarea>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">{{ __('Your name') }} <span class="text-red-500">*</span></label>
                        <input type="text" name="author_name" required maxlength="100" value="{{ old('author_name') }}"
                               placeholder="{{ __('First name + initial of last') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:border-primary-500 focus:ring-1 focus:ring-primary-500">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">{{ __('Email (for verification)') }} <span class="text-red-500">*</span></label>
                        <input type="email" name="author_email" required maxlength="150" value="{{ old('author_email') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:border-primary-500 focus:ring-1 focus:ring-primary-500">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">{{ __('Your status') }}</label>
                        <select name="author_status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:border-primary-500 focus:ring-1 focus:ring-primary-500">
                            <option value="">{{ __('— select —') }}</option>
                            <option value="current_student" @selected(old('author_status') === 'current_student')>{{ __('Current student') }}</option>
                            <option value="alumni" @selected(old('author_status') === 'alumni')>{{ __('Alumni') }}</option>
                            <option value="admitted" @selected(old('author_status') === 'admitted')>{{ __('Admitted (not started yet)') }}</option>
                            <option value="applicant" @selected(old('author_status') === 'applicant')>{{ __('Applicant') }}</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="flex items-start gap-2 text-xs text-gray-600">
                        <input type="checkbox" name="consent" value="1" required class="mt-1">
                        <span>{!! __('I confirm this review is based on my own experience and I consent to its publication after moderation. <a href="/terms" class="text-primary-600 underline" target="_blank">Terms</a> · <a href="/privacy" class="text-primary-600 underline" target="_blank">Privacy</a>') !!}</span>
                    </label>
                </div>

                <x-math-captcha compact />

                <button type="submit" class="w-full sm:w-auto bg-primary-600 hover:bg-primary-700 text-white font-bold px-6 py-2.5 rounded-lg transition shadow-sm">
                    {{ __('Submit review') }} →
                </button>
                <p class="text-xs text-gray-500">{{ __('After verification via email link, your review will be reviewed by our team within 48 hours.') }}</p>
            </form>
        </details>
        @endif
    </div>
</section>

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    window.Alpine && Alpine.data('reviewVoter', (reviewId, helpful, unhelpful) => ({
        voted: false,
        counts: { helpful: helpful, unhelpful: unhelpful, report: 0 },
        async vote(type) {
            if (this.voted) return;
            this.voted = true;
            try {
                const r = await fetch('/' + (document.documentElement.lang || 'en') + '/reviews/' + reviewId + '/vote', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')?.content || '' },
                    body: JSON.stringify({ vote: type }),
                });
                if (r.ok) {
                    const data = await r.json();
                    if (data.count !== undefined && type !== 'report') this.counts[type] = data.count;
                } else {
                    this.voted = false; // Permit retry
                }
            } catch(e) { this.voted = false; }
        }
    }));
});
</script>
@endpush
