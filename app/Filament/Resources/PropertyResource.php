<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Property;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Support\Markdown;
use Filament\Actions\ViewAction;
use Filament\Resources\Resource;
use PhpParser\Node\Expr\Ternary;
use Filament\Forms\Components\Group;
use function Laravel\Prompts\select;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Section;
use Filament\Pages\Actions\EditAction;
use Filament\Forms\Components\Fieldset;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Tables\Actions\DeleteBulkAction;
use App\Filament\Resources\PropertyResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\PropertyResource\RelationManagers;
use App\Filament\Resources\PropertyResource\Pages\EditProperty;
use App\Filament\Resources\PropertyResource\Pages\CreateProperty;
use App\Filament\Resources\PropertyResource\Pages\ListProperties;

class PropertyResource extends Resource
{
    protected static ?string $model = Property::class;

    protected static ?string $navigationIcon = 'heroicon-s-building-office-2';
    protected static ?string $navigationGroup = 'Masters';
    protected static ?string $navigationLabel = 'Properties';
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Group::make()   // Grouping
                    ->schema([
                        Section::make() // Section
                            ->schema([
                                TextInput::make('name')->required(),
                                Select::make('owner_id')
                                    ->relationship('owner', 'name')  // Relationship select values
                                    ->required()
                                    ->native(false),

                                Section::make('Purchase Details')->icon('heroicon-o-banknotes') // Purchase Details Section
                                    ->schema([
                                        TextInput::make('title_deed_no')->required()->label('Title Deed No'),
                                        DatePicker::make('purchase_date')->required()->label('Purchase Date'),
                                        select::make('mortgage_status')
                                            ->options([
                                                'YES' => 'YES',
                                                'NO' => 'NO',
                                            ])->native(false)
                                            ->required(),
                                        TextInput::make('purchase_value')
                                            ->required()
                                            ->label('Purchase Value')
                                            ->numeric(),
                                        FileUpload::make('salesdeed')
                                            ->label('Attach Sales deed')
                                            ->required()
                                            ->directory('titledeeds')
                                            //->multiple()
                                            ->image()
                                            ->imageEditor()
                                            ->acceptedFileTypes(['image/*', 'application/pdf'])
                                            ->downloadable(),
                                    ])->columns(5),

                                Section::make('Property Detail')->icon('heroicon-o-identification') // Property Detail Section
                                    ->schema([
                                        select::make('class')
                                            ->options([
                                                'STUDIO' => 'STUDIO',
                                                '1 BHK' => '1 BHK',
                                                '2 BHK' => '2 BHK',
                                                'OFFICE' => 'OFFICE',
                                                'WAREHOUSE' => 'WAREHOUSE'
                                            ])->native(false)
                                            ->required(),
                                        select::make('type')
                                            ->options([
                                                'Residencial' => 'Residencial',
                                                'Comercial' => 'Commercial',
                                            ])->native(false)
                                            ->required(),
                                        TextInput::make('community')->required(),
                                        TextInput::make('bldg_name')->required()->label('Building Name'),
                                        TextInput::make('plot_no')->required()->label('Plot No'),
                                        TextInput::make('bldg_no')->required()->label('Building No'),
                                        TextInput::make('property_no')->required()->label('Property No'),

                                        Fieldset::make('Area Details')
                                            ->schema([
                                                select::make('floor_detail')->label('Floor Details')
                                                    ->options([
                                                        'Basement' => 'Basement',
                                                        'Ground Floor' => 'Ground Floor',
                                                        'First Floor' => 'First Floor',
                                                        'Second Floor' => 'Second Floor',
                                                        'Third Floor' => 'Third Floor',
                                                        'Fourth Floor' => 'Fourth Floor',
                                                        'Ten Floor' => 'Ten Floor',
                                                        'Eleven Floor' => 'Eleven Floor',
                                                    ])->native(false)
                                                    ->required(),
                                                TextInput::make('suite_area')
                                                    ->required()
                                                    ->label('Suite Area')
                                                    ->numeric()
                                                    ->step('0.01'),
                                                TextInput::make('balcony_area')
                                                    ->required()
                                                    ->label('Balcony Area')
                                                    ->numeric()
                                                    ->step('0.01'),
                                                TextInput::make('area_sq_mter')
                                                    ->required()
                                                    ->label('Sq Meter Area')
                                                    ->numeric()
                                                    ->step('0.01'),
                                                TextInput::make('common_area')
                                                    ->required()
                                                    ->label('Common Area')
                                                    ->numeric()
                                                    ->step('0.01'),
                                                TextInput::make('area_sq_feet')
                                                    ->required()
                                                    ->label('Sq Feet Area')
                                                    ->numeric()
                                                    ->step('0.01'),
                                            ])->columns(3),
                                        TextInput::make('dewa_premise_no')->label('DEWA Premise No'),
                                        TextInput::make('dewa_account_no')->label('DEWA Account No'),
                                    ])->columns(4),
                                select::make('status')
                                    ->options([
                                        'VACANT' => 'VACANT',
                                        'LEASED' => 'LEASED',
                                        'SOLD' => 'SOLD',
                                    ])->native(false)
                                    ->required()
                                    ->default('VACANT'),
                            ])->columns(2)
                    ])->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable()->sortable()->Toggleable(),
                TextColumn::make('class')->searchable()->sortable()->Toggleable('true'),
                TextColumn::make('purchase_date')->label('Purchase Date')->date()->sortable(),
                TextColumn::make('purchase_value')->sortable()->Toggleable(),
                TextColumn::make('owner.name')->label('Owner'), // Access owner's name through the relationship
                TextColumn::make('status')
                    ->badge()
                    ->icon(fn (string $state): ?string => match ($state) {
                        'LEASED' => 'heroicon-s-check-badge',
                        'VACANT' => 'heroicon-o-exclamation-circle',
                        'SOLD' => 'heroicon-o-x-circle',
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'LEASED' => 'success',
                        'VACANT' => 'warning',
                        'SOLD' => 'danger',
                    }),
            ])
            ->filters([
                TernaryFilter::make('is_visible')
                    ->label('Visible')
                    ->boolean()
                    ->truelabel('Visible')
                    ->falselabel('Hidden')
                    ->native(false),

                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'Leased' => 'Leased',
                        'Vacant' => 'Vacant',
                        'Sold' => 'Sold'
                    ])
                    ->native(false),

                SelectFilter::make('owner')
                    ->relationship('owner', 'name')
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                ])
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
            'index' => Pages\ListProperties::route('/'),
            'create' => Pages\CreateProperty::route('/create'),
            'edit' => Pages\EditProperty::route('/{record}/edit'),
        ];
    }
}
