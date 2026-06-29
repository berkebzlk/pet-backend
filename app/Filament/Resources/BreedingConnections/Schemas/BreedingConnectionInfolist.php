<?php

namespace App\Filament\Resources\BreedingConnections\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class BreedingConnectionInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Çiftleşme İsteği Detayları')
                    ->schema([
                        TextEntry::make('initiatorPet.name')
                            ->label('Başlatan Evcil Hayvan')
                            ->description(fn($record) => $record->initiatorPet ? '@' . $record->initiatorPet->username : null),
                        TextEntry::make('targetPet.name')
                            ->label('Hedef Evcil Hayvan')
                            ->description(fn($record) => $record->targetPet ? '@' . $record->targetPet->username : null),
                        TextEntry::make('status')
                            ->label('İstek Durumu')
                            ->badge()
                            ->color(fn($state) => match ($state?->value) {
                                5 => 'success', // ACCEPTED
                                6 => 'danger',  // REJECTED
                                4 => 'warning', // PENDING
                                default => 'gray',
                            })
                            ->formatStateUsing(fn($state) => match ($state?->value) {
                                5 => 'Kabul Edildi',
                                6 => 'Reddedildi',
                                4 => 'Beklemede',
                                default => $state?->label() ?? '-',
                            }),
                        TextEntry::make('created_at')
                            ->label('Oluşturulma Tarihi')
                            ->dateTime(),
                        TextEntry::make('updated_at')
                            ->label('Son Güncelleme Tarihi')
                            ->dateTime(),
                    ])
                    ->columns(2)
            ]);
    }
}
