var baseurl
$(document).on('ready', function () {
    baseurl = $("#baseurl").val();
    var url = baseurl + 'Cuenta/movimientos?json=true';
    var $table;
    var seleccionados;
    var procesados = 0;
    var mensajes = new Array();
    var tipo_envio = 0;

    function botones(id,estado,mail,$ar){
       
       html = `
        <span class='show_enviar '><a href='{baseurl}Cuenta/cambios/{id}/1' title='Agregar' class='btn btn-success btn-sm cambios'><span class='glyphicon glyphicon-print'></span></a></span>`;
        
        html = replaceAll(html,"{baseurl}", baseurl);
        html = replaceAll(html,"{id}", id);
        $ar.append(html);

        $ar.find('.cambios').click(function(){
             $(this).load_dialog({
                title: $(this).attr('title'),
                loaded: function (dlg) {
                    $(dlg).find('form').submit(function () {
                        $(dlg).find('.error').addClass('hidden')
                        $(this).formPost(true, function (data) {
                            if (data.exito == false) {

                            } else {
                                dlg.modal('hide');
                                $table.draw('page');
                            }
                        });
                        return false;
                    });
                }
            });
            return false;
        });
    }

    var $dt = $('#mitabla'),
            conf = {
                data_source: url,
                cactions: ".ocform",
                order: [[1, "desc"]],
                oncheck: function (row, data, selected) {
                    if (selected.length > 0) {
                        $('.onsel').removeClass('hidden');
                        $('.nosel').addClass('hidden');
                    } else {
                        $('.onsel').addClass('hidden');
                        $('.nosel').removeClass('hidden');
                    }
                    seleccionados = selected;
                },
                onrow: function (row, data) {
                    botones(data.DT_RowId,data.DT_Estado,data.DT_EmailSend,$(row).find('td .botones'));
                }
            };
       var $this;
    var $dlg;

    $('.ocform').submit(function () {
        $table.draw();
        return false;
    })

    $('.ocform input,.ocform select').change(function () {
        $table.draw();
        return false;
    })
    
    
    var start = moment().subtract(29, 'days');
    var end = moment();

    function cb(start, end) {
        $('#reportrange span').html(start.format('DD/MM/YYYY') + ' - ' + end.format('DD/MM/YYYY'));
        $('#desde').val(start.format('YYYY-MM-DD'));
        $('#hasta').val(end.format('YYYY-MM-DD'));
        if(typeof($table)!='undefined') $table.draw();
    }

    cb(start, end);
    var buton = "<div class='botones'></div>";
    $table = $dt.load_simpleTable(conf, true,buton);
    
    $('.crearproducto').click(function () {
        $(this).load_dialog({
            title: $(this).attr('title'),
            loaded: function (dlg) {
                $(dlg).find('form').submit(function () {
                    $(dlg).find('.error').addClass('hidden')
                    $(this).formPost(true, function (data) {
                        if (data.exito == false) {

                        } else {
                            $('select#producto').select2("trigger", "select", {
                                data: {id: data.datos.prod_id, text: data.datos.prod_nombre}
                            });
                            dlg.modal('hide');
                            $table.draw('page');
                        }
                    });
                    return false;
                });
            }
        });
        return false;
    });
});
    function calcular_trans() {
        var smonto = $('input[name=montotrans]').val();
        var monto = parseFloat((smonto == "") ? '0' : smonto );
        var totalori = parseFloat($('input[name="valcajaorigen"]').val());
        var totaldes = parseFloat($('input[name="valcajadestino"]').val());
        
        $('input[name="ori_trans"]').val(monto.toFixed(2));
        $('input[name="dest_recibir"]').val(monto.toFixed(2));

        totalori = parseFloat(totalori - monto).toFixed(2);
        $('input[name="ori_monto"]').val(totalori);
        totaldes = parseFloat(totaldes + monto).toFixed(2);
        $('input[name="dest_monto"]').val(totaldes);
    }
    function cuenta_destino(cuen_id){
        $.ajax({
            dataType: 'JSON',
            data:  {cuen_id: cuen_id},
            url:   baseurl+"Cuenta/getCuenta",
            type:  'post',
            success:  function (rpta) {
                $('input[name=dest_cuenta]').val(rpta.data.cuen_banco);
                $('input[name=dest_moneda]').val(rpta.data.cuen_moneda);
                $('input[name=dest_monto]').val(rpta.data.cuen_monto);
                $('input[name=valcajadestino]').val(rpta.data.cuen_monto);
                calcular_trans();
            }
        });
    }
    function met_trans() {
        cuenta_destino($('#cuenta_destino').val());
        $('#cuenta_destino').change(function(){
            cuenta_destino($(this).val());
        })

        $('input[name="montotrans"]').keyup(function(){
            calcular_trans();
        });
    }
    function calcular(){
        cs = parseFloat($('input[name=caja_sistema]').val());
        cr = parseFloat($('input[name=caja_real]').val());
        ag = 0;
        re = 0;
        if(cs<cr){
            ag = parseFloat(cr-cs).toFixed(2);
        }else{
            re = parseFloat(cs-cr).toFixed(2);
        }
        $('input[name=caja_retirar]').val(re);
        $('input[name=caja_agregar]').val(ag);
    }
/*    EN CREAR PRODUCTO  */
    function changeValor(valor) {
        precio = myRound(parseFloat(valor) + Math.m(valor,0.18));
        $('#prod-precio').val(precio);
        $('#prod-valor').dval(valor);
    }
    function changePrecio(precio) {
        valor = myRound(precio/1.18);
        $('#prod-valor').val(valor);
        $('#prod-precio').dval(precio);
    }
