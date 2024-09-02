<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RoomImageResource\Pages;
use App\Models\RoomImage;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Filament\Infolists\Components\TextEntry;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\TextColumn\TextColumnSize;
use Illuminate\Support\Facades\Gate;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Illuminate\Support\Str;
use Illuminate\Http\UploadedFile;

class RoomImageResource extends Resource
{
    protected static ?string $model = RoomImage::class;

    protected static ?string $navigationIcon = 'heroicon-o-photo';

    public static function canEdit(Model $record): bool
    {
        return Gate::allows('admin');
    }

    public static function canCreate(): bool
    {
        return Gate::allows('admin');
    }

    public static function canDelete(Model $record): bool
    {
        return Gate::allows('admin');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Room Image')
                    ->description('Koleksi Image Untuk Ruangan')
                    ->schema([
                        Forms\Components\Select::make('room_type_id')
                            ->placeholder('Select Room Type')
                            ->relationship('roomType', 'room_type')
                            ->native(false)
                            ->required(),
                        Forms\Components\FileUpload::make('image_path')
                            ->label('Image')
                            ->image()
                            ->required()
                            ->visibility('public')
                            ->preserveFilenames()
                            ->rules(['mimes:jpg,jpeg,png,gif'])
                            ->directory('images/kamar')
                            ->getUploadedFileNameForStorageUsing(
                                fn (UploadedFile $file): string => Str::slug(
                                    pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)
                                )
                                . '_' . now()->timestamp
                                . '.' . $file->getClientOriginalExtension()
                            )
                            ->imageEditor()
                            ->maxSize(8192),
                    ])->columns(1),
            ])
            ->columns(1);
    }

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            $model->image_path = $model->getClientOriginalName();
        });
    }

    public static function table(Table $table): Table
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
                        Tables\Columns\TextColumn::make('image_path')
                            ->color('secondary')
                            ->weight(FontWeight::Light)
                            ->size(TextColumnSize::Small)
                            ->limit(25)
                            ->getStateUsing(function ($record) {
                                return str_replace('images/kamar/', '', $record->image_path);
                            })
                            ->tooltip(function (TextColumn $column): ?string {
                                $state = $column->getState();

                                if (strlen($state) <= $column->getCharacterLimit()) {
                                    return null;
                                }

                                // Only render the tooltip if the column content exceeds the length limit.
                                return $state;
                            }
                            )
                    ]  ),
                ])->space(3),
            ])
            ->defaultSort('room_type_id', 'asc')
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
                Tables\Filters\SelectFilter::make('room_type_id')
                    ->options(function () {
                        return \App\Models\RoomType::all()->pluck('room_type', 'id');
                    })
                    ->label('Room Type'),
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

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRoomImages::route('/'),
        ];
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Rooms';
    }
}
