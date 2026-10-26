<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\Character;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Validation\Rule;

class CharacterController extends Controller
{
    public function create(Campaign $campaign): View
    {
        return view('characters.create', compact('campaign'));
    }

    public function store(
        Request $request,
        Campaign $campaign
    ): RedirectResponse {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'age' => ['nullable', 'integer', 'min:1', 'max:120'],
            'backstory' => ['nullable', 'string', 'max:5000'],
        ]);

        $character = $campaign->characters()->create([
            ...$validated,
            'creation_status' => 'draft',
        ]);

        return redirect()
            ->route('characters.details', [
                'campaign' => $campaign->id,
                'character' => $character->id,
            ])
            ->with('success', 'Personagem salvo com sucesso!');
    }

    public function details(
        Campaign $campaign,
        Character $character
    ): View|RedirectResponse {
        abort_unless(
            $character->campaign_id === $campaign->id,
            404
        );

        if ($character->creation_status === 'confirmed') {
            return redirect()
                ->route('characters.review', [
                    'campaign' => $campaign->id,
                    'character' => $character->id,
                ])
                ->with(
                    'error',
                    'Esta ficha já foi confirmada e não pode ser alterada.'
                );
        }

        return view('characters.details', compact(
            'campaign',
            'character'
        ));
    }

    public function updateDetails(
        Request $request,
        Campaign $campaign,
        Character $character
    ): RedirectResponse {
        abort_unless(
            $character->campaign_id === $campaign->id,
            404
        );

        if ($character->creation_status === 'confirmed') {
            return redirect()
                ->route('characters.review', [
                    'campaign' => $campaign->id,
                    'character' => $character->id,
                ])
                ->with(
                    'error',
                    'Esta ficha já foi confirmada e não pode ser alterada.'
                );
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'age' => ['nullable', 'integer', 'min:1', 'max:120'],
            'appearance' => ['nullable', 'string', 'max:5000'],
            'personality' => ['nullable', 'string', 'max:5000'],
            'backstory' => ['nullable', 'string', 'max:5000'],

            'character_class' => [
                'required',
                'string',
                Rule::in([
                    'Combatente',
                    'Especialista',
                    'Ocultista',
                ]),
            ],

            'origin' => [
                'required',
                'string',
                Rule::in([
                    'Acadêmico',
                    'Agente de Saúde',
                    'Amnésico',
                    'Artista',
                    'Atleta',
                    'Chef',
                    'Criminoso',
                    'Cultista Arrependido',
                    'Desgarrado',
                    'Engenheiro',
                    'Executivo',
                    'Explorador',
                    'Fanático por Criaturas',
                    'Fotógrafo',
                    'Investigador',
                    'Lutador',
                    'Magnata',
                    'Mercenário',
                    'Militar',
                    'Operário',
                    'Policial',
                    'Religioso',
                    'Servidor Público',
                    'Teórico da Conspiração',
                    'T.I.',
                    'Trabalhador Rural',
                    'Trambiqueiro',
                    'Universitário',
                    'Vítima',
                ]),
            ],

            'agi' => ['required', 'integer', 'min:0', 'max:3'],
            'forca' => ['required', 'integer', 'min:0', 'max:3'],
            'intelecto' => ['required', 'integer', 'min:0', 'max:3'],
            'presenca' => ['required', 'integer', 'min:0', 'max:3'],
            'vigor' => ['required', 'integer', 'min:0', 'max:3'],
        ]);

        $totalAtributos =
            $validated['agi'] +
            $validated['forca'] +
            $validated['intelecto'] +
            $validated['presenca'] +
            $validated['vigor'];

        if ($totalAtributos !== 9) {
            return back()
                ->withErrors([
                    'attributes' => 'A soma dos cinco atributos deve ser igual a 9.',
                ])
                ->withInput();
        }

        $validated['nex'] = 5;

        $character->update($validated);

        return redirect()
            ->route('characters.details', [
                'campaign' => $campaign->id,
                'character' => $character->id,
            ])
            ->with(
                'success',
                'Detalhes do personagem salvos com sucesso!'
            );
    }

    public function review(
        Campaign $campaign,
        Character $character
    ): View {
        abort_unless(
            $character->campaign_id === $campaign->id,
            404
        );

        return view('characters.review', compact(
            'campaign',
            'character'
        ));
    }

    public function updateResources(
        Request $request,
        Campaign $campaign,
        Character $character
    ): RedirectResponse {
        abort_unless(
            $character->campaign_id === $campaign->id,
            404
        );

        if ($character->creation_status !== 'confirmed') {
            return redirect()
                ->route('characters.review', [
                    'campaign' => $campaign->id,
                    'character' => $character->id,
                ])
                ->with('error', 'Confirme a ficha antes de atualizar os recursos.');
        }

        $validated = $request->validate([
            'pv_current' => [
                'required',
                'integer',
                'min:0',
                'max:' . $character->pv_max,
            ],
            'san_current' => [
                'required',
                'integer',
                'min:0',
                'max:' . $character->san_max,
            ],
            'pe_current' => [
                'required',
                'integer',
                'min:0',
                'max:' . $character->pe_max,
            ],
        ], [
            'pv_current.min' => 'PV não pode ser negativo.',
            'pv_current.max' => 'PV não pode ultrapassar o máximo.',
            'san_current.min' => 'SAN não pode ser negativa.',
            'san_current.max' => 'SAN não pode ultrapassar o máximo.',
            'pe_current.min' => 'PE não pode ser negativo.',
            'pe_current.max' => 'PE não pode ultrapassar o máximo.',
        ]);

        $character->update($validated);

        return redirect()
            ->route('characters.review', [
                'campaign' => $campaign->id,
                'character' => $character->id,
            ])
            ->with('success', 'Recursos atualizados com sucesso!');
    }

    public function updatePv(
        Request $request,
        Campaign $campaign,
        Character $character
    ): RedirectResponse {
        abort_unless(
            $character->campaign_id === $campaign->id,
            404
        );

        if (
            $character->creation_status !== 'confirmed' ||
            $character->pv_current === null ||
            $character->pv_max === null
        ) {
            return redirect()
                ->route('characters.review', [
                    'campaign' => $campaign->id,
                    'character' => $character->id,
                ])
                ->with('error', 'A ficha precisa estar confirmada e possuir PV calculados.');
        }

        $validated = $request->validate([
            'acao' => ['required', 'in:dano,cura'],
            'quantidade' => ['required', 'integer', 'min:1'],
        ], [
            'acao.required' => 'Selecione dano ou recuperação.',
            'acao.in' => 'A ação selecionada é inválida.',
            'quantidade.required' => 'Informe a quantidade de PV.',
            'quantidade.integer' => 'A quantidade deve ser um número inteiro.',
            'quantidade.min' => 'A quantidade deve ser maior que zero.',
        ]);

        $pvAtual = (int) $character->pv_current;
        $quantidade = (int) $validated['quantidade'];

        if ($validated['acao'] === 'dano') {
            $novoPv = max(0, $pvAtual - $quantidade);
        } else {
            $novoPv = min(
                (int) $character->pv_max,
                $pvAtual + $quantidade
            );
        }

        $character->update([
            'pv_current' => $novoPv,
        ]);

        return redirect()
            ->route('characters.review', [
                'campaign' => $campaign->id,
                'character' => $character->id,
            ])
            ->with('success', 'PV atualizados com sucesso!');
    }

    public function updateSan(
        Request $request,
        Campaign $campaign,
        Character $character
    ): RedirectResponse {
        abort_unless(
            $character->campaign_id === $campaign->id,
            404
        );

        if (
            $character->creation_status !== 'confirmed' ||
            $character->san_current === null ||
            $character->san_max === null
        ) {
            return redirect()
                ->route('characters.review', [
                    'campaign' => $campaign->id,
                    'character' => $character->id,
                ])
                ->with(
                    'error',
                    'A ficha precisa estar confirmada e possuir SAN calculada.'
                );
        }

        $validated = $request->validate([
            'acao_san' => ['required', 'in:perda,recuperacao'],
            'quantidade_san' => ['required', 'integer', 'min:1'],
        ], [
            'acao_san.required' => 'Selecione perda ou recuperação de SAN.',
            'acao_san.in' => 'A ação selecionada é inválida.',
            'quantidade_san.required' => 'Informe a quantidade de SAN.',
            'quantidade_san.integer' => 'A quantidade deve ser um número inteiro.',
            'quantidade_san.min' => 'A quantidade deve ser maior que zero.',
        ]);

        $sanAtual = (int) $character->san_current;
        $quantidade = (int) $validated['quantidade_san'];

        if ($validated['acao_san'] === 'perda') {
            $novaSan = max(0, $sanAtual - $quantidade);
        } else {
            $novaSan = min(
                (int) $character->san_max,
                $sanAtual + $quantidade
            );
        }

        $character->update([
            'san_current' => $novaSan,
        ]);

        return redirect()
            ->route('characters.review', [
                'campaign' => $campaign->id,
                'character' => $character->id,
            ])
            ->with('success', 'SAN atualizada com sucesso!');
    }

    public function updatePe(
        Request $request,
        Campaign $campaign,
        Character $character
    ): RedirectResponse {
        abort_unless(
            $character->campaign_id === $campaign->id,
            404
        );

        if (
            $character->creation_status !== 'confirmed' ||
            $character->pe_current === null ||
            $character->pe_max === null
        ) {
            return redirect()
                ->route('characters.review', [
                    'campaign' => $campaign->id,
                    'character' => $character->id,
                ])
                ->with(
                    'error',
                    'A ficha precisa estar confirmada e possuir PE calculados.'
                );
        }

        $validated = $request->validate([
            'acao_pe' => ['required', 'in:gasto,recuperacao'],
            'quantidade_pe' => ['required', 'integer', 'min:1'],
        ], [
            'acao_pe.required' => 'Selecione gasto ou recuperação de PE.',
            'acao_pe.in' => 'A ação selecionada é inválida.',
            'quantidade_pe.required' => 'Informe a quantidade de PE.',
            'quantidade_pe.integer' => 'A quantidade deve ser um número inteiro.',
            'quantidade_pe.min' => 'A quantidade deve ser maior que zero.',
        ]);

        $peAtual = (int) $character->pe_current;
        $quantidade = (int) $validated['quantidade_pe'];

        if ($validated['acao_pe'] === 'gasto') {
            $novoPe = max(0, $peAtual - $quantidade);
        } else {
            $novoPe = min(
                (int) $character->pe_max,
                $peAtual + $quantidade
            );
        }

        $character->update([
            'pe_current' => $novoPe,
        ]);

        return redirect()
            ->route('characters.review', [
                'campaign' => $campaign->id,
                'character' => $character->id,
            ])
            ->with('success', 'PE atualizados com sucesso!');
    }

    public function confirm(
        Campaign $campaign,
        Character $character
        ): RedirectResponse {
        abort_unless(
            $character->campaign_id === $campaign->id,
            404
        );

        $character->refresh();

        if ($character->creation_status === 'confirmed') {
            return redirect()
                ->route('characters.review', [
                    'campaign' => $campaign->id,
                    'character' => $character->id,
                ])
                ->with('success', 'Esta ficha já está confirmada.');
        }

        $atributos = [
            $character->agi,
            $character->forca,
            $character->intelecto,
            $character->presenca,
            $character->vigor,
        ];

        $atributosValidos = collect($atributos)->every(
            fn ($atributo) => $atributo !== null
                && $atributo >= 0
                && $atributo <= 3
        );

        $totalAtributos = array_sum($atributos);

        $classesValidas = [
            'Combatente',
            'Especialista',
            'Ocultista',
        ];

        $origensValidas = [
            'Acadêmico',
            'Agente de Saúde',
            'Amnésico',
            'Artista',
            'Atleta',
            'Chef',
            'Criminoso',
            'Cultista Arrependido',
            'Desgarrado',
            'Engenheiro',
            'Executivo',
            'Explorador',
            'Fanático por Criaturas',
            'Fotógrafo',
            'Investigador',
            'Lutador',
            'Magnata',
            'Mercenário',
            'Militar',
            'Operário',
            'Policial',
            'Religioso',
            'Servidor Público',
            'Teórico da Conspiração',
            'T.I.',
            'Trabalhador Rural',
            'Trambiqueiro',
            'Universitário',
            'Vítima',
        ];

        if (
            empty($character->name) ||
            !in_array($character->character_class, $classesValidas, true) ||
            !in_array($character->origin, $origensValidas, true) ||
            $character->nex !== 5 ||
            !$atributosValidos ||
            $totalAtributos !== 9
        ) {
            return redirect()
                ->route('characters.details', [
                    'campaign' => $campaign->id,
                    'character' => $character->id,
                ])
                ->with(
                    'error',
                    'Não foi possível confirmar a ficha. Confira o nome, a classe, a origem, o NEX e a distribuição dos atributos.'
                );
        }

            $vigor = (int) $character->vigor;
            $presenca = (int) $character->presenca;

            $recursos = match ($character->character_class) {
                'Combatente' => [
                    'pv' => 20 + $vigor,
                    'san' => 12,
                    'pe' => 2 + $presenca,
                ],

                'Especialista' => [
                    'pv' => 16 + $vigor,
                    'san' => 16,
                    'pe' => 3 + $presenca,
                ],

                'Ocultista' => [
                    'pv' => 12 + $vigor,
                    'san' => 20,
                    'pe' => 4 + $presenca,
                ],
            };

            $character->update([
                'creation_status' => 'confirmed',

                'pv_max' => $recursos['pv'],
                'pv_current' => $recursos['pv'],

                'san_max' => $recursos['san'],
                'san_current' => $recursos['san'],

                'pe_max' => $recursos['pe'],
                'pe_current' => $recursos['pe'],
            ]);

        return redirect()
            ->route('characters.review', [
                'campaign' => $campaign->id,
                'character' => $character->id,
            ])
            ->with(
                'success',
                'Ficha do personagem confirmada com sucesso!'
            );
    }

    public function destroy(
        Campaign $campaign,
        Character $character
    ): RedirectResponse {
        abort_unless(
            $character->campaign_id === $campaign->id,
            404
        );

        $character->delete();

        return redirect()
            ->route('home')
            ->with('success', 'Personagem excluído com sucesso!');
    }
}
