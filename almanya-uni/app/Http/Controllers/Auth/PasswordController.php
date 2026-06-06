<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class PasswordController extends Controller
{
    /**
     * Update the user's password.
     */
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validateWithBag('updatePassword', [
            'current_password' => ['required', 'current_password'],
            'password' => [
                'required',
                Password::defaults(),
                'confirmed',
                // Yeni şifre mevcuttan FARKLI olmalı — aynı şifreyle "değiştirme" sessizce
                // başarılı görünüyordu (QA #1, Merve 05.06.2026). Hash karşılaştır.
                function ($attribute, $value, $fail) use ($request) {
                    if (Hash::check($value, $request->user()->password)) {
                        $fail(__('The new password must be different from your current password.'));
                    }
                },
            ],
        ]);

        $request->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        return back()->with('status', 'password-updated');
    }
}
