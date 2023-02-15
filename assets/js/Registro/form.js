$(document).ready(function() {
    var baseurl = $("#baseurl").val();
    var posicion = 0;
    var envio = false;

    $(window).on('beforeunload', function(e) {
        if(!envio) {
            return 'Podria perder todos los datos de la reserva';
        }
    });


    $('.adicionales').hide();
    $('#checkigv').change(function(){
        igv = 0;
        subtotal = $("#total_sub").dval();
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
    
    
    $('input.fecha').daterangepicker({
        singleDatePicker: true,
        locale: {
            format: 'DD/MM/YYYY'
        }
    });

    $('#moneda').change(function(){
        if($(this).val() == 'SOLES') $('.msimb').text('S/');
        else $('.msimb').text('$');
    })
    

    /*
    $.fn.SelectServ = function() {
        $(this).select2({
            placeholder: 'Buscar servicio',
            allowClear: true,
            width: '100%',
            language: "es",
            minimumInputLength: Infinity,
            ajax: {
                url: baseurl + "Registro/buscar_serv",
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
            var check = $(this).closest('.serv-item').find('input[name="complementary[]"]');
            if( check.prop('checked') ) prec = '0.00';
            else prec = e.params.data.precio;

            $(this).closest('.input-group').find('input[name="serv_nombre[]"]').val(e.params.data.text);
            $(this).closest('.serv-item').find('input[name="precio[]"]').val(prec);
        }).on('select2:unselect', function(e) {
            $(this).closest('.input-group').find('input[name="serv_nombre[]"]').val('');
            $(this).closest('.serv-item').find('input[name="precio[]"]').val('0.00');
        });
    }

    $.fn.SelectEmba = function() {
        $(this).select2({
            placeholder: 'Buscar embarcacion',
            allowClear: true,
            width: '100%',
            language: "es",
            minimumInputLength: Infinity,
            ajax: {
                url: baseurl + "Registro/buscar_emba",
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
        });
    }
    $.fn.SelectGuia = function() {
        $(this).select2({
            placeholder: 'Buscar guia',
            allowClear: true,
            width: '100%',
            language: "es",
            minimumInputLength: Infinity,
            ajax: {
                url: baseurl + "contacto/buscar_guia",
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
            $(this).closest('.input-group').find('input[name="guia_nombre[]"]').val(e.params.data.text);
        }).on('select2:unselect', function(e) {
            $(this).closest('.input-group').find('input[name="guia_nombre[]"]').val('');
        });
    }
    */
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
    $.fn.SelectHotel = function() {
        console.log("selecciona hotel")
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
    
    
    $sel = $('select#cliente').select2({
        placeholder: 'Buscar Contacto',
        width: '100%',
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
        $('input[name="deta_lunch_pre[]"]').val('0.00');
        $('.precio').val('0.00').change();
        $('.lunch_prec').val('0.00').change();
        $('input[name="clie_rsocial"]').val(e.params.data.text);
        $('input[name="clie_abrev"]').val(e.params.data.codigo);
        $('input[name="cont_nombres"]').focus();
        $('.precio').change();
        hab_desahab($('.serv-deta'));
        selectServTodo();
    }).on('select2:unselect', function(e) {
        $('.deta_servicio').val(null).trigger('change');
        $('input[name="clie_rsocial"]').val('');
        $('input[name="clie_abrev"]').val('');
        hab_desahab($('.serv-deta'));
    });


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
     
    
    if($('#clie_id').val() != '' ){
        var clie_id = $('#clie_id').val();
        var clie_rsocial = $('#clie_rsocial').val();
        var clie_codigo = $('#clie_codigo').val();
        $('select#cliente').select2("trigger", "select", {
            data: { id: clie_id, text: clie_rsocial, codigo: clie_codigo}
        });

        if (detas.length > 0) {
            $.each(detas, function(i, elem) {
                console.log(elem);
                $nuevafila = $('#clonables .serv-item').clone();
                $nuevafila.find(".datepicker").settingDate();
                $nuevafila.find(".hotel").SelectHotel();
                $nuevafila.find(".timepicker").settingTime();
                $nuevafila.find('input[name="hotel_nombre[]"]').attr('name', "hotel_nombre["+posicion+"]");
                $nuevafila.find('select[name="hotel[]"]').attr('name', "hotel["+posicion+"]");
                if(elem.deta_hote_id){
                    $nuevafila.find('.hotel').select2("trigger", "select", {
                        data: { id: elem.deta_hote_id, text: elem.deta_hotel}
                    });                
                }
                
                
                $nuevafila.find('input[name="posicion[]"]').attr('name', "posicion["+posicion+"]").val(posicion);
                $nuevafila.find('input[name="serv_nombre[]"]').attr('name', "serv_nombre["+posicion+"]").val(elem.serv_descripcion);
                
                $nuevafila.find('select[name="servicio[]"]').attr('name', "servicio["+posicion+"]").val(elem.deta_serv_id).on('change', change_serv_nombre);
                actualiza_subservicio($nuevafila);
                $nuevafila.find('input[name="sub_servname[]"]').attr('name', "sub_servname["+posicion+"]").val(elem.deta_subserv_name);
                $nuevafila.find('select[name="sub_servicio[]"]').attr('name', "sub_servicio["+posicion+"]").val(elem.deta_subserv_id).on('change',change_subserv);
                $nuevafila.find('input[name="deta_id[]"]').val(elem.deta_id).attr('name', "deta_id["+posicion+"]");
                $nuevafila.find('input[name="deta_fecha[]"]').attr('name', "deta_fecha["+posicion+"]").val(elem.deta_fechaserv);
                $nuevafila.find('input[name="deta_fecha_llegada[]"]').attr('name', "deta_fecha_llegada["+posicion+"]").val(elem.deta_fecha_llegada);
                if(elem.deta_hora != "12:00 AM") hora = elem.deta_hora;
                else hora = '';
                $nuevafila.find('input[name="deta_hora[]"]').attr('name', "deta_hora["+posicion+"]").val(hora);
                $nuevafila.find('input[name="pax[]"]').attr('name', "pax["+posicion+"]").val(elem.deta_pax).on('change', updateRow);
                $nuevafila.find('input[name="precio[]"]').attr('name', "precio["+posicion+"]").dval(elem.deta_precio).on('change', updateRow);
                $nuevafila.find('input[name="deta_guia[]"]').attr('name', "deta_guia["+posicion+"]").val(elem.deta_guia);
                $nuevafila.find('input[name="deta_lunch[]"]').attr('name', "deta_lunch["+posicion+"]").val(elem.deta_lunch).on('change', updateRow);
                $nuevafila.find('input[name="deta_lunch_pre[]"]').attr('name', "deta_lunch_pre["+posicion+"]").dval(elem.deta_lunch_pre).on('change', updateRow);
                $nuevafila.find('select[name="tipo_serv[]"]').attr('name', "tipo_serv["+posicion+"]").val(elem.deta_xcta).on('change', change_tipo);

                if(elem.deta_xcta == 'TERCERO') $nuevafila.find('.tercero').show();
                $nuevafila.find('input[name="t_nombre[]"]').attr('name', "t_nombre["+posicion+"]").val(elem.deta_terc_nombre);
                $nuevafila.find('input[name="t_monto[]"]').attr('name', "t_monto["+posicion+"]").dval(elem.deta_terc_monto).on('change',change_valTercero);

                if(elem.deta_hotelchk == "SI")
                    $nuevafila.find('input[name="chk_hotel[]"]').prop('checked',true);
                $nuevafila.find('input[name="chk_hotel[]"]').attr('name', "chk_hotel["+posicion+"]");
                
                $nuevafila.find('input[name="importe[]"]').attr('name', "importe["+posicion+"]").dval(elem.deta_total).on('change', updateRow);
                $nuevafila.find('textarea[name="detalle[]"]').attr('name', "detalle["+posicion+"]").autosize().val(elem.deta_descripcion);
                $nuevafila.find('input[name="bus[]"]').attr('name', "bus["+posicion+"]").val(elem.deta_bus);
                $nuevafila.find('input[name="bus_salida[]"]').attr('name', "bus_salida["+posicion+"]").val(elem.deta_bus_salida);
                $nuevafila.find('select[name="prioridad[]"]').attr('name', "prioridad["+posicion+"]").val(elem.deta_prioridad);
                $nuevafila.find('select[name="color[]"]').attr('name', "color["+posicion+"]").val(elem.deta_color);
                
                $nuevafila.find('.addservicio').on('click', clickCrear);
                $nuevafila.find('.addhotel').on('click', clickCrear);
                $nuevafila.find('.btn-adides').on('click', adicionales);
                $nuevafila.find('.btn-prov').on('click', proveedores);
                $nuevafila.find('input[name="precio[]"]').change();

                $nuevafila.appendTo('.sortable');
                if(elem.proveedores.length > 0){
                    $.each(elem.proveedores, function(i, add) {
                        console.log(add);
                        $nuevafilaadic = $('#clonables .prov-item').clone();

                        find = ".prov-items";
                        $nuevafilaadic.find('.prov_tipo').attr("name","prov_tipo["+posicion+"][]").SelectTServ();
                        $nuevafilaadic.find('.prov_id').attr("name","prov_id["+posicion+"][]").SelectProv();
                        $nuevafilaadic.find('.prov_paqu_id').attr("name","prov_paqu_id["+posicion+"][]").val(add.sepr_id);
                        $nuevafilaadic.find('.prov_descripcion').attr("name","prov_descripcion["+posicion+"][]").val(add.sepr_servicio);
                        $nuevafilaadic.find('.prov_cantidad').attr("name","prov_cantidad["+posicion+"][]").val(add.sepr_cantidad);
						$nuevafilaadic.find('.prov_moneda').attr("name","prov_moneda["+posicion+"][]").val(add.sepr_moneda);
                        $nuevafilaadic.find('.prov_precio').attr("name","prov_precio["+posicion+"][]").dval(add.sepr_precio).on('change',val_dval);
                        $nuevafilaadic.find('.prov_tipo').select2("trigger", "select", {
                            data: { id: add.tipo_id, text: add.tipo_denom}
                        });
                        if(add.prov_id && add.prov_id != "0"){
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
        //$nuevafila.find('input[name="posicion[]"]').attr("name","posicion["+posicion+"]");

        $nuevafila.find('.addservicio').on('click', clickCrear);
        $nuevafila.find('.addguia').on('click', clickCrear);
        $nuevafila.find('.addhotel').on('click', clickCrear);
        $nuevafila.find('.btn-adides').on('click', adicionales);
        $nuevafila.find('.btn-prov').on('click', proveedores);
        

        $nuevafila.find('input[name="posicion[]"]').attr('name', "posicion["+posicion+"]").val(posicion);
        $nuevafila.find('input[name="deta_id[]"]').attr('name', "deta_id["+posicion+"]");
        $nuevafila.find('input[name="serv_nombre[]"]').attr('name', "serv_nombre["+posicion+"]");
        $nuevafila.find('select[name="servicio[]"]').attr('name', "servicio["+posicion+"]").on('change', change_serv_nombre);
        $nuevafila.find('input[name="chk_hotel[]"]').attr('name', "chk_hotel["+posicion+"]");
        $nuevafila.find('input[name="deta_fecha[]"]').attr('name', "deta_fecha["+posicion+"]");
        $nuevafila.find('input[name="deta_hora[]"]').attr('name', "deta_hora["+posicion+"]");
        $nuevafila.find('input[name="pax[]"]').attr('name', "pax["+posicion+"]").on('change', updateRow);
        $nuevafila.find('input[name="precio[]"]').attr('name', "precio["+posicion+"]").on('change', updateRow);
        $nuevafila.find('input[name="hotel_nombre[]"]').attr('name', "hotel_nombre["+posicion+"]");
        $nuevafila.find('select[name="hotel[]"]').attr('name', "hotel["+posicion+"]").on('change', change_hotel_nombre);
        $nuevafila.find('input[name="deta_lunch[]"]').attr('name', "deta_lunch["+posicion+"]").on('change', updateRow);
        $nuevafila.find('input[name="deta_lunch_pre[]"]').attr('name', "deta_lunch_pre["+posicion+"]").on('change', updateRow);
        $nuevafila.find('select[name="tipo_serv[]"]').attr('name', "tipo_serv["+posicion+"]").on('change', change_tipo);
        $nuevafila.find('input[name="importe[]"]').attr('name', "importe["+posicion+"]").on('change', updateRow);
        $nuevafila.find('textarea[name="detalle[]"]').attr('name', "detalle["+posicion+"]").autosize();
        $nuevafila.find('input[name="t_nombre[]"]').attr('name', "t_nombre["+posicion+"]");
        $nuevafila.find('input[name="t_monto[]"]').attr('name', "t_monto["+posicion+"]").on('change',change_valTercero);
        $nuevafila.find('select[name="sub_servicio[]"]').attr('name', "sub_servicio["+posicion+"]").on('change',change_subserv);
        $nuevafila.find('select[name="color[]"]').attr('name', "color["+posicion+"]");

        $nuevafila.hide();
        $nuevafila.appendTo('.sortable');
        hab_desahab($nuevafila);
        $nuevafila.fadeIn();
        updateTotal();
        posicion++;
        return false;
    });

    if ($('.sortable .serv-item').length <= 0)
        $('a.agregarfila').click();

    procesarFormularioClient = function (dlg) {
        $(dlg).find('form').submit(function () {
            $(dlg).find('.error').addClass('hidden')
            $(this).formPost(true, function (data) {
                if (data.exito == false) {
                    alert(data.mensaje);
                } else {
                    alert(data.mensaje);
                    $("#cliente").select2("trigger", "select", {
                        data: {id: data.datos.clie_id, text: data.datos.clie_rsocial}
                    });
                    dlg.modal('hide');
                }
            });
            return false;
        });
    };
    
    $('.newclient').click(function () {
        $(this).load_dialog({
            title: $(this).attr('title'),
            loaded: procesarFormularioClient
        });
        return false;
    });

    procesarFormulario = function (dlg) {
        $(dlg).find('form').submit(function () {
            $(dlg).find('.error').addClass('hidden')
            $(this).formPost(true, function (data) {
                if (data.exito == false) {
                    alert(data.mensaje);
                } else {
                    alert(data.mensaje);
                    dlg.modal('hide');
                }
            });
            return false;
        });
    };
    /*
    $('.addservicio, .addguia, .addhotel').click(function () {
        $(this).load_dialog({
            title: $(this).attr('title'),
            loaded: procesarFormulario
        });
        return false;
    });
    */

    $('#vender').submit(function(e) {
        e.preventDefault();
        if(confirm("Guardar reserva?")){
            envio = true;
            guardarComprobante(this);
        }
    });
    function val_dval(){
        console.log($(this).val());
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
    function proveedores(){
        $nuevafila = $('#clonables .prov-item').clone();
        var padre = $(this).closest(".serv-item");
        pos = padre.find('.posicion').val();
        find = ".prov-items";
        $nuevafila.find('.prov_paqu_id').attr("name","prov_paqu_id["+pos+"][]");
        $nuevafila.find('.prov_id').attr("name","prov_id["+pos+"][]").SelectProv();
        $nuevafila.find('.prov_tipo').attr("name","prov_tipo["+pos+"][]").SelectTServ();
        $nuevafila.find('.prov_cantidad').attr("name","prov_cantidad["+pos+"][]");
		$nuevafila.find('.prov_moneda').attr("name","prov_moneda["+pos+"][]");
        $nuevafila.find('.prov_precio').attr("name","prov_precio["+pos+"][]").on('change',val_dval);
        $nuevafila.find('.prov_descripcion').attr("name","prov_descripcion["+pos+"][]");
        padre.find(find).append($nuevafila);
        return false;   
    }
    $('#add-image').on('click', function(){
        var items = $('.files').find('.image-item');
        $nuevafila = $('#clonables .image-item').clone();
        $nuevafila.find('input[name="imagen"]').attr("name","imagen"+items.length);
        $nuevafila.appendTo('.files');
        $('#num_images').val(items.length+1);
        return false; 
    })
});
function change_serv_nombre(){
    var padre = $(this).closest('.serv-item');
    pos = padre.find('.posicion').val();
    if($(this).val() == '') nombre = '';
    else nombre = padre.find('.servicio option:selected').text();
    padre.find('.serv_nombre').val(nombre);
    actualizaPrecio($('#cliente').val(), $(this).val(), padre);
    actualiza_subservicio(padre);
    if($(this).val() != ''){
        $.ajax({
            type: "POST",
            dataType: "json",
            url: baseurl + "Registro/getServHora/"+$(this).val(),
            success: function(data) {
                padre.find('.timepicker').val(data.hora)
                
            }
        });
    }
}
function actualiza_subservicio(padre){
    var cliente = $('#cliente').val();
    var servicio = padre.find('.servicio').val();
    $.ajax({
        type: "POST",
        dataType: "json",
        async: false,
        url: baseurl + "Registro/getSubServOptions",
        data: {cliente: cliente, servicio: servicio},
        success: function(data) {
            if(data.sub_serv != '' && data.factu == 'SI'){
                padre.find(".sub_serv").show();
                padre.find(".sub_servicio").html(data.sub_serv);
                padre.find('input[name="sub_servname[]"]').val("");    
            }else{
                padre.find(".sub_serv").hide();
                padre.find(".sub_servicio").html("");
            }
        }
    });
}
function actualizaPrecio(contacto, servicio, padre){
    if(cliente != '' && servicio != ''){
        $.ajax({
            type: "POST",
            dataType: "json",
            url: baseurl + "Registro/getServPrecio",
            data: {cliente: contacto, servicio: servicio},
            success: function(data) {
                padre.find('.lunch_prec').val(data.lunch_prec);
                padre.find('.precio').val(data.precio).change();
                $('#moneda').val(data.moneda).change();
            }
        });
    }else{
        padre.find('.precio').val('0.00');
    }
}
function change_hotel_nombre(){
    var padre = $(this).closest('.serv-item');
    pos = padre.find('.posicion').val();
    if($(this).val() == '') nombre = '';
    else nombre = padre.find('.hotel option:selected').text();
    padre.find('.hotel_nombre').val(nombre);
}
function updateRow() {
    if($(this).hasClass('importe')) ttl = '1';
    else ttl = '0';
    if($(this).hasClass('text-right')) $(this).attr('dval',$(this).val()); ///importante
    var padre = $(this).closest('.serv-item');
            pos = padre.find('.posicion').val();
            pax = padre.find('input[name="pax['+pos+']"]').val();
            precio = padre.find('input[name="precio['+pos+']"]').val();
            deta_lunch = padre.find('input[name="deta_lunch['+pos+']"]').val();
            deta_lunch_pre = padre.find('input[name="deta_lunch_pre['+pos+']"]').val();
            adicion = padre.find('input[name="adic_precio['+pos+'][]"]');
            descuento = padre.find('input[name="desc_precio['+pos+'][]"]');
            importe = padre.find('input[name="importe['+pos+']"]').val();
            

    if(!esNumeroPositivo(pax) || pax == '') { pax = 0; padre.find('input[name="pax['+pos+']"]').dval(0); }
    if(!esNumeroPositivo(precio) || precio == '') { precio = 0; padre.find('input[name="precio['+pos+']"]').dval(0); }
    if(!esNumeroPositivo(deta_lunch) || deta_lunch == '') { deta_lunch = 0; padre.find('input[name="deta_lunch['+pos+']"]').dval(0); }
    if(!esNumeroPositivo(deta_lunch_pre) || deta_lunch_pre == '') { deta_lunch_pre = 0; padre.find('input[name="deta_lunch_pre['+pos+']"]').dval(0); }
    if(ttl == '1'){
        if (esNumeroPositivo(pax) && esNumeroPositivo(precio)) {
            //importe = (pax * precio) + (deta_lunch * deta_lunch_pre) + (adiciones - descuentos);
            if(pax > 0){
                precio = importe / pax;
            }else{
                alert("Imposible division por 0 (pax)");
                importe = 0;
            }
            padre.find('.adic-items').html('');
            padre.find('.desc-items').html('');
        } else {
            padre.addClass('error-producto');
        }
    }else{
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
        var importe;
        if (esNumeroPositivo(pax) && esNumeroPositivo(precio)) {
            importe = (pax * precio) + (deta_lunch * deta_lunch_pre) + (adiciones - descuentos);
        } else {
            padre.addClass('error-producto');
        }
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
/* Productos */
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
                window.location.href = baseurl+"Registro/paq_listado/";
            }     
        }
    })
    return false;
    /*

    $(form).formPost(true, {}, function(data) {
        if (data.exito == true) {
            
            window.location.href = data.url;
        } else {
            $('.error').removeClass('hidden').find('.text').html(data.mensaje);
            window.location.href = '#';
        }
    });
    */
}


function hab_desahab($padre){
    cliente = $('#cliente').val();
    if (cliente == '' || cliente == null) {
        $padre.find('input, select, textarea').attr('disabled','disabled');    
    }else{
        $padre.find('input, select, textarea').removeAttr('disabled');
    }
}
function updateTotal(){

    var productos = $('.serv-item', '.serv-wrap'),
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
function change_tipo() {
    console.log("cambia");
    var padre = $(this).closest(".serv-item");
    if($(this).val() == 'TERCERO') padre.find('.tercero').show();
    else {
        padre.find('.t_nombre').val('');
        padre.find('.t_monto').val('0.00');
        padre.find('.tercero').hide();
    }
}
function change_valTercero(){
    var monto = $(this).val();
    if(!esNumeroPositivo(monto) || monto == '') {$(this).dval(0); }
    else{$(this).dval(monto)}
}

function selectServTodo() {

    $.ajax({
        type: "GET",
        dataType: "json",
        async: false,
        url: baseurl + "Registro/buscar_serv",
        data:{t:"RECEPTIVO", c:$('#cliente').val()},
        success: function(data) {
            var html = "<option value=''>* Seleccione</option>";
            $.each(data.items,function(i,elem){
                html += "<option value='"+elem.id+"'>"+elem.text+"</option>";
            })
            $('.servicio').html(html);
        }
    });
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