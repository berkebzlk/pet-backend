<?php

namespace App\Modules\Veterinary\Models;

use App\Modules\User\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VeterinaryProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'clinic_name',
        'city',
        'phone',
        'website',
        'about',
        'specialties',
        'profile_photo',
        'cover_photo',
        'approval_status',
        'rejection_reason',
    ];

    protected $casts = [
        'specialties' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
