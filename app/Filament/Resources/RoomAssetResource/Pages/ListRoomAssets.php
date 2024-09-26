<?php

namespace App\Filament\Resources\RoomAssetResource\Pages;

use App\Filament\Resources\RoomAssetResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRoomAssets extends ListRecords
{
    protected static string $resource = RoomAssetResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
