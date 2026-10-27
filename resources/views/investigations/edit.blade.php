@extends('layouts.app')

@section('title', 'Editar registro | Mestre Paranormal')
@section('topbar', 'Editar registro')

@section('content')

<div class="operations-dashboard">

    <section class="dashboard-heading">
        <div>
            <span class="eyebrow">ARQUIVO CONFIDENCIAL</span>
            <h1>Editar registro</h1>
            <p>Atualize as informações documentadas no diário.</p>
        </div>

        <a
            href="{{ route('investigation.entries.index', ['campaign' => $campaign->id]) }}"
            class="btn btn-secondary"
        >
            Voltar ao diário
        </a>
    </section>

    @if ($errors->any())
        <div class="alert">
            <strong>Verifique os campos informados:</strong>

            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <section class="metric-card">

        <span class="eyebrow">
            MP-{{ str_pad((string) $campaign->id, 4, '0', STR_PAD_LEFT) }}
        </span>

        <h2 style="margin-top:10px;">
            {{ $campaign->title }}
        </h2>

        <form
            action="{{ route('investigation.entries.update', [
                'campaign' => $campaign->id,
                'entry' => $entry->id,
            ]) }}"
            method="POST"
            style="margin-top:24px;"
        >
            @csrf
            @method('PUT')

            <div style="margin-bottom:18px;">
                <label for="title">Título do registro</label>

                <input
                    type="text"
                    id="title"
                    name="title"
                    value="{{ old('title', $entry->title) }}"
                    maxlength="150"
                    required
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
                            @selected(old('category', $entry->category) === $value)
                        >
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div style="margin-bottom:18px;">
                <label for="occurred_at">Data do acontecimento</label>

                <input
                    type="date"
                    id="occurred_at"
                    name="occurred_at"
                    value="{{ old('occurred_at', $entry->occurred_at?->format('Y-m-d')) }}"
                    style="display:block;width:100%;margin-top:8px;padding:12px;"
                >
            </div>

            <div style="margin-bottom:22px;">
                <label for="content">Descrição</label>

                <textarea
                    id="content"
                    name="content"
                    rows="7"
                    maxlength="10000"
                    required
                    style="display:block;width:100%;margin-top:8px;padding:12px;resize:vertical;"
                >{{ old('content', $entry->content) }}</textarea>
            </div>

            <button type="submit" class="btn">
                Salvar alterações
            </button>

            <a
                href="{{ route('investigation.entries.index', ['campaign' => $campaign->id]) }}"
                class="btn btn-secondary"
            >
                Cancelar
            </a>

        </form>

    </section>

</div>

@endsection