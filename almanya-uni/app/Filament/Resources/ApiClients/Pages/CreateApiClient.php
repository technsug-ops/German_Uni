<?php

namespace App\Filament\Resources\ApiClients\Pages;

use App\Filament\Resources\ApiClients\ApiClientResource;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateApiClient extends CreateRecord
{
    protected static string $resource = ApiClientResource::class;

    protected function afterCreate(): void
    {
        $token = $this->record->createToken('initial', $this->record->defaultAbilities());

        Notification::make()
            ->title('✅ API client + Token oluşturuldu')
            ->body("Token (BİR KEZ gösterilir, kopyala):\n\n" . $token->plainTextToken)
            ->persistent()
            ->success()
            ->send();
    }
}
