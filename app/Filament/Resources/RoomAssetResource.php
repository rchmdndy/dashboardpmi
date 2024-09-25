<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RoomAssetResource\Pages;
use App\Filament\Resources\RoomAssetResource\RelationManagers;
use App\Models\RoomAsset;
use Filament\Actions\Action;
use Filament\Actions\ViewAction;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class RoomAssetResource extends Resource
{
    protected static ?string $model = RoomAsset::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make("Room Asset")
                ->description("Koleksi barang untuk ruangan")
                ->schema([
                   Forms\Components\Select::make("room_id")
                   ->placeholder("Select Room")
                    ->relationship("room", "room_name")
                    ->native(false)
                    ->required(),
                    Forms\Components\Select::make("inventory_id")
                    ->placeholder("Select Inventory")
                    ->relationship("inventory", "name")
                    ->native(false)
                    ->required(),
                    Forms\Components\Checkbox::make("isBroken")
                    ->label("Is Broken")
                    ->default(false),
                    Forms\Components\Textarea::make("comment")
                    ->label("Comment")
                    ->rows(3)
                    ->maxLength(255)
                ])->columns(1)
            ])->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('room.room_name')
                    ->label('Room')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('inventory.name')
                    ->label('Inventory')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\IconColumn::make('isBroken')
                    ->label('Apakah rusak?')
                    ->boolean(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('room_id')
                    ->options(fn () => \App\Models\Room::pluck('room_name', 'id')->toArray())
                ->label('Room'),
                Tables\Filters\SelectFilter::make('inventory_id')
                    ->options(fn () => \App\Models\Inventory::pluck('name', 'id')->toArray())
                ->label('Inventory'),
            ])
            ->actions([
                ActionGroup::make([
                    Action::make('isBroken')
                    ->label(fn (Model $record) => $record->isBroken ? 'Tandai tidak rusak' : 'Tandai rusak')
                        ->icon(fn (Model $record) => $record->isBroken ? 'heroicon-m-x-circle' : 'heroicon-m-check-badge')
                        ->color(fn (Model $record) => $record->isBroken ? 'danger' : 'success')
                        ->action(function (Model $record) {
                            $record->isBroken = !$record->isBroken;
                            $record->save();
                            Notification::make()
                                ->title('Status berhasil diperbarui!')
                                ->success()
                                ->send();
                        })
                        ->requiresConfirmation()

                ]),
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
            'index' => Pages\ListRoomAssets::route('/'),
            'create' => Pages\CreateRoomAsset::route('/create'),
            'edit' => Pages\EditRoomAsset::route('/{record}/edit'),
        ];
    }

    public static function getNavigationGroup(): ?string
    {
        return "Rooms";
    }
}
