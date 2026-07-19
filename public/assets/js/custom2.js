$(document).ready(function() {
    CKEDITOR.replace("descricao");
})


//$(document).ready(function() {
document.addEventListener('DOMContentLoaded', function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    var calendarEl=document.getElementById('calendar');
    var events =[];
    var calendar=new FullCalendar.Calendar(calendarEl,{
        themeSystem: 'bootstrap5',
        minTime: '07:00:00',
        maxTime: '17:00:00',
        headerToolbar:{
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay,listMonth'
        },
        locale: 'pt-br',
        initialView: 'timeGridWeek',
        timeZone: 'UTC-4',
        events: '/events',
        navLinks: true,
        editable: true,
        selectable: true,
        selectMirror: true,
        dayMaxEvents: true,
        eventClick: function (info) {
            abrirVisual(info);
        },
        dateClick: function (info) {
            abrirModal(info);
        },
        select: function(info) {
            $('#cmodal').modal('toggle');

        $('#saveBtn').click(function() {
            var eventId         = info.event.id;
            var newStartDate    = info.event.start;
            var newEndDate      = info.event.end || newStartDate;
            var newStartDateUTC = newStartDate.toISOString().slice(0, 20);
            var newEndDateUTC   = newEndDate.toISOString().slice(0, 20);
            var start_date      =  moment(newStartDateUTC).format('YYYY-MM-DD HH:mm');
            var end_date        =  moment(newEndDateUTC).format('YYYY-MM-DD HH:mm');
            var title           =  $('#title').val();
            var client_id       =  client_id;
            var user_id         =  user_id;
            var descricao       =  CKEDITOR.instances.descricao.getDate();
            var color           =  color;
            var procediments_id =  procediments_id;


            $.ajax({
                url:"{{ route('agenda.store') }}",
                type:"POST",
                dataType:'json',
                headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data:{ title, start_date, end_date, client_id, procediments_id, user_id, descricao, color

                    },
                success:function(response)
                {
                        $('#cModal').modal('hide')

                        $('#calendar').fullCalendar('renderEvent', {
                            'client_id'         :response.client_id,
                            'procediments_id'   :response.procediments_id,
                            'user_id'           :response.user_id,
                            'title'             :response.title,
                            'start'             :response.start_date,
                            'end'               :response.end_date,
                            'descricao'         :response.descricao,
                            'color'             :response.color
                        });
                        Swal.fire({
                            position: "top-end",
                            icon: "success",
                            title: "Evento movido com sucesso.",
                            showConfirmButton: false,
                            timer: 1500
                        });
                        console.log('Evento criado com sucesso.');

                },
                error:function(error)
                {
                        if(error.responseJSON.errors) {
                            $('#titleError').html(error.responseJSON.errors.title);
                        }
                        Swal.fire({
                              position: "top-end",
                              icon: "error",
                              title: "Erro ao cadastrar o evento.",
                              showConfirmButton: false,
                              timer: 1500

                        });
                        console.error('Erro ao criado o evento.');
                },
            });
        });
        },
        eventContent: function(info) {
                var eventTitle = nomeShort(info.event.title);
                var labelColor= info.event.color || '#FFFF00';
                var newStartDate = info.event.start;
                var newStartDateUTC = newStartDate.toISOString().slice(11, 16);
                var eventElement = document.createElement('div');
                eventElement.innerHTML = '<span style="background-color:"'+labelColor+'cursor: pointer>✖️</span> '+newStartDateUTC+' - '+ eventTitle;

                eventElement.querySelector('span').addEventListener('click', function() {
                if (confirm("Você tem certeza que deseja apagar este evento?")) {
                    var eventId = info.event.id;
                    $.ajax({
                        method: 'get',
                        url: '/agenda/delete/'+eventId,
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            Swal.fire({
                                  position: "top-end",
                                  icon: "success",
                                  title: "Evento deletado com sucesso.",
                                  showConfirmButton: false,
                                  timer: 1500

                            });
                            console.log('Evento deletado com sucesso.');
                            calendar.refetchEvents(); // Refresh events after deletion
                        },
                        error: function(error) {
                            Swal.fire({
                                  position: "top-end",
                                  icon: "error",
                                  title: "Erro ao deletar o evento.",
                                  showConfirmButton: false,
                                  timer: 1500

                            });
                            console.error('Erro ao apagar o evento:', error);
                        }
                    });
                }
            });
            return {
                domNodes: [eventElement]
            };

        },
        eventDrop: function(info) {
            //moverEvento(info);
            var eventId         = info.event.id;
            var newStartDate    = info.event.start;
            var newEndDate      = info.event.end || newStartDate;
            var newStartDateUTC = newStartDate.toISOString().slice(0, 20);
            var newEndDateUTC   = newEndDate.toISOString().slice(0, 20);
            $.ajax({
                method: 'post',
                url: '/agenda/mover/'+eventId,
                headers: {
                   'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                   '_token': "{{ csrf_token() }}",
                   start_date: newStartDateUTC,
                   end_date: newEndDateUTC
                },
                success:function(response) {
                    calendar.refetchEvents(); // Refresh events after mover
                    Swal.fire({
                       position: "top-end",
                       icon: "success",
                       title: "Evento movido com sucesso.",
                       showConfirmButton: false,
                       timer: 1500

                    });
                    console.log('Evento movido com sucesso.');
                },
                error:function(error) {
                    Swal.fire({
                       position: "top-end",
                       icon: "error",
                       title: "Erro ao mover o evento.",
                       showConfirmButton: false,
                       timer: 1500
                    });

                   console.error('Erro ao mover o evento:', error);
                }
            });
        },
        eventResize: function(info) {
            moverEventoTotal(info);
        },
    });
    //redenderizar o calendario
    calendar.render();

    //Aqui começa as rotinas de controle da Agenda
    const modal = document.querySelector('cmodal');

    //Rotina para Abrir o Modal
    const abrirModal = (info)=>{
        if (modal.classList.contains('hidden')) {
            modal.classList.remove('hidden');

            modal.style.transition = 'opacity 300ms';

            setTimeout(() => modal.style.opacity = 1, 100);
        }
        document.querySelector('#start').value = info.dateStr + " 07:00";
        document.querySelector('#end').value   = info.dateStr + " 17:00";
    }

    //Rotina para Visualizar os dados do Evento
    const abrirVisual = (info) => {
        //recebe o seletor da janela modal
        info.el.style.borderColor = 'red';
        const VisualizarModal = new bootstrap.Modal(document.getElementById("vModal"));
        //Enviar para a janela modal os dados
        document.getElementById('visualizar_codigo').innerText = info.event.extendedProps.codigo;
        document.getElementById('visualizar_nome').innerText = info.event.extendedProps.nomecliente;
        document.getElementById('visualizar_start').innerText = info.event.start.toLocaleString("pt-Br",{
                                                                                        dateStyle: "short",
                                                                                        timeStyle: "short",
                                                                                        timeZone: "UTC"
                                                                                            });
        document.getElementById('visualizar_end').innerText = info.event.end.toLocaleString("pt-Br",{
                                                                                        dateStyle: "short",
                                                                                        timeStyle: "short",
                                                                                        timeZone: "UTC"
                                                                                            });
        document.getElementById('visualizar_terapeuta').innerText = info.event.extendedProps.terapeuta;
        document.getElementById('visualizar_procedimento').innerText = info.event.extendedProps.procedimento;
        document.getElementById('visualizar_descricao').innerHTML = info.event.extendedProps.descricao;             //Alterar para o InnerHTMLtext
        document.getElementById('visualizar_telefone').innerText = info.event.extendedProps.telefone;

        //abrir a janela modal Visualizar
        VisualizarModal.show();
    }
    /*
    //Rotina para Criar Evento
    const criarEvento = (info)=>{
        $('#cmodal').modal('toggle');

        $('#saveBtn').click(function() {
            var eventId         = info.event.id;
            var newStartDate    = info.event.start;
            var newEndDate      = info.event.end || newStartDate;
            var newStartDateUTC = newStartDate.toISOString().slice(0, 20);
            var newEndDateUTC   = newEndDate.toISOString().slice(0, 20);
            var start_date      =  moment(newStartDateUTC).format('YYYY-MM-DD HH:mm');
            var end_date        =  moment(newEndDateUTC).format('YYYY-MM-DD HH:mm');
            var title           =  $('#title').val();
            var client_id       =  client_id;
            var user_id         =  user_id;
            var descricao       =  CKEDITOR.instances.descricao.getDate();
            var color           =  color;
            var procediments_id =  procediments_id;


            $.ajax({
                url:"{{ route('agenda.store') }}",
                type:"POST",
                dataType:'json',
                headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data:{ title, start_date, end_date, client_id, procediments_id, user_id, descricao, color

                    },
                success:function(response)
                {
                        Swal.fire({
                            position: "top-end",
                            icon: "success",
                            title: "Evento movido com sucesso.",
                            showConfirmButton: false,
                            timer: 2500
                        });
                        console.log('Evento criado com sucesso.');
                        $('#cModal').modal('hide')

                        $('#calendar').fullCalendar('renderEvent', {
                            'client_id'         :response.client_id,
                            'procediments_id'   :response.procediments_id,
                            'user_id'           :response.user_id,
                            'title'             :response.title,
                            'start'             :response.start_date,
                            'end'               :response.end_date,
                            'descricao'         :response.descricao,
                            'color'             :response.color
                        });


                },
                error:function(error)
                {
                        if(error.responseJSON.errors) {
                            $('#titleError').html(error.responseJSON.errors.title);
                        }
                        Swal.fire({
                              position: "top-end",
                              icon: "error",
                              title: "Erro ao cadastrar o evento.",
                              showConfirmButton: false,
                              timer: 1500

                        });
                        console.error('Erro ao criado o evento.');
                },
            });
        });
    }
    */
    /*
    //Rotina para mover o evento
    const moverEvento = (info) => {
        var eventId         = info.event.id;
        var newStartDate    = info.event.start;
        var newEndDate      = info.event.end || newStartDate;
        var newStartDateUTC = newStartDate.toISOString().slice(0, 20);
        var newEndDateUTC   = newEndDate.toISOString().slice(0, 20);
        $.ajax({
            method: 'POST',
            url: `/agenda/mover/${eventId}`,
            data: {
                '_token': "{{ csrf_token() }}",
                start_date  : newStartDateUTC,
                end_date    : newEndDateUTC,
            },
            success:function() {
                Swal.fire({
                   position: "top-end",
                   icon: "success",
                   title: "Evento movido com sucesso.",
                   showConfirmButton: false,
                   timer: 1500

                });
                console.log('Evento movido com sucesso.');
            },
            error:function(error) {
                Swal.fire({
                   position: "top-end",
                   icon: "error",
                   title: "Erro ao mover o evento.",
                   showConfirmButton: false,
                   timer: 1500
                });

               console.error('Erro ao mover o evento:', error);
            }
        });
    }
    */

    //Rotina para o mover total o evento
    const moverEventoTotal = (info) => {
        var eventId         = info.event.id;
        var newStartDate    = info.event.start;
        var newEndDate      = info.event.end || newStartDate;
        var newStartDateUTC = newStartDate.toISOString().slice(0, 20);
        var newEndDateUTC   = newEndDate.toISOString().slice(0, 20);
        $.ajax({
            method: 'post',
            url: '/agenda/mover/'+eventId,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                '_token': "{{ csrf_token() }}",
                start_date: newStartDateUTC,
                end_date: newEndDateUTC
            },
            success: function(response) {
                Swal.fire({
                   position: "top-end",
                   icon: "success",
                   title: "Evento movido com sucesso.",
                   showConfirmButton: false,
                   timer: 1500

                });
                console.log('Evento movido com sucesso.');
            },
            error: function(error) {
                Swal.fire({
                    position: "top-end",
                    icon: "error",
                    title: "Erro ao mover o evento.",
                    showConfirmButton: false,
                    timer: 1500
                });
                console.error('Erro ao mover o evento:', error);

            }
        });
    }
    //Função do Botão pesquisar terapeuta
    document.getElementById('searchButton').addEventListener('click', function() {
        var searchKeywords = document.getElementById('searchInput').value;
        filterAndDisplayEvents(searchKeywords);
    });
    function filterAndDisplayEvents(searchKeywords) {
       $.ajax({
           method: 'GET',
           url: '/agenda/search/${searchKeywords}',
           success: function(response) {
               calendar.removeAllEvents();
               calendar.addEventSource(response);
           },
           error: function(error) {
                Swal.fire({
                   position: "top-end",
                   icon: "error",
                   title: "Erro pesquisando o evento.",
                   showConfirmButton: false,
                   timer: 1500
                });
               console.error('Erro pesquisando eventos:', error);
           }
        });
    }

    //Função para encurtar o nome do cliente
    function nomeShort(nameShort) {
        var nameShort = nameShort.split(' ');
            nameShort = nameShort.slice(0,1) + ' ' + nameShort.slice(-1);
        return nameShort;
    }

    //Função para converter a string em um objeto Date
    function converterData(data) {
            // Converter a string em um objeto Date
            const dataObj = new Date(data);
            // Extrair o ano da data
            const ano = dataObj.getFullYear();
            // Obter o mês, mês começa de 0, padStart adiciona zeros à esquerda para garantir que o mês tenha dígitos
            const mes = String(dataObj.getMonth()).padStart(2, '0');
            // Obter o dia do mês, padStart adiciona zeros à esquerda para garantir que o dia tenha dois dígitos
            const dia = String(dataObj.getDate() + 1).padStart(2, '0');
            // Obter a hora, padStart adiciona zeros à esquerda para garantir que a hora tenha dois dígitos
            const hora = String(dataObj.getHours() + 1).padStart(2, '0');
            // Obter minuto, padStart adiciona zeros à esquerda para garantir que o minuto tenha dois dígitos
            const minuto = String(dataObj.getMinutes()).padStart(2, '0');
            // Retornar a data
            return `${ano}-${mes}-${dia} ${hora}:${minuto}`;
    }

});
