$(document).ready(function(){
	$('form').submit(function(e){
		e.preventDefault();
		llenarTabla($(this));
	});
	llenarTabla($('form'));
	
	$('.cuadroIngresos').click(function(){
        $(location).attr('href',baseurl+"Reporte/excel_movimientoTuristas?servicio="+$('input[name="servicios"]').val()+"&mes="+$('select[name="mes"]').val()+"&anio="+$('select[name="anio"]').val());
    })

    number_format = function (number, decimals, dec_point, thousands_sep) {
        number = parseFloat(number);
        number = number.toFixed(decimals);

        var nstr = number.toString();
        nstr += '';
        x = nstr.split('.');
        x1 = x[0];
        x2 = x.length > 1 ? dec_point + x[1] : '';
        var rgx = /(\d+)(\d{3})/;

        while (rgx.test(x1))
            x1 = x1.replace(rgx, '$1' + thousands_sep + '$2');
        return x1 + x2;
    }
})
function llenarTabla($this) {
	var url = $("#nameurl").val()+'/true';
    var processed_json = new Array();
    
    $.gs_loader.show();
    $.getJSON(url, $this.serialize(),function(data) {
        console.log(data);
        $.gs_loader.hide();

        $.each(data.detalles, function(idx, elem){
            if(elem.porcent > 0) {
                processed_json.push({"name":elem.tipo_denom, 
                                        "y":parseFloat(elem.porcent),
                                        "tItem":number_format(elem.total, 2, '.', ' ')
                                    });
            }
        })        
        

        console.log(processed_json);
        /*
        for (i = 0; i < data.detalles.length; i++){
            var otro = new Array();
            if(data[i].pax > 0){
                otro["name"] = data[i]["tipo_denom"].nombre;
                otro["y"] = new Array();

                $.each(data[i].detalles, function(idx, elem){
                    otro["data"].push(parseInt(elem));
                })
                processed_json.push(otro);
            }
        }
        */
        if(data.total > 0){
            Highcharts.chart('informe_gastos', {
                chart: {
                    plotBackgroundColor: null,
                    plotBorderWidth: null,
                    plotShadow: false,
                    type: 'pie'
                },
                title: {
                    text: ''
                },
                tooltip: {
                    pointFormat: '<b>{point.percentage:.1f}% ({point.tItem})</b>'
                },
                plotOptions: {
                    pie: {
                        allowPointSelect: true,
                        cursor: 'pointer',
                        dataLabels: {
                            enabled: true,
                            format: '<b>{point.name}</b>: {point.percentage:.1f} %',
                            style: {
                                color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
                            }
                        }
                    }
                },
                series: [{
                    name: 'brands',
                    colorByPoint: true,
                    data: processed_json
                }]
            });
        }else{
            alert("no hay datos para mostrar");
        }
    }); 
}