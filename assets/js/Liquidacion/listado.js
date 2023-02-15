var url = "";
$(document).on('ready', function() {
    var url = $("#nameurl").val() + '?json=true';
    var $table;
    function botones(id, escobrado, cobrado, pq, permisos, usuario, $ar) {
        html = `<div class='btn-group'>
          <a href='{baseurl}Liquidacion/liq_pdf/{id}' class='btn btn-success btn-xs vercomp'><span class='glyphicon glyphicon-eye-open'></span></a>
          <button type='button' class='btn btn-success btn-xs dropdown-toggle' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'>
            <span class='caret'></span>
            <span class='sr-only'>Toggle Dropdown</span>
          </button>
          <ul class='dropdown-menu'>  
            <li class="show_editar oculto"><a href='{baseurl}Liquidacion/liqu_form/{id}' class='editar'><span class='glyphicon glyphicon-pencil'></span> Editar</a></li>
            <li class="show_editar oculto"><a href='{baseurl}Liquidacion/eliminar/{id}' class='eliminar'><span class='glyphicon glyphicon-remove'></span> Eliminar</a></li>
            <li class="show_cobro oculto"><a href='{baseurl}Liquidacion/liq_cobrar/{id}' class='cobrar'><span class='glyphicon glyphicon-usd'></span> Cobrar</a></li>
			<li class="show_cancelarCobro oculto"><a href='{baseurl}Liquidacion/cancelar_cobroLiquidacion/{id}' class='cancelarCobro'><span class='glyphicon glyphicon-remove'></span> Cancelar Cobro</a></li>
          </ul>
        </div>
        <span style='font-size:100%' class='cobrado label label-primary oculto'>C</span>`;
        html = replaceAll(html, "{baseurl}", baseurl);
        html = replaceAll(html, "{id}", id);
        $ar.append(html);
        $ar.find('.show_clonar').show();
        if(permisos > 1){
            if(escobrado != 'PAGADO'){
                $ar.find('.show_cobro').show();  
            }else {
                $ar.find('.cobrado').show();
            }
			if(cobrado > 0){
				if(usuario == 1 || usuario == 9){
					$ar.find('.show_cancelarCobro').show();
                }
			}else{
				$ar.find('.show_editar').show();
			}
        }
        
        $ar.find('.hoja_liquidacion').click(function(){
            $(this).load_dialog({
                loaded:function($dlg){
                    
                }
            });
            return false;
        });

        $ar.find('.cobrar').click(function() {
            $(this).load_dialog({
                script: baseurl + 'assets/js/Liquidacion/cobros.js',
                loaded: function($dlg) {
                    $dlg.find('form').submit(function() {
                        $(this).formPost(true, {}, function(data) {
                            if (data.exito != true) alert(data.mensaje);
                        });
                        $dlg.find('.close').click();
                        $table.draw('page');
                        return false;
                    })
                }
            });
            return false;
        });
		$ar.find('.cancelarCobro').click(function() {
            if (confirm("¿Realmente desea cancelar el cobro?")) {
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
        $ar.find('.almacen').click(function() {
            $(this).load_dialog({
                title: 'Afectar almacen',
                loaded: function($dlg) {
                    $dlg.find('form').submit(function() {
                        $(this).formPost(true, {}, function(data) {
                            if (data.exito != true) alert(data.mensaje);
                        });
                        $dlg.find('.close').click();
                        $table.draw('page');
                        return false;
                    })
                }
            });
            return false;
        });

        $ar.find('.vercomp').click(function() {
            $(this).load_dialog({
                loaded: function($dlg) {

                }
            });
            return false;
        });

        $ar.find('.eliminar').click(function() {
            if (confirm("¿Desea eliminar el comprobante?")) {
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

    }

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
                    $('.nosel').addClass('hidden');
                } else {
                    $('.onsel').addClass('hidden');
                    $('.nosel').removeClass('hidden');
                }
                seleccionados = selected;
            },
            onrow: function(row, data) {
                botones(data.DT_RowId, data.DT_RowEst, data.DT_RowCobrado,data.DT_PaquId, data.DT_Permisos,data.DT_UsuaId,$(row).find('td .opts'));
            }
        };

    
    var buton = "<div class='opts'><input type='checkbox'></div>";
    $table = $dt.load_simpleTable(conf, true, buton);
    $('.ocform input').DTFilter($table);

    $('.ocform input,.ocform select').change(function() {
        console.log("entra al cambio");
        $table.draw();
        return false;
    })

    $('#btn-report-excel').on('click', function() {
        window.location.href = baseurl + "Venta/reporte_excel/?desde=" + $('#desde').val() + "&hasta=" + $('#hasta').val() + "&comprobantes=" + $('#comprobantes').val() + "&moneda=" + $('#moneda').val() + "&search=" + $('#filtro').val() + "&situacion=" + $('select[name="estado"]').val();
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
            'Ultimos 7 D&iacute;as': [moment().subtract(6, 'days'), moment()],
            'Ultimos 30 D&iacute;as': [moment().subtract(29, 'days'), moment()],
            'Este Mes': [moment().startOf('month'), moment().endOf('month')],
            'Anterior Mes': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
            'Este A&ntilde;o': [moment().startOf('year'), moment().endOf('year')],
            'A&ntildeo Anterior': [moment().subtract(1, "y").startOf("year"), moment().subtract(1, "y").endOf("year")],
        },
        "linkedCalendars": false,
        "showCustomRangeLabel": false,
        "alwaysShowCalendars": true
    }, cb);

    cb(start, end);

});
function change_date(){

    $('.ocform input').change();
}
