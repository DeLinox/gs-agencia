<div id="page-wrapper" class="page-filter">
    <div class="page-header col-md-12">
        <div class="row page-header-title">
            <div class="col-sm-6">
                <h3>Resumen </h3>
			</div>
			<?php $this->load->view("Reporte/opc_reportes") ?>
        </div>

        <div class="row page-header-content">
        	<div class="col-md-12">
            <form class="ocform form-inline">
				<div class="nosel">
					<div class="form-group">
						<?= form_dropdown('mes', $mes, $cur_mes, array('class' => 'form-control input-sm')); ?>
					</div>
					<div class="form-group">
						<?= form_dropdown('anio', $anio, $cur_mes, array('class' => 'form-control input-sm')); ?>
					</div>
					<div class="form-group">
						<?= form_dropdown('tipo', $tipos, 'ESPERADO', array('class' => 'form-control input-sm')); ?>
					</div>
					<div class="form-group">
						<button type="submit" class="btn btn-primary btn-sm">
							<i class="glyphicon glyphicon-search"></i>
							Filtrar
						</button>
					</div>
					<div class="form-group pull-right">
						<a type="button" href="<?= base_url() ?>Reporte/crear_resultado" class="btn btn-danger btn-sm crear"><span class="glyphicon glyphicon-share-alt"></span> Registrar Periodo</a>
						<button class="btn btn-sm btn-success actualizar" type="button">
							<span class="glyphicon glyphicon-cog"></span> Actualizar
						</button>
						<a type="button" href="<?= base_url() ?>Reporte/actualiza_resultado?moneda=SOLES" class="btn btn-info btn-sm actualiza oculto">
							<span class="glyphicon glyphicon-share-alt"></span> Actualiza Soles
						</a>
						<a type="button" href="<?= base_url() ?>Reporte/actualiza_resultado?moneda=DOLARES" class="btn btn-info btn-sm actualiza oculto">
							<span class="glyphicon glyphicon-share-alt"></span> Actualiza Dolares
						</a>
					</div>
				</div>
			</form>
		</div>
		</div>
    </div>
    <div class="page-content">
        <div class="col-md-8 col-md-offset-2">
			<div class="alert alert-danger error hidden" role="alert">
				<span class="text">Error:</span>
			</div>
			<table class="table table-striped table-bordered" id="tbl-resultados"></table>
		</div>
    </div>
</div>