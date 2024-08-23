<?php

namespace App\Filament\Resources\UserTransactionResource\Pages;

use App\Filament\Resources\UserTransactionResource;
use Filament\Resources\Pages\ViewRecord;

class ViewUserTransaction extends ViewRecord
{
    protected static string $resource = UserTransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [

        ];
    }
}
