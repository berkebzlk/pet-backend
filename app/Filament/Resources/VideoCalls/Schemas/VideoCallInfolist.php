<?php

namespace App\Filament\Resources\VideoCalls\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class VideoCallInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Arama Detayları')
                    ->schema([
                        TextEntry::make('caller.name')
                            ->label('Arayan Kullanıcı'),
                        TextEntry::make('receiver.name')
                            ->label('Aranan Kullanıcı'),
                        TextEntry::make('status')
                            ->label('Arama Durumu')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'accepted', 'ended' => 'success',
                                'pending' => 'warning',
                                'rejected', 'busy', 'no_answer' => 'danger',
                                default => 'gray',
                            })
                            ->formatStateUsing(fn (string $state): string => match ($state) {
                                'pending' => 'Bekleniyor',
                                'accepted' => 'Kabul Edildi',
                                'rejected' => 'Reddedildi',
                                'ended' => 'Sonlandırıldı',
                                'busy' => 'Meşgul',
                                'no_answer' => 'Cevapsız',
                                default => $state,
                            }),
                        TextEntry::make('room_name')
                            ->label('Oda Adı')
                            ->placeholder('-'),
                        TextEntry::make('started_at')
                            ->label('Başlangıç Zamanı')
                            ->dateTime()
                            ->placeholder('-'),
                        TextEntry::make('ended_at')
                            ->label('Bitiş Zamanı')
                            ->dateTime()
                            ->placeholder('-'),
                        TextEntry::make('created_at')
                            ->label('Arama Tarihi')
                            ->dateTime(),
                        TextEntry::make('updated_at')
                            ->label('Son Güncelleme Tarihi')
                            ->dateTime(),
                    ])
                    ->columns(2)
            ]);
    }
}
