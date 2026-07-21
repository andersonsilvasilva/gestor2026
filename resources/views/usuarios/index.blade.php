<x-layout-app page-title='Gestão de Usuários'>
    <div class="border-black shadow w-100 p-2">

        <h3>Gestão de Usuários</h3>
        <p class="text-muted">Contas de acesso ao sistema (Administrador, Responsável Técnico, Secretaria). Colaboradores (terapeutas) são gerenciados na tela de Colaboradores.</p>
        <hr>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <div class="mb-3">
            <a href="{{ route('usuarios.create') }}" class="btn btn-danger">Criar novo usuário</a>
        </div>

        @if($usuarios->count() === 0)
            <div class="text-center my-5">
                <p>Nenhum usuário encontrado.</p>
            </div>
        @else
            <table class="table table-striped table-success" id="table">
                <thead class="table-dark">
                    <th>Nome</th>
                    <th>Email</th>
                    <th>Papel</th>
                    <th>Status</th>
                    <th></th>
                </thead>
                <tbody>
                    @foreach ($usuarios as $usuario)
                        <tr>
                            <td>{{ $usuario->name }}</td>
                            <td>{{ $usuario->email }}</td>
                            <td>{{ $roleLabels[$usuario->role] ?? $usuario->role }}</td>
                            <td>
                                @if($usuario->ativo)
                                    <span class="badge bg-success">Ativo</span>
                                @else
                                    <span class="badge bg-secondary">Inativo</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex gap-3 justify-content-end">
                                    <a href="{{ route('usuarios.edit', ['id' => $usuario->id]) }}" class="btn btn-sm btn-outline-dark ms-3"><i class="fa-regular fa-pen-to-square me-2"></i>Editar</a>
                                    <form action="{{ route('usuarios.toggle-status', ['id' => $usuario->id]) }}" method="post" class="m-0">
                                        @csrf
                                        @if($usuario->ativo)
                                            <button type="submit" class="btn btn-sm btn-outline-warning ms-3"><i class="fa-regular fa-circle-xmark me-2"></i>Inativar</button>
                                        @else
                                            <button type="submit" class="btn btn-sm btn-outline-success ms-3"><i class="fa-regular fa-circle-check me-2"></i>Ativar</button>
                                        @endif
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

    </div>
</x-layout-app>
