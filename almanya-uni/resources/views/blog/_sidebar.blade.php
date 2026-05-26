@php
    $activeId = $active_category ?? null;
    $activeId = $activeId?->id;
@endphp
<aside class="space-y-6">
    <div class="bg-white border border-gray-200 rounded-lg p-5">
        <h3 class="font-bold text-lg mb-4">{{ __('Categories') }}</h3>
        <ul class="space-y-2">
            <li>
                <a href="{{ route('blog.index') }}"
                   class="block px-3 py-2 rounded {{ $activeId === null ? 'bg-primary-100 text-primary-800 font-semibold' : 'text-gray-700 hover:bg-gray-50' }}">
                    {{ __('All Posts') }}
                </a>
            </li>
            @foreach ($categories as $cat)
                <li>
                    <a href="{{ route('blog.category', $cat->slug) }}"
                       class="flex items-center justify-between px-3 py-2 rounded {{ $activeId === $cat->id ? 'bg-primary-100 text-primary-800 font-semibold' : 'text-gray-700 hover:bg-gray-50' }}">
                        <span class="flex items-center gap-2">
                            @if ($cat->color)
                                <span class="inline-block w-3 h-3 rounded-full" style="background-color: {{ $cat->color }}"></span>
                            @endif
                            {{ $cat->name }}
                        </span>
                        <span class="text-xs text-gray-500">{{ $cat->posts_count ?? 0 }}</span>
                    </a>
                    {{-- Alt kategoriler --}}
                    @if ($cat->relationLoaded('children') && $cat->children->isNotEmpty())
                        <ul class="ml-4 mt-1 space-y-1 border-l border-gray-200 pl-2">
                            @foreach ($cat->children as $child)
                                <li>
                                    <a href="{{ route('blog.category', $child->slug) }}"
                                       class="flex items-center justify-between px-3 py-1.5 rounded text-sm {{ $activeId === $child->id ? 'bg-primary-100 text-primary-800 font-semibold' : 'text-gray-600 hover:bg-gray-50' }}">
                                        <span class="flex items-center gap-2">
                                            <span class="text-gray-400">↳</span>
                                            {{ $child->name }}
                                        </span>
                                        <span class="text-xs text-gray-400">{{ $child->posts_count ?? 0 }}</span>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </li>
            @endforeach
        </ul>
    </div>

    <div class="bg-gradient-to-br from-primary-50 to-accent-50 border border-primary-200 rounded-lg p-5">
        <div class="text-2xl mb-2">📬</div>
        <h3 class="font-bold text-lg mb-2 text-gray-900">{{ __('Join the Newsletter') }}</h3>
        <p class="text-sm text-gray-700 mb-4 leading-relaxed">{{ __('Weekly summary, new guides, and application deadlines delivered to your inbox.') }}</p>
        <a href="#newsletter-signup"
           onclick="event.preventDefault(); const el = document.getElementById('newsletter-signup'); if (el) { el.scrollIntoView({behavior:'smooth', block:'center'}); const inp = el.querySelector('input[name=email]'); if (inp) setTimeout(() => inp.focus(), 600); }"
           class="block text-center w-full bg-accent-500 hover:bg-accent-600 text-white px-4 py-2.5 rounded-lg font-bold transition shadow-sm">
            {{ __('Subscribe') }} →
        </a>
        <p class="text-xs text-gray-500 mt-2 text-center">{{ __('Free · Unsubscribe anytime') }}</p>
    </div>
</aside>
