<?php

namespace App\Modules\Message\Models;

use App\Modules\Pet\Models\Pet;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Message extends Model
{
    protected $fillable = [
        'sender_pet_id',
        'receiver_pet_id',
        'content',
        'read_at',
    ];

    protected $casts = [
        'read_at' => 'datetime',
    ];

    public function sender(): BelongsTo
    {
        return $this->belongsTo(Pet::class, 'sender_pet_id');
    }

    public function receiver(): BelongsTo
    {
        return $this->belongsTo(Pet::class, 'receiver_pet_id');
    }
}
