<?php if($bool==true): 
    $btn_class = "";
    if($venta->vent_fact_situ=='3'||$venta->vent_fact_situ=='4'||$venta->vent_fact_situ=='5'){
      $btn_class = "default";      
    }else{
     $btn_class = "primary";
    }
    
    $btn_class1 = "";
    if($venta->vent_email_send=='1'){
      $btn_class1 = "default";  
    }else{
        $btn_class1 = "primary";
    } 
?>
	
              
<div style="width:100%; margin:0 auto;">

<iframe frameborder="0" src="<?php echo base_url() ?>Venta/genera_pdf/<?php echo $id; ?>/true" width="100%"  height="450" style="padding-top:1px;"></iframe>
</div>
<div class="text-center">
    <a type="button" class="btn btn-warning btn-sm sunat" href="<?php echo base_url() ?>Venta/getXML/<?php echo $id; ?>"><span class='glyphicon glyphicon-file'></span> XML</a>
	<button type="button" class="btn btn-<?=$btn_class?> btn-sm sunat" data-href="<?php echo base_url() ?>Venta/enviarSunat/<?php echo $id; ?>" data-toggle='modal' data-target='#confirm-archivo'><span class='glyphicon glyphicon-refresh'></span> Sunat</button>
    <button type="button" class="btn btn-sm btn-<?=$btn_class1?> enviar" data-href="<?php echo base_url() ?>Venta/confirm_correo/<?php echo $id; ?>" data-toggle='modal' data-target='#confirm-correo'><span class='glyphicon glyphicon-envelope'></span> Enviar</button>
</div>
  <div class="modal fade" id="confirm-archivo" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel">Confirmación</h4>
            </div>
            <div class="modal-body">
                <p>¿Relmente desea generar el archivo?</p>
                <p class="debug-url"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                <a class="btn btn-danger btn-ok">Enviar a la Sunat</a>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="confirm-correo" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel">Confirmación</h4>
            </div>
            <div class="modal-body">
                
                <input class="form-control" type="text" id="corre-confirmar" value="" name="correo">
                
            </div>
            <div class="modal-footer">
                <button  type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger btn-ok">Enviar correo</button>
            </div>
        </div>
    </div>
</div>

<?php else: ?>

<div class="modal-dialog" role="document" style="width:800px">
    <div class="modal-content">
        <form class="form-horizontal" action="<?= base_url() ?>Cotizacion/confirm_correo/<?php echo $id; ?>" method="post">

        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title" id="gridSystemModalLabel">Cotización</h4>
        </div>
        <div class="modal-body">
        <div class="">
            <iframe frameborder="0" src="<?php echo base_url() ?>Venta/genera_pdf/<?php echo $id; ?>/true" width="100%"  height="450" style="padding-top:1px;"></iframe>

        </div>

            
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
        </div>

    </div>
</div>     
<?php endif; ?>
