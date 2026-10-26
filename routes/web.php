<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\CampaignController;
use App\Http\Controllers\CharacterController;
use App\Http\Controllers\ChatController;

Route::get('/mestre-ia', function () {
    return view('chat');
})->name('chat');

Route::post('/mestre-ia/mensagem', [ChatController::class, 'send'])
    ->name('chat.send');

Route::get('/', [HomeController::class, 'index'])
    ->name('home');

// Criação de campanhas
Route::get('/campanhas/nova', [CampaignController::class, 'create'])
    ->name('campaigns.create');

Route::post('/campanhas', [CampaignController::class, 'store'])
    ->name('campaigns.store');

// Edição da capa da campanha
Route::get('/campanhas/{campaign}/capa', [CampaignController::class, 'editCover'])
    ->name('campaigns.cover.edit');

Route::put('/campanhas/{campaign}/capa', [CampaignController::class, 'updateCover'])
    ->name('campaigns.cover.update');

// Exclusão de campanhas
Route::delete('/campanhas/{campaign}', [CampaignController::class, 'destroy'])
    ->name('campaigns.destroy');

// Criação de personagens
Route::get(
    '/campanhas/{campaign}/personagens/novo',
    [CharacterController::class, 'create']
)->name('characters.create');

Route::post(
    '/campanhas/{campaign}/personagens',
    [CharacterController::class, 'store']
)->name('characters.store');

// Detalhes dos personagens
Route::get(
    '/campanhas/{campaign}/personagens/{character}/detalhes',
    [CharacterController::class, 'details']
)->name('characters.details');

Route::put(
    '/campanhas/{campaign}/personagens/{character}/detalhes',
    [CharacterController::class, 'updateDetails']
)->name('characters.details.update');

// Revisão e confirmação das fichas
Route::get(
    '/campanhas/{campaign}/personagens/{character}/revisao',
    [CharacterController::class, 'review']
)->name('characters.review');

Route::post(
    '/campanhas/{campaign}/personagens/{character}/confirmar',
    [CharacterController::class, 'confirm']
)->name('characters.confirm');

// Recursos dos personagens
Route::put(
    '/campanhas/{campaign}/personagens/{character}/recursos',
    [CharacterController::class, 'updateResources']
)->name('characters.resources.update');

Route::post(
    '/campanhas/{campaign}/personagens/{character}/pv',
    [CharacterController::class, 'updatePv']
)->name('characters.pv.update');

Route::post(
    '/campanhas/{campaign}/personagens/{character}/san',
    [CharacterController::class, 'updateSan']
)->name('characters.san.update');

Route::post(
    '/campanhas/{campaign}/personagens/{character}/pe',
    [CharacterController::class, 'updatePe']
)->name('characters.pe.update');

// Exclusão de personagens
Route::delete(
    '/campanhas/{campaign}/personagens/{character}',
    [CharacterController::class, 'destroy']
)->name('characters.destroy');