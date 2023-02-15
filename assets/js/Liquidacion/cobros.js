$(document).ready(function(){
    $('input.fecha').daterangepicker({
        singleDatePicker: true,
        locale: {
            format: 'DD/MM/YYYY'
        }
    });
    $('input[name="saldo"]').dval($('input[name="saldo"]').val());
    $('input[name="pagado"]').keyup(function(){
        changePagado($(this).val());
    });
    $('input[name="pagado"]').change(function(){
        if(!isNaN($(this).val()))
            $(this).dval($(this).val());
        else
            $(this).dval('0.00');
    });
})
function changePagado(valor) {
    pagado = parseFloat(valor);
    saldo = parseFloat($('input[name="saldo"]').val());
    if(pagado > saldo){
        $('#vuelto').dval(pagado-saldo);
    }else{
        $('#vuelto').dval('0.00');
    }
}

function change_date(){
    $('.ocform input').change();
}