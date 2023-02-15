<div class="modal-dialog" role="document">
    <div class="modal-content">
        <form class="form-horizontal" action="<?= base_url() ?>Planilla/emp_guardar/<?= $emp->emp_id ?>" method="post" id="frm-newClient">
        <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="gridSystemModalLabel"></h4>
            </div>
        <div class="modal-body">
            <div class="alert alert-danger error hidden" role="alert">
                <span class="text">Error:</span>
            </div>
			<div class="row">
                <div class="col-sm-12">
                    <?= form_label('<strong>Nombres</strong>', 'nombres', array('class' => 'control-label')); ?>
                    <?= form_input(array("name" => "nombres", "value" => $emp->emp_nombres, "class" => "form-control input-sm")); ?>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-6">
                    <?= form_label('<strong>Apellido paterno</strong>', 'paterno', array('class' => 'control-label')); ?>
                    <?= form_input(array("name" => "paterno", "value" => $emp->emp_paterno, "class" => "form-control input-sm")); ?>
                </div>
                <div class="col-sm-6">
                    <?= form_label('<strong>Apellido materno</strong>', 'materno', array('class' => 'control-label')); ?>
                    <?= form_input(array("name" => "materno", "value" => $emp->emp_materno, "class" => "form-control input-sm")); ?>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-6">
                    <?= form_label('<strong>DNI</strong>', 'dni', array('class' => 'control-label')); ?>
                    <?= form_input(array("name" => "dni", "value" => $emp->emp_dni, "class" => "form-control input-sm")); ?>
                </div>
                <div class="col-sm-6">
                    <?= form_label('<strong>CUSPP</strong>', 'cuspp', array('class' => 'control-label')); ?>
                    <?= form_input(array("name" => "cuspp", "value" => $emp->emp_cuspp, "class" => "form-control input-sm")); ?>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-6">
                    <?= form_label('<strong>Fecha Ingreso</strong>', 'fechaIngreso', array('class' => 'control-label')); ?>
                    <?= form_input(array("name" => "fechaIngreso", "value" => $emp->emp_fechaIngreso, "class" => "form-control input-sm datepicker")); ?>
                </div>
                <div class="col-sm-6">
                    <?= form_label('<strong>Ocupación / Cargo</strong>', 'ocupacion', array('class' => 'control-label')); ?>
                    <?= form_dropdown('ocupacion', $ocupaciones, $emp->emp_ocu_id, array('class' => 'form-control input-sm')); ?>
                </div>
            </div>
            <!--
            <div class="row">
                <div class="col-sm-6">
                    <?= form_label('<strong>Asignación Familiar</strong>', 'asigFamiliar', array('class' => 'control-label')); ?>
                    <?= form_dropdown('asigFamiliar', $asignacion, $emp->emp_asigFami, array('class' => 'form-control input-sm')); ?>
                </div>
                <div class="col-sm-6">
                    <?= form_label('<strong>Sueldo Básico</strong>', 'remuBasico', array('class' => 'control-label')); ?>
                    <?= form_input(array("name" => "remuBasico", "value" => $emp->emp_remuBasico, "class" => "form-control input-sm dinero")); ?>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-6">
                    <?= form_label('<strong>Asignación Familiar</strong>', 'remuAsig', array('class' => 'control-label')); ?>
                    <?= form_input(array("name" => "remuAsig", "value" => $emp->emp_remuAsig, "class" => "form-control input-sm dinero")); ?>
                </div>
                <div class="col-sm-6">
                    <?= form_label('<strong>Otros</strong>', 'otros', array('class' => 'control-label')); ?>
                    <?= form_input(array("name" => "otros", "value" => $emp->emp_otros, "class" => "form-control input-sm dinero")); ?>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-6">
                    <?= form_label('<strong>Remuneracion Bruta</strong>', 'remuTotal', array('class' => 'control-label')); ?>
                    <?= form_input(array("name" => "remuTotal", "value" => $emp->emp_remuTotal, "class" => "form-control input-sm dinero")); ?>
                </div>
                <div class="col-sm-6">
                    <?= form_label('<strong>Total Descuentos</strong>', 'descTotal', array('class' => 'control-label')); ?>
                    <?= form_input(array("name" => "descTotal", "value" => $emp->emp_descTotal, "class" => "form-control input-sm dinero")); ?>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-6">
                    <?= form_label('<strong>Remuneracion Neta</strong>', 'remuNeto', array('class' => 'control-label')); ?>
                    <?= form_input(array("name" => "remuNeto", "value" => $emp->emp_remuNeto, "class" => "form-control input-sm dinero")); ?>
                </div>
                <div class="col-sm-6">
                    <?= form_label('<strong>Salud</strong>', 'salud', array('class' => 'control-label')); ?>
                    <?= form_input(array("name" => "salud", "value" => $emp->emp_salud, "class" => "form-control input-sm dinero")); ?>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-6">
                    <?= form_label('<strong>Total Aportes</strong>', 'totalAporte', array('class' => 'control-label')); ?>
                    <?= form_input(array("name" => "totalAporte", "value" => $emp->emp_totalAporte, "class" => "form-control input-sm dinero")); ?>
                </div>
            </div>
            -->
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
            <button type="submit" class="btn btn-primary">Guardar</button>
        </div>
        </form>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function(){
        $(".datepicker").daterangepicker({
            singleDatePicker: true,
            locale: {
                format: 'DD/MM/YYYY'
            }
        }); 
    })
</script>