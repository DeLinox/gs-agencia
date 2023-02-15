var url = "";
$(document).on('ready', function() {
    var url = $("#nameurl").val() + '?json=true';
    var $table;
    function botones(id, pago, pagado, orde,permisos, usuario, $ar) {
        html = `<div class='btn-group'>
          <button type='button' class='btn btn-success btn-xs dropdown-toggle' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'>
            <span class='glyphicon glyphicon-cog'></span> <span class='caret'></span> 
            <span class='sr-only'>Toggle Dropdown</span>
          </button>
          <ul class='dropdown-menu'>  
            <li class="show_pagar oculto"><a href='{baseurl}Registro/pagar_prov/{id}' class='pagar'><span class='glyphicon glyphicon-usd'></span> Pagar</a></li>
            <li class="show_editar oculto"><a href='{baseurl}Registro/editar_prov/{id}' class='editar'><span class='glyphicon glyphicon-pencil'></span> Editar</a></li>
            <li class="show_anular oculto"><a href='{baseurl}Registro/anular_provPago/{id}' class='anular'><span class='glyphicon glyphicon-remove'></span> Anular pago</a></li>
          </ul>
        </div>
        <span style='font-size:100%' class='pagado label label-success oculto'>P</span>
        <span style='font-size:100%' class='orden label label-primary oculto'>O</span>`;

        html = replaceAll(html, "{baseurl}", baseurl);
        html = replaceAll(html, "{id}", id);
        $ar.append(html);
        if(permisos > 1) {
            if(pago == 0) $ar.find('.show_pagar, .show_editar').show();
            else $ar.find('.pagado').show();
            if(usuario == 1 || usuario == 9){
                if(pagado > 0){
                    $ar.find('.show_anular').show();
                }
            }
        }
        
        if(orde == 1) $ar.find('.orden').show();
        $ar.find('.editar').click(function() {
            $(this).load_dialog({
                loaded: function($dlg) {
                    $dlg.find('form').submit(function() {
                        $(this).formPost(true, {}, function(data) {
                            alert(data.mensaje);
                            if(data.exito){
                                $dlg.find('.close').click();
                                $table.draw('page');        
                            }
                        });
                        return false;
                    })
                }
            });
            return false;
        });
        $ar.find('.anular').click(function() {
            if (confirm("¿Realmente desea anular el pago?")) {
                $.gs_loader.show();
                $.ajax({
                    type: "POST",
                    dataType: "json",
                    url: $(this).attr('href'),
                    success: function(data) {
                       alert(data.mensaje);
                       $.gs_loader.hide();
                        if(data.exito){
                            $table.draw('page');        
                        }
                    }
                });
            }
            return false;
        });        
        $ar.find('.eliminar').click(function() {
            if (confirm("¿Desea eliminar la reserva?")) {
                $.gs_loader.show();
                $.ajax({
                    type: "POST",
                    dataType: "json",
                    url: $(this).attr('href'),
                    success: function(data) {
                        $.gs_loader.hide();
                        $table.draw('page');
                        if (data.mensaje != '') alert(data.mensaje);
                    },
                    error: function(response) {
                        $table.draw('page');
                        $.gs_loader.hide();
                        alert(response.responseText.replace(/(<([^>]+)>)/ig, ""));
                    }
                });
            }
            return false;
        });
        $ar.find('.pagar').click(function() {
            $(this).load_dialog({
                script: baseurl + 'assets/js/Liquidacion/cobros.js',
                loaded: function($dlg) {
                    $dlg.find('form').submit(function() {
                        $(this).formPost(true, {}, function(data) {
                            alert(data.mensaje);
                            if(data.exito){
                                $dlg.find('.close').click();
                                $table.draw('page');        
                            }
                        });
                        return false;
                    })
                }
            });
            return false;
        });

    }
    $('.gen_ordenPago').on('click', function(){
        
        $.post(baseurl+"Registro/valida_preOrdenPago", { sel: seleccionados }, function (data) {
            if(data != "")
                alert(data);
            else
                window.location = baseurl+"Ordenserv/ord_genOrdenPago?sel="+seleccionados;
        });
    })
    
    var $dt = $('#mitabla'),
        conf = {
            data_source: url,
            cactions: ".ocform",
            order: [
                [1, "desc"]
            ],
            oncheck: function(row, data, selected) {
                
                if (selected.length > 0) {
                    $('.onsel').removeClass('hidden');
                    //$('.nosel').addClass('hidden');
                } else {
                    $('.onsel').addClass('hidden');
                    //$('.nosel').removeClass('hidden');
                }
                
                seleccionados = selected;
            },
            onrow: function(row, data) {
                botones(data.DT_RowId,data.DT_RowEstado,data.DT_RowPagado, data.DT_RowOrden,data.DT_Permisos,data.DT_UsuaId,$(row).find('td .opts'));
                $(row).mousedown(RowClick);
            }
        };
	$('table').click(function(){
        total = 0;
        $("table tbody tr").each(function(i,e){
            if($(this).hasClass('selected')){
                total += parseFloat($(this).find('td:nth-child(13)').text());
            }
        });
        $('#sum_sel').dval(total);
    })
    
    var buton = "<div class='opts'><input type='checkbox' disabled=''></div>";
    $table = $dt.load_simpleTable(conf, true, buton);
    $('.ocform input').DTFilter($table);

    $('.ocform input,.ocform select').change(function() {
        /*
        $ids = "";
        $arr_ids = $('select[name="servicio[]"]').val();
        if($arr_ids != null){
            $.each($arr_ids,function(i,elem) {
                if(elem != ''){
                    if((i+1)<$arr_ids.length){ 
                        $ids += elem+",";
                    }else {
                        $ids += elem;
                    }
                }
            })
            $('input[name="serv_ids"]').val($ids);
        }
        */
        $table.draw();
        return false;
    })

    $('#btn-report-excel').on('click', function() {
        window.location.href = baseurl + "Registro/reporte_excel/?desde=" + $('#desde').val() + "&hasta=" + $('#hasta').val() + "&moneda=" + $('#moneda').val() + "&search=" + $('#filtro').val() + "&estado=" + $('select[name="estado"]').val();
    })

    $(".btn-cuen").click(function(){  
        $cuenta = $('input[name="serv_ids"]');
        $cuenta.val($(this).attr("data-id"));
        $('.btn-cuen').removeClass("active");
        $(this).addClass("active");
        $cuenta.change();
        
    })
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
	
	$('#proveedor').change();
    $('#proveedor').change(function(){
        $.ajax({
            dataType: "JSON",
            url: baseurl+"Registro/getProveedor/"+$(this).val(),
            success: function(response){
                $('#contacto').html(response.html);
            }
        });
    })

});
function change_date(){
    $('.ocform input').change();
}
function resetear_formulario($formulario){
    $($formulario)[0].reset();
    $($formulario+ " #cliente_local"+","+$formulario+ " #hotel_local").val(null).trigger('change');
    $($formulario).find(".adides-item").remove();
    $($formulario).find(".image-item").remove();
}
