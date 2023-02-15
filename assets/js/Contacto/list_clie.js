var url = "";
$(document).on('ready', function() {
    var url = $("#nameurl").val() + '?json=true';
    var $table;
    function botones(id, permisos, $ar) {
        html = `
        <span class='show_editar oculto'><a href='{baseurl}Contacto/eliminar_clie/{id}' class='btn btn-danger btn-sm eliminar'><span class='glyphicon glyphicon-trash'></span></a></span>
        <span class="show_editar oculto"><a href='{baseurl}Contacto/crear_clie/{id}' class='btn btn-success btn-sm editar'><span class='glyphicon glyphicon-pencil'></span></a></span>`;
        html = replaceAll(html, "{baseurl}", baseurl);
        html = replaceAll(html, "{id}", id);
        $ar.append(html);

        $ar.find('.show_clonar').show();
        if(permisos > 1) $ar.find('.show_editar').show();

        $ar.find('.eliminar').click(function() {
            if (confirm("è¢ƒDesea eliminar el Cliente?")) {
                $.gs_loader.show();
                $.ajax({
                    type: "POST",
                    dataType: "json",
                    url: $(this).attr('href'),
                    success: function(data) {
                        console.log("success");
                        $.gs_loader.hide();
                        $table.draw('page');
                        if (data.mensaje != '') alert(data.mensaje);
                    },
                    error: function(data) {
                        console.log("error");
                        $table.draw('page');
                        $.gs_loader.hide();
                        alert(data.mensaje.replace(/(<([^>]+)>)/ig,""));
                    }
                });
            }
            return false;
        });
        $ar.find('.editar').click(function() {
            $(this).load_dialog({
                title: 'Editar Cliente',
                loaded: function($dlg) {
                    $dlg.find('form').submit(function() {
                        $(this).formPost(true, {}, function(data) {
                            if(data.exito){
                                alert(data.mensaje);    
                                $dlg.find('.close').click();
                                $table.draw('page');
                            }else{
                                $('.error').removeClass('hidden').find('.text').html(data.mensaje);
                            }
                        });
                        return false;
                    })
                }
            });
            return false;
        });

    }
    $(".crear").click(function() {
        $(this).load_dialog({
            title: $(this).attr('title'),
            loaded: function($dlg) {
                $dlg.find('form').submit(function() {
                    $(this).formPost(true, {}, function(data) {
                        if(data.exito){
                            alert(data.mensaje);
                            $dlg.find('.close').click();
                            $table.draw('page');
                        }else{
                            $('.error').removeClass('hidden').find('.text').html(data.mensaje);
                        }
                    });
                    return false;
                })
            }
        });
        return false;
    });

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
                botones(data.DT_RowId, data.DT_Permisos, $(row).find('td .opts'));
            }
        };

    
    var buton = "<div class='opts'></div>";
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

