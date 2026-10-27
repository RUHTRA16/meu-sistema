@extends('layouts.app')

@section('title', 'Mestre de IA | RPG Paranormal')

@section('content')

<style>
    .automation-campaign-selector {
        display: grid;
        gap: 10px;
        margin: 20px 0;
    }

    .automation-campaign-selector select {
        width: 100%;
        padding: 12px;
        border-radius: 8px;
        border: 1px solid var(--border-color, #444);
        background: var(--card-bg, #151515);
        color: inherit;
    }

    .dice-roll-stage {
        margin: 20px 0;
        padding: 20px;
        border: 1px solid rgba(153, 110, 255, 0.35);
        border-radius: 14px;
        background: rgba(110, 70, 180, 0.08);
        text-align: center;
    }

    .dice-roll-title {
        margin: 0 0 18px;
        font-weight: 700;
    }

    .dice-roll-list {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 14px;
        margin: 18px 0;
    }

    .dice-roll-item {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 7px;
        min-width: 64px;
    }

    .dice-cube {
        display: grid;
        place-items: center;
        width: 58px;
        height: 58px;
        border: 1px solid #a78bfa;
        border-radius: 13px;
        background: linear-gradient(145deg, #6d28d9, #312e81);
        color: #fff;
        font-size: 23px;
        font-weight: 800;
        box-shadow: 0 5px 18px rgba(109, 40, 217, 0.3);
        transform-style: preserve-3d;
    }

    .dice-cube.rolling {
        animation: dice-roll-animation 0.35s linear infinite;
    }

    .dice-cube.revealed {
        animation: dice-reveal-animation 0.45s ease-out both;
    }

    .dice-label {
        font-size: 12px;
        opacity: 0.8;
    }

    .dice-roll-total {
        margin-top: 18px;
        font-size: 19px;
        font-weight: 800;
    }

    .dice-roll-breakdown {
        margin-top: 8px;
        font-size: 14px;
        opacity: 0.85;
    }

    @keyframes dice-roll-animation {
        0% {
            transform: rotateX(0deg) rotateY(0deg) scale(0.9);
        }

        50% {
            transform: rotateX(180deg) rotateY(180deg) scale(1.08);
        }

        100% {
            transform: rotateX(360deg) rotateY(360deg) scale(0.9);
        }
    }

    @keyframes dice-reveal-animation {
        0% {
            transform: scale(0.65) rotate(-15deg);
            opacity: 0.3;
        }

        70% {
            transform: scale(1.12) rotate(5deg);
            opacity: 1;
        }

        100% {
            transform: scale(1) rotate(0);
            opacity: 1;
        }
    }

    @media (prefers-reduced-motion: reduce) {
        .dice-cube.rolling,
        .dice-cube.revealed {
            animation: none;
        }
    }
</style>

<section class="card">
    <span class="eyebrow">Sistema de inteligência artificial</span>

    <h2>Mestre Paranormal</h2>

    <p>
        Consulte informações sobre suas campanhas, personagens
        e recursos. Prepare-se para investigar o desconhecido.
    </p>

    <div class="automation-campaign-selector">
        <label for="chat-campaign">
            <strong>Campanha em uso</strong>
        </label>

        <select id="chat-campaign" name="campaign_id">
            <option value="">Nenhuma campanha — perguntas gerais</option>

            @foreach ($campaigns as $campaign)
                <option value="{{ $campaign->id }}">
                    {{ $campaign->title }}
                </option>
            @endforeach
        </select>

        <p class="eyebrow" id="campaign-description">
            Selecione uma campanha para consultar suas configurações
            de automação.
        </p>
    </div>

    <div
        class="card"
        id="chat-messages"
        aria-live="polite"
        aria-label="Conversa com o Mestre Paranormal"
    >
        <p><strong>Mestre Paranormal</strong></p>

        <p>
            Saudações, agente. Estou pronto para ajudar em suas
            investigações. Faça uma pergunta sobre Ordem Paranormal RPG
            e consultarei o material de regras disponível.
        </p>
    </div>

    <form id="chat-form">
        @csrf

        <label for="chat-message">
            <strong>Sua mensagem</strong>
        </label>

        <textarea
            id="chat-message"
            name="message"
            rows="3"
            maxlength="2000"
            placeholder="Ex.: Role 3d20 para testar minha perícia."
            required
        ></textarea>

        <button type="submit" class="btn" id="chat-submit">
            Enviar mensagem
        </button>
    </form>

    <p class="eyebrow" id="chat-status" role="status">
        Pronto para receber sua mensagem.
    </p>
</section>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('chat-form');
    const input = document.getElementById('chat-message');
    const messages = document.getElementById('chat-messages');
    const status = document.getElementById('chat-status');
    const submit = document.getElementById('chat-submit');

    const campaignSelect = document.getElementById('chat-campaign');
    const campaignDescription = document.getElementById(
        'campaign-description'
    );

    campaignSelect.addEventListener('change', () => {
        if (campaignSelect.value) {
            const campaignName = campaignSelect.options[
                campaignSelect.selectedIndex
            ].text;

            campaignDescription.textContent =
                `Campanha selecionada: ${campaignName}. `
                + 'As preferências dessa campanha serão consideradas.';
        } else {
            campaignDescription.textContent =
                'Nenhuma campanha selecionada. '
                + 'O chat responderá perguntas gerais.';
        }
    });

    /**
     * Anima os dados e revela os valores enviados pelo servidor.
     * Nenhum resultado é gerado aleatoriamente no navegador.
     */
    async function animateDiceRoll(roll) {
        if (
            !roll
            || !Array.isArray(roll.dice)
            || roll.dice.length === 0
        ) {
            throw new Error('Os dados retornados pelo servidor são inválidos.');
        }

        const stage = document.createElement('section');
        stage.className = 'dice-roll-stage';
        stage.setAttribute('aria-label', 'Resultado da rolagem de dados');

        const title = document.createElement('p');
        title.className = 'dice-roll-title';
        title.textContent = `Rolando ${roll.notation}...`;

        const diceList = document.createElement('div');
        diceList.className = 'dice-roll-list';

        const diceElements = [];

        roll.dice.forEach((die, index) => {
            const item = document.createElement('div');
            item.className = 'dice-roll-item';

            const cube = document.createElement('div');
            cube.className = 'dice-cube rolling';
            cube.textContent = '?';

            const label = document.createElement('span');
            label.className = 'dice-label';
            label.textContent = `Dado ${index + 1} · d${die.sides}`;

            item.append(cube, label);
            diceList.appendChild(item);

            diceElements.push(cube);
        });

        const total = document.createElement('div');
        total.className = 'dice-roll-total';
        total.textContent = 'Os dados estão rolando...';

        stage.append(title, diceList, total);
        messages.appendChild(stage);

        messages.scrollTop = messages.scrollHeight;

        await new Promise(resolve => setTimeout(resolve, 1500));

        diceElements.forEach((cube, index) => {
            cube.classList.remove('rolling');
            cube.classList.add('revealed');
            cube.textContent = String(roll.dice[index].result);
        });

        title.textContent = `Resultado de ${roll.notation}`;

        const modifier = Number(roll.modifier);
        const subtotal = Number(roll.subtotal);
        const finalTotal = Number(roll.total);

        if (
            !Number.isFinite(modifier)
            || !Number.isFinite(subtotal)
            || !Number.isFinite(finalTotal)
        ) {
            throw new Error('O servidor retornou valores inválidos.');
        }

        total.textContent = `Total: ${finalTotal}`;

        const breakdown = document.createElement('p');
        breakdown.className = 'dice-roll-breakdown';

        const individualResults = roll.dice
            .map(die => die.result)
            .join(' + ');

        breakdown.textContent = `Dados: ${individualResults}`;

        if (modifier !== 0) {
            breakdown.textContent +=
                ` | Soma: ${subtotal} | Modificador: `
                + `${modifier > 0 ? '+' : ''}${modifier}`;
        } else {
            breakdown.textContent += ` | Soma: ${subtotal}`;
        }

        stage.appendChild(breakdown);

        status.textContent = 'Rolagem concluída.';
        messages.scrollTop = messages.scrollHeight;
    }

    form.addEventListener('submit', async (event) => {
        event.preventDefault();

        const message = input.value.trim();

        if (!message) {
            status.textContent = 'Digite uma mensagem antes de enviar.';
            return;
        }

        const userParagraph = document.createElement('p');
        const userName = document.createElement('strong');

        userName.textContent = 'Você: ';

        userParagraph.append(
            userName,
            document.createTextNode(message)
        );

        messages.appendChild(userParagraph);

        const campaignId = campaignSelect.value;

        input.value = '';
        input.disabled = true;
        submit.disabled = true;
        campaignSelect.disabled = true;

        status.textContent = 'Consultando o Mestre Paranormal...';

        try {
            const response = await fetch(@json(route('chat.send')), {
                method: 'POST',

                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': form.querySelector(
                        'input[name="_token"]'
                    ).value
                },

                body: JSON.stringify({
                    message: message,
                    campaign_id: campaignId || null
                })
            });

            const data = await response.json();

            if (!response.ok) {
                if (response.status === 422) {
                    throw new Error(
                        data.errors?.message?.[0]
                        ?? data.errors?.campaign_id?.[0]
                        ?? data.message
                        ?? 'Os dados enviados não passaram pela validação.'
                    );
                }

                throw new Error(
                    data.message
                    ?? 'Não foi possível obter uma resposta da IA.'
                );
            }

            if (
                data.success !== true
                || typeof data.reply !== 'string'
                || data.reply.trim() === ''
            ) {
                throw new Error(
                    'O servidor não retornou uma resposta válida.'
                );
            }

            if (data.dice_roll) {
                await animateDiceRoll(data.dice_roll);
            }

            if (typeof window.formatarRespostaMestre !== 'function') {
                throw new Error(
                    'O formatador não foi carregado. '
                    + 'Verifique a compilação do JavaScript.'
                );
            }

            const replyLabel = document.createElement('p');
            const replyName = document.createElement('strong');

            replyName.textContent = 'Mestre Paranormal: ';
            replyLabel.appendChild(replyName);

            const replyContent = document.createElement('div');
            replyContent.className = 'chat-reply-content';

            replyContent.innerHTML = window.formatarRespostaMestre(
                data.reply
            );

            messages.append(replyLabel, replyContent);

            if (!data.dice_roll) {
                status.textContent = 'Resposta recebida com sucesso.';
            }
        } catch (error) {
            status.textContent = error.message
                || 'Ocorreu um erro ao consultar o Mestre Paranormal.';
        } finally {
            input.disabled = false;
            submit.disabled = false;
            campaignSelect.disabled = false;
            input.focus();

            messages.scrollTop = messages.scrollHeight;
        }
    });
});
</script>

@endsection