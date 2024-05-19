<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class CashUpdate extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationGroup = 'Transations';
    protected static ?string $navigationLabel = 'Cash';

    protected static string $view = 'filament.pages.cash-update';
}
