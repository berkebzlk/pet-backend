<?php

namespace App\Filament\Resources\VeterinaryProfiles\Pages;

use App\Filament\Resources\VeterinaryProfiles\VeterinaryProfileResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewVeterinaryProfile extends ViewRecord
{
    protected static string $resource = VeterinaryProfileResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
