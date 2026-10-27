@extends('layouts.app')

@section('title', 'Editar sessão | Mestre Paranormal')
@section('topbar', 'Editar sessão')

@section('content')

<div class="operations-dashboard">

    {{-- CABEÇALHO --}}
    <section class="dashboard-heading">
        <div>
            <span class="eyebrow">DIVISÃO DE INVESTIGAÇÃO PARANORMAL</span>
            <h1>Editar sessão</h1>
            <p>Atualize os dados e o planejamento desta operação.</p>
        </div>

        <a
            href="{{ route('sessions.index', ['campaign' => $campaign->id]) }}"
            class="btn btn-secondary"
        >
            Voltar às sessões
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
                <span class="meta-label">SESSÃO</span>

                <strong>
                    Nº {{ str_pad((string) $session->session_number, 2, '0', STR_PAD_LEFT) }}
                </strong>
            </div>

            <div>
                <span class="meta-label">CADASTRADA EM</span>

                <strong>
                    {{ $session->created_at->format('d/m/Y H:i') }}
                </strong>
            </div>
        </div>
    </section>

    {{-- MENSAGENS DE VALIDAÇÃO --}}
    @if ($errors->any())
        <div class="alert">
            <strong>Não foi possível atualizar a sessão:</strong>

            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- FORMULÁRIO DE EDIÇÃO --}}
    <section class="metric-card">
        <span class="eyebrow">ATUALIZAÇÃO DE REGISTRO</span>

        <h2 style="margin-top:10px;">
            Dados da sessão
        </h2>

        <form
            action="{{ route('sessions.update', [
                'campaign' => $campaign->id,
                'session' => $session->id,
            ]) }}"
            method="POST"
            style="margin-top:24px;"
        >
            @csrf
            @method('PUT')

            <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:18px;margin-bottom:18px;">

                <div>
                    <label for="session_number">
                        Número da sessão
                    </label>

                    <input
                        type="number"
                        id="session_number"
                        name="session_number"
                        min="1"
                        value="{{ old('session_number', $session->session_number) }}"
                        required
                        style="display:block;width:100%;margin-top:8px;padding:12px;"
                    >
                </div>

                <div>
                    <label for="scheduled_at">
                        Data prevista
                    </label>

                    <input
                        type="date"
                        id="scheduled_at"
                        name="scheduled_at"
                        value="{{ old(
                            'scheduled_at',
                            $session->scheduled_at?->format('Y-m-d')
                        ) }}"
                        style="display:block;width:100%;margin-top:8px;padding:12px;"
                    >
                </div>

                <div>
                    <label for="status">
                        Status da sessão
                    </label>

                    <select
                        id="status"
                        name="status"
                        required
                        style="display:block;width:100%;margin-top:8px;padding:12px;"
                    >
                        <option
                            value="planned"
                            @selected(old('status', $session->status) === 'planned')
                        >
                            Planejada
                        </option>

                        <option
                            value="completed"
                            @selected(old('status', $session->status) === 'completed')
                        >
                            Concluída
                        </option>
                    </select>
                </div>

            </div>

            <div style="margin-bottom:18px;">
                <label for="title">
                    Título da sessão
                </label>

                <input
                    type="text"
                    id="title"
                    name="title"
                    value="{{ old('title', $session->title) }}"
                    maxlength="150"
                    required
                    placeholder="Ex.: O mistério da mansão abandonada"
                    style="display:block;width:100%;margin-top:8px;padding:12px;"
                >
            </div>

            <div style="margin-bottom:22px;">
                <label for="summary">
                    Resumo ou planejamento
                </label>

                <textarea
                    id="summary"
                    name="summary"
                    rows="8"
                    maxlength="20000"
                    placeholder="Descreva os acontecimentos, as descobertas e as decisões importantes..."
                    style="display:block;width:100%;margin-top:8px;padding:12px;resize:vertical;"
                >{{ old('summary', $session->summary) }}</textarea>
            </div>

            {{-- AÇÕES --}}
            <div class="investigation-actions">

                <button type="submit" class="btn">
                    Salvar alterações
                </button>

                <a
                    href="{{ route('sessions.index', ['campaign' => $campaign->id]) }}"
                    class="btn btn-secondary"
                >
                    Cancelar
                </a>

            </div>

        </form>
    </section>

</div>

@endsection