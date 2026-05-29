<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\ApplicationTracker;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ApplicationTrackerController extends Controller
{
    public function show(Request $request): View
    {
        return view('journey.show', [
            'tracker'   => $this->resolveTracker($request),
            'steps'     => ApplicationTracker::STEPS,
            'isGuest'   => ! $request->user(),
        ]);
    }

    public function toggle(Request $request, string $stepKey): RedirectResponse
    {
        $validStep = collect(ApplicationTracker::STEPS)->firstWhere('key', $stepKey);
        abort_unless($validStep, 404);

        $user = $request->user();

        if ($user) {
            // Logged-in: persist to DB
            $tracker = $user->applicationTracker ?? ApplicationTracker::create([
                'user_id' => $user->id,
                'started_at' => now(),
                'steps_completed' => [],
            ]);
            if ($tracker->isStepCompleted($stepKey)) {
                $tracker->unmarkStep($stepKey);
            } else {
                $tracker->markStepCompleted($stepKey);
            }
        } else {
            // Guest: session-only state
            $steps = (array) $request->session()->get('journey.steps', []);
            if (in_array($stepKey, $steps, true)) {
                $steps = array_values(array_diff($steps, [$stepKey]));
            } else {
                $steps[] = $stepKey;
            }
            $request->session()->put('journey.steps', $steps);
        }

        return back()->with('status', __('Journey updated.'));
    }

    /** Per-step note + deadline persist (auth only — guest gets soft prompt). */
    public function updateStep(Request $request, string $stepKey): RedirectResponse
    {
        $validStep = collect(ApplicationTracker::STEPS)->firstWhere('key', $stepKey);
        abort_unless($validStep, 404);

        $data = $request->validate([
            'note'     => 'nullable|string|max:2000',
            'deadline' => 'nullable|date|after_or_equal:today',
        ]);

        $user = $request->user();
        if (! $user) {
            return back()->with('error', __('Login required to save notes and deadlines.'));
        }

        $tracker = $user->applicationTracker ?? ApplicationTracker::create([
            'user_id'         => $user->id,
            'started_at'      => now(),
            'steps_completed' => [],
        ]);

        $tracker->setStepData(
            $stepKey,
            $data['note']     ?? null,
            isset($data['deadline']) ? \Carbon\Carbon::parse($data['deadline'])->toDateString() : null
        );

        return back()->with('status', __('Step saved.'));
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'target_intake'   => 'nullable|string|max:20',
            'target_degree'   => 'nullable|in:bachelor,master,phd',
            'email_reminders' => 'sometimes|boolean',
        ]);

        $user = $request->user();

        if ($user) {
            $tracker = $user->applicationTracker ?? new ApplicationTracker(['user_id' => $user->id]);
            $tracker->fill(array_filter($data, fn ($v) => $v !== null && $v !== ''));
            $tracker->last_activity_at = now();
            $tracker->save();
        } else {
            foreach (['target_intake', 'target_degree'] as $k) {
                if (! empty($data[$k])) {
                    $request->session()->put('journey.' . $k, $data[$k]);
                }
            }
        }

        return back()->with('status', __('Preferences saved.'));
    }

    /**
     * Logged-in → DB tracker; guest → session-backed (non-persisted) model.
     */
    private function resolveTracker(Request $request): ApplicationTracker
    {
        $user = $request->user();

        if ($user) {
            return $user->applicationTracker ?? ApplicationTracker::create([
                'user_id' => $user->id,
                'started_at' => now(),
                'last_activity_at' => now(),
                'steps_completed' => [],
            ]);
        }

        // Guest: non-persisted model instance, session'dan state oku
        return new ApplicationTracker([
            'steps_completed' => $request->session()->get('journey.steps', []),
            'target_intake'   => $request->session()->get('journey.target_intake'),
            'target_degree'   => $request->session()->get('journey.target_degree'),
            'started_at'      => $request->session()->get('journey.started_at') ?? now(),
        ]);
    }
}
