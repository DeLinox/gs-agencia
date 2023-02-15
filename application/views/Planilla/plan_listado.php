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
                    
                    <div class="form-group">
                        <?= form_dropdown('mes', $meses, $cur_mes, array('class' => 'form-control input-sm')); ?>
                    </div>
                    <div class="form-group">
                        <?= form_dropdown('anio', $anios, $cur_anio, array('class' => 'form-control input-sm')); ?>
                    </div>
                    <div class="form-group">
                        <button class="btn btn-primary btn-sm" type="button"> Filtrar</button>
                    </div>
                    <div class="form-group pull-right">
                        <a href="<?= base_url() ?>Planilla/plan_add_emp" class="btn btn-sm btn-primary registrar" type="button">
                            <span class="glyphicon glyphicon-list"></span> Asignar Empleado a planilla
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
                        <th rowspan="2" class="vmiddle">#</th>
                        <th rowspan="2" class="vmiddle">DNI</th>
                        <th rowspan="2" class="vmiddle">CUSPP</th>
                        <th rowspan="2" class="vmiddle">APELLIDOS Y NOMBRES</th>
                        <th rowspan="2" class="vmiddle">FECHA INGRESO</th>
                        <th rowspan="2" class="vmiddle">CARGO</th>
                        <th rowspan="2" class="vmiddle">ASIGNACIÓN FAMILIAR</th>
                        <th colspan="3">INGRESOS DEL TRABAJADOR</th>
                        <th rowspan="2" class="vmiddle">TOTAL REMUNERACIÓN BRUTA</th>
                        <th rowspan="2" class="vmiddle">TOTAL DESCUENTOS</th>
                        <th rowspan="2" class="vmiddle">REMUNERACIÓN NETA</th>
                        <th colspan="2">APORTACION DEL EMPLEADOR</th>
                    </tr>
                    <tr>
                        <th>SUELDO BÁSICO</th>
                        <th>ASIGNACIÓN FAMILIAR</th>
                        <th>OTROS</th>
                        <th>SALUD</th>
                        <th>TOTAL APORTES</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>