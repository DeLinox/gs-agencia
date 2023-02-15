$(document).ready(function(){
	$('form').submit(function(e){
		e.preventDefault();
		llenarTabla($(this));
	});
	
	$('.informeDeudas').click(function(){
        $(location).attr('href',baseurl+"Reporte/excel_informeDeudas?search="+$('input[name="search"]').val()+"&desde="+$('input[name="desde"]').val()+"&hasta="+$('input[name="hasta"]').val());
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
	
	llenarTabla($('form'));
})



function llenarTabla($this) {
	var url = $("#nameurl").val()+'/true';
	$.ajax({
        dataType: "json",
        url: url,
        type: "POST",
        data: $this.serialize(),
        success: function(resp){
            $.each(resp, function( i, value ) {
                $html = "";
                $html += '';
                $.each(value, function( j, price ) {
                    if(j != "res"){
                        $html2 = "<table class='table table-striped table-bordered'>";
                        $.each(price.clies, function( k, clie ) {
                            $html2 += '<tr>';
                            $html2 += '<td class="col-sm-6">'+clie.cliente+'</td>';
                            $html2 += '<td class="text-right col-sm-1">'+number_format(clie.s_cobrado, 2, '.', ' ')+'</td>';
                            $html2 += '<td class="text-right col-sm-1">'+number_format(clie.s_saldo, 2, '.', ' ')+'</td>';
                            $html2 += '<td class="text-right col-sm-1">'+number_format(clie.s_total, 2, '.', ' ')+'</td>';
                            $html2 += '<td class="text-right col-sm-1">'+number_format(clie.d_cobrado, 2, '.', ' ')+'</td>';
                            $html2 += '<td class="text-right col-sm-1">'+number_format(clie.d_saldo, 2, '.', ' ')+'</td>';
                            $html2 += '<td class="text-right col-sm-1">'+number_format(clie.d_total, 2, '.', ' ')+'</td>';
                            $html2 += '</tr>';    
                        });
                        $html2 += "</table>";
                        $html += '<div class="panel-heading"><a data-toggle="collapse" href="#'+i+j+'" class="link-panel">';
                                $html += '<table class="table" id="tbl'+i+j+'"><tr>';
                                        $html += '<th class="col-sm-6">'+j+'</th>';
                                        $html += '<th class="text-center mone col-sm-1"><span class="s_cobrado">'+number_format(price.s_cobrado, 2, '.', ' ')+'</span></th>';
                                        $html += '<th class="text-center mone col-sm-1"><span class="s_saldo">'+number_format(price.s_saldo, 2, '.', ' ')+'</span></th>';
                                        $html += '<th class="text-center mone col-sm-1"><span class="s_total">'+number_format(price.s_total, 2, '.', ' ')+'</span></th>';
                                        $html += '<th class="text-center mone col-sm-1"><span class="d_cobrado">'+number_format(price.d_cobrado, 2, '.', ' ')+'</span></th>';
                                        $html += '<th class="text-center mone col-sm-1"><span class="d_saldo">'+number_format(price.d_saldo, 2, '.', ' ')+'</span></th>';
                                        $html += '<th class="text-center mone col-sm-1"><span class="d_total">'+number_format(price.d_total, 2, '.', ' ')+'</span></th>';
                                    $html += '</tr></table>';
                            $html += '</a></div><div id="'+i+j+'" class="panel-collapse collapse">'+$html2+'</div>';
                        
                    }else{
                        $.each(price, function( k, add ) {
                            $('#tbl-'+i).find('.'+k).text(number_format(add, 2, '.', ' '));
                        });
                    }
                });
                $html += '';
                $('#'+i).html($html);
            });
        }
    })
}
