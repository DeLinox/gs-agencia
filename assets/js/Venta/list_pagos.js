var baseurl
$(document).on('ready', function () {
    baseurl = $("#baseurl").val();
    var url = baseurl + 'Venta/pagos?json=true';
    var $table;
    var seleccionados;
    var procesados = 0;
    var mensajes = new Array();
    var tipo_envio = 0;
    function botones(id,estado,mail,pagado,$ar){
       espag = pagado;
       html = `<div class='btn-group'>
          <a href='{baseurl}Venta/vercomp/{id}' class='btn btn-success btn-sm vercomp'><span class='glyphicon glyphicon-eye-open'></span></a>
        </div>`;
        html = replaceAll(html,"{baseurl}", baseurl);
        html = replaceAll(html,"{id}", id);
        $ar.append(html);
        if(espag == 'NO')$ar.find('.show_pago').show();

        $ar.find('.pagar').click(function(){
            $(this).load_dialog({
                title: 'Realizar pago',
                loaded:function($dlg){
                    $dlg.find('form').submit(function(){
                        $(this).formPost(true,{},function(data){
                            if(data.exito!=true)alert(data.mensaje);
                        });
                        $dlg.find('.close').click();
                        $table.draw('page');
                        return false;
                    })
                }
            });
            return false;
        });

        $ar.find('.vercomp').click(function(){
            $(this).load_dialog({
                loaded:function($dlg){
                    
                }
            });
            return false;
        });
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
                    botones(data.DT_RowId,data.DT_Estado,data.DT_EmailSend,data.DT_Pagado,$(row).find('td .botones'));
                }
            };
       var $this;
    var $dlg;

    $('.ocform').submit(function () {
        $table.draw();
        return false;
    })

    $('.ocform input,.ocform select').change(function () {
        $table.draw();
        return false;
    })
    
    
    var start = moment().subtract(29, 'days');
    var end = moment();
function cb(start, end) {
        $('#reportrange span').html(start.format('DD/MM/YYYY') + ' - ' + end.format('DD/MM/YYYY'));
        $('#desde').val(start.format('YYYY-MM-DD'));
        $('#hasta').val(end.format('YYYY-MM-DD'));
        if(typeof($table)!='undefined') $table.draw();
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
    var buton = "<div class='botones'></div>";
    $table = $dt.load_simpleTable(conf, true,buton);   
});
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