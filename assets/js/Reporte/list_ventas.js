var url = "";
var baseurl;
$(document).on('ready', function () {
    baseurl = $("#baseurl").val();

    var url = $("#nameurl").val()+'?json=true';
    var $table;
    var seleccionados;
    var procesados = 0;
    var mensajes = new Array();
    var tipo_envio = 0;

    function botones(id,estado,mail,pagado,$ar){

    }

    var $dt = $('#mitabla'),
            conf = {
                data_source: url,
                cactions: ".ocform",
                order: [[1, "desc"]],
                oncheck: function (row, data, selected) {
                    if (selected.length > 0) {
                        $('.onsel').removeClass('hidden');
                        $('.nosel').addClass('hidden');
                    } else {
                        $('.onsel').addClass('hidden');
                        $('.nosel').removeClass('hidden');
                    }
                    seleccionados = selected;
                },
                onrow: function (row, data) {
                    
                }
            };
	   var $this;
    var $dlg;
    
    $('.ocform').submit(function () {
        $table.draw();
        totales();
        return false;
    })

    $('.ocform input,.ocform select').change(function () {
        $table.draw();
        totales();
        return false;
    })
    
    totales();
    var start = moment().subtract(29, 'days');
    var end = moment();

    function cb(start, end) {
        $('#reportrange span').html(start.format('DD/MM/YYYY') + ' - ' + end.format('DD/MM/YYYY'));
        $('#desde').val(start.format('YYYY-MM-DD'));
        $('#hasta').val(end.format('YYYY-MM-DD'));
        if(typeof($table)!='undefined'){
            $table.draw();  
            totales();
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
    var buton = "";
    $table = $dt.load_simpleTable(conf, true,buton);
	
	$('#btn-report-excel').on('click',function(){
		//$(location).attr('href',baseurl+"Ventat/reporte_excel/"+$('#desde').val()+"/"+$('#hasta').val()+"/"+$('#comprobantes').val()+"/"+$('#moneda').val();
		window.location.href = baseurl+"Reporte/reporte_ventas_general/?desde="+$('#desde').val()+"&hasta="+$('#hasta').val()+"&comprobantes="+$('#comprobantes').val()+"&moneda="+$('#moneda').val()+"&estado="+$('#estado').val()+"&search="+$('#filtro').val();
		
	})

});
function totales(){
	form = $(".ocform");
	 $.ajax({
	        dataType: "json",
	        method: "POST",
	        url: baseurl + "Reporte/getVentas",
	        data: form.serialize(),
	        success: function(resp){
	        	console.log(resp);
	            $('#tbl-resumen').find('tbody').html(resp.html);
	        }
	    });
}