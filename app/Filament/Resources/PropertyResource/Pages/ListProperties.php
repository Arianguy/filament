<?php

namespace App\Filament\Resources\PropertyResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\PropertyResource;
use App\Filament\Resources\PropertyResource\Widgets\Propertystats;

class ListProperties extends ListRecords
{
    protected static string $resource = PropertyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            Propertystats::class
        ];
    }
}
