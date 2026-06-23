<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Schemas\Schema;
use Filament\Infolists\Components\TextEntry;

class UserInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('name'),
                TextEntry::make('username'),
                TextEntry::make('email'),
                TextEntry::make('roles.name')
                    ->badge()
                    ->label('Roles'),
                TextEntry::make('created_at')
                    ->dateTime(),
            ]);
    }
}
