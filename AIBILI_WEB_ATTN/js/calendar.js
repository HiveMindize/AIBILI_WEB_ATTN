// showCalendar
// configura uma instância de FullCalendar com as ausências dadas
// argumentos: mapa_ferias: array: requerimentos de ferias aprovados
//             mapa_ausencias: array: requerimentos de ausencia aprovados
function showCalendar(mapa_ferias, mapa_ausencias, holidays) {

    var _events = [];
    var _title, _start, _end;

    console.log(holidays);
                
    for (var i = 0; i < mapa_ferias.length; i++) {

        pushEvent(_events, mapa_ferias[i], true);
    }

    for (var i = 0; i < mapa_ausencias.length; i++) {

        pushEvent(_events, mapa_ausencias[i], false);
    }

    Object.keys(holidays).forEach(function(key, index) {


        console.log(holidays[key]["translations"]["pt_PT"], holidays[key]["date"]);
        _events.push({
                        title: holidays[key]["translations"]["pt_PT"],
                        start: holidays[key]["date"],
                        color: "red"
                    });
    });

    $(function() {
        $('#calendar').fullCalendar({
            defaultView: 'month',
            weekends: false,
            locale: 'pt',
            timezone: false,

            events: _events
        })
    });
}

// pushEvent
// adiciona um evento, no formato de array suportado pelo FullCalendar
// argumentos: array: array de eventos
//             event: array: evento a adicionar (colaborador, inicio e fim)
//             vacation: booleano: se é um evento de ausencia ou ferias
function pushEvent(array, event, vacation) {

    if (vacation) {

        description = " férias";
    }

    else {

        description = " ausência";
    }

    _title = event.colaborador  + description;
    _start = event.inicio;
    _end = event.fim;
    _observacoes = event.observacoes;
    array.push({
                    title : _title, 
                    start : _start, 
                    end : _end
               });
}