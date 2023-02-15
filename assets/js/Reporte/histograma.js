$(document).ready(function(){
	$('form').submit(function(e){
		e.preventDefault();
		llenarTabla($(this));
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
	//llenarTabla($('form'));
	asignar_seleccion();
	$('.cuadroIngresos').click(function(){
        $(location).attr('href',baseurl+"Reporte/excel_movimientoTuristas?servicio="+$('input[name="servicios"]').val()+"&mes="+$('select[name="mes"]').val()+"&anio="+$('select[name="anio"]').val());
    })
})
function asignar_seleccion(){
	$.ajax({
		type: "POST",
		dataType: "json",
		url: baseurl + "Reporte/getSeleccion",
		data: $('form').serialize(),
		success: function(data) {
			$('select[name="detalles"]').html(data.options);
			llenarTabla($('form'));
		}
	});
}
function llenarTabla($this) {
	var url = $("#nameurl").val()+'/true';
    var processed_json = new Array();
    
    $.gs_loader.show();
    $.getJSON(url, $this.serialize(),function(data) {
        console.log(data);
        // Populate series
        $.gs_loader.hide();
        
        console.log("total: "+data.length);
        for (i = 0; i < data.length; i++){
            var otro = new Array();
            if(data[i].pax > 0){
                otro["name"] = "("+data[i].pax+") "+data[i].nombre;
                otro["data"] = new Array();

                $.each(data[i].detas, function(idx, elem){
                    otro["data"].push(parseInt(elem));
                })
                processed_json.push(otro);
            }
        }
        console.log(processed_json);
        // draw chart
        
        Highcharts.chart('histograma', {
			title:{
				text:''
			},
            yAxis: {
                title: {
                    text: 'Pax'
                }
            },
            legend: {
                layout: 'vertical',
                align: 'right',
                verticalAlign: 'middle'
            },
        
            plotOptions: {
                series: {
                    label: {
                        connectorAllowed: false
                    },
                    pointStart: 1
                }
            },
        
            series: processed_json,
            responsive: {
                rules: [{
                    condition: {
                        maxWidth: 500
                    },
                    chartOptions: {
                        legend: {
                            layout: 'horizontal',
                            align: 'center',
                            verticalAlign: 'bottom'
                        }
                    }
                }]
            }
        
        });
    }); 
}