<?php

namespace App\Filament\Resources\ContractResource\Pages;

use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\ContractResource;

class CreateContract extends CreateRecord
{
    protected static string $resource = ContractResource::class;

    protected function getRedirectUrl(): string
    {
        return ContractResource::getUrl('index');
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->title('Yahoo ! New Contract was added.')
            ->success()
            ->body(' ')
            ->duration(4000)
            ->send();
    }
}
