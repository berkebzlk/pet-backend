<?php

namespace App\Filament\Resources\PetMatches\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class PetMatchesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('initiatorPet.name')
                    ->label('Başlatan Evcil Hayvan')
                    ->description(fn($record) => $record->initiatorPet ? '@' . $record->initiatorPet->username : null)
                    ->searchable()
                    ->sortable(),
                TextColumn::make('targetPet.name')
                    ->label('Hedef Evcil Hayvan')
                    ->description(fn($record) => $record->targetPet ? '@' . $record->targetPet->username : null)
                    ->searchable()
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Durum')
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
                    })
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Tarih')
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->label('Durum')
                    ->options([
                        4 => 'Beklemede',
                        5 => 'Kabul Edildi',
                        6 => 'Reddedildi',
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
