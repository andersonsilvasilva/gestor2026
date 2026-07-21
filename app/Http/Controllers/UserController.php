<?php

namespace App\Http\Controllers;

use App\Mail\ConfirmAccountEmail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class UserController extends Controller
{
    /**
     * Papéis geridos por esta tela (contas de acesso, sem perfil de RH).
     * O papel "rh" continua exclusivo da tela de Colaboradores.
     */
    private const ROLES = ['admin', 'rt', 'sec'];

    private const ROLE_LABELS = [
        'admin' => 'Administrador',
        'rt'    => 'Responsável Técnico',
        'sec'   => 'Secretaria',
    ];

    public function index()
    {
        $this->authorizeAdmin();

        $usuarios = User::whereIn('role', self::ROLES)->orderBy('name')->get();

        return view('usuarios.index', [
            'usuarios'    => $usuarios,
            'roleLabels'  => self::ROLE_LABELS,
        ]);
    }

    public function create()
    {
        $this->authorizeAdmin();

        return view('usuarios.create', ['roleLabels' => self::ROLE_LABELS]);
    }

    public function store(Request $request)
    {
        $this->authorizeAdmin();

        $validated = $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'role'  => 'required|string|in:' . implode(',', self::ROLES),
        ]);

        $token = Str::random(60);

        $user = new User();
        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->role = $validated['role'];
        $user->permissions = json_encode([$validated['role']]);
        $user->confirmation_token = $token;
        $user->ativo = true;
        $user->save();

        try {
            Mail::to($user->email)->send(new ConfirmAccountEmail(route('confirmAccount', $token)));
        } catch (\Exception $e) {
            \Log::error('Erro ao enviar e-mail: ' . $e->getMessage());
        }

        return redirect()->route('usuarios.index')->with('success', 'Usuário criado com sucesso! Um e-mail de confirmação foi enviado.');
    }

    public function edit($id)
    {
        $this->authorizeAdmin();

        $usuario = User::whereIn('role', self::ROLES)->findOrFail($id);

        return view('usuarios.edit', ['usuario' => $usuario, 'roleLabels' => self::ROLE_LABELS]);
    }

    public function update(Request $request)
    {
        $this->authorizeAdmin();

        $validated = $request->validate([
            'id'    => 'required|exists:users,id',
            'name'  => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $request->id,
            'role'  => 'required|string|in:' . implode(',', self::ROLES),
        ]);

        $usuario = User::whereIn('role', self::ROLES)->findOrFail($validated['id']);
        $usuario->name = $validated['name'];
        $usuario->email = $validated['email'];
        $usuario->role = $validated['role'];
        $usuario->save();

        return redirect()->route('usuarios.index')->with('success', 'Usuário atualizado com sucesso!');
    }

    public function toggleStatus($id)
    {
        $this->authorizeAdmin();

        if ((int) $id === (int) Auth::user()->id) {
            return redirect()->route('usuarios.index')->with('error', 'Você não pode inativar a si mesmo.');
        }

        $usuario = User::whereIn('role', self::ROLES)->findOrFail($id);
        $usuario->ativo = !$usuario->ativo;
        $usuario->save();

        return redirect()->route('usuarios.index')->with(
            'success',
            $usuario->ativo ? 'Usuário ativado com sucesso!' : 'Usuário inativado com sucesso!'
        );
    }

    private function authorizeAdmin()
    {
        abort_unless(Auth::user()->can('admin'), 403, 'Você não tem permissão para acessar esta página.');
    }
}
