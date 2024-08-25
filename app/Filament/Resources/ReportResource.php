<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReportResource\Pages;
use App\Filament\Resources\ReportResource\Widgets\ReportStats;
use App\Models\Report;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
use pxlrbt\FilamentExcel\Exports\ExcelExport;

class ReportResource extends Resource
{
    protected static ?string $model = Report::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

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

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Report')
                    ->schema([
                        Forms\Components\Select::make('room_type_id')
                            ->label('Room Type')
                            ->native(false)
                            ->relationship('roomType', 'room_type')
                            ->required(),
                        Forms\Components\TextInput::make('total_bookings')
                            ->required()
                            ->rules(['min:1'])
                            ->numeric(),
                        Forms\Components\TextInput::make('total_income')
                            ->required()
                            ->columnSpanFull()
                            ->prefix('RP. ')
                            ->numeric(),
                        Forms\Components\DatePicker::make('created_at')
                            ->required()
                            ->label('Tanggal')
                            ->default(now())
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->limit(4)
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tanggal')
                    ->date('M, Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('roomType.room_type')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_bookings')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_income')
                    ->money('IDR')
                    ->label('Total Income')
                    ->summarize([
                        Tables\Columns\Summarizers\Sum::make()
                            ->label('Total income')
                            ->money('IDR'),
                    ])
                    ->sortable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->date()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('month')
                    ->label('Month')
                    ->options(collect(range(1, 12))->mapWithKeys(fn ($month) => [$month => Carbon::create()->month($month)->format('F')])->toArray())
                    ->query(function (Builder $query, array $data) {
                        if (isset($data['value'])) {
                            $query->whereMonth('created_at', $data['value']);
                        }
                    }),

                Tables\Filters\SelectFilter::make('year')
                    ->label('Year')

                    ->options(collect(range(2020, Carbon::now()->year))->mapWithKeys(fn ($year) => [$year => $year])->toArray())
                    ->query(function (Builder $query, array $data) {
                        if (isset($data['value'])) {
                            $query->whereYear('created_at', $data['value']);
                        }
                    }),

                Tables\Filters\Filter::make('created_at')
                    ->label('Report date')
                    ->name('Report date')
                    ->form([
                        Forms\Components\DatePicker::make('start_date')
                            ->label('Report From')

                            ->placeholder(fn ($state): string => 'Dec 18, '.now()->subYear()->format('Y')),
                        Forms\Components\DatePicker::make('end_date')
                            ->label('Report Until')

                            ->placeholder(fn ($state): string => now()->format('M d, Y')),
                    ])
                    ->columns(2)
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['start_date'] ?? null,
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['end_date'] ?? null,
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['start_date'] ?? null) {
                            $indicators['start_date'] = 'Report date from '.Carbon::parse($data['start_date'])->toFormattedDateString();
                        }
                        if ($data['end_date'] ?? null) {
                            $indicators['end_date'] = 'Report date Until '.Carbon::parse($data['end_date'])->toFormattedDateString();
                        }

                        return $indicators;
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->color('Amber'),
            ])
            ->bulkActions([
                ExportBulkAction::make()->exports([
                    ExcelExport::make('table')->fromTable()->withFilename(date('Y-m-d').' -Report'),
                ]),
            ])
            ->groups([
                Tables\Grouping\Group::make('created_at')
                    ->label('Date')
                    ->date()
                    ->collapsible(),
            ]);
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
            ReportStats::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListReports::route('/'),
        ];
    }

    public static function getNavigationGroup(): ?string
    {
        return 'History';
    }
}
