<?php

namespace App\Filament\Resources\VideoCalls\Pages;

use App\Filament\Resources\VideoCalls\VideoCallResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewVideoCall extends ViewRecord
{
    protected static string $resource = VideoCallResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //
        ];
    }
}
