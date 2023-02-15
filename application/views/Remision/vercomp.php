<!--
<div class="modal-dialog" style="width:350px;">
	<div class="modal-content">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			<h4 class="modal-title" id="myModalLabel">Ver comprobante</h4>
		</div>
		<div class="modal-body" style="height:500px;overflow:scroll;">
			<pre style="background:#fff; text-align:center"><?php echo file_get_contents(base_url().'Service/get/'.$id); ?></pre>
		</div>
		<div class="modal-footer">
			<a href="appurl://<?php echo $id; ?>" class="btn btn-success btn-ok">Imprimir Factura</a>
			<button  type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
		</div>
	</div>
</div>
-->
<div class="modal-dialog" style="width:800px;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel">Ver factura</h4>
            </div>
            <div class="modal-body" style="height:500px;">
                <iframe frameborder="0" src="<?php echo base_url() ?>Remision/pdf/<?php echo $id; ?>" width="100%"  height="100%" style="padding-top:1px;"></iframe>
            </div>
            <div class="modal-footer">
                
            </div>
        </div>
</div>