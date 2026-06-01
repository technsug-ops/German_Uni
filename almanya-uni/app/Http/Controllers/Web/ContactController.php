<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Feedback;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Public iletişim / gönüllü formu. mailto: yerine in-app form → Feedback'e yazar
 * (type ile: general/partnership/...), Filament "Geri Bildirimler"de görünür +
 * e-posta bildirimi (mevcut Feedback akışı). Auth gerekmez.
 */
class ContactController extends Controller
{
    public function create(Request $request): View
    {
        $type = $request->query('type');
        if (! array_key_exists($type, Feedback::TYPES)) {
            $type = 'general';
        }

        return view('contact.create', [
            'types' => Feedback::TYPES,
            'presetType' => $type,
            'presetSubject' => (string) $request->query('subject', ''),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'type'    => 'required|string|in:' . implode(',', array_keys(Feedback::TYPES)),
            'name'    => 'nullable|string|max:120',
            'email'   => 'required|email|max:255',
            'subject' => 'nullable|string|max:200',
            'message' => 'required|string|min:10|max:5000',
        ]);

        $user = $request->user();

        Feedback::create([
            'user_id'    => $user?->id,
            'type'       => $data['type'],
            'name'       => ($data['name'] ?? null) ?: $user?->name,
            'email'      => $data['email'],
            'subject'    => $data['subject'] ?? null,
            'message'    => $data['message'],
            'page_url'   => url()->previous(),
            'user_agent' => mb_substr((string) $request->userAgent(), 0, 255),
            'ip_hash'    => md5(($request->ip() ?? '') . config('app.key')),
            'status'     => 'new',
        ]);

        return back()->with('status', __('Thanks! Your message has reached us — we\'ll get back to you by email.'));
    }
}
