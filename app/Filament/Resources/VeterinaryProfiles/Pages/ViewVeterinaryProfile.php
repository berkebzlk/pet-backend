<?php

namespace App\Filament\Resources\VeterinaryProfiles\Pages;

use App\Filament\Resources\VeterinaryProfiles\VeterinaryProfileResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;

class ViewVeterinaryProfile extends ViewRecord
{
    protected static string $resource = VeterinaryProfileResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
            Action::make('approve')
                ->label('Onayla')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->requiresConfirmation()
                ->visible(fn ($record) => $record->approval_status !== 'approved')
                ->action(fn ($record) => $record->update([
                    'approval_status' => 'approved',
                    'rejection_reason' => null
                ])),
            Action::make('reject')
                ->label('Reddet')
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->visible(fn ($record) => $record->approval_status !== 'rejected')
                ->form([
                    Textarea::make('rejection_reason')
                        ->label('Reddetme Gerekçesi')
                        ->required()
                        ->placeholder('Lütfen red nedenini yazın...'),
                ])
                ->action(fn ($record, array $data) => $record->update([
                    'approval_status' => 'rejected',
                    'rejection_reason' => $data['rejection_reason'],
                ])),
        ];
    }
}
