var url = "";
$(document).on('ready', function() {
    var url = $("#nameurl").val() + '?json=true';
    var $table;
    function botones(id, pq, comp, liqu, orde, tipo, cobro, pago, permisos, usuario, $ar) {
        html = `<div class='btn-group'>
        <a href='{baseurl}Registro/detalles_paquete/{pq}' title='Detalles' class='btn btn-primary btn-xs detalles'><span class='glyphicon glyphicon-list'></span></a>
          <button type='button' class='btn btn-success btn-xs dropdown-toggle' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'>
            <span class='caret'></span>
            <span class='sr-only'>Toggle Dropdown</span>
          </button>
          <ul class='dropdown-menu'>  
            <li class="show_cobrar oculto"><a href='{baseurl}Registro/paq_cobrar/{pq}' class='cobrar'><span class='glyphicon glyphicon-pencil'></span> Cobrar</a></li>
            <li class="show_pagar oculto"><a href='{baseurl}Registro/paq_pagar/{pq}' class='pagar'><span class='glyphicon glyphicon-pencil'></span> Pagar</a></li>
			<li class="show_cancelarCobro oculto"><a href='{baseurl}Registro/paq_cancelarCobro/{pq}' class='cancelarCobro'><span class='glyphicon glyphicon-remove'></span> Cancelar Cobro</a></li>
          </ul>
        </div>
        <span class='show_correo oculto'><a href='{baseurl}Venta/confirm_correo/{id}' class='btn btn-warning btn-sm correo'><span class='glyphicon glyphicon-envelope'></span></a></span>
        <span style='font-size:100%' class='comprobante label label-success oculto'>C</span>
        <span style='font-size:100%' class='liquidacion label label-info oculto'>L</span>
        <span style='font-size:100%' class='orden label label-primary oculto'>O</span>`;
        html = replaceAll(html, "{baseurl}", baseurl);
        html = replaceAll(html, "{id}", id);
        html = replaceAll(html, "{pq}", pq);
        $ar.append(html);
        if(permisos > 1) {
            if(cobro == 0 && comp != 1 && liqu != 1) $ar.find('.show_cobrar').show();
			if(usuario == 1 || usuario == 9){
				$ar.find('.show_cancelarCobro').show();
			}
			/*
			if(cobro == 1){
                
            }
			*/
        }
        //if(pago == 0) $ar.find('.show_pagar').show();
        if(comp == 1) $ar.find('.comprobante').show();
        if(liqu == 1) $ar.find('.liquidacion').show();
        if(orde == 1) $ar.find('.orden').show();
        $ar.find('.cobrar').click(function() {
            $(this).load_dialog({
                script: baseurl + 'assets/js/Liquidacion/cobros.js',
                loaded: function($dlg) {
                    $dlg.find('form').submit(function() {
                        $(this).formPost(true, {}, function(data) {
                            //if (data.exito != true) alert(data.mensaje);
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
        $ar.find('.detalles').click(function() {
            $(this).load_dialog({
                loaded: function($dlg) {

                }
            });
            return false;
        });

    }
    $('.gen_ord_pago').on('click', function(){
        window.location = baseurl+"Liquidacion/liqu_genReceptivo?sel="+seleccionados;
    })
    $('.gen_comprobante').on('click', function(){
        window.location = baseurl+"Comprobante/to_form?sel="+seleccionados;
    })
    $('.gen_liquidacion').on('click', function(){
        contacto = $('#contacto').val();
        window.location = baseurl+"Liquidacion/to_form?sel="+seleccionados;
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
                botones(data.DT_RowId,data.DT_PaquId, data.DT_RowComp, data.DT_RowLiqu, data.DT_RowOrde, data.DT_RowTipo, data.DT_Cobro,data.DT_Pago,data.DT_Permisos,data.DT_UsuaId,$(row).find('td .opts'));
                $(row).mousedown(RowClick);   
            }
        };

    $data = $('tr');
    $('table').click(function(){
        total = 0;
        saldos = 0;
        $("table tbody tr").each(function(i,e){
            if($(this).hasClass('selected')){
                saldo = parseFloat($(this).find('td:nth-child(16)').text());
                total += parseFloat($(this).find('td:nth-child(14)').text());
                saldos += (isNaN(saldo)?0:saldo);
            }
        });
        $('#sum_sel').dval(total);
        $('#sum_sal').dval(saldos);
    })
	
    var buton = "<div class='opts'><input type='checkbox' disabled=''></div>";
    $table = $dt.load_simpleTable(conf, true, buton);
    $('.ocform input').DTFilter($table);

    $('.ocform input,.ocform select').change(function() {
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
        }else{
			$('input[name="serv_ids"]').val("");
		}
        
        $table.draw();
        return false;
    })

    $('#btn-report-regAux').on('click', function() {
        window.location.href = baseurl + "Registro/reporte_excelAuxiliar/?desde=" + $('#desde').val() + "&hasta=" + $('#hasta').val() + 
											"&det_comp=" + $('select[name="det_comp"]').val() + "&det_liqu=" + $('select[name="det_liqu"]').val() + 
											"&paqu_cobrado=" + $('select[name="paqu_cobrado"]').val() + "&busqueda=" + $('#filtro').val() +
											"&tipo=" + $('select[name="tipo"]').val() + "&contacto=" + $('select[name="contacto"]').val() + 
											"&serv_ids=" + $('input[name="serv_ids"]').val();
    })
	
	$('#btn-report-regCobros').on('click', function() {
        window.location.href = baseurl + "Registro/reporte_excelCobros/?desde=" + $('#desde').val() + "&hasta=" + $('#hasta').val() + 
											"&det_comp=" + $('select[name="det_comp"]').val() + "&det_liqu=" + $('select[name="det_liqu"]').val() + 
											"&paqu_cobrado=" + $('select[name="paqu_cobrado"]').val() + "&busqueda=" + $('#filtro').val() +
											"&tipo=" + $('select[name="tipo"]').val() + "&contacto=" + $('select[name="contacto"]').val() + 
											"&serv_ids=" + $('input[name="serv_ids"]').val();
    })
	
    $('.selectpicker').multiselect({
		includeSelectAllOption: true,
		selectAllText: '* Servicios',
		nonSelectedText: '* Servicios'
	});    
	$('.multiselect').addClass('input-sm');
    /*
    $(".btn-cuen").click(function(){
        if ($(this).hasClass('active')){
            $(this).removeClass("active");
        }else{
            $(this).addClass("active");
        }
        
        $inp = $('input[name="serv_ids"]');
        $ids = "";

        $arr_ids = $('.btn-cuen.active');

        $.each($arr_ids,function(i,elem) {
            if ($(elem).hasClass('active')){
                if((i+1) == $arr_ids.length)
                    $ids += $(elem).attr('data-id');
                else
                    $ids += $(elem).attr('data-id')+",";
            }
        })
        $inp.val($ids);    
        $table.draw();
        return false;
    })
    */
    /*
    $(".btn-cuen").click(function(){
        
        $cuenta = $('input[name="serv_ids"]');
        $cuenta.val($(this).attr("data-id"));
        $('.btn-cuen').removeClass("active");
        $(this).addClass("active");
        $cuenta.change();
        
    })
	*/
    var start = moment().subtract(7, 'days');
    var end = moment();

    function cb(start, end) {
        $('#reportrange span').html(start.format('DD/MM/YYYY') + ' - ' + end.format('DD/MM/YYYY'));
        $('#desde').val(start.format('YYYY-MM-DD'));
        $('#hasta').val(end.format('YYYY-MM-DD'));
        if (typeof($table) != 'undefined') $table.draw();
    }
    $('select#contacto').select2()
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
	
	/*
	$('select[name="tipo"]').change(function(){
		if($(this).val() == 'RECEPTIVO' || $(this).val() == 'PRIVADO'){
			$('#servicio').multiselect('clearSelection');			
			$('#servicio').change();
		}
	})
	*/
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
