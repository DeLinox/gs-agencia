$(document).ready(function(){
    $('input[name="s_monto[]"]').change(function(){
        if(!esNumeroPositivo($(this).val())) { 
            $(this).dval(0);
        }else{
            $(this).dval($(this).val());
        }
    })
	$('input[name="incluye_saldo"]').change(function(){
        if($(this).is(":checked")){
            $('.saldos').show(500);
        }else{
            $('.saldos').hide(500);
        }
        updateTotal();
    })

	$('.edt').mouseover(function() {
		$(this).parent().find('.edt').addClass('hvr');
	}).mouseleave(function(){
		$(this).parent().find('.edt').removeClass('hvr');
	})
    $('input[name="saldo"]').change(function(){
        if(!esNumeroPositivo($(this).val())) { 
            $(this).dval(0);
        }else{
            $(this).dval($(this).val());
        }
    })
	$('.edt').on('click', function() {
		$a = $(this).parent().find('.pdet_id');
		$a.load_dialog({
            title: $a.attr('title'),
            loaded: function(dlg) {
                $(dlg).find('form').submit(function() {
                    $(dlg).find('.error').addClass('hidden')
                    $(this).formPost(true, function(data) {
                    	alert(data.mensaje);
                        if (data.exito) {
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
	buscarCliente = function (dlg) {
        $(dlg).find('form').submit(function () {
            $("input#rsocial").val( $(this).find("input[name='srsocial']").val())
            $("select[name=documento]").val( $(this).find("select[name='sdocumento']").val())
            $("input#docnum").val( $(this).find("input[name='sdocnum']").val() )
            $("input#clie_id").val( $(this).find('#sclie_id').val() )
            dlg.modal('hide');
            return false;
        });
    };

    

    $('.searchclie').click(function () {
        $(this).load_dialog({
            title: $(this).attr('title'),
            loaded: buscarCliente
        });
        return false;
    });

	$('form').submit(function(e) {
        e.preventDefault();
        if(confirm("¿Esta seguro de generar la orden de pago?")){
            $(this).formPost(true, function(data) {
                alert(data.mensaje);
                if (data.exito) {
                    $(location).attr('href',data.direccion);
                }
            });
            return false;
        }
    });
    updateTotal();
})

function updateTotal(){
    var total_total = parseFloat($('#total_last').val());
    if($('input[name="incluye_saldo"]').is(":checked")){
    var saldos = $('input[name="s_monto[]"]');
        saldos.each(function(){
            if (esNumeroPositivo($(this).val())) {
                total_total += parseFloat($(this).val());
            }
        })
    }
    $('input[name="total"]').dval(total_total);
}