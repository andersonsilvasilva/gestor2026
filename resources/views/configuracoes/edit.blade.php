<x-layout-app page-title='Configurações dos Relatórios'>
    <div class="w-50 p-4">

        <h3>Configurações dos Relatórios</h3>
        <p class="text-muted">Esses dados aparecem no cabeçalho de todos os relatórios (AVEIANM, Mapa de Produção Mensal e Diário). O relatório BPA-I usa um cabeçalho oficial fixo do SUS e não é afetado.</p>

        <hr>

        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('configuracoes.update') }}" method="post" enctype="multipart/form-data">

            @csrf

            <div class="mb-3">
                <label for="nome_instituicao" class="form-label">Nome da Instituição</label>
                <input type="text" class="form-control" id="nome_instituicao" name="nome_instituicao" required
                       value="{{ old('nome_instituicao', $configuracao->nome_instituicao) }}">
                <div class="form-text">Linha de destaque no topo do relatório.</div>
                @error('nome_instituicao')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="nome_secretaria" class="form-label">Nome da Secretaria</label>
                <input type="text" class="form-control" id="nome_secretaria" name="nome_secretaria" required
                       value="{{ old('nome_secretaria', $configuracao->nome_secretaria) }}">
                <div class="form-text">Segunda linha, abaixo do nome da instituição.</div>
                @error('nome_secretaria')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="unidade_saude" class="form-label">Unidade de Saúde</label>
                <input type="text" class="form-control" id="unidade_saude" name="unidade_saude" required
                       value="{{ old('unidade_saude', $configuracao->unidade_saude) }}">
                <div class="form-text">Aparece no campo "Unidade de Saúde:" do cabeçalho do relatório.</div>
                @error('unidade_saude')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="logo" class="form-label">Logomarca</label>
                @if($configuracao->logo_path)
                    <div class="mb-2">
                        <img src="{{ asset($configuracao->logo_path) }}" alt="Logo atual" style="max-height: 100px; max-width: 200px;" class="border p-1">
                    </div>
                @endif
                <input type="file" class="form-control" id="logo" name="logo" accept="image/png,image/jpeg">
                <div class="form-text">PNG ou JPG, até 2MB. Se não enviar nada, a logo atual é mantida.</div>
                @error('logo')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <button type="submit" class="btn btn-primary">Salvar</button>
            </div>

        </form>

    </div>

</x-layout-app>
