<?php

namespace App\Filament\Pages;

use Filament\Forms\Get;
use Filament\Forms\Form;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\Gate;
use Filament\Forms\Components\Section;
use App\Filament\Widgets\LastestOrders;
use App\Filament\Widgets\RoomsMapChart;
use App\Filament\Widgets\DashboardWidget;
use Filament\Forms\Components\DatePicker;
use App\Filament\Widgets\StatsOverviewWidget;
use App\Filament\Widgets\RoomsMapChartTreeMap;
use Filament\Pages\Dashboard as BaseDashboard;
use App\Filament\Widgets\DashboardWidgetChartBar;
use App\Filament\Widgets\StatsAssetRoomsOverview;
use App\Filament\Widgets\StatsAssetBookingsOverview;

class Dashboard extends BaseDashboard
{
    // protected static string $view = 'filament.pages.dashboard';

    // protected static ?string $navigationIcon = 'heroicon-o-home';

    use BaseDashboard\Concerns\HasFiltersForm;

    public function filtersForm(Form $form): Form
    {
        return $form
            ->schema([
                Gate::allows('admin') || Gate::allows('pimpinan') ?
                Section::make()
                    ->schema([
                        DatePicker::make('startDate')
                            ->maxDate(fn (Get $get) => $get('endDate') ?: now()),
                        DatePicker::make('endDate')
                            ->minDate(fn (Get $get) => $get('startDate') ?: now()),
                        // ->maxDate(now()),
                    ])
                    ->columns(2) : Section::make()->schema([]), // Ensure a valid Section is always returned
            ]);
    }

    public function getWidgets(): array
    {
        return [
            StatsOverviewWidget::class,
//            \Filament\Widgets\AccountWidget::class,
            // \Filament\Widgets\FilamentInfoWidget::class,
            DashboardWidgetChartBar::class,
            DashboardWidget::class,
            StatsAssetBookingsOverview::class,
            RoomsMapChartTreeMap::class,
            StatsAssetRoomsOverview::class,
            RoomsMapChart::class,
            LastestOrders::class,
        ];

    }
}
