$(document).ready(function() {
    //form = $('#frm-series');
    var msjExito = $('#msj-success');
    var msjError = $('#msj-danger');

    var cambiar = $('#cambiar_logo');
    var agregar = $('#agregar_logo');
    agregar.hide();
    $('#btn-cambiar').click(function(){
        agregar.show();
        cambiar.hide();
    })
    $('#btn-cancelar').click(function(){
        agregar.hide();
        cambiar.show();
    })

    mensajes();
    $('#frm-empresa').submit(function(e){
        e.preventDefault();
        //console.log("asi que quieres enviar");
         $.ajax({
            dataType: "JSON",
            data: $(this).serialize(),
            url:  $(this).attr('action'),
            type: $(this).attr('method'),
            success:  function (response) {
                console.log(response);
                if(response.exito == false){
                    mensajes('error');
                    msjError.html(response.mensaje);    
                }else{
                    mensajes('exito');
                    msjExito.html(response.mensaje);
                }
            }
        });
    })
    $('#frm-logotipo').submit(function(e){
        e.preventDefault();
        //console.log("asi que quieres enviar");
        var url = $(this).attr('action');
        var type = $(this).attr('method');
        var parametros=new FormData($(this)[0])  
        //realizamos la petición ajax con la función de jquery
        
        $.ajax({
            dataType: "JSON",
            type: type,
            url: url,
            data: parametros,
            contentType: false, //importante enviar este parametro en false
            processData: false, //importante enviar este parametro en false
            success: function (response) {
                if(response.exito == false){
                    mensajes('error');
                    msjError.html(response.mensaje);    
                }else{
                    mensajes('exito');
                    msjExito.html(response.mensaje);
                }
            }
        });
        
    })
    function mensajes(num){
        if(num == 'exito'){
            msjExito.show();
            msjError.hide();    
        }else if(num == 'error'){
            msjExito.hide();
            msjError.show();    
        }else{
            msjExito.hide();
            msjError.hide();    
        }   
    }
})
