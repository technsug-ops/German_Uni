<?php

namespace App\Observers;

use App\Models\Program;
use App\Models\University;
use App\Services\WebhookDispatcher;

class WebhookEventObserver
{
    public function __construct(private WebhookDispatcher $dispatcher) {}

    public function created($model): void
    {
        $this->fire($model, 'created');
    }

    public function updated($model): void
    {
        $this->fire($model, 'updated');
    }

    public function deleted($model): void
    {
        $this->fire($model, 'deleted');
    }

    private function fire($model, string $verb): void
    {
        $event = match (true) {
            $model instanceof University => 'university.' . $verb,
            $model instanceof Program => 'program.' . $verb,
            default => null,
        };

        if (!$event) {
            return;
        }

        $this->dispatcher->dispatch($event, [
            'id' => $model->id,
            'slug' => $model->slug ?? null,
            'name_de' => $model->name_de ?? null,
            'changes' => $verb === 'updated' ? array_keys($model->getChanges()) : null,
        ]);
    }
}
