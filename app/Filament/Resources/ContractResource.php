<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Contract;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Section;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\ContractResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\ContractResource\RelationManagers;

class ContractResource extends Resource
{
    protected static ?string $model = Contract::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Transations';
    protected static ?string $navigationLabel = 'Contracts';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Group::make()   // Grouping
                    ->schema([
                        Section::make() // Section
                            ->schema([
                                Forms\Components\Select::make('tenant_id')
                                    ->relationship('Tenant', 'fname')
                                    ->native(false)
                                    ->required(),
                                Forms\Components\TextInput::make('property_id')
                                    ->required()
                                    ->numeric(),
                                Forms\Components\DatePicker::make('cstart')
                                    ->required(),
                                Forms\Components\DatePicker::make('cend')
                                    ->required(),
                                Forms\Components\TextInput::make('amount')
                                    ->required()
                                    ->numeric(),
                                Forms\Components\TextInput::make('sec_amt')
                                    ->required()
                                    ->numeric(),
                                Forms\Components\TextInput::make('ejari')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('validity')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\FileUpload::make('contract_img')->label('Attach Contract Copy')
                                    ->acceptedFileTypes(['image/*', 'application/pdf'])
                                    ->required()
                                    ->multiple(),
                            ])->columns(2)
                    ])->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('tenant.id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('property_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('cstart')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('cend')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('amount')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('sec_amt')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('ejari')
                    ->searchable(),
                Tables\Columns\TextColumn::make('validity')
                    ->searchable(),
                Tables\Columns\TextColumn::make('contract_img')
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
            'index' => Pages\ListContracts::route('/'),
            'create' => Pages\CreateContract::route('/create'),
            'edit' => Pages\EditContract::route('/{record}/edit'),
        ];
    }
}
