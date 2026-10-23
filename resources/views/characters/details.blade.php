@extends('layouts.app')

@section('title', 'Ficha do personagem | RPG Paranormal')

@section('content')

    <section class="card">
        <span class="eyebrow">Arquivo confidencial — Agente</span>

        <h2>Criação de personagem</h2>

        <p><strong>Campanha:</strong> {{ $campaign->title }}</p>
        <p><strong>Personagem:</strong> {{ $character->name }}</p>

        <p>
            Preencha cada etapa para construir a ficha do seu personagem.
        </p>
    </section>

    <section class="card">
        <span class="eyebrow">Progresso da criação</span>

        <div class="creation-steps">
            <div class="creation-step active" id="step-indicator-1">
                <span>01</span>
                <p>Identidade</p>
            </div>

            <div class="creation-step" id="step-indicator-2">
                <span>02</span>
                <p>História e aparência</p>
            </div>

            <div class="creation-step" id="step-indicator-3">
                <span>03</span>
                <p>Classe e origem</p>
            </div>

            <div class="creation-step" id="step-indicator-4">
                <span>04</span>
                <p>Atributos</p>
            </div>
        </div>
    </section>

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
            <strong>Não foi possível salvar todos os dados:</strong>

            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form
        id="character-form"
        action="{{ route('characters.details.update', [
            'campaign' => $campaign->id,
            'character' => $character->id,
        ]) }}"
        method="POST"
    >
        @csrf
        @method('PUT')

        <input
            type="hidden"
            name="current_step"
            id="current_step"
            value="{{ old('current_step', 1) }}"
        >

        {{-- ETAPA 1: IDENTIDADE --}}
        <section class="card creation-panel" id="step-1">
            <span class="eyebrow">Etapa 01 de 04</span>
            <h2>Identidade</h2>

            <label for="name">Nome do personagem</label>
            <input
                type="text"
                id="name"
                name="name"
                value="{{ old('name', $character->name) }}"
                maxlength="255"
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
                min="1"
                max="120"
                value="{{ old('age', $character->age) }}"
            >

            @error('age')
                <p class="error">{{ $message }}</p>
            @enderror
        </section>

        {{-- ETAPA 2: HISTÓRIA E APARÊNCIA --}}
        <section class="card creation-panel" id="step-2" hidden>
            <span class="eyebrow">Etapa 02 de 04</span>
            <h2>História e aparência</h2>

            <label for="appearance">Aparência física</label>
            <textarea
                id="appearance"
                name="appearance"
                rows="4"
                maxlength="5000"
                placeholder="Descreva a aparência do personagem..."
            >{{ old('appearance', $character->appearance) }}</textarea>

            @error('appearance')
                <p class="error">{{ $message }}</p>
            @enderror

            <label for="personality">Personalidade</label>
            <textarea
                id="personality"
                name="personality"
                rows="4"
                maxlength="5000"
                placeholder="Como o personagem pensa, sente e age?"
            >{{ old('personality', $character->personality) }}</textarea>

            @error('personality')
                <p class="error">{{ $message }}</p>
            @enderror

            <label for="backstory">História do personagem</label>
            <textarea
                id="backstory"
                name="backstory"
                rows="6"
                maxlength="5000"
                placeholder="Conte o passado e os acontecimentos importantes..."
            >{{ old('backstory', $character->backstory) }}</textarea>

            @error('backstory')
                <p class="error">{{ $message }}</p>
            @enderror
        </section>

        {{-- ETAPA 3: CLASSE E ORIGEM --}}
        <section class="card creation-panel" id="step-3" hidden>
            <span class="eyebrow">Etapa 03 de 04</span>
            <h2>Classe e origem</h2>

            <p>
                Escolha a classe do agente e informe sua origem.
                Esses dados serão utilizados nas próximas etapas da ficha.
            </p>

            <label for="character_class">Classe do personagem</label>
            <select
                id="character_class"
                name="character_class"
                required
            >
                <option value="">Selecione uma classe</option>

                <option
                    value="Combatente"
                    {{ old('character_class', $character->character_class) === 'Combatente' ? 'selected' : '' }}
                >
                    Combatente
                </option>

                <option
                    value="Especialista"
                    {{ old('character_class', $character->character_class) === 'Especialista' ? 'selected' : '' }}
                >
                    Especialista
                </option>

                <option
                    value="Ocultista"
                    {{ old('character_class', $character->character_class) === 'Ocultista' ? 'selected' : '' }}
                >
                    Ocultista
                </option>
            </select>

            @error('character_class')
                <p class="error">{{ $message }}</p>
            @enderror

            <label for="origin">Origem do personagem</label>
            <input
                type="text"
                id="origin"
                name="origin"
                value="{{ old('origin', $character->origin) }}"
                maxlength="100"
                placeholder="Informe a origem do personagem"
                required
            >

            @error('origin')
                <p class="error">{{ $message }}</p>
            @enderror

            <div class="attribute-total">
                <h3>Nível de Exposição Paranormal (NEX)</h3>

                <p>
                    <strong>5%</strong> — NEX inicial da criação padrão.
                </p>

                <p>
                    O NEX será configurado automaticamente pelo sistema.
                </p>
            </div>
        </section>

        {{-- ETAPA 4: ATRIBUTOS --}}
        <section class="card creation-panel" id="step-4" hidden>
            <span class="eyebrow">Etapa 04 de 04</span>
            <h2>Atributos do personagem</h2>

            <p>
                Distribua os pontos entre os cinco atributos.
                Nesta versão, o total permitido é 9, com valores de 0 a 3
                por atributo. Essa regra provisória será conferida com o
                livro antes de finalizarmos as regras de criação.
            </p>

            @error('attributes')
                <div class="alert">{{ $message }}</div>
            @enderror

            <div class="attributes-grid">
                <div class="attribute-card">
                    <label for="agi">AGI — Agilidade</label>
                    <input
                        type="number"
                        id="agi"
                        name="agi"
                        min="0"
                        max="3"
                        value="{{ old('agi', $character->agi) }}"
                        required
                    >
                    @error('agi')
                        <p class="error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="attribute-card">
                    <label for="forca">FOR — Força</label>
                    <input
                        type="number"
                        id="forca"
                        name="forca"
                        min="0"
                        max="3"
                        value="{{ old('forca', $character->forca) }}"
                        required
                    >
                    @error('forca')
                        <p class="error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="attribute-card">
                    <label for="intelecto">INT — Intelecto</label>
                    <input
                        type="number"
                        id="intelecto"
                        name="intelecto"
                        min="0"
                        max="3"
                        value="{{ old('intelecto', $character->intelecto) }}"
                        required
                    >
                    @error('intelecto')
                        <p class="error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="attribute-card">
                    <label for="presenca">PRE — Presença</label>
                    <input
                        type="number"
                        id="presenca"
                        name="presenca"
                        min="0"
                        max="3"
                        value="{{ old('presenca', $character->presenca) }}"
                        required
                    >
                    @error('presenca')
                        <p class="error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="attribute-card">
                    <label for="vigor">VIG — Vigor</label>
                    <input
                        type="number"
                        id="vigor"
                        name="vigor"
                        min="0"
                        max="3"
                        value="{{ old('vigor', $character->vigor) }}"
                        required
                    >
                    @error('vigor')
                        <p class="error">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="attribute-total">
                <h3>
                    Total dos atributos:
                    <strong><span id="total-atributos">0</span> / 9</strong>
                </h3>

                <p id="mensagem-atributos">Calculando...</p>
            </div>
        </section>

        {{-- NAVEGAÇÃO ENTRE ETAPAS --}}
        <section class="card">
            <div class="step-navigation">
                <button
                    type="button"
                    id="previous-step"
                    class="btn btn-secondary"
                    hidden
                >
                    ← Etapa anterior
                </button>

                <button
                    type="button"
                    id="next-step"
                >
                    Próxima etapa →
                </button>

                <button
                    type="submit"
                    id="save-character"
                    hidden
                >
                    Salvar personagem
                </button>
            </div>

            <a
                href="{{ route('characters.review', [
                    'campaign' => $campaign->id,
                    'character' => $character->id,
                ]) }}"
                class="btn btn-secondary"
            >
                Visualizar ficha completa
            </a>
        </section>
    </form>

    <p>
        <a href="{{ route('home') }}">← Voltar para o início</a>
    </p>

@endsection

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const panels = [
            document.getElementById('step-1'),
            document.getElementById('step-2'),
            document.getElementById('step-3'),
            document.getElementById('step-4')
        ];

        const indicators = [
            document.getElementById('step-indicator-1'),
            document.getElementById('step-indicator-2'),
            document.getElementById('step-indicator-3'),
            document.getElementById('step-indicator-4')
        ];

        const previousButton = document.getElementById('previous-step');
        const nextButton = document.getElementById('next-step');
        const saveButton = document.getElementById('save-character');
        const currentStepInput = document.getElementById('current_step');

        let currentStep = 0;

        function showStep(step) {
            currentStep = step;
            currentStepInput.value = currentStep + 1;

            panels.forEach(function (panel, index) {
                panel.hidden = index !== currentStep;
            });

            indicators.forEach(function (indicator, index) {
                indicator.classList.toggle('active', index === currentStep);
                indicator.classList.toggle('completed', index < currentStep);
            });

            previousButton.hidden = currentStep === 0;
            nextButton.hidden = currentStep === panels.length - 1;
            saveButton.hidden = currentStep !== panels.length - 1;

            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        }

        nextButton.addEventListener('click', function () {
            const currentPanel = panels[currentStep];
            const requiredFields = currentPanel.querySelectorAll('[required]');

            for (const field of requiredFields) {
                if (!field.checkValidity()) {
                    field.reportValidity();
                    return;
                }
            }

            if (currentStep < panels.length - 1) {
                showStep(currentStep + 1);
            }
        });

        previousButton.addEventListener('click', function () {
            if (currentStep > 0) {
                showStep(currentStep - 1);
            }
        });

        const camposAtributos = [
            'agi',
            'forca',
            'intelecto',
            'presenca',
            'vigor'
        ];

        const totalAtributos = document.getElementById('total-atributos');
        const mensagemAtributos = document.getElementById('mensagem-atributos');

        function atualizarTotalAtributos() {
            let total = 0;
            let valoresValidos = true;

            camposAtributos.forEach(function (atributo) {
                const campo = document.getElementById(atributo);
                const valor = Number(campo.value);

                if (
                    campo.value.trim() === '' ||
                    !Number.isInteger(valor) ||
                    valor < 0 ||
                    valor > 3
                ) {
                    valoresValidos = false;
                } else {
                    total += valor;
                }
            });

            totalAtributos.textContent = total;

            if (!valoresValidos) {
                mensagemAtributos.textContent =
                    'Preencha todos os atributos com valores de 0 a 3.';
                mensagemAtributos.style.color = '#ff747e';
            } else if (total === 9) {
                mensagemAtributos.textContent = 'Distribuição válida!';
                mensagemAtributos.style.color = '#ff646e';
            } else {
                mensagemAtributos.textContent =
                    'A soma deve ser 9. ' +
                    (total < 9
                        ? 'Faltam ' + (9 - total) + ' ponto(s).'
                        : 'Você ultrapassou ' + (total - 9) + ' ponto(s).');

                mensagemAtributos.style.color = '#ff747e';
            }
        }

        camposAtributos.forEach(function (atributo) {
            document.getElementById(atributo).addEventListener(
                'input',
                atualizarTotalAtributos
            );
        });

        const erros = @json($errors->keys());

        let etapaInicial = 0;

        if (
            erros.includes('appearance') ||
            erros.includes('personality') ||
            erros.includes('backstory')
        ) {
            etapaInicial = 1;
        } else if (
            erros.includes('character_class') ||
            erros.includes('origin')
        ) {
            etapaInicial = 2;
        } else if (
            erros.includes('agi') ||
            erros.includes('forca') ||
            erros.includes('intelecto') ||
            erros.includes('presenca') ||
            erros.includes('vigor') ||
            erros.includes('attributes')
        ) {
            etapaInicial = 3;
        } else if (
            erros.includes('name') ||
            erros.includes('age')
        ) {
            etapaInicial = 0;
        } else {
            const etapaAnterior = Number(currentStepInput.value);

            if (etapaAnterior >= 1 && etapaAnterior <= panels.length) {
                etapaInicial = etapaAnterior - 1;
            }
        }

        showStep(etapaInicial);
        atualizarTotalAtributos();
    });
</script>
