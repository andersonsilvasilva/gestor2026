<?php

namespace App\Http\Controllers;

use App\Models\Configuracao;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ConfiguracaoController extends Controller
{
    public function edit()
    {
        $this->authorizeAdmin();

        $configuracao = Configuracao::atual();

        return view('configuracoes.edit', compact('configuracao'));
    }

    public function update(Request $request)
    {
        $this->authorizeAdmin();

        $validated = $request->validate([
            'nome_instituicao' => 'required|string|max:255',
            'nome_secretaria'  => 'required|string|max:255',
            'logo'             => 'nullable|image|mimes:png,jpg,jpeg|max:2048',
        ]);

        $configuracao = Configuracao::atual();
        $logoPath = $configuracao->logo_path;

        if ($request->hasFile('logo')) {
            $extensao = $request->file('logo')->extension();
            $logoPath = $request->file('logo')->storeAs('logos', 'logo_instituicao.' . $extensao, 'public');
        }

        $configuracao->update([
            'nome_instituicao' => $validated['nome_instituicao'],
            'nome_secretaria'  => $validated['nome_secretaria'],
            'logo_path'        => $logoPath,
        ]);

        return redirect()->route('configuracoes.edit')->with('success', 'Configurações atualizadas com sucesso!');
    }

    private function authorizeAdmin()
    {
        if (!Auth::user()->can('admin')) {
            abort(403, 'Você não tem permissão para acessar esta página.');
        }
    }
}
