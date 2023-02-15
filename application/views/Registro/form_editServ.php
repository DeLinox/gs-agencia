
<div class="modal-dialog" role="document">
    <div class="modal-content">
		<form id="frm-pago" class="form-horizontal" action="<?=base_url()?>Registro/guardar_editServicio/<?= $orde->sepr_id ?>" method="post">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" id="gridSystemModalLabel">Editar</h4>
			</div>
			<div class="modal-body">
				<div class="alert alert-danger error hidden" role="alert">
					<span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
					<span class="sr-only">Error:</span>
				</div>
				<div class="form-group">
					<?=form_label('<strong>Proveedor</strong>', 'responsable',array('class' => 'col-sm-2 control-label')); ?>
					<div class="col-sm-10">
						<h5><?= $orde->emp_rsocial." - ".$orde->prov_rsocial ?></h5>
					</div>
				</div>	
				<div class="form-group">
					<?=form_label('<strong>Precio</strong>', 'precio',array('class' => 'col-sm-2 control-label')); ?>
					<div class="col-sm-4"><?=form_input(array("name"=>"precio","value"=>$orde->sepr_precio,"class"=>"form-control input-sm","type"=>"number","required"=>"required"));?></div>
					<?=form_label('<strong>Cantidad</strong>', 'cantidad',array('class' => 'col-sm-2 control-label')); ?>
					<div class="col-sm-4"><?=form_input(array("name"=>"cantidad","value"=>$orde->sepr_cantidad,"class"=>"form-control input-sm","type"=>"number","required"=>"required"));?></div>
				</div>	
				<div class="form-group">
					<?=form_label('<strong>Moneda</strong>', 'moneda',array('class' => 'col-sm-2 control-label')); ?>
					<div class="col-sm-4"><?=form_input(array("name"=>"","value"=>$orde->sepr_moneda,"class"=>"form-control input-sm", "readonly" => "readonly"));?>
					</div>
					<?=form_label('<strong>Total</strong>', 'saldo',array('class' => 'col-sm-2 control-label')); ?>
					<div class="col-sm-4"><?=form_input(array("name"=>"total","value"=>$orde->sepr_total,"class"=>"form-control input-sm", "readonly" => "readonly"));?>
					</div>
				</div>
				<div class="form-group">
					<?=form_label('<strong>Descripción</strong>', 'descripcion',array('class' => 'col-sm-2 control-label')); ?>
					<div class="col-sm-10"><textarea name="descripcion" class="form-control input-sm" rows="2"><?= $orde->sepr_servicio ?></textarea></div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
				<button type="submit" class="btn btn-primary">Guardar</button>
			</div>
		</form>
    </div>
</div>
<script type="text/javascript">
	$(document).ready(function(){
		$('input[name="cantidad"]').change(function(){
	        if(!esNumeroPositivo($(this).val())) $(this).val(0);
	        else $(this).val(Math.round($(this).val())); 
	        actualizaTotal();
	    })
	    $('input[name="precio"]').change(function(){
	        if(!esNumeroPositivo($(this).val())) $(this).dval(0);
	        else $(this).dval($(this).val());
	        actualizaTotal();
	    })
	})
	function actualizaTotal() {
		cantidad = $('input[name="cantidad"]').val();
		precio = $('input[name="precio"]').val();
		$('input[name="total"]').dval(Math.m(cantidad,precio));
	}
</script>