<x-layout-app page-title="Editar Usuário">

    <div class="w-100 p-4">

        <h3>Editar Usuário</h3>
        <hr>

        <form action="{{ route('usuarios.update') }}" method="post" class="w-50">

            @csrf

            <input type="hidden" name="id" value="{{ $usuario->id }}">

            <div class="mb-3">
                <label for="name" class="form-label">Nome</label>
                <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $usuario->name) }}">
                @error('name')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $usuario->email) }}">
                @error('email')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="role" class="form-label">Papel</label>
                <select class="form-select" id="role" name="role">
                    @foreach ($roleLabels as $value => $label)
                        <option value="{{ $value }}" @selected(old('role', $usuario->role) === $value)>{{ $label }}</option>
                    @endforeach
                </select>
                @error('role')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="mt-3">
                <button type="submit" class="btn btn-primary">Salvar alterações</button>
                <a href="{{ route('usuarios.index') }}" class="btn btn-outline-danger me-3">Cancelar</a>
            </div>

        </form>

    </div>

</x-layout-app>
