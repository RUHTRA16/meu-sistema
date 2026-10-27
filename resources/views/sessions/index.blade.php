@extends('layouts.app')

@section('title', 'Sessões da campanha | Mestre Paranormal')
@section('topbar', 'Sessões da campanha')

@section('content')

<div class="operations-dashboard">

    {{-- CABEÇALHO --}}
    <section class="dashboard-heading">
        <div>
            <span class="eyebrow">DIVISÃO DE INVESTIGAÇÃO PARANORMAL</span>
            <h1>Sessões da campanha</h1>
            <p>Planeje partidas, registre acontecimentos e acompanhe o progresso do caso.</p>
        </div>

        <a
            href="{{ route('home') }}"
            class="btn btn-secondary"
        >
            Voltar à central
        </a>
    </section>

    {{-- IDENTIFICAÇÃO DA CAMPANHA --}}
    <section class="metric-card" style="margin-bottom:24px;">
        <span class="eyebrow">DOSSIÊ DO CASO</span>

        <h2 style="margin-top:10px;">
            {{ $campaign->title }}
        </h2>

        <div class="investigation-meta">
            <div>
                <span class="meta-label">IDENTIFICAÇÃO</span>
                <strong>
                    MP-{{ str_pad((string) $campaign->id, 4, '0', STR_PAD_LEFT) }}
                </strong>
            </div>

            <div>
                <span class="meta-label">TOTAL DE SESSÕES</span>
                <strong>{{ $sessions->count() }}</strong>
            </div>

            <div>
                <span class="meta-label">CONCLUÍDAS</span>
                <strong>{{ $sessions->where('status', 'completed')->count() }}</strong>
            </div>

            <div>
                <span class="meta-label">PLANEJADAS</span>
                <strong>{{ $sessions->where('status', 'planned')->count() }}</strong>
            </div>
        </div>
    </section>

    {{-- MENSAGENS --}}
    @if (session('success'))
        <div class="alert">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="alert">
            <strong>Verifique os seguintes campos:</strong>

            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- FORMULÁRIO DE CADASTRO --}}
    <section class="metric-card" style="margin-bottom:30px;">
        <span class="eyebrow">NOVO REGISTRO</span>
        <h2 style="margin-top:10px;">Planejar sessão</h2>

        <form
            action="{{ route('sessions.store', ['campaign' => $campaign->id]) }}"
            method="POST"
            style="margin-top:24px;"
        >
            @csrf

            <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:18px;margin-bottom:18px;">

                <div>
                    <label for="session_number">Número da sessão</label>

                    <input
                        type="number"
                        id="session_number"
                        name="session_number"
                        min="1"
                        value="{{ old('session_number', ($sessions->max('session_number') ?? 0) + 1) }}"
                        required
                        style="display:block;width:100%;margin-top:8px;padding:12px;"
                    >
                </div>

                <div>
                    <label for="scheduled_at">Data prevista</label>

                    <input
                        type="date"
                        id="scheduled_at"
                        name="scheduled_at"
                        value="{{ old('scheduled_at') }}"
                        style="display:block;width:100%;margin-top:8px;padding:12px;"
                    >
                </div>

                <div>
                    <label for="status">Status inicial</label>

                    <select
                        id="status"
                        name="status"
                        required
                        style="display:block;width:100%;margin-top:8px;padding:12px;"
                    >
                        <option value="planned" @selected(old('status', 'planned') === 'planned')>
                            Planejada
                        </option>

                        <option value="completed" @selected(old('status') === 'completed')>
                            Concluída
                        </option>
                    </select>
                </div>

            </div>

            <div style="margin-bottom:18px;">
                <label for="title">Título da sessão</label>

                <input
                    type="text"
                    id="title"
                    name="title"
                    value="{{ old('title') }}"
                    maxlength="150"
                    required
                    placeholder="Ex.: O mistério da mansão abandonada"
                    style="display:block;width:100%;margin-top:8px;padding:12px;"
                >
            </div>

            <div style="margin-bottom:22px;">
                <label for="summary">Resumo ou planejamento</label>

                <textarea
                    id="summary"
                    name="summary"
                    rows="5"
                    maxlength="20000"
                    placeholder="Descreva o que será investigado ou os principais acontecimentos da sessão..."
                    style="display:block;width:100%;margin-top:8px;padding:12px;resize:vertical;"
                >{{ old('summary') }}</textarea>
            </div>

            <button type="submit" class="btn">
                Cadastrar sessão
            </button>
        </form>
    </section>

    {{-- HISTÓRICO --}}
    <section class="investigations-section">

        <div class="section-heading">
            <div>
                <span class="eyebrow">ARQUIVO DE OPERAÇÕES</span>
                <h2>Histórico de sessões</h2>
                <p>Acompanhe o desenvolvimento de cada partida da campanha.</p>
            </div>

            <span class="record-counter">
                {{ $sessions->count() }}
                {{ $sessions->count() === 1 ? 'SESSÃO' : 'SESSÕES' }}
            </span>
        </div>

        @forelse ($sessions as $session)

            <article class="investigation-card" style="margin-bottom:20px;">

                <div class="investigation-body">

                    <div class="investigation-title-row">
                        <div class="investigation-title">
                            <span class="eyebrow">
                                SESSÃO Nº {{ str_pad((string) $session->session_number, 2, '0', STR_PAD_LEFT) }}
                            </span>

                            <h3>{{ $session->title }}</h3>
                        </div>

                        <span class="status-badge {{ $session->status === 'completed' ? 'status-confirmed' : 'status-draft' }}">
                            {{ $session->status === 'completed' ? 'Concluída' : 'Planejada' }}
                        </span>
                    </div>

                    <div style="margin-top:18px;white-space:pre-wrap;overflow-wrap:anywhere;">{{ $session->summary ?: 'Nenhum resumo registrado para esta sessão.' }}</div>

                    <div class="investigation-meta" style="margin-top:20px;">
                        <div>
                            <span class="meta-label">DATA PREVISTA</span>

                            <strong>
                                {{ $session->scheduled_at
                                    ? $session->scheduled_at->format('d/m/Y')
                                    : 'Não definida' }}
                            </strong>
                        </div>

                        <div>
                            <span class="meta-label">REGISTRADA EM</span>

                            <strong>
                                {{ $session->created_at->format('d/m/Y H:i') }}
                            </strong>
                        </div>
                    </div>

                    {{-- AÇÕES --}}
                    <div class="investigation-actions" style="margin-top:20px;">

                        <a
                            href="{{ route('sessions.edit', [
                                'campaign' => $campaign->id,
                                'session' => $session->id,
                            ]) }}"
                            class="btn btn-secondary"
                        >
                            Editar sessão
                        </a>

                        @if ($session->status !== 'completed')
                            <form
                                action="{{ route('sessions.complete', [
                                    'campaign' => $campaign->id,
                                    'session' => $session->id,
                                ]) }}"
                                method="POST"
                                onsubmit="return confirm('Deseja marcar esta sessão como concluída?');"
                            >
                                @csrf
                                @method('PATCH')

                                <button type="submit" class="btn">
                                    Marcar como concluída
                                </button>
                            </form>
                        @endif

                        <form
                            action="{{ route('sessions.destroy', [
                                'campaign' => $campaign->id,
                                'session' => $session->id,
                            ]) }}"
                            method="POST"
                            onsubmit="return confirm('Deseja realmente excluir esta sessão? Esta ação não pode ser desfeita.');"
                        >
                            @csrf
                            @method('DELETE')

                            <button type="submit" class="btn btn-secondary">
                                Excluir sessão
                            </button>
                        </form>

                    </div>

                </div>

            </article>

        @empty

            <div class="investigations-empty">
                <div class="empty-emblem" aria-hidden="true">MP</div>

                <span class="eyebrow">NENHUMA SESSÃO REGISTRADA</span>

                <h3>A próxima operação começa aqui.</h3>

                <p>
                    Cadastre a primeira sessão para começar a organizar
                    as partidas e os acontecimentos desta campanha.
                </p>
            </div>

        @endforelse

    </section>

</div>

@endsection