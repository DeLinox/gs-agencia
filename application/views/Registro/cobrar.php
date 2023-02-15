
<div class="modal-dialog" role="document">
    <div class="modal-content">
		<form id="frm-pago" class="form-horizontal" action="<?=base_url()?>venta/guardar_pago/<?= $venta->vent_id ?>" method="post">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" id="gridSystemModalLabel"></h4>
			</div>
			<div class="modal-body">
				<div class="alert alert-danger error hidden" role="alert">
					<span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
					<span class="sr-only">Error:</span>
				</div>
					<div class="form-group">
						<?=form_label('<strong>Cuenta</strong>', 'cuenta',array('class' => 'col-sm-2 control-label')); ?>
						<div class="col-sm-5"><?=form_dropdown('cuenta', $cuentas,'',array('class' => 'form-control input-sm'));?></div>
						<?=form_label('<strong>Total</strong>', 'total',array('class' => 'col-sm-1 control-label')); ?>
						<div class="col-sm-4">
							<div class="input-group">
	                            <span class="input-group-addon msimb"><?= ($venta->vent_moneda == 'SOLES' ? 'S/ ':'$ ') ?></span>
	                            <input type="text" id="total" name="total" class="form-control input-sm text-right" decimales="2" value="<?= $venta->vent_total ?>" readonly="readonly">
	                        </div>		
						</div>
						<div class="hidden">
							<?=form_label('<strong>Moneda</strong>', 'Moneda',array('class' => 'col-sm-2 control-label')); ?>
							<div class="col-sm-4"><?=form_dropdown('moneda', $moneda, $venta->vent_moneda,array('class' => 'form-control input-sm'));?></div>
						</div>

					</div>
					<div class="hidden">
						<div class="form-group">
							<?=form_label('<strong>Cancelado</strong>', 'cancelado',array('class' => 'col-sm-2 control-label')); ?>
							<div class="col-sm-4"><?=form_input(array("name"=>"cancelado","value"=>$venta->cancelado,"class"=>"form-control input-sm", 'readonly' => 'readonly'));?></div>
						</div>	
						<div class="form-group">
							
						</div>
						<div class="form-group">
							<?=form_label('<strong>Saldo</strong>', 'saldo',array('class' => 'col-sm-2 control-label')); ?>
							<div class="col-sm-4"><?=form_input(array("name"=>"saldo","value"=>$venta->vent_total - $venta->cancelado,"class"=>"form-control input-sm", 'readonly' => 'readonly'));?></div>
							<?=form_label('<strong>Fecha</strong>', 'fecha',array('class' => 'col-sm-2 control-label')); ?>
							<div class="col-sm-4">
	                            <div class='input-group'>
	                                <input id="fecha" name="fecha" type="text" value="" class="form-control fecha input-sm"  />
	                                <span class="input-group-addon">
	                                <span class="glyphicon glyphicon-calendar"></span>
	                            </div>
	                        </div>
	                    </span>
						</div>
						<div class="form-group">
							<?=form_label('<strong>Pagado</strong>', 'pagado',array('class' => 'col-sm-2 control-label')); ?>
							<div class="col-sm-4"><?=form_input(array('id' => 'pagado', "name"=>"pagado","value"=>"","class"=>"form-control input-sm", "value" => $venta->vent_total - $venta->cancelado));?></div>
							<?=form_label('<strong>Vuelto</strong>', 'vuelto',array('class' => 'col-sm-2 control-label')); ?>
							<div class="col-sm-4"><?=form_input(array('id' => 'vuelto', "name"=>"vuelto","value"=>"","class"=>"form-control input-sm", 'readonly' => 'readonly'));?></div>
						</div>
					</div>				
					<div class="form-group">
						<?=form_label('<strong>Descripción</strong>', 'descripcion',array('class' => 'col-sm-2 control-label')); ?>
						<div class="col-sm-10">
							<textarea name="descripcion" rows="3" value='' class="form-control input-sm"></textarea>
						</div>
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
    $(document).ready(met_pago());
</script>