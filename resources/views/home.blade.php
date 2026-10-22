
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meu RPG</title>
</head>
<body>
    <h1>Meu RPG</h1>

    <p>Crie personagens, organize campanhas e viva suas próprias aventuras.</p>

    @if (session('success'))
        <p>{{ session('success') }}</p>
    @endif

    <section>
        <h2>Campanhas</h2>

        <p>
            <a href="/campanhas/nova">Nova campanha</a>
        </p>

        @forelse ($campaigns as $campaign)
            
        <article>
            <h3>{{ $campaign->title }}</h3>

            <p>Status: {{ $campaign->status }}</p>

            <p>
                Criada em:
                {{ $campaign->created_at->format('d/m/Y H:i') }}
            </p>

            <a href="/campanhas/{{ $campaign->id }}/personagens/novo">
                Criar personagem
            </a>
        </article>

        @empty
            <p>Você ainda não criou nenhuma campanha.</p>
        @endforelse
    </section>
        
    <section>
        <h2>Personagens</h2>

        @forelse ($campaigns as $campaign)
            <h3>{{ $campaign->title }}</h3>

            @forelse ($campaign->characters as $character)
                <article>
                    <h3>{{ $character->name }}</h3>

                    Status:
                     @if ($character->creation_status === 'confirmed')
                        Confirmada
                     @else
                        Rascunho
                    @endif

                    <a href="/campanhas/{{ $campaign->id }}/personagens/{{ $character->id }}/detalhes">
                        Editar personagem
                    </a>
                </article>
            @empty
                <p>Nenhum personagem nesta campanha ainda.</p>
            @endforelse
        @empty
            <p>Crie uma campanha para começar.</p>
        @endforelse
    </section>

</body>

</html>
