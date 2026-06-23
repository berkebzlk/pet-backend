<?php

namespace App\Filament\Resources\Users\RelationManagers;

use App\Filament\Resources\Pets\PetResource;
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;

class PetsRelationManager extends RelationManager
{
    protected static string $relationship = 'pets';

    protected static ?string $relatedResource = PetResource::class;

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image')
                    ->disk('public'),
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('type')
                    ->searchable(),
                TextColumn::make('breed'),
                TextColumn::make('gender')
                    ->badge(),
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
