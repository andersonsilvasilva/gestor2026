<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\eventos;
use App\Models\User;
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
        $verTudo = Auth::user()->can('view-all-data');

        $atendidoAno = eventos::when(!$verTudo, fn ($q) => $q->where('user_id', $userId))
            ->whereYear('start', date('Y'))
            ->count();

        $atendidoMes = eventos::when(!$verTudo, fn ($q) => $q->where('user_id', $userId))
            ->whereYear('start', date('Y'))
            ->whereMonth('start', date('m'))
            ->count();

        $desde = Carbon::now()->subDays(self::PERIODOS_EM_DIAS['1m'])->startOfDay();
        $ate = Carbon::now()->endOfDay();
        $chartData = $this->buildChartData($verTudo ? null : $userId, $desde, $ate, false);

        $colaboradores = $verTudo
            ? User::where('role', 'rh')->where('ativo', true)->orderBy('name')->get()
            : collect();

        return view('home', array_merge(
            compact('clientes', 'atendidoAno', 'atendidoMes', 'colaboradores', 'verTudo'),
            $chartData
        ));
    }

    /**
     * Endpoint AJAX usado pelo dashboard para trocar o período/colaborador
     * dos gráficos sem recarregar a página.
     *
     * Usuário comum: sempre vê só os próprios dados.
     * Administrador: pode ver todos (padrão) ou filtrar por um colaborador
     * específico via `colaborador_id`, e pode usar um período pronto
     * (`periodo`) ou um intervalo de datas customizado
     * (`data_inicio` + `data_fim`).
     */
    public function chartData(Request $request)
    {
        $verTudo = Auth::user()->can('view-all-data');

        $validated = $request->validate([
            'periodo'        => 'nullable|string|in:' . implode(',', array_keys(self::PERIODOS_EM_DIAS)),
            'data_inicio'    => 'nullable|date|required_with:data_fim',
            'data_fim'       => 'nullable|date|after_or_equal:data_inicio|required_with:data_inicio',
            'colaborador_id' => 'nullable|integer|exists:users,id',
        ]);

        if ($verTudo) {
            $userId = !empty($validated['colaborador_id']) ? (int) $validated['colaborador_id'] : null;
        } else {
            $userId = Auth::user()->id;
        }

        if (!empty($validated['data_inicio'])) {
            $desde = Carbon::parse($validated['data_inicio'])->startOfDay();
            $ate = Carbon::parse($validated['data_fim'])->endOfDay();
        } else {
            $dias = self::PERIODOS_EM_DIAS[$validated['periodo'] ?? '1m'] ?? self::PERIODOS_EM_DIAS['1m'];
            $desde = Carbon::now()->subDays($dias)->startOfDay();
            $ate = Carbon::now()->endOfDay();
        }

        $porMes = $desde->diffInDays($ate) > 31;

        return response()->json($this->buildChartData($userId, $desde, $ate, $porMes));
    }

    private function buildChartData(?int $userId, Carbon $desde, Carbon $ate, bool $porMes): array
    {
        $formatoSql = $porMes ? '%Y-%m-01' : '%Y-%m-%d';

        $atendimentos = eventos::select([
                DB::raw("DATE_FORMAT(start, '{$formatoSql}') as periodo_agrupado"),
                DB::raw('COUNT(*) as total'),
            ])
            ->whereBetween('start', [$desde, $ate])
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
            ->whereBetween('eventos.start', [$desde, $ate])
            ->whereNull('eventos.deleted_at')
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
