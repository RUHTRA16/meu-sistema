<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

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

    public function investigationEntries(): HasMany
    {
        return $this->hasMany(InvestigationEntry::class)
            ->latest();
    }

    public function sessions(): HasMany
    {
        return $this->hasMany(CampaignSession::class)
            ->orderBy('session_number');
    }

    public function automationSetting(): HasOne
    {
        return $this->hasOne(CampaignAutomationSetting::class);
    }
}