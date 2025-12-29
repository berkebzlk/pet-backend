<?php

namespace App\Modules\Pet\Models;

use App\Modules\User\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pet extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'type',
        'breed',
        'gender',
        'birthdate',
        'weight',
        'is_neutered',
        'bio',
        'image',
        'username',
        'posts_count',
        'match_count',
    ];

    protected $casts = [
        'birthdate' => 'date',
        'is_neutered' => 'boolean',
        'weight' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
