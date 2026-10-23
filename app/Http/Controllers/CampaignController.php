<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class CampaignController extends Controller
{
    public function create(): View
    {
        return view('campaigns.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
        ]);

        Campaign::create($validated);

        return redirect('/')
            ->with('success', 'Campanha criada com sucesso!');
    }

    public function destroy(Campaign $campaign): RedirectResponse
    {
        if ($campaign->characters()->exists()) {
            return redirect()
                ->route('home')
                ->with(
                    'error',
                    'Não é possível excluir esta campanha porque ela ainda possui personagens. Exclua os personagens primeiro.'
                );
        }

        $campaign->delete();

        return redirect()
            ->route('home')
            ->with('success', 'Campanha excluída com sucesso!');
    }
}
