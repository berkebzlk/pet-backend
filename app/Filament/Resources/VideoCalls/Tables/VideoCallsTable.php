<?php

namespace App\Filament\Resources\VideoCalls\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class VideoCallsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('caller.name')
                    ->label('Arayan Kullanıcı')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('receiver.name')
                    ->label('Aranan Kullanıcı')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Arama Durumu')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'accepted', 'ended' => 'success',
                        'pending' => 'warning',
                        'rejected', 'busy', 'no_answer' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'pending' => 'Bekleniyor',
                        'accepted' => 'Kabul Edildi',
                        'rejected' => 'Reddedildi',
                        'ended' => 'Sonlandırıldı',
                        'busy' => 'Meşgul',
                        'no_answer' => 'Cevapsız',
                        default => $state,
                    })
                    ->sortable(),
                TextColumn::make('room_name')
                    ->label('Oda Adı')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('started_at')
                    ->label('Başlangıç Zamanı')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('ended_at')
                    ->label('Bitiş Zamanı')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Arama Tarihi')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->label('Arama Durumu')
                    ->options([
                        'pending' => 'Bekleniyor',
                        'accepted' => 'Kabul Edildi',
                        'rejected' => 'Reddedildi',
                        'ended' => 'Sonlandırıldı',
                        'busy' => 'Meşgul',
                        'no_answer' => 'Cevapsız',
                    ]),
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
