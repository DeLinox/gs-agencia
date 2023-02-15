var url = "";
$(document).on('ready', function () {
    //baseurl = $("#baseurl").val();
    var url = $("#nameurl").val()+'?json=true';
    var $table;
    var seleccionados;
    var procesados = 0;
    var mensajes = new Array();
    var tipo_envio = 0;

    function botones(id,mail,$ar){
       esmai = (mail==0);
       html = `<div class='btn-group'>
          <a href='{baseurl}Cotizacion/vercomp/{id}' class='btn btn-success btn-sm vercomp'><span class='glyphicon glyphicon-eye-open'></span></a>
          <button type='button' class='btn btn-success btn-sm dropdown-toggle' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'>
            <span class='caret'></span>
            <span class='sr-only'>Toggle Dropdown</span>
          </button>
          <ul class='dropdown-menu'>
            <li class="show_editar oculto"><a href='{baseurl}Cotizacion/edit/{id}#0' class='editar'><span class='glyphicon glyphicon-pencil'></span> Editar</a></li>
            <li><a href='{baseurl}Cotizacion/confirm_correo/{id}' class='correo'><span class='glyphicon glyphicon-envelope'></span> Enviar correo</a></li>
            <li><a href='{baseurl}Venta/toclonar/{id}' class='clonar'><span class='glyphicon glyphicon-share-alt'></span> Clonar comprobante</a></li>
            <li class="show_editar oculto"><a href='{baseurl}Venta/pedidoTo/{id}/1#0' class='factura'><span class='glyphicon glyphicon-share-alt'></span> Convertir a factura</a></li>
            <li class="show_editar oculto"><a href='{baseurl}Venta/pedidoTo/{id}/3#0' class='boleta'><span class='glyphicon glyphicon-share-alt'></span> Convertir a boleta</a></li>
            <li class="show_editar oculto"><a href='{baseurl}Cotizacion/eliminar/{id}' class='eliminar'><span class='glyphicon glyphicon-remove'></span> Eliminar</a></li>
          </ul>
        </div>
        <span class='show_correo oculto'><a href='{baseurl}Cotizacion/confirm_correo/{id}' class='btn btn-warning btn-sm correo'><span class='glyphicon glyphicon-envelope'></span></a></span>`;
        html = replaceAll(html,"{baseurl}", baseurl);
        html = replaceAll(html,"{id}", id);
        $ar.append(html);
        $ar.find('.show_editar').show();
        //if(esmai)$ar.find('.show_correo').show();

        $ar.find('.correo').click(function(){
            $(this).load_dialog({
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

        $ar.find('.eliminar').click(function(){
            if(confirm("¿Desea eliminar el comprobante?")){
            $.gs_loader.show();
            $.ajax({
                   type: "POST",
                   dataType: "json",
                   url:$(this).attr('href') ,
                   success: function(data){
                        $.gs_loader.hide();
                        $table.draw('page');
                        if(data.mensaje!='') alert(data.mensaje);
                   },
                   error: function(response) {
                        $table.draw('page');
                        $.gs_loader.hide();
                        alert(response.responseText.replace(/(<([^>]+)>)/ig,""));
                   }
                });
            }
            return false;
        });

    }



    $('.correo_todos').click(function(){
        if(confirm('¿Desea enviar a correos?')){
            $.gs_loader.show();
            tipo_envio = 1;
            for(i=0;i<seleccionados.length;i++){
                procesados++;
                $.ajax({
                   type: "POST",
                   dataType: "json",
                   url:baseurl+'Venta/enviarcorreo/'+seleccionados[i],
                   success: function(data){  procesados--;
                    verprocesos(); },
                   error: function(response) { procesados--;
                    verprocesos();  }
                });
            }
            $dt.reset_selected();
            $('.onsel').addClass('hidden');
            $('.nosel').removeClass('hidden');
        }else{

        }
        return false;
    })

    function verprocesos(){
        if(procesados>0){
            $('.procesos').html('<strong>'+procesados+'</strong> Comprobantes en proceso.');
        }else{
            $('.procesos').html('');
            tipo_envio = 0;
            $table.draw('page');
			$.gs_loader.hide();
        }
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
                    botones(data.DT_RowId,data.DT_EmailSend,$(row).find('td .botones'));
                }
            };
	var $this;
    var $dlg;

    $('#confirm-correo').on('show.bs.modal', function (e) {
        $this = $(e.relatedTarget);
        $dlg = $(e.delegateTarget);
        $('#form-confirm').attr('action',$this.attr('data-href'));
         $.ajax({
            url:$this.attr('data-href') ,
            success : function(respuesta) {
              
                $('#corre-confirmar').val(respuesta);
            },
        });
    });

    
    $('#confirm-correo').find('.btn-ok').on('click', function () {
        correo = $('#corre-confirmar').val();
        $dlg.find('.close').click(); 
        $.gs_loader.show('page');
        $.ajax({
           type: "POST",
           url:$this.attr('data-href') ,
           data: {correo: correo},
           success: function(data){
                $table.draw('page');
                $.gs_loader.hide();
           },
           error: function(response) {
                $table.draw('page');
                $.gs_loader.hide();
                alert(response.responseText.replace(/(<([^>]+)>)/ig,""));
            }
         });
    });
    



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
    var buton = "<div class='botones'><input type='checkbox'></div>";
    $table = $dt.load_simpleTable(conf, true,buton);
	
	$('#btn-report-excel').on('click',function(){
		window.location.href = baseurl+"Cotizacion/reporte_excel/?desde="+$('#desde').val()+"&hasta="+$('#hasta').val()+"&comprobantes="+$('#comprobantes').val()+"&moneda="+$('#moneda').val()+"&search="+$('#filtro').val();
	})
    
});
