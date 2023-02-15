$(document).ready(function(){
	$('form').submit(function(e){
		e.preventDefault();
		llenarTabla($(this));
	});
	llenarTabla($('form'));
	
	$('.cuadroIngresos').click(function(){
        $(location).attr('href',baseurl+"Reporte/excel_movimientoTuristas?servicio="+$('input[name="servicios"]').val()+"&mes="+$('select[name="mes"]').val()+"&anio="+$('select[name="anio"]').val());
    })
})
function llenarTabla($this) {
	var url = $("#nameurl").val()+'/true';
	$.ajax({
        dataType: "json",
        url: url,
        type: "POST",
        data: $this.serialize(),
        success: function(resp){
            $('#movimiento_turistas').html(resp.html);
        }
    })
}