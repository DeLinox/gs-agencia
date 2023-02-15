<div class="modal-dialog" role="document" id="mdl-client">
    <div class="modal-content">
        <form class="form-horizontal" action="<?= base_url() ?>reporte/guardar_resultadoGen/<?= $resu->resu_id ?>" method="post">
        <div class="modal-header"> 
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title" id="gridSystemModalLabel"></h4>
        </div>
        
        <div class="modal-body">
                <div class="row">
                    <div class="col-sm-6">
                        <?= form_label('<strong>Ventas Netas</strong>', 'ventas_netas', array('class' => 'control-label')); ?>
                        <input type="text" class="form-control input-sm" name="ventas_netas" value="<?= $resu->resu_ventas_netas ?>" disabled>
                    </div>
                    <div class="col-sm-6">
                        <?= form_label('<strong>Otros Ingresos</strong>', 'otros_ingresos', array('class' => 'control-label')); ?>
                        <input type="text" class="form-control input-sm" name="otros_ingresos" value="<?= $resu->resu_otros_ingresos ?>">
                    </div>
                </div>
				<div class="row">
                    <div class="col-sm-6">
                        <?= form_label('<strong>Planillas</strong>', 'planillas', array('class' => 'control-label')); ?>
                        <input type="text" class="form-control input-sm" name="planillas" value="<?= $resu->resu_costo_ventas ?>">
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-6">
                        <?= form_label('<strong>Costos fijos</strong>', 'costo_ventas', array('class' => 'control-label')); ?>
                        <input type="text" class="form-control input-sm" name="costo_ventas" value="<?= $resu->resu_costo_ventas ?>">
                    </div>
                    <div class="col-sm-6">
                        <?= form_label('<strong>Gastos Operacionales</strong>', 'gastos_operacionales', array('class' => 'control-label')); ?>
                        <input type="text" class="form-control input-sm" name="gastos_operacionales" value="<?= $resu->resu_gastos_operacionales ?>" disabled>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-6">
                        <?= form_label('<strong>Gastos Administracion</strong>', 'gastos_administracion', array('class' => 'control-label')); ?>
                        <input type="text" class="form-control input-sm" name="gastos_administracion" value="<?= $resu->resu_gastos_administracion ?>">
                    </div>
                    <div class="col-sm-6">
                        <?= form_label('<strong>Gastos Venta</strong>', 'gastos_venta', array('class' => 'control-label')); ?>
                        <input type="text" class="form-control input-sm" name="gastos_venta" value="<?= $resu->resu_gastos_venta ?>">
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-6">
                        <?= form_label('<strong>Ingresos Financieros</strong>', 'ingresos_financieros', array('class' => 'control-label')); ?>
                        <input type="text" class="form-control input-sm" name="ingresos_financieros" value="<?= $resu->resu_ingresos_financieros ?>">
                    </div>
                    <div class="col-sm-6">
                        <?= form_label('<strong>Gastos Financieros</strong>', 'gastos_financieros', array('class' => 'control-label')); ?>
                        <input type="text" class="form-control input-sm" name="gastos_financieros" value="<?= $resu->resu_gastos_financieros ?>">
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-6">
                        <?= form_label('<strong>Otros Ingresos</strong>', 'otros_ingresosf', array('class' => 'control-label')); ?>
                        <input type="text" class="form-control input-sm" name="otros_ingresosf" value="<?= $resu->resu_otros_ingresosf ?>">
                    </div>
                    <div class="col-sm-6">
                        <?= form_label('<strong>Otros Gastos</strong>', 'otros_gastos', array('class' => 'control-label')); ?>
                        <input type="text" class="form-control input-sm" name="otros_gastos" value="<?= $resu->resu_otros_gastos ?>">
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-6">
                        <?= form_label('<strong>Inflacion</strong>', 'inflacion', array('class' => 'control-label')); ?>
                        <input type="text" class="form-control input-sm" name="inflacion" value="<?= $resu->resu_inflacion ?>">
                    </div>
                    <div class="col-sm-6">
                        <?= form_label('<strong>Moneda</strong>', 'moneda', array('class' => 'control-label')); ?>
                        <input type="text" class="form-control input-sm" name="moneda" value="<?= $resu->resu_moneda ?>"  disabled>
                    </div>
                </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
            <button type="submit" class="btn btn-primary">Aceptar</button>
        </div>
        </form>
    </div>
</div>
<script>
    $(document).ready(function(){
        $('input').on('change', function(){
            if(!esNumeroPositivo($(this).val())) { 
                $(this).dval(0);
            }else{
                $(this).dval($(this).val());
            }
        })
    })
</script>