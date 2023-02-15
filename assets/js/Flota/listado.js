//<li class="show_editar oculto"><a href='{baseurl}flota/eliminar/{id}' class='eliminar'><span class='glyphicon glyphicon-remove'></span> Eliminar</a></li>
var url = "";
$(document).on('ready', function() {
    var url = $("#nameurl").val() + '?json=true';
    var $table;
    function botones(id,estado,orden,$ar) {
        html = `<div class='btn-group'>
          
          <button type='button' class='btn btn-success btn-xs dropdown-toggle' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'>
            <span class='caret'></span>
            <span class='sr-only'>Toggle Dropdown</span>
          </button>
          <ul class='dropdown-menu'>  
            <li class="show_editar oculto"><a href='{baseurl}flota/editar/{id}' class='editar'><span class='glyphicon glyphicon-pencil'></span> Editar</a></li>
            
            <li class="show_editar oculto"><a href='{baseurl}flota/pagar/{id}' class='pagar'><span class='glyphicon glyphicon-usd'></span> Pagar</a></li>
          </ul>
        </div>
        <span style='font-size:150%; color:#FF8C00' class='prioridad'></span>
        <span style='font-size:100%' class='comprobante label label-success oculto'>C</span>
        <span style='font-size:100%' class='liquidacion label label-info oculto'>L</span>
        <span style='font-size:100%' class='orden label label-primary oculto'>O</span>`;        
        html = replaceAll(html, "{baseurl}", baseurl);
        html = replaceAll(html, "{id}", id);
        $ar.append(html);
        if(estado == 0)$ar.find('.show_editar').show();
		if(orden == 1){
			$ar.find('.orden').show();
			$ar.find('.show_editar').hide();
		}
			
        $ar.find('.editar').click(function() {
            $(this).load_dialog({
                loaded: function($dlg) {
                    $dlg.find('form').submit(function() {
                        $(this).formPost(true, {}, function(data) {
                            if(data.exito){
                                alert(data.mensaje);
                                $dlg.find('.close').click();
                                $table.draw('page');
                            }else{
                                $dlg.find('.error').removeClass('hidden')
                                $dlg.find('.error').html(data.mensaje);
                            }
                        });
                        
                        return false;
                    })
                }
            });
            return false;
        });
		$ar.find('.pagar').click(function() {
            $(this).load_dialog({
                script: baseurl + 'assets/js/Liquidacion/cobros.js',
                loaded: function($dlg) {
                    $dlg.find('form').submit(function() {
                        $(this).formPost(true, {}, function(data) {
                            if(data.exito){
                                alert(data.mensaje);
                                $dlg.find('.close').click();
                                $table.draw('page');
                            }else{
                                $dlg.find('.error').removeClass('hidden')
                                $dlg.find('.error').html(data.mensaje);
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
        $.post(baseurl+"flota/valida_preOrdenPago", { sel: seleccionados }, function (data) {
            if(data != "")
                alert(data);
            else
                window.location = baseurl+"flota/flot_genOrdenPago?sel="+seleccionados;
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
                botones(data.DT_RowId, data.DT_Estado, data.DT_RowOrden, $(row).find('td .opts'));
                $(row).mousedown(RowClick);   
            }
        };

    
    var buton = "<div class='opts'><input type='checkbox' disabled=''></div>";
    $table = $dt.load_simpleTable(conf, true, buton);
    $('.ocform input').DTFilter($table);

    $('.ocform input,.ocform select').change(function() {
        $table.draw();
        return false;
    })

    $('#btn-report-excel').on('click', function() {
        window.location.href = baseurl + "Registro/reporte_excel/?desde=" + $('#desde').val() + "&hasta=" + $('#hasta').val() + "&moneda=" + $('#moneda').val() + "&search=" + $('#filtro').val() + "&estado=" + $('select[name="estado"]').val();
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
				console.log(response);
                $('#contacto').html(response.html);
            }
        });
    })
	
	$('select[name="servicio[]"]').change(function() {
        
        $ids = "";
        $arr_ids = $(this).val();
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
	
	$('.selectpicker').multiselect();
});
function change_date(){
    $('.ocform input').change();
}