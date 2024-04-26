<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Tenant;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Section;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\TenantResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\TenantResource\RelationManagers;

class TenantResource extends Resource
{
    protected static ?string $model = Tenant::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Masters';
    protected static ?string $navigationLabel = 'Tenants';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Group::make()   // Grouping
                    ->schema([
                        Section::make() // Section
                            ->schema([
                                Forms\Components\TextInput::make('fname')->label('Full Name')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('eid')->label('EID No')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\DatePicker::make('eidexp')->label('EID Expiry Date')
                                    ->required(),
                                Forms\Components\TextInput::make('nationality')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('email')->label('Email ID')
                                    ->email()
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('mobile')->label('Mobile No')->placeholder('050....')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('visa')->label('Visa Company Name')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('passportno')->label('Passport No')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\DatePicker::make('passexp')->label('Passport Expiry Date')
                                    ->required(),
                                Section::make('Attachemnts')->icon('heroicon-o-paper-clip') // Purchase Details Section
                                    ->schema([
                                        Forms\Components\FileUpload::make('eidfront')->label('Attach EID Front')
                                            ->required()
                                            ->directory('tenantdocs')
                                            ->multiple()
                                            ->image()
                                            ->imageEditor()
                                            ->acceptedFileTypes(['image/*', 'application/pdf']),
                                        Forms\Components\FileUpload::make('eidback')->label('Attach EID Back')
                                            ->directory('tenantdocs')
                                            ->image()
                                            ->imageEditor()
                                            ->acceptedFileTypes(['image/*', 'application/pdf']),
                                        Forms\Components\FileUpload::make('frontpass')->label('Attach Passport Front Page')
                                            ->required()
                                            ->directory('tenantdocs')
                                            ->multiple()
                                            ->image()
                                            ->imageEditor()
                                            ->acceptedFileTypes(['image/*', 'application/pdf']),
                                        Forms\Components\FileUpload::make('backpass')->label('Attach Passport Back Page')
                                            ->directory('tenantdocs')
                                            ->image()
                                            ->imageEditor()
                                            ->acceptedFileTypes(['image/*', 'application/pdf']),
                                        Forms\Components\FileUpload::make('visa_img')->label('Attach Visa Page')
                                            ->required()
                                            ->directory('tenantdocs')
                                            ->multiple()
                                            ->image()
                                            ->imageEditor()
                                            ->acceptedFileTypes(['image/*', 'application/pdf']),
                                    ])->columns(5)
                            ])->columns(3)
                    ])->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('fname')
                    ->searchable(),
                Tables\Columns\TextColumn::make('eid')
                    ->searchable(),
                Tables\Columns\TextColumn::make('eidexp')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('nationality')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('mobile')
                    ->searchable(),
                Tables\Columns\TextColumn::make('visa')
                    ->searchable(),
                Tables\Columns\TextColumn::make('passportno')
                    ->searchable(),
                Tables\Columns\TextColumn::make('passexp')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('eidfront')
                    ->searchable(),
                Tables\Columns\TextColumn::make('eidback')
                    ->searchable(),
                Tables\Columns\TextColumn::make('frontpass')
                    ->searchable(),
                Tables\Columns\TextColumn::make('backpass')
                    ->searchable(),
                Tables\Columns\TextColumn::make('visa_img')
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
            'index' => Pages\ListTenants::route('/'),
            'create' => Pages\CreateTenant::route('/create'),
            'edit' => Pages\EditTenant::route('/{record}/edit'),
        ];
    }
}
