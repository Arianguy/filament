<?php

namespace App\Filament\Resources\ContractResource\Pages;

use Filament\Actions;
use App\Models\Contract;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\ContractResource;
use Filament\Resources\Pages\ListRecords\Tab;

class ListContracts extends ListRecords
{
    protected static string $resource = ContractResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'All' => Tab::make('All'),
            'Archived' => Tab::make()
                ///->icon('heroicon-m-user-group')
                ->modifyQueryUsing(function ($query) {
                    $query->where('validity', 'N');
                })
                ->badge(Contract::query()->where('validity', 'N')->count())
                ->badgeColor('info'),
            'Valid' => Tab::make()
                ->modifyQueryUsing(function ($query) {
                    $query->where('validity', 'Y');
                })
                ->badge(Contract::query()->where('validity', 'Y')->count())
                ->badgeColor('success'),
        ];
    }

    public function getDefaultActiveTab(): string | int | null
    {
        return 'Valid';
    }
}
