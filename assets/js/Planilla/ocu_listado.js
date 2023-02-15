var url = "";
$(document).on('ready', function() {
    url = $("#nameurl").val() + '?json=true';

    ocu_listado();

    $('.registrar').on('click', function(){
        $(this).load_dialog({
            title: 'Registrar Ocupacion / Cargo',
            loaded: function($dlg) {
                $dlg.find('form').submit(function() {
                    $(this).formPost(true, {}, function(data) {
                        alert(data.mensaje);
                        if (data.exito){ 
                            $dlg.find('.close').click();
                            ocu_listado();        
                        }
                    });
                    return false;
                })
            }
        });
        return false;
    })

    $('.reporte_excel').click(function(){
        window.location.href = baseurl + "Planilla/reporte_excel_hojaserv?fecha="+$('input[name="fecha"]').val();
    })

});

function fn_tbl() {
      
    $('.editar').on('click', function(){
        $(this).load_dialog({
            title: 'Editar Ocupación / Cargo',
            loaded: function($dlg) {
                $dlg.find('form').submit(function() {
                    $(this).formPost(true, {}, function(data) {
                        alert(data.mensaje);
                        if (data.exito){ 
                            $dlg.find('.close').click();
                            ocu_listado();        
                        }
                    });
                    return false;
                })
            }
        });
        return false;
    });
    $('.eliminar').on('click', function(e){
        e.preventDefault();
        if(confirm("Seguro que desea eliminar la ocupacion o cargo?")){
            $.ajax({
                dataType: "json",
                url: $(this).attr('href'),
                type: "POST",
                success: function(resp){
                    alert(resp.mensaje);
                    if(resp.exito){
                        ocu_listado();
                    }
                }
            })
        }    
    });
}
function ocu_listado() {
    $.ajax({
        dataType: "json",
        url: url,
        data: $(".ocform").serialize(),
        type: "POST",
        success: function(resp){
            $('#tbl-planilla').find("tbody").html(resp.html);
            fn_tbl();
        }
    })
}
