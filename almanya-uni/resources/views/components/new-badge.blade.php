@props(['date', 'days' => 14])

@php
    $isNew = $date && (is_object($date) ? $date->diffInDays(now()) : (\Illuminate\Support\Carbon::parse($date)->diffInDays(now()))) <= $days;
@endphp

@if ($isNew)
    <span {{ $attributes->merge(['class' => 'inline-flex items-center gap-1 px-2 py-0.5 text-[10px] font-extrabold uppercase tracking-wider bg-gradient-to-r from-emerald-500 to-emerald-600 text-white rounded-full shadow-sm']) }}
          title="{{ __('Published within the last :n days', ['n' => $days]) }}">
        <x-svg-icon name="sparkles" class="w-3 h-3" />
        {{ __('New') }}
    </span>
@endif
