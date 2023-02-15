
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
            <form class="ocform form-inline">
                <div class="nosel">
				<!--
                <div class="form-group">
                    <?php echo form_dropdown('tipo', $tipo, 'TERCERO', array('class' => 'form-control input-sm')); ?>
                </div>
				-->
                <div class="form-group">
                    <?php echo form_dropdown('moneda', $moneda, 'SOLES', array('class' => 'form-control input-sm')); ?>
                </div>
                <div class="form-group">
                    <?php echo form_dropdown('mes', $mes, $sel_mes, array('class' => 'form-control input-sm')); ?>
                </div>
                <div class="form-group">
                    <?php echo form_dropdown('anio', $anio, $sel_anio, array('class' => 'form-control input-sm')); ?>
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
            <div id="informe_gastos"></div>
        </div>
    </div>
</div>