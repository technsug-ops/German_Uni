<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\FieldOfStudy;
use App\Models\Profession;
use App\Models\Program;
use App\Models\State;
use App\Models\University;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Profil sayfası (sekmeli) — varsayılan: dashboard.
     */
    public function edit(Request $request): View
    {
        $tab = $request->query('tab', 'dashboard');
        $user = $request->user();

        $data = ['user' => $user, 'tab' => $tab];

        switch ($tab) {
            case 'profile':
                $data['fields']  = FieldOfStudy::active()->orderBy('sort_order')->get(['id', 'name_tr', 'icon','name_en','name_de']);
                $data['states']  = State::orderBy('name_de')->get(['id', 'name_tr', 'name_de','name_en','name_de']);
                break;

            case 'favorites':
                // Note: image_url, logo_url eager-load için varsayılan select yeterli
                $data['fav_universities'] = $user->favorites()
                    ->where('favoriteable_type', University::class)
                    ->with(['favoriteable.city:id,name_tr,name_en,name_de,slug','name_en','name_de'])
                    ->latest()
                    ->get();
                $data['fav_programs'] = $user->favorites()
                    ->where('favoriteable_type', Program::class)
                    ->with(['favoriteable.university:id,name_de,slug', 'favoriteable.field:id,name_tr,name_en,name_de,icon,color','name_en','name_de'])
                    ->latest()
                    ->get();
                $data['fav_professions'] = $user->favorites()
                    ->where('favoriteable_type', Profession::class)
                    ->with(['favoriteable'])
                    ->latest()
                    ->get();
                break;

            case 'activity':
                $data['activities'] = $user->activities()
                    ->with('viewable')
                    ->limit(50)
                    ->get();
                break;

            case 'quiz':
                $data['quiz_results'] = $user->quizResults()->limit(20)->get();
                break;

            case 'dashboard':
            default:
                $data['stats'] = [
                    'favorites'  => $user->favorites()->count(),
                    'universities' => $user->favorites()->where('favoriteable_type', University::class)->count(),
                    'programs'   => $user->favorites()->where('favoriteable_type', Program::class)->count(),
                    'professions'=> $user->favorites()->where('favoriteable_type', Profession::class)->count(),
                    'activities' => $user->activities()->count(),
                    'quizzes'    => $user->quizResults()->count(),
                ];
                $data['recent_activities'] = $user->activities()->with('viewable')->limit(5)->get();
                $data['recent_favorites']  = $user->favorites()->with('favoriteable')->latest()->limit(5)->get();
                break;
        }

        return view('profile.show', $data);
    }

    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();

        $user->fill($request->validated());

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        return Redirect::route('profile.edit', ['tab' => 'profile'])->with('status', 'profile-updated');
    }

    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
