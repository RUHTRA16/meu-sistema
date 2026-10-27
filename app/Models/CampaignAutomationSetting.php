<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CampaignAutomationSetting extends Model
{
    protected $fillable = [
        'campaign_id',
        'dice_rolling',
        'initiative',
        'npc_control',
        'ai_actions',
    ];

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }
}