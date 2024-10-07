<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class DashboardWidget extends ChartWidget
{
    protected static ?string $heading = 'Total Orders';

    public ?string $filter = null;

    protected static string $color = 'info';
    public static function canView(): bool
    {
        return Gate::allows('admin') || Gate::allows('pimpinan');
    }

    public function mount(): void
    {
        $this->filter = now()->year;
    }

    public function getDescription(): ?string
    {
        return 'The number of Total Orders Success per month.';
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

        // Query untuk mendapatkan user_transactions per bulan pada tahun yang dipilih
        $incomeData = DB::table('user_transactions')
            ->select(DB::raw('COUNT(*) as total_transactions'), DB::raw('MONTH(transaction_date) as month'))
            ->where('transaction_status', 'success')
            ->whereYear('transaction_date', $selectedYear)
            ->groupBy(DB::raw('MONTH(transaction_date)'))
            ->pluck('total_transactions', 'month')
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
                    'label' => 'Total Orders',
                    'data' => $data['datasets'],
                    'fill' => 'start',
                ],
            ],
            'labels' => $data['labels'],

        ];
    }

    protected function getOptions(): array
    {
        // Hitung nilai maksimum dari data untuk menentukan batas atas sumbu Y
        $data = $this->getData()['datasets'][0]['data'];
        $maxValue = max($data);

        return [
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'stepSize' => 1,
                    ],
                    'max' => $maxValue + round($maxValue * 0.3), // Tambahkan 30% sebagai batas atas
                ],
            ],
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
