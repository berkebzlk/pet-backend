<?php

namespace App\Filament\Resources\BreedingConnections;

use App\Filament\Resources\BreedingConnections\Pages\ListBreedingConnections;
use App\Filament\Resources\BreedingConnections\Pages\ViewBreedingConnection;
use App\Filament\Resources\BreedingConnections\Schemas\BreedingConnectionForm;
use App\Filament\Resources\BreedingConnections\Schemas\BreedingConnectionInfolist;
use App\Filament\Resources\BreedingConnections\Tables\BreedingConnectionsTable;
use App\Modules\Breeding\Models\BreedingConnection;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class BreedingConnectionResource extends Resource
{
    protected static ?string $model = BreedingConnection::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedHeart;

    protected static ?string $navigationLabel = 'Çiftleşme Talepleri';

    protected static ?string $modelLabel = 'Çiftleşme Talebi';

    protected static ?string $pluralModelLabel = 'Çiftleşme Talepleri';

    protected static string|\UnitEnum|null $navigationGroup = 'Sosyal Modüller';

    public static function form(Schema $schema): Schema
    {
        return BreedingConnectionForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return BreedingConnectionInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BreedingConnectionsTable::configure($table);
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
            'index' => ListBreedingConnections::route('/'),
            'view' => ViewBreedingConnection::route('/{record}'),
        ];
    }
}
