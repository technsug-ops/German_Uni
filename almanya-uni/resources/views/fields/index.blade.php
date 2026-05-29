@extends('layouts.app')

@section('title', __('Fields of Study — Which Subject to Study in Germany?') . ' — ' . brand('name'))
@section('meta_description', __('Engineering, Medicine, Computer Science, Law, Art — explore programs in Germany by the field you want to study.'))

@section('content')

{{-- HERO --}}
<section class="bg-gradient-to-br from-primary-700 via-primary-600 to-accent-500 text-white">
    <div class="max-w-[1400px] mx-auto px-4 py-12 md:py-16">
        <nav class="text-sm text-primary-100 mb-3">
            <a href="/" class="hover:text-white">{{ __('Home') }}</a>
            <span class="mx-2 opacity-50">›</span>
            <span class="text-white">{{ __('Fields') }}</span>
        </nav>
        <h1 class="text-3xl md:text-5xl font-extrabold mb-3 leading-tight">
            {{ __('Fields of Study') }}
        </h1>
        <p class="text-lg md:text-xl text-primary-100 max-w-3xl mb-6">
            {{ __('Discover the fields you can study in Germany. Each field has its own list of programs, top universities and career perspective.') }}
        </p>
        <div class="flex flex-wrap gap-4 text-sm">
            <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-white/15 backdrop-blur ring-1 ring-white/20">
                <span class="text-2xl">📚</span>
                <span><strong class="text-lg">{{ $fields->count() }}</strong> {{ __('fields') }}</span>
            </div>
            <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-white/15 backdrop-blur ring-1 ring-white/20">
                <span class="text-2xl">🎓</span>
                <span><strong class="text-lg">{{ number_format($fields->sum('programs_count')) }}</strong> {{ __('programs') }}</span>
            </div>
        </div>
    </div>
</section>

{{-- GRID --}}
<section class="bg-gray-50 py-10">
    <div class="max-w-[1400px] mx-auto px-4">
        @if ($fields->isEmpty())
            <x-empty-state
                icon="🎯"
                :title="__('No fields available right now')"
                :description="__('Try browsing universities or programs while we are updating the catalog.')"
                :actions="[
                    ['label' => __('Universities'), 'url' => route('universities.index'), 'primary' => true, 'icon' => '🎓'],
                    ['label' => __('Programs'), 'url' => route('programs.index'), 'icon' => '📚'],
                ]"
            />
        @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach ($fields as $field)
                <a href="{{ route('fields.show', $field->slug) }}"
                   class="group bg-white rounded-xl overflow-hidden border border-gray-200 hover:border-primary-500 hover:shadow-lg hover:-translate-y-0.5 transition-all flex flex-col">
                    <div class="aspect-[16/9] overflow-hidden bg-gray-100 relative">
                        @if($field->image_url)
                            <img src="{{ $field->image_url }}" alt="{{ $field->name }}" loading="lazy"
                                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                            <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent"></div>
                        @else
                            <div class="w-full h-full flex items-center justify-center text-7xl"
                                 style="background: linear-gradient(135deg, {{ $field->color }}33, {{ $field->color }}66);">
                                <span>{{ $field->icon }}</span>
                            </div>
                        @endif
                        @if($field->content_blocks)
                            <span class="absolute top-2 left-2 inline-block px-2 py-0.5 rounded-full bg-emerald-500 text-white text-[10px] font-bold uppercase tracking-wider shadow-sm">✦ {{ __('Guide') }}</span>
                        @endif
                        <span class="absolute bottom-2 right-2 inline-block px-2 py-0.5 rounded-full bg-black/60 backdrop-blur text-white text-xs font-semibold">
                            {{ __(':n programs', ['n' => number_format($field->programs_count)]) }}
                        </span>
                    </div>
                    <div class="p-5">
                        <div class="flex items-center gap-2 mb-1">
                            <span class="text-2xl">{{ $field->icon }}</span>
                            <h3 class="text-lg font-bold text-gray-900 group-hover:text-primary-600 transition leading-tight">
                                {{ $field->name }}
                            </h3>
                        </div>
                        <p class="text-xs text-gray-500">{{ $field->name }}</p>
                    </div>
                </a>
            @endforeach
        </div>
        @endif
    </div>
</section>

@endsection
