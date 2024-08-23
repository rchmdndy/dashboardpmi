<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BookingResource\Pages;
use App\Filament\Resources\BookingResource\Widgets\BookingStats;
use App\Models\Booking;
use Filament\Forms;
use Filament\Pages\SubNavigationPosition;
use Filament\Resources\Pages\Page;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Gate;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
use pxlrbt\FilamentExcel\Exports\ExcelExport;

class BookingResource extends Resource
{
    protected static ?string $model = Booking::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar';

    protected static ?string $recordTitleAttribute = 'user_email';

    protected static SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

    public static function canEdit(Model $record): bool
    {
        return false;
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canDelete(Model $record): bool
    {
        return false;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->sortable(),
                Tables\Columns\TextColumn::make('user_email')
                    ->searchable(isIndividual: true, isGlobal: true),
                Tables\Columns\TextColumn::make('user_transaction.order_id')
                    ->searchable(isIndividual: true, isGlobal: true)
                    ->label('Order ID')
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
                    ->summarize([
                        Tables\Columns\Summarizers\Count::make()
                            ->label('Total Bookings')
                            ->numeric(),
                    ])
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
            ->filtersLayout(FiltersLayout::AboveContent)
            ->filters([
                //
                Tables\Filters\Filter::make('start_date')
                    ->form([
                        Forms\Components\DatePicker::make('start_date')
                            ->label('Check-in')

                            ->placeholder(fn ($state): string => 'Dec 18, '.now()->subYear()->format('Y')),
                        Forms\Components\DatePicker::make('end_date')
                            ->label('Check-out')

                            ->placeholder(fn ($state): string => now()->format('M d, Y')),
                    ])
                    ->columns(2)
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['start_date'] ?? null,
                                fn (Builder $query, $date): Builder => $query->whereDate('start_date', '>=', $date),
                            )
                            ->when(
                                $data['end_date'] ?? null,
                                fn (Builder $query, $date): Builder => $query->whereDate('end_date', '<=', $date),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['start_date'] ?? null) {
                            $indicators['start_date'] = 'Check-in In '.Carbon::parse($data['start_date'])->toFormattedDateString();
                        }
                        if ($data['end_date'] ?? null) {
                            $indicators['end_date'] = 'Check-out In '.Carbon::parse($data['end_date'])->toFormattedDateString();
                        }

                        return $indicators;
                    }),
            ])
            ->filtersFormColumns(1)

            ->actions(array_filter([
                Tables\Actions\ViewAction::make(),
                Gate::allows('admin') ? Tables\Actions\EditAction::make()->color('Amber') : null,

            ]))
            ->bulkActions([
                ExportBulkAction::make()->exports([
                    ExcelExport::make('table')->fromTable()->withFilename(date('Y-m-d').' -Booking-Report'),
                ]),
            ])
            ->groups([
                Tables\Grouping\Group::make('start_date')
                    ->label('Check-in')
                    ->date()
                    ->collapsible(),
                Tables\Grouping\Group::make('end_date')
                    ->label('Check-out')
                    ->date()
                    ->collapsible(),
                Tables\Grouping\Group::make('room.roomType.room_type')
                    ->label('Room Type')
                    ->collapsible(),
            ]);
    }

    public static function getGlobalSearchResultTitle(Model $record): string
    {
        return $record->user_email;
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            'Booked By' => $record->user_email,
            'Room Name' => $record->room->room_name,
            'Check In' => $record->start_date,
            'Check Out' => $record->end_date,
            'In' => 'Bookings',
        ];
    }

    public function getGloballySearchableAttribute(): array
    {
        return [
            'user_email', 'user_transaction.order_id',
        ];
    }

    public static function getGlobalSearchEloquentQuery(): Builder
    {
        return parent::getGlobalSearchEloquentQuery()->with(['room']);
    }

    public static function getGlobalSearchResultUrl(Model $record): string
    {
        return BookingResource::getUrl('view', ['record' => $record]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getWidgets(): array
    {
        return [
            BookingStats::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBookings::route('/'),
            'view' => Pages\ViewBooking::route('/{record}/view'),
        ];
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([Pages\ViewBooking::class]);
    }

    public static function getNavigationLabel(): string
    {
        return __('Bookings');
    }

    public static function getNavigationGroup(): ?string
    {
        return 'History';
    }
}
