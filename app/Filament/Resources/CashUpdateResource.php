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

    public static function query(): Builder
    {
        return parent::query()->where('paytype', 'CASH');
    }

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
                                    ->label('Contract No')
                                    ->native(false)
                                    ->searchable()
                                    ->preload()
                                    ->required(),
                                Forms\Components\Hidden::make('paytype')
                                    ->default('CASH'),

                                Section::make('Cash Receipt Detail')->icon('heroicon-o-banknotes') // Payment Detail Section
                                    ->schema([
                                        Forms\Components\DatePicker::make('cheqdate')
                                            ->label('Cash Received Date')
                                            ->required(),
                                        Forms\Components\TextInput::make('cheqamt')
                                            ->label('Cash Amount')
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
                                    ])->columns(5)
                            ])->columns(5)
                    ])->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table

            ->columns([
                Tables\Columns\TextColumn::make('contract.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('paytype')
                    ->label('Type')
                    ->searchable(),
                Tables\Columns\TextColumn::make('cheqdate')
                    ->label('Receipt Date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('cheqamt')
                    ->label('Cash Amount')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('trans_type')
                    ->label('Towards')
                    ->searchable(),
                Tables\Columns\TextColumn::make('narration')
                    ->label('Narration')
                    ->searchable(),
                Tables\Columns\TextColumn::make('cheq_img')
                    ->label('Attachments')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
