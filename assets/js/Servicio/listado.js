$(document).ready(function(){

    $('.aditar_serv').on('click',function(){
        $(this).load_dialog({
            title: $(this).attr('title'),
            loaded: function(dlg) {
                $(dlg).find('form').submit(function() {
                    $(dlg).find('.error').addClass('hidden')
                    $(this).formPost(true, function(data) {
                        if (data.exito == false) {
                            alert(data.mensaje);
                        } else {
                            alert(data.mensaje);
                            dlg.modal('hide');
                            location.reload();
                        }
                    });
                    return false;
                });
            }
        });
        return false;
    })
    $('.eliminar').on('click',function(e) {
        e.preventDefault();
        if(confirm("¿Realmente desea eliminar el servicio?")){
            $.ajax({
                type: "POST",
                dataType: "json",
                url: $(this).attr('href'),
                success: function(data) {
                    if (data.exito == false) {
                        alert(data.mensaje);
                    } else {
                        alert(data.mensaje);
                        location.reload();
                    }
                }
            });
        }
    })
})
