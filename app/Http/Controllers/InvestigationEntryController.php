<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\InvestigationEntry;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InvestigationEntryController extends Controller
{
    /**
     * Exibe o diário da campanha.
     */
    public function index(Campaign $campaign): View
    {
        $entries = $campaign->investigationEntries()->get();

        return view('investigations.index', compact(
            'campaign',
            'entries'
        ));
    }

    /**
     * Salva um novo registro no diário.
     */
    public function store(
        Request $request,
        Campaign $campaign
    ): RedirectResponse {
        $validated = $this->validateEntry($request);

        $campaign->investigationEntries()->create($validated);

        return redirect()
            ->route('investigation.entries.index', $campaign)
            ->with('success', 'Registro adicionado ao diário da investigação!');
    }

    /**
     * Exibe o formulário de edição.
     */
    public function edit(
        Campaign $campaign,
        InvestigationEntry $entry
    ): View {
        $entry = $this->findCampaignEntry($campaign, $entry);

        return view('investigations.edit', compact(
            'campaign',
            'entry'
        ));
    }

    /**
     * Atualiza um registro existente.
     */
    public function update(
        Request $request,
        Campaign $campaign,
        InvestigationEntry $entry
    ): RedirectResponse {
        $entry = $this->findCampaignEntry($campaign, $entry);

        $validated = $this->validateEntry($request);

        $entry->update($validated);

        return redirect()
            ->route('investigation.entries.index', $campaign)
            ->with('success', 'Registro atualizado com sucesso!');
    }

    /**
     * Exclui um registro do diário.
     */
    public function destroy(
        Campaign $campaign,
        InvestigationEntry $entry
    ): RedirectResponse {
        $entry = $this->findCampaignEntry($campaign, $entry);

        $entry->delete();

        return redirect()
            ->route('investigation.entries.index', $campaign)
            ->with('success', 'Registro excluído do diário da investigação.');
    }

    /**
     * Valida os campos usados no cadastro e na edição.
     */
    private function validateEntry(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:150'],
            'content' => ['required', 'string', 'max:10000'],
            'category' => [
                'required',
                'in:pista,evidencia,suspeito,descoberta,acontecimento',
            ],
            'occurred_at' => ['nullable', 'date'],
        ], [
            'title.required' => 'Informe o título do registro.',
            'title.max' => 'O título deve ter no máximo 150 caracteres.',
            'content.required' => 'Descreva a pista ou o acontecimento.',
            'content.max' => 'A descrição deve ter no máximo 10.000 caracteres.',
            'category.required' => 'Selecione uma categoria.',
            'category.in' => 'A categoria selecionada é inválida.',
            'occurred_at.date' => 'Informe uma data válida.',
        ]);
    }

    /**
     * Garante que o registro pertença à campanha informada.
     */
    private function findCampaignEntry(
        Campaign $campaign,
        InvestigationEntry $entry
    ): InvestigationEntry {
        return $campaign->investigationEntries()
            ->whereKey($entry->id)
            ->firstOrFail();
    }
}
