<?php

use App\Modules\Core\Enums\HttpStatusEnum;

return [
    HttpStatusEnum::OK->value => 'Başarılı',
    HttpStatusEnum::CREATED->value => 'Oluşturuldu',
    HttpStatusEnum::NO_CONTENT->value => 'İçerik Yok',
    HttpStatusEnum::BAD_REQUEST->value => 'Hatalı İstek',
    HttpStatusEnum::UNAUTHORIZED->value => 'Yetkisiz Erişim',
    HttpStatusEnum::FORBIDDEN->value => 'Erişim Yasağı',
    HttpStatusEnum::NOT_FOUND->value => ':attribute bulunamadı',
    HttpStatusEnum::METHOD_NOT_ALLOWED->value => 'Yöntem Yasağı',
    HttpStatusEnum::CONFLICT->value => 'Çakışma',
    HttpStatusEnum::UNPROCESSABLE_ENTITY->value => 'İşlenemez İçerik',
    HttpStatusEnum::INTERNAL_SERVER_ERROR->value => 'Sunucu Hatası',
    HttpStatusEnum::SERVICE_UNAVAILABLE->value => 'Hizmet Mevcut Değil',
    HttpStatusEnum::GATEWAY_TIMEOUT->value => 'Ağ Geçidi Zaman Aşımı',
];