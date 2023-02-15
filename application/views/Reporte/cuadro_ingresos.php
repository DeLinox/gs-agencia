
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
                <div class="form-group">
                    <input type="text" name="search" class="form-control input-sm" placeholder="Buscar contacto">
                </div>
                <?php if (!isset($igv)): ?>
                <div class="form-group">
                    <?php echo form_dropdown('tipo', $tipo, $sel_tipo, array('class' => 'form-control input-sm')); ?>
                </div>
                <?php endif ?>
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
					<button type="button" class="btn btn-success btn-sm cuadroIngresos">
                        <i class="glyphicon glyphicon-save-file"></i>
                        Exportar
                    </button>
                </div>
                </div>
            </form>
            </br>
            <table class="table table-striped table-bordered table-hover" id="cuadro_ingresos">
                <thead>
                    <tr>
                        <th></th>
                        <th colspan="3" class="text-center">SOLES</th>
                        <th colspan="3" class="text-center">DOLARES</th>
                    </tr>
                    <tr>
                        <th>CONTACTO</th>
                        <th class="text-center">COBRADO</th>
                        <th class="text-center">SALDO</th>
                        <th class="text-center">TOTAL</th>
                        <th class="text-center">COBRADO</th>
                        <th class="text-center">SALDO</th>
                        <th class="text-center">TOTAL</th>
                    </tr>
                </thead>
                <tbody>
                    
                </tbody>
            </table>    
        </div>
    </div>
</div>