<?php

namespace App\Filament\Resources\VeterinaryProfiles\Schemas;

use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class VeterinaryProfileInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Klinik Görselleri')
                    ->schema([
                        ImageEntry::make('profile_photo')
                            ->label('Profil Fotoğrafı')
                            ->disk('public'),
                        ImageEntry::make('cover_photo')
                            ->label('Kapak Fotoğrafı')
                            ->disk('public'),
                    ])
                    ->columns(2),

                Section::make('Klinik Bilgileri')
                    ->schema([
                        TextEntry::make('clinic_name')
                            ->label('Klinik Adı'),
                        TextEntry::make('user.name')
                            ->label('Sahibi (Üye)'),
                        TextEntry::make('city')
                            ->label('Şehir'),
                        TextEntry::make('phone')
                            ->label('Telefon')
                            ->placeholder('-'),
                        TextEntry::make('website')
                            ->label('Web Sitesi')
                            ->placeholder('-'),
                        TextEntry::make('specialties')
                            ->label('Uzmanlık Alanları')
                            ->badge()
                            ->placeholder('-'),
                        TextEntry::make('about')
                            ->label('Hakkında')
                            ->placeholder('-')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Section::make('Sistem & Onay Durumu')
                    ->schema([
                        TextEntry::make('approval_status')
                            ->label('Onay Durumu')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'approved' => 'success',
                                'pending' => 'warning',
                                'rejected' => 'danger',
                                default => 'gray',
                            })
                            ->formatStateUsing(fn (string $state): string => match ($state) {
                                'approved' => 'Onaylandı',
                                'pending' => 'Beklemede',
                                'rejected' => 'Reddedildi',
                                default => $state,
                            }),
                        TextEntry::make('created_at')
                            ->label('Kayıt Tarihi')
                            ->dateTime(),
                        TextEntry::make('rejection_reason')
                            ->label('Red Gerekçesi')
                            ->visible(fn ($record) => $record->approval_status === 'rejected')
                            ->columnSpanFull()
                            ->placeholder('-'),
                    ])
                    ->columns(2),
            ]);
    }
}
