@extends('layouts.app')

@section('title', __('Contact Us') . ' — ' . brand('name'))

<x-seo :title="__('Contact Us') . ' — ' . brand('name')" :description="__('Get in touch — questions, partnership, translation or development help. We reply by email.')" />

@section('content')

<section class="bg-gradient-to-br from-primary-700 via-primary-600 to-accent-500 text-white">
    <div class="max-w-2xl mx-auto px-4 py-10 md:py-14">
        <nav class="text-sm text-primary-100 mb-3">
            <a href="/" class="hover:text-white">{{ __('Home') }}</a>
            <span class="mx-2 opacity-60">›</span>
            <span class="text-white">{{ __('Contact') }}</span>
        </nav>
        <h1 class="text-3xl md:text-4xl font-extrabold leading-tight drop-shadow mb-2">✉️ {{ __('Contact Us') }}</h1>
        <p class="text-primary-50 max-w-2xl">
            {{ __('A question, a partnership idea, or want to help translate or develop? Write to us — we reply by email.') }}
        </p>
    </div>
</section>

<div class="max-w-2xl mx-auto px-4 py-10">

    @if (session('status'))
        <div class="mb-6 rounded-xl bg-green-50 border border-green-200 text-green-800 px-5 py-4">
            {{ session('status') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-6 rounded-xl bg-red-50 border border-red-200 text-red-700 px-5 py-4 text-sm">
            <ul class="list-disc list-inside space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('contact.store') }}" class="bg-white border border-gray-200 rounded-2xl p-6 md:p-8 space-y-5 shadow-sm">
        @csrf

        <div>
            <label for="type" class="block text-sm font-semibold text-gray-800 mb-1.5">{{ __('Subject area') }}</label>
            <select name="type" id="type"
                    class="w-full rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500">
                @foreach ($types as $key => $label)
                    <option value="{{ $key }}" @selected(old('type', $presetType) === $key)>{{ $label }}</option>
                @endforeach
            </select>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
            <div>
                <label for="name" class="block text-sm font-semibold text-gray-800 mb-1.5">{{ __('Your name') }}</label>
                <input type="text" name="name" id="name" value="{{ old('name', auth()->user()?->name) }}" maxlength="120"
                       class="w-full rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500"
                       placeholder="{{ __('Optional') }}">
            </div>
            <div>
                <label for="email" class="block text-sm font-semibold text-gray-800 mb-1.5">{{ __('Email') }} <span class="text-red-500">*</span></label>
                <input type="email" name="email" id="email" value="{{ old('email', auth()->user()?->email) }}" required maxlength="255"
                       class="w-full rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500"
                       placeholder="you@example.com">
            </div>
        </div>

        <div>
            <label for="subject" class="block text-sm font-semibold text-gray-800 mb-1.5">{{ __('Subject') }}</label>
            <input type="text" name="subject" id="subject" value="{{ old('subject', $presetSubject) }}" maxlength="200"
                   class="w-full rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500"
                   placeholder="{{ __('Optional') }}">
        </div>

        <div>
            <label for="message" class="block text-sm font-semibold text-gray-800 mb-1.5">{{ __('Message') }} <span class="text-red-500">*</span></label>
            <textarea name="message" id="message" rows="6" required minlength="10" maxlength="5000"
                      class="w-full rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500"
                      placeholder="{{ __('How can we help?') }}">{{ old('message') }}</textarea>
        </div>

        <div class="flex items-center justify-between gap-4 pt-2">
            <p class="text-xs text-gray-500">{{ __('We use your email only to reply. No spam.') }}</p>
            <button type="submit"
                    class="inline-flex items-center gap-2 bg-primary-600 hover:bg-primary-700 text-white font-bold px-7 py-3 rounded-lg shadow transition">
                <x-svg-icon name="envelope" class="w-5 h-5" />
                {{ __('Send') }}
            </button>
        </div>
    </form>

    <p class="text-center text-sm text-gray-500 mt-6">
        {{ __('Prefer FAQ?') }}
        <a href="{{ route('faqs.index') }}" class="text-primary-600 font-semibold hover:underline">{{ __('Browse FAQ') }}</a>
    </p>
</div>

@endsection
