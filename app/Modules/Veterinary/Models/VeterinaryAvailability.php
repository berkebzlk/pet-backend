<?php

namespace App\Modules\Veterinary\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VeterinaryAvailability extends Model
{
    use HasFactory;

    protected $fillable = [
        'veterinary_profile_id',
        'day_of_week',
        'start_time',
        'end_time',
        'slot_duration',
    ];

    public function veterinaryProfile(): BelongsTo
    {
        return $this->belongsTo(VeterinaryProfile::class);
    }
}
