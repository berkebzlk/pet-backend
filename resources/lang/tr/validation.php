<?php

return [
    'required' => ':attribute alanı zorunludur',
    'string' => ':attribute alanı metin olmalıdır',
    'unique' => ':attribute alanı zaten mevcut',
    'email' => ':attribute alanı e-posta formatı olmalıdır',
    'max' => ':attribute alanı en fazla :max karakter olmalıdır',
    'min' => ':attribute alanı en az :min karakter olmalıdır',
    'confirmed' => ':attribute alanı eşleşmiyor',
    'invalid' => 'Bazı alanlar geçersiz',

    'attributes' => [
        'id' => 'ID',
        'name' => 'Ad',
        'email' => 'E-posta',
        'password' => 'Şifre',
        'password_confirmation' => 'Şifre (Tekrar)',
        'role' => 'Rol',
        'permissions' => 'Yetkiler',
        'title' => 'Başlık',
        'content' => 'İçerik',
        'image' => 'Resim',
        'status' => 'Durum',
        'created_at' => 'Oluşturulma Tarihi',
        'updated_at' => 'Güncellenme Tarihi',
        'deleted_at' => 'Silinme Tarihi',
    ]
];
