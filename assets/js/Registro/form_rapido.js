$(document).ready(function(){
    
    $('input.fecha').daterangepicker({
        singleDatePicker: true,
        locale: {
            format: 'DD/MM/YYYY'
        }
    });
    
    $('#moneda_local').on('change', function(){
        console.log("change_moneda");
        if($(this).val() == 'SOLES') $(".msimb").text("S/.");
        else $(".msimb").text("$");
    })
    $('input[name="t_monto[0]"]').on('change', function(){
        var monto = $(this).val();
        if(!esNumeroPositivo(monto) || monto == '') {$(this).dval(0); }
        else{$(this).dval(monto)}
    })

    var tercero = $('.tercero');
    var sel_tipo = $('select[name="tipo_serv[0]"]');
    tercero.hide();
    sel_tipo.on('change', function(){
        if($(this).val() == 'TERCERO') tercero.show();
        else {
            $('input[name="t_nombre[0]"]').val('');
            $('input[name="t_monto[0]"]').val('0.00');
            tercero.hide();
        }
    })

    $('.datepicker').daterangepicker({
        singleDatePicker: true,
        //minDate: moment(),
        locale: {
            format: 'DD/MM/YYYY'
        }
    });
    $('.timepicker').datetimepicker({
        format: 'LT'
    });

    $('#checkigv').change(function(){
        igv = 0;
        subtotal = $("#total_sub").val();
        total = 0;
        if($(this).prop('checked')){
            igv = Math.m(subtotal,0.18);
            $(this).val('SI');
        }else{
            $(this).val('NO');
        }
        total = parseFloat(subtotal) + parseFloat(igv);
        $("#total_sub").dval(subtotal);
        $("#total_igv").dval(igv);
        $("#total_total").dval(total);
    })
    
    //selectServicio($(".serv-item").find('.hotel'),"Contacto/buscar_hotel");
    
    $hot = $('select#hotel_local').select2({
        placeholder: 'Buscar hotel',
        dropdownParent: $("#mdl-local"),
        allowClear: true,
        width: '100%',
        language: "es",
        minimumInputLength: Infinity,
        ajax: {
            url: baseurl + "contacto/buscar_hotel",
            dataType: 'json',
            data: function(params) {
                return {
                    q: params.term,
                    p: params.page
                };
            },
            processResults: function(data, params) {
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
        escapeMarkup: function(markup) {
            return markup;
        },
        minimumInputLength: 0
    }).on("select2:select", function(e) {
        $(this).siblings('input').val(e.params.data.text);
    }).on('select2:unselect', function(e) {
        $(this).siblings('input').val("");
    });
    
    $sel = $('select#cliente_local').select2({
        placeholder: 'Buscar Contacto',
        width: '100%',
        dropdownParent: $("#mdl-local"),
        language: "es",
        minimumInputLength: Infinity,
        ajax: {
            url: baseurl + "Contacto/buscar_clie",
            dataType: 'json',
            data: function(params) {
                return {
                    q: params.term,
                    p: params.page,
                    t: $('#tipop').val()
                };
            },
            processResults: function(data, params) {
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
        escapeMarkup: function(markup) {
            return markup;
        },
        minimumInputLength: 0,
        templateResult: formatRepo, 
        templateSelection: formatRepoSelection,
    }).on("select2:select", function(e) {
        $('.deta_servicio, .deta_hotel').val(null).trigger('change');
        selectServicio($(".serv-item").find('.servicio'),"Registro/buscar_serv");
        actualiza_subservicio();
        $('input[name="clie_rsocial"]').val(e.params.data.text);
        $('input[name="clie_abrev"]').val(e.params.data.codigo);
        
    });
    
    if($('#clie_id').val() != '' ){
        var clie_id = $('#clie_id').val();
        var clie_rsocial = $('#clie_rsocial').val();
        var clie_codigo = $('#clie_codigo').val();
        var lunch_pre = $('#deta_lunch_pre').val();
        
        $('select#cliente_local').select2("trigger", "select", {
            data: { id: clie_id, text: clie_rsocial, codigo: clie_codigo, lunch_prec: lunch_pre}
        });
        if (detas.length > 0) {
            $('#moneda_local').change();
            $.each(detas, function(i, elem) {
                console.log(elem);
                var padre = $(".serv-item");
                if(elem.deta_hotel != null){
                    $('select[name="hotel[0]"]').select2("trigger", "select", {
                        data: { id: elem.deta_hote_id, text: elem.deta_hotel}
                    });
                }
                
                $('select[name="servicio[0]"]').val(elem.deta_serv_id);
                $('input[name="serv_nombre[0]"]').val(elem.deta_serv_name);
                
                actualiza_subservicio();
                $('input[name="sub_servname[0]"]').val(elem.deta_subserv_name);
                $('select[name="sub_servicio[0]"]').val(elem.deta_subserv_id);

                $('textarea[name="detalle[0]"]').val(elem.deta_descripcion);
                $('input[name="deta_fecha[0]"]').val(elem.deta_fechaserv);
                $('input[name="deta_guia[0]"]').val(elem.deta_guia);
                if(elem.deta_hora != "12:00 AM")
                    $('input[name="deta_hora[0]"]').val(elem.deta_hora);
                $('input[name="deta_lunch[0]"]').val(elem.deta_lunch).on('change', updateRow);
                $('input[name="deta_lunch_pre[0]"]').val(elem.deta_lunch_pre).on('change', updateRow);
                $('select[name="tipo_serv[0]"]').val(elem.deta_xcta);
                $('select[name="prioridad[0]"]').val(elem.deta_prioridad);
                if(elem.deta_xcta == 'TERCERO') tercero.show();
                
                $('input[name="t_nombre[0]"]').val(elem.deta_terc_nombre);
                $('input[name="t_monto[0]"]').val(elem.deta_terc_monto);

                $('input[name="pax[0]"]').val(elem.deta_pax).on('change', updateRow);
                $('input[name="precio[0]"]').val(elem.deta_precio).on('change', updateRow);
                $('input[name="importe[0]"]').val(elem.deta_total);
                $('input[name="deta_id[0]"]').val(elem.deta_id);
                $('input[name="bus[0]"]').val(elem.deta_bus);

                if(elem.adiciones.length > 0){
                    
                    $.each(elem.adiciones, function(i, add) {
                        $nuevafilaadic = $('#clonables .adides-item').clone();
                        
                        name = "adic_nombre[0][]";
                        precio = "adic_precio[0][]";
                        id = "adic_id[0][]";
                        find = ".adic-items";
                        
                        $nuevafilaadic.find('.adic_id').attr("name",id).val(add.padi_id);
                        $nuevafilaadic.find('.adides-precio').attr("name",precio).val(add.padi_monto).on('change', updateRow);
                        $nuevafilaadic.find('.adides-nombre').attr("name",name).val(add.padi_descripcion);

                        padre.find(find).append($nuevafilaadic);
                    });
                    
                }
                if(elem.descuentos.length > 0){
                    $.each(elem.descuentos, function(i, add) {
                        $nuevafilaadic = $('#clonables .adides-item').clone();

                        name = "desc_nombre[0][]";
                        precio = "desc_precio[0][]";
                        id = "desc_id[0][]";
                        find = ".desc-items";
                        $nuevafilaadic.find('.adic_id').attr("name",id).val(add.padi_id);
                        $nuevafilaadic.find('.adides-precio').attr("name",precio).val(add.padi_monto).on('change', updateRow);
                        $nuevafilaadic.find('.adides-nombre').attr("name",name).val(add.padi_descripcion);
                        padre.find(find).append($nuevafilaadic);
                    });
                }
                
            });
        }
    }
    /*
    $('#hotel_local').change(function(){
        if($(this).val() == '') nombre = '';
        else nombre = $('#hotel_local option:selected').text();
        $('input[name="hotel_nombre[0]"]').val(nombre);
    })
    */
    $('#servicio_local').change(function(){
        if($(this).val() == '') nombre = '';
        else nombre = $('.servicio option:selected').text();
        $('.serv_nombre').val(nombre);

        if($(this).val() != ''){
            $.ajax({
                type: "POST",
                dataType: "json",
                url: baseurl + "Registro/getServHora/"+$(this).val(),
                success: function(data) {
                    $.gs_loader.hide();
                    $('#deta_hora').val(data.hora);
                }
            });
        }
        actualizaPrecio();
        actualiza_subservicio();
    })
    $('input[name="pax[0]"]').on('change', updateRow);
    $('input[name="precio[0]"]').on('change', updateRow);
    $('input[name="deta_lunch[0]"]').on('change', updateRow);
    $('input[name="deta_lunch_pre[0]"]').on('change', updateRow);
    $('input[name="adicion_val[0]"]').on('change', updateRow);
    $('input[name="descuento_val[0]"]').on('change', updateRow);
    $('.btn-adides').on('click', adicionales);
    $('.newclient, .addservicio, .addhotel').on('click', clickCrear);
    $('.adides-precio').on('change', updateRow);

    $('.sub_servicio').change(change_subserv);
    
    $('#add-image').on('click', function(){
        var items = $('.files').find('.image-item');
        $nuevafila = $('#clonables .image-item').clone();
        $nuevafila.find('input[name="imagen"]').attr("name","imagen"+items.length);
        $nuevafila.appendTo('.files');
        $('#num_images').val(items.length+1);
        return false; 
    })
    

})
function clickCrear() {
    $(this).load_dialog({
        title: $(this).attr('title'),
        loaded: function(dlg) {
            $(dlg).find('form').submit(function() {
                $(dlg).find('.error').addClass('hidden')
                $(this).formPost(true, function(data) {
                    if (data.exito == false) {
                        alert(data.mensaje);
                    } else {
                        alert(data.mensaje);
                        selectServicio($('.serv-item').find('.servicio'),"Registro/buscar_serv");
                        dlg.modal('hide');
                    }
                });
                return false;
            });
        }
    });
    return false;
}
function adicionales(){
    $nuevafila = $('#clonables .adides-item').clone();
    var padre = $(this).closest(".serv-item");
    pos = padre.find('.posicion').val();
            
    if($(this).attr("data-val") == '1'){
        name = "adic_nombre["+pos+"][]";
        precio = "adic_precio["+pos+"][]";
        find = ".adic-items";
    }else{
        name = "desc_nombre["+pos+"][]";
        precio = "desc_precio["+pos+"][]";
        find = ".desc-items";
    }
    $nuevafila.find('.adides-precio').on('change', updateRow);
    $nuevafila.find('.adides-nombre').attr("name",name);
    $nuevafila.find('.adides-precio').attr("name",precio);
    padre.find(find).append($nuevafila);
    updateRow();
    return false;   
}
function actualizaPrecio(){
    var cliente = $('#cliente_local').val();
    var servicio = $('#servicio_local').val();
    $.ajax({
        type: "POST",
        dataType: "json",
        url: baseurl + "Registro/getServPrecio",
        data: {cliente: cliente, servicio: servicio},
        success: function(data) {
            $('input[name="deta_lunch_pre[0]"]').val(data.lunch_prec);
            $('input[name="precio[0]"]').val(data.precio).change();
        }
    });
}
function actualiza_subservicio(){
    var cliente = $('#cliente_local').val();
    var servicio = $('#servicio_local').val();
    $.ajax({
        type: "POST",
        dataType: "json",
        url: baseurl + "Registro/getSubServOptions",
        data: {cliente: cliente, servicio: servicio},
        success: function(data) {
            console.log(data);
            if(data.sub_serv != '' && data.factu == 'SI'){
                $(".sub_serv").show();
                $("#sub_servicio_local").html(data.sub_serv);
                $('input[name="sub_servname[0]"]').val("");    
            }else{
                $(".sub_serv").hide();
                $("#sub_servicio_local").html("");
            }
        }
    });
}
function change_subserv(){
    $.ajax({
        type: "POST",
        dataType: "json",
        url: baseurl + "Registro/getSubServ",
        data: {sub_servid: $(this).val()},
        success: function(data) {
            console.log(data);
            if(data != null){
                console.log("if");
                $('input[name="precio[0]"]').val(data.precio).change();
                $('input[name="sub_servname[0]"]').val(data.descripcion);
                $('select[name="moneda_local"]').val(data.moneda).change();
            }else{
                console.log("else");
                $('input[name="precio[0]"]').val("0.00").change();
                $('input[name="sub_servname[0]"]').val("");
            }

        }
    });
}
function updateRow() {
    if($(this).hasClass('text-right')) $(this).attr('dval',$(this).val()); ///importante
    var padre = $(this).closest('.serv-item');
            pos = padre.find('.posicion').val();
            pax = padre.find('input[name="pax[0]"]').val();
            precio = padre.find('input[name="precio[0]"]').val();
            deta_lunch = padre.find('input[name="deta_lunch[0]"]').val();
            deta_lunch_pre = padre.find('input[name="deta_lunch_pre[0]"]').val();
            adicion = padre.find('input[name="adic_precio[0][]"]');
            descuento = padre.find('input[name="desc_precio[0][]"]');
            

    if(!esNumeroPositivo(pax) || pax == '') { pax = 0; padre.find('input[name="pax[0]"]').dval(0); }
    if(!esNumeroPositivo(precio) || precio == '') { precio = 0; padre.find('input[name="precio[0]"]').dval(0); }
    if(!esNumeroPositivo(deta_lunch) || deta_lunch == '') { deta_lunch = 0; padre.find('input[name="deta_lunch[0]"]').dval(0); }
    if(!esNumeroPositivo(deta_lunch_pre) || deta_lunch_pre == '') { deta_lunch_pre = 0; padre.find('input[name="deta_lunch_pre[0]"]').dval(0); }
    
    var adiciones = 0;
    var descuentos = 0;
    var adic = [];
    var desc = [];
    if(adicion.length > 0){
        adicion.each(function(i, elem) {
            var numero = $(elem).val();
            if(!esNumeroPositivo(numero) || numero == '') { numero = 0; $(this).dval(0); }
            adiciones += parseFloat(numero);
            adic[i] = [$(this),numero];
        })
        for (var i = adic.length - 1; i >= 0; i--) {
            adic[i][0].dval(adic[i][1]);
        }
    }
    if(descuento.length > 0){
        descuento.each(function(i, elem) {
            var numero = $(elem).val();
            if(!esNumeroPositivo(numero) || numero == '') { numero = 0; $(this).dval(0); }
            descuentos += parseFloat(numero);
            desc[i] = [$(this),numero];
        })
        
        for (var i = desc.length - 1; i >= 0; i--) {
            desc[i][0].dval(desc[i][1]);
        }
    }

    var importe = 0;

    if (esNumeroPositivo(pax) && esNumeroPositivo(precio)) {
        importe = (pax * precio) + (deta_lunch * deta_lunch_pre) + (adiciones - descuentos);
    } else {
        padre.addClass('error-producto');
    }
    //////////////////////////////////////////////
    llenarrow({importe:importe,precio:precio,pax:pax, deta_lunch:deta_lunch, deta_lunch_pre:deta_lunch_pre},padre, pos)
    updateTotal();
    ///////////////////
}
function llenarrow(ar , padre){
    $.each(ar,function(item,val){
        if(item == 'deta_lunch' || item == 'pax')
            $item = padre.find('input[name="'+item+'[0]"]').val((val));
        else
            $item = padre.find('input[name="'+item+'[0]"]').dval((val));
    })
}
function updateTotal(){
    var productos = $('.serv-item'),
            subtotal = 0.00,
            igv = 0.00,
            total = 0.00;
    productos.each(function () {
        var servicio = $(this).find('.servicio').val(),
            cantidad = $(this).find('.pax').val(),
            importe = $(this).find('.importe').dval();
        if (servicio != '' || cantidad != '' || importe != '') {
            if (esNumeroPositivo(importe)) {
                if ($(this).hasClass('error-producto')) {
                    $(this).removeClass('error-producto');
                }
                subtotal += parseFloat(importe);
            } else {
                $(this).addClass('error-producto');
            }
        }
    });

    if($('#checkigv').prop('checked'))
        igv = Math.m(subtotal,0.18);
    total = subtotal + igv;
    $("#total_sub").dval(subtotal);
    $("#total_igv").dval(igv);
    $("#total_total").dval(total);
}
function formatRepo (repo) {
  var markup = "<strong>"+repo.codigo+":</strong> "+repo.text;
  return markup;
}

function formatRepoSelection (repo) {
  return "<strong>"+repo.codigo+":</strong> "+repo.text;
}
function selectServicio(campo,busca) {
    $.ajax({
        type: "GET",
        dataType: "json",
        url: baseurl + busca,
        data:{t:"RECEPTIVO", c:$('#cliente_local').val()},
        success: function(data) {
            var html = "<option value=''>* Seleccione</option>";
            $.each(data.items,function(i,elem){
                html += "<option value='"+elem.id+"'>"+elem.text+"</option>";
            })
            campo.html(html);
            actualizaPrecio();
        }
    });
}
