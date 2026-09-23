{{-- HTML mailin düz metin eşi. Çok parçalı gönderim spam puanını düşürür. --}}
{!! $bodyText !!}
@php
    $sig     = $signature ?? [];
    $sigName = trim((string) ($sig['name'] ?? ''));
    $sigRole = trim((string) ($sig['role'] ?? ''));
    $imprint = trim((string) ($sig['imprint'] ?? ''));
@endphp
@if (($showSignature ?? true) && ($sigName !== '' || $sigRole !== ''))

--
@if ($sigName !== ''){{ $sigName }}
@endif
@if ($sigRole !== ''){{ $sigRole }}
@endif
{{ $fromEmail ?? '' }}@if (filled($sig['phone'] ?? null)) · {{ $sig['phone'] }}@endif

{{ $sig['site'] ?? 'applytogerman.com' }}
@endif
@if ($imprint !== '')

{{ $imprint }}
@endif
