```blade
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Criação de personagem</title>
</head>
<body>
    <h1>Criação de personagem</h1>

    <p>
        <strong>Campanha:</strong>
        {{ $campaign->title }}
    </p>

    <p>
        <strong>Personagem:</strong>
        {{ $character->name }}
    </p>

    @if (session('success'))
        <p>{{ session('success') }}</p>
    @endif

    <form
        action="/campanhas/{{ $campaign->id }}/personagens/{{ $character->id }}/detalhes"
        method="POST"
    >
        @csrf
        @method('PUT')

        <hr>

        <h2>Etapa 1 — Identidade</h2>

        <div>
            <label for="name">Nome do personagem</label>
            <br>
            <input
                type="text"
                id="name"
                name="name"
                value="{{ old('name', $character->name) }}"
                required
            >

            @error('name')
                <p>{{ $message }}</p>
            @enderror
        </div>

        <br>

        <div>
            <label for="age">Idade</label>
            <br>
            <input
                type="number"
                id="age"
                name="age"
                min="1"
                max="120"
                value="{{ old('age', $character->age) }}"
            >

            @error('age')
                <p>{{ $message }}</p>
            @enderror
        </div>

        <hr>

        <h2>Etapa 2 — História e aparência</h2>

        <div>
            <label for="appearance">Aparência física</label>
            <br>
            <textarea
                id="appearance"
                name="appearance"
                rows="4"
            >{{ old('appearance', $character->appearance) }}</textarea>

            @error('appearance')
                <p>{{ $message }}</p>
            @enderror
        </div>

        <br>

        <div>
            <label for="personality">Personalidade</label>
            <br>
            <textarea
                id="personality"
                name="personality"
                rows="4"
            >{{ old('personality', $character->personality) }}</textarea>

            @error('personality')
                <p>{{ $message }}</p>
            @enderror
        </div>

        <br>

        <div>
            <label for="backstory">História do personagem</label>
            <br>
            <textarea
                id="backstory"
                name="backstory"
                rows="6"
            >{{ old('backstory', $character->backstory) }}</textarea>

            @error('backstory')
                <p>{{ $message }}</p>
            @enderror
        </div>

        <hr>

        <h2>Etapa 3 — Atributos do personagem</h2>

        <p>
            Distribua os valores dos atributos conforme as regras
            que implementaremos para o sistema de RPG.
        </p>

        <div>
            <label for="agi">AGI — Agilidade</label>
            <br>
            <input
                type="number"
                id="agi"
                name="agi"
                min="0"
                max="5"
                value="{{ old('agi', $character->agi) }}"
                required
            >

            @error('agi')
                <p>{{ $message }}</p>
            @enderror
        </div>

        <br>

        <div>
            <label for="forca">FOR — Força</label>
            <br>
            <input
                type="number"
                id="forca"
                name="forca"
                min="0"
                max="5"
                value="{{ old('forca', $character->forca) }}"
                required
            >

            @error('forca')
                <p>{{ $message }}</p>
            @enderror
        </div>

        <br>

        <div>
            <label for="intelecto">INT — Intelecto</label>
            <br>
            <input
                type="number"
                id="intelecto"
                name="intelecto"
                min="0"
                max="5"
                value="{{ old('intelecto', $character->intelecto) }}"
                required
            >

            @error('intelecto')
                <p>{{ $message }}</p>
            @enderror
        </div>

        <br>

        <div>
            <label for="presenca">PRE — Presença</label>
            <br>
            <input
                type="number"
                id="presenca"
                name="presenca"
                min="0"
                max="5"
                value="{{ old('presenca', $character->presenca) }}"
                required
            >

            @error('presenca')
                <p>{{ $message }}</p>
            @enderror
        </div>

        <br>

        <div>
            <label for="vigor">VIG — Vigor</label>
            <br>
            <input
                type="number"
                id="vigor"
                name="vigor"
                min="0"
                max="5"
                value="{{ old('vigor', $character->vigor) }}"
                required
            >

            @error('vigor')
                <p>{{ $message }}</p>
            @enderror
        </div>

        <hr>

        <button type="submit">Salvar personagem</button>
    </form>

    <p>
        <a href="/campanhas/{{ $campaign->id }}/personagens/{{ $character->id }}/revisao">
            Visualizar ficha completa
        </a>
    </p>

    <p>
        <a href="/">
            Voltar para a página inicial
        </a>
    </p>
</body>
</html>