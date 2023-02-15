<div id="page-wrapper" class="page-filter">
    <div class="page-header col-md-12">
        <div class="row page-header-title">
            <div class="col-sm-6">
                <h3>Cotizaciones </h3></div>
            <div class="col-sm-6 text-right" role="group" aria-label="...">
				<a href="<?php echo base_url() ?>Cotizacion/crear/#0" title="Crear" class="crear btn btn-danger btn-sm">
                    <i class="fa fa-plus fa-fw"></i>
                    Crear pedido
                </a>
            </div>
        </div>
        <div class="row page-header-content">
        	<div class="col-md-12">
            <form class="ocform form-inline">
				<div class="onsel form-inline hidden">
					<a class="btn btn-warning btn-sm correo_todos" href="#">
						<i class="glyphicon glyphicon-envelope"></i>
						Enviar Correos
					</a>
				</div>
				<div class="nosel">
					<div class="form-group">
						<input type="text" class="form-control input-sm" name="search[value]" id="filtro" placeholder="Cliente" value="">
					</div>

					<input type="hidden" name="desde" id="desde"/>
					<input type="hidden" name="hasta" id="hasta"/>
					<div class="form-group">
						<div id="reportrange" class="pull-right" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc;">
							<i class="glyphicon glyphicon-calendar fa fa-calendar"></i>&nbsp;
							<span></span> <b class="caret"></b>
						</div>
					</div>

					<div class="form-group">
						<button type="submit" class="btn btn-primary btn-sm">
							<i class="glyphicon glyphicon-search"></i>
							Filtrar
						</button>
					</div>
					<div class="form-group">
						<button type="button" class="btn btn-primary btn-sm" id="btn-report-excel">
							<i class="glyphicon glyphicon-download-alt"></i>
							Excel
						</button>
					</div>
				</div>
			</form>
		</div>
		</div>
    </div>
    <div class="page-content">
        <div class="col-md-12">


				<div class="alert alert-danger error hidden" role="alert">
					<span class="text">Error:</span>
				</div>

				<?php echo $this->Model_general->genDataTable('mitabla', $columns, true, true); ?>


				<div class="alert alert-info procesos">

				</div>

				<div class="alert alert-danger mensajes">
					<ul class="list-group">
					</ul>
				</div>
		</div>
    </div>
</div>