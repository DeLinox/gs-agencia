$(document).ready(function () {
    var baseurl = $("#baseurl").val();
 
    $('select.cmb').select2({placeholder: 'Seleccione', minimumResultsForSearch: Infinity, width: '100%', allowClear: false});
	
   function getSerie($tipo){
        if($tipo=='01')return "F001";
        if($tipo=='03')return "B001";
        if($tipo=='07')return "FC01";
        if($tipo=='08')return "FD01";
    }

	$sel = $('select#comprobantes').select2({
        placeholder: 'Agregar comprobantes',
        allowClear: true,
        data: JSON.parse($('select#comprobantes').attr('data')),
        width: '100%',
        language: "es",
        minimumInputLength: Infinity,
        ajax: {
            url: baseurl + "Venta/buscar",
            dataType: 'json',
            data: function (params) {
                return {
                    q: params.term,
                    p: params.page
                };
            },
            processResults: function (data, params) {
                params.page = params.page || 1;
                return {
                    results: data.items,
                    pagination: {
                        more: (params.page * 30) < data.total_count
                    }
                };
            },
            cache: true
        },
        escapeMarkup: function (markup) {
            return markup;
        },
        minimumInputLength: 0,
    }).val(JSON.parse($('select#comprobantes').attr('init'))).trigger('change');

    //$sel.select2("trigger", "select2", {data: [{id: '4', text: '345'},{id: '4', text: '345'}]});

  
    $('input.fecha').daterangepicker({
        singleDatePicker: true,
        locale: {
                format: 'DD/MM/YYYY'
            }});

    $('#subir').submit(function(e){
        e.preventDefault();
        guardarComprobante(this);
    });

});
/* Productos */
function guardarComprobante(form){

     $(form).formPost(true,{},function(data){
         if(data.exito==true){
            window.location.href = $('#baseurl').val()+'Resumen/Listado';
         }else{
            $('.error').removeClass('hidden').find('.text').html(data.mensaje);
            window.location.href = '#';
         }
    });
}