<?php

namespace App\Observers;

use App\Mail\FeedbackReceived;
use App\Models\Feedback;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

class FeedbackObserver
{
    public function created(Feedback $feedback): void
    {
        // Admin email — config'den veya is_admin=true kullanıcıdan
        $adminEmail = config('mail.admin_email')
            ?: User::where('is_admin', true)->orderBy('id')->value('email');

        if (!$adminEmail) return;

        try {
            Mail::to($adminEmail)->send(new FeedbackReceived($feedback));
        } catch (\Throwable $e) {
            \Log::warning('Feedback mail send failed: ' . $e->getMessage());
        }
    }
}
