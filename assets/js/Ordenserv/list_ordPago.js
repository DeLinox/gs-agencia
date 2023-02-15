var url = "";
$(document).on('ready', function() {
    var url = $("#nameurl").val() + '?json=true';
    var $table;
    function botones(id, espagado,pagado, permisos,usuario, $ar) {
        html = `<div class='btn-group'>
          <a href='{baseurl}Ordenserv/ordPagoPdf/{id}' class='btn btn-success btn-xs verord'><span class='glyphicon glyphicon-eye-open'></span></a>
          <button type='button' class='btn btn-success btn-xs dropdown-toggle' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'>
            <span class='caret'></span>
            <span class='sr-only'>Toggle Dropdown</span>
          </button>
          <ul class='dropdown-menu'>  
            <li class="show_pago oculto"><a href='{baseurl}Ordenserv/ord_pagar/{id}' class='pagar'><span class='glyphicon glyphicon-usd'></span> Pagar</a></li>
            <li class="show_anular oculto"><a href='{baseurl}Ordenserv/ord_anularPago/{id}' class='anular'><span class='glyphicon glyphicon-remove'></span> Anular Pago</a></li>
            <li class="show_eliminar oculto"><a href='{baseurl}Ordenserv/elim_ordenPago/{id}' class='eliminar'><span class='glyphicon glyphicon-remove'></span> Eliminar</a></li>
          </ul>
        </div>`;
        //<span style='font-size:100%' class='pagado label label-primary oculto'>P</span>

        html = replaceAll(html, "{baseurl}", baseurl);
        html = replaceAll(html, "{id}", id);
        $ar.append(html);

        if(permisos > 1){
            if(pagado == 0) $ar.find('.show_eliminar, .show_editar').show();
            if(usuario == 1 || usuario == 9){
                if(pagado > 0) $ar.find('.show_anular').show();    
            }
            if(espagado != '1') $ar.find('.show_pago').show();
        }
        //else $ar.find('.pagado').show();

        $ar.find('.pagar').click(function() {
            $(this).load_dialog({
                title: 'Realizar pago',
                script: baseurl + 'assets/js/Ordenserv/pagar.js',
                loaded: function($dlg) {
                    $dlg.find('form').submit(function() {
                        $(this).formPost(true, {}, function(data) {
                            console.log(data);
                            if (data.exito != true){ 
                                
                            }else{
                                $dlg.find('.close').click();
                                $table.draw('page');        
                            }
                        });
                        
                        return false;
                    })
                }
            });
            return false;
        });
        $ar.find('.anular').click(function() {
            if (confirm("¿Realmente desea anular el pago?")) {
                $.gs_loader.show();
                $.ajax({
                    type: "POST",
                    dataType: "json",
                    url: $(this).attr('href'),
                    success: function(data) {
                       alert(data.mensaje);
                       $.gs_loader.hide();
                        if(data.exito){
                            $table.draw('page');        
                        }
                    }
                });
            }
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

        $ar.find('.verord').click(function() {
            $(this).load_dialog({
                loaded: function($dlg) {

                }
            });
            return false;
        });

        $ar.find('.eliminar').click(function() {
            if (confirm("¿Realmente desea eliminar la orden?")) {
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
                botones(data.DT_RowId, data.DT_esPagado, data.DT_Pagado, data.DT_Permisos,data.DT_UsuaId, $(row).find('td .opts'));
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

function change_date(){
    $('.ocform input').change();
}
