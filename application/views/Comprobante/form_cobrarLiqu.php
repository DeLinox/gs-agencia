
<div class="modal-dialog" role="document">
    <div class="modal-content">
		<form id="frm-pago" class="form-horizontal" action="<?=base_url()?>Comprobante/guardar_cobroLiquidacion/<?= $liquidacion->id ?>" method="post">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" id="gridSystemModalLabel">Realizar cobro: <?= $liquidacion->numero." - ".$liquidacion->cliente ?></h4>
			</div>
			<div class="modal-body">
				<div class="alert alert-danger error hidden" role="alert">
					<span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
					<span class="sr-only">Error:</span>
				</div>
				    <div class="form-group">
				        <?=form_label('<strong>Moneda</strong>', 'Moneda',array('class' => 'col-sm-2 control-label')); ?>
						<div class="col-sm-4"><?=form_dropdown('moneda', $moneda, $liquidacion->moneda,array('class' => 'form-control input-sm', "disabled" => "disabled"));?></div>
				    </div>
					<div class="form-group hidden">
						<?=form_label('<strong>Documento</strong>', 'documento',array('class' => 'col-sm-2 control-label')); ?>
						<div class="col-sm-4"><?=form_dropdown('documento', $documentos, '10',array('class' => 'form-control input-sm'));?>
						</div>

						<div class="col-sm-3"><?=form_input(array("name"=>"serie","class"=>"form-control input-sm","placeholder" =>"Serie"));?>
						</div>
						<div class="col-sm-3"><?=form_input(array("name"=>"numero","class"=>"form-control input-sm","placeholder" =>"N¨²mero"));?>
						</div>
                        
					</div>
					<div class="form-group">
						<?=form_label('<strong>Cuenta</strong>', 'cuenta',array('class' => 'col-sm-2 control-label')); ?>
						<div class="col-sm-4"><?=form_dropdown('cuenta', $cuentas, '10',array('class' => 'form-control input-sm', "required" => "required"));?>
						</div>
						<?=form_label('<strong>C&oacutedigo CTA</strong>', 'codigo',array('class' => 'col-sm-2 control-label')); ?>
						<div class="col-sm-4">
	                        <div class='input-group'>
	                        	<span class="input-group-addon ini-cuen"></span>
	                            <input type='text' name="codigo-cuen" class="form-control" required/>
	                            
	                        </div>
                        </div>
					</div>
					<div class="form-group">
						<?=form_label('<strong>Total</strong>', 'total',array('class' => 'col-sm-2 control-label')); ?>
						<div class="col-sm-4"><?=form_input(array("name"=>"total","value"=>$liquidacion->total,"class"=>"form-control input-sm", 'readonly' => 'readonly'));?></div>
						<?=form_label('<strong>Cancelado</strong>', 'cancelado',array('class' => 'col-sm-2 control-label')); ?>
						<div class="col-sm-4"><?=form_input(array("name"=>"cancelado","value"=>$liquidacion->cancelado,"class"=>"form-control input-sm", 'readonly' => 'readonly'));?></div>
					</div>	
					<div class="form-group">
						<?=form_label('<strong>Saldo</strong>', 'saldo',array('class' => 'col-sm-2 control-label')); ?>
						<div class="col-sm-4"><?=form_input(array("name"=>"saldo","value"=>$liquidacion->total - $liquidacion->cancelado,"class"=>"form-control input-sm", 'readonly' => 'readonly'));?></div>
						<div class="">
						<?=form_label('<strong>Fecha</strong>', 'fecha',array('class' => 'col-sm-2 control-label')); ?>
						<div class="col-sm-4">
                            <div class='input-group'>
                                <input id="fecha" name="fecha" type="text" value="" class="form-control fecha input-sm"  />
                                <span class="input-group-addon">
                                <span class="glyphicon glyphicon-calendar"></span>
                            </div>
                        </div>
                        </div>
                        
                    </span>
					</div>
					<div class="form-group">
						<?=form_label('<strong>Pagado</strong>', 'pagado',array('class' => 'col-sm-2 control-label')); ?>
						<div class="col-sm-4"><?=form_input(array('id' => 'pagado', "name"=>"pagado","value"=>"","class"=>"form-control input-sm","required"=>"required"));?></div>
						<?=form_label('<strong>Vuelto</strong>', 'vuelto',array('class' => 'col-sm-2 control-label')); ?>
						<div class="col-sm-4"><?=form_input(array('id' => 'vuelto', "name"=>"vuelto","value"=>"","class"=>"form-control input-sm", 'readonly' => 'readonly'));?></div>
					</div>					
					<div class="form-group">
						<?=form_label('<strong>DescripciÃ³n</strong>', 'descripcion',array('class' => 'col-sm-2 control-label')); ?>
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