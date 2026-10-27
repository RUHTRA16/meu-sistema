
<style>
    .campaign-ai {
        display: grid;
        gap: 18px;
    }

    .campaign-ai-header {
        display: flex;
        align-items: center;
        gap: 14px;
    }

    .campaign-ai-icon {
        display: grid;
        place-items: center;
        width: 48px;
        height: 48px;
        flex-shrink: 0;
        border: 1px solid rgba(167, 139, 250, .4);
        border-radius: 14px;
        background: rgba(109, 40, 217, .15);
        font-size: 23px;
    }

    .campaign-ai-header h2 {
        margin: 0 0 5px;
    }

    .campaign-ai-header p {
        margin: 0;
        opacity: .75;
    }

    .campaign-ai-context {
        padding: 12px 14px;
        border: 1px solid var(--border-color, #343434);
        border-radius: 10px;
        background: rgba(120, 80, 180, .08);
        font-size: 13px;
    }

    .campaign-ai-messages {
        display: flex;
        flex-direction: column;
        gap: 12px;
        min-height: 280px;
        max-height: 520px;
        overflow-y: auto;
        padding: 18px;
        border: 1px solid var(--border-color, #343434);
        border-radius: 12px;
        background: rgba(0, 0, 0, .12);
        overflow-wrap: anywhere;
    }

    .campaign-ai-message {
        padding: 12px 14px;
        border-radius: 10px;
        line-height: 1.6;
    }

    .campaign-ai-message-user {
        align-self: flex-end;
        max-width: 90%;
        background: rgba(109, 40, 217, .18);
        border: 1px solid rgba(167, 139, 250, .2);
    }

    .campaign-ai-message-assistant {
        align-self: flex-start;
        width: 100%;
        box-sizing: border-box;
        background: var(--card-bg, rgba(255, 255, 255, .025));
        border: 1px solid var(--border-color, #343434);
    }

    .campaign-ai-message-label {
        display: block;
        margin-bottom: 7px;
        font-size: 12px;
        font-weight: 800;
        letter-spacing: .05em;
        text-transform: uppercase;
        opacity: .8;
    }

    .campaign-ai-form {
        display: grid;
        gap: 12px;
    }

    .campaign-ai-form textarea {
        width: 100%;
        box-sizing: border-box;
        resize: vertical;
    }

    .campaign-ai-form button:disabled {
        cursor: wait;
        opacity: .6;
    }

    .campaign-ai-status {
        min-height: 18px;
        margin: 0;
        font-size: 12px;
        opacity: .75;
    }

    .dice-roll-stage {
        margin-top: 10px;
        padding: 20px;
        border: 1px solid rgba(153, 110, 255, .35);
        border-radius: 14px;
        background: rgba(110, 70, 180, .08);
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
        box-shadow: 0 5px 18px rgba(109, 40, 217, .3);
    }

    .dice-cube.rolling {
        animation: campaign-dice-roll .35s linear infinite;
    }

    .dice-cube.revealed {
        animation: campaign-dice-reveal .45s ease-out both;
    }

    .dice-label {
        font-size: 12px;
        opacity: .8;
    }

    .dice-roll-total {
        margin-top: 18px;
        font-size: 19px;
        font-weight: 800;
    }

    .dice-roll-breakdown {
        margin-top: 8px;
        font-size: 14px;
        opacity: .85;
    }

    @keyframes campaign-dice-roll {
        0% {
            transform: rotateX(0deg) rotateY(0deg) scale(.9);
        }

        50% {
            transform: rotateX(180deg) rotateY(180deg) scale(1.08);
        }

        100% {
            transform: rotateX(360deg) rotateY(360deg) scale(.9);
        }
    }

    @keyframes campaign-dice-reveal {
        0% {
            transform: scale(.65) rotate(-15deg);
            opacity: .3;
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

<section class="card campaign-ai" id="campaign-ai">
    <header class="campaign-ai-header">
        <div class="campaign-ai-icon" aria-hidden="true">✧</div>

        <div>
            <span class="eyebrow">INTELIGÊNCIA ARTIFICIAL</span>
            <h2>Mestre Paranormal</h2>
            <p>Seu assistente durante esta investigação.</p>
        </div>
    </header>

    <div class="campaign-ai-context">
        <strong>Campanha ativa:</strong>
        {{ $campaign->title }}

        <p>
            O chat enviará automaticamente o identificador desta campanha.
            Não é necessário selecionar a campanha novamente.
        </p>
    </div>

    <div
        class="campaign-ai-messages"
        id="campaign-chat-messages"
        aria-live="polite"
        aria-label="Conversa com o Mestre Paranormal"
    >
        <div class="campaign-ai-message campaign-ai-message-assistant">
            <span class="campaign-ai-message-label">Mestre Paranormal</span>

            Saudações, agente. Estou pronto para ajudar nesta investigação.
            Faça perguntas sobre as regras, consulte o livro ou solicite
            uma rolagem de dados conforme as configurações da campanha.
        </div>
    </div>

    <form class="campaign-ai-form" id="campaign-chat-form">
        @csrf

        <label for="campaign-chat-message">
            <strong>Sua mensagem</strong>
        </label>

        <textarea
            id="campaign-chat-message"
            name="message"
            rows="3"
            maxlength="2000"
            placeholder="Ex.: Consulte as regras de um ritual ou role 1d20+5."
            required
        ></textarea>

        <button type="submit" class="btn" id="campaign-chat-submit">
            Enviar mensagem
        </button>

        <p
            class="campaign-ai-status"
            id="campaign-chat-status"
            role="status"
        >
            Pronto para receber sua mensagem.
        </p>
    </form>
</section>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('campaign-chat-form');

    if (!form || form.dataset.initialized === 'true') {
        return;
    }

    form.dataset.initialized = 'true';

    const input = document.getElementById('campaign-chat-message');
    const messages = document.getElementById('campaign-chat-messages');
    const status = document.getElementById('campaign-chat-status');
    const submit = document.getElementById('campaign-chat-submit');

    const campaignId = @json($campaign->id);
    const csrfToken = form.querySelector('input[name="_token"]').value;

    function scrollToBottom() {
        messages.scrollTop = messages.scrollHeight;
    }

    function appendMessage(type, label, content) {
        const message = document.createElement('div');

        message.className = type === 'user'
            ? 'campaign-ai-message campaign-ai-message-user'
            : 'campaign-ai-message campaign-ai-message-assistant';

        const heading = document.createElement('span');
        heading.className = 'campaign-ai-message-label';
        heading.textContent = label;

        const body = document.createElement('div');

        if (type === 'user') {
            body.textContent = content;
        } else if (typeof window.formatarRespostaMestre === 'function') {
            body.innerHTML = window.formatarRespostaMestre(content);
        } else {
            body.textContent = content;
        }

        message.append(heading, body);
        messages.appendChild(message);

        scrollToBottom();

        return message;
    }

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

        scrollToBottom();

        await new Promise(resolve => setTimeout(resolve, 1200));

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

        breakdown.textContent =
            'Dados: ' + roll.dice.map(die => die.result).join(' + ');

        if (modifier !== 0) {
            breakdown.textContent +=
                ` | Soma: ${subtotal} | Modificador: `
                + `${modifier > 0 ? '+' : ''}${modifier}`;
        } else {
            breakdown.textContent += ` | Soma: ${subtotal}`;
        }

        stage.appendChild(breakdown);

        scrollToBottom();
    }

    form.addEventListener('submit', async event => {
        event.preventDefault();

        const message = input.value.trim();

        if (!message) {
            status.textContent = 'Digite uma mensagem antes de enviar.';
            return;
        }

        appendMessage('user', 'Você', message);

        input.value = '';
        input.disabled = true;
        submit.disabled = true;

        status.textContent = 'Consultando o Mestre Paranormal...';

        try {
            const response = await fetch(@json(route('chat.send')), {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({
                    message: message,
                    campaign_id: campaignId
                })
            });

            const data = await response.json();

            if (!response.ok) {
                throw new Error(
                    data.errors?.message?.[0]
                    ?? data.errors?.campaign_id?.[0]
                    ?? data.message
                    ?? 'Não foi possível consultar o Mestre Paranormal.'
                );
            }

            if (
                data.success !== true
                || typeof data.reply !== 'string'
                || data.reply.trim() === ''
            ) {
                throw new Error('O servidor não retornou uma resposta válida.');
            }

            if (data.dice_roll) {
                await animateDiceRoll(data.dice_roll);
            }

            const assistantMessage = appendMessage(
                'assistant',
                'Mestre Paranormal',
                data.reply
            );

        if (data.pending_action) {
            const action = data.pending_action;

            const panel = document.createElement('div');
            panel.style.marginTop = '14px';
            panel.style.padding = '14px';
            panel.style.border = '1px solid rgba(181, 31, 43, .55)';
            panel.style.borderRadius = '10px';
            panel.style.background = 'rgba(181, 31, 43, .08)';

            const title = document.createElement('strong');
            title.textContent = 'Confirmar alteração de PV';

            const details = document.createElement('p');
            details.style.whiteSpace = 'pre-line';

            const operation = action.action === 'dano' ? 'Dano' : 'Cura';

            details.textContent =
                'Personagem: ' + action.character_name + '\n' +
                operation + ': ' + action.quantity + ' PV\n' +
                'PV atuais: ' + action.pv_before + '/' + action.pv_max + '\n' +
                'PV após a alteração: ' + action.pv_after + '/' + action.pv_max;

            const confirmButton = document.createElement('button');
            confirmButton.type = 'button';
            confirmButton.className = 'btn';
            confirmButton.textContent = 'Confirmar alteração';

            const cancelButton = document.createElement('button');
            cancelButton.type = 'button';
            cancelButton.textContent = 'Cancelar';
            cancelButton.style.marginLeft = '8px';

            const resultText = document.createElement('p');
            resultText.setAttribute('role', 'status');

            confirmButton.addEventListener('click', async () => {
                confirmButton.disabled = true;
                cancelButton.disabled = true;
                resultText.textContent = 'Aplicando alteração...';

                try {
                    const response = await fetch(action.confirm_url, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: JSON.stringify({
                            token: action.token
                        })
                    });

                    const result = await response.json();

                    if (!response.ok || result.success !== true) {
                        throw new Error(
                            result.message || 'Não foi possível confirmar a alteração.'
                        );
                    }

                    resultText.textContent = result.reply;
                    confirmButton.remove();
                    cancelButton.remove();

                    status.textContent = 'Alteração de PV confirmada.';
                } catch (error) {
                    resultText.textContent = error.message
                        || 'Erro ao confirmar a alteração.';

                    confirmButton.disabled = false;
                    cancelButton.disabled = false;
                }

                scrollToBottom();
            });

            cancelButton.addEventListener('click', () => {
                panel.textContent = 'Proposta cancelada. Nenhum PV foi alterado.';
            });

            panel.append(title, details, confirmButton, cancelButton, resultText);
            assistantMessage.appendChild(panel);

            status.textContent = 'Aguardando sua confirmação para alterar os PV.';
        } else {
            status.textContent = data.dice_roll
                ? 'Resposta recebida. Rolagem concluída.'
                : 'Resposta recebida com sucesso.';
        }

        } catch (error) {
            status.textContent = error.message
                || 'Ocorreu um erro ao consultar o Mestre Paranormal.';
        } finally {
            input.disabled = false;
            submit.disabled = false;
            input.focus();
        }
    });
});
</script>