@extends('layouts.app')

@section('title', 'Automações | Mestre Paranormal')
@section('topbar', 'Configurações de automação')

@section('content')
<div class="operations-dashboard">

    <section class="dashboard-heading">
        <div>
            <span class="eyebrow">CENTRAL DE OPERAÇÕES</span>
            <h1>Configurações de automação</h1>
            <p>
                Escolha quais funcionalidades da campanha serão controladas
                manualmente e quais poderão ser automatizadas pelo sistema.
            </p>
        </div>

        <a
            href="{{ route('sessions.index', ['campaign' => $campaign->id]) }}"
            class="btn"
        >
            Voltar às sessões
        </a>
    </section>

    <section class="dashboard-section">
        <div class="section-heading">
            <div>
                <span class="eyebrow">CAMPANHA SELECIONADA</span>
                <h2>{{ $campaign->title }}</h2>
                <p>As preferências abaixo são exclusivas desta campanha.</p>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success" role="status">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger" role="alert">
                <strong>Não foi possível salvar as configurações.</strong>

                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form
            action="{{ route('campaign-settings.automation.update', ['campaign' => $campaign->id]) }}"
            method="POST"
            class="automation-settings-form"
        >
            @csrf
            @method('PUT')

            <div class="automation-setting-card">
                <div class="automation-setting-info">
                    <span class="automation-setting-icon">🎲</span>

                    <div>
                        <h3>Rolagem de dados</h3>
                        <p>
                            Define se os dados serão rolados manualmente pelo
                            usuário ou pelo sistema.
                        </p>
                    </div>
                </div>

                <div class="automation-options">
                    <label class="automation-option">
                        <input
                            type="radio"
                            name="dice_rolling"
                            value="manual"
                            @checked(old('dice_rolling', $settings->dice_rolling) === 'manual')
                        >
                        <span>
                            <strong>Manual</strong>
                            <small>O usuário realiza a rolagem.</small>
                        </span>
                    </label>

                    <label class="automation-option">
                        <input
                            type="radio"
                            name="dice_rolling"
                            value="automatic"
                            @checked(old('dice_rolling', $settings->dice_rolling) === 'automatic')
                        >
                        <span>
                            <strong>Automática</strong>
                            <small>O sistema realiza a rolagem.</small>
                        </span>
                    </label>
                </div>
            </div>

            <div class="automation-setting-card">
                <div class="automation-setting-info">
                    <span class="automation-setting-icon">⚔️</span>

                    <div>
                        <h3>Iniciativa e ordem de combate</h3>
                        <p>
                            Define como a iniciativa e a ordem dos participantes
                            serão organizadas durante o combate.
                        </p>
                    </div>
                </div>

                <div class="automation-options">
                    <label class="automation-option">
                        <input
                            type="radio"
                            name="initiative"
                            value="manual"
                            @checked(old('initiative', $settings->initiative) === 'manual')
                        >
                        <span>
                            <strong>Manual</strong>
                            <small>O usuário organiza a ordem.</small>
                        </span>
                    </label>

                    <label class="automation-option">
                        <input
                            type="radio"
                            name="initiative"
                            value="automatic"
                            @checked(old('initiative', $settings->initiative) === 'automatic')
                        >
                        <span>
                            <strong>Automática</strong>
                            <small>O sistema organiza a ordem.</small>
                        </span>
                    </label>
                </div>
            </div>

            <div class="automation-setting-card">
                <div class="automation-setting-info">
                    <span class="automation-setting-icon">👥</span>

                    <div>
                        <h3>Controle de NPCs e criaturas</h3>
                        <p>
                            Define se o mestre decide as ações dos personagens
                            não jogadores ou se a IA pode conduzi-las.
                        </p>
                    </div>
                </div>

                <div class="automation-options">
                    <label class="automation-option">
                        <input
                            type="radio"
                            name="npc_control"
                            value="manual"
                            @checked(old('npc_control', $settings->npc_control) === 'manual')
                        >
                        <span>
                            <strong>Manual</strong>
                            <small>O mestre decide as ações.</small>
                        </span>
                    </label>

                    <label class="automation-option">
                        <input
                            type="radio"
                            name="npc_control"
                            value="automatic"
                            @checked(old('npc_control', $settings->npc_control) === 'automatic')
                        >
                        <span>
                            <strong>Automático</strong>
                            <small>A IA conduz as ações permitidas.</small>
                        </span>
                    </label>
                </div>
            </div>

            <div class="automation-setting-card">
                <div class="automation-setting-info">
                    <span class="automation-setting-icon">🧠</span>

                    <div>
                        <h3>Ações da inteligência artificial</h3>
                        <p>
                            Define como a IA poderá executar operações,
                            como registrar informações e atualizar dados.
                        </p>
                    </div>
                </div>

                <div class="automation-options">
                    <label class="automation-option">
                        <input
                            type="radio"
                            name="ai_actions"
                            value="confirm"
                            @checked(old('ai_actions', $settings->ai_actions) === 'confirm')
                        >
                        <span>
                            <strong>Solicitar confirmação</strong>
                            <small>A IA pede autorização antes de executar.</small>
                        </span>
                    </label>

                    <label class="automation-option">
                        <input
                            type="radio"
                            name="ai_actions"
                            value="automatic"
                            @checked(old('ai_actions', $settings->ai_actions) === 'automatic')
                        >
                        <span>
                            <strong>Automática</strong>
                            <small>A IA executa ações previamente autorizadas.</small>
                        </span>
                    </label>
                </div>
            </div>

            <div class="automation-form-actions">
                <a
                    href="{{ route('sessions.index', ['campaign' => $campaign->id]) }}"
                    class="btn"
                >
                    Cancelar
                </a>

                <button type="submit" class="btn btn-primary">
                    Salvar configurações
                </button>
            </div>
        </form>
    </section>
</div>
@endsection