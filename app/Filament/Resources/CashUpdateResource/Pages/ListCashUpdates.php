<?php

namespace App\Filament\Resources\CashUpdateResource\Pages;

use Filament\Actions;
use Illuminate\Database\Eloquent\Builder;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\CashUpdateResource;

class ListCashUpdates extends ListRecords
{
    protected static string $resource = CashUpdateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    protected function getTableQuery(): Builder
    {
        return CashUpdateResource::getEloquentQuery()->where('paytype', 'CASH');
    }
}
