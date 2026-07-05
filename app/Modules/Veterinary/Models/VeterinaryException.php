<?php

namespace App\Modules\Veterinary\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VeterinaryException extends Model
{
    use HasFactory;

    protected $fillable = [
        'veterinary_profile_id',
        'date',
        'is_working',
        'start_time',
        'end_time',
    ];

    protected $casts = [
        'is_working' => 'boolean',
        'date' => 'date',
    ];

    public function veterinaryProfile(): BelongsTo
    {
        return $this->belongsTo(VeterinaryProfile::class);
    }
}
