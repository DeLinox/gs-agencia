$(document).ready(function(){
    var posicion = 0;
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
    
    $.fn.SelectServ = function() {
        $(this).select2({
            placeholder: 'Buscar servicio',
            allowClear: true,
            width: '100%',
            language: "es",
            minimumInputLength: Infinity,
            ajax: {
                url: baseurl + "Registro/buscar_servPrivado",
                dataType: 'json',
                data: function(params) {
                    return {
                        q: params.term,
                        p: params.page,
                        c: $("#cliente_local").val()
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
            console.log(e.params.data);
            $(this).closest('.form-group').find('.serv_nombre').val(e.params.data.text);
            $(this).closest('.form-group').find('.hora').val(e.params.data.hora);
            if(e.params.data.precio && e.params.data.precio != '') precio = e.params.data.precio;
            else precio = 0;
            $(this).closest('.serv-item').find('.precio').dval(precio);
        }).on('select2:unselect', function(e) {
            $(this).closest('.form-group').find('.serv_nombre').val('');
            $(this).closest('.serv-item').find('.precio').dval(0);
            $(this).closest('.form-group').find('.hora').val("");
        });
    }
    $.fn.SelectHotel = function() {
        $(this).select2({
            placeholder: 'Buscar hotel',
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
            $(this).closest('.input-group').find('.hotel_nombre').val(e.params.data.text);
        }).on('select2:unselect', function(e) {
            $(this).closest('.input-group').find('.hotel_nombre').val('');
        });
    }
    $.fn.SelectTServ = function() {
        $(this).select2({
            placeholder: 'Buscar servicio',
            allowClear: true,
            width: '100%',
            language: "es",
            minimumInputLength: Infinity,
            ajax: {
                async: false,
                url: baseurl + "Registro/buscar_tserv",
                dataType: 'json',
                data: function(params) {
                    return {
                        q: params.term,
                        p: params.page,
                        c: $('#cliente').val()
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
            $(this).closest('.prov-item').find('.prov_id').removeAttr("disabled");
            $(this).closest('.prov-item').find('.prov_id').select2("trigger", "select", {
                data: { id: '', text: ''}
            });
        }).on('select2:unselect', function(e) {
            $(this).closest('.prov-item').find('.prov_id').addAttr("disabled");
            $(this).closest('.prov-item').find('.prov_id').select2("trigger", "select", {
                data: { id: '', text: ''}
            });
        });
    }
    $.fn.SelectProv = function() {
        $(this).select2({
            placeholder: 'Buscar proveedor',
            allowClear: true,
            width: '100%',
            language: "es",
            minimumInputLength: Infinity,
            ajax: {
                async: false,
                url: baseurl + "Contacto/sbuscar_prov",
                dataType: 'json',
                data: function(params) {
                    return {
                        q: params.term,
                        p: params.page,
                        s: $(this).closest('.prov-item').find('.prov_tipo').val()
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
            
        }).on('select2:unselect', function(e) {
            
        });
    }
    function proveedores(){
        $nuevafila = $('#clonables .prov-item').clone();
        var padre = $(this).closest(".serv-item");
        pos = padre.find('.posicion').val();
        find = ".prov-items";
        $nuevafila.find('.prov_paqu_id').attr("name","prov_paqu_id["+pos+"][]");
        $nuevafila.find('.prov_id').attr("name","prov_id["+pos+"][]").SelectProv();
        $nuevafila.find('.prov_tipo').attr("name","prov_tipo["+pos+"][]").SelectTServ();
        $nuevafila.find('.prov_cantidad').attr("name","prov_cantidad["+pos+"][]");
        $nuevafila.find('.prov_precio').attr("name","prov_precio["+pos+"][]").on('change',val_dval);
        $nuevafila.find('.prov_descripcion').attr("name","prov_descripcion["+pos+"][]");
		$nuevafila.find('.prov_moneda').attr("name","prov_moneda["+pos+"][]");
        padre.find(find).append($nuevafila);
        return false;   
    }
    function clickCrear() {
        var actual = $(this);
        $(this).load_dialog({
            title: $(this).attr('title'),
            loaded: function(dlg) {
                $(dlg).find('form').submit(function() {
                    $(dlg).find('.error').addClass('hidden')
                    $(this).formPost(true, function(data) {
                        if (data.exito == false) {

                        } else {
                            alert(data.mensaje);
                            selectServicio(actual);                    
                            dlg.modal('hide');
                        }
                    });
                    return false;
                });
            }
        });
        return false;
    }
    $.fn.settingDate = function() {
        $this = $(this);
        $(this).daterangepicker({
            singleDatePicker: true,
            //minDate: moment(),
            locale: {
                format: 'DD/MM/YYYY'
            }
        });
    }
    $.fn.settingTime = function() {
        $(this).datetimepicker({
            format: 'LT'
        });
    }

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
        $('input[name="clie_rsocial"]').val(e.params.data.text);
        $('input[name="clie_abrev"]').val(e.params.data.codigo);
        hab_desahab($('.serv-deta'));
    }).on('select2:unselect', function(e) {
        $('input[name="clie_rsocial"]').val('');
        $('input[name="clie_abrev"]').val('');
        hab_desahab($('.serv-deta'));
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
                $nuevafila = $('#clonables .serv-item').clone();
                $nuevafila.find(".hotel").SelectHotel();
                $nuevafila.find(".datepicker").settingDate();
                $nuevafila.find(".timepicker").settingTime();
                $nuevafila.find(".servicio").SelectServ();
                $nuevafila.find('.addservicio').on('click', clickCrear);

                if(elem.deta_hote_id){
                    $nuevafila.find('.hotel').select2("trigger", "select", {
                        data: { id: elem.deta_hote_id, text: elem.deta_hotel}
                    });                
                }
                if(elem.deta_serv_id){
                    $nuevafila.find('.servicio').select2("trigger", "select", {
                        data: { id: elem.deta_serv_id, text: elem.deta_serv_name}
                    });                
                }

                $nuevafila.find('input[name="posicion[]"]').attr('name', "posicion["+posicion+"]").val(posicion);
                $nuevafila.find('input[name="deta_id[]"]').attr('name', "deta_id["+posicion+"]").val(elem.deta_id);
                $nuevafila.find('select[name="embarcacion[]"]').attr('name', "embarcacion["+posicion+"]").val(elem.deta_emba_id).on('change', change_embarcacion_nombre)
                $nuevafila.find('input[name="emba_name[]"]').attr('name', "emba_name["+posicion+"]").val(elem.deta_emba_name);
                $nuevafila.find('input[name="deta_fecha[]"]').attr('name', "deta_fecha["+posicion+"]").val(elem.deta_fechaserv);
                $nuevafila.find('input[name="deta_fecha_llegada[]"]').attr('name', "deta_fecha_llegada["+posicion+"]").val(elem.deta_fecha_llegada);
                if(elem.deta_hora != "12:00 AM") hora = elem.deta_hora;
                else hora = '';
                $nuevafila.find('input[name="deta_hora[]"]').attr('name', "deta_hora["+posicion+"]").val(hora);
                $nuevafila.find('input[name="pax[]"]').attr('name', "pax["+posicion+"]").val(elem.deta_pax).on('change', updateRow);
                $nuevafila.find('input[name="precio[]"]').attr('name', "precio["+posicion+"]").dval(elem.deta_precio).on('change', updateRow);
                $nuevafila.find('input[name="deta_guia[]"]').attr('name', "deta_guia["+posicion+"]").val(elem.deta_guia);
                $nuevafila.find('input[name="deta_ruta[]"]').attr('name', "deta_ruta["+posicion+"]").val(elem.deta_ruta);
                $nuevafila.find('input[name="deta_lunch[]"]').attr('name', "deta_lunch["+posicion+"]").val(elem.deta_lunch).on('change', updateRow);
                $nuevafila.find('input[name="deta_lunch_pre[]"]').attr('name', "deta_lunch_pre["+posicion+"]").dval(elem.deta_lunch_pre).on('change', updateRow);
                $nuevafila.find('select[name="tipo_serv[]"]').attr('name', "tipo_serv["+posicion+"]").val(elem.deta_xcta);
                $nuevafila.find('input[name="importe[]"]').attr('name', "importe["+posicion+"]").dval(elem.deta_total);
                $nuevafila.find('input[name="deta_lugar[]"]').attr('name', "deta_lugar["+posicion+"]").val(elem.deta_lugar);
                $nuevafila.find('textarea[name="detalle[]"]').attr('name', "detalle["+posicion+"]").val(elem.deta_descripcion).autosize();
                $nuevafila.find('select[name="deta_prioridad[]"]').attr('name', "deta_prioridad["+posicion+"]").val(elem.deta_prioridad);

                $nuevafila.appendTo('.sortable');
                if(elem.proveedores.length > 0){
                    $.each(elem.proveedores, function(i, add) {
                        
                        $nuevafilaadic = $('#clonables .prov-item').clone();
                        find = ".prov-items";
                        $nuevafilaadic.find('.prov_tipo').attr("name","prov_tipo["+posicion+"][]").SelectTServ();
                        $nuevafilaadic.find('.prov_id').attr("name","prov_id["+posicion+"][]").SelectProv();
                        $nuevafilaadic.find('.prov_paqu_id').attr("name","prov_paqu_id["+posicion+"][]").val(add.sepr_id);
                        
                        $nuevafilaadic.find('.prov_cantidad').attr("name","prov_cantidad["+posicion+"][]").val(add.sepr_cantidad);
                        $nuevafilaadic.find('.prov_descripcion').attr("name","prov_descripcion["+posicion+"][]").val(add.sepr_servicio);
						$nuevafilaadic.find('.prov_moneda').attr("name","prov_moneda["+posicion+"][]").val(add.sepr_moneda);
                        $nuevafilaadic.find('.prov_precio').attr("name","prov_precio["+posicion+"][]").dval(add.sepr_precio).on('change',val_dval);
                        $nuevafilaadic.find('.prov_tipo').select2("trigger", "select", {
                            data: { id: add.tipo_id, text: add.tipo_denom}
                        });
                        if(add.prov_id && add.prov_id != "0" && add.prov_rsocial != ""){
                            $nuevafilaadic.find('.prov_id').select2("trigger", "select", {
                                data: { id: add.prov_id, text: add.prov_rsocial}
                            });    
                        }
                        
                        
                        $nuevafila.find(find).append($nuevafilaadic);
                    });
                }
                if(elem.adiciones.length > 0){
                    $.each(elem.adiciones, function(i, add) {
                        $nuevafilaadic = $('#clonables .adides-item').clone();

                        name = "adic_nombre["+posicion+"][]";
                        precio = "adic_precio["+posicion+"][]";
                        id = "adic_id["+posicion+"][]";
                        find = ".adic-items";
                        $nuevafilaadic.find('.adic_id').attr("name",id).val(add.padi_id);
                        $nuevafilaadic.find('.adides-precio').attr("name",precio).val(add.padi_monto).on('change', updateRow);
                        $nuevafilaadic.find('.adides-nombre').attr("name",name).val(add.padi_descripcion);
                        $nuevafila.find(find).append($nuevafilaadic);    
                    });
                }
                if(elem.descuentos.length > 0){
                    $.each(elem.descuentos, function(i, add) {
                        $nuevafilaadic = $('#clonables .adides-item').clone();

                        name = "desc_nombre["+posicion+"][]";
                        precio = "desc_precio["+posicion+"][]";
                        id = "desc_id["+posicion+"][]";
                        find = ".desc-items";
                        $nuevafilaadic.find('.adic_id').attr("name",id).val(add.padi_id);
                        $nuevafilaadic.find('.adides-precio').attr("name",precio).val(add.padi_monto).on('change', updateRow);
                        $nuevafilaadic.find('.adides-nombre').attr("name",name).val(add.padi_descripcion);
                        $nuevafila.find(find).append($nuevafilaadic);    
                    });
                }
                $nuevafila.find('.addhotel').on('click', clickCrear);
                $nuevafila.find('.btn-adides').on('click', adicionales);
                $nuevafila.find('.btn-prov').on('click', proveedores);
                updateTotal();
                posicion++;
            });
        }
    }
    $('a.agregarfila').click(function() {
        
        $nuevafila = $('#clonables .serv-item').clone();
        //$nuevafila.find(".deta_servicio").SelectServ();
        
        $nuevafila.find(".hotel").SelectHotel();
        $nuevafila.find(".datepicker").settingDate();
        $nuevafila.find(".timepicker").settingTime();
        $nuevafila.find(".servicio").SelectServ();
        $nuevafila.find('.addservicio').on('click', clickCrear);
        //$nuevafila.find('input[name="posicion[]"]').attr("name","posicion["+posicion+"]");

        $nuevafila.find('.addhotel').on('click', clickCrear);
        $nuevafila.find('.btn-adides').on('click', adicionales);
        $nuevafila.find('.btn-prov').on('click', proveedores);
        
        $nuevafila.find('select[name="embarcacion[]"]').attr('name', "embarcacion["+posicion+"]").on('change', change_embarcacion_nombre);

        $nuevafila.find('input[name="posicion[]"]').attr('name', "posicion["+posicion+"]").val(posicion);
        $nuevafila.find('input[name="deta_id[]"]').attr('name', "deta_id["+posicion+"]");
        $nuevafila.find('input[name="serv_nombre[]"]').attr('name', "serv_nombre["+posicion+"]");
        $nuevafila.find('select[name="servicio[]"]').attr('name', "servicio["+posicion+"]");
        
        $nuevafila.find('input[name="deta_fecha[]"]').attr('name', "deta_fecha["+posicion+"]");
        $nuevafila.find('input[name="deta_fecha_llegada[]"]').attr('name', "deta_fecha_llegada["+posicion+"]");
        $nuevafila.find('input[name="deta_hora[]"]').attr('name', "deta_hora["+posicion+"]");
        $nuevafila.find('input[name="pax[]"]').attr('name', "pax["+posicion+"]").on('change', updateRow);
        $nuevafila.find('input[name="precio[]"]').attr('name', "precio["+posicion+"]").on('change', updateRow);
        $nuevafila.find('input[name="hotel_nombre[]"]').attr('name', "hotel_nombre["+posicion+"]");
        $nuevafila.find('select[name="hotel[]"]').attr('name', "hotel["+posicion+"]").on('change', change_hotel_nombre);
        $nuevafila.find('input[name="deta_lunch[]"]').attr('name', "deta_lunch["+posicion+"]").on('change', updateRow);
        $nuevafila.find('input[name="deta_lunch_pre[]"]').attr('name', "deta_lunch_pre["+posicion+"]").on('change', updateRow);
        $nuevafila.find('select[name="tipo_serv[]"]').attr('name', "tipo_serv["+posicion+"]");
        $nuevafila.find('input[name="importe[]"]').attr('name', "importe["+posicion+"]").on('change', updateRow);
        $nuevafila.find('textarea[name="detalle[]"]').attr('name', "detalle["+posicion+"]").autosize();
        /*
        $nuevafila.find('input[name="t_nombre[]"]').attr('name', "t_nombre["+posicion+"]");
        $nuevafila.find('input[name="t_monto[]"]').attr('name', "t_monto["+posicion+"]").on('change',change_valTercero);
        $nuevafila.find('select[name="sub_servicio[]"]').attr('name', "sub_servicio["+posicion+"]").on('change',change_subserv);
        */

        $nuevafila.hide();
        $nuevafila.appendTo('.sortable');
        hab_desahab($nuevafila);
        $nuevafila.fadeIn();
        posicion++;
        return false;
    });
    
    if ($('.sortable .serv-item').length <= 0)
        $('a.agregarfila').click();


    $('#add-image').on('click', function(){
        var items = $('.files').find('.image-item');
        $nuevafila = $('#clonables .image-item').clone();
        $nuevafila.find('input[name="imagen"]').attr("name","imagen"+items.length);
        $nuevafila.appendTo('.files');
        $('#num_images').val(items.length+1);
        return false; 
    })

    $('#frm-gen_orden').submit(function(e) {
        e.preventDefault();
        if(confirm("Guardar reserva?")){
            envio = true;
            guardarComprobante(this);
        }
    });
})

function val_dval(){
    $(this).dval($(this).val());
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
function updateRow() {
    if($(this).hasClass('text-right')) $(this).attr('dval',$(this).val()); ///importante
    var padre = $(this).closest('.serv-item');
            pos = padre.find('.posicion').val();
            pax = padre.find('input[name="pax['+pos+']"]').val();
            precio = padre.find('input[name="precio['+pos+']"]').val();
            deta_lunch = padre.find('input[name="deta_lunch['+pos+']"]').val();
            deta_lunch_pre = padre.find('input[name="deta_lunch_pre['+pos+']"]').val();
            adicion = padre.find('input[name="adic_precio['+pos+'][]"]');
            descuento = padre.find('input[name="desc_precio['+pos+'][]"]');
            

    if(!esNumeroPositivo(pax) || pax == '') { pax = 0; padre.find('input[name="pax['+pos+']"]').dval(0); }
    if(!esNumeroPositivo(precio) || precio == '') { precio = 0; padre.find('input[name="precio['+pos+']"]').dval(0); }
    if(!esNumeroPositivo(deta_lunch) || deta_lunch == '') { deta_lunch = 0; padre.find('input[name="deta_lunch['+pos+']"]').dval(0); }
    if(!esNumeroPositivo(deta_lunch_pre) || deta_lunch_pre == '') { deta_lunch_pre = 0; padre.find('input[name="deta_lunch_pre['+pos+']"]').dval(0); }
    
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
        //importe = (pax * precio) + (deta_lunch * deta_lunch_pre) + (adiciones - descuentos);
        importe = parseFloat(precio) + (adiciones - descuentos);
    } else {
        padre.addClass('error-producto');
    }
    //////////////////////////////////////////////
    llenarrow({importe:importe,precio:precio,pax:pax, deta_lunch:deta_lunch, deta_lunch_pre:deta_lunch_pre},padre, pos)
    updateTotal();
    ///////////////////
}
function llenarrow(ar , padre, pos){
    $.each(ar,function(item,val){
        if(item == 'deta_lunch' || item == 'pax')
            $item = padre.find('input[name="'+item+'['+pos+']"]').val((val));
        else
            $item = padre.find('input[name="'+item+'['+pos+']"]').dval((val));
    })
}
function guardarComprobante(form) {
    $.each($('.sortable .serv-item [dval]'), function(index, item) {
        temp = $(item).attr('dval');
        $(item).attr('dval', $(item).val())
        $(item).val(temp)
    })

    var formData = new FormData($(form)[0]);
    $.ajax({
        dataType: "json",
        url: $(form).attr("action"),
        type: $(form).attr("method"),
        data: formData,
        cache: false,
        contentType: false,
        processData: false,
        success: function(data){
            if (data.exito == false) {
                window.location.href = '#';
                $('.error').removeClass('hidden').find('.text').html(data.mensaje);
            } else {
                window.location.href = baseurl+"Registro/paq_listado";
            }     
        }
    })
    return false;
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


function change_embarcacion_nombre(){
    var padre = $(this).closest('div');
    if($(this).val() == '') nombre = '';
    else nombre = padre.find('.emba_id option:selected').text();
    padre.find('.emba_name').val(nombre);
}
function change_hotel_nombre(){
    var padre = $(this).closest('.serv-item');
    pos = padre.find('.posicion').val();
    if($(this).val() == '') nombre = '';
    else nombre = padre.find('.hotel option:selected').text();
    padre.find('.hotel_nombre').val(nombre);
}
function selectServicio($this) {
    var objeto = $this.parent().siblings('select');
    var buscar, ini;
    if(objeto.hasClass('hotel') || objeto.hasClass('servicio')){
        if(objeto.hasClass('hotel')){
            buscar = "Contacto/buscar_hotel";
            ini = "hotel";
        }else if(objeto.hasClass('servicio')){
            buscar = "Registro/buscar_serv";
            ini = "Servicio";
        }
        $.ajax({
            type: "GET",
            dataType: "json",
            url: baseurl + buscar,
            data:{t:"RECEPTIVO"},
            success: function(data) {
                var html = "<option>* "+ini+"</option>";
                $.each(data.items,function(i,elem){
                    html += "<option value='"+elem.id+"'>"+elem.text+"</option>";
                })
                objeto.html(html);
            }
        });
    }
}
/*
function change_tipo() {
    var padre = $(this).closest(".serv-item");
    if($(this).val() == 'TERCERO') padre.find('.tercero').show();
    else {
        padre.find('.t_nombre').val('');
        padre.find('.t_monto').val('0.00');
        padre.find('.tercero').hide();
    }
}
*/
function change_valTercero(){
    var monto = $(this).val();
    if(!esNumeroPositivo(monto) || monto == '') {$(this).dval(0); }
    else{$(this).dval(monto)}
}
function change_subserv(){
    var padre = $(this).closest('.serv-item');
    
    $.ajax({
        type: "POST",
        dataType: "json",
        async: false,
        url: baseurl + "Registro/getSubServ",
        data: {sub_servid: $(this).val()},
        success: function(data) {
            if(data != null){
                padre.find('.precio').val(data.precio).change();
                padre.find('input[name="sub_servname[]"]').val(data.descripcion);
                padre.find('select[name="moneda"]').val(data.moneda).change();
            }else{
                padre.find('.precio').al("0.00").change();
                padre.find('input[name="sub_servname[]"]').val("");
            }

        }
    });
}
function hab_desahab($padre){
    cliente = $('#cliente_local').val();
    if (cliente == '' || cliente == null) {
        $padre.find('input, select, textarea').attr('disabled','disabled');    
    }else{
        $padre.find('input, select, textarea').removeAttr('disabled');
    }
}
function formatRepo (repo) {
  var markup = "<strong>"+repo.codigo+":</strong> "+repo.text;
  return markup;
}

function formatRepoSelection (repo) {
  return "<strong>"+repo.codigo+":</strong> "+repo.text;
}
