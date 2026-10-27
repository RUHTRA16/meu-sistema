@extends('layouts.app')

@section('title', 'Diário de investigação | Mestre Paranormal')
@section('topbar', 'Diário de investigação')

@section('content')

<div class="operations-dashboard">

    <section class="dashboard-heading">
        <div>
            <span class="eyebrow">DIVISÃO DE INVESTIGAÇÃO PARANORMAL</span>
            <h1>Diário de investigação</h1>
            <p>Registre pistas, evidências, suspeitos e acontecimentos do caso.</p>
        </div>

        <a href="{{ route('home') }}" class="btn btn-secondary">
            Voltar à central
        </a>
    </section>

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
                <span class="meta-label">REGISTROS</span>
                <strong>{{ $entries->count() }}</strong>
            </div>
        </div>
    </section>

    @if (session('success'))
        <div class="alert">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="alert">
            <strong>Não foi possível salvar o registro:</strong>

            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <section class="metric-card" style="margin-bottom:30px;">
        <span class="eyebrow">NOVO REGISTRO</span>
        <h2 style="margin-top:10px;">Adicionar ao diário</h2>

        <form
            action="{{ route('investigation.entries.store', ['campaign' => $campaign->id]) }}"
            method="POST"
            style="margin-top:24px;"
        >
            @csrf

            <div style="margin-bottom:18px;">
                <label for="title">Título do registro</label>

                <input
                    type="text"
                    id="title"
                    name="title"
                    value="{{ old('title') }}"
                    maxlength="150"
                    required
                    placeholder="Ex.: Símbolos encontrados na residência"
                    style="display:block;width:100%;margin-top:8px;padding:12px;"
                >
            </div>

            <div style="margin-bottom:18px;">
                <label for="category">Categoria</label>

                <select
                    id="category"
                    name="category"
                    required
                    style="display:block;width:100%;margin-top:8px;padding:12px;"
                >
                    @foreach ([
                        'acontecimento' => 'Acontecimento',
                        'pista' => 'Pista',
                        'evidencia' => 'Evidência',
                        'suspeito' => 'Suspeito',
                        'descoberta' => 'Descoberta',
                    ] as $value => $label)
                        <option
                            value="{{ $value }}"
                            @selected(old('category', 'acontecimento') === $value)
                        >
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div style="margin-bottom:18px;">
                <label for="occurred_at">
                    Data do acontecimento (opcional)
                </label>

                <input
                    type="date"
                    id="occurred_at"
                    name="occurred_at"
                    value="{{ old('occurred_at') }}"
                    style="display:block;width:100%;margin-top:8px;padding:12px;"
                >
            </div>

            <div style="margin-bottom:22px;">
                <label for="content">Descrição</label>

                <textarea
                    id="content"
                    name="content"
                    rows="6"
                    maxlength="10000"
                    required
                    placeholder="Descreva o que foi encontrado e por que essa informação é importante..."
                    style="display:block;width:100%;margin-top:8px;padding:12px;resize:vertical;"
                >{{ old('content') }}</textarea>
            </div>

            <button type="submit" class="btn">
                Salvar registro
            </button>
        </form>
    </section>

    <section class="investigations-section">

        <div class="section-heading">
            <div>
                <span class="eyebrow">ARQUIVO CONFIDENCIAL</span>
                <h2>Histórico da investigação</h2>
                <p>Todos os registros associados a este caso.</p>
            </div>

            <span class="record-counter">
                {{ $entries->count() }}
                {{ $entries->count() === 1 ? 'REGISTRO' : 'REGISTROS' }}
            </span>
        </div>

        @forelse ($entries as $entry)

            <article class="investigation-card" style="margin-bottom:20px;">
                <div class="investigation-body">

                    <div class="investigation-title-row">
                        <div class="investigation-title">
                            <span class="eyebrow">
                                {{ strtoupper($entry->category) }}
                            </span>

                            <h3>{{ $entry->title }}</h3>
                        </div>
                    </div>

                    <div style="margin-top:18px;white-space:pre-wrap;overflow-wrap:anywhere;">{{ $entry->content }}</div>

                    <div class="investigation-meta" style="margin-top:20px;">
                        <div>
                            <span class="meta-label">DATA DO ACONTECIMENTO</span>

                            <strong>
                                {{ $entry->occurred_at
                                    ? $entry->occurred_at->format('d/m/Y')
                                    : 'Não informada' }}
                            </strong>
                        </div>

                        <div>
                            <span class="meta-label">REGISTRADO EM</span>

                            <strong>
                                {{ $entry->created_at->format('d/m/Y H:i') }}
                            </strong>
                        </div>
                    </div>

                    <div class="investigation-actions" style="margin-top:20px;">

                        <a
                            href="{{ route('investigation.entries.edit', [
                                'campaign' => $campaign->id,
                                'entry' => $entry->id,
                            ]) }}"
                            class="btn btn-secondary"
                        >
                            Editar registro
                        </a>

                        <form
                            action="{{ route('investigation.entries.destroy', [
                                'campaign' => $campaign->id,
                                'entry' => $entry->id,
                            ]) }}"
                            method="POST"
                            onsubmit="return confirm('Deseja realmente excluir este registro? Esta ação não pode ser desfeita.');"
                        >
                            @csrf
                            @method('DELETE')

                            <button type="submit" class="btn btn-secondary">
                                Excluir registro
                            </button>
                        </form>

                    </div>

                </div>
            </article>

        @empty

            <div class="investigations-empty">
                <div class="empty-emblem" aria-hidden="true">MP</div>

                <span class="eyebrow">DIÁRIO SEM REGISTROS</span>

                <h3>Nenhuma pista documentada até agora.</h3>

                <p>
                    Utilize o formulário acima para registrar a primeira
                    informação desta investigação.
                </p>
            </div>

        @endforelse

    </section>

</div>

@endsection