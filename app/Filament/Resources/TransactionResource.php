<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\Transaction;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\TransactionResource\Pages;
use App\Filament\Resources\TransactionResource\RelationManagers;

class TransactionResource extends Resource
{
    protected static ?string $model = Transaction::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Transations';
    protected static ?string $navigationLabel = 'Cheques';

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
                                    ->default('CHEQUE'),

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
                                            ->required()
                                            ->directory('Cheques')
                                            ->openable()
                                            ->image()
                                            ->imageEditor()
                                            ->acceptedFileTypes(['image/*', 'application/pdf'])
                                            ->downloadable(),
                                    ])->columns(7)
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
                Action::make('edit_status')
                    ->label('Edit Status')
                    ->icon('heroicon-m-pencil-square')
                    ->form([
                        Select::make('cheqstatus')
                            ->label('Status')
                            ->options([
                                'CLEARED' => 'CLEARED',
                                'BOUNCED' => 'BOUNCED',
                            ]),
                        DatePicker::make('depositdate')
                            ->label('Deposit Date')
                            ->format('d/m/Y') // Format date to DD/MM/YYYY
                            ->required(),
                        TextInput::make('depositac')
                            ->label('Deposit Account')
                            ->default('00919875242')
                            ->required(),
                        Textarea::make('remarks')
                            ->label('Remarks')
                            ->required(),
                    ])
                    ->action(function ($record, $data) {
                        // Update the fields in the database
                        $record->update([
                            'cheqstatus' => $data['cheqstatus'],
                            'depositdate' => \Carbon\Carbon::createFromFormat('d/m/Y', $data['depositdate'])->format('Y-m-d'), // Format to Y-m-d for database
                            'depositac' => $data['depositac'],
                            'remarks' => $data['remarks'],
                        ]);
                        // Send a success notification
                        Notification::make()
                            ->title('Update Successful')
                            ->body('The fields have been updated successfully.')
                            ->success()
                            ->send();
                    }),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
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
