@php $brandName = $brandName ?? brand('name'); @endphp
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>{{ $brandName }} — Yeni Feedback</title>
</head>
<body style="margin:0;padding:0;background:#f3f4f6;font-family:-apple-system,Segoe UI,Roboto,Helvetica,sans-serif;color:#111827;line-height:1.6;">

<table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="background:#f3f4f6;">
    <tr><td align="center" style="padding:24px 12px;">
        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="600" style="max-width:600px;background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 4px 12px rgba(0,0,0,0.05);">

            <tr><td style="background:linear-gradient(135deg,#1e40af 0%,#3b82f6 100%);padding:24px;color:#fff;">
                <h1 style="margin:0;font-size:22px;font-weight:700;">💬 Yeni Geri Bildirim</h1>
                <p style="margin:6px 0 0;font-size:13px;color:#bfdbfe;">
                    {{ \App\Models\Feedback::TYPES[$feedback->type] ?? $feedback->type }}
                    · {{ $feedback->created_at->format('d.m.Y H:i') }}
                </p>
            </td></tr>

            <tr><td style="padding:24px;">
                <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="font-size:14px;">
                    @if ($feedback->name || $feedback->email)
                        <tr><td style="padding:6px 0;color:#6b7280;width:100px;">Gönderen</td>
                            <td style="padding:6px 0;font-weight:600;">
                                {{ $feedback->name ?: 'Anonim' }}
                                @if ($feedback->email)
                                    <a href="mailto:{{ $feedback->email }}" style="color:#2563eb;font-weight:normal;">&lt;{{ $feedback->email }}&gt;</a>
                                @endif
                            </td></tr>
                    @endif
                    @if ($feedback->page_url)
                        <tr><td style="padding:6px 0;color:#6b7280;">Sayfa</td>
                            <td style="padding:6px 0;">
                                <a href="{{ url($feedback->page_url) }}" style="color:#2563eb;">{{ $feedback->page_url }}</a>
                            </td></tr>
                    @endif
                    <tr><td style="padding:6px 0;color:#6b7280;vertical-align:top;">Mesaj</td>
                        <td style="padding:6px 0;white-space:pre-wrap;">{{ $feedback->message }}</td></tr>
                </table>
            </td></tr>

            <tr><td style="padding:0 24px 24px;">
                <a href="{{ url('/admin/feedbacks/' . $feedback->id . '/edit') }}"
                   style="display:inline-block;padding:10px 20px;background:#1e40af;color:#fff;text-decoration:none;border-radius:6px;font-weight:600;font-size:14px;">
                    Admin panelinde aç →
                </a>
            </td></tr>

            <tr><td style="padding:16px 24px;background:#f9fafb;border-top:1px solid #e5e7eb;font-size:11px;color:#6b7280;">
                IP hash: <code>{{ $feedback->ip_hash }}</code>
                @if ($feedback->user_agent)
                    · UA: {{ \Illuminate\Support\Str::limit($feedback->user_agent, 80) }}
                @endif
            </td></tr>

        </table>
    </td></tr>
</table>

</body>
</html>
