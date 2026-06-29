<?php

namespace App\Filament\Resources\PetMatches\Pages;

use App\Filament\Resources\PetMatches\PetMatchResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewPetMatch extends ViewRecord
{
    protected static string $resource = PetMatchResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //
        ];
    }
}
