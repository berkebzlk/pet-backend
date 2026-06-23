<?php

namespace App\Filament\Resources\Users\RelationManagers;

use App\Filament\Resources\VeterinaryProfiles\VeterinaryProfileResource;
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;

class VeterinaryProfileRelationManager extends RelationManager
{
    protected static string $relationship = 'veterinaryProfile';

    protected static ?string $relatedResource = VeterinaryProfileResource::class;

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('clinic_name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('city')
                    ->searchable(),
                TextColumn::make('phone'),
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
