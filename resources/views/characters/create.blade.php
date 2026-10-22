<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Criar personagem | Meu RPG</title>
</head>
<body>
    <h1>Criar personagem</h1>

    <h2>Etapa 1 — Identidade</h2>

    <p>Campanha: {{ $campaign->title }}</p>

    <p>Comece definindo quem é o seu personagem.</p>

    @if ($errors->any())
        <div>
            <strong>Confira os dados informados:</strong>

            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form
        action="/campanhas/{{ $campaign->id }}/personagens"
        method="POST"
    >
        @csrf

        <div>
            <label for="name">Nome do personagem</label>

            <input
                type="text"
                id="name"
                name="name"
                value="{{ old('name') }}"
                maxlength="255"
                required
            >
        </div>

        <div>
            <label for="age">Idade</label>

            <input
                type="number"
                id="age"
                name="age"
                value="{{ old('age') }}"
                min="1"
                max="120"
            >
        </div>

        <div>
            <label for="backstory">História do personagem</label>

            <textarea
                id="backstory"
                name="backstory"
                rows="6"
                maxlength="5000"
            >{{ old('backstory') }}</textarea>
        </div>

        <button type="submit">Salvar e continuar</button>
    </form>

    <p>
        <a href="/">Voltar para o início</a>
    </p>
</body>
</html>
