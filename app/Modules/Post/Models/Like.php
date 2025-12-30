<?php

namespace App\Modules\Post\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Like extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'post_id'];

    public function user()
    {
        return $this->belongsTo(\App\Modules\User\Models\User::class);
    }

    public function post()
    {
        return $this->belongsTo(Post::class);
    }
}
