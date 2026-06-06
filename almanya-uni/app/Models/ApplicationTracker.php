<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApplicationTracker extends Model
{
    protected $fillable = [
        'user_id', 'steps_completed', 'steps_data',
        'target_intake', 'target_university_id', 'target_degree',
        'started_at', 'last_activity_at', 'email_reminders',
    ];

    protected $casts = [
        'steps_completed'  => 'array',
        'steps_data'       => 'array',
        'started_at'       => 'datetime',
        'last_activity_at' => 'datetime',
        'email_reminders'  => 'boolean',
    ];

    public const STEPS = [
        ['key' => 'eligibility',       'order' => 1, 'emoji' => '🎓', 'title' => 'Eligibility Check',        'desc' => 'Is your diploma recognized in Germany?',                       'tool_route' => 'tools.eligibility-checker', 'duration' => '5 min'],
        ['key' => 'university_match',  'order' => 2, 'emoji' => '🎯', 'title' => 'Find your universities',  'desc' => 'Take the recommendation quiz + browse rankings.',              'tool_route' => 'tools.recommendation',      'duration' => '15 min'],
        ['key' => 'documents',         'order' => 3, 'emoji' => '📋', 'title' => 'Prepare documents',       'desc' => 'Diploma + apostille + sworn translation + motivation letter.', 'tool_route' => 'translation-offices.index', 'tool_label' => 'Find sworn translators', 'duration' => '2-4 weeks'],
        ['key' => 'apply',             'order' => 4, 'emoji' => '📨', 'title' => 'Submit applications',     'desc' => 'Via uni-assist or directly to universities.',                  'tool_route' => 'tools.deadlines',           'duration' => '1-2 weeks'],
        ['key' => 'acceptance',        'order' => 5, 'emoji' => '✅', 'title' => 'Get acceptance letter',   'desc' => 'Verify the offer + plan your next steps.',                     'tool_route' => null,                        'duration' => '4-8 weeks'],
        ['key' => 'financial_proof',   'order' => 6, 'emoji' => '🏦', 'title' => 'Open blocked account',    'desc' => '€11,904 in a Sperrkonto. Compare providers.',                  'tool_route' => 'tools.blocked-account',     'duration' => '1-3 days'],
        ['key' => 'visa',              'order' => 7, 'emoji' => '🛂', 'title' => 'Apply for student visa',  'desc' => 'Book consulate appointment + prepare interview.',              'tool_route' => 'tools.visa-cost',           'duration' => '4-8 weeks'],
        ['key' => 'arrival',           'order' => 8, 'emoji' => '✈️', 'title' => 'Pre-arrival checklist',   'desc' => 'Housing + Anmeldung + bank + insurance.',                      'tool_route' => 'tools.cost-of-living',      'duration' => 'Before departure'],
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function targetUniversity(): BelongsTo
    {
        return $this->belongsTo(University::class, 'target_university_id');
    }

    public function isStepCompleted(string $stepKey): bool
    {
        return in_array($stepKey, (array) $this->steps_completed, true);
    }

    public function completedCount(): int
    {
        return count((array) $this->steps_completed);
    }

    public function progressPercent(): int
    {
        return (int) round($this->completedCount() / count(self::STEPS) * 100);
    }

    public function currentStep(): ?array
    {
        foreach (self::STEPS as $step) {
            if (! $this->isStepCompleted($step['key'])) {
                return $step;
            }
        }
        return null;
    }

    public function markStepCompleted(string $stepKey): void
    {
        $completed = (array) $this->steps_completed;
        if (! in_array($stepKey, $completed, true)) {
            $completed[] = $stepKey;
            $this->steps_completed = $completed;
        }
        // Always (re)stamp completed_at
        $data = (array) $this->steps_data;
        $data[$stepKey]['completed_at'] = now()->toIso8601String();
        $this->steps_data = $data;
        $this->last_activity_at = now();
        $this->save();
    }

    public function unmarkStep(string $stepKey): void
    {
        $completed = array_values(array_diff((array) $this->steps_completed, [$stepKey]));
        $this->steps_completed = $completed;
        $data = (array) $this->steps_data;
        if (isset($data[$stepKey]['completed_at'])) {
            unset($data[$stepKey]['completed_at']);
        }
        $this->steps_data = $data;
        $this->last_activity_at = now();
        $this->save();
    }

    // ════════ Per-step accessors (note / deadline / completed_at) ════════

    public function stepCompletedAt(string $key): ?\Carbon\Carbon
    {
        $iso = data_get((array) $this->steps_data, "{$key}.completed_at");
        return $iso ? \Carbon\Carbon::parse($iso) : null;
    }

    public function stepDeadline(string $key): ?\Carbon\Carbon
    {
        $iso = data_get((array) $this->steps_data, "{$key}.deadline");
        return $iso ? \Carbon\Carbon::parse($iso) : null;
    }

    public function stepNote(string $key): ?string
    {
        return data_get((array) $this->steps_data, "{$key}.note");
    }

    public function setStepData(string $key, ?string $note = null, ?string $deadline = null): void
    {
        $data = (array) $this->steps_data;
        if ($note !== null)     $data[$key]['note']     = $note;
        if ($deadline !== null) $data[$key]['deadline'] = $deadline;
        $this->steps_data = $data;
        $this->last_activity_at = now();
        $this->save();
    }

    /** Days since last activity — null if never started. */
    public function daysSinceActivity(): ?int
    {
        if (! $this->last_activity_at) return null;
        return (int) now()->diffInDays($this->last_activity_at);
    }

    public function nextStep(): ?array
    {
        return $this->currentStep();
    }
}
