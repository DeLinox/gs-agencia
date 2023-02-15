<div class="modal-dialog" role="document">
    <div class="modal-content">
		<form class="form-horizontal" action="<?=base_url()?>Configuracion/guardar_permisos/<?= $id ?>" method="post">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" id="gridSystemModalLabel"></h4>
			</div>
			<div class="modal-body">
				<div class="alert alert-danger error hidden" role="alert">
					<span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
					<span class="sr-only">Error:</span>
				</div>
				<table class="table table-striped table-bordered">
					<thead>
						<tr>
							<th>Permiso</th>
							<th>Nivel de Acceso</th>
							<th>Permiso</th>
							<th>Nivel de Acceso</th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ($permisos as $i => $mod): if($mod->mod_id != 15): if($i%2 == 0) echo "<tr>"; ?>
							<td><?= $mod->mod_nombre ?></td>
							<input type="hidden" name="modulo[]" value="<?= $mod->mod_id ?>">
							<td><?=form_dropdown("nivel[]",$niveles,$mod->nivel_acceso,array("class"=>"form-control input-sm"));?></td>
						<?php if($i%2 != 0) echo "</tr>"; endif; endforeach; ?>
					</tbody>
				</table>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
				<button type="submit" class="btn btn-primary">Guardar</button>
			</div>
		</form>
    </div>
</div>
