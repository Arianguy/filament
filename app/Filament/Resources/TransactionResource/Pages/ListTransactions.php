<?php

namespace App\Filament\Resources\TransactionResource\Pages;

use Filament\Actions;
use Filament\Forms\Components\Tabs;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Filament\Resources\Pages\ListRecords\Tab;
use App\Filament\Resources\TransactionResource;
use App\Models\Transaction;
use Illuminate\Database\Query\Builder as QueryBuilder;


class ListTransactions extends ListRecords
{
    protected static string $resource = TransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    protected function getTableQuery(): Builder
    {
        return TransactionResource::getEloquentQuery()->where('paytype', 'CHEQUE');
    }

    public function getTabs(): array
    {
        return [
            'All' => Tab::make('All'),
            'Cleared' => Tab::make()
                ///->icon('heroicon-m-user-group')
                ->modifyQueryUsing(function ($query) {
                    $query->where('cheqstatus', 'CLEARED');
                }),
            'Upcoming' => Tab::make()
                ->modifyQueryUsing(function ($query) {
                    $query->where('cheqstatus', 'PENDING');
                })
                ->badge(Transaction::query()->where('cheqstatus', 'PENDING')->count())
                ->badgeColor('success'),
        ];
    }

    public function getDefaultActiveTab(): string | int | null
    {
        return 'Upcoming';
    }
}
