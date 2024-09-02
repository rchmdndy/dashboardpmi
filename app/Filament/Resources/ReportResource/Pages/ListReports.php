<?php

namespace App\Filament\Resources\ReportResource\Pages;

use App\Filament\Resources\ReportResource;
use Filament\Actions;
use Filament\Pages\Concerns\ExposesTableToWidgets;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;

class ListReports extends ListRecords
{
    protected static string $resource = ReportResource::class;

    use ExposesTableToWidgets;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return ReportResource::getWidgets();
    }

    public function getTabs(): array
    {
        $year = now()->year;

        return [
            null => Tab::make('All'),
            'Januari' => Tab::make()->query(fn ($query) => $query->where('created_at', 'like', "$year-01%")),
            'Februari' => Tab::make()->query(fn ($query) => $query->where('created_at', 'like', "$year-02%")),
            'Maret' => Tab::make()->query(fn ($query) => $query->where('created_at', 'like', "$year-03%")),
            'April' => Tab::make()->query(fn ($query) => $query->where('created_at', 'like', "$year-04%")),
            'Mei' => Tab::make()->query(fn ($query) => $query->where('created_at', 'like', "$year-05%")),
            'Juni' => Tab::make()->query(fn ($query) => $query->where('created_at', 'like', "$year-06%")),
            'Juli' => Tab::make()->query(fn ($query) => $query->where('created_at', 'like', "$year-07%")),
            'Agustus' => Tab::make()->query(fn ($query) => $query->where('created_at', 'like', "$year-08%")),
            'September' => Tab::make()->query(fn ($query) => $query->where('created_at', 'like', "$year-09%")),
            'Oktober' => Tab::make()->query(fn ($query) => $query->where('created_at', 'like', "$year-10%")),
            'November' => Tab::make()->query(fn ($query) => $query->where('created_at', 'like', "$year-11%")),
            'Desember' => Tab::make()->query(fn ($query) => $query->where('created_at', 'like', "$year-12%")),
        ];
    }
}
