<?php

namespace App\Filament\Resources\VideoCalls;

use App\Filament\Resources\VideoCalls\Pages\ListVideoCalls;
use App\Filament\Resources\VideoCalls\Pages\ViewVideoCall;
use App\Filament\Resources\VideoCalls\Schemas\VideoCallForm;
use App\Filament\Resources\VideoCalls\Schemas\VideoCallInfolist;
use App\Filament\Resources\VideoCalls\Tables\VideoCallsTable;
use App\Modules\VideoCall\Models\VideoCall;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class VideoCallResource extends Resource
{
    protected static ?string $model = VideoCall::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedVideoCamera;

    protected static ?string $navigationLabel = 'Görüntülü Aramalar';

    protected static ?string $modelLabel = 'Görüntülü Arama';

    protected static ?string $pluralModelLabel = 'Görüntülü Aramalar';

    protected static string|\UnitEnum|null $navigationGroup = 'Sistem Logları';

    public static function form(Schema $schema): Schema
    {
        return VideoCallForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return VideoCallInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return VideoCallsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListVideoCalls::route('/'),
            'view' => ViewVideoCall::route('/{record}'),
        ];
    }
}
