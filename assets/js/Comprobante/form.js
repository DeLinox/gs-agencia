$(document).ready(function () {
    var baseurl = $("#baseurl").val();

    //$('#serie, #numero').attr("readonly",'');
    $('#serie').attr("readonly",'');

    if($('#comprobante').val() != '07'&&$('#comprobante').val() != '08') $('#notas').hide(); 
    else{ 
        $('#notas').show();
        if($('#comprobante').val() == '07'){
              $('.credito').show();  
              $('.debito').hide();  
            } 
            else{
              $('.debito').show();  
              $('.credito').hide();    
            } 
    }

    $('#comprobante').change(function(){
        if($(this).val() == '07'||$(this).val() == '08'){
            $('#notas').show();
            if($(this).val() == '07'){
              $('.credito').show();  
              $('.debito').hide();  
            } 
            else{
              $('.debito').show();  
              $('.credito').hide();    
            } 
        }else
            $('#notas').hide();
    });
    
    

    $('select.cmb').select2({placeholder: 'Seleccione', minimumResultsForSearch: Infinity, width: '100%', allowClear: false});
	
    function getSerie($tipo){
        if($tipo=='01')return "F001";
        if($tipo=='02')return "RH01";
        if($tipo=='03')return "B001";
        if($tipo=='07')return "FC01";
        if($tipo=='08')return "FD01";
    }

    $('#serie').change(function(){
        changeSerie($(this).val());
    })

    $('#moneda').change(function(){
        if($(this).val()=='DOLARES') $('.msimb').text('$');
        else $('.msimb').text('S/');
    });
     $('#moneda').change();

	$('#comprobante').change(function(){

        $('#serie').val(getSerie($(this).val()));
        $('#serie').change();
	});

    function changeSerie($serie){
        $.ajax({
          dataType: "json",
          url: baseurl + "comprobante/getnext/"+$serie,
          success: function(data){
            $('#numero').val(data.numero);
          }
        });
    }

    $('#nc_comprobante').change(function(){
        if($('#comprobante').val()=='07'){
           if($(this).val()=='01') $("#serie").val("FC01"); 
           if($(this).val()=='03') $("#serie").val("BC01"); 
        }else if($('#comprobante').val()=='07'){
           if($(this).val()=='01') $("#serie").val("FC01"); 
           if($(this).val()=='03') $("#serie").val("BC01"); 
        }
        $('#serie').change();
    });

    $('#detraccion, #genera_archivo, #enviar_email').change(function(){
        if( $(this).prop('checked') ) {
            $(this).val(1);
        }else{
            $(this).val(2);
        }
    })

    
    $('#exterior').change(function(){
        if( $(this).prop('checked') ) {
            //$('select[name="tipo[]"]').val('40').change();
            $(this).val(1);
        }else{
            //$('select[name="tipo[]"]').val('10').change();
            $(this).val(2);
        }
    })
    $.fn.SelectFecha = function () {
        $(this).daterangepicker({
            singleDatePicker: true,
            locale: {
                format: 'DD/MM/YYYY'
            }
        });
    }


    $.fn.SelectServicio = function () {
        $(this).select2({
            placeholder: 'Buscar servicio',
            allowClear: true,
            width: '100%',
            language: "es",
            minimumInputLength: Infinity,
            ajax: {
                url: baseurl + "registro/buscar_serv",
                dataType: 'json',
                data: function (params) {
                    return {
                        q: params.term,
                        p: params.page
                    };
                },
                processResults: function (data, params) {
                    params.page = params.page || 1;
                    return {
                        results: data.items,
                        pagination: {
                            more: (params.page * 30) < data.total_count
                        }
                    };
                },
                cache: true
            },
            escapeMarkup: function (markup) {
                return markup;
            },
            minimumInputLength: 0 
        }).on("select2:select", function (e) {
            $(this).closest('.fact-item').find('input[name="cantidad[]"]').removeAttr('disabled');
            $(this).closest('.fact-item').find('input[name="descuento[]"]').removeAttr('disabled');
            $(this).closest('.fact-item').find('input[name="precio[]"]').removeAttr('disabled');
            $(this).closest('.fact-item').find('input[name="valor[]"]').removeAttr('disabled');
            $(this).closest('.fact-item').find('select[name="gratuita[]"]').removeAttr('disabled');
            $(this).closest('.fact-item').find('select[name="tipo[]"]').removeAttr('disabled');
            $(this).closest('.fact-item').find('input[name="codigo[]"]').removeAttr('disabled');
            //$(this).closest('.fact-item').find('select[name="tipo[]"]').val(e.params.data.igvtipo);
            $(this).closest('.fact-item').find('select[name="unidad[]"]').val(e.params.data.unid_nombre);
            $(this).closest('.fact-item').find('input[name="codigo[]"]').val(e.params.data.codigo);
			$(this).closest('.fact-item').find('input[name="servicio_nombre[]"]').val(e.params.data.text);
            $(this).closest('.fact-item').find('input[name="precio[]"]').dval(myRound(0.00));
            $(this).closest('.fact-item').find('input[name="precio[]"]').change()
            updateTotal();
            $(this).closest('.fact-item').find("textarea").removeAttr('disabled');
            $(this).closest('.fact-item').find('textarea[name="detalle[]"]').val(e.params.data.text);
        }).on('select2:unselect', function (e) {
            $(this).closest('.fact-item').find('textarea[name="detalle[]"]').val('');
            $(this).closest('.fact-item').find('input[name="cantidad[]"]').attr('disabled', '');
            $(this).closest('.fact-item').find('input[name="descuento[]"]').attr('disabled', '');
            $(this).closest('.fact-item').find('input[name="precio[]"]').attr('disabled', '');
            $(this).closest('.fact-item').find('input[name="valor[]"]').attr('disabled', '');
            $(this).closest('.fact-item').find('select[name="gratuita[]"]').attr('disabled', '');
            $(this).closest('.fact-item').find('select[name="tipo[]"]').attr('disabled', '');
            $(this).closest('.fact-item').find('input[name="codigo[]"]').attr('disabled', '');
            $(this).closest('.fact-item').find('input[name="valor[]"]').dval(myRound(0));
            $(this).closest('.fact-item').find('input[name="valor[]"]').change()
            updateTotal();
            $(this).closest('.fact-item').find("textarea").attr('disabled', '');
        });
    }

    
    
    function clickCrear() {
        $(this).load_dialog({
            title: $(this).attr('title'),
            loaded: function(dlg) {
                $(dlg).find('form').submit(function() {
                    $(dlg).find('.error').addClass('hidden')
                    $(this).formPost(true, function(data) {
                        if (data.exito == false) {

                        } else {
                            alert(data.mensaje);
                    dlg.modal('hide');
                            dlg.modal('hide');
                        }
                    });
                    return false;
                });
            }
        });
        return false;
    }
    function clickCrearProducto() {
        $(this).load_dialog({
            title: $(this).attr('title'),
            loaded: function (dlg) {
                $(dlg).find('form').submit(function () {
                    $(dlg).find('.error').addClass('hidden')
                    $(this).formPost(true, function (data) {
                        if (data.exito == false) {
                            alert("Algo salio mal, intentelo más tarde");
                        } else {
                            alert("servicio creado exitosamente");
                            /*
                            $('select#servicio').select2("trigger", "select", {
                                data: {id: data.servicio.serv_id, text: data.servicio.serv_descripcion}
                            });
                            */
                            dlg.modal('hide');
                        }
                    });
                    return false;
                });
            }
        });
        return false;
    }

    //if($('#id_venta').val() != ''){
    if(detas.length > 0){
         $.each(detas,function(i,elem){
            $nuevafila = $('#clonables .fact-item').clone();
            
            console.log(elem);
            $nuevafila.find(".deta_servicio").SelectServicio();
            $nuevafila.find(".fecha").SelectFecha();
            

            $nuevafila.find(".deta_servicio").select2("trigger", "select", {
                data: {id: elem.deta_serv_id, text: elem.deta_serv_name}
            });
            $nuevafila.find('input[name="deta_id[]"]').val(elem.deta_id);
            $nuevafila.find('input[name="deta_vent_id[]"]').val(elem.deta_vent_id);
            $nuevafila.find('input[name="deta_pdet_id[]"]').val(elem.deta_pdet_id);

            $nuevafila.find('input[name="igv[]"]').dval(elem.deta_igv);
            $nuevafila.find('input[name="lunch[]"]').val(elem.deta_lunch);
            $nuevafila.find('textarea[name="adicion[]"]').val(elem.deta_adic);
            $nuevafila.find('input[name="adicion_val[]"]').dval(elem.deta_adic_val).on('change', updateRow);
            $nuevafila.find('textarea[name="descuento[]"]').val(elem.deta_desc);
            $nuevafila.find('input[name="descuento_val[]"]').dval(elem.deta_desc_val).on('change', updateRow);
            $nuevafila.find('input[name="fecha[]"]').val(elem.deta_fechaserv);
            $nuevafila.find('textarea[name="nombre_grupo[]"]').val(elem.deta_descripcion);
            $nuevafila.find('input[name="serv_prec[]"]').dval(elem.deta_precio).on('change', updateRow);
			$nuevafila.find('input[name="fprecio[]"]').dval(elem.deta_fprecio);
			$nuevafila.find('input[name="file[]"]').val(elem.deta_file);
            $nuevafila.find('input[name="pax[]"]').val(elem.deta_pax).on('change', updateRow);
            $nuevafila.find('input[name="lunch_efect[]"]').val(elem.deta_lunch_efect).on('change', updateRow);
            $nuevafila.find('input[name="lunch_prec[]"]').dval(elem.deta_lunch_prec).on('change', updateRow);
            $nuevafila.find('input[name="total[]"]').dval(elem.deta_total);
            $nuevafila.find('input[name="serv_prec[]"]').change();
            $nuevafila.appendTo('.sortable');
            updateTotal();
        });
    }
    //}

    $('a.agregarfila').click(function () {
        /*
        $nuevafila = $('#clonables .fact-item').clone();
        $nuevafila.find(".deta_servicio").SelectServicio();
        $nuevafila.find('.addservicio').on('click', clickCrear);
        $nuevafila.hide();
        $nuevafila.appendTo('.sortable');
        $nuevafila.find('.formdrown ul li').click(function(){return false;})
        $nuevafila.find('input[name="cantidad[]"]').on('change', updateRow);
        $nuevafila.find('input[name="precio[]"]').on('change', updateRow);
        $nuevafila.find('input[name="descuento[]"]').on('change', updateRow);
        $nuevafila.find('input[name="valor[]"]').on('change', updateRow);
        $nuevafila.find('textarea[name="detalle[]"]').autosize();
        $nuevafila.fadeIn();
        return false;
        */
    });

    if ($('.sortable .fact-item').length <= 0){
        $('a.agregarfila').click();
    }
    
    procesarFormulario = function (dlg) {
        $(dlg).find('form').submit(function () {
            $(dlg).find('.error').addClass('hidden')
            $(this).formPost(true, function (data) {
                if (data.exito == false) {
                    alert(data.mensaje);
                } else {
                    /*$('select#cliente').select2("trigger", "select", {
                        data: {id: data.datos.clie_id, text: , docnum: , direccion: , docu: data.datos.clie_docu_id}
                    });*/
                $("input#docnum").val( data.datos.clie_docnum )
                $("input#clie_id").val( data.datos.clie_id )
                $("input#direccion").val( data.datos.clie_direccion )
                $("input#rsocial").val( data.datos.clie_rsocial )
                $("input#email").val( data.datos.clie_email )
                $("select[name=documento]").eq(0).val( data.datos.clie_docu_id );
                    dlg.modal('hide');
                }
            });
            return false;
        });
    };
    $('.newclient').click(function () {
        $(this).load_dialog({
            title: $(this).attr('title'),
            script: baseurl + 'assets/js/Cliente/consulta_sunat.js',
            loaded: procesarFormulario
        });
        return false;
    });

    buscarCliente = function (dlg) {
        $(dlg).find('form').submit(function () {
            $("input#rsocial").val( $(this).find("input[name='srsocial']").val())
            $("select[name=documento]").val( $(this).find("select[name='sdocumento']").val())
            $("input#docnum").val( $(this).find("input[name='sdocnum']").val() )
            $("input#direccion").val( $(this).find("input[name='sdireccion']").val() )
            $("input#email").val( $(this).find("input[name='semail']").val() )
            $("input#clie_id").val( $(this).find('#sclie_id').val() )
            dlg.modal('hide');
            return false;
        });
    };

    

    $('.searchprov').click(function () {
        $(this).load_dialog({
            title: $(this).attr('title'),
            loaded: buscarCliente
        });
        return false;
    });
    $('.checkbox input[type="checkbox"]').on('change', function() {
        if($(this).prop('checked')){
            $(this).val(1);
        }else{
            $(this).val(2);
        }
    });
    $('#vender').submit(function(e){
        e.preventDefault();
        guardarComprobante(this);
    });
    $('#completar').click(function(){
        doc_num = $('#docnum').val();
        $.ajax({
          dataType: "json",
          url: baseurl + "contacto/sbuscar_prov",
          data: { 
                q :'num',
                num: doc_num 
            },
          success: function(e){
            if (e.total_count == 1) {
                $("input#docnum").val(e.items[0].docnum);
                $("input#direccion").val(e.items[0].direccion);
                $("input#clie_id").val(e.items[0].id)
                $("input#rsocial").val(e.items[0].text);
                $("input#email").val(e.items[0].email);
                $("select[name=documento]").val(e.items[0].doc);
            }else{
                $("input#docnum").val('');
                $("input#direccion").val('');
                $("input#clie_id").val('')
                $("input#rsocial").val('');
                $("input#email").val('');
                $("select[name=documento]").val(0);
            }
          }
        });
        
    })
    $('#desc_global').change(updateRow)


});
/* Productos */
function guardarComprobante(form){
    $.each($('.sortable .fact-item [dval]'),function(index,item){
        temp = $(item).attr('dval');
        $(item).attr('dval',$(item).val())
        $(item).val(temp)
    })
     $(form).formPost(true,{},function(data){
         if(data.exito==true){
            window.location.href = $('#baseurl').val()+'Comprobante/comp_listado';
         }else{
            $.each($('.sortable .fact-item [dval]'),function(index,item){
                temp = $(item).val();
                $(item).val($(item).attr('dval'))
                $(item).attr('dval',temp)
            });
            $('.error').removeClass('hidden').find('.text').html(data.mensaje);
            window.location.href = '#';
         }
    });
}
/*    EN CREAR PRODUCTO  */
function changeValor(valor) {
    precio = myRound(parseFloat(valor) + Math.m(valor,0.18));
    $('#prod-precio').val(precio);
    $('#prod-valor').dval(valor);
}
function changePrecio(precio) {
    valor = myRound(precio/1.18);
    $('#prod-valor').val(valor);
    $('#prod-precio').dval(precio);
}
        
    

function llenarrow(ar , padre){
    $.each(ar,function(item,val){
        if(item == 'pax' || item == 'lunch_efect')
            $item = padre.find('input[name="'+item+'[]"]').val((val));
        else
            $item = padre.find('input[name="'+item+'[]"]').dval((val));
    })
    
}

function tiponom(id){
    if(id>=10&&id<20) return 'GRAVADA';
    if(id>=20&&id<30) return 'EXONERADA';
    if(id>=30&&id<40) return 'INAFECTA';
    if(id>=40&&id<=40) return 'EXPORTACION';
}
function updateRow() {
    config = $(this).attr('name');
    if($(this).hasClass('text-right')) $(this).attr('dval',$(this).val()); ///importante
    var padre = $(this).closest('.fact-item'),
            descuento = padre.find('input[name="descuento_val[]"]').val(),
            adicion = padre.find('input[name="adicion_val[]"]').val(),
            cantidad = padre.find('input[name="pax[]"]').val(), //pax
            precio = padre.find('input[name="serv_prec[]"]').val(), //serv_prec
            lunch_prec = padre.find('input[name="lunch_prec[]"]').val(),
            lunch = padre.find('input[name="lunch_efect[]"]').val(),
            servicio = padre.find('select[name="servicio[]"]').val(),
            tipo = 'GRAVADA';

    if(!esNumeroPositivo(cantidad)) { cantidad = 1; padre.find('input[name="pax[]"]').val(1); }
    if(!esNumeroPositivo(lunch)) { lunch = 1; padre.find('input[name="lunch_efect[]"]').val(1); }
    if(!esNumeroPositivo(descuento)) { descuento = 0; padre.find('input[name="descuento_val[]"]').dval(0); }
    if(!esNumeroPositivo(adicion)) { adicion = 0; padre.find('input[name="adicion_val[]"]').dval(0); }
    if(!esNumeroPositivo(precio)) { precio = 0; padre.find('input[name="serv_prec[]"]').dval(0); }
    if(!esNumeroPositivo(lunch_prec)) { lunch_prec = 0; padre.find('input[name="lunch_prec[]"]').dval(0); }
        
    if($('input[name="tipo"]').val() == "PRIVADO"){
        var valor = parseFloat(precio) + parseFloat(adicion) - parseFloat(descuento);
        var valIgv = (tipo != 'GRAVADA') ? 0.00 : Math.m(valor, 0.18);
        var importe = valor + valIgv;    
    }else{
        var valor = Math.m(precio,cantidad) + Math.m(lunch,lunch_prec) + parseFloat(adicion) - parseFloat(descuento);
        var valIgv = (tipo != 'GRAVADA') ? 0.00 : Math.m(valor, 0.18);
        var importe = valor + valIgv;    
    }
	var fprecio = importe/cantidad;
    llenarrow({igv:valIgv,pax:cantidad,serv_prec:precio,lunch_efect:lunch,lunch_prec:lunch_prec,valor:valor,total:importe,fprecio:fprecio},padre)
    updateTotal();
}




function updateTotal(){
    var productos = $('.fact-item', '.fact-wrap'),
            gravadas = 0.00,
            exoneradas = 0.00,
            inafectas = 0.00,
            exportas = 0.00,
            subtotal = 0.00,
            descuentos = 0.00,
            gratuitas = 0.00,
            suma_igv = 0.00,
            total = 0.00;
            val = 0.00;
            impo = 0.00;
            desc_global = 0.00;
    productos.each(function () {
        var producto = $(this).find('input[name="nombre_grupo[]"]').dval(),
            //unidad = $(this).find('input[name="unidad[]"]').dval(),
            cantidad = $(this).find('input[name="pax[]"]').val(),
            descuento = 0.00,
            precio = $(this).find('input[name="serv_prec[]"]').dval(),
            igv = $(this).find('input[name="igv[]"]').dval(),
            lunch_prec = $(this).find('input[name="lunch_prec[]"]').dval(),
            importe = $(this).find('input[name="total[]"]').dval(),
            lunch = $(this).find('textarea[name="lunch_efect[]"]').val();

        if (producto != '' || cantidad != '' || precio != '' || importe != '') {

            if (esNumeroPositivo(importe)) {
                if ($(this).hasClass('error-producto')) {
                    $(this).removeClass('error-producto');
                }
                /*
                switch (tipo) {
                  case "GRAVADA":
                    gravadas+= parseFloat(valor*cantidad-descuento);
                    break;
                  case "EXONERADA":
                    exoneradas+= parseFloat(valor*cantidad);
                    break;
                  case "INAFECTA":
                    inafectas+= parseFloat(valor*cantidad);
                    break;
                  case "EXPORTACION":
                    exportas+= parseFloat(valor*cantidad);
                    break;
                }
                */
                
                total += parseFloat(importe);
                suma_igv += parseFloat(igv);
            } else {
                $(this).addClass('error-producto');
            }
        }
    });
    $("#total_sub").dval(total-suma_igv);
    $("#total_igv").dval(suma_igv);
    $("#total_total").dval(total);
}
