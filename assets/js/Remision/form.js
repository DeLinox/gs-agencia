$(document).ready(function () {
    var baseurl = $("#baseurl").val();
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
    $('select.cmb').select2({placeholder: 'Seleccione', minimumResultsForSearch: Infinity, width: '100%', allowClear: false});


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

    $('input.fecha').daterangepicker({
        singleDatePicker: true,
        locale: {
            format: 'DD/MM/YYYY'
        }
    });


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
            minimumInputLength: 0
        }).on("select2:select", function (e) {
            $(this).closest('.fact-item').find('input[name="cantidad[]"]').removeAttr('disabled');
            $(this).closest('.fact-item').find('input[name="unidad[]"]').removeAttr('disabled');
            $(this).closest('.fact-item').find('input[name="codigo[]"]').removeAttr('disabled');
            $(this).closest('.fact-item').find('input[name="unidad[]"]').val(e.params.data.unid_nombre);
            $(this).closest('.fact-item').find('input[name="codigo[]"]').val(e.params.data.codigo);
            $(this).closest('.fact-item').find("textarea").removeAttr('disabled');
            $(this).closest('.fact-item').find('textarea[name="detalle[]"]').val(e.params.data.text);
        }).on('select2:unselect', function (e) {
            $(this).closest('.fact-item').find('textarea[name="detalle[]"]').val('');
            $(this).closest('.fact-item').find('input[name="cantidad[]"]').attr('disabled', '');
            $(this).closest('.fact-item').find('input[name="unidad[]"]').attr('disabled', '');
            $(this).closest('.fact-item').find('input[name="codigo[]"]').attr('disabled', '');
            $(this).closest('.fact-item').find("textarea").attr('disabled', '');
        });
    }

    $.fn.SelectUbigeo = function () {
        console.log($(this).val());
        $(this).select2({
            placeholder: 'Buscar distrito',
            allowClear: true,
            width: '100%',
            language: "es",
            minimumInputLength: 2,
            ajax: {
                url: baseurl + "Remision/buscar_ubigeo",
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
            var tipo = $(this).closest('.ubigeo').attr("name");
            console.log(tipo);
            if(tipo == 'inicio') $('input[name="deta_inicio"]').val(e.params.data.text);
            else $('input[name="deta_fin"]').val(e.params.data.text);
        }).on('select2:unselect', function (e) {
            var tipo = $(this).closest('.ubigeo').attr("name");
            if(tipo == 'inicio') $('input[name="deta_inicio"]').val('');
            else $('input[name="deta_fin"]').val('');
        });
    }
    $(".ubigeo").SelectUbigeo();

    $.fn.SelectTransport = function () {
        $(this).select2({
            placeholder: 'Buscar transportista',
            allowClear: true,
            width: '100%',
            language: "es",
            ajax: {
                url: baseurl + "Remision/buscar_transportista",
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
            $('#placa_trans').removeAttr("disabled");
            $('#placa_trans').val(e.params.data.placa);
            $('#docnum_trans').val(e.params.data.docnum);
            $('#doc_trans').val(e.params.data.docu);
            $('#denom_trans').val(e.params.data.text);
        }).on('select2:unselect', function (e) {
            $('#placa_trans').attr("disabled", "");
            $('#placa_trans').val('');
            $('#docnum_trans').val("");
            $('#doc_trans').val("");
            $('#denom_trans').val("");
        });
    }
    $(".transportista").SelectTransport();

    $.fn.SelectConductor = function () {
        $(this).select2({
            placeholder: 'Buscar conductor',
            allowClear: true,
            width: '100%',
            language: "es",
            ajax: {
                url: baseurl + "Remision/buscar_conductor",
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
            $('#doc_conduc').val(e.params.data.doc);
            $('#docnum_conduc').val(e.params.data.docnum);
            $('#nombre_conduc').val(e.params.data.text);
        }).on('select2:unselect', function (e) {
            $('#doc_conduc').val("");
            $('#docnum_conduc').val("");
            $('#nombre_conduc').val("");
        });
    }
    $(".conductor").SelectConductor();

    //if($('#id_venta').val() != ''){
        if(productos.length > 0){
         $.each(productos,function(i,elem){
            $nuevafila = $('#clonables .fact-item').clone();
            
            console.log(elem);
            $nuevafila.find(".deta_producto").SelectProducto();

            $nuevafila.find(".deta_producto").select2("trigger", "select", {
                data: {id: elem.deta_prod_id, text: elem.deta_descripcion}
            });
            
            $nuevafila.find('textarea[name="detalle[]"]').val(elem.deta_descripcion);
            $nuevafila.appendTo('.sortable');
            $nuevafila.find('textarea[name="detalle[]"]').autosize();
            $nuevafila.find('input[name="deta_id[]"]').val(elem.deta_id);
            $nuevafila.find('input[name="cantidad[]"]').val(elem.deta_cantidad);
            $nuevafila.find('input[name="unidad[]"]').val(elem.deta_unidad);
            $nuevafila.find('input[name="codigo[]"]').val(elem.deta_codigo);
        });
         }
        
    //}
    if ($('#id_conductor').val() != ''){
        $('select#cond_id').select2("trigger", "select", {
            data: {id: $('#id_conductor').val(), text: $('#nombre_conduc').val(), doc: $('#doc_conduc').val(), docnum: $('#docnum_conduc').val()}
        });
        
    }
    if ($('#id_transportista').val() != ''){
        $('select#trans_id').select2("trigger", "select", {
            data: {id: $('#id_conductor').val(), text: $('#denom_trans').val(), placa: $('#placa_trans').val(), docnum: $('#docnum_trans').val(), docu: $('#doc_trans').val()}
        });
    }
    
    if ($('#id_inicio').val() != ''){
        $('select#inicio').select2("trigger", "select", {
            data: {id: $('#id_inicio').val(), text: $('#deta_inicio').val()}
        });
    }
    if ($('#id_fin').val() != ''){
        $('select#fin').select2("trigger", "select", {
            data: {id: $('#id_fin').val(), text: $('#deta_fin').val()}
        });
    }
    $('a.agregarfila').click(function () {
        $nuevafila = $('#clonables .fact-item').clone();
        $nuevafila.find(".deta_producto").SelectProducto();

        $nuevafila.hide();
        $nuevafila.appendTo('.sortable');
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
            script: baseurl + 'assets/js/Cliente/search.js?v=3',
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
                    $("select[name=documento]").eq(0).val(e.items[0].docu);
                }else{
                    /*$("input#docnum").val("");
                    $("input#direccion").val("");
                    $("input#rsocial").val("");
                    $("input#email").val("");
                    $("select[name=documento]").eq(0).select2('val', "0");*/
                }
          }
        });
        
    })


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
            if(data.from==true) window.location.href = $('#baseurl').val()+'Remision/ver/'+data.id+'/true';
            else window.location.href = $('#baseurl').val()+'Remision/listado/'+data.tipo;
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
function formatRepo (repo) {
  var markup = "<strong>"+repo.tipo+":</strong> "+repo.text+"</br><strong>"+repo.documento+":</strong> "+repo.docnum;

  return markup;
}

function formatRepoSelection (repo) {
  return repo.text;
}