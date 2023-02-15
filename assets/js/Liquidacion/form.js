$(document).ready(function () {
    var baseurl = $("#baseurl").val();
	
	$('input[name="incluye_saldo"]').change(function(){
		/*
        if($(this).is(":checked")){
            $('.saldos').show(500);
        }else{
            $('.saldos').hide(500);
        }
		*/
        updateTotal();
    })
    $('#serie, #numero').attr("readonly",'');
    $('input[name="s_monto[]"]').change(function(){
        if(!esNumeroPositivo($(this).val())) { 
            $(this).dval(0);
        }else{
            $(this).dval($(this).val());
        }
        updateTotal();
    })
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
    /*
	actualiza_numeroClie($("#clie_id").val());
    function actualiza_numeroClie($clie_id){
        $.ajax({
          dataType: "json",
          url: baseurl + "liquidacion/nextnumclie/"+$clie_id+"/1",
          success: function(data){
            console.log(data);
            $('input[name="clie_numero"]').val(data.numero);
          }
        });
    }
    */
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
            habilitar($(this).closest('.fact-item'));
        }).on('select2:unselect', function (e) {
            //$(this).closest('.fact-item')
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
			console.log(elem);
            $nuevafila = $('#clonables .fact-item').clone();

            $nuevafila.find(".deta_servicio").SelectServicio();
            $nuevafila.find(".fecha").SelectFecha();

            $nuevafila.find(".deta_servicio").select2("trigger", "select", {
                data: {id: elem.serv_id, text: elem.serv_name}
            });
            $nuevafila.find('input[name="guia[]"]').val(elem.guia);
            $nuevafila.find('input[name="hotel[]"]').val(elem.hotel);
            $nuevafila.find('input[name="lunch[]"]').val(elem.lunch);
            $nuevafila.find('input[name="pdet_id[]"]').val(elem.pdet_id);
            $nuevafila.find('input[name="deta_id[]"]').val(elem.id);
            $nuevafila.find('input[name="cobrado_pax[]"]').dval(elem.cobrado_pax);
            

            $nuevafila.find('input[name="serv_id[]"]').val(elem.serv_id);
            $nuevafila.find('input[name="serv_name[]"]').val(elem.serv_name);
            $nuevafila.find('input[name="pax[]"]').val(elem.pax).on('change', updateRow);
            $nuevafila.find('input[name="fecha[]"]').val(elem.fecha);
            $nuevafila.find('input[name="nombre[]"]').val(elem.nombre);
			$nuevafila.find('input[name="tipo[]"]').val(elem.tipo);
            $nuevafila.find('input[name="serv_prec[]"]').dval(elem.serv_prec).on('change', updateRow);
            $nuevafila.find('input[name="lunch_efect[]"]').val(elem.lunch_efect).on('change', updateRow);
            $nuevafila.find('input[name="lunch_prec[]"]').dval(elem.lunch_prec).on('change', updateRow);
			
			$nuevafila.find('input[name="adicion[]"]').val(elem.deta_adic);
			$nuevafila.find('input[name="adicion_val[]"]').dval(elem.deta_adic_val).on('change', updateRow);
			$nuevafila.find('input[name="descuento[]"]').val(elem.deta_desc);
			$nuevafila.find('input[name="descuento_val[]"]').dval(elem.deta_desc_val).on('change', updateRow);
			
            $nuevafila.find('input[name="total[]"]').dval(elem.total);
            $nuevafila.appendTo('.sortable');
            habilitar($nuevafila);
            $nuevafila.find('input[name="pax[]"]').change();
            updateTotal();
        });
    }
    //}

    $('a.agregarfila').click(function () {
        $nuevafila = $('#clonables .fact-item').clone();
        $nuevafila.find(".deta_servicio").SelectServicio();
        $nuevafila.find(".fecha").SelectFecha();
        $nuevafila.hide();
        $nuevafila.appendTo('.sortable');
        $nuevafila.find('.formdrown ul li').click(function(){return false;})
        $nuevafila.find('input[name="pax[]"]').on('change', updateRow);
        $nuevafila.find('input[name="serv_prec[]"]').on('change', updateRow);
        $nuevafila.find('input[name="lunch_efect[]"]').on('change', updateRow);
        $nuevafila.find('input[name="lunch_prec[]"]').on('change', updateRow);
        $nuevafila.find('textarea[name="detalle[]"]').autosize();
        $nuevafila.fadeIn();
        updateTotal();
        return false;
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
            $("input#clie_id").val( $(this).find('#sclie_id').val() )
            //actualiza_numeroClie($(this).find('#sclie_id').val());
            dlg.modal('hide');
            return false;
        });
    };

    

    $('.searchclie').click(function () {
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
            window.location.href = $('#baseurl').val()+'Liquidacion/liqu_listado';
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
function updateRow() {
    if($(this).hasClass('text-right')) $(this).attr('dval',$(this).val()); ///importante
    var padre = $(this).closest('.fact-item'),
            pax = padre.find('input[name="pax[]"]').val(),
            serv_prec = padre.find('input[name="serv_prec[]"]').dval(),
            lunch_efect = padre.find('input[name="lunch_efect[]"]').val(),
            lunch_prec = padre.find('input[name="lunch_prec[]"]').dval(),
			adicion = padre.find('input[name="adicion_val[]"]').val(),
			descuento = padre.find('input[name="descuento_val[]"]').val(),
            servicio = padre.find('select[name="servicio[]"]').val(),
			tipo = padre.find('input[name="tipo[]"]').val(),
            cobrado_pax = padre.find('input[name="cobrado_pax[]"]').dval();


    if(!esNumeroPositivo(pax)) { pax = 0; padre.find('input[name="pax[]"]').dval(0); }
    if(!esNumeroPositivo(serv_prec)) { serv_prec = 0; padre.find('input[name="serv_prec[]"]').dval(0); }
    if(!esNumeroPositivo(lunch_efect)) { lunch_efect = 0; padre.find('input[name="lunch_efect[]"]').dval(0); }
    if(!esNumeroPositivo(lunch_prec)) { lunch_prec = 0; padre.find('input[name="lunch_prec[]"]').dval(0); }
    if(!esNumeroPositivo(cobrado_pax)) { cobrado_pax = 0; padre.find('input[name="cobrado_pax[]"]').dval(0); }
	if(!esNumeroPositivo(adicion)) { adicion = 0; padre.find('input[name="adicion_val[]"]').dval(0); }
	if(!esNumeroPositivo(descuento)) { descuento = 0; padre.find('input[name="descuento_val[]"]').dval(0); }

    if(tipo != 'PRIVADO'){
        var importe = Math.m(pax,serv_prec) + Math.m(lunch_efect,lunch_prec)-cobrado_pax + parseFloat(adicion) - parseFloat(descuento);
    }else{
        var importe = Math.m(1,serv_prec)-cobrado_pax + parseFloat(adicion) - parseFloat(descuento);
    }
    llenarrow({pax:pax,serv_prec:serv_prec,lunch_efect:lunch_efect,lunch_prec:lunch_prec,total:importe,adicion_val:adicion,descuento_val:descuento},padre)
    updateTotal();
}




function updateTotal(){
    var productos = $('.fact-item', '.fact-wrap'),
            total_total = 0.00;
    productos.each(function () {
        var serv_name = $(this).find('input[name="serv_name[]"]').val(),
            pax = $(this).find('input[name="pax[]"]').val(),
            serv_prec = $(this).find('input[name="serv_prec[]"]').dval(),
            lunch_efect = $(this).find('input[name="lunch_efect[]"]').val(),
            lunch_prec = $(this).find('input[name="lunch_prec[]"]').dval(),
            total = $(this).find('input[name="total[]"]').val();

        if (serv_name != '' || pax != '' || serv_prec != '' || lunch_efect != '' || lunch_prec != '') {
            if (esNumeroPositivo(total)) {
                total_total += parseFloat(total);
            } else {
                $(this).addClass('error-producto');
            }
        }
    });
	if($('input[name="incluye_saldo"]').is(":checked")){
        var saldos = $('input[name="s_monto[]"]');
        saldos.each(function(){
            $(this).dval($(this).val());
            if (esNumeroPositivo($(this).val())) {
				total_total += parseFloat($(this).val());
            }
        })
    }
    $("#total_total").dval(total_total);
}
function habilitar($this){
    $this.find("input[name='guia[]']").removeAttr('disabled');
    $this.find("input[name='hotel[]']").removeAttr('disabled');
    $this.find("input[name='lunch[]']").removeAttr('disabled');
    $this.find("input[name='pax[]']").removeAttr('disabled');
    $this.find("input[name='nombre[]']").removeAttr('disabled');
    $this.find("input[name='fecha[]']").removeAttr('disabled');
    $this.find("input[name='serv_prec[]']").removeAttr('disabled');
    $this.find("input[name='lunch_efect[]']").removeAttr('disabled');
    $this.find("input[name='lunch_prec[]']").removeAttr('disabled');
    $this.find("input[name='cobrado_pax[]']").removeAttr('disabled');
	$this.find("input[name='adicion[]']").removeAttr('disabled');
	$this.find("input[name='adicion_val[]']").removeAttr('disabled');
	$this.find("input[name='descuento[]']").removeAttr('disabled');
	$this.find("input[name='descuento_val[]']").removeAttr('disabled');
}