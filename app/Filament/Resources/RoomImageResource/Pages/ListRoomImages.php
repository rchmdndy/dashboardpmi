<?php

namespace App\Filament\Resources\RoomImageResource\Pages;

use App\Filament\Resources\RoomImageResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRoomImages extends ListRecords
{
    protected static string $resource = RoomImageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
