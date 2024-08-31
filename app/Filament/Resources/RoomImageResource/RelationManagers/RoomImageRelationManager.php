<?php

namespace App\Filament\Resources\RoomImageResource\RelationManagers;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use App\Models\RoomImage;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Gate;
use Filament\Support\Enums\FontWeight;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Resources\RelationManagers\RelationManager;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class RoomImageRelationManager extends RelationManager
{
    protected static string $relationship = 'room_image';

    public function form(Form $form): Form
    {
        return $form
        ->schema([
            Forms\Components\Section::make('Room Image')
                ->description('Koleksi Image Untuk Ruangan')
                ->schema([
                    Forms\Components\TextInput::make('room_type_id')
                        ->default(fn ($get) => $this->getOwnerRecord()?->id)
                        ->disabled()
                        ->label('Room Type ID'),
                    Forms\Components\FileUpload::make('image_path')
                        ->label('Image')
                        ->image()
                        ->required()
                        ->visibility('public')
                        ->preserveFilenames()
                        ->directory('images/kamar')
                        ->rules(['mimes:jpg,jpeg,png,gif'])
                        ->getUploadedFileNameForStorageUsing(
                            fn (TemporaryUploadedFile $file): string => (string) str($file->getClientOriginalName())
                                ->prepend(now()->timestamp.'_')
                        )
                        ->imageEditor()
                        ->maxSize(8192),
                ])->columns(1),
        ])
        ->columns(1);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\Layout\Stack::make([
                    Tables\Columns\ImageColumn::make('image_path')
                        ->getStateUsing(function (RoomImage $record): string {
                            return $record->image_path;
                        })
                        ->visibility('public')
                        ->height('100%')
                        ->width('100%')
                        ->height(200)
                        ->width(200),

                    Tables\Columns\Layout\Stack::make([
                        Tables\Columns\TextColumn::make('roomType.room_type')
                            ->weight(FontWeight::Bold),
                    ]),
                ])->space(3),
            ])
            ->contentGrid([
                'md' => 2,
                'xl' => 3,
            ])
            ->paginated([
                15,
                30,
                60,
                'all',
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->color('Amber'),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions(
                array_filter([
                    Gate::allows('admin') ?
                    Tables\Actions\BulkActionGroup::make([
                        Tables\Actions\DeleteBulkAction::make(),
                    ]) : null,
                ]),
            )
            ->groups([
                Tables\Grouping\Group::make('roomType.room_type')
                    ->label('Room Type')
                    ->collapsible(),
            ]);
    }
}
