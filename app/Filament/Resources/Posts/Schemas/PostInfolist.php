<?php

namespace App\Filament\Resources\Posts\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Schemas\Schema;

class PostInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                ImageEntry::make('image_url')
                    ->label('Gönderi Fotoğrafı')
                    ->disk('public')
                    ->maxWidth(300),

                TextEntry::make('author')
                    ->label('Gönderen (Yazar)')
                    ->getStateUsing(fn($record) => $record->pet ? $record->pet->name . ' (@' . $record->pet->username . ')' : $record->veterinaryProfile->clinic_name),

                TextEntry::make('likes_count')
                    ->label('Beğeni Sayısı')
                    ->getStateUsing(fn($record) => $record->likes()->count()),

                TextEntry::make('comments_count')
                    ->label('Yorum Sayısı')
                    ->getStateUsing(fn($record) => $record->comments()->count()),

                TextEntry::make('description')
                    ->label('Açıklama')
                    ->placeholder('Açıklama belirtilmemiş.'),

                TextEntry::make('created_at')
                    ->label('Yayınlanma Tarihi')
                    ->dateTime(),
            ]);
    }
}
