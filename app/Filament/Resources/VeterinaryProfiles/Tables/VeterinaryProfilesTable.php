<?php

namespace App\Filament\Resources\VeterinaryProfiles\Tables;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class VeterinaryProfilesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('profile_photo')
                    ->label('Fotoğraf')
                    ->circular()
                    ->disk('public'),
                TextColumn::make('clinic_name')
                    ->label('Klinik Adı')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('user.name')
                    ->label('Sahibi (Üye)')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('city')
                    ->label('Şehir')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('phone')
                    ->label('Telefon')
                    ->searchable(),
                TextColumn::make('approval_status')
                    ->label('Onay Durumu')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'approved' => 'success',
                        'pending' => 'warning',
                        'rejected' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'approved' => 'Onaylandı',
                        'pending' => 'Beklemede',
                        'rejected' => 'Reddedildi',
                        default => $state,
                    })
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Kayıt Tarihi')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('approval_status')
                    ->label('Onay Durumu')
                    ->options([
                        'pending' => 'Beklemede',
                        'approved' => 'Onaylandı',
                        'rejected' => 'Reddedildi',
                    ]),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
                Action::make('approve')
                    ->label('Onayla')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn($record) => $record->approval_status !== 'approved')
                    ->action(fn($record) => $record->update([
                        'approval_status' => 'approved',
                        'rejection_reason' => null
                    ])),
                Action::make('reject')
                    ->label('Reddet')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn($record) => $record->approval_status !== 'rejected')
                    ->form([
                        Textarea::make('rejection_reason')
                            ->label('Reddetme Gerekçesi')
                            ->required()
                            ->placeholder('Lütfen bu profili reddetme nedeninizi yazın...'),
                    ])
                    ->action(fn($record, array $data) => $record->update([
                        'approval_status' => 'rejected',
                        'rejection_reason' => $data['rejection_reason'],
                    ])),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
