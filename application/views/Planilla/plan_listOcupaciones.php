<div id="page-wrapper" class="page-filter">
    <div class="page-header col-md-12">
        <div class="row page-header-title">
            <input id="tipo_usuario" value="<?= $this->session->userdata('authorizedadmin') ?>" type="hidden">
            <div class="col-sm-6">
                <h3 style="display: inline-block;"><?php echo $titulo; ?></h3>
            </div>
            <?php $this->load->view("Planilla/opc_planilla") ?>
        </div>
        <div class="row page-header-content">
            <div class="col-md-12">
                <form class="ocform form-inline" action="<?= base_url() ?>Ordenserv/reporte_excelOrdenes" method="POST">
                    <div class="form-group pull-right">
                        <a href="<?= base_url() ?>Planilla/plan_crearOcupacion" class="btn btn-sm btn-primary registrar" type="button">
                            <span class="glyphicon glyphicon-list"></span> Agregar Ocupacion / Cargo
                        </a>
                        <a href="<?= base_url() ?>Planilla/emp_reporteExcel" class="btn btn-sm btn-success registrar" type="button">
                            <span class="glyphicon glyphicon-download-alt"></span> Exportar
                        </a>
                    </div> 
                </form>

            </div>
        </div>
    </div>
    <div class="page-content">
        <div class="table-responsive">
            <table class="table table-striped table-bordered planilla" id="tbl-planilla">
                <thead>
                    <tr>
                        <th class="col-md-2">OPCIONES</th>
                        <th>DESCRIPCIÓN</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>