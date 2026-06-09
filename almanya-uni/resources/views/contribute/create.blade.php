@extends('layouts.app')

@section('title', __('Share Your Experience — Community Contribution') . ' — ' . brand('name'))

<x-seo :title="__('Share Your Experience — AlmanyaUni Community')" :description="__('Share your Germany experience and tips. Once approved, earn the Community Contributor badge on your profile.')" />

@section('content')

<section class="bg-gradient-to-br from-emerald-700 via-emerald-600 to-teal-500 text-white">
    <div class="max-w-3xl mx-auto px-4 py-10 md:py-14">
        <nav class="text-sm text-emerald-100 mb-3">
            <a href="/" class="hover:text-white">{{ __('Home') }}</a>
            <span class="mx-2 opacity-60">›</span>
            <span class="text-white">{{ __('Share Experience') }}</span>
        </nav>
        <h1 class="text-3xl md:text-4xl font-extrabold leading-tight drop-shadow mb-2">{{ __('Share Your Experience') }}</h1>
        <p class="text-emerald-50 max-w-2xl">
            {!! __('What you learned on your Germany journey will guide another student. Once approved, you earn the <strong class="text-white">"Community Contributor"</strong> badge on your profile.') !!}
        </p>
    </div>
</section>

<div class="max-w-3xl mx-auto px-4 py-10">

    @if (session('status'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl p-4 mb-6">{{ session('status') }}</div>
    @endif

    <form method="POST" action="{{ route('contribute.store') }}" class="bg-white border border-gray-200 rounded-2xl p-6 space-y-5">
        @csrf

        <div>
            <label class="block text-sm font-semibold text-gray-900 mb-2">{{ __('Contribution type') }}</label>
            <div class="grid grid-cols-3 gap-2">
                @foreach ($types as $val => $label)
                    <label class="cursor-pointer">
                        <input type="radio" name="type" value="{{ $val }}" {{ old('type','experience')===$val?'checked':'' }} class="sr-only peer">
                        <div class="border-2 border-gray-200 peer-checked:border-emerald-500 peer-checked:bg-emerald-50 rounded-lg p-3 text-center text-sm font-medium transition">{{ $label }}</div>
                    </label>
                @endforeach
            </div>
        </div>

        <div>
            <label class="block text-sm font-semibold text-gray-900 mb-2">{{ __('Related topic') }}</label>
            <div class="flex flex-wrap gap-2">
                @foreach ($targets as $val => $label)
                    <label class="cursor-pointer">
                        <input type="radio" name="target_type" value="{{ $val }}" {{ old('target_type','general')===$val?'checked':'' }} class="sr-only peer">
                        <div class="border border-gray-200 peer-checked:border-emerald-500 peer-checked:bg-emerald-50 peer-checked:text-emerald-700 rounded-full px-4 py-1.5 text-sm transition">{{ $label }}</div>
                    </label>
                @endforeach
            </div>
            <input type="text" name="target_label" value="{{ old('target_label') }}" placeholder="{{ __('Which city/university? (optional — e.g., Berlin, TU München)') }}"
                   class="mt-3 w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-400 focus:border-emerald-400">
        </div>

        <div>
            <label class="block text-sm font-semibold text-gray-900 mb-2">{{ __('Title') }}</label>
            <input type="text" name="title" value="{{ old('title') }}" required maxlength="160"
                   placeholder="{{ __('E.g.: Opened my Sperrkonto with Fintiba in 1 day — step by step') }}"
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-emerald-400 focus:border-emerald-400">
        </div>

        <div>
            <label class="block text-sm font-semibold text-gray-900 mb-2">{{ __('Your experience / tip') }}</label>
            <textarea name="content" required rows="8" minlength="60" maxlength="6000"
                      placeholder="{{ __('Describe your experience, what you learned, and your advice to other students in detail. The more concrete, the more useful.') }}"
                      class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-emerald-400 focus:border-emerald-400">{{ old('content') }}</textarea>
            <p class="text-xs text-gray-400 mt-1">{{ __('At least 60 characters. Do not share personal/contact info.') }}</p>
        </div>

        @if ($errors->any())
            <div class="bg-red-50 border border-red-200 rounded p-3 text-sm text-red-700">
                <ul class="list-disc list-inside">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            </div>
        @endif

        <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold px-6 py-3 rounded-lg transition shadow-md">
            {{ __('Submit My Contribution') }}
        </button>
        <p class="text-xs text-gray-500">{{ __('Published after editor approval. Spam/ad content will not be approved.') }}</p>
    </form>

    {{-- User's contributions --}}
    @if ($mine->isNotEmpty())
        <section class="mt-8">
            <h2 class="text-lg font-bold text-gray-900 mb-3">{{ __('My Contributions') }}</h2>
            <div class="space-y-2">
                @foreach ($mine as $c)
                    <div class="flex items-center justify-between bg-white border border-gray-200 rounded-lg px-4 py-3">
                        <div class="min-w-0">
                            <p class="font-medium text-sm text-gray-900 truncate">{{ $c->type_label }} · {{ $c->title }}</p>
                            <p class="text-xs text-gray-400">{{ $c->created_at->format('d.m.Y') }}</p>
                        </div>
                        <span class="text-xs font-semibold px-2.5 py-1 rounded-full whitespace-nowrap
                            {{ $c->status==='approved' ? 'bg-emerald-100 text-emerald-700' : ($c->status==='rejected' ? 'bg-red-100 text-red-700' : 'bg-amber-100 text-amber-700') }}">
                            {{ ['pending'=>'⏳ ' . __('Pending approval'),'approved'=>'✅ ' . __('Published'),'rejected'=>'❌ ' . __('Rejected')][$c->status] ?? $c->status }}
                        </span>
                    </div>
                @endforeach
            </div>
        </section>
    @endif
</div>
@endsection
