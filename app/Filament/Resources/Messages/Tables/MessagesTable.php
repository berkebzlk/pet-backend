<?php

namespace App\Filament\Resources\Messages\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class MessagesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('sender.name')
                    ->label('Gönderen Evcil Hayvan')
                    ->description(fn($record) => $record->sender ? '@' . $record->sender->username : null)
                    ->searchable()
                    ->sortable(),
                TextColumn::make('receiver.name')
                    ->label('Alıcı Evcil Hayvan')
                    ->description(fn($record) => $record->receiver ? '@' . $record->receiver->username : null)
                    ->searchable()
                    ->sortable(),
                TextColumn::make('content')
                    ->label('Mesaj İçeriği')
                    ->limit(50)
                    ->searchable()
                    ->placeholder('-'),
                TextColumn::make('read_at')
                    ->label('Okunma Tarihi')
                    ->dateTime()
                    ->placeholder('Okunmadı')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Gönderilme Tarihi')
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                //
            ])
            ->actions([
                ViewAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
