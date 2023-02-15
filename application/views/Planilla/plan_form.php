<div class="modal-dialog" role="document">
    <div class="modal-content">
        <form class="form-horizontal" action="<?= base_url() ?>Planilla/plan_guardar/<?= $plan->plan_id ?>" method="post" id="frm-newClient">
        <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="gridSystemModalLabel"></h4>
            </div>
        <div class="modal-body">
            <div class="alert alert-danger error hidden" role="alert">
                <span class="text">Error:</span>
            </div>
            <div class="row">
                <div class="col-sm-6">
                    <?= form_label('<strong>Empleado</strong>', 'empleado', array('class' => 'control-label')); ?>
                    <?= form_dropdown('empleado', $empleados, $plan->plan_emp_id, array('class' => 'form-control input-sm','disabled' => 'disabled')); ?>
                </div>
                <div class="col-sm-6">
                    <?= form_label('<strong>Periodo</strong>', 'periodo', array('class' => 'control-label')); ?>
                    <?= form_dropdown('periodo', $periodos, $plan->plan_peri_id, array('class' => 'form-control input-sm','disabled' => 'disabled')); ?>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-6">
                    <?= form_label('<strong>Asignación Familiar</strong>', 'asigFamiliar', array('class' => 'control-label')); ?>
                    <?= form_dropdown('asigFamiliar', $asignacion, $plan->plan_asigFami, array('class' => 'form-control input-sm')); ?>
                </div>
                <div class="col-sm-6">
                    <?= form_label('<strong>Sueldo Básico</strong>', 'remuBasico', array('class' => 'control-label')); ?>
                    <?= form_input(array("name" => "remuBasico", "value" => $plan->plan_remuBasico, "class" => "form-control input-sm dinero")); ?>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-6">
                    <?= form_label('<strong>Asignación Familiar</strong>', 'remuAsig', array('class' => 'control-label')); ?>
                    <?= form_input(array("name" => "remuAsig", "value" => $plan->plan_remuAsig, "class" => "form-control input-sm dinero")); ?>
                </div>
                <div class="col-sm-6">
                    <?= form_label('<strong>Otros</strong>', 'otros', array('class' => 'control-label')); ?>
                    <?= form_input(array("name" => "otros", "value" => $plan->plan_otros, "class" => "form-control input-sm dinero","readonly" => "readonly")); ?>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-6 has-success">
                    <?= form_label('<strong>Remuneracion Bruta</strong>', 'remuTotal', array('class' => 'control-label')); ?>
                    <?= form_input(array("name" => "remuTotal", "value" => $plan->plan_remuTotal, "class" => "form-control input-sm dinero","readonly" => "readonly")); ?>
                </div>
                <div class="col-sm-6">
                    <?= form_label('<strong>Total Descuentos</strong>', 'descTotal', array('class' => 'control-label')); ?>
                    <?= form_input(array("name" => "descTotal", "value" => $plan->plan_descTotal, "class" => "form-control input-sm dinero")); ?>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-6 has-success">
                    <?= form_label('<strong>Remuneracion Neta</strong>', 'remuNeto', array('class' => 'control-label')); ?>
                    <?= form_input(array("name" => "remuNeto", "value" => $plan->plan_remuNeto, "class" => "form-control input-sm dinero","readonly" => "readonly")); ?>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-6">
                    <?= form_label('<strong>Total Aportes</strong>', 'totalAporte', array('class' => 'control-label')); ?>
                    <?= form_input(array("name" => "totalAporte", "value" => $plan->plan_totalAporte, "class" => "form-control input-sm dinero")); ?>
                </div>
                <div class="col-sm-6">
                    <?= form_label('<strong>Salud</strong>', 'salud', array('class' => 'control-label')); ?>
                    <?= form_input(array("name" => "salud", "value" => $plan->plan_salud, "class" => "form-control input-sm dinero")); ?>
                </div>
            </div>
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
        $('.dinero').change(function(){
            if(!esNumeroPositivo($(this).val()) || $(this).val() == '') { 
                $(this).val('0.00');
            }else{
                $(this).dval($(this).val());
            }
        })
        $('select[name="asigFamiliar"]').on('change',function(){
            var asig = $('input[name="remuAsig"]');
            if($(this).val() == "NO"){
                asig.attr('readonly','readonly');
                asig.val('0.00');
                actualizar();
            }else{
                asig.removeAttr('readonly');
            }
        })

        $('input[name="remuBasico"], input[name="remuAsig"], input[name="descTotal"]').on('change',actualizar)
    })
    function actualizar() {
        console.log("actualizar todo we");
        var basico = parseFloat($('input[name="remuBasico"]').val());
        var familiar = parseFloat($('input[name="remuAsig"]').val());
        var otros = parseFloat($('input[name="otros"]').val());
        var descuentos = parseFloat($('input[name="descTotal"]').val());
        total = basico + familiar + otros;
        $('input[name="remuTotal"]').dval(total);
        $('input[name="remuNeto"]').dval(total-descuentos);

    }
</script>