<?php

use App\Modules\Core\Enums\StatusEnum;

return [
    StatusEnum::SUCCESS->value => 'Başarılı',
    StatusEnum::ERROR->value => 'Hata',
    StatusEnum::WARNING->value => 'Uyarı',
    StatusEnum::INFO->value => 'Bilgi',
];
