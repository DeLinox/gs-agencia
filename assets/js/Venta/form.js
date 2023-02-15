$(document).ready(function() {
    var baseurl = $("#baseurl").val();

    $('input.fecha').daterangepicker({
        singleDatePicker: true,
        locale: {
            format: 'DD/MM/YYYY'
        }
    });

    $.fn.SelectProducto = function() {
        $(this).select2({
            placeholder: 'Buscar producto',
            allowClear: true,
            width: '100%',
            language: "es",
            minimumInputLength: Infinity,
            ajax: {
                url: baseurl + "Producto/buscar",
                dataType: 'json',
                data: function(params) {
                    return {
                        q: params.term,
                        p: params.page
                    };
                },
                processResults: function(data, params) {
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
            escapeMarkup: function(markup) {
                return markup;
            },
            minimumInputLength: 0
        }).on("select2:select", function(e) {
            $(this).closest('.serv-item').find('input[name="cantidad[]"]').removeAttr('disabled');
            $(this).closest('.serv-item').find('input[name="descuento[]"]').removeAttr('disabled');
            $(this).closest('.serv-item').find('input[name="precio[]"]').removeAttr('disabled');
            $(this).closest('.serv-item').find('input[name="valor[]"]').removeAttr('disabled');
            $(this).closest('.serv-item').find('select[name="gratuita[]"]').removeAttr('disabled');
            $(this).closest('.serv-item').find('select[name="tipo[]"]').removeAttr('disabled');
            $(this).closest('.serv-item').find('input[name="codigo[]"]').removeAttr('disabled');
            //$(this).closest('.serv-item').find('select[name="tipo[]"]').val(e.params.data.igvtipo);
            $(this).closest('.serv-item').find('select[name="unidad[]"]').val(e.params.data.unid_nombre);
            $(this).closest('.serv-item').find('input[name="codigo[]"]').val(e.params.data.codigo);
            $(this).closest('.serv-item').find('input[name="precio[]"]').dval(myRound(e.params.data.precio));
            $(this).closest('.serv-item').find('input[name="precio[]"]').change()
            updateTotal();
            $(this).closest('.serv-item').find("textarea").removeAttr('disabled');
            $(this).closest('.serv-item').find('textarea[name="detalle[]"]').val(e.params.data.text);
        }).on('select2:unselect', function(e) {
            $(this).closest('.serv-item').find('textarea[name="detalle[]"]').val('');
            $(this).closest('.serv-item').find('input[name="cantidad[]"]').attr('disabled', '');
            $(this).closest('.serv-item').find('input[name="descuento[]"]').attr('disabled', '');
            $(this).closest('.serv-item').find('input[name="precio[]"]').attr('disabled', '');
            $(this).closest('.serv-item').find('input[name="valor[]"]').attr('disabled', '');
            $(this).closest('.serv-item').find('select[name="gratuita[]"]').attr('disabled', '');
            $(this).closest('.serv-item').find('select[name="tipo[]"]').attr('disabled', '');
            $(this).closest('.serv-item').find('input[name="codigo[]"]').attr('disabled', '');
            $(this).closest('.serv-item').find('input[name="valor[]"]').dval(myRound(0));
            $(this).closest('.serv-item').find('input[name="valor[]"]').change()
            updateTotal();
            $(this).closest('.serv-item').find("textarea").attr('disabled', '');
        });
    }


    function clickCrearProducto() {
        $(this).load_dialog({
            title: $(this).attr('title'),
            loaded: function(dlg) {
                $(dlg).find('form').submit(function() {
                    $(dlg).find('.error').addClass('hidden')
                    $(this).formPost(true, function(data) {
                        if (data.exito == false) {

                        } else {
                            $('select#producto').select2("trigger", "select", {
                                data: { id: data.datos.prod_id, text: data.datos.prod_nombre }
                            });
                            dlg.modal('hide');
                        }
                    });
                    return false;
                });
            }
        });
        return false;
    }

    if (productos.length > 0) {
        $.each(productos, function(i, elem) {
            $nuevafila = $('#clonables .serv-item').clone();

            console.log(elem);
            $nuevafila.find(".deta_producto").SelectProducto();

            $nuevafila.find(".deta_producto").select2("trigger", "select", {
                data: { id: elem.deta_prod_id, text: elem.deta_descripcion }
            });

            $nuevafila.find('textarea[name="detalle[]"]').val(elem.deta_descripcion);
            $nuevafila.find('select[name="tipo[]"]').val(elem.deta_afec_id);
            $nuevafila.find('.crearproducto').on('click', clickCrearProducto);
            $nuevafila.appendTo('.sortable');
            $nuevafila.find('textarea[name="detalle[]"]').autosize();
            $nuevafila.find('input[name="deta_id[]"]').val(elem.deta_id);
            $nuevafila.find('input[name="cantidad[]"]').dval(elem.deta_cantidad).on('change', updateRow);
            $nuevafila.find('input[name="precio[]"]').dval(elem.deta_precio).on('change', updateRow);
            $nuevafila.find('input[name="descuento[]"]').dval(elem.deta_descuento).on('change', updateRow);
            $nuevafila.find('input[name="valor[]"]').dval(elem.deta_valor).on('change', updateRow);
            $nuevafila.find('input[name="igv[]"]').dval(elem.deta_igv);
            $nuevafila.find('select[name="unidad[]"]').val(elem.deta_unidad);
            $nuevafila.find('input[name="codigo[]"]').val(elem.deta_prod_codigo);
            $nuevafila.find('input[name="importe[]"]').dval(elem.deta_importe);
            $nuevafila.find('select[name="gratuita[]"]').val(elem.deta_esgratuita);

            $nuevafila.find('.formdrown ul li').click(function() { return false; })

            $nuevafila.find('input[name="precio[]"]').change();

            updateTotal();
            $nuevafila.find('select[name="gratuita[]"]').on('change', updateRow);
            $nuevafila.find('select[name="tipo[]"]').on('change', updateRow);
        });
    }

    $('a.agregarfila').click(function() {
        $nuevafila = $('#clonables .serv-item').clone();
        $nuevafila.find(".deta_producto").SelectProducto();
        $nuevafila.find('.crearproducto').on('click', clickCrearProducto);
        $nuevafila.hide();
        $nuevafila.appendTo('.sortable');
        $nuevafila.find('textarea[name="detalle[]"]').autosize();
        $nuevafila.fadeIn();
        return false;
    });

    if ($('.sortable .serv-item').length <= 0)
        $('a.agregarfila').click();


    $sel = $('select#cliente').select2({
        placeholder: 'Buscar cliente',
        allowClear: true,
        width: '100%',
        language: "es",
        minimumInputLength: Infinity,
        ajax: {
            url: baseurl + "Cliente/buscar",
            dataType: 'json',
            data: function(params) {
                return {
                    q: params.term,
                    p: params.page
                };
            },
            processResults: function(data, params) {
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
        escapeMarkup: function(markup) {
            return markup;
        },
        minimumInputLength: 0,
    }).on("select2:select", function(e) {

    });


    $('#vender').submit(function(e) {
        e.preventDefault();
        guardarComprobante(this);
    });


});
/* Productos */
function guardarComprobante(form) {
    $.each($('.sortable .serv-item [dval]'), function(index, item) {
        temp = $(item).attr('dval');
        $(item).attr('dval', $(item).val())
        $(item).val(temp)
    })
    $(form).formPost(true, {}, function(data) {
        if (data.exito == true) {
            if (data.from == true) window.location.href = $('#baseurl').val() + 'Venta/ver/' + data.id + '/true';
            else window.location.href = $('#baseurl').val() + 'Venta/listado/#0';
        } else {
            $.each($('.sortable .serv-item [dval]'), function(index, item) {
                temp = $(item).val();
                $(item).val($(item).attr('dval'))
                $(item).attr('dval', temp)
            });
            $('.error').removeClass('hidden').find('.text').html(data.mensaje);
            window.location.href = '#';
        }
    });
}