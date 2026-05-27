@props(['label' => null, 'compact' => false])

@php
    $captcha = \App\Support\MathCaptcha::generate();
    $label = $label ?? __('Bot check: how much is :a + :b?', ['a' => $captcha['a'], 'b' => $captcha['b']]);
    $errorKey = 'captcha_answer';
@endphp

<div class="mt-3" data-math-captcha>
    <input type="hidden" name="captcha_key" value="{{ $captcha['key'] }}">

    @if ($compact)
        <div class="flex items-center gap-2">
            <label for="captcha_answer" class="text-sm font-semibold text-gray-700 whitespace-nowrap">
                🤖 {{ $captcha['question'] }} =
            </label>
            <input
                type="number"
                name="captcha_answer"
                id="captcha_answer"
                inputmode="numeric"
                autocomplete="off"
                required
                min="2" max="18"
                aria-label="{{ $label }}"
                class="w-20 px-3 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:border-primary-500 focus:ring-1 focus:ring-primary-500"
            >
        </div>
    @else
        <label for="captcha_answer" class="block text-sm font-semibold text-gray-700 mb-1">
            🤖 {{ __('Bot check') }}
        </label>
        <div class="flex items-center gap-3">
            <span class="px-3 py-2 bg-gray-100 border border-gray-300 rounded-md font-mono text-base text-gray-900 select-none">
                {{ $captcha['question'] }} = ?
            </span>
            <input
                type="number"
                name="captcha_answer"
                id="captcha_answer"
                inputmode="numeric"
                autocomplete="off"
                required
                min="2" max="18"
                placeholder="{{ __('Your answer') }}"
                aria-label="{{ $label }}"
                class="flex-1 max-w-[140px] px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:border-primary-500 focus:ring-1 focus:ring-primary-500"
            >
        </div>
    @endif

    @error($errorKey)
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>
