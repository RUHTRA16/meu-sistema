 @extends('layouts.app')

@section('title', 'Central de operações | Mestre Paranormal')
@section('topbar', 'Central de operações')

@section('content')

<div class="operations-dashboard">

    {{-- CABEÇALHO DA CENTRAL --}}
    <section class="dashboard-heading">
        <div>
            <span class="eyebrow">DIVISÃO DE INVESTIGAÇÃO PARANORMAL</span>
            <h1>Central de operações</h1>
            <p>Selecione uma investigação para acessar sua área de trabalho.</p>
        </div>

        <a href="{{ route('campaigns.create') }}" class="btn dashboard-create-btn">
            <span aria-hidden="true">+</span>
            Nova investigação
        </a>
    </section>

    {{-- INDICADORES GERAIS --}}
    <section class="dashboard-metrics">

        <article class="metric-card">
            <div class="metric-topline">
                <span class="metric-icon" aria-hidden="true">▤</span>
                <span class="metric-label">ARQUIVOS REGISTRADOS</span>
            </div>

            <strong class="metric-number">
                {{ $campaigns->count() }}
            </strong>

            <span class="metric-caption">Campanhas cadastradas</span>
        </article>

        <article class="metric-card">
            <div class="metric-topline">
                <span class="metric-icon" aria-hidden="true">◉</span>
                <span class="metric-label">AGENTES IDENTIFICADOS</span>
            </div>

            <strong class="metric-number">
                {{ $campaigns->sum(fn ($campaign) => $campaign->characters->count()) }}
            </strong>

            <span class="metric-caption">Personagens cadastrados</span>
        </article>

        <article class="metric-card metric-alert">
            <div class="metric-topline">
                <span class="metric-icon" aria-hidden="true">⌁</span>
                <span class="metric-label">CASOS EM ANDAMENTO</span>
            </div>

            <strong class="metric-number">
                {{ $campaigns->where('status', 'active')->count() }}
            </strong>

            <span class="metric-caption">Investigações ativas</span>
        </article>

    </section>

    {{-- MENSAGENS DO SISTEMA --}}
    @if (session('success'))
        <div class="alert">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="alert">
            {{ session('error') }}
        </div>
    @endif

    {{-- LISTA DE INVESTIGAÇÕES --}}
    <section class="investigations-section">

        <div class="section-heading">
            <div>
                <span class="eyebrow">ARQUIVO DE CASOS</span>
                <h2>Suas investigações</h2>
                <p>Entre em uma campanha para acessar o Mestre Paranormal e suas ferramentas.</p>
            </div>

            <span class="record-counter">
                {{ $campaigns->count() }}
                {{ $campaigns->count() === 1 ? 'ARQUIVO' : 'ARQUIVOS' }}
            </span>
        </div>

        @forelse ($campaigns as $campaign)

            <article class="investigation-card">

                {{-- CAPA DA INVESTIGAÇÃO --}}
                @php
                    $coverUrl = $campaign->cover_path
                        ? asset('storage/' . $campaign->cover_path)
                        : ($loop->first
                            ? asset('images/campaigns/manssao_assombrada.jpg')
                            : null);
                @endphp

                <div
                    class="investigation-cover {{ $coverUrl ? 'has-custom-cover' : '' }}"
                    @if ($coverUrl)
                        style="--campaign-cover: url('{{ $coverUrl }}')"
                    @endif
                >
                    <div class="cover-grid" aria-hidden="true"></div>
                    <div class="cover-overlay" aria-hidden="true"></div>

                    <span class="cover-classification">
                        ARQUIVO CONFIDENCIAL
                    </span>

                    <div class="cover-emblem" aria-hidden="true">
                        <span>MP</span>
                    </div>

                    <span class="cover-reference">
                        CASO Nº {{ str_pad((string) $campaign->id, 4, '0', STR_PAD_LEFT) }}
                    </span>
                </div>

                {{-- INFORMAÇÕES DA INVESTIGAÇÃO --}}
                <div class="investigation-body">

                    <div class="investigation-title-row">
                        <div class="investigation-title">
                            <span class="eyebrow">DOSSIÊ DE INVESTIGAÇÃO</span>
                            <h3>{{ $campaign->title }}</h3>
                        </div>

                        <span class="status-badge status-campaign">
                            {{ match ($campaign->status) {
                                'setup' => 'Em preparação',
                                'active' => 'Em andamento',
                                'completed' => 'Concluída',
                                default => ucfirst(str_replace('_', ' ', $campaign->status ?? 'Não definido')),
                            } }}
                        </span>
                    </div>

                    {{-- RESUMO DA INVESTIGAÇÃO --}}
                    <div class="investigation-meta">

                        <div>
                            <span class="meta-label">EQUIPE REGISTRADA</span>

                            <strong>
                                {{ $campaign->characters->count() }}
                                {{ $campaign->characters->count() === 1 ? 'personagem' : 'personagens' }}
                            </strong>
                        </div>

                        <div>
                            <span class="meta-label">IDENTIFICAÇÃO</span>

                            <strong>
                                MP-{{ str_pad((string) $campaign->id, 4, '0', STR_PAD_LEFT) }}
                            </strong>
                        </div>

                        <div>
                            <span class="meta-label">DIÁRIO DE INVESTIGAÇÃO</span>

                            <strong>
                                {{ $campaign->investigationEntries->count() }}
                                {{ $campaign->investigationEntries->count() === 1 ? 'registro' : 'registros' }}
                            </strong>
                        </div>

                        <div>
                            <span class="meta-label">SESSÕES DE RPG</span>

                            <strong>
                                {{ $campaign->sessions->count() }}
                                {{ $campaign->sessions->count() === 1 ? 'sessão' : 'sessões' }}
                            </strong>
                        </div>

                    </div>

                    {{-- BOTÃO PRINCIPAL --}}
                    <div class="campaign-entry-action">

                        <a
                            href="{{ route('campaigns.show', ['campaign' => $campaign->id]) }}"
                            class="btn campaign-enter-btn"
                        >
                            <span aria-hidden="true">▶</span>
                            Entrar na campanha
                            <span class="campaign-enter-arrow" aria-hidden="true">→</span>
                        </a>

                        <p>
                            Acesse o mestre de RPG, gerencie seus agentes e continue a investigação.
                        </p>

                    </div>

                </div>

                {{-- EQUIPE: RESUMO INFORMATIVO --}}
                <div class="investigation-team">

                    <div class="team-heading">
                        <h4>Agentes vinculados</h4>

                        <span>
                            {{ $campaign->characters->count() }}
                            {{ $campaign->characters->count() === 1 ? 'REGISTRADO' : 'REGISTRADOS' }}
                        </span>
                    </div>

                    @forelse ($campaign->characters as $character)

                        <div class="agent-row">

                            <div class="agent-avatar" aria-hidden="true">
                                {{ mb_strtoupper(mb_substr($character->name, 0, 1)) }}
                            </div>

                            <div class="agent-info">
                                <strong>{{ $character->name }}</strong>

                                <span>
                                    {{ $character->creation_status === 'confirmed'
                                        ? 'Ficha confirmada'
                                        : 'Ficha em preparação' }}
                                </span>
                            </div>

                            <span class="status-badge {{ $character->creation_status === 'confirmed' ? 'status-confirmed' : 'status-draft' }}">
                                {{ $character->creation_status === 'confirmed'
                                    ? 'Confirmada'
                                    : 'Rascunho' }}
                            </span>

                        </div>

                    @empty

                        <div class="team-empty">
                            <span aria-hidden="true">◉</span>
                            <p>Nenhum agente vinculado a esta investigação.</p>
                        </div>

                    @endforelse

                </div>

                {{-- RODAPÉ: EXCLUSÃO DA INVESTIGAÇÃO --}}
                <div class="investigation-footer">

                    <span>
                        <span class="footer-status-dot"></span>
                        REGISTRO DISPONÍVEL
                    </span>

                    <form
                        action="{{ route('campaigns.destroy', [
                            'campaign' => $campaign->id,
                        ]) }}"
                        method="POST"
                        onsubmit="return confirm('Tem certeza de que deseja excluir esta campanha? Campanhas com personagens não podem ser excluídas.');"
                    >
                        @csrf
                        @method('DELETE')

                        <button type="submit" class="text-danger-button">
                            Excluir investigação
                        </button>
                    </form>

                </div>

            </article>

        @empty

            {{-- ESTADO VAZIO --}}
            <div class="investigations-empty">

                <div class="empty-emblem" aria-hidden="true">MP</div>

                <span class="eyebrow">NENHUM REGISTRO LOCALIZADO</span>

                <h3>Sua primeira investigação começa aqui.</h3>

                <p>
                    Ainda não existem campanhas cadastradas.
                    Crie uma investigação para começar a organizar seus agentes e registros.
                </p>

                <a href="{{ route('campaigns.create') }}" class="btn">
                    + Abrir primeira investigação
                </a>

            </div>

        @endforelse

    </section>

</div>

{{-- ESTILO DO BOTÃO PRINCIPAL --}}
<style>
    .campaign-entry-action {
        margin: 24px 0 8px;
        padding: 20px;
        border: 1px solid rgba(181, 31, 43, 0.35);
        border-radius: 12px;
        background: linear-gradient(
            120deg,
            rgba(181, 31, 43, 0.12),
            rgba(18, 18, 18, 0.65)
        );
    }

    .campaign-enter-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 12px;
        min-height: 48px;
        padding: 12px 22px;
        border: 1px solid #d52a37;
        border-radius: 7px;
        background: #b51f2b;
        color: #fff;
        font-weight: 700;
        text-decoration: none;
        transition:
            background 0.2s ease,
            border-color 0.2s ease,
            transform 0.2s ease,
            box-shadow 0.2s ease;
    }

    .campaign-enter-btn:hover {
        background: #d52a37;
        border-color: #f04452;
        color: #fff;
        transform: translateY(-2px);
        box-shadow: 0 6px 22px rgba(181, 31, 43, 0.25);
    }

    .campaign-enter-arrow {
        margin-left: 8px;
        font-size: 1.15rem;
        transition: transform 0.2s ease;
    }

    .campaign-enter-btn:hover .campaign-enter-arrow {
        transform: translateX(4px);
    }

    .campaign-enter-btn:focus-visible {
        outline: 2px solid #fff;
        outline-offset: 4px;
    }

    .campaign-entry-action p {
        margin: 12px 0 0;
        color: #aaa;
        font-size: 0.9rem;
        line-height: 1.5;
    }

    @media (max-width: 600px) {
        .campaign-enter-btn {
            display: flex;
            width: 100%;
        }

        .campaign-entry-action {
            padding: 16px;
        }
    }
</style>

@endsection