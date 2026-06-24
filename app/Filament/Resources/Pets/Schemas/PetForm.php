<?php

namespace App\Filament\Resources\Pets\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class PetForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required(),
                TextInput::make('name')
                    ->required(),
                TextInput::make('type')
                    ->required(),
                TextInput::make('breed'),
                Select::make('gender')
                    ->options(['male' => 'Male', 'female' => 'Female'])
                    ->required(),
                DatePicker::make('birthdate')
                    ->required(),
                TextInput::make('weight')
                    ->numeric(),
                Toggle::make('is_neutered')
                    ->required(),
                Textarea::make('bio')
                    ->columnSpanFull(),
                FileUpload::make('image')
                    ->image()
                    ->disk('public'),
                TextInput::make('username')
                    ->required(),
                TextInput::make('posts_count')
                    ->required()
                    ->numeric()
                    ->default(0)
                    ->disabled(),
                TextInput::make('match_count')
                    ->required()
                    ->numeric()
                    ->default(0)
                    ->disabled(),
                Toggle::make('is_breeding_available')
                    ->required(),
            ]);
    }
}
