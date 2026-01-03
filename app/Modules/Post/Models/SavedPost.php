<?php

namespace App\Modules\Post\Models;

use App\Modules\Pet\Models\Pet;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SavedPost extends Model
{
    use HasFactory;

    protected $fillable = ['pet_id', 'post_id'];

    public function pet()
    {
        return $this->belongsTo(Pet::class);
    }

    public function post()
    {
        return $this->belongsTo(Post::class);
    }
}
