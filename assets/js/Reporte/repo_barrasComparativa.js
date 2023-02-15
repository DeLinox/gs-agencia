$(document).ready(function(){
	$('form').submit(function(e){
		e.preventDefault();
		llenarGrafico($(this));
	});
	
	$('select[name="detalles"]').select2();
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
	
	$('.informeDeudas').click(function(){
        $(location).attr('href',baseurl+"Reporte/excel_informeDeudas?search="+$('input[name="search"]').val()+"&desde="+$('input[name="desde"]').val()+"&hasta="+$('input[name="hasta"]').val());
    })
    
    //llenarGrafico($('form'));
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
        $.gs_loader.hide();
        $('#table-content').find("tbody").html(data.table);
		$('#table-total').find("tbody").html(data.total);

        Highcharts.chart('reporte_grafico', {
            data: {
                table: 'table-content'
            },
            chart: {
                type: 'column'
            },
            title: {
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
                allowDecimals: false,
                title: {
                    text: 'Pax'
                }
            },
            tooltip: {
                formatter: function () {
                    return '<b>' + this.series.name + '</b><br/>' +
                        this.point.y + ' ' + this.point.name.toLowerCase();
                }
            },
            
        });
     
    }); 
}
