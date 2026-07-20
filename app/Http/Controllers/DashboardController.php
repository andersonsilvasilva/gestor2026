<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\eventos;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    private const PERIODOS_EM_DIAS = [
        '7d' => 7,
        '1m' => 30,
        '6m' => 182,
        '1a' => 365,
    ];

    private const MESES_PT = [
        '01' => 'Jan', '02' => 'Fev', '03' => 'Mar', '04' => 'Abr',
        '05' => 'Mai', '06' => 'Jun', '07' => 'Jul', '08' => 'Ago',
        '09' => 'Set', '10' => 'Out', '11' => 'Nov', '12' => 'Dez',
    ];

    public function index()
    {
        $clientes = Client::count();
        $userId = Auth::user()->id;
        $isAdmin = $userId == 1;

        $atendidoAno = eventos::when(!$isAdmin, fn ($q) => $q->where('user_id', $userId))
            ->whereYear('start', date('Y'))
            ->count();

        $atendidoMes = eventos::when(!$isAdmin, fn ($q) => $q->where('user_id', $userId))
            ->whereYear('start', date('Y'))
            ->whereMonth('start', date('m'))
            ->count();

        $chartData = $this->buildChartData($isAdmin ? null : $userId, '1m');

        return view('home', array_merge(
            compact('clientes', 'atendidoAno', 'atendidoMes'),
            $chartData
        ));
    }

    /**
     * Endpoint AJAX usado pelo dashboard para trocar o período dos gráficos
     * sem recarregar a página.
     */
    public function chartData(Request $request)
    {
        $validated = $request->validate([
            'periodo' => 'required|string|in:' . implode(',', array_keys(self::PERIODOS_EM_DIAS)),
        ]);

        $userId = Auth::user()->id;
        $isAdmin = $userId == 1;

        return response()->json($this->buildChartData($isAdmin ? null : $userId, $validated['periodo']));
    }

    private function buildChartData(?int $userId, string $periodo): array
    {
        $dias = self::PERIODOS_EM_DIAS[$periodo] ?? self::PERIODOS_EM_DIAS['1m'];
        $desde = Carbon::now()->subDays($dias)->startOfDay();
        $porMes = $dias > 31;
        $formatoSql = $porMes ? '%Y-%m-01' : '%Y-%m-%d';

        $atendimentos = eventos::select([
                DB::raw("DATE_FORMAT(start, '{$formatoSql}') as periodo_agrupado"),
                DB::raw('COUNT(*) as total'),
            ])
            ->where('start', '>=', $desde)
            ->when($userId, fn ($q) => $q->where('user_id', $userId))
            ->groupBy('periodo_agrupado')
            ->orderBy('periodo_agrupado')
            ->get();

        $atendimentosLabels = [];
        $atendimentosTotais = [];
        foreach ($atendimentos as $linha) {
            $data = Carbon::parse($linha->periodo_agrupado);
            $atendimentosLabels[] = $porMes
                ? self::MESES_PT[$data->format('m')] . '/' . $data->format('Y')
                : $data->format('d/m');
            $atendimentosTotais[] = $linha->total;
        }

        $procedimentos = DB::table('eventos')
            ->join('procediments', 'procediments.id', '=', 'eventos.procediments_id')
            ->where('eventos.status', '1')
            ->where('eventos.start', '>=', $desde)
            ->when($userId, fn ($q) => $q->where('eventos.user_id', $userId))
            ->select('procediments.name', DB::raw('COUNT(*) as total'))
            ->groupBy('procediments.name')
            ->orderByDesc('total')
            ->get();

        return [
            'atendimentosLabels'   => $atendimentosLabels,
            'atendimentosTotais'   => $atendimentosTotais,
            'procedimentosLabels'  => $procedimentos->pluck('name'),
            'procedimentosTotais'  => $procedimentos->pluck('total'),
        ];
    }
}
