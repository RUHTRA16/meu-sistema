<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\Character;
use App\Services\BookSearchService;
use App\Services\DiceRollService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Throwable;

class ChatController extends Controller
{
    public function send(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'message' => ['required', 'string', 'min:1', 'max:2000'],
            'campaign_id' => [
                'nullable',
                'integer',
                'exists:campaigns,id',
            ],
        ]);

        try {
            $campaign = null;
            $automation = null;

            if (!empty($validated['campaign_id'])) {
                $campaign = Campaign::with('automationSetting')
                    ->findOrFail($validated['campaign_id']);

                $automation = $campaign->automationSetting;
            }

            $settings = [
                'dice_rolling' => $automation?->dice_rolling ?? 'manual',
                'initiative' => $automation?->initiative ?? 'manual',
                'npc_control' => $automation?->npc_control ?? 'manual',
                'ai_actions' => $automation?->ai_actions ?? 'confirm',
            ];

            $apiKey = config('services.groq.api_key');
            $baseUrl = rtrim(
                (string) config('services.groq.base_url'),
                '/'
            );

            /*
             * AÇÕES REAIS DE PV
             *
             * Só consulta o classificador quando a mensagem contém
             * termos que podem indicar uma alteração de vida.
             */
            if (
                $campaign !== null
                && $this->mayBePvAction($validated['message'])
            ) {
                if (!filled($apiKey) || $baseUrl === '') {
                    return response()->json([
                        'success' => false,
                        'message' => 'A API do Groq não está configurada.',
                    ], 500);
                }

                $intent = $this->classifyPvAction(
                    $validated['message'],
                    $apiKey,
                    $baseUrl
                );

                if ($intent['intent'] === 'modify_pv') {
                    return $this->handlePvAction(
                        $campaign,
                        $settings['ai_actions'],
                        $intent
                    );
                }

                if ($intent['intent'] === 'clarify') {
                    return response()->json([
                        'success' => true,
                        'reply' => $intent['question']
                            ?: 'Preciso de mais informações para alterar os PV '
                                . 'com segurança. Informe o personagem, '
                                . 'a quantidade e se é dano ou cura.',
                    ]);
                }
            }

            /*
             * ROLAGEM DE DADOS
             */
            $diceService = app(DiceRollService::class);

            try {
                $diceRequest = $diceService->parseRequest(
                    $validated['message']
                );
            } catch (InvalidArgumentException $exception) {
                return response()->json([
                    'message' => $exception->getMessage(),
                ], 422);
            }

            if ($diceRequest !== null) {
                if ($campaign === null) {
                    return response()->json([
                        'success' => true,
                        'reply' => 'Para realizar uma rolagem de dados, '
                            . 'selecione primeiro uma campanha no chat.',
                    ]);
                }

                if ($settings['dice_rolling'] !== 'automatic') {
                    return response()->json([
                        'success' => true,
                        'reply' => 'A rolagem automática está desativada '
                            . 'nesta campanha. Role '
                            . $diceRequest['notation']
                            . ' manualmente e informe o resultado.',
                    ]);
                }

                $diceResult = $diceService->roll($diceRequest);

                Log::info('Rolagem de dados realizada.', [
                    'campaign_id' => $campaign->id,
                    'notation' => $diceResult['notation'],
                    'total' => $diceResult['total'],
                ]);

                return response()->json([
                    'success' => true,
                    'reply' => 'Rolagem concluída para '
                        . $diceResult['notation']
                        . '. Confira os resultados abaixo.',
                    'dice_roll' => $diceResult,
                ]);
            }

            /*
             * CONSULTA NORMAL À IA
             */
            if (!filled($apiKey)) {
                return response()->json([
                    'message' => 'A chave da API do Groq não está configurada.',
                ], 500);
            }

            $bookContext = app(BookSearchService::class)->search(
                $validated['message'],
                5
            );

            preg_match_all(
                '/^PÁGINA\s+(\d+)\s*$/mi',
                $bookContext,
                $matches
            );

            $availablePages = array_values(array_unique(
                array_map('intval', $matches[1] ?? [])
            ));

            sort($availablePages);

            $systemPrompt = <<<'PROMPT'
Você é o Mestre Paranormal, assistente de Ordem Paranormal RPG.

Responda sempre em português brasileiro, com clareza e organização.

REGRAS DO LIVRO:
1. Use os trechos recuperados como referência prioritária.
2. Não invente regras, valores, requisitos ou páginas.
3. Só atribua informações ao livro quando sustentadas pelos trechos.
4. Cite páginas apenas quando estiverem na lista permitida.
5. Se os trechos forem insuficientes, explique a limitação.
6. Organize respostas extensas com títulos e listas.

REGRAS DA CAMPANHA:
7. Respeite as configurações fornecidas.
8. Não invente resultados de dados, recursos ou atributos.
9. Nunca afirme que modificou uma ficha sem operação confirmada pelo servidor.
10. Não presuma uma campanha quando nenhuma foi selecionada.
11. Somente o servidor Laravel pode validar e executar alterações reais.
12. Se uma operação não estiver implementada, explique essa limitação.
PROMPT;

            if ($campaign !== null) {
                $systemPrompt .= "\n\nCAMPANHA SELECIONADA:\n"
                    . "Nome: {$campaign->title}\n"
                    . "Status: {$campaign->status}\n"
                    . "Rolagem de dados: {$settings['dice_rolling']}\n"
                    . "Iniciativa: {$settings['initiative']}\n"
                    . "Controle de NPCs: {$settings['npc_control']}\n"
                    . "Ações da IA: {$settings['ai_actions']}\n"
                    . "Não afirme ter executado uma ação que o servidor não executou.";
            } else {
                $systemPrompt .= "\n\nNenhuma campanha foi selecionada. "
                    . "Responda perguntas gerais sem presumir estado de campanha.";
            }

            if (trim($bookContext) !== '') {
                $pagesText = $availablePages !== []
                    ? implode(', ', $availablePages)
                    : 'Nenhuma página identificada';

                $systemPrompt .= "\n\nPÁGINAS PERMITIDAS: {$pagesText}\n\n"
                    . "TRECHOS RECUPERADOS DO LIVRO:\n"
                    . $bookContext;
            } else {
                $systemPrompt .= "\n\nNenhum trecho relevante foi encontrado. "
                    . "Não invente informações nem cite páginas.";
            }

            if ($baseUrl === '') {
                return response()->json([
                    'message' => 'A URL da API do Groq não está configurada.',
                ], 500);
            }

            $response = Http::withToken($apiKey)
                ->acceptJson()
                ->timeout(45)
                ->post($baseUrl . '/chat/completions', [
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
                    'temperature' => 0.2,
                    'max_tokens' => 1200,
                ]);

            if ($response->failed()) {
                Log::error('Erro na API do Groq.', [
                    'status' => $response->status(),
                    'response' => mb_substr($response->body(), 0, 2000),
                ]);

                return response()->json([
                    'message' => 'O Groq não conseguiu responder. '
                        . 'Verifique o modelo, os limites e a configuração da API.',
                ], 502);
            }

            $reply = $response->json('choices.0.message.content');

            if (!is_string($reply) || trim($reply) === '') {
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
                'message' => $exception->getMessage(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
            ]);

            return response()->json([
                'message' => 'Não foi possível obter uma resposta. '
                    . 'Consulte o log do Laravel para identificar a causa.',
            ], 502);
        }
    }

    /**
     * Confirma uma alteração de PV previamente proposta pela IA.
     */
    public function confirmPvAction(
        Request $request,
        Campaign $campaign
    ): JsonResponse {
        $validated = $request->validate([
            'token' => ['required', 'string', 'min:32', 'max:100'],
        ]);

        $pendingActions = session('pending_ai_pv_actions', []);
        $pending = $pendingActions[$validated['token']] ?? null;

        if (
            !is_array($pending)
            || (int) ($pending['campaign_id'] ?? 0) !== (int) $campaign->id
            || (int) ($pending['expires_at'] ?? 0) < time()
        ) {
            unset($pendingActions[$validated['token']]);
            session(['pending_ai_pv_actions' => $pendingActions]);

            return response()->json([
                'success' => false,
                'message' => 'Esta proposta expirou ou não é válida. '
                    . 'Envie o pedido novamente ao Mestre Paranormal.',
            ], 422);
        }

        try {
            $result = $this->applyPvChange(
                $campaign,
                (int) $pending['character_id'],
                $pending['action'],
                (int) $pending['quantity']
            );

            unset($pendingActions[$validated['token']]);
            session(['pending_ai_pv_actions' => $pendingActions]);

            return response()->json([
                'success' => true,
                'reply' => $this->formatPvResult($result, 'Alteração confirmada'),
                'pv_result' => $result,
            ]);
        } catch (InvalidArgumentException $exception) {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage(),
            ], 422);
        }
    }

    private function mayBePvAction(string $message): bool
    {
        return (bool) preg_match(
            '/\b(pv|pontos?\s+de\s+vida|vida|dano|danos|ferid[oa]s?|'
            . 'machucad[oa]s?|curar|cura|recuperar|recupere|'
            . 'restaurar|restaure|sofreu|sofrer|perdeu|perder)\b/iu',
            $message
        );
    }

    /**
     * Usa o modelo para interpretar a intenção; não altera o banco.
     */
    private function classifyPvAction(
        string $message,
        string $apiKey,
        string $baseUrl
    ): array {
        $response = Http::withToken($apiKey)
            ->acceptJson()
            ->timeout(20)
            ->post($baseUrl . '/chat/completions', [
                'model' => 'openai/gpt-oss-20b',
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => <<<'PROMPT'
Você identifica pedidos explícitos de alteração dos Pontos de Vida (PV)
em um RPG. Não executa ações; apenas classifica a mensagem.

Responda SOMENTE com JSON válido neste formato:
{
  "intent": "modify_pv" | "clarify" | "none",
  "character_name": "nome ou null",
  "action": "dano" | "cura" | null,
  "quantity": número inteiro ou null,
  "question": "pergunta curta ou null
}

Regras:
- "modify_pv" somente quando a mensagem pedir claramente uma alteração de PV.
- Dano, perder vida ou sofrer dano usam action "dano".
- Curar, recuperar ou restaurar PV usam action "cura".
- A quantidade deve ser explícita na mensagem. Nunca a invente.
- O personagem deve ser identificado pelo nome citado na mensagem.
- Se houver intenção de alterar PV, mas faltar personagem, quantidade ou ação,
  use "clarify" e formule uma pergunta.
- Se a mensagem apenas discutir regras ou mencionar PV sem pedir alteração,
  use "none".
- Não trate uma pergunta hipotética como uma alteração real.
- Não interprete pedidos para alterar SAN ou PE como alteração de PV.
PROMPT,
                    ],
                    [
                        'role' => 'user',
                        'content' => $message,
                    ],
                ],
                'temperature' => 0,
                'max_tokens' => 250,
                'response_format' => ['type' => 'json_object'],
            ]);

        if ($response->failed()) {
            Log::warning('Falha ao classificar possível ação de PV.', [
                'status' => $response->status(),
            ]);

            throw new \RuntimeException(
                'Não foi possível interpretar com segurança o pedido de PV.'
            );
        }

        $content = $response->json('choices.0.message.content');

        if (!is_string($content)) {
            throw new \RuntimeException(
                'A IA não retornou uma classificação válida para a ação de PV.'
            );
        }

        $data = json_decode($content, true);

        if (!is_array($data)) {
            throw new \RuntimeException(
                'A classificação da ação de PV retornou JSON inválido.'
            );
        }

        $intent = $data['intent'] ?? 'none';

        if (!in_array($intent, ['modify_pv', 'clarify', 'none'], true)) {
            $intent = 'none';
        }

        $action = $data['action'] ?? null;

        if (!in_array($action, ['dano', 'cura'], true)) {
            $action = null;
        }

        $quantity = filter_var(
            $data['quantity'] ?? null,
            FILTER_VALIDATE_INT
        );

        if ($quantity === false || $quantity === null || $quantity < 1 || $quantity > 1000) {
            $quantity = null;
        }

        return [
            'intent' => $intent,
            'character_name' => is_string($data['character_name'] ?? null)
                ? trim($data['character_name'])
                : null,
            'action' => $action,
            'quantity' => $quantity,
            'question' => is_string($data['question'] ?? null)
                ? trim($data['question'])
                : null,
        ];
    }

    private function handlePvAction(
        Campaign $campaign,
        string $automationMode,
        array $intent
    ): JsonResponse {
        if (
            empty($intent['character_name'])
            || empty($intent['action'])
            || empty($intent['quantity'])
        ) {
            return response()->json([
                'success' => true,
                'reply' => 'Para alterar os PV, preciso confirmar o personagem, '
                    . 'a quantidade e se a ação é dano ou cura.',
            ]);
        }

        $characters = $campaign->characters()
            ->where('creation_status', 'confirmed')
            ->get();

        $requestedName = $this->normalizeName($intent['character_name']);

        $matches = $characters->filter(
            fn (Character $character) =>
                $this->normalizeName($character->name) === $requestedName
        )->values();

        if ($matches->count() !== 1) {
            $availableNames = $characters->pluck('name')->implode(', ');

            return response()->json([
                'success' => true,
                'reply' => $matches->isEmpty()
                    ? 'Não encontrei um personagem confirmado com o nome "'
                        . $intent['character_name']
                        . '" nesta campanha.'
                        . ($availableNames !== ''
                            ? ' Personagens confirmados: ' . $availableNames . '.'
                            : ' Ainda não há personagens confirmados nesta campanha.')
                    : 'Encontrei mais de um personagem com esse nome. '
                        . 'Informe o nome completo para evitar alterar a ficha errada.',
            ]);
        }

        /** @var Character $character */
        $character = $matches->first();

        if (
            $character->pv_current === null
            || $character->pv_max === null
        ) {
            return response()->json([
                'success' => true,
                'reply' => 'A ficha de ' . $character->name
                    . ' não possui PV configurados. Verifique se a ficha '
                    . 'foi confirmada corretamente.',
            ]);
        }

        if ($automationMode === 'automatic') {
            try {
                $result = $this->applyPvChange(
                    $campaign,
                    (int) $character->id,
                    $intent['action'],
                    (int) $intent['quantity']
                );

                Log::info('Ação de PV executada automaticamente pela IA.', [
                    'campaign_id' => $campaign->id,
                    'character_id' => $character->id,
                    'action' => $intent['action'],
                    'quantity' => $intent['quantity'],
                ]);

                return response()->json([
                    'success' => true,
                    'reply' => $this->formatPvResult(
                        $result,
                        'Ação automática executada'
                    ),
                    'pv_result' => $result,
                ]);
            } catch (InvalidArgumentException $exception) {
                return response()->json([
                    'success' => false,
                    'message' => $exception->getMessage(),
                ], 422);
            }
        }

        $token = Str::random(48);

        $pendingActions = session('pending_ai_pv_actions', []);
        $pendingActions[$token] = [
            'campaign_id' => $campaign->id,
            'character_id' => $character->id,
            'action' => $intent['action'],
            'quantity' => (int) $intent['quantity'],
            'expires_at' => time() + 900,
        ];

        session(['pending_ai_pv_actions' => $pendingActions]);

        $preview = $this->previewPvChange(
            (int) $character->pv_current,
            (int) $character->pv_max,
            $intent['action'],
            (int) $intent['quantity']
        );

        return response()->json([
            'success' => true,
            'reply' => 'Proposta de alteração de PV para '
                . $character->name . '. Confira os valores e confirme '
                . 'se deseja executar a ação.',
            'pending_action' => [
                'token' => $token,
                'character_name' => $character->name,
                'action' => $intent['action'],
                'quantity' => (int) $intent['quantity'],
                'pv_before' => (int) $character->pv_current,
                'pv_after' => $preview,
                'pv_max' => (int) $character->pv_max,
                'confirm_url' => route('chat.pv.confirm', $campaign),
            ],
        ]);
    }

    private function applyPvChange(
        Campaign $campaign,
        int $characterId,
        string $action,
        int $quantity
    ): array {
        if (!in_array($action, ['dano', 'cura'], true) || $quantity < 1 || $quantity > 1000) {
            throw new InvalidArgumentException('A ação de PV ou a quantidade é inválida.');
        }

        return DB::transaction(function () use (
            $campaign,
            $characterId,
            $action,
            $quantity
        ) {
            $character = Character::query()
                ->where('campaign_id', $campaign->id)
                ->whereKey($characterId)
                ->lockForUpdate()
                ->first();

            if (!$character || $character->creation_status !== 'confirmed') {
                throw new InvalidArgumentException(
                    'O personagem não pertence a esta campanha ou não está confirmado.'
                );
            }

            if ($character->pv_current === null || $character->pv_max === null) {
                throw new InvalidArgumentException(
                    'A ficha não possui PV configurados.'
                );
            }

            $before = (int) $character->pv_current;
            $maximum = (int) $character->pv_max;

            $after = $this->previewPvChange(
                $before,
                $maximum,
                $action,
                $quantity
            );

            $character->pv_current = $after;
            $character->save();

            return [
                'character_id' => $character->id,
                'character_name' => $character->name,
                'action' => $action,
                'quantity' => $quantity,
                'pv_before' => $before,
                'pv_after' => $after,
                'pv_max' => $maximum,
            ];
        });
    }

    private function previewPvChange(
        int $current,
        int $maximum,
        string $action,
        int $quantity
    ): int {
        if ($action === 'dano') {
            return max(0, $current - $quantity);
        }

        return min($maximum, $current + $quantity);
    }

    private function formatPvResult(array $result, string $heading): string
    {
        $actionLabel = $result['action'] === 'dano' ? 'Dano' : 'Cura';

        return $heading . "!\n\n"
            . "Personagem: {$result['character_name']}\n"
            . "{$actionLabel}: {$result['quantity']} PV\n"
            . "PV anteriores: {$result['pv_before']}/{$result['pv_max']}\n"
            . "PV atuais: {$result['pv_after']}/{$result['pv_max']}";
    }

    private function normalizeName(string $name): string
    {
        return Str::lower(trim(Str::ascii($name)));
    }
}
