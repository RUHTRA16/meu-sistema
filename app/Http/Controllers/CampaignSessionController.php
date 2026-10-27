<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\CampaignSession;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CampaignSessionController extends Controller
{
    public function index(Campaign $campaign): View
    {
        $sessions = $campaign->sessions()->get();

        return view('sessions.index', compact(
            'campaign',
            'sessions'
        ));
    }

    public function store(
        Request $request,
        Campaign $campaign
    ): RedirectResponse {
        $validated = $this->validateSession($request, $campaign);

        $campaign->sessions()->create($validated);

        return redirect()
            ->route('sessions.index', $campaign)
            ->with('success', 'Sessão cadastrada com sucesso!');
    }

    public function edit(
        Campaign $campaign,
        CampaignSession $session
    ): View {
        $session = $this->findCampaignSession($campaign, $session);

        return view('sessions.edit', compact(
            'campaign',
            'session'
        ));
    }

    public function update(
        Request $request,
        Campaign $campaign,
        CampaignSession $session
    ): RedirectResponse {
        $session = $this->findCampaignSession($campaign, $session);

        $validated = $this->validateSession(
            $request,
            $campaign,
            $session
        );

        $session->update($validated);

        return redirect()
            ->route('sessions.index', $campaign)
            ->with('success', 'Sessão atualizada com sucesso!');
    }

    public function complete(
        Campaign $campaign,
        CampaignSession $session
    ): RedirectResponse {
        $session = $this->findCampaignSession($campaign, $session);

        $session->update([
            'status' => 'completed',
        ]);

        return redirect()
            ->route('sessions.index', $campaign)
            ->with('success', 'Sessão marcada como concluída!');
    }

    public function destroy(
        Campaign $campaign,
        CampaignSession $session
    ): RedirectResponse {
        $session = $this->findCampaignSession($campaign, $session);

        $session->delete();

        return redirect()
            ->route('sessions.index', $campaign)
            ->with('success', 'Sessão excluída com sucesso!');
    }

    private function validateSession(
        Request $request,
        Campaign $campaign,
        ?CampaignSession $session = null
    ): array {
        return $request->validate([
            'session_number' => [
                'required',
                'integer',
                'min:1',
                Rule::unique('campaign_sessions', 'session_number')
                    ->where('campaign_id', $campaign->id)
                    ->ignore($session?->id),
            ],
            'title' => [
                'required',
                'string',
                'max:150',
            ],
            'scheduled_at' => [
                'nullable',
                'date',
            ],
            'summary' => [
                'nullable',
                'string',
                'max:20000',
            ],
            'status' => [
                'required',
                Rule::in(['planned', 'completed']),
            ],
        ], [
            'session_number.required' => 'Informe o número da sessão.',
            'session_number.integer' => 'O número da sessão deve ser inteiro.',
            'session_number.min' => 'O número da sessão deve ser maior que zero.',
            'session_number.unique' => 'Esse número já está sendo utilizado nesta campanha.',
            'title.required' => 'Informe o título da sessão.',
            'title.max' => 'O título deve ter no máximo 150 caracteres.',
            'scheduled_at.date' => 'Informe uma data válida.',
            'summary.max' => 'O resumo deve ter no máximo 20.000 caracteres.',
            'status.required' => 'Selecione o status da sessão.',
            'status.in' => 'O status selecionado é inválido.',
        ]);
    }

    private function findCampaignSession(
        Campaign $campaign,
        CampaignSession $session
    ): CampaignSession {
        return $campaign->sessions()
            ->whereKey($session->id)
            ->firstOrFail();
    }
}