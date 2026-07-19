<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <!-- resources -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css" rel="stylesheet">
        <!-- favicon -->
        <link rel="icon" href="{{ asset('assets/images/favicon.png') }}" type="image/png">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <!-- resources -->
        <link rel="stylesheet" href="{{ asset('assets/bootstrap/bootstrap.min.css') }}">
        <link rel="stylesheet" href="{{ asset('assets/fontawesome/css/all.min.css') }}">
        <link rel="stylesheet" href="{{ asset('assets/datatables/datatables.min.css') }}">
        <link rel="stylesheet" href="https://cdn.datatables.net/v/dt/dt-2.1.8/datatables.min.css" >
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.1/css/bootstrap-select.css" />
        <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
        <script type="text/javascript" src="{{ asset('assets/js/jquery/jquery.mask.min.js')}}"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script src="https://momentjs.com/downloads/moment.js"></script>


        <!-- custom -->
        
        <link rel="stylesheet" href="{{ asset('assets/css/main.css') }}"> 
        {{--@vite(['resources/sass/app.scss', 'resources/js/app.js'])--}}
   
        <title>{{ env('APP_NAME')}} @isset($pageTitle) - {{ $pageTitle }} @endisset</title>
      
    </head>

    <body>
        <x-user-bar />

        <div class="d-flex pt-2">
            <div class="col-0">
                <x-side-bar/>
            </div>
            <div class="col-10">
                {{ $slot }}
            </div>
            
        </div-d-flex>
    </body>

</html>