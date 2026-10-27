<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>@yield('title', 'Mestre Paranormal')</title>

    <meta name="theme-color" content="#08090b">
    <meta name="description" content="Central de operações paranormais: campanhas, agentes e investigações.">

    <link rel="stylesheet" href="{{ asset('app.css') }}">

    @vite('resources/js/app.js')
</head>
<body class="app-shell">

    <div class="app-layout">

        {{-- MENU LATERAL --}}
        <aside class="sidebar" id="app-sidebar">

            <a href="{{ route('home') }}" class="brand-lockup">
                <span class="brand-symbol" aria-hidden="true">MP</span>

                <span class="brand-copy">
                    <strong>MESTRE PARANORMAL</strong>
                    <small>DIVISÃO DE INVESTIGAÇÃO</small>
                </span>
            </a>

            <div class="sidebar-classification">
                <span class="classification-dot"></span>
                ACESSO RESTRITO
            </div>

            <nav class="sidebar-nav" aria-label="Navegação principal">

                <span class="nav-section-label">CENTRAL</span>

                <a href="{{ route('home') }}"
                   class="sidebar-link {{ request()->routeIs('home') ? 'is-active' : '' }}"
                   @if (request()->routeIs('home')) aria-current="page" @endif>
                    <span class="nav-icon" aria-hidden="true">⌂</span>
                    <span>Visão geral</span>
                </a>

                <a href="{{ route('campaigns.create') }}"
                   class="sidebar-link {{ request()->routeIs('campaigns.create') ? 'is-active' : '' }}"
                   @if (request()->routeIs('campaigns.create')) aria-current="page" @endif>
                    <span class="nav-icon" aria-hidden="true">▤</span>
                    <span>Nova campanha</span>
                </a>

                <a href="{{ route('chat') }}"
                   class="sidebar-link {{ request()->routeIs('chat') ? 'is-active' : '' }}"
                   @if (request()->routeIs('chat')) aria-current="page" @endif>
                    <span class="nav-icon" aria-hidden="true">◉</span>
                    <span>Mestre de IA</span>
                    <span class="nav-link-tag">IA</span>
                </a>

                <span class="nav-section-label nav-section-spaced">
                    SISTEMA
                </span>

                <div class="sidebar-note">
                    <span class="sidebar-note-label">PROTOCOLO ATIVO</span>

                    <strong>Monitoramento paranormal</strong>

                    <p>
                        Registros e campanhas disponíveis nesta central.
                    </p>
                </div>

            </nav>

            <div class="sidebar-footer">
                <span class="system-status-light"></span>

                <span>
                    <strong>SISTEMA OPERACIONAL</strong>
                    <small>Central local ativa</small>
                </span>
            </div>

        </aside>

        {{-- ÁREA PRINCIPAL --}}
        <div class="app-main">

            {{-- BARRA SUPERIOR --}}
            <header class="topbar">

                <div class="topbar-context">
                    <span class="topbar-kicker">
                        ORDEM DE INVESTIGAÇÃO
                    </span>

                    <span class="topbar-separator">/</span>

                    <span class="topbar-current">
                        @yield('topbar', 'Central de operações')
                    </span>
                </div>

                <div class="topbar-right">

                    <span class="topbar-access">
                        <span class="system-status-light"></span>
                        Ambiente seguro
                    </span>

                    <button
                        class="mobile-menu-toggle"
                        type="button"
                        aria-controls="app-sidebar"
                        aria-expanded="false"
                        aria-label="Abrir menu de navegação"
                        onclick="
                            document.body.classList.toggle('sidebar-open');
                            this.setAttribute(
                                'aria-expanded',
                                document.body.classList.contains('sidebar-open') ? 'true' : 'false'
                            );
                        "
                    >
                        <span></span>
                        <span></span>
                        <span></span>
                    </button>

                </div>

            </header>

            {{-- CONTEÚDO DA PÁGINA --}}
            <main class="main-content">

                @hasSection('page-heading')
                    <div class="content-heading">
                        @yield('page-heading')
                    </div>
                @endif

                @yield('content')

                {{-- RODAPÉ --}}
                <footer class="app-footer">
                    <span class="footer-mark">MP</span>

                    <span>MESTRE PARANORMAL</span>

                    <span class="footer-divider">/</span>

                    <span>Registros de investigação e campanhas</span>

                    <span class="footer-version">
                        SISTEMA DE OPERAÇÕES
                    </span>
                </footer>

            </main>

        </div>

    </div>

    {{-- FUNDO DO MENU MOBILE --}}
    <div
        class="sidebar-backdrop"
        aria-hidden="true"
        onclick="
            document.body.classList.remove('sidebar-open');
            document.querySelector('.mobile-menu-toggle')?.setAttribute('aria-expanded', 'false');
        "
    ></div>

</body>
</html>