<?php

namespace App\Filament\Resources\PackageResource\Pages;

use App\Filament\Resources\PackageResource;
use Filament\Resources\Pages\ViewRecord;
use JoseEspinal\RecordNavigation\Traits\HasRecordNavigation;

class ViewPackage extends ViewRecord
{
    use HasRecordNavigation;

    protected static string $resource = PackageResource::class;

    protected function getHeaderActions(): array
    {
        return array_merge(parent::getActions(), $this->getNavigationActions());
    }
}
