<div class="modal-dialog" role="document">
    <div class="modal-content">
		<form class="form-horizontal" action="<?=base_url()?>usuario/guardar" method="post">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" id="gridSystemModalLabel"></h4>
			</div>
			<div class="modal-body">
				<div class="alert alert-danger error hidden" role="alert">
					<span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
					<span class="sr-only">Error:</span>
				</div>
				<?php
				 echo form_hidden('usuario',$usuario->usua_id );

				?>
					<div class="form-group">
						<?=form_label('<strong>Nombres</strong>', 'nombres',array('class' => 'col-sm-2 control-label')); ?>
						<div class="col-sm-4"><?=form_input(array("name"=>"nombres","value"=>$usuario->usua_nombres,"class"=>"form-control input-sm"));?></div>
						<?=form_label('<strong>Apellidos</strong>', 'apellidos',array('class' => 'col-sm-2 control-label')); ?>
						<div class="col-sm-4"><?=form_input(array("name"=>"apellidos","value"=>$usuario->usua_apellidos,"class"=>"form-control input-sm"));?></div>
					</div>	
					<div class="form-group">
						<?=form_label('<strong>Dni</strong>', 'dni',array('class' => 'col-sm-2 control-label')); ?>
						<div class="col-sm-4"><?=form_input(array("name"=>"dni","value"=>$usuario->usua_dni,"class"=>"form-control input-sm"));?></div>
					</div>
					<div class="form-group">
						<?=form_label('<strong>Email</strong>', 'email',array('class' => 'col-sm-2 control-label')); ?>
						<div class="col-sm-4"><?=form_input(array("name"=>"email","value"=>$usuario->usua_email,"class"=>"form-control input-sm"));?></div>
						<?=form_label('<strong>Movil</strong>', 'movil',array('class' => 'col-sm-2 control-label')); ?>
						<div class="col-sm-4"><?=form_input(array("name"=>"movil","value"=>$usuario->usua_movil,"class"=>"form-control input-sm"));?></div>
					</div>
					<hr>
					<div class="form-group">
						<?=form_label('<strong>Usuario</strong>', 'usuario',array('class' => 'col-sm-2 control-label')); ?>
						<div class="col-sm-4"><?=form_input(array("name"=>"login","value"=>$usuario->usua_user,"class"=>"form-control input-sm"));?></div>
						<?=form_label('<strong>password</strong>', 'password',array('class' => 'col-sm-2 control-label')); ?>
						<?=form_checkbox(array('name'=> 'active','value'=>"active",'checked'=> false,"class"=>"col-sm-1 activar"));?>
						<div class="col-sm-3"><?=form_input(array("name"=>"password","type"=>"password","value"=>"","class"=>"form-control input-sm password"));?></div>
					</div>
					<div class="form-group">
						<?=form_label('<strong>Tipo</strong>', 'tipo',array('class' => 'col-sm-2 control-label')); ?>
						<div class="col-sm-4"><?=form_dropdown('tipo', $options,$usuario->usua_tipo_id,array('class' => 'form-control input-sm'));?></div>
						<?=form_label('<strong>Habilitado</strong>', 'estado',array('class' => 'col-sm-2 control-label')); ?>
						<?php if($usuario->usua_habilitado==1){$checked=true;}else{$checked=false;}?>
						<div class="col-sm-4"><?=form_checkbox(array('name'=> 'habilitado','value'=>$usuario->usua_habilitado,'checked'=> $checked));?></div>
					</div>	
					<div class="form-group">
						<?=form_label('<strong>Sucursal</strong>', 'sucursal',array('class' => 'col-sm-2 control-label')); ?>
						<div class="col-sm-4"><?=form_dropdown('sucursal', $sucursal,$usuario->suus_suco_id,array('class' => 'form-control input-sm'));?></div>
					</div>	

			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
				<button type="submit" class="btn btn-primary">Guardar</button>
			</div>
		</form>
    </div>
</div>
