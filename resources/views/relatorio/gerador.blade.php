<x-layout-app page-title='Gerador relatório'>
    <div class="card shadow-sm w-25 p-4">

        <h3>Gerador de Relatórios</h3>

        <hr>

        @if(session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        <form action="{{ route('gerar') }}" method="post">

            @csrf

            <div class="mb-3">
                <label for="dta_inicio" class="form-label">Data Inicial do relatório</label>
                <input type="date" class="form-control" id="dta_inicio" name="dta_inicio" value='{{ old('dta_inicio', date("Y-m-d"))}}'>
                @error('dta_inicio')
                    <div class="text-danger">{{ $message }}</div>
                @enderror                
            </div>
            <div class="mb-3">
                <label for="dta_fim" class="form-label">Data Final do relatório</label>
                <input type="date" class="form-control" id="dta_fim" name="dta_fim" value='{{ old('dta_fim', date("Y-m-d"))}}'>
                @error('dta_fim')
                    <div class="text-danger">{{ $message }}</div>
                @enderror                
            </div>
            <div class="col-auto mt-1">
                <label for="id_colaborador" class="form-label">Nome do colaborador</label>
                @if($isAdmin)
                    <select class="form-select form-select-md me-2 mb-2" id='id_colaborador' name='id_colaborador'>
                        <option value="">Escolha o terapeuta</option>
                        @foreach ($terapeutas as $terapeuta)
                            <option value="{{ $terapeuta->id }}">{{ $terapeuta->name }}</option>
                        @endforeach
                    </select>
                @else
                    <input type="text" class="form-control" value="{{ auth()->user()->name }}" disabled>
                    <input type="hidden" name="id_colaborador" value="{{ auth()->id() }}">
                @endif
                @error('id_colaborador')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>
            <div class="col-auto mt-1">
                <label for="q_relatorio" class="form-label">Qual relatório deseja gerar?</label>
                <select class="form-select form-select-md me-2 mb-2" id='q_relatorio' name='q_relatorio'>
                    <option value="">Escolha o relatório</option>
                    <option value="aveianm">Relatório AVEIANM</option>
                    <option value="bpa1">Relatório BPA-I</option>
                    <option value="mapa_producao">Relatório Mapa de Produção Mensal</option>
                    <option value="mapa_producao_diario">Relatório Mapa de Produção Diário</option>
                </select>
                @error('q_relatorio')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>
            <div class="mt-3">
                <button type="submit" id="saveBtn" class="btn btn-outline-primary">Gerar o Relatório</button>
            </div>

        </form>

    </div>

</x-layout-app>