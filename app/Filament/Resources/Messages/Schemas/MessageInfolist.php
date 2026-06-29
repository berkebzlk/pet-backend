<?php

namespace App\Filament\Resources\Messages\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class MessageInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Mesaj Detayları')
                    ->schema([
                        TextEntry::make('sender.name')
                            ->label('Gönderen Evcil Hayvan')
                            ->description(fn($record) => $record->sender ? '@' . $record->sender->username : null),
                        TextEntry::make('receiver.name')
                            ->label('Alıcı Evcil Hayvan')
                            ->description(fn($record) => $record->receiver ? '@' . $record->receiver->username : null),
                        TextEntry::make('content')
                            ->label('Mesaj İçeriği')
                            ->columnSpanFull()
                            ->placeholder('-'),
                        TextEntry::make('read_at')
                            ->label('Okunma Tarihi')
                            ->dateTime()
                            ->placeholder('Okunmadı'),
                        TextEntry::make('created_at')
                            ->label('Gönderilme Tarihi')
                            ->dateTime(),
                        TextEntry::make('updated_at')
                            ->label('Son Güncelleme Tarihi')
                            ->dateTime(),
                    ])
                    ->columns(2)
            ]);
    }
}
