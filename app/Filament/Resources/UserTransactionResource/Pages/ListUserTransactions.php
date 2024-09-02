<?php

namespace App\Filament\Resources\UserTransactionResource\Pages;

use App\Filament\Resources\UserTransactionResource;
use Filament\Actions;
use Filament\Pages\Concerns\ExposesTableToWidgets;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Gate;

class ListUserTransactions extends ListRecords
{
    protected static string $resource = UserTransactionResource::class;

    use ExposesTableToWidgets;

    protected function getHeaderActions(): array
    {
        return array_filter([Gate::allows('admin') ? Actions\CreateAction::make() : null]);
    }

    protected function getHeaderWidgets(): array
    {
        return UserTransactionResource::getWidgets();
    }

    public function getTabs(): array
    {
        return [
            null => Tab::make('All'),
            'Success' => Tab::make()->query(fn ($query) => $query->where('transaction_status', 'success')),
            'Pending' => Tab::make()->query(fn ($query) => $query->where('transaction_status', 'pending')),
            'Failed' => Tab::make()->query(fn ($query) => $query->where('transaction_status', 'failed')),
        ];
    }
}
