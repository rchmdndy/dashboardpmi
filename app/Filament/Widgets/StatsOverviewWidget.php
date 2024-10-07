<?php

namespace App\Filament\Widgets;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class StatsOverviewWidget extends BaseWidget
{
    use InteractsWithPageFilters;

    protected static ?int $sort = 0;

    public static function canView(): bool
    {
        return Gate::allows('admin') || Gate::allows('pimpinan');
    }

    protected function getStats(): array
    {
        $startDate = $this->filters['startDate'] ?? null
            ? Carbon::parse($this->filters['startDate'])
            : now()->startOfMonth();

        $endDate = $this->filters['endDate'] ?? null
            ? Carbon::parse($this->filters['endDate'])
            : now()->endOfMonth();

        $this_month_revenue = DB::table('reports')
            ->where('created_at', '>=', Carbon::now()->startOfMonth())
            ->sum('total_income') ?? 0;

        $before_month_revenue = DB::table('reports')
            ->whereBetween('created_at', [Carbon::now()->subMonth()->startOfMonth(), Carbon::now()->subMonth()->endOfMonth()])
            ->sum('total_income') ?? 0;

        $revenue = DB::table('reports')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('total_income');
        $formattedRevenue = 'Rp. '.number_format($revenue, 0, ',', '.');

        $percentage = $before_month_revenue > 0
            ? round(($this_month_revenue - $before_month_revenue) / $before_month_revenue * 100, 2)
            : 0;

        $orders = DB::table('user_transactions')
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->where('transaction_status', 'success')
            ->count();

        $guest = DB::table('user_transactions')
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->where('transaction_status', 'success')
            ->sum('amount');

        return [
            Stat::make('Revenue in this Month', ''.$formattedRevenue)
                ->description($percentage > 0 ? "$percentage% increase from last month" : "$percentage% change from last month")
                ->descriptionIcon($percentage >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->color($percentage >= 0 ? 'success' : 'danger'),

            Stat::make('Orders in this Month', $orders)
                ->description('Total orders')
                ->descriptionIcon('heroicon-o-shopping-cart')
                ->chart([3, 2, 5, 3, 7, 4, 9])
                ->color('Amber'),

            Stat::make('Guest in this Month', $guest)
                ->description('Total guest this month')
                ->descriptionIcon('heroicon-o-user-group')
                ->chart([50, 89, 55, 53, 44, 80, 92])
                ->color('secondary'),

        ];
    }
}
