<?php

use Illuminate\Support\Facades\Route;

use App\Models\Campaign;

use App\Http\Controllers\HomeController;
use App\Http\Controllers\CampaignController;
use App\Http\Controllers\CharacterController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\InvestigationEntryController;
use App\Http\Controllers\CampaignSessionController;
use App\Http\Controllers\CampaignAutomationSettingController;

/*
|--------------------------------------------------------------------------
| Mestre Paranormal — Inteligência Artificial
|--------------------------------------------------------------------------
*/

// Página do chat com seleção de campanha.
Route::get('/mestre-ia', function () {
    $campaigns = Campaign::query()
        ->orderBy('title')
        ->get();

    return view('chat', compact('campaigns'));
})->name('chat');

// Envio de mensagens para a IA.
Route::post(
    '/mestre-ia/mensagem',
    [ChatController::class, 'send']
)->name('chat.send');

Route::post(
    '/campanhas/{campaign}/mestre-ia/acao-pv/confirmar',
    [ChatController::class, 'confirmPvAction']
)->name('chat.pv.confirm');

/*
|--------------------------------------------------------------------------
| Página inicial
|--------------------------------------------------------------------------
*/

Route::get('/', [HomeController::class, 'index'])
    ->name('home');

/*
|--------------------------------------------------------------------------
| Criação e gerenciamento de campanhas
|--------------------------------------------------------------------------
*/

// Formulário de criação.
Route::get(
    '/campanhas/nova',
    [CampaignController::class, 'create']
)->name('campaigns.create');

// Salvar campanha.
Route::post(
    '/campanhas',
    [CampaignController::class, 'store']
)->name('campaigns.store');

// Página individual da campanha.
Route::get(
    '/campanhas/{campaign}',
    [CampaignController::class, 'show']
)->name('campaigns.show');

// Formulário de edição da capa.
Route::get(
    '/campanhas/{campaign}/capa',
    [CampaignController::class, 'editCover']
)->name('campaigns.cover.edit');

// Atualizar capa.
Route::put(
    '/campanhas/{campaign}/capa',
    [CampaignController::class, 'updateCover']
)->name('campaigns.cover.update');

// Excluir campanha.
Route::delete(
    '/campanhas/{campaign}',
    [CampaignController::class, 'destroy']
)->name('campaigns.destroy');

/*
|--------------------------------------------------------------------------
| Diário de investigação
|--------------------------------------------------------------------------
*/

// Listar registros do diário.
Route::get(
    '/campanhas/{campaign}/diario',
    [InvestigationEntryController::class, 'index']
)->name('investigation.entries.index');

// Criar registro.
Route::post(
    '/campanhas/{campaign}/diario',
    [InvestigationEntryController::class, 'store']
)->name('investigation.entries.store');

// Formulário de edição.
Route::get(
    '/campanhas/{campaign}/diario/{entry}/editar',
    [InvestigationEntryController::class, 'edit']
)->name('investigation.entries.edit');

// Atualizar registro.
Route::put(
    '/campanhas/{campaign}/diario/{entry}',
    [InvestigationEntryController::class, 'update']
)->name('investigation.entries.update');

// Excluir registro.
Route::delete(
    '/campanhas/{campaign}/diario/{entry}',
    [InvestigationEntryController::class, 'destroy']
)->name('investigation.entries.destroy');

/*
|--------------------------------------------------------------------------
| Personagens
|--------------------------------------------------------------------------
*/

// Formulário de criação.
Route::get(
    '/campanhas/{campaign}/personagens/novo',
    [CharacterController::class, 'create']
)->name('characters.create');

// Salvar personagem.
Route::post(
    '/campanhas/{campaign}/personagens',
    [CharacterController::class, 'store']
)->name('characters.store');

// Exibir detalhes do personagem.
Route::get(
    '/campanhas/{campaign}/personagens/{character}/detalhes',
    [CharacterController::class, 'details']
)->name('characters.details');

// Atualizar detalhes.
Route::put(
    '/campanhas/{campaign}/personagens/{character}/detalhes',
    [CharacterController::class, 'updateDetails']
)->name('characters.details.update');

// Página de revisão da ficha.
Route::get(
    '/campanhas/{campaign}/personagens/{character}/revisao',
    [CharacterController::class, 'review']
)->name('characters.review');

// Confirmar ficha.
Route::post(
    '/campanhas/{campaign}/personagens/{character}/confirmar',
    [CharacterController::class, 'confirm']
)->name('characters.confirm');

/*
|--------------------------------------------------------------------------
| Recursos dos personagens
|--------------------------------------------------------------------------
*/

// Atualizar recursos gerais.
Route::put(
    '/campanhas/{campaign}/personagens/{character}/recursos',
    [CharacterController::class, 'updateResources']
)->name('characters.resources.update');

// Atualizar pontos de vida.
Route::post(
    '/campanhas/{campaign}/personagens/{character}/pv',
    [CharacterController::class, 'updatePv']
)->name('characters.pv.update');

// Atualizar sanidade.
Route::post(
    '/campanhas/{campaign}/personagens/{character}/san',
    [CharacterController::class, 'updateSan']
)->name('characters.san.update');

// Atualizar pontos de esforço.
Route::post(
    '/campanhas/{campaign}/personagens/{character}/pe',
    [CharacterController::class, 'updatePe']
)->name('characters.pe.update');

// Excluir personagem.
Route::delete(
    '/campanhas/{campaign}/personagens/{character}',
    [CharacterController::class, 'destroy']
)->name('characters.destroy');

/*
|--------------------------------------------------------------------------
| Sessões de campanha
|--------------------------------------------------------------------------
*/

// Listar sessões.
Route::get(
    '/campanhas/{campaign}/sessoes',
    [CampaignSessionController::class, 'index']
)->name('sessions.index');

// Criar sessão.
Route::post(
    '/campanhas/{campaign}/sessoes',
    [CampaignSessionController::class, 'store']
)->name('sessions.store');

// Formulário de edição.
Route::get(
    '/campanhas/{campaign}/sessoes/{session}/editar',
    [CampaignSessionController::class, 'edit']
)->name('sessions.edit');

// Atualizar sessão.
Route::put(
    '/campanhas/{campaign}/sessoes/{session}',
    [CampaignSessionController::class, 'update']
)->name('sessions.update');

// Concluir sessão.
Route::patch(
    '/campanhas/{campaign}/sessoes/{session}/concluir',
    [CampaignSessionController::class, 'complete']
)->name('sessions.complete');

// Excluir sessão.
Route::delete(
    '/campanhas/{campaign}/sessoes/{session}',
    [CampaignSessionController::class, 'destroy']
)->name('sessions.destroy');

/*
|--------------------------------------------------------------------------
| Configurações de automação das campanhas
|--------------------------------------------------------------------------
*/

// Exibir configurações.
Route::get(
    '/campanhas/{campaign}/configuracoes/automacao',
    [CampaignAutomationSettingController::class, 'edit']
)->name('campaign-settings.automation.edit');

// Salvar configurações.
Route::put(
    '/campanhas/{campaign}/configuracoes/automacao',
    [CampaignAutomationSettingController::class, 'update']
)->name('campaign-settings.automation.update');