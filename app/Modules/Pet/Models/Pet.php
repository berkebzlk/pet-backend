<?php

namespace App\Modules\Pet\Models;

use App\Modules\User\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Modules\Post\Models\Like;
use App\Modules\Post\Models\SavedPost;

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

    public function likes(): HasMany
    {
        return $this->hasMany(Like::class);
    }

    public function savedPosts(): HasMany
    {
        return $this->hasMany(SavedPost::class);
    }
}
