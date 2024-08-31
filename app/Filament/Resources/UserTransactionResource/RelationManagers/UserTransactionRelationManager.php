<?php

namespace App\Filament\Resources\UserTransactionResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class UserTransactionRelationManager extends RelationManager
{
    protected static string $relationship = 'user_transaction';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('user_email')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('user_email')
            ->columns([
                Tables\Columns\TextColumn::make('user_email'),
                Tables\Columns\TextColumn::make('channel'),
                Tables\Columns\TextColumn::make('transaction_date'),
                Tables\Columns\TextColumn::make('amount'),
                Tables\Columns\TextColumn::make('total_price'),
                Tables\Columns\TextColumn::make('transaction_status'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
            ])
            ->actions([
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
            ]);
    }
}
