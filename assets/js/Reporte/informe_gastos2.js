$(document).ready(function(){
    tipo = $('select[name="tipo"]');
    spropio = $('select[name="spropio"]');
    stercero = $('select[name="stercero"]');

    if(tipo.val() == 'PROPIO') spropio.removeClass('hidden');
    else if(tipo.val() == 'TERCERO') stercero.removeClass('hidden');

    tipo.change(function(){
        if($(this).val() == 'PROPIO'){
            spropio.removeClass('hidden');
            stercero.addClass('hidden');
        }else if(tipo.val() == 'TERCERO'){
            stercero.removeClass('hidden');
            spropio.addClass('hidden');
        }else{
            stercero.addClass('hidden');
            spropio.addClass('hidden');
        }
    })

	$('form').submit(function(e){
		$.gs_loader.show();
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

    /*
    var start = moment().subtract(7, 'days');
    var end = moment();
    */
   var start = moment($('#desde').val());
    var end = moment($('#hasta').val());

    function cb(start, end) {
        $('#reportrange span').html(start.format('DD/MM/YYYY') + ' - ' + end.format('DD/MM/YYYY'));
        $('#desde').val(start.format('YYYY-MM-DD'));
        $('#hasta').val(end.format('YYYY-MM-DD'));
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

    var table = $('#cuadro_ingresos').DataTable({
		pageLength: 100,
        dom: 'Bfrtip',
        buttons: [
            {
                extend: 'excelHtml5',
                title: 'Informe de deudas '+$("#desde").val()+" - "+$("#hasta").val(),
                footer: true,
                exportOptions: {
                    columns: [1, 2, 3, 4, 5, 6, 7]
                }
            }
        ],
        columnDefs: [ {
                targets: 0,
                data: null,
                defaultContent: "<a title='Detalles' href='"+baseurl+"Reporte/deta_informeGastos' class='btn btn-primary btn-sm'>Detalles</a>",
            },{
                targets: [ 8 ],
                visible: false,
                searchable: false
            }
        ],
        bFilter: false,
        bInfo: false,
        footerCallback: function ( row, data, start, end, display ) {
            var api = this.api(), data;
 
            // Remove the formatting to get integer data for summation
            var intVal = function ( i ) {
                return typeof i === 'string' ?
                    i.replace(/[\$,]/g, '')*1 :
                    typeof i === 'number' ?
                        i : 0;
            };
 
            // Total over all pages
            total2 = api
                .column( 2 )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );
            total3 = api
                .column( 3 )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );
            total4 = api
                .column( 4 )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );
            total5 = api
                .column( 5 )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );
            total6 = api
                .column( 6 )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );
            total7 = api
                .column( 7 )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );
 
            // Update footer
            $( api.column( 2 ).footer() ).html(
                number_format(total2, 2, '.', ' ')
            );
            $( api.column( 3 ).footer() ).html(
                number_format(total3, 2, '.', ' ')
            );
            $( api.column( 4 ).footer() ).html(
                number_format(total4, 2, '.', ' ')
            );
            $( api.column( 5 ).footer() ).html(
                number_format(total5, 2, '.', ' ')
            );
            $( api.column( 6 ).footer() ).html(
                number_format(total6, 2, '.', ' ')
            );
            $( api.column( 7 ).footer() ).html(
                number_format(total7, 2, '.', ' ')
            );
        }
    });

    $('#cuadro_ingresos tbody').on( 'click', 'a', function (e) {
        //e.preventDefault();
        var data = table.row( $(this).parents('tr') ).data();
        //alert( "ID is: "+ data[ 8 ] );
        var tipo = $('select[name="tipo"]').val();
        var desde = $('#desde').val();
        var hasta = $('#hasta').val();
        var spropio = $('select[name="spropio"]').val();
        var stercero = $('select[name="stercero"]').val();
        var attr = $(this).attr('href');
        $(this).attr('href',attr+"?tipo="+tipo+"&id="+data[ 8 ]+"&desde="+desde+"&hasta="+hasta+"&spropio="+spropio+"&stercero="+stercero);
        $(this).load_dialog({
            title: $(this).attr('title'),
            loaded: function(dlg) {
                
            }
        });
        return false;
    } );
})