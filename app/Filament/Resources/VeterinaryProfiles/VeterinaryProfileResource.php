<?php

namespace App\Filament\Resources\VeterinaryProfiles;

use App\Filament\Resources\VeterinaryProfiles\Pages\CreateVeterinaryProfile;
use App\Filament\Resources\VeterinaryProfiles\Pages\EditVeterinaryProfile;
use App\Filament\Resources\VeterinaryProfiles\Pages\ListVeterinaryProfiles;
use App\Filament\Resources\VeterinaryProfiles\Pages\ViewVeterinaryProfile;
use App\Filament\Resources\VeterinaryProfiles\Schemas\VeterinaryProfileForm;
use App\Filament\Resources\VeterinaryProfiles\Schemas\VeterinaryProfileInfolist;
use App\Filament\Resources\VeterinaryProfiles\Tables\VeterinaryProfilesTable;
use App\Modules\Veterinary\Models\VeterinaryProfile;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class VeterinaryProfileResource extends Resource
{
    protected static ?string $model = VeterinaryProfile::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'Veterinary';

    public static function form(Schema $schema): Schema
    {
        return VeterinaryProfileForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return VeterinaryProfileInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return VeterinaryProfilesTable::configure($table);
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
            'index' => ListVeterinaryProfiles::route('/'),
            'create' => CreateVeterinaryProfile::route('/create'),
            'view' => ViewVeterinaryProfile::route('/{record}'),
            'edit' => EditVeterinaryProfile::route('/{record}/edit'),
        ];
    }
}
