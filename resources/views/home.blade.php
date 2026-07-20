<x-layout-app page-title="Home">

    <div class="w-100 me-0 p-1">
        <!-- Compiled and minified CSS -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
        <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
        <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">

        <div class="row container">
            <section class="info">

              <div class="col s12 m4">
              <article class="bg-gradient-green card z-depth-4 ">
                <i class="material-icons">person</i>
                <p>Seus Atendimentos/Mês</p>
                <h3> {{$atendidoMes}} </h3>
              </article>
              </div>

              <div class="col s12 m4">
                <article class="bg-gradient-blue card z-depth-4 ">
                  <i class="material-icons">multiline_chart</i>
                  <p>Total de Atendimentos/Ano</p>
                  <h3> {{$atendidoAno}}</h3>
                </article>
                </div>

                <div class="col s12 m4">
                  <article class="bg-gradient-orange card z-depth-4 ">
                    <i class="material-icons">assignment_ind</i>
                    <p>Total de Usuários</p>
                    <h3> {{$clientes}}</h3>
                  </article>
                  </div>

            </section>
          </div>

          <div class="row container">
              <div class="col s12 periodo-selector no-print">
                  <span class="periodo-label">Período:</span>
                  <div class="btn-group" role="group" aria-label="Período do gráfico">
                      <button type="button" class="btn btn-sm btn-outline-primary periodo-btn" data-periodo="7d" data-label="Últimos 7 dias">7 dias</button>
                      <button type="button" class="btn btn-sm btn-outline-primary periodo-btn active" data-periodo="1m" data-label="Último mês">1 mês</button>
                      <button type="button" class="btn btn-sm btn-outline-primary periodo-btn" data-periodo="6m" data-label="Últimos 6 meses">6 meses</button>
                      <button type="button" class="btn btn-sm btn-outline-primary periodo-btn" data-periodo="1a" data-label="Último ano">1 ano</button>
                  </div>
                  <button type="button" id="btnImprimir" class="btn btn-sm btn-outline-dark ms-auto">
                      <i class="material-icons" style="font-size: 1rem; vertical-align: text-bottom;">print</i>
                      Imprimir Relatório
                  </button>
              </div>

              <div class="col s12 print-header d-print-block">
                  <h4>{{ env('APP_NAME') }} — Relatório do Dashboard</h4>
                  <p>
                      Colaborador: <strong>{{ auth()->user()->name }}</strong> &nbsp;|&nbsp;
                      Período: <strong id="print-periodo-label">Último mês</strong> &nbsp;|&nbsp;
                      Gerado em: <strong>{{ now()->format('d/m/Y H:i') }}</strong>
                  </p>
                  <hr>
              </div>
          </div>

              <div class="row container ">
                  <section class="graficos col s12 m6" >
                    <div class="grafico card z-depth-4">
                        <div class="grafico-header">
                            <h5 class="center"> Total de Atendimentos</h5>
                            <div class="btn-group tipo-grafico-group no-print" role="group" aria-label="Tipo do gráfico de atendimentos">
                                <button type="button" class="btn btn-sm btn-outline-secondary tipo-btn active" data-chart="atendimentos" data-tipo="bar">Barra</button>
                                <button type="button" class="btn btn-sm btn-outline-secondary tipo-btn" data-chart="atendimentos" data-tipo="line">Linha</button>
                                <button type="button" class="btn btn-sm btn-outline-secondary tipo-btn" data-chart="atendimentos" data-tipo="pie">Pizza</button>
                            </div>
                        </div>
                        <canvas id="myChart" width="400" height="200"></canvas>
                    </div>
                  </section>

                  <section class="graficos col s12 m6">
                      <div class="grafico card z-depth-4">
                          <div class="grafico-header">
                              <h5 class="center"> Total de Procedimentos </h5>
                              <div class="btn-group tipo-grafico-group no-print" role="group" aria-label="Tipo do gráfico de procedimentos">
                                  <button type="button" class="btn btn-sm btn-outline-secondary tipo-btn" data-chart="procedimentos" data-tipo="bar">Barra</button>
                                  <button type="button" class="btn btn-sm btn-outline-secondary tipo-btn" data-chart="procedimentos" data-tipo="line">Linha</button>
                                  <button type="button" class="btn btn-sm btn-outline-secondary tipo-btn active" data-chart="procedimentos" data-tipo="pie">Pizza</button>
                              </div>
                          </div>
                      <canvas id="myChart2" width="400" height="200"></canvas>
                  </div>
                 </section>
              </div>
          </div>
    </div>
    <!-- Compiled and minified JavaScript -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
    <script src="{{ asset('assets/js/chart/chart.js') }}"></script>
    <script src="{{ asset('assets/js/chart/main.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/js/jquery/jquery.mask.min.js')}}"></script>
    {{--<script src="{{ asset('assets/js/custom.js') }}"></script>--}}

    <style>
        .periodo-selector { margin-bottom: 1rem; display: flex; align-items: center; gap: .5rem; }
        .periodo-label { font-weight: 600; }
        .grafico-header { display: flex; flex-direction: column; align-items: center; gap: .5rem; margin-bottom: .5rem; }
        .tipo-grafico-group .btn { font-size: .75rem; }
        .print-header { display: none; }
        @media print {
            .grafico { box-shadow: none !important; border: 1px solid #ddd; page-break-inside: avoid; }
            .graficos { width: 50% !important; float: left; }
        }
    </style>

    <script>
        const CORES_ATENDIMENTOS = [
            'rgba(255, 206, 86, 1)', 'rgba(255, 99, 36, 1)', 'rgba(54, 162, 235, 1)',
            'rgba(255, 159, 184, 1)', 'rgba(255, 99, 132, 1)', 'rgba(54, 162, 235, 1)',
            'rgba(193, 150, 25, 1)', 'rgba(124, 162, 35, 1)', 'rgba(154, 62, 235, 1)',
            'rgba(255, 159, 64, 1)', 'rgba(155, 99, 132, 1)', 'rgba(54, 162, 235, 1)',
        ];
        const CORES_PROCEDIMENTOS = [
            'rgba(255, 99, 132)', 'rgba(54, 162, 235)', 'rgba(93, 150, 25)',
            'rgba(24, 162, 35)', 'rgba(154, 62, 235)', 'rgba(255, 159, 64)',
            'rgba(255, 206, 86)', 'rgba(75, 192, 192)', 'rgba(153, 102, 255)',
            'rgba(201, 203, 207)',
        ];

        const charts = {
            atendimentos: { instance: null, canvasId: 'myChart', label: 'Atendimentos', cores: CORES_ATENDIMENTOS, tipo: 'bar', labels: @json($atendimentosLabels), dados: @json($atendimentosTotais) },
            procedimentos: { instance: null, canvasId: 'myChart2', label: 'Procedimentos', cores: CORES_PROCEDIMENTOS, tipo: 'pie', labels: @json($procedimentosLabels), dados: @json($procedimentosTotais) },
        };

        function renderChart(chartKey) {
            const cfg = charts[chartKey];
            const ctx = document.getElementById(cfg.canvasId);

            if (cfg.instance) {
                cfg.instance.destroy();
            }

            const options = cfg.tipo === 'pie' ? {} : {
                scales: {
                    yAxes: [{ ticks: { beginAtZero: true } }],
                },
            };

            cfg.instance = new Chart(ctx, {
                type: cfg.tipo,
                data: {
                    labels: cfg.labels,
                    datasets: [{
                        label: cfg.label,
                        data: cfg.dados,
                        backgroundColor: cfg.cores,
                        borderColor: cfg.tipo === 'line' ? 'rgba(54, 162, 235, 1)' : 'rgba(255, 206, 255, 1)',
                        borderWidth: cfg.tipo === 'line' ? 2 : 1,
                        fill: cfg.tipo === 'line' ? false : undefined,
                    }],
                },
                options: options,
            });
        }

        function atualizarPeriodo(periodo) {
            fetch(`{{ route('home.chart-data') }}?periodo=${periodo}`, {
                credentials: 'same-origin',
                headers: { 'Accept': 'application/json' },
            })
                .then(resp => resp.json())
                .then(json => {
                    charts.atendimentos.labels = json.atendimentosLabels;
                    charts.atendimentos.dados = json.atendimentosTotais;
                    charts.procedimentos.labels = json.procedimentosLabels;
                    charts.procedimentos.dados = json.procedimentosTotais;
                    renderChart('atendimentos');
                    renderChart('procedimentos');
                })
                .catch(() => {
                    alert('Não foi possível atualizar os gráficos. Tente novamente.');
                });
        }

        document.querySelectorAll('.periodo-btn').forEach(btn => {
            btn.addEventListener('click', function () {
                document.querySelectorAll('.periodo-btn').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                document.getElementById('print-periodo-label').textContent = this.dataset.label;
                atualizarPeriodo(this.dataset.periodo);
            });
        });

        document.getElementById('btnImprimir').addEventListener('click', function () {
            window.print();
        });

        document.querySelectorAll('.tipo-btn').forEach(btn => {
            btn.addEventListener('click', function () {
                const chartKey = this.dataset.chart;
                document.querySelectorAll(`.tipo-btn[data-chart="${chartKey}"]`).forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                charts[chartKey].tipo = this.dataset.tipo;
                renderChart(chartKey);
            });
        });

        renderChart('atendimentos');
        renderChart('procedimentos');
    </script>

</x-layout-app>
