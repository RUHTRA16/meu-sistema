@extends('layouts.app')

@section('title', 'Mestre de IA | RPG Paranormal')

@section('content')

<section class="card">
    <span class="eyebrow">Sistema de inteligência artificial</span>

    <h2>Mestre Paranormal</h2>

    <p>
        Consulte informações sobre suas campanhas, personagens
        e recursos. Prepare-se para investigar o desconhecido.
    </p>

    <div
        class="card"
        id="chat-messages"
        aria-live="polite"
        aria-label="Conversa com o Mestre Paranormal"
    >
        <p><strong>Mestre Paranormal</strong></p>
        <p>
            Saudações, agente. Estou pronto para receber sua mensagem.
            Minha conexão com a inteligência artificial real ainda
            será configurada.
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
            placeholder="Ex.: Como funciona a sanidade no RPG?"
            required
        ></textarea>

        <button type="submit" class="btn" id="chat-submit">
            Enviar mensagem
        </button>
    </form>

    <p class="eyebrow" id="chat-status" role="status">
        Interface conectada ao Laravel — IA real ainda não conectada.
    </p>
</section>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('chat-form');
    const input = document.getElementById('chat-message');
    const messages = document.getElementById('chat-messages');
    const status = document.getElementById('chat-status');
    const submit = document.getElementById('chat-submit');

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
        userParagraph.append(userName, document.createTextNode(message));
        messages.appendChild(userParagraph);

        input.value = '';
        input.disabled = true;
        submit.disabled = true;
        status.textContent = 'Enviando mensagem...';

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
                body: JSON.stringify({ message })
            });

            const data = await response.json();

            if (!response.ok) {
                if (response.status === 422) {
                    throw new Error(
                        data.errors?.message?.[0]
                        ?? 'A mensagem não passou pela validação.'
                    );
                }

                throw new Error(
                    data.message ?? 'Não foi possível enviar a mensagem.'
                );
            }

            const replyParagraph = document.createElement('p');
            const replyName = document.createElement('strong');

            replyName.textContent = 'Mestre Paranormal: ';
            replyParagraph.append(
                replyName,
                document.createTextNode(data.reply)
            );

            messages.appendChild(replyParagraph);
            status.textContent = 'Mensagem enviada com sucesso.';
        } catch (error) {
            status.textContent = error.message
                || 'Ocorreu um erro ao enviar a mensagem.';
        } finally {
            input.disabled = false;
            submit.disabled = false;
            input.focus();
            messages.scrollTop = messages.scrollHeight;
        }
    });
});
</script>

@endsection