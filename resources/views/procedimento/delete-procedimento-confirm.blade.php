<x-layout-app page-title='Apagar Procedimento'>
    <div class="w-25 p-4">

        <h3>Apagar Procedimento</h3>

        <hr>

        <p>Você tem certeza que quer apagar este procedimento?</p>

        <div class="text-center">
            <h3 class="my-5">{{ $procedimento->name}}</h3>
            <a href="{{ route('procedimentos') }}" class="btn btn-secondary px-5">Não</a>
            <form action="{{ route('procedimentos.delete-Procedimento-confirm', ['id'=>$procedimento->id]) }}" method="post" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-danger px-5">Sim</button>
            </form>
        </div>

    </div>
</x-layout-app>