<?php

use App\Modules\Core\Enums\StatusEnum;

return [
    StatusEnum::SUCCESS->value => 'Başarılı',
    StatusEnum::ERROR->value => 'Hata',
    StatusEnum::WARNING->value => 'Uyarı',
    StatusEnum::INFO->value => 'Bilgi',
    StatusEnum::PENDING->value => 'Beklemede',
    StatusEnum::ACCEPTED->value => 'Kabul Edildi',
    StatusEnum::REJECTED->value => 'Reddedildi',
];
