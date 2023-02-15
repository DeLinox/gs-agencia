//<li class="show_editar oculto"><a href='{baseurl}flota/eliminar/{id}' class='eliminar'><span class='glyphicon glyphicon-remove'></span> Eliminar</a></li>
var url = "";
$(document).on('ready', function() {
    var url = $("#nameurl").val() + '?json=true';
    var $table;
    function botones(id,$ar) {
        html = `<a href='{baseurl}Planilla/peri_crear/{id}' class='editar btn btn-primary btn-xs' title='Editar Periodo'><span class='glyphicon glyphicon-pencil'></span> Editar</a>`;        
        html = replaceAll(html, "{baseurl}", baseurl);
        html = replaceAll(html, "{id}", id);
        $ar.append(html);
        //if(estado == 0)$ar.find('.show_editar').show();
        $ar.find('.editar').click(function() {
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
                                $dlg.find('.error').removeClass('hidden')
                                $dlg.find('.error').html(data.mensaje);
                            }
                        });
                        
                        return false;
                    })
                }
            });
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
                    //$('.nosel').addClass('hidden');
                } else {
                    $('.onsel').addClass('hidden');
                    //$('.nosel').removeClass('hidden');
                }
                
                seleccionados = selected;
            },
            onrow: function(row, data) {
                botones(data.DT_RowId, $(row).find('td .opts'));
            }
        };

    
    var buton = "<div class='opts'><input type='checkbox' disabled=''></div>";
    $table = $dt.load_simpleTable(conf, true, buton);
    $('.ocform input').DTFilter($table);

    $('.ocform input,.ocform select').change(function() {
        $table.draw();
        return false;
    })

    $('#btn-report-excel').on('click', function() {
        window.location.href = baseurl + "Registro/reporte_excel/?desde=" + $('#desde').val() + "&hasta=" + $('#hasta').val() + "&moneda=" + $('#moneda').val() + "&search=" + $('#filtro').val() + "&estado=" + $('select[name="estado"]').val();
    })

    $('.registrar').click(function(){
        $(this).load_dialog({
            title: $(this).attr('title'),
            loaded: function($dlg) {
                $dlg.find('form').submit(function() {
                    $(this).formPost(true, {}, function(data) {
                        alert(data.mensaje);
                        if(data.exito){
                            $dlg.find('.close').click();
                            $table.draw('page');
                        }
                    });
                    
                    return false;
                })
            }
        });
        return false;
    })
});
