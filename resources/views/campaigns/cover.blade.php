@extends('layouts.app')

@section('title', 'Editar capa | Mestre Paranormal')
@section('topbar', 'Editar capa da investigação')

@section('content')

<div class="operations-dashboard">

    <section class="dashboard-heading">
        <div>
            <span class="eyebrow">DIVISÃO DE INVESTIGAÇÃO PARANORMAL</span>

            <h1>Editar capa</h1>

            <p>
                Personalize a identidade visual deste dossiê sem alterar
                os dados da investigação.
            </p>
        </div>

        <a href="{{ route('home') }}" class="btn btn-secondary">
            Voltar à central
        </a>
    </section>

    <section class="card campaign-cover-editor">

        <span class="eyebrow">
            DOSSIÊ Nº {{ str_pad((string) $campaign->id, 4, '0', STR_PAD_LEFT) }}
        </span>

        <h2>{{ $campaign->title }}</h2>

        <p>
            Selecione uma nova imagem para substituir a capa atual
            ou remova a imagem personalizada para voltar ao visual padrão.
        </p>

        @if ($errors->any())
            <div class="alert">
                <strong>Confira os dados informados:</strong>

                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @php
            $currentCoverUrl = $campaign->cover_path
                ? asset('storage/' . $campaign->cover_path)
                : ($campaign->id === $campaign->getKey() && $campaign->id == $campaign->getKey()
                    ? null
                    : null);
        @endphp

        @if ($currentCoverUrl)
            <div class="cover-editor-preview">
                <span class="meta-label">CAPA ATUAL</span>

                <img
                    src="{{ $currentCoverUrl }}"
                    alt="Capa atual da campanha {{ $campaign->title }}"
                    class="cover-editor-image"
                >
            </div>
        @else
            <div class="team-empty">
                <span aria-hidden="true">◉</span>

                <p>
                    Esta investigação não possui uma capa personalizada.
                    Você pode selecionar uma imagem abaixo.
                </p>
            </div>
        @endif

        <form
            action="{{ route('campaigns.cover.update', ['campaign' => $campaign->id]) }}"
            method="POST"
            enctype="multipart/form-data"
        >
            @csrf
            @method('PUT')

            <label for="cover">Nova imagem da capa</label>

            <input
                type="file"
                id="cover"
                name="cover"
                accept="image/jpeg,image/png,image/webp"
                class="@error('cover') input-error @enderror"
            >

            <small>
                Formatos aceitos: JPG, PNG e WebP. Tamanho máximo: 4 MB.
            </small>

            @error('cover')
                <p class="error">{{ $message }}</p>
            @enderror

            <div id="cover-preview-container" class="cover-editor-preview" hidden>
                <span class="meta-label">PRÉ-VISUALIZAÇÃO DA NOVA CAPA</span>

                <img
                    id="cover-preview"
                    src=""
                    alt="Pré-visualização da nova capa"
                    class="cover-editor-image"
                >
            </div>

            <div class="investigation-actions">
                <button type="submit" class="btn">
                    Salvar nova capa
                </button>

                <a href="{{ route('home') }}" class="btn btn-secondary">
                    Cancelar
                </a>
            </div>
        </form>

        @if ($campaign->cover_path)
            <div class="cover-remove-section">
                <div>
                    <h3>Remover capa personalizada</h3>

                    <p>
                        A imagem personalizada será removida e a investigação
                        voltará a utilizar o visual padrão do sistema.
                    </p>
                </div>

                <form
                    action="{{ route('campaigns.cover.update', ['campaign' => $campaign->id]) }}"
                    method="POST"
                    onsubmit="return confirm('Deseja realmente remover a capa personalizada desta investigação?');"
                >
                    @csrf
                    @method('PUT')

                    <input type="hidden" name="remove_cover" value="1">

                    <button type="submit" class="text-danger-button">
                        Remover capa
                    </button>
                </form>
            </div>
        @endif

    </section>

</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const input = document.getElementById('cover');
        const container = document.getElementById('cover-preview-container');
        const preview = document.getElementById('cover-preview');

        if (!input || !container || !preview) {
            return;
        }

        let currentObjectUrl = null;

        input.addEventListener('change', function () {
            const file = input.files && input.files[0];

            if (currentObjectUrl) {
                URL.revokeObjectURL(currentObjectUrl);
                currentObjectUrl = null;
            }

            if (!file) {
                preview.removeAttribute('src');
                container.hidden = true;
                return;
            }

            if (!file.type.startsWith('image/')) {
                preview.removeAttribute('src');
                container.hidden = true;
                return;
            }

            currentObjectUrl = URL.createObjectURL(file);
            preview.src = currentObjectUrl;
            container.hidden = false;
        });
    });
</script>

@endsection