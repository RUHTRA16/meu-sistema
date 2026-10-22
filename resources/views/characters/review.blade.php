```blade
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Revisão da ficha</title>
</head>

<body>
    <h1>Revisão da ficha do personagem</h1>

    @if (session('success'))
        <p>{{ session('success') }}</p>
    @endif

    <p>
        <strong>Campanha:</strong>
        {{ $campaign->title }}
    </p>

    <hr>

    {{-- Identidade do personagem --}}
    <section>
        <h2>Identidade</h2>

        <p>
            <strong>Nome:</strong>
            {{ $character->name }}
        </p>

        <p>
            <strong>Idade:</strong>
            {{ $character->age ?? 'Não informada' }}
        </p>
    </section>

    <hr>

    {{-- Atributos do personagem --}}
    <section>
        <h2>Atributos</h2>

        <ul>
            <li>
                <strong>AGI — Agilidade:</strong>
                {{ $character->agi }}
            </li>

            <li>
                <strong>FOR — Força:</strong>
                {{ $character->forca }}
            </li>

            <li>
                <strong>INT — Intelecto:</strong>
                {{ $character->intelecto }}
            </li>

            <li>
                <strong>PRE — Presença:</strong>
                {{ $character->presenca }}
            </li>

            <li>
                <strong>VIG — Vigor:</strong>
                {{ $character->vigor }}
            </li>
        </ul>
    </section>

    <hr>

    {{-- Aparência física --}}
    <section>
        <h2>Aparência</h2>

        <p>
            {{ $character->appearance ?: 'Ainda não informada.' }}
        </p>
    </section>

    {{-- Personalidade --}}
    <section>
        <h2>Personalidade</h2>

        <p>
            {{ $character->personality ?: 'Ainda não informada.' }}
        </p>
    </section>

    {{-- História do personagem --}}
    <section>
        <h2>História</h2>

        <p>
            {{ $character->backstory ?: 'Ainda não informada.' }}
        </p>
    </section>

    <hr>

    {{-- Status e confirmação da ficha --}}
    <section>
        <h2>Status da ficha</h2>

        @if ($character->creation_status === 'confirmed')
            <p>Confirmada</p>
            <p>Ficha confirmada!</p>
        @else
            <p>Rascunho</p>

            <form
                action="/campanhas/{{ $campaign->id }}/personagens/{{ $character->id }}/confirmar"
                method="POST"
            >
                @csrf

                <button type="submit">
                    Confirmar ficha
                </button>
            </form>
        @endif
    </section>

    <hr>

    {{-- Navegação --}}
    <nav>
        <p>
            <a href="{{ route('characters.details', [
                'campaign' => $campaign->id,
                'character' => $character->id,
            ]) }}">
                Voltar e editar
            </a>
        </p>

        <p>
            <a href="{{ route('home') }}">
                Voltar para a página inicial
            </a>
        </p>
    </nav>
</body>

</html>