<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\Transaction;
use Forms\Components\Select;
use Filament\Resources\Resource;
use Filament\Forms\Components\View;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Fieldset;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\TransactionResource\Pages;
use App\Filament\Resources\TransactionResource\RelationManagers;

class TransactionResource extends Resource
{
    protected static ?string $model = Transaction::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Transations';
    protected static ?string $navigationLabel = 'Cash/Cheques';

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
                                            ->label('Bank Name')
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
                                            ->label('Amount')
                                            ->required()
                                            ->numeric(2),
                                        Forms\Components\select::make('status')
                                            ->options([
                                                'RENT' => 'RENT',
                                                'SECURITY DEPOSIT' => 'SECURITY DEPOSIT',
                                            ])->native(false)
                                            ->required()
                                            ->default('RENT'),
                                        Forms\Components\TextInput::make('narration')
                                            ->label('Reason')
                                            ->required()
                                            ->maxLength(255),
                                        Forms\Components\FileUpload::make('cheq_img')
                                            ->label('Attach Cheque')
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
                Tables\Columns\TextColumn::make('contract.id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('paytype')
                    ->searchable(),
                Tables\Columns\TextColumn::make('cheqno')
                    ->searchable(),
                Tables\Columns\TextColumn::make('cheqbank')
                    ->searchable(),
                Tables\Columns\TextColumn::make('cheqamt')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('cheqdate')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('trans_type')
                    ->searchable(),
                Tables\Columns\TextColumn::make('narration')
                    ->searchable(),
                Tables\Columns\TextColumn::make('depositdate')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('cheqstatus')
                    ->searchable(),
                Tables\Columns\TextColumn::make('depositac')
                    ->searchable(),
                Tables\Columns\TextColumn::make('remarks')
                    ->searchable(),
                Tables\Columns\TextColumn::make('cheq_img')
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
            'index' => Pages\ListTransactions::route('/'),
            'create' => Pages\CreateTransaction::route('/create'),
            'edit' => Pages\EditTransaction::route('/{record}/edit'),
        ];
    }
}
