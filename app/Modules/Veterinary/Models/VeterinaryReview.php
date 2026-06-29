<?php

namespace App\Modules\Veterinary\Models;

use App\Modules\Pet\Models\Pet;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VeterinaryReview extends Model
{
    use HasFactory;

    protected $fillable = [
        'veterinary_profile_id',
        'pet_id',
        'rating',
        'comment',
    ];

    protected $casts = [
        'rating' => 'integer',
    ];

    public function veterinaryProfile(): BelongsTo
    {
        return $this->belongsTo(VeterinaryProfile::class);
    }

    public function pet(): BelongsTo
    {
        return $this->belongsTo(Pet::class);
    }
}
