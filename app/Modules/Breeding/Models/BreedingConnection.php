<?php

namespace App\Modules\Breeding\Models;

use App\Modules\Core\Enums\StatusEnum;
use App\Modules\Pet\Models\Pet;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BreedingConnection extends Model
{
    protected $table = 'breeding_connections';
    protected $fillable = [
        'initiator_pet_id',
        'target_pet_id',
        'status',
    ];

    protected $casts = [
        'status' => StatusEnum::class,
    ];

    public function initiatorPet(): BelongsTo
    {
        return $this->belongsTo(Pet::class, 'initiator_pet_id');
    }

    public function targetPet(): BelongsTo
    {
        return $this->belongsTo(Pet::class, 'target_pet_id');
    }
}
