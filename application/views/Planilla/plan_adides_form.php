<div class="modal-dialog" role="document">
    <div class="modal-content">
        <form class="form-horizontal" action="<?= base_url() ?>Planilla/plan_guardar_adides/<?= $desa->desa_id ?>" method="post" id="frm-newClient">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title" id="gridSystemModalLabel"></h4>
        </div>
        <input type="hidden" name="plan_id" value="<?= $desa->desa_plan_id ?>">
        <div class="modal-body">
            <div class="alert alert-danger error hidden" role="alert">
                <span class="text">Error:</span>
            </div>
            <div class="row">
                <div class="col-sm-6">
                    <?= form_label('<strong>Fecha</strong>', 'fecha', array('class' => 'control-label')); ?>
                    <?= form_input(array("name" => "fecha", "value" => $desa->desa_fecha, "class" => "form-control input-sm datepicker")); ?>
                </div>
                <div class="col-sm-6">
                    <?= form_label('<strong>Gastos de la empresa</strong>', 'gastEmp', array('class' => 'control-label')); ?>
                    <?= form_input(array("name" => "gastEmp", "value" => $desa->desa_gastEmpresa, "class" => "form-control input-sm dinero")); ?>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-6">
                    <?= form_label('<strong>Monto</strong>', 'monto', array('class' => 'control-label')); ?>
                    <?= form_input(array("name" => "monto", "value" => $desa->desa_monto, "class" => "form-control input-sm dinero")); ?>
                </div>
                <div class="col-sm-6">
                    <?= form_label('<strong>Tipo</strong>', 'tipo', array('class' => 'control-label')); ?>
                    <?= form_dropdown('tipo', $tipo, $desa->desa_tipo, array('class' => 'form-control input-sm')); ?>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-6">
                    <?= form_label('<strong>Otros</strong>', 'otros', array('class' => 'control-label')); ?>
                    <?= form_input(array("name" => "otros", "value" => $desa->desa_otros, "class" => "form-control input-sm dinero")); ?>
                </div>
                <div class="col-sm-6">
                    <?= form_label('<strong>Concepto</strong>', 'concepto', array('class' => 'control-label')); ?>
                    <textarea class="form-control input-sm" name="concepto"><?= $desa->desa_concepto ?></textarea>
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
    $('.dinero').change(function(){
        if(!esNumeroPositivo($(this).val()) || $(this).val() == '') { 
            $(this).val('0.00');
        }else{
            $(this).dval($(this).val());
        }
    })
</script>