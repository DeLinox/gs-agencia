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
                        <a href="<?= base_url() ?>Planilla/plan_adides/<?= $plan_id ?>" class="btn btn-sm btn-primary registrar" type="button">
                            <span class="glyphicon glyphicon-list"></span> Agregar Adicion o descuento
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
            <?= $ref ?>
        </div>
        <div class="table-responsive">
            <table class="table table-striped table-bordered planilla" id="tbl-planilla">
                <thead>
                    <tr>
                        <th rowspan="2" class="vmiddle">#</th>
                        <th rowspan="2" class="vmiddle">FECHA</th>
                        <th rowspan="2" class="vmiddle">CONCEPTO</th>
                        <th rowspan="2" class="vmiddle">GASTOS DE LA EMPRESA</th>
                        <th colspan="2">INGRESOS DEL TRABAJADOR</th>
                        <th rowspan="2" class="vmiddle">OTROS</th>
                        <th rowspan="2" class="vmiddle">TOTAL DESCUENTOS Y ADICIONES</th>
                    </tr>
                    <tr>
                        <th>ADICIONAL</th>
                        <th>DESCUENTO</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>