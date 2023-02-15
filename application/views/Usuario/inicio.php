<div id="page-wrapper" class="page-filter">
	<div class="page-header col-md-12">
		<div class="row page-header-title">
			<div class="col-sm-6">
				<h3>Todos los usuarios</h3></div>
			<div class="col-sm-6 text-right" role="group" aria-label="...">
					<a href="<?php echo base_url()?>usuario/form/" title="Crear usuario" class="crear btn btn-danger btn-sm">
					<i class="glyphicon glyphicon-plus"></i>
					Crear usuario
				</a>
			</div>
		</div>
		<div class="row page-header-content">
			<div class="onsel form-inline col-xs-12 hidden">
				<a class="btn btn-default btn-sm borrar" href="<?=base_url()?>usuario/borrar">
					<i class="glyphicon glyphicon-trash"></i>
					Borrar
				</a>
			</div>
			<div class="nosel">
				<div class="col-sm-9">
					<form class="ocform form-inline">
						<div class="form-group">
						<input type="text" class="form-control input-sm" name="search[value]" id="filtro" placeholder="Filtro" value="">
						</div>
						<div class="form-group">
						<button type="submit" class="btn btn-primary btn-sm">
							<i class="glyphicon glyphicon-search"></i>
							Filtrar
						</button>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
	<div class="page-content">
		<div class="col-md-12">
		<?php  echo $this->Model_general->genDataTable('mitabla', $columns, true,true); ?>
		</div>
	</div>
</div>