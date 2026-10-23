@extends('layouts.app')

@section('title', 'Início | RPG Paranormal')

@section('content')

    <section class="stats-grid">
        <div class="stat-card">
            <span class="eyebrow">Campanhas</span>
            <h2>{{ $campaigns->count() }}</h2>
            <p>Casos registrados</p>
        </div>

        <div class="stat-card">
            <span class="eyebrow">Personagens</span>
            <h2>{{ $campaigns->sum(fn ($campaign) => $campaign->characters->count()) }}</h2>
            <p>Agentes cadastrados</p>
        </div>
    </section>

    <section class="card operations-header">
        <div class="operations-header-content">
            <span class="eyebrow">ORDEM • CENTRAL DE INTELIGÊNCIA</span>

            <h2>Central de operações</h2>

            <p>
                Os arquivos estão disponíveis, agente.
                Consulte suas campanhas, gerencie sua equipe
                e prepare-se para investigar o inexplicável.
            </p>

            <div class="operations-status">
                <span class="status-indicator"></span>
                SISTEMA OPERACIONAL
            </div>
        </div>

        <div class="operations-classification">
            <span>CLASSIFICAÇÃO</span>
            <strong>CONFIDENCIAL</strong>
            <span>ACESSO AOS ARQUIVOS</span>
        </div>
    </section>

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

    @forelse ($campaigns as $campaign)
        <section class="card">
            <span class="eyebrow">Arquivo de campanha</span>

            <h2>{{ $campaign->title }}</h2>

            <div class="character-status">
                <span>Status da campanha</span>

                <span class="status-badge status-campaign">
                    {{ match ($campaign->status) {
                        'setup' => 'Em preparação',
                        'active' => 'Em andamento',
                        'completed' => 'Concluída',
                        default => ucfirst(str_replace('_', ' ', $campaign->status ?? 'Não definido')),
                    } }}
                </span>
            </div>

            <hr>

            <h3>Personagens</h3>

            @forelse ($campaign->characters as $character)
                <div class="character-card">
                    <h3>{{ $character->name }}</h3>

                    <div class="character-status">
                        <span>Status da ficha</span>

                        @if ($character->creation_status === 'confirmed')
                            <span class="status-badge status-confirmed">
                                Confirmada
                            </span>
                        @else
                            <span class="status-badge status-draft">
                                Rascunho
                            </span>
                        @endif
                    </div>

                    <a
                        href="{{ route('characters.details', [
                            'campaign' => $campaign->id,
                            'character' => $character->id,
                        ]) }}"
                        class="btn btn-secondary"
                    >
                        Editar personagem
                    </a>

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
                            Excluir personagem
                        </button>
                    </form>

                </div>
            @empty
                <p>Esta campanha ainda não possui personagens.</p>
            @endforelse

            <hr>

            <a
                href="{{ route('characters.create', [
                    'campaign' => $campaign->id,
                ]) }}"
                class="btn"
            >
                + Criar personagem
            </a>

            <form
                action="{{ route('campaigns.destroy', [
                    'campaign' => $campaign->id,
                ]) }}"
                method="POST"
                onsubmit="return confirm('Tem certeza de que deseja excluir esta campanha? Campanhas com personagens não podem ser excluídas.');"
            >
                @csrf
                @method('DELETE')

                <button type="submit" class="btn btn-secondary">
                    Excluir campanha
                </button>
            </form>

        </section>
    @empty
        <section class="card">
            <h2>Nenhuma campanha encontrada</h2>

            <p>
                Seu primeiro caso começa aqui. Crie uma campanha
                para começar a montar sua equipe.
            </p>

            <a href="{{ route('campaigns.create') }}" class="btn">
                Criar primeira campanha
            </a>
        </section>
    @endforelse

@endsection