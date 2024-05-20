<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use App\Models\CashUpdate;
use Filament\Tables\Table;
use App\Models\Transaction;
use Filament\Resources\Resource;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Section;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\CashUpdateResource\Pages;
use App\Filament\Resources\CashUpdateResource\RelationManagers;

class CashUpdateResource extends Resource
{
    protected static ?string $model = Transaction::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Transations';
    protected static ?string $navigationLabel = 'Cash';
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Group::make()   // Grouping
                    ->schema([
                        Section::make() // Section
                            ->schema([
                                Forms\Components\Select::make('contract_id')
                                    ->relationship('contract', 'name')
                                    ->native(false)
                                    ->searchable()
                                    ->preload()
                                    ->required(),
                                Forms\Components\select::make('type')
                                    ->options([
                                        'CASH' => 'CASH',
                                        'CHEQUE' => 'CHEQUE',
                                    ])
                                    ->default('CHEQUE')
                                    ->disabled(),

                                Section::make('Payment Detail')->icon('heroicon-o-banknotes') // Payment Detail Section
                                    ->schema([
                                        Forms\Components\select::make('cheqbank')
                                            ->label('Cheque Bank Name')
                                            ->options([
                                                'Mashreq Bank' => 'Mashreq Bank',
                                                'Mashreq Neo' => 'Mashreq Neo',
                                                'Emirates NBD' => 'Emirates NBD',
                                                'Emirates Islamic' => 'Emirates Islamic',
                                                'FAB' => 'FAB',
                                                'ADIB' => 'ADIB',
                                                'ADCB' => 'ADCB',
                                                'CBD Bank' => 'CBD',
                                                'Dubai Islamic Bank' => 'Dubai Islamic Bank',
                                                'RAK Bank' => 'RAK Bank',
                                                'Online Transfer' => 'Online Transfer',
                                            ])->native(false)
                                            ->searchable()
                                            ->preload()
                                            ->required(),
                                        Forms\Components\TextInput::make('cheqno')
                                            ->label('Cheque No')
                                            ->required(),
                                        Forms\Components\DatePicker::make('cheqdate')
                                            ->label('Cheque Date')
                                            ->required(),
                                        Forms\Components\TextInput::make('cheqamt')
                                            ->label('Cheque Amount')
                                            ->required()
                                            ->numeric(2),
                                        Forms\Components\select::make('trans_type')
                                            ->label('Transaction Type')
                                            ->options([
                                                'RENT' => 'RENT',
                                                'SECURITY DEPOSIT' => 'SECURITY DEPOSIT',
                                            ])->native(false)
                                            ->required()
                                            ->default('RENT'),
                                        Forms\Components\TextInput::make('narration')
                                            ->label('Narration')
                                            ->required()
                                            ->maxLength(255),
                                        Forms\Components\FileUpload::make('cheq_img')
                                            ->label('Attach Cheque copy')
                                            ->acceptedFileTypes(['image/*', 'application/pdf']),
                                    ])->columns(7)
                            ])->columns(5)
                    ])->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCashUpdates::route('/'),
            'create' => Pages\CreateCashUpdate::route('/create'),
            'edit' => Pages\EditCashUpdate::route('/{record}/edit'),
        ];
    }
}
