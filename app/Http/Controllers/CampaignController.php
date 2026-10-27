<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class CampaignController extends Controller
{
    public function create(): View
    {
        return view('campaigns.create');
    }

    /**
     * Exibe os detalhes de uma campanha.
     */
    public function show(Campaign $campaign): View
    {
        $campaign->load('characters');

        return view('campaigns.show', compact('campaign'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'cover' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:4096',
            ],
        ], [
            'title.required' => 'Informe o nome da campanha.',
            'title.max' => 'O nome da campanha não pode ultrapassar 255 caracteres.',
            'cover.image' => 'O arquivo selecionado precisa ser uma imagem válida.',
            'cover.mimes' => 'A capa deve estar no formato JPG, PNG ou WebP.',
            'cover.max' => 'A imagem da capa deve ter no máximo 4 MB.',
        ]);

        $coverPath = null;

        if ($request->hasFile('cover')) {
            $coverPath = $request->file('cover')
                ->store('campaign-covers', 'public');
        }

        Campaign::create([
            'title' => $validated['title'],
            'cover_path' => $coverPath,
        ]);

        return redirect('/')
            ->with('success', 'Campanha criada com sucesso!');
    }

    /**
     * Exibe a página de edição da capa.
     */
    public function editCover(Campaign $campaign): View
    {
        return view('campaigns.cover', compact('campaign'));
    }

    /**
     * Atualiza ou remove a capa da campanha.
     */
    public function updateCover(
        Request $request,
        Campaign $campaign
    ): RedirectResponse {
        $request->validate([
            'cover' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:4096',
            ],
            'remove_cover' => ['nullable', 'boolean'],
        ], [
            'cover.image' => 'O arquivo selecionado precisa ser uma imagem válida.',
            'cover.mimes' => 'A capa deve estar no formato JPG, PNG ou WebP.',
            'cover.max' => 'A imagem da capa deve ter no máximo 4 MB.',
        ]);

        $removeCover = $request->boolean('remove_cover');

        if ($removeCover && $request->hasFile('cover')) {
            return back()
                ->withErrors([
                    'cover' => 'Escolha uma nova imagem ou marque a opção de remover a capa, não as duas.',
                ])
                ->withInput();
        }

        if ($request->hasFile('cover')) {
            $newCoverPath = $request->file('cover')
                ->store('campaign-covers', 'public');

            if ($campaign->cover_path) {
                Storage::disk('public')->delete($campaign->cover_path);
            }

            $campaign->cover_path = $newCoverPath;
            $campaign->save();

            return redirect()
                ->route('home')
                ->with('success', 'Capa da investigação atualizada com sucesso!');
        }

        if ($removeCover && $campaign->cover_path) {
            Storage::disk('public')->delete($campaign->cover_path);

            $campaign->cover_path = null;
            $campaign->save();

            return redirect()
                ->route('home')
                ->with('success', 'Capa personalizada removida com sucesso!');
        }

        return redirect()
            ->route('home')
            ->with('success', 'Nenhuma alteração foi necessária.');
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

        if ($campaign->cover_path) {
            Storage::disk('public')->delete($campaign->cover_path);
        }

        $campaign->delete();

        return redirect()
            ->route('home')
            ->with('success', 'Campanha excluída com sucesso!');
    }
}