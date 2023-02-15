var baseurl
$(document).on('ready', function () {
    baseurl = $("#baseurl").val();
    var url = baseurl + 'Proveedores/Listado?json=true';
    var $table;
    var seleccionados;
    var procesados = 0;
    var mensajes = new Array();
    var tipo_envio = 0;

    function botones(id,estado,mail,$ar){
       esini = (estado==1||estado==6);
       eslis = (estado==1||estado==2||estado==6);
       esval = (estado==3||estado==4);
       esmai = (mail==0);
       if ($('#session-data').val()==2) {
       html = `
        <span class='show_enviar '><a href='{baseurl}Proveedores/crear/{id}' class='btn btn-primary btn-sm edit'><span class='glyphicon glyphicon-edit'></span></a></span> 
        <span class='show_correo '><a href='{baseurl}Proveedores/eliminar/{id}' class='btn btn-warning btn-sm delete'><span class='glyphicon glyphicon-remove'></span></a></span>`;
        }else{html='';}
        html = replaceAll(html,"{baseurl}", baseurl);
        html = replaceAll(html,"{id}", id);
        $ar.append(html);

        $ar.find('.edit').click(function(){
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
        $ar.find('.delete').click(function(){
            if(confirm("¿Desea al Proveedor?")){
            $.gs_loader.show();
            $.ajax({
                   type: "POST",
                   dataType: "json",
                   url:$(this).attr('href') ,
                   success: function(data){
                        $.gs_loader.hide();
                        $table.draw('page');
                        if(data.mensaje!='') alert(data.mensaje);
                   },
                   error: function(response) {
                        $table.draw('page');
                        $.gs_loader.hide();
                        alert(response.responseText.replace(/(<([^>]+)>)/ig,""));
                   }
                });
            }
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
    
    $('.crearproveedor').click(function () {
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
