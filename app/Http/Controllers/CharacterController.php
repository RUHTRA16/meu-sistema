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

        $totalAtributos =
            $character->agi +
            $character->forca +
            $character->intelecto +
            $character->presenca +
            $character->vigor;

        if (
            empty($character->name) ||
            !in_array($character->character_class, [
                'Combatente',
                'Especialista',
                'Ocultista',
            ], true) ||
            empty($character->origin) ||
            $character->nex !== 5 ||
            $character->agi === null ||
            $character->forca === null ||
            $character->intelecto === null ||
            $character->presenca === null ||
            $character->vigor === null ||
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

        $character->update([
            'creation_status' => 'confirmed',
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