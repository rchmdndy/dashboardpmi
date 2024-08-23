<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class DashboardWidgetChartBar extends ChartWidget
{
    protected static ?string $heading = 'Monthly Revenue';

    public ?string $filter = null;

    protected static string $color = 'success';

    public function getDescription(): ?string
    {
        return 'Monthly Revenue This month';
    }

    public function mount(): void
    {
        $this->filter = now()->year;
    }

    protected function getFilters(): ?array
    {
        $currentYear = now()->year;
        $startYear = 2024;
        $endYear = $currentYear + 1;

        $filters = [];

        for ($year = $startYear; $year <= $endYear; $year++) {
            $filters[$year] = $year;
        }

        return $filters;
    }

    protected function getData(): array
    {
        $selectedYear = $this->filter ?? now()->year;

        // Array default untuk setiap bulan
        $defaultData = array_fill(0, 12, null);

        // Query untuk mendapatkan total_income per bulan pada tahun yang dipilih
        $incomeData = DB::table('reports')
            ->select(DB::raw('SUM(total_income) as total_income'), DB::raw('MONTH(created_at) as month'))
            ->whereYear('created_at', $selectedYear)
            ->groupBy(DB::raw('MONTH(created_at)'))
            ->pluck('total_income', 'month')
            ->toArray();

        // Gabungkan defaultData dengan incomeData
        foreach ($incomeData as $month => $total_income) {
            $defaultData[$month - 1] = $total_income;
        }

        $data = [
            'datasets' => $defaultData,
            'labels' => [
                'January', 'February', 'March', 'April', 'May', 'June',
                'July', 'August', 'September', 'October', 'November', 'December',
            ],
        ];

        return [
            'datasets' => [
                [
                    'label' => 'Revenue',
                    'data' => $data['datasets'],
                    'fill' => 'start',
                ],
            ],
            'labels' => $data['labels'],
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
