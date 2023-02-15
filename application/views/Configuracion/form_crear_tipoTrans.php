<div class="modal-dialog" role="document">
    <div class="modal-content">
        <form class="form-horizontal" action="<?= base_url() ?>Configuracion/guardar_tipoTrans/<?= $tipo->id ?>" method="post" id="frm-newClient">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title" id="gridSystemModalLabel"></h4>
        </div>
        <div class="modal-body">
            <div class="alert alert-danger error hidden" role="alert">
                <span class="text">Error:</span>
            </div>
            <div>
                <?= form_label('<strong>Tipo transporte</strong>', 'tipo_nombre', array('class' => 'control-label')); ?>
                <?= form_input(array("name" => "tipo_nombre", "value" => $tipo->denom, "class" => "form-control input-sm")); ?>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
            <button type="submit" class="btn btn-primary">Guardar</button>
        </div>
        </form>
    </div>
</div>