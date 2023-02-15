var baseurl;
$(document).ready(function(){
	baseurl = $("#baseurl").val();


	$('.btn-genOrden').prop('disabled', true);
	$('.all').change(function(){
		if($(this).prop('checked') ) {
		    $('.sel').prop('checked', true);
		    $('.btn-genOrden').prop('disabled', false);
		}else{
			$('.sel').prop('checked', false);
			$('.btn-genOrden').prop('disabled', true);
		}
	});

	$('.sel').change(function(){
		if(cont_seleccionados() > 0)
			$('.btn-genOrden').prop('disabled', false);
		else
			$('.btn-genOrden').prop('disabled', true);
	});

	$('.btn-genOrden').on('click', function(){

		if(cont_seleccionados() > 0){
			var selected = [];
		    $(".sel").each(function() {
		      if (this.checked) {
		        // agregas cada elemento.
		        selected.push($(this).val());
		      }
		    });
		    var paqu_id = $('#paqu_id').val();
			$(this).load_dialog({
	            title: $(this).attr('title'),
	            script: baseurl + 'assets/js/registro/form_genOrden.js',
	            custom_url: baseurl+"registro/gen_orden?sel="+selected+"&paqu_id="+paqu_id,
	            loaded: function(dlg) {

	                $(dlg).find('form').submit(function() {
	                    $(dlg).find('.errorOrd').addClass('hidden')
	                    $(this).formPost(true, function(data) {
	                    	console.log(data.mensaje);
	                        if (data.exito == false) {
	                        	$('.errorOrd').removeClass('hidden').find('.text').html(data.mensaje);
	                        } else {
	                            alert(data.mensaje);
	                            getOrdenes();
	                            dlg.modal('hide');
	                        }
	                    });
	                    return false;
	                });

	            }
	        });
	        return false;
		}else{
			alert("debes seleccion al menos un paquete");
		}
	})
	
	getOrdenes();	
})
function getOrdenes() {
	$.ajax({
       type: "POST",
       dataType: "json",
       url: baseurl+"registro/getOrdenes/"+$('#paqu_id').val(),
       success: function(data){
            $('#ord_servs').find("tbody").html(data.html);
       }
    });
}

function cont_seleccionados() {
	var cant = 0;
	$(".sel").each(function(){
		if($(this).prop('checked')) cant++;
	});
	return cant;
}
function editar_ord(id) {
	$(this).load_dialog({
        title: "Editar Orden de Servicio",
        script: baseurl + 'assets/js/registro/form_genOrden.js',
        custom_url: baseurl+"registro/gen_orden/"+id,
        loaded: function(dlg) {
        	console.log(dlg);
            $(dlg).find('form').submit(function() {
                $(dlg).find('.error').addClass('hidden')
                $(this).formPost(true, function(data) {
                    if (data.exito == false) {

                    } else {
                        alert(data.mensaje);
                        getOrdenes();
                        dlg.modal('hide');
                    }
                });
                return false;
            });

        }
    });
    return false;
}
function eliminar_ord(id){
	if(confirm("¿Realmente desea eliminar el item?")){
		$.ajax({
	       type: "POST",
	       dataType: "json",
	       url: baseurl+"registro/eliminar_orden/"+id,
	       success: function(data){
	            if (data.exito != false){
	            	getOrdenes();
	            	alert("Se borro el item correctamente");
	            }else
	            	alert("Ocurrio un error, intentelo mas tarde");
	       }
	    });	
    }
}