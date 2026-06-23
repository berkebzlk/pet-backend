<?php

namespace App\Filament\Resources\VeterinaryProfiles\Pages;

use App\Filament\Resources\VeterinaryProfiles\VeterinaryProfileResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListVeterinaryProfiles extends ListRecords
{
    protected static string $resource = VeterinaryProfileResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
