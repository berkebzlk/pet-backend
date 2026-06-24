<?php

namespace App\Filament\Resources\VeterinaryProfiles\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class VeterinaryProfileForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->label('Sahibi (Üye)')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->required(),
                TextInput::make('clinic_name')
                    ->label('Klinik Adı')
                    ->required(),
                TextInput::make('city')
                    ->label('Şehir')
                    ->required(),
                TextInput::make('phone')
                    ->label('Telefon')
                    ->tel(),
                TextInput::make('website')
                    ->label('Web Sitesi')
                    ->url(),
                Textarea::make('about')
                    ->label('Hakkında')
                    ->columnSpanFull(),
                TagsInput::make('specialties')
                    ->label('Uzmanlık Alanları'),
                FileUpload::make('profile_photo')
                    ->label('Profil Fotoğrafı')
                    ->image()
                    ->disk('public')
                    ->directory('clinics/profiles'),
                FileUpload::make('cover_photo')
                    ->label('Kapak Fotoğrafı')
                    ->image()
                    ->disk('public')
                    ->directory('clinics/covers'),
                Select::make('approval_status')
                    ->label('Onay Durumu')
                    ->options([
                        'pending' => 'Beklemede',
                        'approved' => 'Onaylandı',
                        'rejected' => 'Reddedildi',
                    ])
                    ->required()
                    ->default('pending'),
                Textarea::make('rejection_reason')
                    ->label('Red Nedeni')
                    ->columnSpanFull()
                    ->visible(fn ($get) => $get('approval_status') === 'rejected'),
            ]);
    }
}
