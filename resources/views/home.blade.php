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
              <div class="row container ">
                  <section class="graficos col s12 m6" >            
                    <div class="grafico card z-depth-4">
                        <h5 class="center"> Total atendimentos - Mês/Ano</h5>
                        <canvas id="myChart" width="400" height="200"></canvas>
                    </div>           
                  </section> 
                  
                  <section class="graficos col s12 m6">            
                      <div class="grafico card z-depth-4">
                          <h5 class="center"> Total procedimentos no Ano </h5>
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


    <Script>
          /* Gráfico 01 */
          var ctx = document.getElementById('myChart');
          var myChart = new Chart(ctx, {

              type: 'bar',
              data: {
                  labels: @json($clienteNomeMes),
                  datasets: [{
                      label: 'Atendimentos' ,
                      data: @json($clienteTotal),
                      backgroundColor: [
                          'rgba(255, 206, 86, 1)',
                          'rgba(255, 99, 36, 1)',
                          'rgba(54, 162, 235, 1)',                       
                          'rgba(255, 159, 184, 1)',
                          'rgba(255, 99, 132, 1)',
                          'rgba(54, 162, 235, 1)', 
                          'rgba(193, 150, 25, 1)',
                          'rgba(124, 162, 35, 1)',
                          'rgba(154, 62, 235, 1)',                        
                          'rgba(255, 159, 64, 1)',
                          'rgba(155, 99, 132, 1)',
                          'rgba(54, 162, 235, 1)' 
                      ],
                      borderColor: [
                          'rgba(255, 206, 255, 1)',
                      ],
                    borderWidth: 1, 
                  }]
              },
              options: {
                  scales: {
                      yAxes: [{
                          ticks: {
                              beginAtZero: true
                          }
                      }]
                  }
              }
          });

          /* Gráfico 02 */
          var ctx = document.getElementById('myChart2');
          var myChart = new Chart(ctx, {
              type: 'pie',
              data: {
                  labels: @json($ProcedimentoAno),
                  datasets: [{
                      label: 'Tipos de Procedimentos no 2024',
                      data: @json($ProcediementoTotal),
                      backgroundColor: [
                          'rgba(255, 99, 132)',
                          'rgba(54, 162, 235)', 
                          'rgba(93, 150, 25)',
                          'rgba(24, 162, 35)',
                          'rgba(154, 62, 235)',                        
                          'rgba(255, 159, 64)'
                      ]
                  }]
              }
          });
    </Script>
 
</x-layout-app>