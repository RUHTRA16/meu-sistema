<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Character extends Model
{
    protected $fillable = [
        'campaign_id',
        'name',
        'age',
        'backstory',
        'appearance',
        'personality',
        'creation_status',
        'agi',
        'forca',
        'intelecto',
        'presenca',
        'vigor',
        'character_class',
        'origin',
        'nex',
    ];

            protected function casts(): array
        {
            return [
            'age' => 'integer',
            'nex' => 'integer',
            'agi' => 'integer',
            'forca' => 'integer',
            'intelecto' => 'integer',
            'presenca' => 'integer',
            'vigor' => 'integer',
            ];  
        }

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }
}
