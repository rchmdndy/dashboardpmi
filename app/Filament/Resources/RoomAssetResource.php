<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use App\Models\RoomAsset;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Gate;
use Illuminate\Database\Eloquent\Model;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\ActionGroup;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\RoomAssetResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\RoomAssetResource\RelationManagers;

class RoomAssetResource extends Resource
{
    protected static ?string $model = RoomAsset::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';
    public static function canEdit(Model $record): bool
    {
        return Gate::allows('admin') || Gate::allows('inventoris');
    }

    public static function canCreate(): bool
    {
        return Gate::allows('admin') || Gate::allows('inventoris');
    }

    public static function canDelete(Model $record): bool
    {
        return Gate::allows('admin') || Gate::allows('inventoris');
    }

    public static function canViewAny(): bool
    {
        return Gate::allows('admin') || Gate::allows('pimpinan') || Gate::allows('inventoris');
    }

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
            ->headerActions([
                Action::make('Print')
                ->label('Print')
                ->color('red')
                ->action(function (Table $table) {

                    $currentRecords = $table->getRecords();
                    $recordIds = $currentRecords->pluck('id')->toArray();
                    $user = auth()->user();
                    // dd($recordIds);

                    $url = URL::route('RoomAssets.print', ['records' => $recordIds, 'user' => $user]);

                    return redirect()->to($url);
                })
            ])
            ->defaultSort('room_id', 'desc')
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
                    ->label('Item rusak?')
                    ->boolean(),
                Tables\Columns\TextColumn::make('comment')
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('room_id')
                    ->options(fn () => \App\Models\Room::pluck('room_name', 'id')->toArray())
                ->label('Room'),
                Tables\Filters\SelectFilter::make('inventory_id')
                    ->options(fn () => \App\Models\Inventory::pluck('name', 'id')->toArray()),
                Tables\Filters\TernaryFilter::make('isBroken')
                    ->label('Hanya tampilkan item yang rusak?')
                    ->options([
                        'true' => 'Yes',
                        'false' => 'No',
                    ]),
            ])
            ->actions([
                Gate::allows('admin') || Gate::allows('inventoris') ? 
                ActionGroup::make([
                    Action::make('isBroken')
                        ->label(fn (Model $record) => $record->isBroken == false ? 'Item rusak ?' : 'Item sudah diperbaiki ?')
                        ->icon(fn (Model $record) => $record->isBroken == false ? 'heroicon-m-x-circle' : 'heroicon-m-check-badge')
                        ->tooltip(fn (Model $record) => $record->isBroken == false ? 'Klik untuk membuat kondisi menjadi rusak' : 'Klik jika sudah berhasil diperbaiki')
                        ->color(fn (Model $record) => $record->isBroken == false ? 'danger' : 'success')
                        ->form([
                            Forms\Components\Textarea::make('comment')
                                ->default(fn (Model $record) => $record->comment)
                                ->label('Keterangan'),
                            ])
                        ->action(function (Model $record, array $data): void {
                            $record->comment = $data['comment'];
                            $record->isBroken = !$record->isBroken;
                            $record->save();
                            Notification::make()
                                ->title('Status berhasil diperbarui!')
                                ->success()
                                ->send();
                        })
                        ->requiresConfirmation()
                        ->modalDescription('Anda anda ingin menambah keterangan item?'),

                    Tables\Actions\EditAction::make()
                        ->label('Edit Item')
                ]) : Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort(column: 'room_id', direction: 'asc');
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
        return "Inventories";
    }
}
