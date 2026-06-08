@extends('layouts.app')

@section('title', __('Housing Experiences') . ' — ' . brand('name'))

@section('content')
<section class="bg-gradient-to-br from-primary-700 via-primary-600 to-primary-800 text-white">
    <div class="max-w-[1400px] mx-auto px-4 py-8">
        <nav class="text-sm text-primary-100 mb-2">
            <a href="{{ lroute('housing.index') }}" class="hover:text-white">{{ __('Housing') }}</a>
            <span class="mx-2">/</span>
            <span>{{ __('Experiences') }}</span>
        </nav>
        <h1 class="text-3xl md:text-4xl font-extrabold">💬 {{ __('Community Experiences') }}</h1>
        <p class="text-primary-100 mt-2">{{ __('What international students go through while finding a home in Germany.') }}</p>
    </div>
</section>

<div class="max-w-[1400px] mx-auto px-4 py-8">
    @auth
        <div class="mb-6 text-right">
            <a href="{{ lroute('housing.tip-create') }}" class="inline-block bg-accent-500 hover:bg-accent-600 text-white font-bold px-5 py-2.5 rounded-lg transition">
                ✍ {{ __('Share my experience') }}
            </a>
        </div>
    @endauth

    @if ($tips->isEmpty())
        <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-8 text-center">
            <p class="text-yellow-900 font-semibold mb-2">{{ __('No experiences yet.') }}</p>
            @auth
                <a href="{{ lroute('housing.tip-create') }}" class="inline-block bg-primary-600 hover:bg-primary-700 text-white px-5 py-2 rounded font-semibold">
                    {{ __('Be the first to share an experience') }}
                </a>
            @else
                <a href="{{ route('login') }}" class="inline-block bg-primary-600 hover:bg-primary-700 text-white px-5 py-2 rounded font-semibold">
                    {{ __('Log in & share') }}
                </a>
            @endauth
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach ($tips as $tip)
                <article class="bg-white border border-gray-200 rounded-xl p-5">
                    <div class="flex items-start justify-between gap-2 mb-3">
                        <h3 class="font-bold text-gray-900 leading-snug">{{ $tip->title }}</h3>
                        <span class="inline-block text-xs font-semibold bg-primary-100 text-primary-700 px-2 py-0.5 rounded-full whitespace-nowrap">
                            {{ ucfirst($tip->category) }}
                        </span>
                    </div>
                    <p class="text-sm text-gray-700 leading-relaxed mb-3">{{ $tip->content }}</p>
                    <p class="text-xs text-gray-500">
                        @if ($tip->city_name) 📍 {{ $tip->city_name }} · @endif
                        {{ $tip->user->name ?? __('Anonymous') }} · {{ $tip->created_at->diffForHumans() }}
                    </p>
                </article>
            @endforeach
        </div>

        <div class="mt-8">{{ $tips->links() }}</div>
    @endif
</div>
@endsection
