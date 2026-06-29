<?php

namespace App\Filament\Resources\BreedingConnections\Pages;

use App\Filament\Resources\BreedingConnections\BreedingConnectionResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListBreedingConnections extends ListRecords
{
    protected static string $resource = BreedingConnectionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //
        ];
    }
}
