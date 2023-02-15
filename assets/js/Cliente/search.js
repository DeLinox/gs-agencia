$(document).ready(function () {
	var baseurl = $("#baseurl").val();
	$('select.cmb').select2({placeholder: 'Seleccione', minimumResultsForSearch: Infinity, width: '100%', allowClear: false});
	$sel = $('select#cliente2').select2({
        placeholder: 'Buscar cliente',
        dropdownParent: $("#mdl-client"),
        allowClear: true,
        width: '100%',
        language: "es",
        minimumInputLength: Infinity,
        ajax: {
            url: baseurl + "Cliente/buscar",
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
    }).on("select2:select", function (e) {
        console.log(e.params.data)
        $("input#sdocnum").val(e.params.data.docnum)
        $("input#sdireccion").val(e.params.data.direccion)
        $("input#srsocial").val(e.params.data.text)
        $("input#semail1").val(e.params.data.email)
        $("input#sclie_id").val(e.params.data.id)
        $("select[name=sdocumento]").val(e.params.data.docu);
    });
})