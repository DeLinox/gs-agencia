<div id="page-wrapper" class="page-filter">
    <div class="page-header col-md-12">
        <div class="row page-header-title">
            <div class="col-sm-6">
                <h3>Listado de Pagos </h3></div>
        </div>
        <div class="row page-header-content">
        	<div class="col-md-12">
            <form class="ocform form-inline">
				<div class="nosel">
					
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