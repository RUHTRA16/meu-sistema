@extends('layouts.app')

@section('title', 'Criar personagem | RPG Paranormal')

@section('content')

    <section class="card">
        <span class="eyebrow">Etapa 01 — Identificação</span>

        <h2>Criar personagem</h2>

        <p>
            Campanha:
            <strong>{{ $campaign->title }}</strong>
        </p>

        <p>
            Antes de enfrentar o desconhecido, defina quem estará
            entrando nessa investigação.
        </p>
    </section>

    <section class="card">
        <h2>Identidade do personagem</h2>

        @if ($errors->any())
            <div class="alert">
                <strong>Confira os dados informados:</strong>

                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form
            action="{{ route('characters.store', ['campaign' => $campaign->id]) }}"
            method="POST"
        >
            @csrf

            <label for="name">Nome do personagem</label>

            <input
                type="text"
                id="name"
                name="name"
                value="{{ old('name') }}"
                maxlength="255"
                placeholder="Digite o nome do agente"
                class="@error('name') input-error @enderror"
                required
            >

            @error('name')
                <p class="error">{{ $message }}</p>
            @enderror

            <label for="age">Idade</label>

            <input
                type="number"
                id="age"
                name="age"
                value="{{ old('age') }}"
                min="1"
                max="120"
                placeholder="Ex.: 25"
                class="@error('age') input-error @enderror"
                required
            >

            @error('age')
                <p class="error">{{ $message }}</p>
            @enderror

            <label for="backstory">História do personagem</label>

            <textarea
                id="backstory"
                name="backstory"
                rows="6"
                maxlength="5000"
                placeholder="Conte um pouco sobre o passado do personagem..."
                class="@error('backstory') input-error @enderror"
            >{{ old('backstory') }}</textarea>

            @error('backstory')
                <p class="error">{{ $message }}</p>
            @enderror

            <button type="submit">Salvar e continuar</button>

            <a href="{{ route('home') }}" class="btn btn-secondary">
                Voltar ao início
            </a>
        </form>
    </section>

@endsection