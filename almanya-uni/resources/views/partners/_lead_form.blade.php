{{-- İlgi formu → Lead. $kind, $item --}}
<div class="border-t border-gray-100 pt-4">
    <h3 class="font-bold text-gray-900 mb-1">{{ __('Interested? Get in touch') }}</h3>
    <p class="text-xs text-gray-500 mb-3">{{ __('Leave your details — the provider (or our team) will reach out to you.') }}</p>

    @if (session('lead_success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm rounded-lg p-3">
            ✅ {{ __('Thanks! Your request has been received.') }}
        </div>
    @else
        <form method="POST" action="{{ route('partner-lead.store') }}" class="space-y-2.5">
            @csrf
            <input type="hidden" name="source_type" value="{{ $kind }}">
            <input type="hidden" name="source_id" value="{{ $item->id }}">
            <input type="hidden" name="source_name" value="{{ $item->name }}">
            {{-- honeypot --}}
            <input type="text" name="website" tabindex="-1" autocomplete="off" class="hidden" aria-hidden="true">

            <input type="text" name="name" value="{{ old('name') }}" placeholder="{{ __('Your name') }}"
                   class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500">
            <input type="email" name="email" value="{{ old('email') }}" placeholder="{{ __('Email') }}"
                   class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500">
            <input type="text" name="phone" value="{{ old('phone') }}" placeholder="{{ __('Phone (optional)') }}"
                   class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500">
            <textarea name="message" rows="3" placeholder="{{ __('Your message (optional)') }}"
                      class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('message') }}</textarea>

            @error('email')
                <p class="text-xs text-red-600">{{ $message }}</p>
            @enderror

            <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-500 text-white font-semibold rounded-lg px-4 py-2.5 text-sm">
                {{ __('Send request') }}
            </button>
            <p class="text-[11px] text-gray-400 text-center">{{ __('We respect your privacy — your info is shared only with the relevant provider.') }}</p>
        </form>
    @endif
</div>
