<div id="page-wrapper" class="page-filter">
    <div class="page-header col-md-12">
        <div class="row page-header-title">
            <input id="tipo_usuario" value="<?= $this->session->userdata('authorizedadmin') ?>" type="hidden">
            <div class="col-sm-6">
                <h3>Proveedores </h3>
            </div>
            <div class="col-sm-6 text-right" role="group" aria-label="...">
                <div class="dropdown">
                    <a class="btn btn-sm btn-danger" type="button" href="<?= base_url() ?>Ordenserv/ord_listado">
                        <i class="glyphicon glyphicon-log-out"></i> Ordenes de Servicio
                    </a>
                </div>
            </div>
        </div>
        <div class="row page-header-content">
            <div class="col-md-12">
                <form class="ocform form-inline">
                    <div class="nosel">
                        <div class="form-group">
                            <input type="text" class="form-control input-sm" name="search[value]" id="filtro" placeholder="Buscar" value="">
                        </div>
                        
                        
                        <input type="hidden" name="desde" id="desde" />
                        <input type="hidden" name="hasta" id="hasta" />
                        <div class="form-group">
                            <div id="reportrange" class="pull-right" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc;">
                                <i class="glyphicon glyphicon-calendar fa fa-calendar"></i>&nbsp;
                                <span></span> <b class="caret"></b>
                            </div>
                        </div>
                        <div class="form-group">
                            <?php echo form_dropdown('proveedor', $proveedor, '', array('class' => 'form-control','id' => 'proveedor')); ?>
                        </div>
                        <div class="form-group">
                            <?php echo form_dropdown('moneda', $moneda, '', array('class' => 'form-control','id' => 'moneda')); ?>
                        </div>
                        <div class="form-group">
                            <?php echo form_dropdown('estado', $estado, '', array('class' => 'form-control','id' => 'estado')); ?>
                        </div>
                    
                        <div class="onsel form-group hidden">
                            <a class="btn btn-primary btn-sm gen_ordenPago" href="#">
                                <i class="glyphicon glyphicon-plus"></i>
                                Orden de Pago
                            </a>
                        </div>
                        <div class="form-group pull-right">
                            <div class="dropdown">
                                <button class="btn btn-sm btn-success reporte_excel dropdown-toggle" type="button" data-toggle="dropdown">
                                    <span class="glyphicon glyphicon-download-alt"></span> Exportar
                                </button>
                                <ul class="dropdown-menu  dropdown-menu-right">
                                    <li><a href="#" id="btn-report-excel">  Excel </a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="page-content">
            <?php echo genDataTable('mitabla', $columns, true, true); ?>
    </div>
</div>