<?php

namespace App\Filament\Resources\RoomResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Gate;

class RoomAssetsRelationManager extends RelationManager
{
    protected static string $relationship = 'roomAssets';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('inventory_id')
                    ->label('Inventory')
                    ->relationship('inventory', 'name')
                    ->required(),
                Forms\Components\Checkbox::make("isBroken")
                    ->label("Is Broken")
                    ->default(false),
                Forms\Components\Textarea::make("comment")
                    ->label("Comment")
                    ->rows(3)
                    ->maxLength(255)
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title=Room Assets')
            ->columns([
//                Tables\Columns\TextColumn::make('title=Room Assets'),
                Tables\Columns\TextColumn::make('inventory.name')->label('Inventory'),
                Tables\Columns\IconColumn::make('isBroken')
                    ->label('Item rusak?')
                    ->boolean(),
                Tables\Columns\TextColumn::make('comment')
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Gate::allows('admin') ? Action::make('isBroken')
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
                    ->modalDescription('Anda anda ingin menambah keterangan item?')
                    : null,

                Tables\Actions\EditAction::make()
                    ->label('Edit Item'),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}