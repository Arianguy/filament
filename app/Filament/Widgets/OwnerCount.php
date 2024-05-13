<?php

namespace App\Filament\Widgets;

use App\Models\Owner;
use App\Models\Tenant;
use App\Models\Contract;
use App\Models\Property;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;


class OwnerCount extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            stat::make('Total Owners', Owner::count())
                ->description('No of Owners')
                ->descriptionIcon('heroicon-o-sparkles')
                ->color('Success'),

            stat::make('Total Properties', Property::count())
                ->description('No of Property')
                ->descriptionIcon('heroicon-o-sparkles')
                ->color('Success'),

            stat::make('Total Tenants', Tenant::count())
                ->description('No of Property')
                ->descriptionIcon('heroicon-o-sparkles')
                ->color('Success'),

            stat::make('Leased Properties', Property::where('status', 'LEASED')->count())
                ->description('No of Property')
                ->descriptionIcon('heroicon-o-sparkles')
                ->color('Success'),

            stat::make('Vacant Properties', Property::where('status', 'VACANT')->count())
                ->description('No of Property')
                ->descriptionIcon('heroicon-o-sparkles')
                ->color('Success'),

            $stat = Stat::make(
                'Total Amount for Valid Contracts',
                number_format(Contract::where('validity', 'YES')->sum('amount'), 2)
            ),


        ];
    }
}
