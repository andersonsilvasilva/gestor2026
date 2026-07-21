<x-layout-app page-title='Colaboradores'>
    <div class="border-black shadow w-100 p-2">

        <h3>Colaboradores</h3>
        <hr>

        @if($colaborators->count()=== 0)

            <div class="text-center my-5">
                <p>Nenhum colaborador encontrado.</p>
                <a href="{{ route('colaborators.rh.new-colaborator')}}" class="btn btn-danger">Criar um novo Colaborador</a>
            </div>

        @else
            <div class="mb-3">
                <a href="{{ route('colaborators.rh.new-colaborator')}}" class="btn btn-danger">Criar um novo Colaborador</a>
            </div>
            <table class="table table-striped table-success" id="table">
                <thead class="table-dark">
                    <th>Nome</th>
                    <th>Email</th>
                    <th>Regras</th>
                    <th>Salario</th>
                    <th>Cidade</th>
                    <th>Data de Admissão</th>
                    <th>Status</th>
                    <th></th>
                </thead>
                <tbody>
                    @foreach ($colaborators as $colaborator)
                        <tr>
                            <td>{{$colaborator->name}}</td>
                            <td>{{$colaborator->email}}</td>
                            <td>{{$colaborator->role}}</td>
                            <td>{{$colaborator->detail->salario}}</td>
                            <td>{{$colaborator->detail->cidade}}</td>
                            <td>{{ \Carbon\Carbon::parse($colaborator->detail->admission_date)->format('d/m/Y')}}
                            <td>
                                @if($colaborator->ativo)
                                    <span class="badge bg-success">Ativo</span>
                                @else
                                    <span class="badge bg-secondary">Inativo</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex gap-3 justify-content-end">
                                        <a href="{{ route('colaborators.rh.edita-colaborator',  ['id' => $colaborator->id]) }}" class="btn btn-sm btn-outline-dark ms-3"><i class="fa-regular fa-pen-to-square me-2"></i>Editar</a>
                                        <form action="{{ route('colaborators.rh.toggle-status', ['id' => $colaborator->id]) }}" method="post" class="m-0">
                                            @csrf
                                            @if($colaborator->ativo)
                                                <button type="submit" class="btn btn-sm btn-outline-warning ms-3"><i class="fa-regular fa-circle-xmark me-2"></i>Inativar</button>
                                            @else
                                                <button type="submit" class="btn btn-sm btn-outline-success ms-3"><i class="fa-regular fa-circle-check me-2"></i>Ativar</button>
                                            @endif
                                        </form>
                                        <a href="{{ route('colaborators.rh.delete-colaborator', ['id' => $colaborator->id]) }}" class="btn btn-sm btn-outline-dark ms-3"><i class="fa-regular fa-trash-can me-2"></i>Deletar</a>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            
        @endif
        
    </div>
</x-layout-app>
