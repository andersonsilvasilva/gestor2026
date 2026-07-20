<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use PHPJasper\PHPJasper;
use Symfony\Component\Process\Process;

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

    public function index()
    {
        $terapeutas = User::all();

        return view('relatorio.gerador', compact('terapeutas'));
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

        $report = self::REPORTS[$validated['q_relatorio']];

        $jrxmlPath = storage_path('app/public/report/source/MyReports/' . $report . '.jrxml');
        $jasperPath = storage_path('app/public/report/compiled/' . $report . '.jasper');
        $binDir = storage_path('app/jasperstarter7/bin');

        if (!file_exists($jasperPath) && file_exists($jrxmlPath)) {
            $compile = new Process([$binDir . '/jasperstarter.bat', 'compile', $jrxmlPath, '-o', dirname($jasperPath)], $binDir);
            $compile->setTimeout(60);
            $compile->run();
        }

        if (!file_exists($jasperPath)) {
            return redirect()->back()->with('error', 'Relatório não encontrado: ' . $report);
        }

        $outputDir = storage_path('app/public/report/output');
        if (!is_dir($outputDir)) {
            mkdir($outputDir, 0777, true);
        }
        $outputBasename = $report . '_' . uniqid();
        $outputPath = $outputDir . '/' . $outputBasename;

        $dbUrl = sprintf(
            "jdbc:mysql://%s:%s/%s?sessionVariables=sql_mode=''",
            env('DB_HOST', '127.0.0.1'),
            env('DB_PORT', '3306'),
            env('DB_DATABASE', 'gestor')
        );

        $args = [
            $binDir . '/jasperstarter.bat',
            'process',
            $jasperPath,
            '-o', $outputPath,
            '-f', 'pdf',
            '-P',
            'id_colaborador=' . $validated['id_colaborador'],
            'dta_inicio=' . $validated['dta_inicio'],
            'dta_fim=' . $validated['dta_fim'],
            '-t', 'generic',
            '--db-driver', 'com.mysql.cj.jdbc.Driver',
            '--db-url', $dbUrl,
            '-u', env('DB_USERNAME', 'root'),
        ];
        if (env('DB_PASSWORD')) {
            $args[] = '-p';
            $args[] = env('DB_PASSWORD');
        }

        $process = new Process($args, $binDir);
        $process->setTimeout(120);
        $process->run();

        if (!$process->isSuccessful() || !file_exists($outputPath . '.pdf')) {
            \Log::error('Falha ao gerar relatório ' . $report . ': ' . $process->getOutput() . $process->getErrorOutput());

            return redirect()->back()->with('error', 'Não foi possível gerar o relatório. Verifique os parâmetros e tente novamente.');
        }

        return Response::make(file_get_contents($outputPath . '.pdf'), 200, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $report . '.pdf"',
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
