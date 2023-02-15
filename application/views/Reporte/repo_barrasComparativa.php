
<div id="page-wrapper">
    <div class="page-header col-md-12">
        <div class="row page-header-title">
            <div class="col-sm-6">
                <h3 style="display: inline-block;"><?php echo $titulo; ?></h3>
            </div>
            <?php $this->load->view("Reporte/opc_reportes") ?>
        </div>
    </div>
    <div class="page-content serv">
        <div class="col-md-10 col-md-offset-1">
            <div class="alert alert-danger error hidden" role="alert">
                <span class="text">Error:</span>
            </div>
            <form class="ocform form-inline" action="<?= base_url() ?>Reporte/data_barrasComparativa" method="POST">
                <div class="nosel">
                    <div class="form-group">
                        <?php echo form_dropdown('tipo', $tipo, 'SERVICIOS', array('class' => 'form-control input-sm actualiza')); ?>
                    </div>
                    <div class="form-group">
                        <?php echo form_dropdown('anio', $anio, $sel_anio, array('class' => 'form-control input-sm actualiza')); ?>
                    </div>
                    <div class="form-group">
                        <?php echo form_dropdown('mes', $mes, $sel_mes, array('class' => 'form-control input-sm actualiza')); ?>
                    </div>
					<div class="form-group">
                        <?php echo form_dropdown('detalles', array("" => "* Seleccion general"), '', array('class' => 'form-control input-sm')); ?>
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary btn-sm">
                            <i class="glyphicon glyphicon-search"></i>
                            Filtrar
                        </button>
                    </div>
                    
                </div>
            </form>
            </br>
            <div id="reporte_grafico" style="min-width: 300px; height: 400px; margin: 0 auto"></div>
            <div class="col-sm-6">
                <table id="table-content" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th></th>
                            <th>LOCAL</th>
                            <th>RECEPTIVO</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
			<div class="col-sm-4">
                <table id="table-total" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th></th>
                            <th>TOTAL</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">


</script>