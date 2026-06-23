<?php

namespace App\Filament\Resources\Pets\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class PetInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('user.name')
                    ->label('User'),
                TextEntry::make('name'),
                TextEntry::make('type'),
                TextEntry::make('breed')
                    ->placeholder('-'),
                TextEntry::make('gender')
                    ->badge(),
                TextEntry::make('birthdate')
                    ->date(),
                TextEntry::make('weight')
                    ->numeric()
                    ->placeholder('-'),
                IconEntry::make('is_neutered')
                    ->boolean(),
                TextEntry::make('bio')
                    ->placeholder('-')
                    ->columnSpanFull(),
                ImageEntry::make('image')
                    ->disk('public')
                    ->placeholder('-'),
                TextEntry::make('username'),
                TextEntry::make('posts_count')
                    ->numeric(),
                TextEntry::make('match_count')
                    ->numeric(),
                IconEntry::make('is_breeding_available')
                    ->boolean(),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
