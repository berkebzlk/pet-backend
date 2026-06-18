<?php

namespace App\Modules\Post\Models;

use App\Modules\Pet\Models\Pet;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'pet_id',
        'veterinary_profile_id',
        'image_url',
        'description',
    ];

    public function pet()
    {
        return $this->belongsTo(Pet::class);
    }

    public function veterinaryProfile()
    {
        return $this->belongsTo(\App\Modules\Veterinary\Models\VeterinaryProfile::class);
    }

    public function likes()
    {
        return $this->hasMany(Like::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function savedBy()
    {
        return $this->hasMany(SavedPost::class);
    }


}
