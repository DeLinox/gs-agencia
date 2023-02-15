var url = "";
$(document).on('ready', function() {
    url = $("#nameurl").val() + '?json=true';

    plan_listado();

    $('.registrar').on('click', function(){
        $(this).load_dialog({
            title: 'Asignar empleado a planilla',
            loaded: function($dlg) {
                $dlg.find('form').submit(function() {
                    $(this).formPost(true, {}, function(data) {
                        alert(data.mensaje);
                        if (data.exito){ 
                            $dlg.find('.close').click();
                            plan_listado();        
                        }
                    });
                    return false;
                })
            }
        });
        return false;
    })

    $('.ocform select').change(plan_listado);

    $('.reporte_excel').click(function(){
        window.location.href = baseurl + "Planilla/reporte_excel_hojaserv?fecha="+$('input[name="fecha"]').val();
    })

});

function fn_tbl() {
      
    $('.editar').on('click', function(){
        $(this).load_dialog({
            title: 'Editar Planilla de empleado',
            loaded: function($dlg) {
                $dlg.find('form').submit(function() {
                    $(this).formPost(true, {}, function(data) {
                        alert(data.mensaje);
                        if (data.exito){ 
                            $dlg.find('.close').click();
                            plan_listado();        
                        }
                    });
                    return false;
                })
            }
        });
        return false;
    });
    $('.imprimir').on('click', function(){
        $(this).load_dialog({
            
        });
        return false;
    });
    $('.eliminar').on('click', function(e){
        e.preventDefault();
        if(confirm("Seguro que desea eliminar el empleado de este periodo?")){
            $.ajax({
                dataType: "json",
                url: $(this).attr('href'),
                type: "POST",
                success: function(resp){
                    alert(resp.mensaje);
                    if(resp.exito){
                        plan_listado();
                    }
                }
            })
        }    
    });

    $('.cancelar').on('click', function(e){
        e.preventDefault();
        if(confirm("Seguro que desea cancelar el pago?")){
            $.ajax({
                dataType: "json",
                url: $(this).attr('href'),
                type: "POST",
                success: function(resp){
                    alert(resp.mensaje);
                    if(resp.exito){
                        plan_listado();
                    }
                }
            })
        }    
    });

    $('.pagos').on('click', function(e){
        $(this).load_dialog({
            loaded: function($dlg) {
                $dlg.find('form').submit(function(e) {
                    e.preventDefault();
                    if(confirm('Desea guardar el pago?')){
                        $(this).formPost(true, {}, function(data) {
                            alert(data.mensaje);
                            if (data.exito){ 
                                $dlg.find('.close').click();
                                plan_listado();        
                            }
                        });
                        return false;
                    }
                })
            }
        });
        return false;
    });
}
function plan_listado() {
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
