var url = "";
$(document).on('ready', function () {
    //baseurl = $("#baseurl").val();
    var url = $("#nameurl").val()+'?json=true';
    var $table;
    var seleccionados;
    var procesados = 0;
    var mensajes = new Array();
    var tipo_envio = 0;

    function botones(id,mail,$ar){
       esmai = (mail==0);
       html = `
          <a href='{baseurl}usuario/form/{id}' class='btn btn-success btn-sm edit'><span class='glyphicon glyphicon-edit'></span></a>
          <a href='{baseurl}usuario/borrar/{id}' class='btn btn-danger btn-sm delete'><span class='glyphicon glyphicon-trash'></span></a>`;
        html = replaceAll(html,"{baseurl}", baseurl);
        html = replaceAll(html,"{id}", id);
        $ar.append(html);
        $ar.find('.show_editar').show();
        if(esmai)$ar.find('.show_correo').show();

        $ar.find('.edit').click(function(){
            $(this).load_dialog({
            	title: $(this).attr('title'),
            	script: baseurl+'assets/js/Usuario/form.js',
                loaded:function($dlg){
                    $dlg.find('form').submit(function(){
                        $(this).formPost(true,{},function(data){
                            if(data.exito!=true)alert(data.mensaje);
                        });
                        $dlg.find('.close').click();
                        $table.draw('page');
                        return false;
                    })
                }
            });
            return false;
        });

        $ar.find('.delete').click(function(){
            if(confirm("¿Desea eliminar el usuario?")){
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
                    botones(data.DT_RowId,data.DT_EmailSend,$(row).find('td .botones'));
                }
            };
	var $this;
    var $dlg;

    
    $('.ocform').submit(function () {
        $table.draw();
        return false;
    })
    $('.crear').click(function(){
            $(this).load_dialog({
            	title: $(this).attr('title'),
            	script: baseurl+'assets/js/Usuario/form.js',
                loaded:function($dlg){
                    $dlg.find('form').submit(function(){
                        $(this).formPost(true,{},function(data){
                            if(data.exito!=true)alert(data.mensaje);
                        });
                        $dlg.find('.close').click();
                        $table.draw('page');
                        return false;
                    })
                }
            });
            return false;
        });
    
    
    
    var buton = "<div class='botones'></div>";
    $table = $dt.load_simpleTable(conf, true,buton);
    
});
