$(document).ready(function(){
	$.fn.selDate = function () {
		$(this).daterangepicker({
	        singleDatePicker: true,
	        locale: {
	            format: 'DD/MM/YYYY'
	        }
	    });	
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
	
	var moneda = $('select[name="moneda"]');
	monedaChange();
	moneda.on('change', monedaChange);	
	function monedaChange(){
		if(moneda.val() == 'SOLES') $('.simbolo').text('S/');
		else $('.simbolo').text('$');	
	}

	$.fn.SelectTServ = function() {
        $(this).select2({
            placeholder: 'Buscar servicio',
            allowClear: true,
            width: '100%',
            language: "es",
            minimumInputLength: Infinity,
            ajax: {
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
            $(this).closest('.input-group').find('input[name="serv_nombre[]"]').val(e.params.data.text);
            $(this).closest('.serv-item').find('input[name="precio[]"]').val(e.params.data.precio);
            /*
            $(this).closest('.serv-item').find('select[name="proveedor[]"]').select2("trigger", "select", {
                data: { id: '', text: '' }
            });
            */
        }).on('select2:unselect', function(e) {
            $(this).closest('.input-group').find('input[name="serv_nombre[]"]').val('');
            $(this).closest('.serv-item').find('input[name="precio[]"]').val('0.00');
            /*
            $(this).closest('.serv-item').find('select[name="proveedor[]"]').select2("trigger", "select", {
                data: { id: '', text: '' }
            });
            */
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
                url: baseurl + "Contacto/sbuscar_prov",
                dataType: 'json',
                data: function(params) {
                    return {
                        q: params.term,
                        p: params.page,
                        s: $(this).closest('.adic-item').find('select[name="tservicio[]"]').val()
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
            $(this).closest('.input-group').find('input[name="serv_nombre[]"]').val(e.params.data.text);
            $(this).closest('.serv-item').find('input[name="precio[]"]').val(e.params.data.precio);
        }).on('select2:unselect', function(e) {
            $(this).closest('.input-group').find('input[name="serv_nombre[]"]').val('');
            $(this).closest('.serv-item').find('input[name="precio[]"]').val('0.00');
        });
    }

	
	$('.fecha').selDate();
	
	if(adic.length > 0){
		$.each(adic, function(i, elem) {
			console.log(elem);
			$nuevafila = $('#clonables .adic-item').clone();
	        $nuevafila.hide();
	        $nuevafila.appendTo('.serv_adicionales');
	        $nuevafila.find(".adic_tservicio").SelectTServ();
	        $nuevafila.find(".adic_proveedor").SelectProv();
            $nuevafila.find(".datepicker").settingDate();
            $nuevafila.find(".timepicker").settingTime();

	        $nuevafila.find(".adic_tservicio").select2("trigger", "select", {
                data: { id: elem.sepr_tipo, text: elem.tipo_denom }
            });
            $nuevafila.find(".adic_proveedor").select2("trigger", "select", {
                data: { id: elem.sepr_prov_id, text: elem.prov_rsocial }
            });
            $nuevafila.find('input[name="add_guia[]"]').val(elem.sepr_guia);
            $nuevafila.find('input[name="add_fecha[]"]').val(elem.sepr_fecha);
            $nuevafila.find('input[name="add_hora[]"]').val(elem.sepr_hora);

            $nuevafila.find('input[name="adic_id[]"]').val(elem.sepr_id);
            $nuevafila.find('input[name="adicional_cant[]"]').val(elem.sepr_cantidad).on('change', updateRow);
            $nuevafila.find('input[name="adicional_precio[]"]').val(elem.sepr_precio).on('change', updateRow);
            $nuevafila.find('input[name="adicional_deta[]"]').val(elem.sepr_servicio);

            $nuevafila.find('select[name="moneda[]"]').val(elem.sepr_moneda).on('change', updateSimb);
            //$nuevafila.find('select[name="estado[]"]').val(elem.ordv_adic_estado);
            updateSimb();
	        
	        $nuevafila.fadeIn();
	        
		});
	}else{
		$('.agregaradic').click();	
	} 


	
    $('.agregaradic').click(function() {
    	
        $nuevafila = $('#clonables .adic-item').clone();
        $nuevafila.hide();
        $nuevafila.appendTo('.serv_adicionales');
        $nuevafila.find(".datepicker").settingDate();
        $nuevafila.find(".timepicker").settingTime();
        
        $nuevafila.find(".adic_tservicio").SelectTServ();
        $nuevafila.find(".adic_proveedor").SelectProv();
        $nuevafila.find('input[name="adicional_cant[]"]').on('change', updateRow);
        $nuevafila.find('input[name="adicional_precio[]"]').on('change', updateRow);
        //$nuevafila.find(".borrarItem").on('click', deleteItem);
        
        $nuevafila.fadeIn();
        return false;
    });

    $sel = $('select#servicio').select2({
        placeholder: 'Buscar Servicio',
        width: '100%',
        language: "es",
        minimumInputLength: Infinity,
        ajax: {
            url: baseurl + "Registro/buscar_serv",
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
        minimumInputLength: 0,
    }).on("select2:select", function(e) {
        $('#serv_id').val(e.params.data.id);
    	$('#serv_name').val(e.params.data.text);
    }).on('select2:unselect', function(e) {
        $('#serv_id').val('');
    	$('#serv_name').val('');
    });
    $('#frm-gen-orden').submit(function(e) {
        e.preventDefault();
        if(confirm("Desea guardar la orden?")){
            guardarComprobante(this);        
        }
    });

    if($('#serv_id').val() != ''){
    	var id = $('#serv_id').val();
    	var txt = $('#serv_name').val();
	    $("#servicio").select2("trigger", "select", {
	        data: { id: id, text: txt }
	    });
    }


})
function updateSimb() {
    var padre = $(this).closest('.adic-item'),
            moneda = padre.find('select[name="moneda[]"]').val();
    if(moneda != 'SOLES')
        padre.find('.simbolo').text('$');
    else
        padre.find('.simbolo').text('S/');
}
function updateRow() {
    config = $(this).attr('name');
    if($(this).hasClass('text-right')) $(this).attr('dval',$(this).val()); ///importante
    var padre = $(this).closest('.adic-item'),
            cant = padre.find('input[name="adicional_cant[]"]').val(),
            precio = padre.find('input[name="adicional_precio[]"]').val();

    if(!esNumeroPositivo(cant) || cant == '') { cant = 0; padre.find('input[name="adicional_cant[]"]').val(0); }
    if(!esNumeroPositivo(precio) || precio == '') { precio = 0; padre.find('input[name="adicional_precio[]"]').dval(0); }
    
    
    llenarrow({adicional_cant:cant,adicional_precio:precio},padre)
}
function llenarrow(ar , padre){
    $.each(ar,function(item,val){
        if(item == 'adicional_cant')
            $item = padre.find('input[name="'+item+'[]"]').val((val));
        else
            $item = padre.find('input[name="'+item+'[]"]').dval((val));
    })
}
function deleteItem() {
	$this = $(this);
	if(confirm("Desea eliminar el item?")){
		$this.parents('.serv-item').fadeOut('slow', function() {
            $this.parents('.serv-item').remove();
        });
    }
}
function guardarComprobante(form) {
    $.each($('.sortable .serv-item [dval]'), function(index, item) {
        temp = $(item).attr('dval');
        $(item).attr('dval', $(item).val())
        $(item).val(temp)
    })
    $.each($('.adic-item [dval]'), function(index, item) {
        temp = $(item).attr('dval');
        $(item).attr('dval', $(item).val())
        $(item).val(temp)
    })
    $(form).formPost(true, {}, function(data) {
        if (data.exito == true) {
            window.location.href = data.url;
        } else {
            $('.error').removeClass('hidden').find('.text').html(data.mensaje);
            window.location.href = '#';
        }
    });
}

