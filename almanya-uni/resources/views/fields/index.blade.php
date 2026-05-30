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
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25"/></svg>
                <span><strong class="text-lg">{{ $fields->count() }}</strong> {{ __('fields') }}</span>
            </div>
            <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-white/15 backdrop-blur ring-1 ring-white/20">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.438 60.438 0 0 0-.491 6.347A48.62 48.62 0 0 1 12 20.904a48.62 48.62 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.636 50.636 0 0 0-2.658-.813A59.906 59.906 0 0 1 12 3.493a59.903 59.903 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0 1 12 13.489a50.702 50.702 0 0 1 7.74-3.342M6.75 15a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Zm0 0v-3.675A55.378 55.378 0 0 1 12 8.443m-7.007 11.55A5.981 5.981 0 0 0 6.75 15.75v-1.5"/></svg>
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
                            <div class="w-full h-full flex items-center justify-center"
                                 style="background: linear-gradient(135deg, {{ $field->color }}33, {{ $field->color }}66);">
                                <span class="text-white" style="color: {{ $field->color }};">{!! e_icon($field->icon, 'w-16 h-16') !!}</span>
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
                            <span style="color: {{ $field->color }};">{!! e_icon($field->icon, 'w-6 h-6') !!}</span>
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
