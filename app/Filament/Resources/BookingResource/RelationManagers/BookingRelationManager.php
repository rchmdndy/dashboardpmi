<?php

namespace App\Filament\Resources\BookingResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class BookingRelationManager extends RelationManager
{
    protected static string $relationship = 'booking';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('user_email')
            ->description('History Bookings')
            ->columns([
                Tables\Columns\TextColumn::make('user_transaction.order_id')
                    ->searchable(isIndividual: true, isGlobal: true)
                    ->label('Order ID')
                    ->limit(20),
                Tables\Columns\TextColumn::make('user_email')
                    ->searchable(isIndividual: true, isGlobal: false)
                    ->label('Email address')
                    ->limit(20),
                Tables\Columns\TextColumn::make('room.room_name')
                    ->searchable(isIndividual: true, isGlobal: false)
                    ->label('Room Name')
                    ->limit(20),
                Tables\Columns\TextColumn::make('room.roomType.room_type')
                    ->searchable()
                    ->label('Room Type')
                    ->limit(20),
                // ->toggleable(isToggledHiddenByDefault: true)
                Tables\Columns\TextColumn::make('start_date')
                    ->label('Check-in')
                    ->searchable(isIndividual: true, isGlobal: false)
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('end_date')
                    ->label('Check-out')
                    ->searchable(isIndividual: true, isGlobal: false)
                    ->date()
                    ->sortable(),
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
            ->headerActions([
            ])
            ->actions([
            ])
            ->bulkActions([
            ]);
    }
}
