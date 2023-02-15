<div class="modal-dialog" role="document">
    <div class="modal-content">
        <form class="form-horizontal" action="<?= base_url() ?>clientes/mostrar" method="post">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title" id="gridSystemModalLabel">Buscar cliente</h4>
        </div>
        <div class="modal-body">
                <input id="sclie_id" name="sclie_id" type="hidden" value="<?= $cliente->clie_id ?>">
                <div>
                    <label for="serie">Cliente</label>
                    <select id="cliente2" name="cliente2" class="form-control" rel='<?php echo $venta->clie_selected_data; ?>'>
                    </select>
                </div>
                <div>
                    <?= form_label('<strong>Razón social</strong>', 'srsocial', array('class' => 'control-label')); ?>
                    <?= form_input(array("name" => "srsocial", "id" => "srsocial", "value" => $cliente->clie_rsocial, "class" => "form-control input-sm")); ?>
                </div>
                <div>
                    <?= form_label('<strong>Documento</strong>', 'sdocumento', array('class' => 'control-label')); ?>
                    <?= form_dropdown('sdocumento', $docu_options, $cliente->clie_docu_id, array('class' => 'form-control input-sm')); ?>
                </div>
                <div>
                    <?= form_label('<strong>Numero de documento</strong>', 'sdocnum', array('class' => 'control-label')); ?>
                    <?= form_input(array("name" => "sdocnum", "id" => "sdocnum", "value" => $cliente->clie_docnum, "class" => "form-control input-sm")); ?>
                </div>
                <div>
                    <?= form_label('<strong>Dirección</strong>', 'sdireccion', array('class' => 'control-label')); ?>
                    <?= form_input(array("name" => "sdireccion", "id" => "sdireccion", "value" => $cliente->clie_direccion, "class" => "form-control input-sm")); ?>
                </div>  
                <div>
                    <?= form_label('<strong>Correo</strong>', 'semail1', array('class' => 'control-label')); ?>
                    <?= form_input(array("name" => "semail", "id" => "semail1", "value" => "", "class" => "form-control input-sm")); ?>
                </div>
                <div>
                    <?= form_input(array("type" => "hidden", "name" => "clie_id", "id" => "clie_id", "value" => $cliente->clie_id, "class" => "form-control input-sm")); ?>
                </div>
                
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
            <button type="submit" class="btn btn-primary">Aceptar</button>
        </div>
        </form>
    </div>
</div>