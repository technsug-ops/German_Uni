<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Contribution;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ContributionController extends Controller
{
    public function create(): View
    {
        // Kullanıcının kendi katkıları (durum takibi)
        $mine = auth()->check()
            ? Contribution::where('user_id', auth()->id())->latest()->take(10)->get()
            : collect();

        return view('contribute.create', [
            'types'   => Contribution::TYPES,
            'targets' => Contribution::TARGETS,
            'mine'    => $mine,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'type'         => 'required|in:experience,tip,correction',
            'title'        => 'required|string|min:8|max:160',
            'content'      => 'required|string|min:60|max:6000',
            'target_type'  => 'required|in:general,city,university,program',
            'target_label' => 'nullable|string|max:120',
        ]);

        Contribution::create([
            'user_id'      => $request->user()->id,
            'type'         => $data['type'],
            'title'        => $data['title'],
            'content'      => $data['content'],
            'target_type'  => $data['target_type'],
            'target_label' => $data['target_label'] ?? null,
            'status'       => 'pending', // editör onayına gider
        ]);

        return back()->with('status', __('Katkın alındı! Editör onayından sonra yayınlanacak ve profilinde "Topluluk Katkıcısı" rozeti görünecek. Teşekkürler 🙌'));
    }
}
