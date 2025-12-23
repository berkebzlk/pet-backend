<?php

namespace App\Modules\Match\Models;

use App\Modules\Pet\Models\Pet;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PetMatch extends Model
{
    protected $table = 'matches';
    protected $fillable = [
        'initiator_pet_id',
        'target_pet_id',
        'status',
    ];

    public function initiator(): BelongsTo
    {
        return $this->belongsTo(Pet::class, 'initiator_pet_id');
    }

    public function target(): BelongsTo
    {
        return $this->belongsTo(Pet::class, 'target_pet_id');
    }
}
