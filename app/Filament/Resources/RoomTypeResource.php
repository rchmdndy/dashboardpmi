<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RoomImageResource\RelationManagers\RoomImageRelationManager;
use App\Filament\Resources\RoomResource\RelationManagers\RoomRelationManager;
use App\Filament\Resources\RoomTypeResource\Pages;
use App\Models\RoomType;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\SubNavigationPosition;
use Filament\Resources\Pages\Page;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Gate;

class RoomTypeResource extends Resource
{
    protected static ?string $model = RoomType::class;

    protected static ?string $navigationIcon = 'heroicon-o-square-3-stack-3d';

    protected static ?string $recordTitleAttribute = 'room_type';

    protected static SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

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
                        Forms\Components\TextInput::make('room_type')
                            ->required()
                            ->label('Type Room Name')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('capacity')
                            ->required()
                            ->helperText('Kapasitas tipe ruangan ini')
                            ->rules(['integer', 'min:1']),
                        Forms\Components\TextInput::make('price')
                            ->required()
                            ->numeric()
                            ->columnSpanFull()
                            ->helperText('Harga per tipe ruangan')
                            ->prefix('Rp. '),
                    ])->columns(2),
                Forms\Components\MarkdownEditor::make('description')
                    ->label('Description (Optional)')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('row_number')
                    ->label('No.')
                    ->rowIndex()
                    ->sortable(false),
                Tables\Columns\TextColumn::make('room_type')
                    ->searchable(),
                Tables\Columns\TextColumn::make('capacity')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('price')
                    ->money('IDR')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make()->color('Amber'),
                    Tables\Actions\DeleteAction::make(),
                ]),

            ])
            ->bulkActions([
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
            RoomRelationManager::class,
            RoomImageRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRoomTypes::route('/'),
            'create' => Pages\CreateRoomType::route('/create'),
            'edit' => Pages\EditRoomType::route('/{record}/edit'),
            'view' => Pages\ViewRoomType::route('/{record}/view'),
        ];
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            Pages\ViewRoomType::class,
            Pages\EditRoomType::class,
        ]);
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Rooms';
    }

    public static function getGlobalSearchResultTitle(Model $record): string
    {
        return $record->room_type;
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            'Room Type' => $record->room_type,
            'Capacity' => $record->capacity,
            'Price' => $record->price,
            'In' => 'Room Types',
        ];
    }

    public static function getGlobalSearchResultUrl(Model $record): string
    {
        return Gate::allows('admin') ? RoomTypeResource::getUrl('edit', ['record' => $record]) : RoomTypeResource::getUrl('view', ['record' => $record]);
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['room_type'];
    }
}
