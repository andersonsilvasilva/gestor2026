<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Configuracao extends Model
{
    protected $table = 'configuracoes';

    protected $fillable = [
        'nome_instituicao',
        'nome_secretaria',
        'logo_path',
    ];

    /**
     * Configuração única do sistema (linha singleton).
     */
    public static function atual(): self
    {
        return static::first() ?? static::create([]);
    }
}
