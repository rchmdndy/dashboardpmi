<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BookingResource\RelationManagers\BookingRelationManager;
use App\Filament\Resources\RoomResource\Pages;
use App\Models\Room;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\SubNavigationPosition;
use Filament\Resources\Pages\Page;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Gate;

class RoomResource extends Resource
{
    protected static ?string $model = Room::class;

    protected static ?string $navigationIcon = 'heroicon-o-pencil-square';

    protected static ?string $recordTitleAttribute = 'room_name';

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
                Forms\Components\Section::make('Room Information')
                    ->schema([
                        Forms\Components\Select::make('room_type_id')
                            ->native(false)
                            ->placeholder('Select Room Type')
                            ->relationship('roomType', 'room_type')
                            ->required(),
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

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('row_number')
                    ->label('No.')
                    ->rowIndex()
                    ->sortable(false),
                Tables\Columns\TextColumn::make('room_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('roomType.room_type')
                    ->searchable(),
                Tables\Columns\TextColumn::make('parentRoom.room_name')
                    ->limit(15)
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),
            ])
            ->defaultSort('room_type_id', 'asc')
            ->filters([
                //
                Tables\Filters\SelectFilter::make('room_type_id')
                    ->options(function () {
                        return \App\Models\RoomType::all()->pluck('room_type', 'id');
                    })
                    ->label('Room Type'),
            ])
            ->actions([
                ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make()->color('Amber'),
                    Tables\Actions\DeleteAction::make(),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->groups([
                Tables\Grouping\Group::make('roomType.room_type')
                    ->label('Room Type')
                    ->collapsible(),
                Tables\Grouping\Group::make('parent_id')
                    ->label('Parent ID')
                    ->collapsible(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
            BookingRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRooms::route('/'),
            'create' => Pages\CreateRoom::route('/create'),
            'edit' => Pages\EditRoom::route('/{record}/edit'),
            'view' => Pages\ViewRoom::route('/{record}/view'),
            'delete' => Pages\DeleteRoom::route('/{record}/delete'),
        ];
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            Pages\ViewRoom::class,
            Pages\EditRoom::class,
        ]);
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Rooms';
    }

    public static function getGlobalSearchResultTitle(Model $record): string
    {
        return $record->room_name;
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            'Room Name' => $record->room_name,
            'Room Type' => $record->roomType->room_type,
            'In' => 'Rooms',
        ];
    }

    public static function getGlobalSearchEloquentQuery(): Builder
    {
        return parent::getGlobalSearchEloquentQuery()->with(['roomType']);
    }

    public static function getGlobalSearchResultUrl(Model $record): string
    {
        return Gate::allows('admin') ? RoomResource::getUrl('edit', ['record' => $record]) : RoomResource::getUrl('view', ['record' => $record]);
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['room_name'];
    }
}
