
<div class="modal-dialog" role="document">
    <div class="modal-content">
		<form id="frm-pago" class="form-horizontal" action="<?=base_url()?>almacen/guardar_pago/<?= $movi->movi_id ?>" method="post">
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
	                            <span class="input-group-addon msimb"><?= ($movi->movi_moneda == 'SOLES' ? 'S/ ':'$ ') ?></span>
	                            <input type="text" id="total" name="total" class="form-control input-sm text-right" decimales="2" value="<?= $movi->movi_total ?>" readonly="readonly">
	                        </div>		
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
