<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Configuracao extends Model
{
    protected $table = 'configuracoes';

    protected $fillable = [
        'nome_instituicao',
        'nome_secretaria',
        'unidade_saude',
        'logo_path',
    ];

    /**
     * Configuração única do sistema (linha singleton).
     */
    public static function atual(): self
    {
        return static::first() ?? static::create([]);
    }

    /**
     * Raiz realmente servida na web. Em produção (Hostinger), `public_html`
     * é uma pasta IRMÃ da raiz do app, não a `public/` do Laravel — por isso
     * `public_path()` sozinho aponta pro lugar errado lá (embora funcione
     * normalmente em ambiente local, onde essa pasta irmã não existe).
     */
    public static function webRootPath(): string
    {
        $publicHtmlIrmao = dirname(base_path()) . '/public_html';

        return is_dir($publicHtmlIrmao) ? $publicHtmlIrmao : public_path();
    }
}
