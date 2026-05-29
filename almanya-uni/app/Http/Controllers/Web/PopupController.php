<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Popup;
use Illuminate\Http\JsonResponse;

class PopupController extends Controller
{
    public function trackView(Popup $popup): JsonResponse
    {
        $popup->increment('view_count');
        return response()->json(['ok' => true]);
    }

    public function trackClick(Popup $popup): JsonResponse
    {
        $popup->increment('click_count');
        return response()->json(['ok' => true]);
    }

    public function trackDismiss(Popup $popup): JsonResponse
    {
        $popup->increment('dismiss_count');
        return response()->json(['ok' => true]);
    }
}
