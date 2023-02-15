<div id="page-wrapper" class="page-filter">
    <div class="page-header col-md-12">
        <div class="row page-header-title">
            <input id="tipo_usuario" value="<?= $this->session->userdata('authorizedadmin') ?>" type="hidden">
            <div class="col-sm-6">
                <h3><?= $titulo ?></h3>
            </div>
            <?php $this->load->view("Planilla/opc_planilla") ?>
        </div>
        <div class="row page-header-content">
            <div class="col-md-12">
                <form class="ocform form-inline" action="<?= base_url() ?>Planilla/reporte_excelEmpleados" method="POST">
                    <div class="nosel">
                        <div class="form-group pull-right">
                            <a href="<?= base_url() ?>Planilla/emp_crear" class="btn btn-sm btn-primary registrar" type="button" title="Crear Periodo">
                                <span class="glyphicon glyphicon-list"></span> Registrar Empleado
                            </a>
                            <button class="btn btn-sm btn-success" type="submit">
                                <span class="glyphicon glyphicon-download-alt"></span> Exportar
                            </button>
						</div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="page-content">
        <div class="table-responsive">
            <?php echo genDataTable('mitabla', $columns, true, true); ?>
        </div>
    </div>
</div>