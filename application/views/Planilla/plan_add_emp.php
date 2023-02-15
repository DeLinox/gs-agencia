<div class="modal-dialog" role="document">
    <div class="modal-content">
        <form class="form-horizontal" action="<?= base_url() ?>Planilla/plan_guardarEmpPeri" method="post" id="frm-newClient">
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
                    <?= form_dropdown('empleado', $empleados, "", array('class' => 'form-control input-sm')); ?>
                </div>
                <div class="col-sm-6">
                    <?= form_label('<strong>Periodo</strong>', 'periodo', array('class' => 'control-label')); ?>
                    <?= form_dropdown('periodo', $periodos, "", array('class' => 'form-control input-sm')); ?>
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
    })
</script>