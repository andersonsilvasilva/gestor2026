<x-layout-app page-title="Novo Usuário">

    <div class="w-100 p-4">

        <h3>Novo Usuário</h3>
        <hr>

        <form action="{{ route('usuarios.store') }}" method="post" class="w-50">

            @csrf

            <div class="mb-3">
                <label for="name" class="form-label">Nome</label>
                <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}">
                @error('name')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}">
                @error('email')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="role" class="form-label">Papel</label>
                <select class="form-select" id="role" name="role">
                    <option value="">Escolha o papel</option>
                    @foreach ($roleLabels as $value => $label)
                        <option value="{{ $value }}" @selected(old('role') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
                @error('role')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <p class="text-muted">O usuário receberá um e-mail para confirmar a conta e definir a senha.</p>

            <div class="mt-3">
                <button type="submit" class="btn btn-primary">Criar usuário</button>
                <a href="{{ route('usuarios.index') }}" class="btn btn-outline-danger me-3">Cancelar</a>
            </div>

        </form>

    </div>

</x-layout-app>
