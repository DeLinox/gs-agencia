$(document).ready(function(){
	get_cuentas();	
	$('#frm-pagos').submit(function(e){
		e.preventDefault();
		$form = $(this);
		if(confirm("Seguro que desea subir los pagos?")){
			var formData = new FormData($(this)[0]);
			$.gs_loader.show();
		    $.ajax({
		        dataType: "json",
		        url: $(this).attr("action"),
		        type: $(this).attr("method"),
		        data: formData,
		        cache: false,
		        contentType: false,
		        processData: false,
		        success: function(data){
		        	$.gs_loader.hide();
		        	if(data.exito)
		        		$(".respuesta-pagos").html(data.html);
		        	else
		        		$(".respuesta-pagos").html(data.mensaje);
		        	$form[0].reset();
		        },

		    })
		    return false;
	    }
	})
})
function get_cuentas() {
	$.ajax({
       type: "POST",
       dataType: "json",
       url:baseurl+"cuenta/get_cuentas",
       success: function(data){
            $('#cuentas').find("tbody").html(data.html);
            add_func();
       }
    });
}
function add_func(){
	$('.editar').click(clickCrear);
	$('.eliminar').click(clickEliminar);
}
function clickCrear() {
    var actual = $(this);
    $(this).load_dialog({
        title: $(this).attr('title'),
        loaded: function(dlg) {
            $(dlg).find('form').submit(function() {
                $(dlg).find('.error').addClass('hidden')
                $(this).formPost(true, function(data) {
                    if (data.exito == false) {
                    	$(dlg).find('.mensaje').show().html(data.mensaje);
                    } else {
                        alert(data.mensaje);
                        dlg.modal('hide');
                        get_cuentas();
                    }
                });
                return false;
            });
        }
    });
    return false;
}
function clickEliminar() {
	if(confirm("Esta seguro que desea eliminar la cuenta?")){
	    var actual = $(this);
	    $.ajax({
	       type: "POST",
	       dataType: "json",
	       url: baseurl+"cuenta/eliminar/"+actual.attr("data-id"),
	       success: function(data){
	       		alert(data.mensaje)
	 			if(data.exito){
	 				get_cuentas();
	 			}
	       }
	    });
	    return false;
    }
}