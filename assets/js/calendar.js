$(document).on('ready', function() {
    var start = moment($('#desde').val());
    var end = moment($('#hasta').val());//moment().subtract(-7, 'days');

    function cb(start, end) {
        $('#reportrange span').html(start.format('DD/MM/YYYY') + ' - ' + end.format('DD/MM/YYYY'));
        $('#desde').val(start.format('YYYY-MM-DD'));
        $('#hasta').val(end.format('YYYY-MM-DD'));
        change_date();
        if (typeof($table) != 'undefined'){
            $table.draw();
        } 
    }
    $('#reportrange').daterangepicker({
        startDate: start,
        endDate: end,
        
        "opens": "right",
        "autoApply": true,
        locale: {
            format: 'DD/MM/YYYY',
            "applyLabel": "Aplicar",
            "cancelLabel": "Canelar",
            "customRangeLabel": "Rango",
            "daysOfWeek": [
                "Do",
                "Lu",
                "Ma",
                "Mi",
                "Ju",
                "Vi",
                "Sa"
            ],
            "monthNames": [
                "Enero",
                "Febrero",
                "Marzo",
                "Abril",
                "Mayo",
                "Junio",
                "Julio",
                "Agosto",
                "Setiembre",
                "Octubre",
                "Noviembre",
                "Diciembre"
            ],
        },
        ranges: {
            'Hoy': [moment(), moment()],
            'Mañana': [moment().subtract(-1, 'days'), moment().subtract(-1, 'days')],
            'Siguientes 7 Días': [moment(), moment().subtract(-6, 'days')],
            'Siguientes 30 Días': [moment(), moment().subtract(-29, 'days')],
            'Este Mes': [moment().startOf('month'), moment().endOf('month')],
            'Siguiente Mes': [moment().subtract(-1, 'month').startOf('month'), moment().subtract(-1, 'month').endOf('month')],
            'Este Año': [moment().startOf('year'), moment().endOf('year')],
            'Año Siguiente': [moment().subtract(-1, "y").startOf("year"), moment().subtract(-1, "y").endOf("year")],
        },
        "linkedCalendars": false,
        "showCustomRangeLabel": false,
        "alwaysShowCalendars": true
    }, cb);

    cb(start, end);
});