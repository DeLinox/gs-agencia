var url = "";
$(document).on('ready', function() {
	//$("select[name='tipo'] option[value='PRIVADO']").hide();

    var url = $("#nameurl").val() + '?json=true';
    var $table;
    function botones(id, pq, comp, liqu, orde, prio, tipo, permisos,$ar,$fechaServicio,file,mod_modulosTR,paqu_habilitado) {
        html = `<div class='btn-group'>
          <a href='{baseurl}Registro/detalles_paquete/{pq}' title='Detalles' class='btn btn-primary btn-xs detalles'><span class='glyphicon glyphicon-list'></span></a>
          <button type='button' class='btn btn-success btn-xs dropdown-toggle' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'>
            <span class='caret'></span>
            <span class='sr-only'>Toggle Dropdown</span>
          </button>
          <ul class='dropdown-menu'>  
            <li class="show_editar oculto"><a href='{baseurl}Registro/paq_crear_local/{pq}' class='editar'><span class='glyphicon glyphicon-pencil'></span> Editar</a></li>
            <li class="show_editar oculto"><a href='{baseurl}Registro/paq_eliminar/{pq}' class='eliminar'><span class='glyphicon glyphicon-remove'></span> Eliminar</a></li>
            <li class="habilitar_ver oculto"><a href='{baseurl}Registro/paq_habilitar/{pq}' class='habilitar'><span class='glyphicon glyphicon-thumbs-up'></span> Habilitar</a></li>
            <li class="deshabilitar_ver oculto"><a href='{baseurl}Registro/paq_habilitar/{pq}' class='habilitar'><span class='glyphicon glyphicon-thumbs-down'></span> Deshabilitar</a></li>
          </ul>
        </div>
        <span class='show_correo oculto'><a href='{baseurl}Venta/confirm_correo/{id}' class='btn btn-warning btn-sm correo'><span class='glyphicon glyphicon-envelope'></span></a></span>
        <span style='font-size:150%; color:#FF8C00' class='prioridad'></span>
        <span style='font-size:100%' class='comprobante label label-success oculto'>C</span>
        <span style='font-size:100%' class='liquidacion label label-info oculto'>L</span>
        <span style='font-size:100%' class='orden label label-primary oculto'>O</span>`;
        html = replaceAll(html, "{baseurl}", baseurl);
        html = replaceAll(html, "{id}", id);
        html = replaceAll(html, "{pq}", pq);
        $ar.append(html);
        $ar.find('.show_clonar').show();
        var fechaSerivicio = new Date($fechaServicio)
        var today = new Date()
        var isToday = (fechaSerivicio.toDateString() === today.toDateString());
        file = file.substring(0, 2)
        if (permisos > 1 && ((isToday && file == 'TR') || paqu_habilitado == "1"||file != 'TR')) $ar.find('.show_editar').show();
        if (mod_modulosTR > 1 && file == 'TR') {
            if (paqu_habilitado != "1") {
                $ar.find('.habilitar_ver').show();
            } else {
                $ar.find('.deshabilitar_ver').show();
            }
        }

        if(prio == 2) $ar.find('.prioridad').addClass('glyphicon glyphicon-star-empty');
        else if(prio == 3) $ar.find('.prioridad').addClass('glyphicon glyphicon-star');
        if(comp == 1) $ar.find('.comprobante').show();
        if(liqu == 1) $ar.find('.liquidacion').show();
        if(orde == 1) $ar.find('.orden').show();
        $ar.find('.hoja_liquidacion').click(function(){
            $(this).load_dialog({
                loaded:function($dlg){
                    
                }
            });
            return false;
        });

        $ar.find('.editar').click(function(e) {
            e.preventDefault();
            if(tipo == 'LOCAL'){
                $(this).load_dialog({
                    script: baseurl + 'assets/js/Registro/form_local.js?v=2.9',
                    loaded: function($dlg) {
                        $dlg.find('.guardar').click(function(e) {
                            //e.preventDefault();
                            var $form = "#frm-gen_orden";
                            if(confirm("¿Guardar reserva?")){
                                var formData = new FormData($($form)[0]);
                                $.ajax({
                                    dataType: "json",
                                    url: $($form).attr("action"),
                                    type: $($form).attr("method"),
                                    data: formData,
                                    cache: false,
                                    contentType: false,
                                    processData: false,
                                    success: function(resp){
                                        if (resp.exito == false) {
                                            alert(resp.mensaje);
                                        } else {
                                            alert(resp.mensaje);
                                            $dlg.find('.close').click();
                                            $table.draw('page');
                                        }     
                                    }
                                })
                                return false;
                                /*
                                $(this).formPost(true, {}, function(data) {
                                    if (data.exito != true) alert(data.mensaje);
                                });
                                $dlg.find('.close').click();
                                $table.draw('page');
                                return false;
                                */
                            }
                        })
                    }
                });
                return false;
            }else if(tipo == 'PRIVADO'){
                location.href = baseurl+"registro/paq_crear_privado/"+pq;
            }else{
                window.location = baseurl+"Registro/paq_edit/"+pq;
            }
        });

        $ar.find('.pagar').click(function() {
            $(this).load_dialog({
                title: 'Realizar pago',
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
            if (confirm("�Se eliminar�n todos los servicios del FILE seleccionado, �Desea continuar?")) {
                $.gs_loader.show();
                $.ajax({
                    type: "POST",
                    dataType: "json",
                    url: $(this).attr('href'),
                    success: function(data) {
                        $.gs_loader.hide();
                        alert(data.mensaje)
                        $table.draw('page');
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
        $ar.find('.habilitar').click(function () {
            $.gs_loader.show();
            $.ajax({
                type: "POST",
                dataType: "json",
                url: $(this).attr('href'),
                success: function (data) {
                    $.gs_loader.hide();
                    alert(data.mensaje)
                    $table.draw('page');
                },
                error: function (response) {
                    $table.draw('page');
                    $.gs_loader.hide();
                    alert(response.responseText.replace(/(<([^>]+)>)/ig, ""));
                }
            });
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
    
    $('.crear_local').on('click', function(){
        $(this).load_dialog({
            title: $(this).attr('title'),
            script: baseurl + 'assets/js/Registro/form_local.js?v=2.5',
            loaded: function(dlg) {
                $(dlg).find('.guardar').click(function(e) {
                    //e.preventDefault();
                    var cerrar = true;
                    var $form = "#frm-gen_orden";
                    if(!$(this).hasClass("cerrar")){
                        cerrar = false;
                    }
                    if(confirm("Desea guardar la reserva")){
                        var formData = new FormData($($form)[0]);
                        $.ajax({
                            dataType: "json",
                            url: $($form).attr("action"),
                            type: $($form).attr("method"),
                            data: formData,
                            cache: false,
                            contentType: false,
                            processData: false,
                            success: function(resp){
                                if (resp.exito == false) {
                                    alert(resp.mensaje);
                                } else {
                                    alert(resp.mensaje);
                                    if(cerrar){
                                        dlg.modal('hide');
                                    }else{
                                        resetear_formulario($form);
                                    }
                                    $table.draw('page');
                                }     
                            }
                        })
                        return false;
                    }
                });
            }
        });
        return false;
    })
    $('.crear_rapido').on('click', function(){
        $(this).load_dialog({
            title: $(this).attr('title'),
            script: baseurl + 'assets/js/Registro/form_rapido.js?v=1.0',
            loaded: function(dlg) {
                $(dlg).find('.guardar').click(function(e) {
                    //e.preventDefault();
                    var cerrar = true;
                    var $form = "#frm-gen_orden";
                    if(!$(this).hasClass("cerrar")){
                        cerrar = false;
                    }
                    if(confirm("Desea guardar la reserva")){
                        var formData = new FormData($($form)[0]);
                        $.ajax({
                            dataType: "json",
                            url: $($form).attr("action"),
                            type: $($form).attr("method"),
                            data: formData,
                            cache: false,
                            contentType: false,
                            processData: false,
                            success: function(resp){
                                if (resp.exito == false) {
                                    alert(resp.mensaje);
                                } else {
                                    alert(resp.mensaje);
                                    if(cerrar){
                                        dlg.modal('hide');
                                    }else{
                                        resetear_formulario($form);
                                    }
                                    $table.draw('page');
                                }     
                            }
                        })
                        return false;
                    }
                });
            }
        });
        return false;
    })
    
    $('.gen_ord_serv').on('click', function(){
        window.location = baseurl+"Ordenserv/ord_gen?sel="+seleccionados;
    })
    
    $('.btn-estado').on('click', function(){
        if(confirm("Seguro que quieres cambiar de estado?")){
            $.ajax({
                dataType: "json",
                url: baseurl+"Registro/paq_change_estado",
                type: "POST",
                data: {estado: $(this).attr("data-acc"), sel: seleccionados},
                success: function(resp){
                    alert(resp.mensaje);
                    if (resp.exito) {
                        $table.draw('page');
                    }
                }
            })
            return false;
        }
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
                botones(data.DT_RowId,data.DT_PaquId, data.DT_RowComp, data.DT_RowLiqu, data.DT_RowOrde, data.DT_RowPrio, data.DT_RowTipo, data.DT_Permisos, $(row).find('td .opts'),data.fechaSimple,data.FILE,data.mod_modulosTR,data.paqu_habilitado);
                $(row).mousedown(RowClick);   
            }
        };

    
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
    $('.selectpicker').multiselect();    

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
	
    $(".btn-cuen").click(function(){
        if($(this).hasClass("active")){
            $(this).removeClass("active");
        }else{
            $(this).addClass("active");
        }

        $btns = $(".btn-cuen.active");
        console.log("cantidad de elementos: "+$btns.length);
        $ids = "";
        $.each($btns,function(i,elem) {
            if($(elem).hasClass("active")){
                if ($btns.length == (i+1)) $ids += $(elem).attr("data-id");
                else $ids += $(elem).attr("data-id")+",";
            }
        })
        $('input[name="serv_ids"]').val($ids);
        $('input[name="serv_ids"]').change();
        /*
        $("select[name='scontacto'] option[value='']").attr("selected", true);
        $cuenta = $('input[name="serv_ids"]');
        $cuenta.val($(this).attr("data-id"));
        $('.btn-cuen').removeClass("active");
        $(this).addClass("active");
        $cuenta.change();
        */
    })
	
	if($('input[name="serv_ids"]').val().length>0){
		var a = $('input[name="serv_ids"]').val().split(',');
		for(k in a){
			$('button[data-id="'+a[k]+'"]').addClass('active');
		}
		
	}
		
	
    $("select[name='scontacto']").change(function(){
    	$cuenta = $('input[name="serv_ids"]');
    	$cuenta.val($(this).val());
    	$('.btn-cuen').removeClass("active");
    	$cuenta.change();
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
    $($formulario).find(".prov-item").remove();
}
