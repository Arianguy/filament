<?php

namespace App\Filament\Resources\PropertyResource\Widgets;

use App\Models\Contract;
use App\Models\Property;
use App\Models\Transaction;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class Propertystats extends BaseWidget
{
    protected function getStats(): array
    {
        $totalSecAmt = number_format(Contract::where('validity', '=', 'Y')->sum('sec_amt'), 2);

        $totalCheqAmt = Transaction::where('cheqstatus', '=', 'PENDING')
            ->whereHas('contract', function ($query) {
                $query->where('validity', 'Y');
            })
            ->sum('cheqamt');
        $formattedTotalCheqAmt = number_format($totalCheqAmt, 0);

        $totalProperties = Property::count();
        $leasedProperties = Property::where('status', 'LEASED')->count();
        $vacantProperties = Property::where('status', 'VACANT')->count();
        $commProperties = Property::where('type', 'Commercial')->count();
        $resProperties = Property::where('type', 'Residential')->count();

        return [
            Stat::make('', "$totalProperties ")
                ->description('Total Properties')
                ->descriptionIcon('heroicon-o-sparkles')
                ->color('success'),

            Stat::make('', "$commProperties ")
                ->description('Commercial')
                ->descriptionIcon('heroicon-o-sparkles')
                ->color('success'),

            Stat::make('', "$resProperties ")
                ->description('Residential')
                ->descriptionIcon('heroicon-o-sparkles')
                ->color('success'),

            Stat::make('', "$leasedProperties ")
                ->description('Leased')
                ->descriptionIcon('heroicon-o-sparkles')
                ->color('success'),

            Stat::make('', "$vacantProperties ")
                ->description('Vacant')
                ->descriptionIcon('heroicon-o-sparkles')
                ->color('success'),

        ];
    }
}
