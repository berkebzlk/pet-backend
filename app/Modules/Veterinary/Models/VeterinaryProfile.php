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
        'average_rating',
        'reviews_count',
    ];

    protected $casts = [
        'specialties' => 'array',
        'average_rating' => 'decimal:2',
        'reviews_count' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function reviews(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(VeterinaryReview::class);
    }

    public function getSortableFields(): array
    {
        return ['clinic_name', 'average_rating', 'created_at', 'id'];
    }
}
