<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\CampaignController;
use App\Http\Controllers\CharacterController;

Route::get('/', [HomeController::class, 'index'])
    ->name('home');

Route::get('/campanhas/nova', [CampaignController::class, 'create'])
    ->name('campaigns.create');

    
Route::delete('/campanhas/{campaign}',[CampaignController::class, 'destroy'])
    ->name('campaigns.destroy');


Route::post('/campanhas', [CampaignController::class, 'store'])
    ->name('campaigns.store');

Route::get('/campanhas/{campaign}/personagens/novo',
    [CharacterController::class, 'create']
)->name('characters.create');

Route::post('/campanhas/{campaign}/personagens',
    [CharacterController::class, 'store']
)->name('characters.store');

Route::get(
    '/campanhas/{campaign}/personagens/{character}/detalhes',
    [CharacterController::class, 'details']
)->name('characters.details');


Route::put(
    '/campanhas/{campaign}/personagens/{character}/detalhes',
    [CharacterController::class, 'updateDetails']
)->name('characters.details.update');


Route::get(
    '/campanhas/{campaign}/personagens/{character}/revisao',
    [CharacterController::class, 'review']
)->name('characters.review');


Route::post(
    '/campanhas/{campaign}/personagens/{character}/confirmar',
    [CharacterController::class, 'confirm']
)->name('characters.confirm');

Route::delete(
    '/campanhas/{campaign}/personagens/{character}',
    [CharacterController::class, 'destroy']
)->name('characters.destroy');
