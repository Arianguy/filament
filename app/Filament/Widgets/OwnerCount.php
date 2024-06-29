<?php

namespace App\Filament\Widgets;

use App\Models\Owner;
use App\Models\Tenant;
use App\Models\Contract;
use App\Models\Property;
use App\Models\Transaction;
use Illuminate\Database\Eloquent\Builder;
use Filament\Widgets\StatsOverviewWidget\Card;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class OwnerCount extends BaseWidget
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

        return [
            Stat::make('', Owner::count())
                ->description('Owners')
                ->descriptionIcon('heroicon-o-sparkles')
                ->color('success'),

            Stat::make('', "$totalProperties Properties: $leasedProperties Leased, $vacantProperties Vacant")
                ->description('Property Status')
                ->descriptionIcon('heroicon-o-sparkles')
                ->color('success'),

            Stat::make('', Tenant::count())
                ->description('Total Tenants till date')
                ->descriptionIcon('heroicon-o-sparkles')
                ->color('success'),

            Stat::make('', number_format(Contract::where('validity', 'Y')->sum('amount'), 0))
                ->description('Rent Total of Active Contracts')
                ->descriptionIcon('heroicon-o-sparkles'),

            Stat::make('', $formattedTotalCheqAmt)
                ->description('Pending cheque amount of Active contracts')
                ->descriptionIcon('heroicon-o-currency-dollar'),

            Stat::make('', $totalSecAmt)
                ->description('Total Security Deposites')
                ->descriptionIcon('heroicon-o-currency-dollar'),
        ];
    }
}
