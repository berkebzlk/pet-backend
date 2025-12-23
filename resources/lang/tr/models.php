<?php

use App\Modules\Match\Models\PetMatch;
use App\Modules\Pet\Models\Pet;
use App\Modules\Role\Models\Role;
use App\Modules\User\Models\User;

return [
    Role::class => 'Rol',
    User::class => 'Kullanıcı',
    Pet::class => 'Hayvan',
    PetMatch::class => 'Eşleşme',
];