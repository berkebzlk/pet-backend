<?php

namespace App\Filament\Resources\VeterinaryProfiles\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class VeterinaryProfileForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required(),
                TextInput::make('clinic_name')
                    ->required(),
                TextInput::make('city')
                    ->required(),
                TextInput::make('phone')
                    ->tel(),
                TextInput::make('website')
                    ->url(),
                Textarea::make('about')
                    ->columnSpanFull(),
                TextInput::make('specialties'),
                TextInput::make('profile_photo'),
                TextInput::make('cover_photo'),
            ]);
    }
}
