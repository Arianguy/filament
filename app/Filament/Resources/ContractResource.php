<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Contract;
use App\Models\Property;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Section;
use App\Filament\Resources\ContractResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\ContractResource\RelationManagers;
use Carbon\Carbon;

class ContractHelper
{
    public static function calculateRemainingDays(Contract $contract): string
    {
        $today = Carbon::today();
        $endDate = Carbon::parse($contract->cend);

        if ($endDate->isBefore($today)) {
            return 'Expired';
        } else {
            $diffInDays = abs($endDate->diffInDays($today)); // Use abs() for absolute value
            return number_format($diffInDays);
        }
    }
}
class ContractResource extends Resource
{
    protected static ?string $model = Contract::class;
    protected static ?string $navigationIcon = 'heroicon-o-swatch';
    protected static ?string $navigationGroup = 'Transations';
    protected static ?string $navigationLabel = 'Contracts';

    public static function form(Form $form): Form
    {
        $vacantProperties = Property::where('status', 'Vacant')->pluck('name', 'id')->toArray();

        return $form
            ->schema([
                Group::make()   // Grouping
                    ->schema([
                        Section::make() // Section
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->label('Contract Number')
                                    ->default(function () {
                                        // Generate unique name with "RIY-" prefix and 10-digit timestamp
                                        $timestamp = time();
                                        $uniqueNumber = str_pad(rand(0, pow(10, 6) - 1), 6, '0', STR_PAD_LEFT);
                                        return "RIY-$uniqueNumber";
                                    })
                                    ->readonly()
                                    ->unique(ignoreRecord: true),

                                Section::make() // Section
                                    ->schema([
                                        Forms\Components\Select::make('tenant_id')
                                            ->label('Tenant Name')
                                            ->relationship('Tenant', 'fname')
                                            ->searchable()
                                            ->preload()
                                            ->native(false)
                                            ->required(),
                                        Forms\Components\Select::make('property_id')
                                            ->label('Property Name')
                                            ->relationship('property', 'name')
                                            ->options($vacantProperties) // Use the filtered options array
                                            // ->relationship('Property', 'name')
                                            ->native(false)
                                            ->required(),
                                        Forms\Components\DatePicker::make('cstart')
                                            ->label('Start Date')
                                            ->required(),
                                        Forms\Components\DatePicker::make('cend')
                                            ->label('End Date')
                                            ->required(),
                                        Forms\Components\TextInput::make('amount')
                                            ->label('Rent Amount')
                                            ->required()
                                            ->numeric(2),
                                        Forms\Components\TextInput::make('sec_amt')
                                            ->label('Security Deposite')
                                            ->required()
                                            ->numeric(),
                                        Forms\Components\TextInput::make('ejari')
                                            ->required()
                                            ->maxLength(255),
                                        Forms\Components\select::make('validity')
                                            ->options([
                                                'Y' => 'ACTIVE',
                                                'N' => 'EXPIRED',
                                                'C' => 'CANCELLED',
                                            ])->native(false)
                                            ->required(),
                                        // Forms\Components\TextInput::make('validity')
                                        //     ->required()
                                        //     ->maxLength(255),
                                        Forms\Components\FileUpload::make('contract_img')->label('Attach Contract Copy')
                                            ->required()
                                            ->directory('Contracts')
                                            ->openable()
                                            // ->multiple()
                                            ->image()
                                            ->imageEditor()
                                            ->acceptedFileTypes(['image/*', 'application/pdf'])
                                            ->downloadable(),
                                    ])->columns(2)
                            ])->columns(8)
                    ])->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id'),
                Tables\Columns\TextColumn::make('name')
                    ->label('Contract No')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('tenant.fname')
                    ->sortable()
                    ->limit(30),
                Tables\Columns\TextColumn::make('property.name')
                    ->sortable()
                    ->searchable(),
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
                Tables\Columns\TextColumn::make('ejari'),
                Tables\Columns\TextColumn::make('validity')
                    ->searchable(),
                Tables\Columns\TextColumn::make('days_remaining')
                    ->label('Days Balance')
                    ->getStateUsing(function (Contract $record) {
                        return ContractHelper::calculateRemainingDays($record);
                    })
                    ->badge()
                    ->color(function (string $state): string {
                        if ($state === 'Expired') {
                            return 'danger';
                        } elseif ($state > 30) {
                            return 'success';
                        } else {
                            // Optional: Define a default color for other states
                            return 'primary'; // Example default color
                        }
                    }),
                // Tables\Columns\TextColumn::make('contract_img')
                //     ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])->defaultSort('cend', 'asc')
            //->recordUrl(null)
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
