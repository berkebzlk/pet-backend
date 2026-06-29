<?php

namespace App\Filament\Resources\Users\RelationManagers;

use App\Filament\Resources\VeterinaryProfiles\VeterinaryProfileResource;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class VeterinaryProfileRelationManager extends RelationManager
{
    protected static string $relationship = 'veterinaryProfile';

    protected static ?string $relatedResource = VeterinaryProfileResource::class;

    protected static ?string $title = 'Klinik Profili';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('clinic_name')
                    ->label('Klinik Adı')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('city')
                    ->label('Şehir')
                    ->searchable(),
                TextColumn::make('phone')
                    ->label('Telefon'),
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
                    }),
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
            ]);
    }
}
