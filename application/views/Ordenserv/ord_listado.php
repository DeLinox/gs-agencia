<div id="page-wrapper" class="page-filter">
    <div class="page-header col-md-12">
        <div class="row page-header-title">
            <input id="tipo_usuario" value="<?= $this->session->userdata('authorizedadmin') ?>" type="hidden">
            <div class="col-sm-6">
                <h3>Ordedes de Servicio </h3>
            </div>
            <div class="col-sm-6 text-right" role="group" aria-label="...">
                <!--
                <a class="btn btn-sm btn-danger" type="button" href="<?= base_url() ?>Ordenserv/ord_listadoPago">
                    <i class="glyphicon glyphicon-log-out"></i> Ordenes de Pago
                </a>
                
                <a class="btn btn-sm btn-danger" type="button" href="<?= base_url() ?>Ordenserv/ord_proveedores">
                    <i class="glyphicon glyphicon-log-out"></i> Proveedores
                </a>
                -->
            </div>
        </div>
        <div class="row page-header-content">
            <div class="col-md-12">
                <form class="ocform form-inline" action="<?= base_url() ?>Ordenserv/reporte_excelOrdenes" method="POST">
                    
                    <div class="form-group">
                        <input type="text" class="form-control input-sm" name="search[value]" id="filtro" placeholder="Cliente" value="">
                    </div>
                    <div class="form-group">
                    </div>
                    <input type="hidden" name="desde" id="desde" value="<?php echo $poststr['desde'] ?>" />
                        <input type="hidden" name="hasta" id="hasta" value="<?php echo $poststr['hasta'] ?>" />
                    <div class="form-group">
                        <div id="reportrange" class="pull-right" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc;">
                            <i class="glyphicon glyphicon-calendar fa fa-calendar"></i>&nbsp;
                            <span></span> <b class="caret"></b>
                        </div>
                    </div>
                    <div class="form-group pull-right">
                        <button class="btn btn-sm btn-success" type="submit">
                            <span class="glyphicon glyphicon-download-alt"></span> Exportar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="page-content">
            <?php echo genDataTable('mitabla', $columns, true, true); ?>
    </div>
</div>