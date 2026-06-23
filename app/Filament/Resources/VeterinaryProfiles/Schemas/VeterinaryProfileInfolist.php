<?php

namespace App\Filament\Resources\VeterinaryProfiles\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class VeterinaryProfileInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('user.name')
                    ->label('User'),
                TextEntry::make('clinic_name'),
                TextEntry::make('city'),
                TextEntry::make('phone')
                    ->placeholder('-'),
                TextEntry::make('website')
                    ->placeholder('-'),
                TextEntry::make('about')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('profile_photo')
                    ->placeholder('-'),
                TextEntry::make('cover_photo')
                    ->placeholder('-'),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
