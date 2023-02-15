
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
            <form class="ocform form-inline" action="<?= base_url() ?>Reporte/data_barras" method="POST">
                <div class="nosel">
                    <div class="form-group">
                        <?php echo form_dropdown('tipo', $tipo, '', array('class' => 'form-control input-sm actualiza')); ?>
                    </div>
                    <div class="form-group">
                        <?php echo form_dropdown('tipor', $tipor, '', array('class' => 'form-control input-sm actualiza')); ?>
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
                    <!--
                    <div class="form-group">
                        <input type="hidden" name="desde" id="desde" />
                        <input type="hidden" name="hasta" id="hasta" />
                        <div class="form-group">
                            <div id="reportrange" class="pull-right" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc;">
                                <i class="glyphicon glyphicon-calendar fa fa-calendar"></i>&nbsp;
                                <span></span> <b class="caret"></b>
                            </div>
                        </div>
                    </div>
                    -->
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
        </div>
    </div>
</div>
<script type="text/javascript">
	

</script>