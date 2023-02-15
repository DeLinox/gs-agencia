$(document).ready(function(){
    $('#moneda').change(function(){
        if($(this).val()=='DOLARES') $('.msimb').text('$');
        else $('.msimb').text('S/');
    });
    $('#moneda').change();
    
	$('input[name="descuentos"]').change(actualizar_total);
	actualizar_total();
	$("form").submit(function(e){
		e.preventDefault();
		$(this).formPost(true, function(data) {
			alert(data.mensaje);
            if (data.exito) {
            	$(location).attr('href',baseurl+"Comprobante/comp_listadoLiquidacion");
            }
        });

	})
})
function actualizar_total(){
	$descuentos = $('input[name="descuentos"]').val();
	$subtotal = $('input[name="total_sub"]').val();
	if(!esNumeroPositivo($descuentos)) { $descuentos = 0; $('input[name="descuentos"]').dval(0); }
	if(!esNumeroPositivo($subtotal)) { $subtotal = 0; $('input[name="total_sub"]').dval(0); }
	//$tporcentaje = parseFloat($subtotal*($porcentaje/100));
	$('#total_igv').dval($descuentos);
	$('#total_total').dval($subtotal - $descuentos);
	$('input[name="descuentos"]').dval($descuentos);
	$('input[name="total_sub"]').dval($subtotal);
}
/*
function actualizar_total(){
	$porcentaje = $('input[name="porcentaje"]').val();
	$subtotal = $('input[name="total_sub"]').val();
	if(!esNumeroPositivo($porcentaje)) { $porcentaje = 0; $('input[name="porcentaje"]').dval(0); }
	if(!esNumeroPositivo($subtotal)) { $subtotal = 0; $('input[name="total_sub"]').dval(0); }
	$tporcentaje = parseFloat($subtotal*($porcentaje/100));
	$('#total_igv').dval($tporcentaje);
	$('#total_total').dval($subtotal - $tporcentaje);
	$('input[name="porcentaje"]').dval($porcentaje);
	$('input[name="total_sub"]').dval($subtotal);
}
*/