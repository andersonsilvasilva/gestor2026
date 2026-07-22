<?php

namespace App\Http\Controllers;

use App\Models\Configuracao;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Str;
use PHPJasper\PHPJasper;

define('driver_path', '/vendor/geekcom/phpjasper/bin/jasperstarter/jdbc');
define('source_path', '/app/public/report/source/MyReports/gestor');
define('compiled_path', '/app/public/report/compiled');
define('output_path', '/app/public/report/output');

class ControllerReport extends Controller
{
    /**
     * Relatórios disponíveis, gerados com a engine JasperReports 7.0.1
     * (storage/app/jasperstarter7). O tipo de cada parâmetro precisa
     * bater com o declarado no .jrxml de cada relatório.
     */
    private const REPORTS = [
        'aveianm'              => 'aveianm',
        'bpa1'                 => 'bpa1',
        'mapa_producao'        => 'mapa_producao',
        'mapa_producao_diario' => 'mapa_producao_diario',
    ];

    /**
     * BPA-I usa o cabeçalho oficial fixo do SUS (uma única imagem) e não
     * declara os parâmetros de logo/instituição/secretaria.
     */
    private const REPORTS_COM_CABECALHO_CONFIGURAVEL = ['aveianm', 'mapa_producao', 'mapa_producao_diario'];

    /**
     * Nomes amigáveis usados para montar o nome do arquivo baixado.
     */
    private const REPORT_LABELS = [
        'aveianm'              => 'AVEIANM',
        'bpa1'                 => 'BPA-I',
        'mapa_producao'        => 'Mapa de Producao Mensal',
        'mapa_producao_diario' => 'Mapa de Producao Diario',
    ];

    public function index()
    {
        $verTudo = Auth::user()->can('view-all-data');
        $terapeutas = $verTudo
            ? User::where('role', 'rh')->where('ativo', true)->orderBy('name')->get()
            : User::where('id', Auth::user()->id)->get();

        return view('relatorio.gerador', compact('terapeutas', 'verTudo'));
    }

    public function getDatabaseConfigMysql()
    {
        $jdbc_dir = base_path(driver_path);

        return [
            'driver'      => env('DB_CONNECTION'),
            'host'        => env('DB_HOST', 'localhost'),
            'port'        => env('DB_PORT', '3306'),
            'username'    => env('DB_USERNAME', 'root'),
            'password'    => env('DB_PASSWORD', ''),
            'database'    => env('DB_DATABASE', 'forge'),
            'jdbc_driver' => 'com.mysql.cj.jdbc.Driver',
            'jdbc_url'    => 'jdbc:mysql://' . env('DB_HOST', 'localhost') . ':' . env('DB_PORT', '3306') . ';databaseName=' . env('DB_DATABASE', 'forge') . '&serverTimezone=UTC',
            'jdbc_dir'    => $jdbc_dir,
        ];
    }

    public function generateReport(Request $request)
    {
        $validated = $request->validate([
            'q_relatorio'     => 'required|string|in:' . implode(',', array_keys(self::REPORTS)),
            'id_colaborador'  => 'required|exists:users,id',
            'dta_inicio'      => 'required|date',
            'dta_fim'         => 'required|date|after_or_equal:dta_inicio',
        ]);

        $verTudo = Auth::user()->can('view-all-data');
        if (!$verTudo && (int) $validated['id_colaborador'] !== (int) Auth::user()->id) {
            abort(403, 'Você só pode gerar relatórios dos seus próprios atendimentos.');
        }

        $report = self::REPORTS[$validated['q_relatorio']];

        $parametros = [
            'id_colaborador=' . $validated['id_colaborador'],
            'dta_inicio=' . $validated['dta_inicio'],
            'dta_fim=' . $validated['dta_fim'],
        ];

        $payload = [
            'relatorio'  => $report,
            'parametros' => $parametros,
        ];

        if (in_array($report, self::REPORTS_COM_CABECALHO_CONFIGURAVEL, true)) {
            $configuracao = Configuracao::atual();

            $payload['incluir_logo'] = true;
            if ($configuracao->logo_path) {
                $payload['logo_url'] = rtrim(config('app.url'), '/') . '/' . $configuracao->logo_path;
            }

            $parametros[] = 'NOME_INSTITUICAO=' . $configuracao->nome_instituicao;
            $parametros[] = 'NOME_SECRETARIA=' . $configuracao->nome_secretaria;
            $parametros[] = 'UNIDADE_SAUDE=' . $configuracao->unidade_saude;
            $payload['parametros'] = $parametros;
        }

        // O motor Java do JasperReports roda num VPS externo (a Hostinger nao
        // tem Java) - ver reports-service/ na raiz do repo.
        $response = Http::withToken(config('services.reports.secret'))
            ->timeout(130)
            ->post(rtrim(config('services.reports.url'), '/') . '/gerar', $payload);

        if (!$response->successful()) {
            \Log::error('Falha ao gerar relatório ' . $report . ': ' . $response->status() . ' ' . $response->body());

            return redirect()->back()->with('error', 'Não foi possível gerar o relatório. Verifique os parâmetros e tente novamente.');
        }

        $colaborador = User::find($validated['id_colaborador']);
        $periodo = \Carbon\Carbon::parse($validated['dta_inicio'])->format('d-m-Y')
            . '_a_' . \Carbon\Carbon::parse($validated['dta_fim'])->format('d-m-Y');

        $filename = Str::slug(self::REPORT_LABELS[$validated['q_relatorio']])
            . '_' . $periodo
            . '_' . Str::slug($colaborador->name ?? 'colaborador')
            . '.pdf';

        return Response::make($response->body(), 200, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $filename . '"',
        ]);
    }

    public function getParametros()
    {
        $report = 'testedb';

        $input = storage_path(source_path) . '/' . $report . '.jrxml';

        $jasper = new PHPJasper;
        $output = $jasper->listParameters($input)->execute();

        foreach ($output as $parameter_description) {
            $parameter_description = trim($parameter_description);
            $dados = explode(' ', trim($parameter_description), 3);
            echo '<strong>Parametro:</strong>  ' . $dados[1] .
                ' <strong>Tipo de Dados:</strong>  ' . $dados[2] . '<br>';
        }
    }
}
