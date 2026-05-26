<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Feedback;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FeedbackController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'type'    => 'required|string|in:' . implode(',', array_keys(Feedback::TYPES)),
            'name'    => 'nullable|string|max:120',
            'email'   => 'nullable|email|max:255',
            'subject' => 'nullable|string|max:200',
            'message' => 'required|string|min:5|max:5000',
            'page_url' => 'nullable|string|max:500',
        ]);

        $user = $request->user();
        $data['user_id'] = $user?->id;
        if (!$data['name'] && $user) $data['name'] = $user->name;
        if (!$data['email'] && $user) $data['email'] = $user->email;
        $data['user_agent'] = mb_substr((string) $request->userAgent(), 0, 255);
        $data['ip_hash'] = md5(($request->ip() ?? '') . config('app.key'));

        Feedback::create($data);

        return response()->json([
            'ok' => true,
            'message' => 'Teşekkürler! Mesajın bize ulaştı.',
        ]);
    }
}
