<?php

namespace App\Filament\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use App\Models\UserTransaction;
use Illuminate\Support\Facades\Gate;
use Filament\Widgets\TableWidget as BaseWidget;
use App\Filament\Resources\UserTransactionResource;

class LastestOrders extends BaseWidget
{
    protected static ?int $sort = 4;

    protected int|string|array $columnSpan = 'full';

    public static function canView(): bool
    {
        return Gate::allows('admin') || Gate::allows('pimpinan') || Gate::allows('customerService');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(UserTransactionResource::getEloquentQuery())
            ->defaultPaginationPageOption(5)
            ->defaultSort('created_at', 'desc')
            ->columns([
                // ...
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
                    ->searchable(isIndividual: true, isGlobal: false),
                Tables\Columns\TextColumn::make('transaction_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('amount')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_price')
                    ->money('IDR')
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
            ->actions([
                Tables\Actions\Action::make('open')
                    ->color('indigo')
                    ->button()
                    ->url(fn (UserTransaction $record): string => UserTransactionResource::getUrl('view', ['record' => $record])),
            ]);
    }
}
