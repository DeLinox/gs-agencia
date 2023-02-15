
<div class="modal-dialog modal-pagar  modal-local" role="document">
    <div class="modal-content">
		<form id="frm-pago" class="form-horizontal" action="<?=base_url()?>ordenserv/guardar_pago/<?= $orde->id ?>" method="post">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" id="gridSystemModalLabel">Realizar cobro: OP-<?= $orde->numero ?></h4>
			</div>
			<div class="modal-body">
				<div class="alert alert-danger error hidden" role="alert">
					<span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
					<span class="sr-only">Error:</span>
				</div>
				<h3 >Realizar pago: OP-<?= $orde->numero." : ".$orde->prov_name ?></h3>
				<input type="hidden" name="moneda" value="<?= $orde->moneda ?>">
				<div class="table-responsive">
					<table class="table table-striped table-bordered">
						<tr>
							<th>#</th>
							<th>FECHA</th>
							<th>HORA</th>
							<th>SERVICIO</th>
							<th>GUIA</th>
							<th>M</th>
							<th>CANT</th>
							<th>PRECIO</th>
							<th>TOTAL</th>
						</tr>
					<?php foreach ($detas as $k => $val): ?>
						<tr>
							<td><?= ($k+1) ?></td>
							<td><?= $val->fecha ?></td>
							<td><?= $val->hora ?></td>
							<td><?= $val->servicio ?></td>
							<td><?= $val->guia ?></td>
							<td><?= ($val->moneda == 'SOLES')?"S/ ":"$ " ?></td>
							<td class="text-right"> <?= $val->cantidad ?></td>
							<td class="text-right"> <?= $val->precio ?></td>
							<td class="text-right"> <?= $val->total ?></td>
						</tr>
					<?php endforeach ?>
					</table>
					
				</div>
				<div class="form-group">
					<?=form_label('<strong>Documento</strong>', 'documento',array('class' => 'col-sm-2 control-label')); ?>
					<div class="col-sm-4"><?=form_dropdown('documento', $documentos, '10',array('class' => 'form-control input-sm'));?>
					</div>

					<div class="col-sm-2"><?=form_input(array("name"=>"serie","class"=>"form-control input-sm","placeholder" =>"Serie"));?>
					</div>
					<div class="col-sm-2"><?=form_input(array("name"=>"numero","class"=>"form-control input-sm","placeholder" =>"Número"));?>
					</div>
                    
				</div>
				<div class="form-group">
					<?=form_label('<strong>Cuenta</strong>', 'cuenta',array('class' => 'col-sm-2 control-label')); ?>
					<div class="col-sm-2"><?=form_dropdown('cuenta', $cuentas, '10',array('class' => 'form-control input-sm', "required" => "required"));?>
					</div>
					<?=form_label('<strong>C&oacutedigo CTA</strong>', 'codigo',array('class' => 'col-sm-2 control-label')); ?>
					<div class="col-sm-2">
                        <div class='input-group'>
                        	<span class="input-group-addon ini-cuen"></span>
                            <input type='text' name="codigo_cuen" class="form-control" required/>
                            
                        </div>
                    </div>
				</div>
				<div class="form-group">
					<?=form_label('<strong>Total</strong>', 'total',array('class' => 'col-sm-2 control-label')); ?>
					<div class="col-sm-2"><?=form_input(array("name"=>"total","value"=>$orde->total,"class"=>"form-control input-sm", 'readonly' => 'readonly'));?></div>
					<?=form_label('<strong>Cancelado</strong>', 'cancelado',array('class' => 'col-sm-2 control-label')); ?>
					<div class="col-sm-2"><?=form_input(array("name"=>"cancelado","value"=>$orde->cancelado,"class"=>"form-control input-sm", 'readonly' => 'readonly'));?></div>
				</div>	
				<div class="form-group">
					<?=form_label('<strong>Pagado</strong>', 'pagado',array('class' => 'col-sm-2 control-label')); ?>
					<div class="col-sm-2"><?=form_input(array('id' => 'pagado', "name"=>"pagado","value"=>"","class"=>"form-control input-sm"));?></div>
					<?=form_label('<strong>Saldo</strong>', 'saldo',array('class' => 'col-sm-2 control-label')); ?>
					<div class="col-sm-2"><?=form_input(array("name"=>"saldo","value"=>$orde->total - $orde->cancelado,"class"=>"form-control input-sm", 'readonly' => 'readonly'));?>
					</div>
				</div>
				
				<div class="form-group">
					<?=form_label('<strong>Observación</strong>', 'observacion',array('class' => 'col-sm-2 control-label')); ?>
					<div class="col-sm-8">
						<textarea name="observacion" rows="3" value='' class="form-control input-sm"></textarea>
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
	$(document).ready(function(){
		$('select[name="cuenta"]').change(function(){
			if($(this).val() != ''){
				$.ajax({
		            dataType: "json",
		            url: baseurl+"registro/get_cod_cuen/"+$(this).val(),
		            success: function(resp){
		                $('.ini-cuen').text(resp.codigo);
		            }
		        })	
		        return false;
	        }else{
	        	$('.ini-cuen').text('');
	        }
		})
	})
</script>