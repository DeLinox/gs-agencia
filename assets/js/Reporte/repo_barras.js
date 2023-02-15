$(document).ready(function(){
	$('form').submit(function(e){
		e.preventDefault();
		llenarGrafico($(this));
	});
	$('select[name="detalles"]').select2();
	$('.informeDeudas').click(function(){
        $(location).attr('href',baseurl+"Reporte/excel_informeDeudas?search="+$('input[name="search"]').val()+"&desde="+$('input[name="desde"]').val()+"&hasta="+$('input[name="hasta"]').val());
    })
	$('.actualiza').on("change", function(){
		$.ajax({
            type: "POST",
            dataType: "json",
            url: baseurl + "Reporte/getSeleccion",
			data: $('form').serialize(),
            success: function(data) {
                $('select[name="detalles"]').html(data.options);
            }
        });
	})
    
	/*
	var start = moment().subtract(7, 'days');
    var end = moment();

    function cb(start, end) {
        $('#reportrange span').html(start.format('DD/MM/YYYY') + ' - ' + end.format('DD/MM/YYYY'));
        $('#desde').val(start.format('YYYY-MM-DD'));
        $('#hasta').val(end.format('YYYY-MM-DD'));
        if (typeof($table) != 'undefined') $table.draw();
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
            'Ayer': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            'Ultimos 7 Días': [moment().subtract(6, 'days'), moment()],
            'Ultimos 30 Días': [moment().subtract(29, 'days'), moment()],
            'Este Mes': [moment().startOf('month'), moment().endOf('month')],
            'Anterior Mes': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
            'Este Año': [moment().startOf('year'), moment().endOf('year')],
            'Año Anterior': [moment().subtract(1, "y").startOf("year"), moment().subtract(1, "y").endOf("year")],
        },
        "linkedCalendars": false,
        "showCustomRangeLabel": false,
        "alwaysShowCalendars": true
    }, cb);

    cb(start, end);
	*/
	asignar_seleccion();
})


function asignar_seleccion(){
	$.ajax({
		type: "POST",
		dataType: "json",
		url: baseurl + "Reporte/getSeleccion",
		data: $('form').serialize(),
		success: function(data) {
			$('select[name="detalles"]').html(data.options);
			llenarGrafico($('form'));
		}
	});
}
function llenarGrafico($this) {
    var processed_json = new Array();
    $.gs_loader.show();
    $.getJSON($this.attr('action'), $this.serialize(),function(data) {
        // Populate series
        $.gs_loader.hide();
        for (i = 0; i < data.length; i++){
            if(data[i].cantidad > 0)
                processed_json.push([data[i].nombre, parseInt(data[i].cantidad)]);
        }
     
        // draw chart
        
        $('#reporte_grafico').highcharts({
            chart: {
                type: 'column'
            },
            title: {
                text: ''
            },
            subtitle: {
                text: ''
            },
            xAxis: {
                type: 'category',
                labels: {
                    rotation: -90,
                    style: {
                        fontSize: '13px',
                        fontFamily: 'Verdana, sans-serif'
                    }
                }
            },
            yAxis: {
                min: 0,
                title: {
                    text: 'Pax'
                }
            },
            legend: {
                enabled: false
            },
            tooltip: {
                pointFormat: '<b>{point.y} Pax</b>'
            },
            series: [{
                name: 'Population',
                data: processed_json,
                dataLabels: {
                    enabled: true,
                    rotation: -90,
                    color: '#FFFFFF',
                    align: 'right',
                    y: 10, // 10 pixels down from the top
                    style: {
                        fontSize: '13px',
                        fontFamily: 'Verdana, sans-serif'
                    }
                }
            }]
        }); 
    }); 
}
