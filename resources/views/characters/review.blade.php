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

        @if (session('error'))
            <div class="alert">
                {{ session('error') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="alert">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
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

    @if ($character->creation_status === 'confirmed')
        <section class="card">
            <span class="eyebrow">Registro 06</span>
            <h2>Recursos do personagem</h2>

            <div class="attributes-grid">
                <div class="attribute-card">
                    <span class="eyebrow">PV</span>
                    <h3>Pontos de Vida</h3>
                    <h2>
                        {{ $character->pv_current ?? 0 }}
                        /
                        {{ $character->pv_max ?? 0 }}
                    </h2>
                </div>

                <div class="attribute-card">
                    <span class="eyebrow">SAN</span>
                    <h3>Sanidade</h3>
                    <h2>
                        {{ $character->san_current ?? 0 }}
                        /
                        {{ $character->san_max ?? 0 }}
                    </h2>
                </div>

                <div class="attribute-card">
                    <span class="eyebrow">PE</span>
                    <h3>Pontos de Esforço</h3>
                    <h2>
                        {{ $character->pe_current ?? 0 }}
                        /
                        {{ $character->pe_max ?? 0 }}
                    </h2>
                </div>
            </div>

            <hr>

            <h3>Atualizar recursos manualmente</h3>

            <form
                action="{{ route('characters.resources.update', [
                    'campaign' => $campaign->id,
                    'character' => $character->id,
                ]) }}"
                method="POST"
            >
                @csrf
                @method('PUT')

                <div>
                    <label for="pv_current">PV atuais</label>
                    <input
                        type="number"
                        id="pv_current"
                        name="pv_current"
                        min="0"
                        max="{{ $character->pv_max }}"
                        value="{{ old('pv_current', $character->pv_current) }}"
                        required
                    >
                    <small>Máximo: {{ $character->pv_max }}</small>
                </div>

                <div>
                    <label for="san_current">SAN atual</label>
                    <input
                        type="number"
                        id="san_current"
                        name="san_current"
                        min="0"
                        max="{{ $character->san_max }}"
                        value="{{ old('san_current', $character->san_current) }}"
                        required
                    >
                    <small>Máximo: {{ $character->san_max }}</small>
                </div>

                <div>
                    <label for="pe_current">PE atuais</label>
                    <input
                        type="number"
                        id="pe_current"
                        name="pe_current"
                        min="0"
                        max="{{ $character->pe_max }}"
                        value="{{ old('pe_current', $character->pe_current) }}"
                        required
                    >
                    <small>Máximo: {{ $character->pe_max }}</small>
                </div>

                <button type="submit">Salvar recursos</button>
            </form>

            <hr>

            <h3>Controle de Pontos de Vida</h3>

            <p>
                Registre o dano recebido ou recupere os PV perdidos.
                O sistema respeitará os limites mínimo e máximo.
            </p>

            <form
                action="{{ route('characters.pv.update', [
                    'campaign' => $campaign->id,
                    'character' => $character->id,
                ]) }}"
                method="POST"
            >
                @csrf

                <div>
                    <label for="acao_pv">Ação</label>
                    <select id="acao_pv" name="acao" required>
                        <option value="dano">Receber dano</option>
                        <option value="cura">Recuperar PV</option>
                    </select>
                </div>

                <div>
                    <label for="quantidade_pv">Quantidade de PV</label>
                    <input
                        type="number"
                        id="quantidade_pv"
                        name="quantidade"
                        min="1"
                        step="1"
                        required
                    >
                </div>

                <button type="submit">Aplicar alteração</button>
            </form>

            <hr>

            <h3>Controle de Sanidade</h3>

            <p>
                Registre a perda ou recuperação de Sanidade do personagem.
            </p>

            <form
                action="{{ route('characters.san.update', [
                    'campaign' => $campaign->id,
                    'character' => $character->id,
                ]) }}"
                method="POST"
            >
                @csrf

                <div>
                    <label for="acao_san">Ação</label>
                    <select id="acao_san" name="acao_san" required>
                        <option value="perda">Perder SAN</option>
                        <option value="recuperacao">Recuperar SAN</option>
                    </select>
                </div>

                <div>
                    <label for="quantidade_san">Quantidade de SAN</label>
                    <input
                        type="number"
                        id="quantidade_san"
                        name="quantidade_san"
                        min="1"
                        step="1"
                        required
                    >
                </div>

                <button type="submit">Aplicar alteração de SAN</button>
            </form>

            <hr>

            <h3>Controle de Pontos de Esforço</h3>

            <p>
                Registre o gasto de PE ao utilizar habilidades ou poderes,
                ou informe a quantidade de pontos recuperados.
                O sistema respeitará os limites mínimo e máximo.
            </p>

            <form
                action="{{ route('characters.pe.update', [
                    'campaign' => $campaign->id,
                    'character' => $character->id,
                ]) }}"
                method="POST"
            >
                @csrf

                <div>
                    <label for="acao_pe">Ação</label>
                    <select id="acao_pe" name="acao_pe" required>
                        <option value="gasto">Gastar PE</option>
                        <option value="recuperacao">Recuperar PE</option>
                    </select>
                </div>

                <div>
                    <label for="quantidade_pe">Quantidade de PE</label>
                    <input
                        type="number"
                        id="quantidade_pe"
                        name="quantidade_pe"
                        min="1"
                        step="1"
                        required
                    >
                </div>

                <button type="submit">Aplicar alteração de PE</button>
            </form>
        </section>
    @endif

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
        @if ($character->creation_status !== 'confirmed')
            <a
                href="{{ route('characters.details', [
                    'campaign' => $campaign->id,
                    'character' => $character->id,
                ]) }}"
                class="btn btn-secondary"
            >
                Voltar e editar
            </a>
        @endif

        <a href="{{ route('home') }}" class="btn">
            Página inicial
        </a>
    </nav>

@endsection