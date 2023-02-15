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
				 echo form_hidden('usuario',$usua->id );
				?>
					<div class="form-group">
						<?=form_label('<strong>Nombres</strong>', 'nombres',array('class' => 'col-sm-2 control-label')); ?>
						<div class="col-sm-4"><?=form_input(array("name"=>"nombres","value"=>$usua->nombres,"class"=>"form-control input-sm"));?></div>
						<?=form_label('<strong>Tipo reserva</strong>', 'nombres',array('class' => 'col-sm-2 control-label')); ?>
						<div class="col-sm-4"><?=form_dropdown("tipo",array("0" => "Ambos","1"=>"Local", "2"=>"Receptivo"),$usua->tipo,array("class"=>"form-control input-sm"));?></div>
					</div>
					<div class="form-group">
						<?=form_label('<strong>Email</strong>', 'email',array('class' => 'col-sm-2 control-label')); ?>
						<div class="col-sm-4"><?=form_input(array("name"=>"email","value"=>$usua->email,"class"=>"form-control input-sm"));?></div>
						<?=form_label('<strong>Movil</strong>', 'movil',array('class' => 'col-sm-2 control-label')); ?>
						<div class="col-sm-4"><?=form_input(array("name"=>"movil","value"=>$usua->cel,"class"=>"form-control input-sm"));?></div>
					</div>
					<hr>
					<div class="form-group">
						<?=form_label('<strong>Usuario</strong>', 'usuario',array('class' => 'col-sm-2 control-label')); ?>
						<div class="col-sm-4"><?=form_input(array("name"=>"login","value"=>$usua->user,"class"=>"form-control input-sm"));?></div>
						<?=form_label('<strong>password</strong>', 'password',array('class' => 'col-sm-2 control-label')); ?>
						<?=form_checkbox(array('name'=> 'active','value'=>"active",'checked'=> false,"class"=>"col-sm-1 activar"));?>
						<div class="col-sm-3"><?=form_input(array("name"=>"password","type"=>"password","value"=>"","class"=>"form-control input-sm password"));?></div>
					</div>
					<?php if ($this->session->userdata("authorizedadmin")): ?>
					<div class="form-group">
						<?=form_label('<strong>Habilitado</strong>', 'habilitado',array('class' => 'col-sm-2 control-label')); ?>
						<div class="col-sm-4"><?=form_dropdown('habilitado', array("1" => "SI", "0"=> "NO"),$usua->habilitado,array('class' => 'col-sm-2 form-control')); ?></div>
					</div>
					<?php endif ?>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
				<button type="submit" class="btn btn-primary">Guardar</button>
			</div>
		</form>
    </div>
</div>
