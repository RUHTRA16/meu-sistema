<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Campaign extends Model
{
    protected $fillable = [
        'title',
        'status',
        'cover_path',
    ];

    public function characters(): HasMany
    {
        return $this->hasMany(Character::class);
    }
}
