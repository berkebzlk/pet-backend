<?php

namespace App\Filament\Resources\BreedingConnections\Schemas;

use App\Modules\Core\Enums\StatusEnum;
use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;

class BreedingConnectionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('initiator_pet_id')
                    ->relationship('initiatorPet', 'name')
                    ->required(),
                Select::make('target_pet_id')
                    ->relationship('targetPet', 'name')
                    ->required(),
                Select::make('status')
                    ->options(StatusEnum::class)
                    ->default('pending')
                    ->required(),
            ]);
    }
}
