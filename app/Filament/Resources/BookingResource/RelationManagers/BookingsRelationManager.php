<?php

namespace App\Filament\Resources\BookingResource\RelationManagers;

use App\Rules\RoomAvailable;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class BookingsRelationManager extends RelationManager
{
    protected static string $relationship = 'bookings';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make('Booking')
                            ->description('Booking Information')
                            ->schema([
                                Forms\Components\Select::make('user_email')
                                    ->searchable()
                                    ->required()
                                    ->preload()
                                    ->relationship('user', 'email')
                                    ->placeholder('Input Email'),
                                Forms\Components\Select::make('user_transaction_id')
                                    ->required()
                                    ->label('Order ID')
                                    ->searchable()
                                    ->preload()
                                    ->relationship('user_transaction', 'order_id')
                                    ->placeholder('Select Order ID'),
                                Forms\Components\Select::make('room_id')
                                    ->placeholder('Select Room')
                                    ->searchable()
                                    ->preload()
                                    ->relationship('room', 'room_name')
                                    ->required()
                                    ->columnSpanFull(),
                            ])
                            ->columns(2),
                    ]),

                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make('Booking Date')
                            ->description('Date Information')
                            ->schema([
                                Forms\Components\DatePicker::make('start_date')
                                    ->label('Check In')
                                    ->rules(function (callable $get) {
                                        $roomId = $get('room_id');
                                        $startDate = $get('start_date');
                                        $endDate = $get('end_date');

                                        // dd($roomId, $startDate, $endDate);
                                        return [
                                            new RoomAvailable($roomId, $startDate, $endDate),
                                        ];
                                    })
                                    ->required(),
                                Forms\Components\DatePicker::make('end_date')
                                    ->label('Check Out')
                                    ->required(),
                            ])
                            ->columns(1),

                    ]),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->defaultPaginationPageOption(5)
            ->recordTitleAttribute('user_transaction_id')
            ->columns([
                Tables\Columns\TextColumn::make('user_transaction.order_id')
                    ->limit(30),
                Tables\Columns\TextColumn::make('user_email')
                    ->label('Email Address')
                    ->limit(20),
                Tables\Columns\TextColumn::make('room.room_name')
                    ->label('Room Book'),
                Tables\Columns\TextColumn::make('room.roomType.room_type')
                    ->label('Room Type'),
                Tables\Columns\TextColumn::make('start_date')
                    ->label('Check In'),
                Tables\Columns\TextColumn::make('end_date')
                    ->label('Check Out'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->color('indigo'),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                // Tables\Actions\BulkActionGroup::make([
                //     Tables\Actions\DeleteBulkAction::make(),
                // ]),
            ]);
    }
}
