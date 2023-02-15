$(document).ready(function() {
	//form = $('#frm-series');
	var msjExito = $('#msj-success');
	var msjError = $('#msj-danger');
	mensajes();
	$('#frm-series').submit(function(e){
		e.preventDefault();
		//console.log("asi que quieres enviar");
		 $.ajax({
	 		dataType: "JSON",
            data: $(this).serialize(),
            url:  $(this).attr('action'),
            type: $(this).attr('method'),
            success:  function (response) {
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
