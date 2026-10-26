<?php

namespace App\Http\Controllers;

use App\Services\BookSearchService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class ChatController extends Controller
{
    public function send(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'message' => ['required', 'string', 'min:1', 'max:2000'],
        ]);

        $apiKey = config('services.groq.api_key');

        if (! filled($apiKey)) {
            return response()->json([
                'message' => 'A chave da API do Groq não está configurada.',
            ], 500);
        }

        try {
            $bookSearch = app(BookSearchService::class);

            $bookContext = $bookSearch->search(
                $validated['message'],
                5
            );

            $systemPrompt = <<<'PROMPT'
Você é o Mestre Paranormal, um assistente especializado
em Ordem Paranormal RPG.

Responda sempre em português brasileiro, com clareza
e organização.

REGRAS OBRIGATÓRIAS:

1. Leia atentamente a pergunta e identifique todos os
   pedidos feitos pelo usuário.

2. Responda a cada parte da pergunta. Se forem solicitadas
   definições, exemplos, valores numéricos ou procedimentos,
   não deixe nenhum desses pontos sem resposta.

3. Use os trechos do livro fornecidos no contexto como
   referência prioritária para regras oficiais.

4. Não invente regras, números, páginas ou citações.
   Diferencie claramente regras oficiais de sugestões.

5. Quando a informação solicitada não estiver nos trechos
   encontrados, informe essa limitação. Não apresente
   suposições como fatos confirmados pelo livro.

6. Quando houver informações numéricas, explique o que
   significam e em qual situação se aplicam.
   Diferencie os valores permitidos na criação de personagem
   dos valores possíveis durante a progressão, quando isso
   for relevante e estiver sustentado pelo material.

7. Organize respostas extensas com títulos e listas.
   Evite repetir a mesma informação desnecessariamente.

8. Nunca afirme que consultou uma página específica se
   essa informação não estiver disponível no contexto.

9. Não afirme que alterou fichas, atributos, inventários
   ou recursos. Alterações de dados devem ser realizadas
   e validadas pelo sistema Laravel.

10. Se os trechos não forem suficientes para responder
    completamente, responda apenas o que estiver sustentado
    pelo material e indique o que não foi possível confirmar.
PROMPT;

            if ($bookContext !== '') {
                $systemPrompt .= "\n\n"
                    . "TRECHOS RECUPERADOS DO LIVRO DE REGRAS:\n"
                    . $bookContext;
            } else {
                $systemPrompt .= "\n\n"
                    . "Nenhum trecho relevante foi encontrado "
                    . "no livro de regras. Não invente informações "
                    . "nem afirme que consultou regras específicas.";
            }

            $response = Http::withToken($apiKey)
                ->acceptJson()
                ->timeout(45)
                ->post(
                    rtrim(config('services.groq.base_url'), '/')
                        . '/chat/completions',
                    [
                        'model' => 'openai/gpt-oss-20b',
                        'messages' => [
                            [
                                'role' => 'system',
                                'content' => $systemPrompt,
                            ],
                            [
                                'role' => 'user',
                                'content' => $validated['message'],
                            ],
                        ],
                        'temperature' => 0.3,
                        'max_tokens' => 1200,
                    ]
                );

            if ($response->failed()) {
                Log::error('Erro na API do Groq.', [
                    'status' => $response->status(),
                ]);

                return response()->json([
                    'message' => 'O Groq não conseguiu responder. '
                        . 'Verifique o modelo e os limites da API.',
                ], 502);
            }

            $reply = $response->json(
                'choices.0.message.content'
            );

            if (! is_string($reply) || trim($reply) === '') {
                return response()->json([
                    'message' => 'A IA retornou uma resposta vazia.',
                ], 502);
            }

            return response()->json([
                'success' => true,
                'reply' => trim($reply),
            ]);
        } catch (Throwable $exception) {
            Log::error('Erro ao consultar o Mestre Paranormal.', [
                'exception' => get_class($exception),
            ]);

            return response()->json([
                'message' => 'Não foi possível obter uma resposta. '
                    . 'Tente novamente.',
            ], 502);
        }
    }
}