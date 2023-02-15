<div class="modal-dialog" role="document">
    <div class="modal-content">
		<form class="form-horizontal" action="" method="post" id="perfil_form" enctype="multipart/form-data">
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
							<?=form_label('<strong>Nombres:</strong>', 'nombres',array('class' => 'col-sm-2 text_right')); ?>
							<div class="col-sm-4">
								<?=form_label($usuario->usua_nombres, 'nombres',array('class' => 'text_right')); ?>
							</div>
							<?=form_label('<strong>Apellidos:</strong>', 'apellidos',array('class' => 'col-sm-2 text_right')); ?>
							<div class="col-sm-4">
								<?=form_label($usuario->usua_apellidos, 'nombres',array('class' => 'text_right')); ?>
							</div>
						</div>
						<div class="form-group">
							<?=form_label('<strong>Dni:</strong>', 'dni',array('class' => 'col-sm-2 text_right')); ?>
							<div class="col-sm-4">
								<?=form_label($usuario->usua_dni, 'dni',array('class' => 'text_right')); ?>
							</div>
						</div>
						<div class="form-group">
							<?=form_label('<strong>email:</strong>', 'email',array('class' => 'col-sm-2')); ?>
							<div class="col-sm-4">
								<?=form_input(array("name"=>"email","value"=>$usuario->usua_email,"class"=>"form-control input-sm"));?>
							</div>
							<?=form_label('<strong>Movil:</strong>', 'movil',array('class' => 'col-sm-2 ')); ?>
							<div class="col-sm-4">
								<?=form_input(array("name"=>"movil","value"=>$usuario->usua_movil,"class"=>"form-control input-sm"));?>
							</div>
						</div>
					
						<hr>
						<div class="form-group">
							<?=form_label('<strong>Cambiar contraseña:</strong>', 'nombres',array('class' => 'col-sm-4 text_right')); ?>
							<div class="col-sm-4">
								
								<?php  echo form_checkbox(array('name'=> 'active','id'=> 'act_password','value'=> '','checked'=> FALSE));
								?>
							</div>
							
						</div>
						<hr>
						<div class="form-group">
							<?=form_label('<strong>Contraseña Anterior:</strong>', 'password',array('class' => 'col-sm-4')); ?>
							<div class="col-sm-6">
								<?=form_input(array("name"=>"old_password","type"=>"password","value"=>"","class"=>"form-control input-sm password"));?>
							</div>
						</div>
						<div class="form-group">
							<?=form_label('<strong>Nueva contraseña:</strong>', 'password',array('class' => 'col-sm-4')); ?>
							<div class="col-sm-6">
								<?=form_input(array("name"=>"new_password","type"=>"password","value"=>"","class"=>"form-control input-sm password"));?>
							</div>
							<?=form_label('<strong>Repetir contraseña:</strong>', 'password',array('class' => 'col-sm-4 ')); ?>
							<div class="col-sm-6">
								<?=form_input(array("name"=>"new2_password","type"=>"password","value"=>"","class"=>"form-control input-sm password"));?>
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
