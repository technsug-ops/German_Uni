<?php

namespace App\Rules;

use App\Support\MathCaptcha;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Http\Request;

/**
 * Validation rule that pairs the submitted captcha_answer with captcha_key
 * (both injected by the <x-math-captcha /> component).
 *
 * Usage:
 *   'captcha_answer' => ['required', new MathCaptchaRule()],
 */
class MathCaptchaRule implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $key = request()->input('captcha_key');
        if (! MathCaptcha::validate($key, $value)) {
            $fail(__('Bot check failed — please solve the math question again.'));
        }
    }
}
