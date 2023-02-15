$(document).ready(function () {
    var baseurl = $("#baseurl").val();
    
    $('select.cmb').select2({placeholder: 'Seleccione', minimumResultsForSearch: Infinity, width: '100%', allowClear: false});
	
    
    $('#serie').change(function(){
        changeSerie($('#comprobante').val(),$(this).val());
    })

     $('#moneda').change(function(){
        if($(this).val()=='DOLARES') $('.msimb').text('$');
        else $('.msimb').text('S/');
     });
     $('#moneda').change();


    function changeSerie($tipo,$serie){
        $.ajax({
          dataType: "json",
          url: baseurl + "Venta/getnext/"+$tipo+"/"+$serie,
          success: function(data){
            $('#numero').val(data.numero);
          }
        });
    }

     $('#detraccion').change(function(){
        if( $(this).prop('checked') ) {

        }else{

        }
    })

    $('#exterior').change(function(){
        if( $(this).prop('checked') ) {
            $('select[name="tipo[]"]').val('40').change();
        }else{
            $('select[name="tipo[]"]').val('10').change();
        }
    })

    $.fn.SelectProducto = function () {
        $(this).select2({
            placeholder: 'Buscar producto',
            allowClear: true,
            width: '100%',
            language: "es",
            minimumInputLength: Infinity,
            ajax: {
                url: baseurl + "Producto/buscar",
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
            minimumInputLength: 0,
            templateResult: formatRepo, 
             templateSelection: formatRepoSelection 
        }).on("select2:select", function (e) {
            $(this).closest('.fact-item').find('input[name="cantidad[]"]').removeAttr('disabled');
            $(this).closest('.fact-item').find('input[name="descuento[]"]').removeAttr('disabled');
            $(this).closest('.fact-item').find('input[name="precio[]"]').removeAttr('disabled');
            $(this).closest('.fact-item').find('input[name="valor[]"]').removeAttr('disabled');
            $(this).closest('.fact-item').find('select[name="gratuita[]"]').removeAttr('disabled');
            $(this).closest('.fact-item').find('select[name="tipo[]"]').removeAttr('disabled');
            $(this).closest('.fact-item').find('input[name="codigo[]"]').removeAttr('disabled').val(e.params.data.codigo);
            $(this).closest('.fact-item').find('select[name="unidad[]"]').val(e.params.data.unid_nombre)
            $(this).closest('.fact-item').find('input[name="precio[]"]').dval(myRound(e.params.data.precio));
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
            $(this).closest('.fact-item').find('input[name="codigo[]"]').attr('disabled', '').val('');
            $(this).closest('.fact-item').find('input[name="valor[]"]').dval(myRound(0));
            $(this).closest('.fact-item').find('input[name="valor[]"]').change()
            updateTotal();
            $(this).closest('.fact-item').find("textarea").attr('disabled', '');
        });
    }

    $('input.fecha').daterangepicker({
        singleDatePicker: true,
        locale: {
                format: 'DD/MM/YYYY'
            }});

    function clickCrearProducto() {
        $(this).load_dialog({
            title: $(this).attr('title'),
            loaded: function (dlg) {
                $(dlg).find('form').submit(function () {
                    $(dlg).find('.error').addClass('hidden')
                    $(this).formPost(true, function (data) {
                        if (data.exito == false) {

                        } else {
                            $('select#producto').select2("trigger", "select", {
                                data: {id: data.datos.prod_id, text: data.datos.prod_nombre}
                            });
                            dlg.modal('hide');
                        }
                    });
                    return false;
                });
            }
        });
        return false;
    }

    if($('#id_cotizacion').val() != ''){

         $.each(productos,function(i,elem){

            console.log(elem);

            $nuevafila = $('#clonables .fact-item').clone();
            
            $nuevafila.find(".deta_producto").SelectProducto();
            elem.deta_detalle = elem.deta_descripcion;

            $nuevafila.find(".deta_producto").select2("trigger", "select", {
                data: {id: elem.deta_prod_id, text: elem.deta_descripcion}
            });
            
            $nuevafila.find('textarea[name="detalle[]"]').val(elem.deta_detalle);
            $nuevafila.find('select[name="tipo[]"]').val(elem.deta_afec_id);
            $nuevafila.find('.crearproducto').on('click', clickCrearProducto);
            $nuevafila.appendTo('.sortable');
            $nuevafila.find('textarea[name="detalle[]"]').autosize();
            $nuevafila.find('input[name="deta_id[]"]').val(elem.deta_id);
            $nuevafila.find('input[name="cantidad[]"]').dval(elem.deta_cantidad).on('change', updateRow);
            $nuevafila.find('input[name="precio[]"]').dval(elem.deta_precio).on('change', updateRow);
            $nuevafila.find('input[name="descuento[]"]').dval(elem.deta_descuento).on('change', updateRow);
            $nuevafila.find('input[name="valor[]"]').dval(elem.deta_valor).on('change', updateRow);
            $nuevafila.find('input[name="igv[]"]').dval(elem.deta_igv);
            $nuevafila.find('select[name="unidad[]"]').val(elem.deta_unidad);
            $nuevafila.find('input[name="codigo[]"]').val(elem.deta_codigo);
            $nuevafila.find('input[name="importe[]"]').dval(elem.deta_importe);
            $nuevafila.find('select[name="gratuita[]"]').val(elem.deta_esgratuita);

            $nuevafila.find('.formdrown ul li').click(function(){return false;})

            $nuevafila.find('input[name="precio[]"]').change();

            updateTotal();
            $nuevafila.find('select[name="gratuita[]"]').on('change', updateRow);
            $nuevafila.find('select[name="tipo[]"]').on('change', updateRow);
          
    });
    }

    $('a.agregarfila').click(function () {
        $nuevafila = $('#clonables .fact-item').clone();
        $nuevafila.find(".deta_producto").SelectProducto();
        $nuevafila.find('.crearproducto').on('click', clickCrearProducto);
        $nuevafila.hide();
        $nuevafila.appendTo('.sortable');
        $nuevafila.find('.formdrown ul li').click(function(){return false;})
        $nuevafila.find('input[name="precio_ini[]"]').dval('0.00');
        $nuevafila.find('input[name="cantidad[]"]').on('change', updateRow);
        $nuevafila.find('input[name="precio[]"]').on('change', updateRow);
        $nuevafila.find('input[name="descuento[]"]').on('change', updateRow);
        $nuevafila.find('input[name="valor[]"]').on('change', updateRow);
        $nuevafila.find('select[name="gratuita[]"]').on('change', updateRow);
        $nuevafila.find('select[name="tipo[]"]').val($('#exterior').prop('checked')?'40':'10');
        $nuevafila.find('select[name="tipo[]"]').on('change', updateRow);
        $nuevafila.find('textarea[name="detalle[]"]').autosize();
        $nuevafila.fadeIn();
        return false;
    });

    if ($('.sortable .fact-item').length <= 0)
        $('a.agregarfila').click();
    
    procesarFormulario = function (dlg) {
        $(dlg).find('form').submit(function () {
            $(dlg).find('.error').addClass('hidden')
            $(this).formPost(true, function (data) {
                if (data.exito == false) {

                } else {
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
            loaded: procesarFormulario
        });
        return false;
    });

    buscarCliente = function (dlg) {
        $(dlg).find('form').submit(function () {
            $("input#docnum").val( $(this).find('#sdocnum').val() )
            $("input#clie_id").val( $(this).find('#sclie_id').val() )
            $("input#direccion").val( $(this).find('#sdireccion').val() )
            $("input#rsocial").val( $(this).find('#srsocial').val() )
            $("input#email").val( $(this).find('#semail1').val() )
            $("select[name=documento]").eq(0).val($(this).find('select[name=sdocumento]').val());
            dlg.modal('hide');
            return false;
        });
    };
    $('.searchclient').click(function () {
        $(this).load_dialog({
            title: $(this).attr('title'),
            script: baseurl + 'assets/js/Cliente/search.js?v=6',
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
    $('#cotizar').submit(function(e){
        e.preventDefault();
        /*var limite = ($('#moneda').val() == 'SOLES') ? limiteSoles : limiteDolares;
        var total = $('#total_total').val();
        */
        guardarComprobante(this);

    });
    $('#completar').click(function(){
        doc_num = $('#docnum').val();
        $.ajax({
          dataType: "json",
          url: baseurl + "Cliente/buscar",
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
                    $("select[name=documento]").eq(0).select2('val', e.items[0].docu);
                }else{
                    $("input#docnum").val("");
                    $("input#direccion").val("");
                    $("input#clie_id").val("");
                    $("input#rsocial").val("");
                    $("input#email").val("");
                    $("select[name=documento]").eq(0).select2('val', "0");
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
            if(data.from==true) window.location.href = $('#baseurl').val()+'Cotizacion/ver/'+data.id+'/true';
            else window.location.href = $('#baseurl').val()+'Cotizacion/listado/#0';
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
            descripcion = padre.find('input[name="text_producto[]"]').dval(),
            cantidad = padre.find('input[name="cantidad[]"]').dval(),
            descuento = padre.find('input[name="descuento[]"]').dval(),
            precio = padre.find('input[name="precio[]"]').dval(),
            valor = padre.find('input[name="valor[]"]').dval(),
            tipo = tiponom(padre.find('select[name="tipo[]"]').val()),
            gratuita = padre.find('select[name="gratuita[]"]').val();

    if(!esNumeroPositivo(cantidad)) { cantidad = 1; padre.find('input[name="cantidad[]"]').dval(1); }
    if(!esNumeroPositivo(descuento)) { descuento = 0; padre.find('input[name="descuento[]"]').dval(myRound(0)); }

    if(config=='tipo[]'){
        precio = (tipo != 'GRAVADA') ? valor : parseFloat(valor) + Math.m(valor,0.18);
    }

    if(config=='gratuita[]'){
        if(gratuita=='SI') padre.find('select[name="tipo[]"]').val('21');
        else padre.find('select[name="tipo[]"]').val('10');
    }
    //console.log(precio)
    //alert(config)
    if(config=='valor[]'){
        if (esNumeroPositivo(valor)) {
            precio = (tipo != 'GRAVADA') ? valor : valor*1.18;
        } else {
            padre.addClass('error-producto');
        }
    }
    //////////////////////////////////////////////
    if(config=='precio[]'){
        if (esNumeroPositivo(precio)) {
            valor = (tipo != 'GRAVADA') ? precio : precio / 1.18;
        } else {
            padre.find('input[name="valor[]"]').dval(myRound(0));
            padre.addClass('error-producto');
        }
    }

    var valIgv = (tipo != 'GRAVADA') ? 0.00 : Math.m(Math.m(valor,cantidad) - descuento, 0.18);
    var importe = Math.m(valor,cantidad) + (valIgv) - (descuento);
    llenarrow({igv:valIgv,precio:precio,valor:valor,importe:importe},padre)
    ///////////////////

    updateTotal();
}




function updateTotal()
{
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
            desc_global = $('#desc_global').val();
    productos.each(function () {
        var producto = $(this).find('input[name="producto[]"]').dval(),
            //unidad = $(this).find('input[name="unidad[]"]').dval(),
            cantidad = $(this).find('input[name="cantidad[]"]').dval(),
            descuento = $(this).find('input[name="descuento[]"]').dval(),
            precio = $(this).find('input[name="precio[]"]').dval(),
            igv = $(this).find('input[name="igv[]"]').dval(),
            valor = $(this).find('input[name="valor[]"]').dval(),
            importe = $(this).find('input[name="importe[]"]').dval(),
            gratuita = $(this).find('select[name="gratuita[]"]').val(),
            tipo = tiponom($(this).find('select[name="tipo[]"]').val()),
            detalle = $(this).find('textarea[name="detalle[]"]').val();

        if (producto != '' || cantidad != '' || precio != '' || importe != '' || detalle != '') {

            if(gratuita=='SI') gratuitas += parseFloat(valor*cantidad);

            if (esNumeroPositivo(importe) && gratuita!='SI') {
                if ($(this).hasClass('error-producto')) {
                    $(this).removeClass('error-producto');
                }
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
                total += parseFloat(importe);
                val += Math.m(valor,cantidad);
                subtotal += Math.m(valor,cantidad);
                descuentos += parseFloat(descuento);
                suma_igv += parseFloat(igv);
            } else {
                $(this).addClass('error-producto');
            }
        }
    });

    var valorTotal = val - descuentos;
    var importeTotal = myRound(parseFloat(valorTotal) + parseFloat(suma_igv));



    $("#total_gravadas").dval(gravadas-gravadas*(desc_global/100));
    $("#total_exoneradas").dval(exoneradas-exoneradas*(desc_global/100));
    $("#total_gratuitas").dval(gratuitas);
    $("#total_inafectas").dval(inafectas-inafectas*(desc_global/100));
    $("#total_exportas").dval(exportas);
    $("#total_sub").dval(subtotal);
    $("#total_descuentos").dval(descuentos+valorTotal*(desc_global/100));
    $("#total_valor").dval(valorTotal);
    $("#total_igv").dval($("#total_gravadas").dval()*0.18);
    sum = $("#total_gravadas").dval()*1+$("#total_exoneradas").dval()*1+$("#total_inafectas").dval()*1+$("#total_exportas").dval()*1+$("#total_igv").dval()*1;
    $("#total_total").dval(sum);
}
function formatRepo (repo) {
  var markup = "<strong>"+repo.codigo+":</strong> "+repo.text;
  return markup;
}

function formatRepoSelection (repo) {
  return "<strong>"+repo.codigo+":</strong> "+repo.text;
}