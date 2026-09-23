@php
    /**
     * Panelden gönderilen tüm mailler (outreach + lead yanıtları) bu şablondan geçer.
     *
     * İki düzen:
     *   personal → sade. Soğuk kurumsal temasta bülten görünümlü mail yanıt oranını
     *              düşürdüğü ve spam filtrelerini tetiklediği için VARSAYILAN budur.
     *   rich     → logolu başlık, görsel, buton, footer. Duyuru/medya kiti için.
     *
     * Kural: her kritik stil satır-içi yazılır. Gmail <style> bloğunu atar,
     * Outlook modern CSS'i yok sayar.
     */
    $isRich  = ($layout ?? 'personal') === 'rich';
    $assets  = rtrim((string) config('app.url'), '/');
    $font    = "-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Arial,Helvetica,sans-serif";
    $sig     = $signature ?? [];
    $sigName = trim((string) ($sig['name'] ?? ''));
    $sigRole = trim((string) ($sig['role'] ?? ''));
    $hasSig  = ($showSignature ?? true) && ($sigName !== '' || $sigRole !== '');
    $imprint = trim((string) ($sig['imprint'] ?? ''));
    $hasCta  = $isRich && filled($ctaUrl ?? null) && filled($ctaLabel ?? null);
@endphp
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="{{ $lang ?? 'tr' }}">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    {{-- Karanlık modda istemcinin renkleri ters çevirmesini engelle: e-postada
         bu çevrim çoğu zaman okunmaz bir sonuç üretir. --}}
    <meta name="color-scheme" content="light only" />
    <meta name="supported-color-schemes" content="light" />
    <title>{{ $subjectLine ?? '' }}</title>
    <style type="text/css">
        body { margin:0; padding:0; width:100% !important; -webkit-text-size-adjust:100%; }
        img { border:0; outline:none; text-decoration:none; -ms-interpolation-mode:bicubic; }
        table { border-collapse:collapse; }
        @media only screen and (max-width:620px) {
            .mailCard { width:100% !important; }
            .pad { padding-left:22px !important; padding-right:22px !important; }
        }
    </style>
</head>
<body style="margin:0;padding:0;background:{{ $isRich ? '#F3F4F6' : '#FFFFFF' }};color:#374151;">

{{-- Gelen kutusunda konunun yanında görünen ön izleme satırı --}}
@if (filled($preheader ?? null))
    <div style="display:none;max-height:0;max-width:0;opacity:0;overflow:hidden;mso-hide:all;font-size:1px;line-height:1px;color:#FFFFFF;">
        {{ $preheader }}
        {!! str_repeat('&#847;&zwnj;&nbsp;', 40) !!}
    </div>
@endif

<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0"
       style="background:{{ $isRich ? '#F3F4F6' : '#FFFFFF' }};">
    <tr>
        <td align="center" style="padding:{{ $isRich ? '32px 16px' : '26px 16px 18px' }};">

            <table role="presentation" class="mailCard" width="600" cellpadding="0" cellspacing="0" border="0"
                   style="width:600px;max-width:600px;background:#FFFFFF;{{ $isRich ? 'border-radius:12px;overflow:hidden;box-shadow:0 2px 8px rgba(0,0,0,0.05);' : '' }}">

                @if ($isRich)
                    {{-- Marka başlığı. Logo görseli engellenirse marka adı metin
                         olarak yerinde kalsın diye wordmark HTML ile yazılıyor. --}}
                    <tr>
                        <td bgcolor="#1A1A1A" class="pad" style="background:#1A1A1A;padding:20px 32px;">
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
                                <tr>
                                    <td width="34" style="width:34px;vertical-align:middle;">
                                        <img src="{{ $assets }}/img/logos/atg-icon.png" width="34" height="34"
                                             alt="ApplyToGerman"
                                             style="display:block;width:34px;height:34px;border-radius:8px;border:0;" />
                                    </td>
                                    <td style="vertical-align:middle;padding-left:12px;font-family:{{ $font }};font-size:19px;font-weight:700;color:#FFFFFF;letter-spacing:-0.2px;">
                                        Apply<span style="color:#F4C430;font-weight:400;">To</span>German
                                    </td>
                                    <td align="right" style="vertical-align:middle;font-family:{{ $font }};font-size:11px;color:#9CA3AF;">
                                        Almanya başvuru rehberi
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    {{-- Alman bayrağı şeridi — görsel değil, tablo hücresi:
                         görseller kapalıyken de görünür. --}}
                    <tr>
                        <td style="font-size:0;line-height:0;">
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
                                <tr>
                                    <td width="33%" height="4" bgcolor="#3A3936" style="height:4px;font-size:0;line-height:0;">&nbsp;</td>
                                    <td width="34%" height="4" bgcolor="#D81E2C" style="height:4px;font-size:0;line-height:0;">&nbsp;</td>
                                    <td width="33%" height="4" bgcolor="#F4C430" style="height:4px;font-size:0;line-height:0;">&nbsp;</td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    @if (filled($heroUrl ?? null))
                        <tr>
                            <td style="font-size:0;line-height:0;">
                                <img src="{{ $heroUrl }}" width="600" alt=""
                                     style="display:block;width:100%;max-width:600px;height:auto;border:0;" />
                            </td>
                        </tr>
                    @endif
                @endif

                {{-- Gövde --}}
                <tr>
                    <td class="pad" style="padding:{{ $isRich ? '30px 32px 6px' : '0 8px 6px' }};font-family:{{ $font }};font-size:15px;line-height:1.65;color:#374151;">
                        {!! $bodyHtml !!}
                    </td>
                </tr>

                @if ($hasCta)
                    <tr>
                        <td class="pad" style="padding:8px 32px 26px;">
                            <table role="presentation" cellpadding="0" cellspacing="0" border="0">
                                <tr>
                                    <td bgcolor="#F4C430" style="background:#F4C430;border-radius:8px;">
                                        <a href="{{ $ctaUrl }}"
                                           style="display:inline-block;padding:13px 28px;font-family:{{ $font }};font-size:15px;font-weight:700;color:#141414;text-decoration:none;border-radius:8px;">
                                            {{ $ctaLabel }} &nbsp;&rarr;
                                        </a>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                @endif

                @if ($hasSig)
                    <tr>
                        <td class="pad" style="padding:{{ $isRich ? '0 32px 28px' : '0 8px 22px' }};">
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0"
                                   style="border-top:1px solid #E5E7EB;">
                                <tr>
                                    <td width="52" style="width:52px;vertical-align:top;padding-top:18px;">
                                        <img src="{{ $assets }}/img/logos/atg-icon.png" width="40" height="40"
                                             alt="ApplyToGerman"
                                             style="display:block;width:40px;height:40px;border-radius:9px;border:0;" />
                                    </td>
                                    <td style="vertical-align:top;padding:18px 0 0 12px;font-family:{{ $font }};font-size:13px;line-height:1.6;color:#6B7280;">
                                        @if ($sigName !== '')
                                            <span style="display:block;font-size:14px;font-weight:700;color:#111827;">{{ $sigName }}</span>
                                        @endif
                                        @if ($sigRole !== '')
                                            <span style="display:block;">{{ $sigRole }}</span>
                                        @endif
                                        <span style="display:block;padding-top:2px;">
                                            <a href="mailto:{{ $fromEmail ?? '' }}" style="color:#1E40AF;text-decoration:none;">{{ $fromEmail ?? '' }}</a>
                                            @if (filled($sig['phone'] ?? null))
                                                &nbsp;·&nbsp; {{ $sig['phone'] }}
                                            @endif
                                        </span>
                                        <span style="display:block;">
                                            <a href="{{ $assets }}" style="color:#1E40AF;text-decoration:none;">{{ $sig['site'] ?? 'applytogerman.com' }}</a>
                                        </span>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                @endif

                @if ($isRich)
                    <tr>
                        <td class="pad" bgcolor="#F9FAFB" style="background:#F9FAFB;border-top:1px solid #E5E7EB;padding:18px 32px;font-family:{{ $font }};font-size:11.5px;line-height:1.6;color:#9CA3AF;">
                            <a href="{{ $assets }}" style="color:#6B7280;text-decoration:none;">applytogerman.com</a>
                            &nbsp;·&nbsp; Bu maile doğrudan yanıt verebilirsiniz.
                            @if ($imprint !== '')
                                <br />{{ $imprint }}
                            @endif
                        </td>
                    </tr>
                @endif

            </table>

            @if (! $isRich && $imprint !== '')
                <table role="presentation" class="mailCard" width="600" cellpadding="0" cellspacing="0" border="0" style="width:600px;max-width:600px;">
                    <tr>
                        <td class="pad" style="padding:4px 8px 0;font-family:{{ $font }};font-size:11px;line-height:1.6;color:#9CA3AF;">
                            {{ $imprint }}
                        </td>
                    </tr>
                </table>
            @endif

        </td>
    </tr>
</table>

</body>
</html>
