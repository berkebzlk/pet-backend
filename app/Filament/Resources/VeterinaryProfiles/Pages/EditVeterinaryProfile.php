<?php

namespace App\Filament\Resources\VeterinaryProfiles\Pages;

use App\Filament\Resources\VeterinaryProfiles\VeterinaryProfileResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditVeterinaryProfile extends EditRecord
{
    protected static string $resource = VeterinaryProfileResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
