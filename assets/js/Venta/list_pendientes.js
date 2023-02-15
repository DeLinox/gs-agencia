var url = "";
$(document).on('ready', function () {
    //baseurl = $("#baseurl").val();
    var url = $("#nameurl").val()+'?json=true';
    var $table;
    var seleccionados;
    var procesados = 0;
    var mensajes = new Array();
    var tipo_envio = 0;

    
    var $dt = $('#tbl-pendientes');

    llenarTabla($('#frm-pend'), $dt);

    $('.ocform').submit(function (e) {
        e.preventDefault();
        llenarTabla($('#frm-pend'), $dt);
        return false;
    })

    $('.ocform input,.ocform select').change(function () {
        llenarTabla($('#frm-pend'), $dt);
        return false;
    })
    
    
    var start = moment().subtract(29, 'days');
    var end = moment();

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
    var buton = "<div class='botones'><input type='checkbox'></div>";
    
	
	$('#btn-report-excel').on('click',function(){
		//$(location).attr('href',baseurl+"Ventat/reporte_excel/"+$('#desde').val()+"/"+$('#hasta').val()+"/"+$('#comprobantes').val()+"/"+$('#moneda').val();
		window.location.href = baseurl+"Venta/reporte_excel_pendientes/?comprobantes="+$('#comprobantes').val()+"&search="+$('#filtro').val();
		
	})
    
    

});
function llenarTabla(form, tabla) {
    $.ajax({
        type: form.attr('method'),
        url: form.attr('action'),
        dataType: "json",
        data: form.serialize(), // Adjuntar los campos del formulario enviado.
        success: function(resp){
           tabla.find('tbody').html(resp.data);
           opciones();
        }
    });
}
function opciones(){
    $('.pagar').click(function(){
        $(this).load_dialog({
            title: 'Realizar pago',
            loaded:function($dlg){
                $dlg.find('form').submit(function(e){
                    e.preventDefault();
                    console.log("trataste de enviar el formulario we");
                    $.ajax({
                        type: $(this).attr('method'),
                        url: $(this).attr('action'),
                        dataType: "json",
                        data: $(this).serialize(), // Adjuntar los campos del formulario enviado.
                        success: function(resp){
                            llenarTabla($('#frm-pend'), $('#tbl-pendientes'));
                           $dlg.find('.close').click();
                        }
                    });
                })
            }
        });
        return false;
    });
}
function met_pago(){
    $('input.fecha').daterangepicker({
        singleDatePicker: true,
        locale: {
            format: 'DD/MM/YYYY'
        }
    });
    $('input[name="saldo"]').dval($('input[name="saldo"]').val());
    $('input[name="pagado"]').keyup(function(){
        changePagado($(this).val());
    });
}
function changePagado(valor) {
    pagado = parseFloat(valor);
    saldo = parseFloat($('input[name="saldo"]').val());
    console.log(pagado+" - "+saldo+" = "+(pagado-saldo));
    if(pagado > saldo){
        $('#vuelto').dval(pagado-saldo);
    }else{
        $('#vuelto').dval('0.00');
    }
}