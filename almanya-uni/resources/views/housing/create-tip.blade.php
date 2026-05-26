@extends('layouts.app')

@section('title', __('Share Experience — Housing') . ' — ' . brand('name'))

@section('content')
<section class="bg-gradient-to-br from-primary-700 via-primary-600 to-primary-800 text-white">
    <div class="max-w-3xl mx-auto px-4 py-8">
        <nav class="text-sm text-primary-100 mb-2">
            <a href="{{ lroute('housing.index') }}" class="hover:text-white">{{ __('Housing') }}</a>
            <span class="mx-2">/</span>
            <a href="{{ lroute('housing.tips') }}" class="hover:text-white">{{ __('Experiences') }}</a>
            <span class="mx-2">/</span>
            <span>{{ __('New') }}</span>
        </nav>
        <h1 class="text-3xl md:text-4xl font-extrabold">✍️ {{ __('Share your experience') }}</h1>
        <p class="text-primary-100 mt-2">{{ __('Tell other students what you went through — help shorten their path.') }}</p>
    </div>
</section>

<div class="max-w-3xl mx-auto px-4 py-8">
    @if ($errors->any())
        <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
            <ul class="text-sm text-red-700 list-disc list-inside">
                @foreach ($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ lroute('housing.tip-store') }}" class="bg-white border border-gray-200 rounded-xl p-6 space-y-5">
        @csrf

        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">{{ __('Title') }} <span class="text-red-500">*</span></label>
            <input type="text" name="title" value="{{ old('title') }}" required maxlength="255"
                   placeholder="{{ __('e.g. Scam I ran into while looking for a WG in Berlin') }}"
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:border-primary-500 focus:outline-none">
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">{{ __('Category') }} <span class="text-red-500">*</span></label>
                <select name="category" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:border-primary-500 focus:outline-none">
                    <option value="">— {{ __('Select') }} —</option>
                    <option value="wg" @selected(old('category') === 'wg')>{{ __('WG (shared flat)') }}</option>
                    <option value="private" @selected(old('category') === 'private')>{{ __('Private apartment') }}</option>
                    <option value="dorm" @selected(old('category') === 'dorm')>{{ __('Dorm (Studierendenwerk)') }}</option>
                    <option value="scam-warning" @selected(old('category') === 'scam-warning')>⚠️ {{ __('Scam warning') }}</option>
                    <option value="landlord-talk" @selected(old('category') === 'landlord-talk')>{{ __('Talk with landlord') }}</option>
                    <option value="other" @selected(old('category') === 'other')>{{ __('Other') }}</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">{{ __('City (optional)') }}</label>
                <select name="city_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:border-primary-500 focus:outline-none">
                    <option value="">— {{ __('General') }} —</option>
                    @foreach ($cities as $c)
                        <option value="{{ $c->id }}" @selected(old('city_id') == $c->id)>{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">{{ __('Experience') }} <span class="text-red-500">*</span></label>
            <textarea name="content" rows="10" required minlength="50" maxlength="5000"
                      placeholder="{{ __('Example openings: \'I applied for a dorm in Berlin in March, the room came in September…\' or \'When applying to WG ads, watch out for…\'') }}"
                      class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:border-primary-500 focus:outline-none">{{ old('content') }}</textarea>
            <p class="text-xs text-gray-500 mt-1">{{ __('Minimum 50, maximum 5000 characters.') }}</p>
        </div>

        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 text-sm text-blue-900">
            ℹ️ {!! __('Your post will be published after <strong>editor approval</strong>. Do not include personal details (phone, address, landlord name).') !!}
        </div>

        <div class="flex justify-end gap-3">
            <a href="{{ lroute('housing.index') }}" class="px-5 py-2.5 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">{{ __('Cancel') }}</a>
            <button type="submit" class="bg-primary-600 hover:bg-primary-700 text-white font-semibold px-6 py-2.5 rounded-lg transition">
                {{ __('Submit') }}
            </button>
        </div>
    </form>
</div>
@endsection
