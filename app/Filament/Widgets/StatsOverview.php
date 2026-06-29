<?php

namespace App\Filament\Widgets;

use App\Modules\Core\Enums\StatusEnum;
use App\Modules\Match\Models\PetMatch;
use App\Modules\Pet\Models\Pet;
use App\Modules\User\Models\User;
use App\Modules\Veterinary\Models\VeterinaryProfile;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Toplam Üye', User::count())
                ->description('Platforma kayıtlı toplam kullanıcı')
                ->descriptionIcon('heroicon-m-users')
                ->color('primary'),

            Stat::make('Toplam Evcil Hayvan', Pet::count())
                ->description('Kayıtlı sevimli dostlarımızın sayısı')
                ->descriptionIcon('heroicon-m-heart')
                ->color('danger'),

            Stat::make('Aktif Eşleşmeler', PetMatch::where('status', StatusEnum::ACCEPTED)->count())
                ->description('Kabul edilmiş pet eşleşmeleri')
                ->descriptionIcon('heroicon-m-sparkles')
                ->color('success'),

            Stat::make('Onaylı Klinikler', VeterinaryProfile::where('approval_status', 'approved')->count())
                ->description('Aktif hizmet veren veteriner klinikleri')
                ->descriptionIcon('heroicon-m-check-badge')
                ->color('info'),
        ];
    }
}
