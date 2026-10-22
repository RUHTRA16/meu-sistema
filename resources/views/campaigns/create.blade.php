<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nova campanha | Meu RPG</title>
</head>
<body>
    <h1>Nova campanha</h1>

    <p>Prepare uma nova aventura para jogar.</p>

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

    <form action="{{ route('campaigns.store') }}" method="POST">
        @csrf

        <div>
            <label for="title">Nome da campanha</label>
            <input
                type="text"
                id="title"
                name="title"
                value="{{ old('title') }}"
                maxlength="255"
                required
            >
        </div>

        <button type="submit">Criar campanha</button>
    </form>

    <p>
        <a href="/">Voltar para o início</a>
    </p>
</body>
</html>