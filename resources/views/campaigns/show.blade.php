@extends('layouts.app')

@section('title', $campaign->title . ' | Mestre Paranormal')
@section('topbar', 'Central da investigação')

@section('content')

@php
    $coverUrl = $campaign->cover_path
        ? asset('storage/' . $campaign->cover_path)
        : asset('images/campaigns/manssao_assombrada.jpg');

    $caseNumber = 'MP-' . str_pad(
        (string) $campaign->id,
        4,
        '0',
        STR_PAD_LEFT
    );

    $statusLabel = match ($campaign->status) {
        'setup' => 'Em preparação',
        'active' => 'Em andamento',
        'completed' => 'Concluída',
        default => ucfirst(
            str_replace('_', ' ', $campaign->status ?? 'Não definido')
        ),
    };

    $characters = $campaign->characters;
@endphp

<style>
    .campaign-workspace {
        display: grid;
        gap: 26px;
        min-width: 0;
    }

    .campaign-workspace *,
    .campaign-workspace *::before,
    .campaign-workspace *::after {
        box-sizing: border-box;
    }

    .campaign-workspace [id] {
        scroll-margin-top: 100px;
    }

    .campaign-workspace .workspace-heading {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 18px;
    }

    .campaign-workspace .workspace-heading h1 {
        margin: 8px 0;
        overflow-wrap: anywhere;
    }

    .campaign-workspace .workspace-heading p {
        margin-bottom: 0;
    }

    .campaign-workspace .workspace-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }

    .campaign-workspace .workspace-cover {
        position: relative;
        min-height: 240px;
        overflow: hidden;
        border: 1px solid rgba(255, 255, 255, .12);
        border-radius: 14px;
        background:
            linear-gradient(
                90deg,
                rgba(0, 0, 0, .94),
                rgba(0, 0, 0, .30)
            ),
            var(--campaign-cover) center 45% / cover no-repeat,
            #151515;
    }

    .campaign-workspace .workspace-cover-content {
        position: relative;
        z-index: 1;
        display: flex;
        flex-direction: column;
        justify-content: flex-end;
        align-items: flex-start;
        gap: 12px;
        min-height: 240px;
        padding: 28px;
    }

    .campaign-workspace .workspace-classification {
        color: #ff858b;
        font-size: 11px;
        font-weight: 800;
        letter-spacing: .15em;
    }

    .campaign-workspace .workspace-case-number {
        color: rgba(255, 255, 255, .7);
        font-size: 12px;
        letter-spacing: .12em;
    }

    .campaign-workspace .workspace-cover h2 {
        margin: 0;
        color: #fff;
        overflow-wrap: anywhere;
    }

    .campaign-workspace .workspace-cover p {
        max-width: 650px;
        margin: 0;
        color: rgba(255, 255, 255, .8);
        line-height: 1.6;
    }

    .campaign-workspace .workspace-shortcuts {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 14px;
    }

    .campaign-workspace .workspace-shortcut {
        display: flex;
        flex-direction: column;
        gap: 10px;
        min-width: 0;
        min-height: 165px;
        padding: 18px;
        border: 1px solid var(--border-color, #333);
        border-radius: 12px;
        background: var(--card-bg, #121212);
        color: inherit;
        text-decoration: none;
        transition:
            border-color .2s ease,
            background .2s ease,
            transform .2s ease;
    }

    .campaign-workspace .workspace-shortcut:hover {
        border-color: rgba(181, 31, 43, .75);
        background: rgba(181, 31, 43, .06);
        color: inherit;
        transform: translateY(-2px);
    }

    .campaign-workspace .workspace-shortcut:focus-visible {
        outline: 2px solid #d52a37;
        outline-offset: 3px;
    }

    .campaign-workspace .shortcut-icon {
        display: grid;
        place-items: center;
        flex-shrink: 0;
        width: 42px;
        height: 42px;
        border: 1px solid rgba(181, 31, 43, .35);
        border-radius: 11px;
        background: rgba(181, 31, 43, .1);
        color: #ff858b;
        font-size: 20px;
    }

    .campaign-workspace .workspace-shortcut strong {
        font-size: 14px;
    }

    .campaign-workspace .workspace-shortcut small {
        color: var(--muted-color, #999);
        font-size: 12px;
        line-height: 1.5;
    }

    .campaign-workspace .workspace-section {
        display: grid;
        gap: 16px;
        min-width: 0;
    }

    .campaign-workspace .workspace-section-heading {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 12px;
    }

    .campaign-workspace .workspace-section-heading h2 {
        margin: 5px 0;
    }

    .campaign-workspace .workspace-section-heading p {
        margin: 0;
        opacity: .75;
    }

    .campaign-workspace .workspace-section-heading .eyebrow {
        font-size: 11px;
    }

    .campaign-workspace .workspace-agent-list {
        display: grid;
        gap: 0;
        overflow: hidden;
        border: 1px solid var(--border-color, #333);
        border-radius: 12px;
        background: var(--card-bg, #121212);
    }

    .campaign-workspace .workspace-agent {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 14px;
        padding: 16px;
    }

    .campaign-workspace .workspace-agent + .workspace-agent {
        border-top: 1px solid var(--border-color, #333);
    }

    .campaign-workspace .workspace-agent-avatar {
        display: grid;
        place-items: center;
        flex-shrink: 0;
        width: 44px;
        height: 44px;
        border: 1px solid rgba(181, 31, 43, .4);
        border-radius: 50%;
        background: rgba(181, 31, 43, .12);
        color: #ff858b;
        font-size: 18px;
        font-weight: 800;
    }

    .campaign-workspace .workspace-agent-info {
        display: grid;
        flex: 1;
        gap: 5px;
        min-width: 130px;
    }

    .campaign-workspace .workspace-agent-info small {
        opacity: .7;
    }

    .campaign-workspace .workspace-agent-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }

    .campaign-workspace .workspace-empty {
        display: grid;
        justify-items: center;
        gap: 12px;
        padding: 32px 18px;
        text-align: center;
    }

    .campaign-workspace .workspace-empty p {
        margin: 0;
        opacity: .75;
    }

    .campaign-workspace .workspace-status {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        width: fit-content;
        padding: 7px 11px;
        border: 1px solid rgba(181, 31, 43, .35);
        border-radius: 999px;
        background: rgba(181, 31, 43, .1);
        font-size: 12px;
        font-weight: 700;
    }

    .campaign-workspace .workspace-status-dot {
        width: 7px;
        height: 7px;
        border-radius: 50%;
        background: #e04b55;
    }

    .campaign-workspace .workspace-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 12px;
        padding-top: 18px;
        border-top: 1px solid var(--border-color, #333);
    }

    .campaign-workspace .workspace-footer p {
        margin: 0;
        font-size: 12px;
        opacity: .7;
    }

    @media (max-width: 900px) {
        .campaign-workspace .workspace-shortcuts {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 560px) {
        .campaign-workspace .workspace-shortcuts {
            grid-template-columns: 1fr;
        }

        .campaign-workspace .workspace-cover-content {
            padding: 20px;
        }

        .campaign-workspace .workspace-agent-actions {
            width: 100%;
        }

        .campaign-workspace .workspace-agent-actions .btn {
            flex: 1;
            text-align: center;
        }

        .campaign-workspace .workspace-heading,
        .campaign-workspace .workspace-actions {
            width: 100%;
        }
    }

    @media (prefers-reduced-motion: reduce) {
        .campaign-workspace .workspace-shortcut {
            transition: none;
        }

        html {
            scroll-behavior: auto;
        }
    }

    html {
        scroll-behavior: smooth;
    }
</style>

<div class="operations-dashboard campaign-workspace">

    {{-- CABEÇALHO DA CAMPANHA --}}
    <section class="workspace-heading">
        <div>
            <span class="eyebrow">DIVISÃO DE INVESTIGAÇÃO PARANORMAL</span>

            <h1>{{ $campaign->title }}</h1>

            <p>
                Gerencie sua equipe, consulte o Mestre Paranormal e acompanhe
                os acontecimentos deste caso.
            </p>
        </div>

        <div class="workspace-actions">
            <a href="{{ route('home') }}" class="btn btn-secondary">
                ← Central de operações
            </a>

            <a
                href="{{ route('campaigns.cover.edit', ['campaign' => $campaign->id]) }}"
                class="btn btn-secondary"
            >
                Editar capa
            </a>
        </div>
    </section>

    {{-- MENSAGENS DO SISTEMA --}}
    @if (session('success'))
        <div class="alert" role="status">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="alert" role="alert">
            {{ session('error') }}
        </div>
    @endif

    {{-- IDENTIFICAÇÃO DO CASO --}}
    <section
        class="workspace-cover"
        style="--campaign-cover: url('{{ $coverUrl }}')"
    >
        <div class="workspace-cover-content">
            <span class="workspace-classification">
                ARQUIVO CONFIDENCIAL
            </span>

            <span class="workspace-case-number">
                CASO Nº {{ $caseNumber }}
            </span>

            <h2>{{ $campaign->title }}</h2>

            <p>
                Dossiê operacional da investigação.
                Todos os recursos abaixo estão vinculados a este caso.
            </p>

            <span class="workspace-status">
                <span class="workspace-status-dot"></span>
                {{ $statusLabel }}
            </span>
        </div>
    </section>

    {{-- NAVEGAÇÃO INTERNA --}}
    <section class="workspace-section">
        <div class="workspace-section-heading">
            <div>
                <span class="eyebrow">NAVEGAÇÃO DO CASO</span>
                <h2>Recursos da investigação</h2>
                <p>Escolha uma ferramenta para continuar sua operação.</p>
            </div>
        </div>

        <div class="workspace-shortcuts">

            <a
                href="#mestre-paranormal"
                class="workspace-shortcut"
            >
                <span class="shortcut-icon" aria-hidden="true">✧</span>
                <strong>Mestre Paranormal</strong>
                <small>
                    Consulte as regras, tire dúvidas e solicite rolagens
                    de dados à inteligência artificial.
                </small>
            </a>

            <a
                href="#equipe"
                class="workspace-shortcut"
            >
                <span class="shortcut-icon" aria-hidden="true">◉</span>
                <strong>Equipe operacional</strong>
                <small>
                    Consulte os agentes cadastrados e acesse suas fichas
                    e recursos.
                </small>
            </a>

            <a
                href="{{ route('investigation.entries.index', ['campaign' => $campaign->id]) }}"
                class="workspace-shortcut"
            >
                <span class="shortcut-icon" aria-hidden="true">▤</span>
                <strong>Diário de investigação</strong>
                <small>
                    Registrar pistas, evidências, descobertas
                    e acontecimentos importantes.
                </small>
            </a>

            <a
                href="{{ route('sessions.index', ['campaign' => $campaign->id]) }}"
                class="workspace-shortcut"
            >
                <span class="shortcut-icon" aria-hidden="true">◷</span>
                <strong>Sessões</strong>
                <small>
                    Organizar encontros, acompanhar o histórico
                    e registrar o andamento da campanha.
                </small>
            </a>

            <a
                href="{{ route('campaign-settings.automation.edit', ['campaign' => $campaign->id]) }}"
                class="workspace-shortcut"
            >
                <span class="shortcut-icon" aria-hidden="true">⚙</span>
                <strong>Automações da IA</strong>
                <small>
                    Configurar rolagens de dados, iniciativa,
                    controle de NPCs e ações da inteligência artificial.
                </small>
            </a>

            <a
                href="{{ route('campaigns.cover.edit', ['campaign' => $campaign->id]) }}"
                class="workspace-shortcut"
            >
                <span class="shortcut-icon" aria-hidden="true">▧</span>
                <strong>Identidade do caso</strong>
                <small>
                    Personalizar a capa utilizada para identificar
                    esta investigação.
                </small>
            </a>

        </div>
    </section>

    {{-- TERMINAL DO MESTRE PARANORMAL --}}
    <section class="workspace-section" id="mestre-paranormal">
        <div class="workspace-section-heading">
            <div>
                <span class="eyebrow">ASSISTÊNCIA INTELIGENTE</span>
                <h2>Terminal do Mestre Paranormal</h2>
                <p>
                    Converse com a IA dentro do contexto desta investigação.
                </p>
            </div>
        </div>

        @include('campaigns.partials.ai-chat', [
            'campaign' => $campaign,
        ])
    </section>

    {{-- EQUIPE OPERACIONAL --}}
    <section class="workspace-section" id="equipe">
        <div class="workspace-section-heading">
            <div>
                <span class="eyebrow">AGENTES VINCULADOS</span>
                <h2>Equipe operacional</h2>
                <p>
                    Personagens cadastrados neste dossiê.
                </p>
            </div>

            <a
                href="{{ route('characters.create', ['campaign' => $campaign->id]) }}"
                class="btn"
            >
                + Adicionar agente
            </a>
        </div>

        <div class="workspace-agent-list">

            @forelse ($characters as $character)
                <article class="workspace-agent">

                    <div class="workspace-agent-avatar" aria-hidden="true">
                        {{ mb_strtoupper(mb_substr($character->name, 0, 1)) }}
                    </div>

                    <div class="workspace-agent-info">
                        <strong>{{ $character->name }}</strong>

                        <small>
                            {{ $character->creation_status === 'confirmed'
                                ? 'Ficha confirmada'
                                : 'Ficha em preparação' }}
                        </small>
                    </div>

                    <span class="status-badge {{ $character->creation_status === 'confirmed' ? 'status-confirmed' : 'status-draft' }}">
                        {{ $character->creation_status === 'confirmed'
                            ? 'Confirmada'
                            : 'Rascunho' }}
                    </span>

                    <div class="workspace-agent-actions">

                        @if ($character->creation_status !== 'confirmed')
                            <a
                                href="{{ route('characters.details', [
                                    'campaign' => $campaign->id,
                                    'character' => $character->id,
                                ]) }}"
                                class="btn btn-secondary"
                            >
                                Editar
                            </a>
                        @endif

                        <a
                            href="{{ route('characters.review', [
                                'campaign' => $campaign->id,
                                'character' => $character->id,
                            ]) }}"
                            class="btn"
                        >
                            Ver ficha
                        </a>

                    </div>

                </article>
            @empty
                <div class="workspace-empty">
                    <span class="shortcut-icon" aria-hidden="true">◉</span>

                    <strong>Nenhum agente cadastrado</strong>

                    <p>
                        Cadastre o primeiro personagem para começar
                        a montar sua equipe operacional.
                    </p>

                    <a
                        href="{{ route('characters.create', ['campaign' => $campaign->id]) }}"
                        class="btn"
                    >
                        Cadastrar primeiro agente
                    </a>
                </div>
            @endforelse

        </div>
    </section>

    {{-- RODAPÉ --}}
    <footer class="workspace-footer">
        <p>
            Dossiê {{ $caseNumber }}
            · Criado em {{ $campaign->created_at?->format('d/m/Y') ?? 'data não informada' }}
        </p>

        <a href="{{ route('home') }}" class="btn btn-secondary">
            Voltar à central
        </a>
    </footer>

</div>

@endsection
