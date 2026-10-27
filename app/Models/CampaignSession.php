<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CampaignSession extends Model
{
    protected $fillable = [
        'campaign_id',
        'session_number',
        'title',
        'scheduled_at',
        'summary',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'scheduled_at' => 'date',
        ];
    }

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }
}