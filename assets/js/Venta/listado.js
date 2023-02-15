var url = "";
$(document).on('ready', function() {
    var url = $("#nameurl").val() + '?json=true';
    var $table;
    

    function botones(id, estado, mail, pagado, comp, nc, alm, $ar) {
        esini = (estado == 1 || estado == 4);
        eslis = (estado == 1 || estado == 4);
        esval = (estado == 2);
        esmal = (estado == 3);
        esmai = (mail == 'NO');

        if(esmal)$ar.closest('tr').css({textDecoration:'line-through',color:'red'});
        
        espag = pagado;
        esalm = alm;
        
        html = `<div class='btn-group'>
          <a href='{baseurl}Venta/vercomp/{id}' class='btn btn-success btn-sm vercomp'><span class='glyphicon glyphicon-eye-open'></span></a>
          <button type='button' class='btn btn-success btn-sm dropdown-toggle' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'>
            <span class='caret'></span>
            <span class='sr-only'>Toggle Dropdown</span>
          </button>
          <ul class='dropdown-menu'>
            
            <li class="show_editar oculto"><a href='{baseurl}Venta/edit/{id}' class='editar'><span class='glyphicon glyphicon-pencil'></span> Editar</a></li>
            <li class="show_correo oculto"><a href='{baseurl}Venta/confirm_correo/{id}' class='correo'><span class='glyphicon glyphicon-envelope'></span> Enviar correo</a></li>
            <li class="show_credito oculto"><a href='{baseurl}Venta/tocredit/{id}' class='credito'><span class='glyphicon glyphicon-share-alt'></span> Generar Nota de credito</a></li>
            <li class="show_baja oculto"><a href='{baseurl}Baja/crear/{id}' class='baja'><span class='glyphicon glyphicon-circle-arrow-down'></span> Generar baja</a></li>
            <li class="show_xml oculto"><a href='{baseurl}Venta/getxml/{id}'><span class='glyphicon glyphicon-menu-hamburger'></span> Comprobante digital</a></li>
            <li class="show_cdr oculto"><a href='{baseurl}Venta/getcdr/{id}'><span class='glyphicon glyphicon-cloud-download'></span> Constancia Sunat</a></li>
            <li class="show_editar oculto"><a href='{baseurl}Venta/eliminar/{id}' class='eliminar'><span class='glyphicon glyphicon-remove'></span> Eliminar</a></li>
          </ul>
        </div>
        <span class='show_enviar oculto'><a href='{baseurl}Venta/enviarSunat/{id}' class='btn btn-primary btn-sm enviar'><span class='glyphicon glyphicon-refresh'></span></a></span> 
        <span class='show_correo oculto'><a href='{baseurl}Venta/confirm_correo/{id}' class='btn btn-warning btn-sm correo'><span class='glyphicon glyphicon-envelope'></span></a></span>
        <span class='show_almacen oculto'><a href='{baseurl}Venta/alm_confirm/{id}' title='Afectar almacen' class='btn btn-info btn-sm almacen'><span class='glyphicon glyphicon-edit'></span></a></span>`;
        html = replaceAll(html, "{baseurl}", baseurl);
        html = replaceAll(html, "{id}", id);
        $ar.append(html);
        if (tipo_usuario != 3) {
            $ar.find('.show_clonar').show();
            if (eslis) $ar.find('.show_enviar').show();
            if (esini) $ar.find('.show_editar').show();
            if (esval) $ar.find('.show_credito,.show_baja,.show_xml,.show_cdr').show();
            if (esmai) $ar.find('.show_correo').show();
            if (esalm == 'NO') $ar.find('.show_almacen').show();
            else $ar.find('.show_editar').hide();
            if (comp == 3 && estado == 1) $ar.find('.show_enviar').hide(); //solo para boletas de venta
            if ((comp == 7 || comp == 8) && estado == 1) {
                if (nc == 3) $ar.find('.show_enviar').hide(); //solo para boletas de venta  
            }
        }
        $ar.find('.correo').click(function() {
            $(this).load_dialog({
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
            if (confirm("¿Desea eliminar el comprobante?")) {
                $.gs_loader.show();
                $.ajax({
                    type: "POST",
                    dataType: "json",
                    url: $(this).attr('href'),
                    success: function(data) {
                        $.gs_loader.hide();
                        $table.draw('page');
                        if (data.mensaje != '') alert(data.mensaje);
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

    }


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
                    $('.nosel').addClass('hidden');
                } else {
                    $('.onsel').addClass('hidden');
                    $('.nosel').removeClass('hidden');
                }
                seleccionados = selected;
            },
            onrow: function(row, data) {
                botones(data.DT_RowId, data.DT_Estado, data.DT_EmailSend, data.DT_Pagado, data.DT_Comp, data.DT_NC, data.DT_ALM, $(row).find('td .opts'));
            }
        };

    
    var buton = "<div class='opts'><input type='checkbox'></div>";
    $table = $dt.load_simpleTable(conf, true, buton);
    $('.ocform input').DTFilter($table);
    $('.ocform input,.ocform select').change(function() {
        $table.draw();
        return false;
    })

    $('#btn-report-excel').on('click', function() {
        window.location.href = baseurl + "Venta/reporte_excel/?desde=" + $('#desde').val() + "&hasta=" + $('#hasta').val() + "&comprobantes=" + $('#comprobantes').val() + "&moneda=" + $('#moneda').val() + "&search=" + $('#filtro').val() + "&situacion=" + $('select[name="estado"]').val();
    })



});

