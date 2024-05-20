<?php

namespace App\Filament\Resources\CashUpdateResource\Pages;

use App\Filament\Resources\CashUpdateResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCashUpdates extends ListRecords
{
    protected static string $resource = CashUpdateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
