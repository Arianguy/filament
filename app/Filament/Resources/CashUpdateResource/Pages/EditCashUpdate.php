<?php

namespace App\Filament\Resources\CashUpdateResource\Pages;

use App\Filament\Resources\CashUpdateResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCashUpdate extends EditRecord
{
    protected static string $resource = CashUpdateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
