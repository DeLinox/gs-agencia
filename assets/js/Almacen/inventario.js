var url = "";
$(document).on('ready', function () {
    baseurl = $("#baseurl").val();
    var url = baseurl+'Almacen/inventario?json=true';
    var $table;
    var seleccionados;
    var procesados = 0;
    var mensajes = new Array();
    var tipo_envio = 0;

    function botones(id,estado,mail,$ar){

    }

    $('.enviar_todos').click(function(){
        if(confirm('¿Desea enviar a la sunat los comprobantes de la lista?')){
            
			tipo_envio = 1;
			$.gs_loader.show();
            for(i=0;i<seleccionados.length;i++)$('tr#'+seleccionados[i]+' .enviar').click();
			$dt.reset_selected();
            $('.onsel').addClass('hidden');
            $('.nosel').removeClass('hidden');
        }else{

        }
        return false;
    })

    $('.correo_todos').click(function(){
        if(confirm('¿Desea enviar a correos?')){
            $.gs_loader.show();
            tipo_envio = 1;
            for(i=0;i<seleccionados.length;i++){
                procesados++;
                $.ajax({
                   type: "POST",
                   dataType: "json",
                   url:baseurl+'Venta/enviarcorreo/'+seleccionados[i],
                   success: function(data){  procesados--;
                    verprocesos(); },
                   error: function(response) { procesados--;
                    verprocesos();  }
                });
            }
            $dt.reset_selected();
            $('.onsel').addClass('hidden');
            $('.nosel').removeClass('hidden');
        }else{

        }
        return false;
    })

    function verprocesos(){
        if(procesados>0){
            $('.procesos').html('<strong>'+procesados+'</strong> Comprobantes en proceso.');
        }else{
            $('.procesos').html('');
            tipo_envio = 0;
            $table.draw('page');
			$.gs_loader.hide();
        }
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
                  /*es = !(data.DT_Enviado==3||data.DT_Enviado==4||data.DT_Enviado==5);
                    if(es)$(row).find('td .botones').append("<a href='"+baseurl+"Venta/edit/"+data.DT_RowId+"' class='btn btn-primary btn-sm'><span class='glyphicon glyphicon-pencil'></span></a>");
					if(es)$(row).find('td .botones').append("<a  class='btn btn-primary genapp btn-sm' data-href='"+baseurl+"Venta/enviarSunat/"+data.DT_RowId+"' data-toggle='modal' data-target='#confirm-archivo'><span class='glyphicon glyphicon-refresh'></span></a>");
                    $(row).find('td .botones').append("<a data-href='"+baseurl+"Venta/ver/"+data.DT_RowId+"' class='ver btn btn-warning btn-sm' data-toggle='modal'  data-id='"+data.DT_RowId+"' data-target='#ver-factura'><span class='glyphicon glyphicon-print'></span></a>");
                    $(row).find('td .botones').append("<a href='"+baseurl+"Venta/tocredit/"+data.DT_RowId+"' class='ver btn btn-warning btn-sm'><span class='glyphicon glyphicon-share-alt'></span></a>");
                    $(row).find('td .botones').append("<a href='"+baseurl+"Baja/crear/"+data.DT_RowId+"' class='ver btn btn-warning btn-sm'><span class='glyphicon glyphicon-circle-arrow-down'></span></a>");
                    if(data.DT_EmailSend!=1)$(row).find('td .botones').append("<a  class='btn btn-danger btn-sm' data-href='"+baseurl+"Venta/confirm_correo/"+data.DT_RowId+"' data-toggle='modal' data-target='#confirm-correo'><span class='glyphicon glyphicon-envelope'></span></a>");
                    $(row).find('td .botones .ver').click(function(){
                        $('#ver-factura .modal-body').load($(this).attr('data-href'));
                        $('#ver-factura .sunat').attr('data-href',baseurl+"Venta/enviarSunat/"+$(this).attr('data-id'));
                        $('#ver-factura .enviar').attr('data-href',baseurl+"Venta/confirm_correo/"+$(this).attr('data-id'));
                        $('#ver-factura .xml').attr('href',baseurl+"Venta/getXML/"+$(this).attr('data-id'));
                        $('#ver-factura .sunat').click(function(){
                          $('#ver-factura').find('.close').click(); 
                        });
                        $('#ver-factura .enviar').click(function(){
                          $('#ver-factura').find('.close').click(); 
                        });
                    })*/
                    
                    botones(data.DT_RowId,data.DT_Estado,data.DT_EmailSend,$(row).find('td .botones'));
                }
            };
	   var $this;
    var $dlg;



    $('#confirm-archivo').find('.btn-ok').on('click', function () {
        $.gs_loader.show();
        $dlg.find('.close').click(); 
        $.ajax({
           type: "POST",
           dataType: "json",
           url:$this.attr('data-href') ,
           success: function(data){
                $table.draw('page');
                $.gs_loader.hide();
                if(data.mensaje!='') alert(data.mensaje)
           },
           error: function(response) {
            $table.draw('page');
                $.gs_loader.hide();
                alert(response.responseText.replace(/(<([^>]+)>)/ig,""));
            }

         }); 
    });
    $('#confirm-archivo').on('show.bs.modal', function (e) {
        $this = $(e.relatedTarget);
        $dlg = $(e.delegateTarget);
    });

    $('#confirm-correo').on('show.bs.modal', function (e) {
        $this = $(e.relatedTarget);
        $dlg = $(e.delegateTarget);
        $('#form-confirm').attr('action',$this.attr('data-href'));
         $.ajax({
            url:$this.attr('data-href') ,
            success : function(respuesta) {
              
                $('#corre-confirmar').val(respuesta);
            },
        });
    });

    
    $('#confirm-correo').find('.btn-ok').on('click', function () {
        correo = $('#corre-confirmar').val();
        $dlg.find('.close').click(); 
        $.gs_loader.show('page');
        $.ajax({
           type: "POST",
           url:$this.attr('data-href') ,
           data: {correo: correo},
           success: function(data){
                $table.draw('page');
                $.gs_loader.hide();
           },
           error: function(response) {
                $table.draw('page');
                $.gs_loader.hide();
                alert(response.responseText.replace(/(<([^>]+)>)/ig,""));
            }
         });
    });
    



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
    $table = $dt.load_simpleTable(conf, true,'');
	
	$('#btn-report-excel').on('click',function(){
		//$(location).attr('href',baseurl+"Ventat/reporte_excel/"+$('#desde').val()+"/"+$('#hasta').val()+"/"+$('#comprobantes').val()+"/"+$('#moneda').val();
		window.location.href = baseurl+"Almacen/reporte_excel_inventario/?sucursal="+$('#sucursal').val()+"&search="+$('#filtro').val();
		
	})
    
});
