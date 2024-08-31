<?php

namespace App\Filament\Resources\UserTransactionResource\Widgets;

use App\Filament\Resources\UserTransactionResource\Pages\ListUserTransactions;
use App\Models\UserTransaction;
use Filament\Widgets\Concerns\InteractsWithPageTable;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use Illuminate\Support\Facades\Log;

class TransactionStats extends BaseWidget
{
    use InteractsWithPageTable;

    protected static ?string $pollingInterval = null;

    protected function getTablePage(): string
    {
        return ListUserTransactions::class;
    }

    protected function getStats(): array
    {
        // Log::info('Loading widget...');
        $orderData = Trend::model(UserTransaction::class)
            ->between(
                start: now()->subYear(),
                end: now(),
            )
            ->perMonth()
            ->count();

        // $revenueData = Trend::model(UserTransaction::class)
        //     ->between(
        //         start: now()->subYear(),
        //         end: now(),
        //     )
        //     ->perMonth()
        //     ->sum('total_price');

        // dd($revenueData);

        return [
            Stat::make('Transactions', $this->getPageTableQuery()->count())
                ->chart(
                    $orderData
                        ->map(fn (TrendValue $value) => $value->aggregate)
                        ->toArray()
                )
                ->icon('heroicon-o-shopping-cart')
                ->color('Amber'),

            Stat::make('Completed orders', $this->getPageTableQuery()->whereIn('transaction_status', ['success'])->count())
                ->icon('heroicon-o-check-badge'),

            Stat::make('Failed orders', $this->getPageTableQuery()->whereIn('transaction_status', ['failed'])->count())
                ->icon('heroicon-o-x-circle'),

            Stat::make('Pending orders', $this->getPageTableQuery()->whereIn('transaction_status', ['pending'])->count())
                ->icon('heroicon-o-clock'),

            Stat::make('Average Guest', number_format($this->getPageTableQuery()->avg('amount')))
                ->icon('heroicon-o-user-group'),

            Stat::make('Total Revenue', 'Rp. '.number_format($this->getPageTableQuery()->where('transaction_status', 'success')->sum('total_price')))
                ->icon('heroicon-o-currency-dollar')
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->color('success'),
        ];
    }
}
