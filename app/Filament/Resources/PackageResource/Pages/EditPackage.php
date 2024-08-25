<?php

namespace App\Filament\Resources\PackageResource\Pages;

use App\Filament\Resources\PackageResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use JoseEspinal\RecordNavigation\Traits\HasRecordNavigation;

class EditPackage extends EditRecord
{
    use HasRecordNavigation;

    protected static string $resource = PackageResource::class;

    protected function getHeaderActions(): array
    {
        $existingActions = [
            // Your existing actions here...
            Actions\DeleteAction::make(),
        ];

        return array_merge($existingActions, $this->getNavigationActions());
    }
}
