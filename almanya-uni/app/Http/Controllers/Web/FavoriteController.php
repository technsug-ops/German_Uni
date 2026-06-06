<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Favorite;
use App\Models\Profession;
use App\Models\Program;
use App\Models\University;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    private const TYPE_MAP = [
        'university' => University::class,
        'program'    => Program::class,
        'profession' => Profession::class,
    ];

    /**
     * AJAX endpoint: favorile / favoriden çıkar
     */
    public function toggle(Request $request): JsonResponse
    {
        $data = $request->validate([
            'type' => 'required|string|in:university,program,profession',
            'id'   => 'required|integer',
        ]);

        $modelClass = self::TYPE_MAP[$data['type']];
        $model = $modelClass::findOrFail($data['id']);

        $existing = Favorite::where('user_id', $request->user()->id)
            ->where('favoriteable_type', $modelClass)
            ->where('favoriteable_id', $model->id)
            ->first();

        if ($existing) {
            $existing->delete();
            $action = 'removed';
        } else {
            Favorite::create([
                'user_id'           => $request->user()->id,
                'favoriteable_type' => $modelClass,
                'favoriteable_id'   => $model->id,
            ]);
            $action = 'added';
        }

        // Skor favori sayısına bağlı → cache'i temizle ki puan anında güncellensin.
        $request->user()->clearScoreCache();

        return response()->json([
            'action' => $action,
            'count'  => $model->favorites()->count(),
        ]);
    }

    /**
     * Bir favorinin notunu güncelle (kendi favorin olmalı).
     */
    public function updateNote(Request $request, int $id): JsonResponse
    {
        $data = $request->validate([
            'note' => 'nullable|string|max:1000',
        ]);

        $favorite = Favorite::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        $favorite->update(['note' => $data['note'] ?: null]);

        return response()->json(['ok' => true]);
    }
}
