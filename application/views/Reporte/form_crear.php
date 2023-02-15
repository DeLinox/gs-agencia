<div class="modal-dialog" role="document">
    <div class="modal-content">
        <form class="form-horizontal" action="<?= base_url() ?>Cliente/guardar/<?= $cliente->clie_id ?>" method="post">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title" id="gridSystemModalLabel">Registrar cliente</h4>
        </div>
        <div class="modal-body">
                <div>
                    <?= form_label('<strong>Razón social</strong>', 'rsocial', array('class' => 'control-label')); ?>
                    <?= form_input(array("name" => "rsocial", "value" => $cliente->clie_rsocial, "class" => "form-control input-sm")); ?>
                </div>
                <div>
                    <?= form_label('<strong>Documento</strong>', 'documento', array('class' => 'control-label')); ?>
                    <?= form_dropdown('documento', $docu_options, $cliente->clie_docu_id, array('class' => 'form-control input-sm')); ?>
                </div>
                <div>
                    <?= form_label('<strong>Numero de documento</strong>', 'docnum', array('class' => 'control-label')); ?>
                    <?= form_input(array("name" => "docnum", "value" => $cliente->clie_docnum, "class" => "form-control input-sm")); ?>
                </div>
                <div>
                    <?= form_label('<strong>Dirección</strong>', 'direccion', array('class' => 'control-label')); ?>
                    <?= form_input(array("name" => "direccion", "value" => $cliente->clie_direccion, "class" => "form-control input-sm")); ?>
                </div>	
                <div>
                    <?= form_label('<strong>Correo</strong>', 'email1', array('class' => 'control-label')); ?>
                    <?= form_input(array("name" => "email", "value" => $cliente->clie_email, "class" => "form-control input-sm")); ?>
                </div>
                
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
            <button type="submit" class="btn btn-primary">Guardar</button>
        </div>
        </form>
    </div>
</div>