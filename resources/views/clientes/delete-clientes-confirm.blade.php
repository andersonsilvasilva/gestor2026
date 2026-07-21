<x-layout-app page-title="Apagar o Cliente">
    <div class="w-25 p-4">

        <h3>Apagar o Cliente</h3>
        <hr>

        <p>Você tem certeza que deseja apagar o cliente?</p>
        
        <div class="text-left">
            <h6>{{$clientes->nome}}</h6>
            <p>Cartão C.N.S.: <strong>{{$clientes->cns}}</strong></p>                
            <p>Data de Nascimento: <strong>{{ \Carbon\Carbon::parse($clientes->data_nascimento)->format('d/m/Y')}}</strongs></p>
            <p></p>    
            <a href="{{route('clientes')}}" class="btn btn-secondary px-5">Não</a>
            <form action="{{route('cliente.delete-cliente_confirm',['id' =>Crypt::encrypt( $clientes->id)])}}" method="post" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-danger px-5">Sim</button>
            </form>
        </div>

    </div>

</x-layout-app>