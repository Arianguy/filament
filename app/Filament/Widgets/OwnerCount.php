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
        $totalSecAmt = number_format(contract::where('validity', '=', 'Y')->sum('sec_amt'), 2);
        // $totalActiveContracts = number_format(Contract::where('validity', 'Y')->sum('amount'), 2);

        $totalCheqAmt = Transaction::where('cheqstatus', '=', 'PENDING')
            ->whereHas('contract', function ($query) {
                $query->where('validity', 'Y');
            })
            ->sum('cheqamt');
        $formattedTotalCheqAmt = number_format($totalCheqAmt, 0);

        // $totalSecAmt = Transaction::where('cheqstatus', '=', 'PENDING')
        //     ->whereHas('contract', function ($query) {
        //         $query->where('validity', 'Y');
        //     })
        //     ->sum('cheqamt');
        // $formattedTotalSecqAmt = number_format($totalSecAmt, 0);


        return [
            stat::make('', Owner::count())
                ->description('Owners')
                ->descriptionIcon('heroicon-o-sparkles')
                ->color('Success'),

            stat::make('', Property::count())
                ->description('No of Property')
                ->descriptionIcon('heroicon-o-sparkles')
                ->color('Success'),

            stat::make('', Property::where('status', 'LEASED')->count())
                ->description('No of Leased Properties')
                ->descriptionIcon('heroicon-o-sparkles')
                ->color('Success'),

            stat::make('', Property::where('status', 'VACANT')->count())
                ->description('No of Vacant Properties')
                ->descriptionIcon('heroicon-o-sparkles')
                ->color('Success'),

            stat::make('', Tenant::count())
                ->description('Total Tenants till date')
                ->descriptionIcon('heroicon-o-sparkles')
                ->color('Success'),

            Stat::make(
                '',
                number_format(Contract::where('validity', 'Y')->sum('amount'), 0)
            )->description('Rent Total of Active Contracts')
                ->descriptionIcon('heroicon-o-sparkles'),

            // Stat::make('Pending Cheque Amount', $totalCheqAmt)
            //     ->description('Total cheque amount of pending cheques')
            //     ->descriptionIcon('heroicon-o-currency-dollar'),


            Stat::make('', $formattedTotalCheqAmt)
                ->description('Pending cheque amount of Active contracts')
                ->descriptionIcon('heroicon-o-currency-dollar'),

            Stat::make('', $totalSecAmt)
                ->description('Total Security Deposites')
                ->descriptionIcon('heroicon-o-currency-dollar'),

        ];
    }
}
