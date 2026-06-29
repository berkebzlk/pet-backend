<?php

namespace App\Filament\Resources\PetMatches;

use App\Filament\Resources\PetMatches\Pages\ListPetMatches;
use App\Filament\Resources\PetMatches\Pages\ViewPetMatch;
use App\Filament\Resources\PetMatches\Schemas\PetMatchForm;
use App\Filament\Resources\PetMatches\Schemas\PetMatchInfolist;
use App\Filament\Resources\PetMatches\Tables\PetMatchesTable;
use App\Modules\Match\Models\PetMatch;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class PetMatchResource extends Resource
{
    protected static ?string $model = PetMatch::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedSparkles;

    protected static ?string $navigationLabel = 'Eşleşmeler';

    protected static ?string $modelLabel = 'Eşleşme';

    protected static ?string $pluralModelLabel = 'Eşleşmeler';

    protected static string|\UnitEnum|null $navigationGroup = 'Sosyal Modüller';

    public static function form(Schema $schema): Schema
    {
        return PetMatchForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return PetMatchInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PetMatchesTable::configure($table);
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
            'index' => ListPetMatches::route('/'),
            'view' => ViewPetMatch::route('/{record}'),
        ];
    }
}
