
<div class="modal-dialog" role="document">
    <div class="modal-content">
		<form id="frm-pago" class="form-horizontal" action="<?=base_url()?>flota/guardar/<?= $orde->sepr_id ?>" method="post">
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
					<?=form_label('<strong>Responsable</strong>', 'responsable',array('class' => 'col-sm-2 control-label')); ?>
					<div class="col-sm-10"><?=form_input(array("name"=>"responsable","value"=>$orde->sepr_responsable,"class"=>"form-control input-sm"));?></div>
				</div>	
				<div class="form-group">
					<?=form_label('<strong>Precio (Gal)</strong>', 'precio',array('class' => 'col-sm-2 control-label')); ?>
					<div class="col-sm-4"><?=form_input(array("name"=>"precio","value"=>$orde->sepr_combu_precio,"class"=>"form-control input-sm"));?></div>
					<?=form_label('<strong>Galones</strong>', 'galones',array('class' => 'col-sm-2 control-label')); ?>
					<div class="col-sm-4"><?=form_input(array("name"=>"galones","value"=>$orde->sepr_combu_galones,"class"=>"form-control input-sm"));?></div>
				</div>	
				<div class="form-group">
					<?=form_label('<strong>Moneda</strong>', 'moneda',array('class' => 'col-sm-2 control-label')); ?>
					<div class="col-sm-4"><?=form_input(array("name"=>"","value"=>$orde->sepr_moneda,"class"=>"form-control input-sm", "readonly" => "readonly"));?>
					</div>
					<?=form_label('<strong>Total</strong>', 'saldo',array('class' => 'col-sm-2 control-label')); ?>
					<div class="col-sm-4"><?=form_input(array("name"=>"total","value"=>$orde->sepr_combu_total,"class"=>"form-control input-sm", "readonly" => "readonly"));?>
					</div>
				</div>
				<div class="form-group">
					<?=form_label('<strong>Observación</strong>', 'obs',array('class' => 'col-sm-2 control-label')); ?>
					<div class="col-sm-10"><textarea class="form-control input-sm" name="obs"><?= $orde->sepr_servicio ?></textarea></div>
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
		$('input[name="galones"], input[name="precio"]').change(function(){
	        if(!esNumeroPositivo($(this).val())) { 
	            $(this).dval(0);
	        }else{
	            $(this).dval($(this).val());
	        }
	        actualizaTotal();
	    })
	})
	function actualizaTotal() {
		galones = $('input[name="galones"]').val();
		precio = $('input[name="precio"]').val();
		$('input[name="total"]').dval(Math.m(galones,precio));
	}
</script>