@extends('layouts.app')

@section('title', 'Revisão da ficha | RPG Paranormal')

@section('content')

    <section class="card">
        <span class="eyebrow">Arquivo confidencial — Revisão</span>

        <h2>Ficha do personagem</h2>

        <p>
            <strong>Campanha:</strong> {{ $campaign->title }}
        </p>

        <p>
            Confira todas as informações antes de confirmar o cadastro
            do personagem.
        </p>

        @if (session('success'))
            <div class="alert">
                {{ session('success') }}
            </div>
        @endif
    </section>

    <section class="card">
        <span class="eyebrow">Registro 01</span>
        <h2>Identidade</h2>

        <p>
            <strong>Nome:</strong> {{ $character->name }}
        </p>

        <p>
            <strong>Idade:</strong>
            {{ $character->age ?? 'Não informada' }}
        </p>
    </section>

    <section class="card">
        <span class="eyebrow">Registro 02</span>
        <h2>Atributos</h2>

        <div class="attributes-grid">
            <div class="attribute-card">
                <span class="eyebrow">AGI</span>
                <h3>Agilidade</h3>
                <h2>{{ $character->agi }}</h2>
            </div>

            <div class="attribute-card">
                <span class="eyebrow">FOR</span>
                <h3>Força</h3>
                <h2>{{ $character->forca }}</h2>
            </div>

            <div class="attribute-card">
                <span class="eyebrow">INT</span>
                <h3>Intelecto</h3>
                <h2>{{ $character->intelecto }}</h2>
            </div>

            <div class="attribute-card">
                <span class="eyebrow">PRE</span>
                <h3>Presença</h3>
                <h2>{{ $character->presenca }}</h2>
            </div>

            <div class="attribute-card">
                <span class="eyebrow">VIG</span>
                <h3>Vigor</h3>
                <h2>{{ $character->vigor }}</h2>
            </div>
        </div>
    </section>

    <section class="card">
        <span class="eyebrow">Registro 03</span>
        <h2>Aparência física</h2>

        <p>
            {{ $character->appearance ?: 'Ainda não informada.' }}
        </p>
    </section>

    <section class="card">
        <span class="eyebrow">Registro 04</span>
        <h2>Personalidade</h2>

        <p>
            {{ $character->personality ?: 'Ainda não informada.' }}
        </p>
    </section>

    <section class="card">
        <span class="eyebrow">Registro 05</span>
        <h2>História do personagem</h2>

        <p>
            {{ $character->backstory ?: 'Ainda não informada.' }}
        </p>
    </section>

    <section class="card">
        <span class="eyebrow">Situação do registro</span>
        <h2>Status da ficha</h2>

        @if ($character->creation_status === 'confirmed')
            <div class="alert">
                <strong>Ficha confirmada!</strong>
                <p>O personagem foi confirmado com sucesso.</p>
            </div>
        @else
            <p>A ficha ainda está em rascunho.</p>

            <form
                action="{{ url('/campanhas/' . $campaign->id . '/personagens/' . $character->id . '/confirmar') }}"
                method="POST"
            >
                @csrf

                <button type="submit">
                    Confirmar ficha
                </button>
            </form>
        @endif
    </section>

    <nav>
        <a
            href="{{ route('characters.details', [
                'campaign' => $campaign->id,
                'character' => $character->id,
            ]) }}"
            class="btn btn-secondary"
        >
            Voltar e editar
        </a>

        <a href="{{ route('home') }}" class="btn">
            Página inicial
        </a>
    </nav>

@endsection