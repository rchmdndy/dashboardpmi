<?php

namespace App\Filament\Resources\RoomResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class RoomRelationManager extends RelationManager
{
    protected static string $relationship = 'room';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Room Information')
                    ->schema([
                        Forms\Components\TextInput::make('room_type_id')
                            ->default(fn ($get) => $this->getOwnerRecord()?->id)
                            ->disabled()
                            ->label('Room Type ID'),
                        Forms\Components\TextInput::make('room_name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Select::make('parent_id')
                            ->native(false)
                            ->placeholder('Select Parent Room')
                            ->helperText('Menunjukkan hubungan kepada parent room')
                            ->relationship('parentRoom', 'room_name')
                            ->columnSpanFull(),
                    ])->columns(2),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('room_type_id')
            ->columns([
                Tables\Columns\TextColumn::make('row_number')
                    ->label('No.')
                    ->rowIndex()
                    ->sortable(false),
                Tables\Columns\TextColumn::make('room_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('roomType.room_type')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                Tables\Columns\TextColumn::make('parentRoom.room_name')
                    ->limit(15)
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
