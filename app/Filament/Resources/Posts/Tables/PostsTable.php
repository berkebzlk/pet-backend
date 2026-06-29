<?php

namespace App\Filament\Resources\Posts\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class PostsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image_url')
                    ->label('Fotoğraf')
                    ->disk('public'),
                TextColumn::make('type')
                    ->label('Gönderi Tipi')
                    ->badge()
                    ->getStateUsing(fn($record) => $record->pet_id ? 'pet' : ($record->veterinary_profile_id ? 'clinic' : '-'))
                    ->color(fn(string $state): string => match ($state) {
                        'pet' => 'info',
                        'clinic' => 'success',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'pet' => 'Evcil Hayvan',
                        'clinic' => 'Klinik / Veteriner',
                        default => 'Bilinmiyor',
                    }),
                TextColumn::make('author')
                    ->label('Gönderen')
                    ->getStateUsing(fn($record) => $record->pet ? $record->pet->name . ' (@' . $record->pet->username . ')' : ($record->veterinaryProfile ? $record->veterinaryProfile->clinic_name : '-'))
                    ->searchable(query: function ($query, string $search) {
                        $query->whereHas('pet', function ($q) use ($search) {
                            $q->where('name', 'like', "%{$search}%")
                                ->orWhere('username', 'like', "%{$search}%");
                        })->orWhereHas('veterinaryProfile', function ($q) use ($search) {
                            $q->where('clinic_name', 'like', "%{$search}%");
                        });
                    }),
                TextColumn::make('description')
                    ->label('Açıklama')
                    ->limit(50)
                    ->searchable()
                    ->placeholder('-'),
                TextColumn::make('likes_count')
                    ->label('Beğeni')
                    ->counts('likes')
                    ->sortable(),
                TextColumn::make('comments_count')
                    ->label('Yorum')
                    ->counts('comments')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Yayınlanma Tarihi')
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('type')
                    ->label('Gönderici Türü')
                    ->options([
                        'pet' => 'Evcil Hayvan',
                        'clinic' => 'Klinik / Veteriner',
                    ])
                    ->query(function ($query, array $data) {
                        if ($data['value'] === 'pet') {
                            $query->whereNotNull('pet_id');
                        } elseif ($data['value'] === 'clinic') {
                            $query->whereNotNull('veterinary_profile_id');
                        }
                    }),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
