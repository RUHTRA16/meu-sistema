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
            <p>Gerencie seus casos, consulte os agentes e investigue o desconhecido.</p>
        </div>

        <a href="{{ route('campaigns.create') }}" class="btn dashboard-create-btn">
            <span aria-hidden="true">+</span>
            Nova investigação
        </a>
    </section>

    {{-- INDICADORES --}}
    <section class="dashboard-metrics">

        <article class="metric-card">
            <div class="metric-topline">
                <span class="metric-icon" aria-hidden="true">▤</span>
                <span class="metric-label">ARQUIVOS REGISTRADOS</span>
            </div>

            <strong class="metric-number">{{ $campaigns->count() }}</strong>
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
        <div class="alert">{{ session('success') }}</div>
    @endif

    @if (session('error'))
        <div class="alert">{{ session('error') }}</div>
    @endif

    {{-- LISTA DE INVESTIGAÇÕES --}}
    <section class="investigations-section">

        <div class="section-heading">
            <div>
                <span class="eyebrow">BANCO DE DADOS</span>
                <h2>Investigações registradas</h2>
                <p>Consulte os arquivos disponíveis na central.</p>
            </div>

            <span class="record-counter">
                {{ $campaigns->count() }}
                {{ $campaigns->count() === 1 ? 'ARQUIVO' : 'ARQUIVOS' }}
            </span>
        </div>

        @forelse ($campaigns as $campaign)

            <article class="investigation-card">

                {{-- CAPA DINÂMICA DA INVESTIGAÇÃO --}}
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
                    </div>

                    <div class="investigation-actions">

                        <a
                            href="{{ route('campaigns.cover.edit', ['campaign' => $campaign->id]) }}"
                            class="btn btn-secondary"
                        >
                            Editar capa
                        </a>

                        <a
                            href="#campaign-{{ $campaign->id }}-characters"
                            class="btn btn-secondary"
                        >
                            Consultar equipe
                        </a>

                        <a
                            href="{{ route('characters.create', ['campaign' => $campaign->id]) }}"
                            class="btn"
                        >
                            + Adicionar agente
                        </a>

                    </div>
                </div>

                {{-- EQUIPE DA CAMPANHA --}}
                <div
                    class="investigation-team"
                    id="campaign-{{ $campaign->id }}-characters"
                >

                    <div class="team-heading">
                        <h4>Agentes vinculados</h4>
                        <span>{{ $campaign->characters->count() }} REGISTRADOS</span>
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
                                {{ $character->creation_status === 'confirmed' ? 'Confirmada' : 'Rascunho' }}
                            </span>

                            <div class="agent-actions">

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

                                <form
                                    action="{{ route('characters.destroy', [
                                        'campaign' => $campaign->id,
                                        'character' => $character->id,
                                    ]) }}"
                                    method="POST"
                                    onsubmit="return confirm('Tem certeza de que deseja excluir este personagem? Esta ação não pode ser desfeita.');"
                                >
                                    @csrf
                                    @method('DELETE')

                                    <button type="submit" class="btn btn-secondary">
                                        Excluir
                                    </button>
                                </form>

                            </div>
                        </div>

                    @empty

                        <div class="team-empty">
                            <span aria-hidden="true">◉</span>
                            <p>Nenhum agente vinculado a esta investigação.</p>
                        </div>

                    @endforelse

                </div>

                {{-- RODAPÉ DA INVESTIGAÇÃO --}}
                <div class="investigation-footer">
                    <span>
                        <span class="footer-status-dot"></span>
                        REGISTRO DISPONÍVEL
                    </span>

                    <form
                        action="{{ route('campaigns.destroy', ['campaign' => $campaign->id]) }}"
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
                    Crie um caso para começar a organizar seus agentes e registros.
                </p>

                <a href="{{ route('campaigns.create') }}" class="btn">
                    + Abrir primeira investigação
                </a>
            </div>

        @endforelse

    </section>

</div>

@endsection