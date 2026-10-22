<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\CampaignController;

Route::get('/', [HomeController::class, 'index'])
    ->name('home');

Route::get('/campanhas/nova', [CampaignController::class, 'create'])
    ->name('campaigns.create');

Route::post('/campanhas', [CampaignController::class, 'store'])
    ->name('campaigns.store');