<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\DashboardWidget;
use App\Filament\Widgets\DashboardWidgetChartBar;
use App\Filament\Widgets\LastestOrders;
use App\Filament\Widgets\RoomsMapChart;
use App\Filament\Widgets\RoomsMapChartTreeMap;
use App\Filament\Widgets\StatsOverviewWidget;
use Filament\Facades\Filament;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    // protected static string $view = 'filament.pages.dashboard';

    // protected static ?string $navigationIcon = 'heroicon-o-home';

    use BaseDashboard\Concerns\HasFiltersForm;

    public function filtersForm(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->schema([
                        DatePicker::make('startDate')
                            ->maxDate(fn (Get $get) => $get('endDate') ?: now()),
                        DatePicker::make('endDate')
                            ->minDate(fn (Get $get) => $get('startDate') ?: now()),
                        // ->maxDate(now()),
                    ])
                    ->columns(2),
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
            RoomsMapChartTreeMap::class,
            RoomsMapChart::class,
            LastestOrders::class,
        ];

    }
}
