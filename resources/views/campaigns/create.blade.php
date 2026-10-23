@extends('layouts.app')

@section('title', 'Nova campanha | RPG Paranormal')

@section('content')

    <section class="card">
        <span class="eyebrow">Novo arquivo de investigação</span>

        <h2>Criar campanha</h2>

        <p>
            Prepare uma nova aventura, reúna seus agentes
            e descubra os mistérios que aguardam por você.
        </p>

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

        <form action="{{ route('campaigns.store') }}" method="POST">
            @csrf

            <label for="title">Nome da campanha</label>

            <input
                type="text"
                id="title"
                name="title"
                value="{{ old('title') }}"
                maxlength="255"
                placeholder="Ex.: O Segredo da Floresta"
                class="@error('title') input-error @enderror"
                required
            >

            @error('title')
                <p class="error">{{ $message }}</p>
            @enderror

            <button type="submit">Criar campanha</button>

            <a href="{{ route('home') }}" class="btn btn-secondary">
                Voltar
            </a>
        </form>
    </section>

@endsection
