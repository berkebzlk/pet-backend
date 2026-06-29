<?php

namespace App\Filament\Resources\VideoCalls\Pages;

use App\Filament\Resources\VideoCalls\VideoCallResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListVideoCalls extends ListRecords
{
    protected static string $resource = VideoCallResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //
        ];
    }
}
