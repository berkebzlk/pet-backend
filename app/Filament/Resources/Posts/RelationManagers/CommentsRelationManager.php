<?php

namespace App\Filament\Resources\Posts\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CommentsRelationManager extends RelationManager
{
    protected static string $relationship = 'comments';

    protected static ?string $title = 'Yorumlar';

    public function isReadOnly(): bool
    {
        return false;
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('pet_id')
                    ->relationship('pet', 'name')
                    ->required()
                    ->searchable()
                    ->disabled()
                    ->label('Yazıcı Pet'),
                Textarea::make('content')
                    ->label('Yorum İçeriği')
                    ->required()
                    ->rows(3)
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('content')
            ->columns([
                ImageColumn::make('pet.image')
                    ->label('Fotoğraf')
                    ->circular()
                    ->disk('public')
                    ->defaultImageUrl(url('/images/default-avatar.png')),
                TextColumn::make('pet.name')
                    ->label('Yazan Pet')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('content')
                    ->label('Yorum')
                    ->searchable()
                    ->wrap(),
                TextColumn::make('created_at')
                    ->label('Yazılma Tarihi')
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                //
            ])
            ->headerActions([])
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
