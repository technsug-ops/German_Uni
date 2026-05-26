@props(['data' => []])

@php
    $payload = is_array($data) ? array_filter($data, fn ($v) => $v !== null && $v !== '') : $data;
@endphp

@if (!empty($payload))
    @push('meta')
        <script type="application/ld+json">{!! json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}</script>
    @endpush
@endif
