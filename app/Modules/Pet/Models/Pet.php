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

    public function posts(): HasMany
    {
        return $this->hasMany(\App\Modules\Post\Models\Post::class);
    }

    public function initiatedMatches(): HasMany
    {
        return $this->hasMany(\App\Modules\Match\Models\PetMatch::class, 'initiator_pet_id');
    }

    public function receivedMatches(): HasMany
    {
        return $this->hasMany(\App\Modules\Match\Models\PetMatch::class, 'target_pet_id');
    }

    public function receivedLikes(): \Illuminate\Database\Eloquent\Relations\HasManyThrough
    {
        return $this->hasManyThrough(
            \App\Modules\Post\Models\Like::class,
            \App\Modules\Post\Models\Post::class,
            'pet_id', // Foreign key on posts table...
            'post_id', // Foreign key on likes table...
            'id', // Local key on pets table...
            'id' // Local key on posts table...
        );
    }
}
