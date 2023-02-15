var url = "";
$(document).on('ready', function() {
    var url = $("#nameurl").val() + '?json=true';
    var $table;
    function botones(id, pq, comp, liqu, orde, prio, tipo, $ar) {
        html = `
        <span style='font-size:150%; color:#FF8C00' class='prioridad'></span>
        <span style='font-size:100%' class='comprobante label label-success oculto'>C</span>
        <span style='font-size:100%' class='liquidacion label label-info oculto'>L</span>
        <span style='font-size:100%' class='orden label label-primary oculto'>O</span>`;
        html = replaceAll(html, "{baseurl}", baseurl);
        html = replaceAll(html, "{id}", id);
        html = replaceAll(html, "{pq}", pq);
        $ar.append(html);
        $ar.find('.show_clonar').show();
        $ar.find('.show_editar').show();
        if(prio == 2) $ar.find('.prioridad').addClass('glyphicon glyphicon-star-empty');
        else if(prio == 3) $ar.find('.prioridad').addClass('glyphicon glyphicon-star');
        if(comp == 1) $ar.find('.comprobante').show();
        if(liqu == 1) $ar.find('.liquidacion').show();
        if(orde == 1) $ar.find('.orden').show();
        $ar.find('.hoja_liquidacion').click(function(){
            $(this).load_dialog({
                loaded:function($dlg){
                    
                }
            });
            return false;
        });


        $ar.find('.eliminar').click(function() {
            if (confirm("Se eliminarán todos los servicios del FILE seleccionado, ¿Desea continuar?")) {
                $.gs_loader.show();
                $.ajax({
                    type: "POST",
                    dataType: "json",
                    url: $(this).attr('href'),
                    success: function(data) {
                        $.gs_loader.hide();
                        alert(data.mensaje)
                        $table.draw('page');
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
                    //$('.nosel').addClass('hidden');
                } else {
                    $('.onsel').addClass('hidden');
                    //$('.nosel').removeClass('hidden');
                }
                
                seleccionados = selected;
            },
            onrow: function(row, data) {
                botones(data.DT_RowId,data.DT_PaquId, data.DT_RowComp, data.DT_RowLiqu, data.DT_RowOrde, data.DT_RowPrio, data.DT_RowTipo, $(row).find('td .opts'));
                $(row).mousedown(RowClick);   
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
});
function change_date(){
    $('.ocform input').change();
}