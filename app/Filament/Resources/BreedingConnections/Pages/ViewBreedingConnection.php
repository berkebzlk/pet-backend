<?php

namespace App\Filament\Resources\BreedingConnections\Pages;

use App\Filament\Resources\BreedingConnections\BreedingConnectionResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewBreedingConnection extends ViewRecord
{
    protected static string $resource = BreedingConnectionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //
        ];
    }
}
