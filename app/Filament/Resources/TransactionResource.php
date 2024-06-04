<?php

namespace App\Filament\Resources;

use Carbon\Carbon;
use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\Transaction;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Forms\Components\Grid;
use Filament\Tables\Actions\Action;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Support\Enums\FontWeight;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Infolists\Components\Fieldset;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ImageEntry;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\TransactionResource\Pages;
use App\Filament\Resources\TransactionResource\RelationManagers;


class ChequeHelper
{
    public static function calculateRemainingDays(Transaction $Cheque): string
    {
        $today = Carbon::today();
        $endDate = Carbon::parse($Cheque->cheqdate);

        if ($endDate->isBefore($today)) {
            return 'Past Due';
        } else {
            $diffInDays = abs($endDate->diffInDays($today)); // Use abs() for absolute value
            return number_format($diffInDays);
        }
    }
}

class TransactionResource extends Resource
{
    protected static ?string $model = Transaction::class;

    protected static ?string $navigationIcon = 'heroicon-o-credit-card';
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
                    ->sortable()
                    ->searchable(),
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
                Tables\Columns\TextColumn::make('days_remaining')
                    ->label('Days Balance')
                    ->getStateUsing(function (Transaction $record) {
                        return ChequeHelper::calculateRemainingDays($record);
                    })
                    ->badge()
                    ->color(function (string $state): string {
                        if ($state === 'Past Due') {
                            return 'danger';
                        } elseif ($state > 30) {
                            return 'success';
                        } else {
                            // Optional: Define a default color for other states
                            return 'primary'; // Example default color
                        }
                    }),
                Tables\Columns\TextColumn::make('trans_type')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('narration')
                    ->searchable(),
                //Tables\Columns\TextColumn::make('cheq_img')
                //   ->searchable(),
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
                SelectFilter::make('contract')
                    ->relationship('contract', 'name')
                    ->searchable()
                    ->preload()
                    ->indicator('Contract '),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Action::make('edit_status')
                    ->label('Clear Cheque')
                    ->icon('heroicon-m-pencil-square')
                    ->form([
                        Grid::make(6)
                            ->schema([
                                Select::make('cheqstatus')
                                    ->label('Status')
                                    ->required()
                                    ->options([
                                        'CLEARED' => 'CLEARED',
                                        'BOUNCED' => 'BOUNCED',
                                    ])
                                    ->columnSpan(2),
                                DatePicker::make('depositdate')
                                    ->label('Deposit Date')
                                    ->format('d/m/Y') // Format date to DD/MM/YYYY
                                    ->required()
                                    ->columnSpan(2),
                                TextInput::make('depositac')
                                    ->label('Deposit Account')
                                    ->default('00919875242')
                                    ->required()
                                    ->columnSpan(2),
                                Textarea::make('remarks')
                                    ->label('Remarks')
                                    ->required()
                                    ->columnSpan(6),
                            ]),
                    ])
                    ->action(function ($record, $data) {
                        // Update the fields in the database
                        $record->update([
                            'cheqstatus' => $data['cheqstatus'],
                            'depositdate' => Carbon::createFromFormat('d/m/Y', $data['depositdate'])->format('Y-m-d'), // Format to Y-m-d for database
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

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Fieldset::make('')
                    ->schema([
                        TextEntry::make('contract.name')
                            ->label('Contract No:'),
                        TextEntry::make('contract.property.name')
                            ->label('Property Name:'),
                        TextEntry::make('contract.property.owner.name')
                            ->label('Property Owner:'),
                        // TextEntry::make('break')
                        //     ->label(' ') // An empty label or any text can act as a break
                        //     ->columnSpanFull(), // Ensures it takes a full width break
                        TextEntry::make('contract.tenant.fname')
                            ->label('Tenant Name: ')
                            ->weight(FontWeight::Bold),
                        TextEntry::make('contract.tenant.mobile')
                            ->label('Tenant Mobile: ')
                            ->weight(FontWeight::Bold),
                        TextEntry::make('contract.tenant.email')
                            ->label('Tenant Email: ')
                            ->weight(FontWeight::Bold),
                        TextEntry::make('cheqdate')
                            ->label('Cheque Date : ')
                            ->dateTime('d-M-Y')
                            ->weight(FontWeight::Bold),
                        //    ->color('gray'),
                        TextEntry::make('cheqamt')
                            ->label('Cheque Amount :')
                            ->numeric(2)
                            ->money('AED')
                            ->weight(FontWeight::Bold),
                        TextEntry::make('cheqbank')
                            ->label('Bank:')
                            ->weight(FontWeight::Bold),

                        TextEntry::make('days_remaining')
                            ->label('Days Balance')
                            ->getStateUsing(function (Transaction $record) {
                                return ChequeHelper::calculateRemainingDays($record);
                            })
                            ->badge()
                            ->color(function (string $state): string {
                                if ($state === 'Past Due') {
                                    return 'danger';
                                } elseif ($state > 30) {
                                    return 'success';
                                } else {
                                    // Optional: Define a default color for other states
                                    return 'primary'; // Example default color
                                }
                            }),
                        TextEntry::make('cheqstatus')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'CLEARED' => 'success',
                                'BOUNCED' => 'danger',
                                'PENDING' => 'info',
                            }),

                        TextEntry::make('narration')
                            ->label('Narration:')
                            ->weight(FontWeight::Thin),
                        ImageEntry::make('cheq_img')
                            ->width(600)
                            ->height(300)
                            // ->size(500)
                            ->columnSpan(2),
                    ])->columns(3),
            ])->columns(3);
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
