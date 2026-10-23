<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>@yield('title', 'RPG Paranormal')</title>
    
        <link rel="stylesheet" href="{{ asset('app.css') }}">
    
</head>
<body>
    <main class="container">
            <header class="page-header">
                <a href="{{ route('home') }}">
                    <span class="eyebrow">Arquivo confidencial</span>
                    <h1>RPG Paranormal</h1>
                </a>

                <p>Investigue. Sobreviva. Descubra a verdade.</p>

                <nav class="main-nav" aria-label="Navegação principal">
                    <a href="{{ route('home') }}" class="btn btn-secondary">
                        Início
                    </a>

                    <a href="{{ route('campaigns.create') }}" class="btn">
                        + Nova campanha
                    </a>
                </nav>
            </header>

        @yield('content')

        <hr>

        <footer>
            <p>RPG Paranormal — Sistema de campanhas e personagens.</p>
        </footer>
    </main>
</body>
</html>
