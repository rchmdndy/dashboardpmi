<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BookingResource\RelationManagers\BookingsRelationManager;
use App\Filament\Resources\UserTransactionResource\Pages;
use App\Filament\Resources\UserTransactionResource\Widgets\TransactionStats;
use App\Models\UserTransaction;
use Filament\Forms;
use Filament\Infolists\Components\Section as ComponentsSection;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Pages\SubNavigationPosition;
use Filament\Resources\Pages\Page;
use Filament\Resources\Resource;
use Filament\Support\Enums\ActionSize;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Gate;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
use pxlrbt\FilamentExcel\Exports\ExcelExport;

class UserTransactionResource extends Resource
{
    protected static ?string $model = UserTransaction::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected $bookingService;

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
                    ->limit(4)
                    ->sortable(),
                Tables\Columns\TextColumn::make('user_email')
                    ->limit(20)
                    ->label('Email')
                    ->searchable(isIndividual: true, isGlobal: false),
                Tables\Columns\TextColumn::make('user.name')
                    ->limit(30)
                    ->label('Name')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('channel'),
                Tables\Columns\TextColumn::make('order_id')
                    ->limit(20)
                    ->searchable(isIndividual: true, isGlobal: true),
                Tables\Columns\TextColumn::make('transaction_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('amount')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_price')
                    ->money('IDR')
                    ->summarize([
                        Tables\Columns\Summarizers\Sum::make()
                            ->money('IDR'),
                    ])
                    ->sortable(),
                Tables\Columns\TextColumn::make('transaction_status')
                    ->badge()
                    ->color(function ($state) {
                        return match ($state) {
                            'success' => 'success',
                            'pending' => 'warning',
                            'failed' => 'danger',
                            default => 'secondary',
                        };
                    })
                    ->icon(function ($state) {
                        return match ($state) {
                            'success' => 'heroicon-m-check-badge',
                            'pending' => 'heroicon-m-clock',
                            'failed' => 'heroicon-m-x-circle',
                            default => 'heroicon-m-sparkles',
                        };
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->paginated(true)

            ->filters([
                Tables\Filters\Filter::make('transaction_date')
                    ->label('transaction_date')
                    ->form([
                        Forms\Components\DatePicker::make('start_date')
                            ->label('Transaction From')

                            ->placeholder(fn ($state): string => 'Dec 18, '.now()->subYear()->format('Y')),
                        Forms\Components\DatePicker::make('end_date')
                            ->label('Transaction Until')

                            ->placeholder(fn ($state): string => now()->format('M d, Y')),
                    ])
                    ->columns(2)
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['start_date'] ?? null,
                                fn (Builder $query, $date): Builder => $query->whereDate('transaction_date', '>=', $date),
                            )
                            ->when(
                                $data['end_date'] ?? null,
                                fn (Builder $query, $date): Builder => $query->whereDate('transaction_date', '<=', $date),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['start_date'] ?? null) {
                            $indicators['start_date'] = 'transaction date from '.Carbon::parse($data['start_date'])->toFormattedDateString();
                        }
                        if ($data['end_date'] ?? null) {
                            $indicators['end_date'] = 'transaction date Until '.Carbon::parse($data['end_date'])->toFormattedDateString();
                        }

                        return $indicators;
                    }),
                // FILTER BY TRANSACTION STATUS
                Tables\Filters\SelectFilter::make('transaction_status')
                    ->options([
                        'success' => 'Success',
                        'pending' => 'Pending',
                        'failed' => 'Failed',
                    ])
                    ->label('Transaction Status'),

                // // FILTER BY DATE
                // Tables\Filters\Filter::make('transaction_date')
                //     ->form([
                //         DatePicker::make('transaction_date')
                //             ->placeholder('Select a date'),
                //     ])
                //     ->query(fn (Builder $query, array $data): Builder =>
                //         $query->when(
                //             $data['transaction_date'] ?? null,
                //             fn (Builder $query, $date) => $query->whereDate('transaction_date', $date)
                //         )
                //     )
                //     ->indicateUsing(fn (array $data): array =>
                //         $data['transaction_date'] ?? null
                //             ? ['transaction_date' => 'Order from ' . Carbon::parse($data['transaction_date'])->toFormattedDateString()]
                //             : []
                //     ),
            ])
            ->actions([
                ActionGroup::make([
                    ViewAction::make()
                        ->label('View')
                        ->icon('heroicon-o-eye'),
                    EditAction::make()
                        ->label('Edit')
                        ->color('Amber')
                        ->icon('heroicon-o-pencil'),
                    ActionGroup::make([
                        Action::make('Success')
                            ->icon('heroicon-m-check-badge')
                            ->tooltip('Click to change transaction status to success')
                            ->color('success')
                            ->label('Success')
                            ->requiresConfirmation()
                            ->modalHeading('Confirm Success Transaction')
                            ->modalDescription('Are you sure you\'d like to set transaction to Success?')
                            ->modalSubmitActionLabel('Yes')
                            ->modalIcon('heroicon-m-check-badge')
                            ->modalIconColor('success')
                            ->action(function (Model $record) {
                                $record->transaction_status = 'success';
                                $record->save();

                                Notification::make()
                                    ->title('Transaction Updated')
                                    ->body('The transaction status has been successfully updated to Success.')
                                    ->success()
                                    ->icon('heroicon-m-check-badge')
                                    ->color('success')
                                    ->send();
                            }),
                        Action::make('Pending')
                            ->icon('heroicon-m-clock')
                            ->tooltip('Click to change transaction status to pending')
                            ->color('warning')
                            ->label('Pending')
                            ->requiresConfirmation()
                            ->modalHeading('Confirm Pending Transaction')
                            ->modalDescription('Are you sure you\'d like to set transaction to Pending?')
                            ->modalSubmitActionLabel('Yes')
                            ->modalIcon('heroicon-m-clock')
                            ->modalIconColor('warning')
                            ->action(function (Model $record) {
                                $record->transaction_status = 'pending';
                                $record->save();

                                Notification::make()
                                    ->title('Transaction Updated')
                                    ->body('The transaction status has been successfully updated to Pending.')
                                    ->success()
                                    ->icon('heroicon-m-check-badge')
                                    ->color('success')
                                    ->send();
                            }),
                        Action::make('Failed')
                            ->icon('heroicon-m-x-circle')
                            ->tooltip('Click to change transaction status to failed')
                            ->color('danger')
                            ->requiresConfirmation()
                            ->modalHeading('Confirm Failed Transaction')
                            ->modalDescription('Are you sure you\'d like to set transaction to Failed?')
                            ->modalSubmitActionLabel('Yes')
                            ->modalIcon('heroicon-m-x-circle')
                            ->modalIconColor('danger')
                            ->label('Failed')
                            ->action(function (Model $record) {
                                $record->transaction_status = 'failed';
                                $record->save();

                                Notification::make()
                                    ->title('Transaction Updated')
                                    ->body('The transaction status has been successfully updated to Failed.')
                                    ->success()
                                    ->icon('heroicon-m-check-badge')
                                    ->color('success')
                                    ->send();
                            }),

                    ])
                        ->label('Set Status')
                        ->icon('heroicon-m-ellipsis-vertical')
                        ->size(ActionSize::ExtraSmall)
                        ->color('gray')
                        ->button()
                        ->visible(fn () => Gate::allows('admin')),
                ]),
            ])
            ->bulkActions([
                ExportBulkAction::make()->exports([
                    ExcelExport::make('table')->fromTable()->withFilename(date('Y-m-d').' -User-Transaction-Report'),
                ]),

            ])
            ->groups([
                Tables\Grouping\Group::make('transaction_date')
                    ->label('Transaction Date')
                    ->date()
                    ->collapsible(),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                ComponentsSection::make('Transaction Details')
                    ->description('Showing Transaction Details information')
                    ->schema([
                        TextEntry::make('user_email'),
                        TextEntry::make('channel'),
                        TextEntry::make('order_id'),
                        TextEntry::make('transaction_date')
                            ->badge()
                            ->icon('heroicon-m-calendar-days')
                            ->color('blue'),
                        TextEntry::make('amount'),
                        TextEntry::make('total_price')
                            ->badge()
                            ->money('IDR')
                            ->icon('heroicon-m-currency-dollar')
                            ->color('secondary'),
                        TextEntry::make('transaction_status')
                            ->badge()
                            ->color(function ($state) {
                                return match ($state) {
                                    'success' => 'success',
                                    'pending' => 'warning',
                                    'failed' => 'danger',
                                    default => 'secondary',
                                };
                            })
                            ->icon(function ($state) {
                                return match ($state) {
                                    'success' => 'heroicon-m-check-badge',
                                    'pending' => 'heroicon-m-clock',
                                    'failed' => 'heroicon-m-x-circle',
                                    default => 'heroicon-m-sparkles',
                                };
                            }),
                    ]),

            ])
            ->columns(1)
            ->inlineLabel();
    }

    public static function getRelations(): array
    {
        return [
            BookingsRelationManager::class,
        ];
    }

    public static function getWidgets(): array
    {
        return [
            TransactionStats::class,
        ];
    }

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-shopping-cart';
    }

    public static function getNavigationBadge(): ?string
    {
        $modelClass = static::$model;

        return (string) $modelClass::where('transaction_status', 'pending')->count();
    }

    public static function getNavigationLabel(): string
    {
        return __('Transactions');
    }

    public static function getNavigationGroup(): ?string
    {
        return 'History';
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUserTransactions::route('/'),
            'view' => Pages\ViewUserTransaction::route('/{record}/view'),
        ];
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            Pages\ViewUserTransaction::class,
        ]);
    }

    public static function getGlobalSearchResultTitle(Model $record): string
    {
        return $record->order_id;
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            'Email' => $record->user_email,
            'Name' => $record->user->name,
            'Order ID' => $record->order_id,
            'Transaction status' => $record->transaction_status,
            'transaction date' => $record->transaction_date,
            'In' => 'Transactions',
        ];
    }

    public static function getGlobalSearchEloquentQuery(): Builder
    {
        return parent::getGlobalSearchEloquentQuery()->with(['user']);
    }

    public static function getGlobalSearchResultUrl(Model $record): string
    {
        return UserTransactionResource::getUrl('view', ['record' => $record]);
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['user.name', 'user_email', 'order_id'];
    }
}
