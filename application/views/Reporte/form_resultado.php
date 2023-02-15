<div class="modal-dialog" role="document" id="mdl-client">
    <div class="modal-content">
        <form class="form-horizontal" action="<?= base_url() ?>reporte/guardar_resultado" method="post">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title" id="gridSystemModalLabel"></h4>
        </div>
        <div class="modal-body">
                <div class="row">
                    <div class="col-sm-6">
                        <?= form_label('<strong>Mes</strong>', 'mes', array('class' => 'control-label')); ?>
                        <?= form_dropdown('mes', $mes, $resu->mes, array('class' => 'form-control input-sm')); ?>
                    </div>
                    <div class="col-sm-6">
                        <?= form_label('<strong>Año</strong>', 'anio', array('class' => 'control-label')); ?>
                        <?= form_dropdown('anio', $anio, $resu->anio, array('class' => 'form-control input-sm')); ?>
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