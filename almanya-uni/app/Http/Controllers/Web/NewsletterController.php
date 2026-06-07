<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Mail\NewsletterConfirmation;
use App\Mail\NewsletterWelcome;
use App\Models\Subscriber;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class NewsletterController extends Controller
{
    /**
     * Form submission — email + (opsiyonel) isim alır, doğrulama maili gönderir.
     * AJAX uyumlu (JSON döner) + non-JS fallback (redirect).
     */
    public function subscribe(Request $request): JsonResponse|RedirectResponse
    {
        $data = $request->validate([
            // NOT: 'dns' doğrulaması shared hosting'de canlı DNS sorgusu yapar → yavaş/timeout
            // riskine girer (gateway 500 "Server Error"). RFC format kontrolü yeterli.
            'email'           => ['required', 'email:rfc', 'max:191'],
            'name'            => ['nullable', 'string', 'max:100'],
            'source'          => ['nullable', 'string', 'max:50'],
            'website'         => ['nullable', 'max:0'],  // 🍯 honeypot — bot doldurursa max=0 fail
            'gdpr_consent'    => ['accepted'],
            'captcha_answer'  => ['required', new \App\Rules\MathCaptchaRule()],
        ], [
            'email.email'        => 'Geçerli bir e-posta adresi gir.',
            'website.max'        => 'Spam algılandı.',
            'gdpr_consent.accepted' => 'KVKK onayı vermelisin.',
        ]);

        try {

        $email = strtolower(trim($data['email']));
        $existing = Subscriber::where('email', $email)->first();

        if ($existing && $existing->is_confirmed) {
            return $this->respond($request, false, 'Bu e-posta zaten abone listemizde.', 'already_subscribed');
        }

        if ($existing && $existing->is_pending) {
            // Mevcut pending kaydı — token resend. Mail hatası dış catch'e düşüp
            // "Bir sorun oluştu" göstermesin diye burada da try-catch + queue().
            try {
                Mail::to($existing->email)->send(new NewsletterConfirmation($existing));
            } catch (\Throwable $e) {
                Log::error('Newsletter resend failed', ['email' => $existing->email, 'err' => $e->getMessage()]);
                return $this->respond($request, false, 'Mail gönderilemedi. Birazdan tekrar dene.', 'mail_failed');
            }
            return $this->respond($request, true, 'Doğrulama maili tekrar gönderildi. E-postanı kontrol et.', 'resent');
        }

        // Yeni kayıt (veya unsubscribed olanı yeniden kaydet)
        $sub = Subscriber::updateOrCreate(
            ['email' => $email],
            [
                'name'             => $data['name'] ?? null,
                'language'         => app()->getLocale(),
                'source'           => $data['source'] ?? 'unknown',
                'referrer_url'    => $request->headers->get('referer'),
                'confirmed_at'    => null,
                'unsubscribed_at' => null,
                'unsubscribe_reason' => null,
                'ip_address'      => $request->ip(),
                'user_agent'      => substr((string) $request->userAgent(), 0, 255),
            ]
        );

        // Token'ları regenerate et (eski kayıt ise eski token tutmuyoruz)
        $sub->confirm_token = \Illuminate\Support\Str::random(48);
        $sub->unsubscribe_token = \Illuminate\Support\Str::random(48);
        $sub->save();

        try {
            // SENKRON send() — KAS'ta queue worker YOK (cron yok). queue() ile atılan mail
            // database queue'da birikip HİÇ gönderilmiyordu (UI "gönderildi" der, mail çıkmaz).
            // send() istek anında yollar + gerçek mail hatasını fırlatır (catch yakalar).
            Mail::to($sub->email)->send(new NewsletterConfirmation($sub));
            $sub->update(['last_sent_at' => now()]);
        } catch (\Throwable $e) {
            Log::error('Newsletter mail failed', ['email' => $sub->email, 'err' => $e->getMessage()]);
            // 200 (500 değil): AJAX'ta ham "Server Error" yerine dostça mesaj göster.
            return $this->respond($request, false, 'Mail gönderilemedi. Birazdan tekrar dene.', 'mail_failed');
        }

        return $this->respond($request, true, 'Doğrulama maili gönderildi! Gelen kutunu kontrol et (spam klasörünü de).', 'pending');

        } catch (\Throwable $e) {
            // Beklenmedik hata (DB, vb.) → 500 "Server Error" yerine kontrollü JSON.
            Log::error('Newsletter subscribe failed', ['err' => $e->getMessage()]);
            return $this->respond($request, false, 'Bir sorun oluştu. Birazdan tekrar dene.', 'error');
        }
    }

    public function confirm(string $token): View
    {
        $sub = Subscriber::where('confirm_token', $token)->first();

        if (! $sub) {
            return view('newsletter.result', [
                'success' => false,
                'title'   => 'Doğrulama linki geçersiz',
                'message' => 'Bu link daha önce kullanılmış, süresi geçmiş veya bozulmuş olabilir. Tekrar abone olmayı dene.',
            ]);
        }

        if ($sub->is_confirmed) {
            return view('newsletter.result', [
                'success' => true,
                'title'   => 'Zaten doğrulanmışsın 🎉',
                'message' => 'E-posta adresin daha önce doğrulanmıştı. Bültenleri almaya devam edeceksin.',
            ]);
        }

        if ($sub->unsubscribed_at) {
            // Tekrar aktive et
            $sub->unsubscribed_at = null;
            $sub->unsubscribe_reason = null;
        }

        $sub->confirmed_at = now();
        $sub->save();

        // Welcome email — locale-aware, en değerli 5 tool ile lead-nurture.
        // Queue ile yollar: confirm() response'unu bekletmez, rate-limit kuyruğunda yürür.
        try {
            \Illuminate\Support\Facades\Mail::to($sub->email)->send(new NewsletterWelcome($sub));
            $sub->update(['last_sent_at' => now()]);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Newsletter welcome mail failed', [
                'email' => $sub->email,
                'err'   => $e->getMessage(),
            ]);
        }

        return view('newsletter.result', [
            'success' => true,
            'title'   => 'Aboneliğin onaylandı! 🎉',
            'message' => 'AlmanyaUni e-bülteni artık ' . $sub->email . ' adresine gelecek. Yeni yazılarımız haftada 1-2 yayınlanır.',
            'subscriber' => $sub,
        ]);
    }

    public function unsubscribe(Request $request, string $token): View
    {
        $sub = Subscriber::where('unsubscribe_token', $token)->first();

        if (! $sub) {
            return view('newsletter.result', [
                'success' => false,
                'title'   => 'Link geçersiz',
                'message' => 'Bu unsubscribe linki bulunamadı veya bozulmuş.',
            ]);
        }

        if ($sub->unsubscribed_at) {
            return view('newsletter.result', [
                'success' => true,
                'title'   => 'Zaten abone değilsin',
                'message' => 'Bu e-posta adresi listemizde aktif değil. Bültenler gönderilmiyor.',
            ]);
        }

        $reason = $request->input('reason');

        $sub->unsubscribed_at = now();
        $sub->unsubscribe_reason = $reason ? substr($reason, 0, 255) : 'user_clicked_link';
        $sub->save();

        return view('newsletter.result', [
            'success' => true,
            'title'   => 'Aboneliğin iptal edildi',
            'message' => 'Üzgünüz, gittin diye. Artık ' . $sub->email . ' adresine bülten göndermeyeceğiz. Fikrini değiştirirsen istediğin zaman yeniden abone olabilirsin.',
            'subscriber' => $sub,
        ]);
    }

    private function respond(Request $request, bool $success, string $message, string $status, int $code = 200): JsonResponse|RedirectResponse
    {
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'ok'      => $success,
                'status'  => $status,
                'message' => $message,
            ], $code);
        }

        return back()
            ->with($success ? 'newsletter_success' : 'newsletter_error', $message)
            ->withInput($success ? [] : $request->only(['email', 'name']));
    }
}
