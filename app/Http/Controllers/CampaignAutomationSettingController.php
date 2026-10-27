<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CampaignAutomationSettingController extends Controller
{
    /**
     * Exibe as configurações de automação da campanha.
     */
    public function edit(Campaign $campaign): View
    {
        $settings = $campaign->automationSetting()->firstOrCreate([]);

        return view('campaign-settings.automation', [
            'campaign' => $campaign,
            'settings' => $settings,
        ]);
    }

    /**
     * Salva as preferências de automação da campanha.
     */
    public function update(
        Request $request,
        Campaign $campaign
    ): RedirectResponse {
        $validated = $request->validate([
            'dice_rolling' => [
                'required',
                Rule::in(['manual', 'automatic']),
            ],
            'initiative' => [
                'required',
                Rule::in(['manual', 'automatic']),
            ],
            'npc_control' => [
                'required',
                Rule::in(['manual', 'automatic']),
            ],
            'ai_actions' => [
                'required',
                Rule::in(['confirm', 'automatic']),
            ],
        ], [
            'dice_rolling.required' => 'Selecione o modo de rolagem de dados.',
            'initiative.required' => 'Selecione o modo de iniciativa.',
            'npc_control.required' => 'Selecione o modo de controle de NPCs.',
            'ai_actions.required' => 'Selecione o modo de execução da IA.',
            '*.in' => 'Uma das opções selecionadas é inválida.',
        ]);

        $campaign->automationSetting()->updateOrCreate(
            ['campaign_id' => $campaign->id],
            $validated
        );

        return redirect()
            ->route('campaign-settings.automation.edit', $campaign)
            ->with('success', 'Configurações de automação salvas com sucesso!');
    }
}
