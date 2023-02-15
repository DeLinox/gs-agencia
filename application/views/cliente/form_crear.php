<div class="modal-dialog" role="document">
    <div class="modal-content">
        <form class="form-horizontal" action="<?= base_url() ?>contacto/guardar_clie/<?= $cliente->clie_id ?>" method="post" id="frm-newClient">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title" id="gridSystemModalLabel"></h4>
        </div>
        <div class="modal-body">
                <div>
                    <?= form_label('<strong>Razón social</strong>', 'rsocial', array('class' => 'control-label')); ?>
                    <?= form_input(array("name" => "rsocial", "value" => $cliente->clie_rsocial, "class" => "form-control input-sm")); ?>
                </div>
                <div class="row">
                    <div class="col-sm-6">
                        <?= form_label('<strong>Documento</strong>', 'documento', array('class' => 'control-label')); ?>
                        <?= form_dropdown('documento', $docu_options, $cliente->clie_tdoc_id, array('class' => 'form-control input-sm')); ?>
                    </div>
                    <div class="col-sm-6">
                        <?= form_label('<strong>Numero de documento</strong>', 'docnum', array('class' => 'control-label')); ?>
                        <?= form_input(array("name" => "docnum", "value" => $cliente->clie_doc_nro, "class" => "form-control input-sm")); ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-6">
                        <?= form_label('<strong>TIpo</strong>', 'tipo', array('class' => 'control-label')); ?>
                        <?= form_dropdown('tipo', $tipo, $cliente->clie_tipo, array('class' => 'form-control input-sm')); ?>
                    </div>
                    <div class="col-sm-6">
                        <?= form_label('<strong>Correo</strong>', 'email1', array('class' => 'control-label')); ?>
                        <?= form_input(array("name" => "email", "value" => $cliente->clie_email, "class" => "form-control input-sm")); ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-6">
                        <?= form_label('<strong>Abreviatura</strong>', 'abrev', array('class' => 'control-label')); ?>
                        <?= form_input(array("name" => "abrev", "value" => $cliente->clie_abrev, "class" => "form-control input-sm")); ?>
                    </div>
                    <div class="col-sm-6">
                        <?= form_label('<strong>Activo</strong>', 'estado', array('class' => 'control-label')); ?>
                        <?= form_dropdown('estado', array("1" => "SI", "0" => "NO"), $cliente->clie_activo, array('class' => 'form-control input-sm')); ?>
                    </div>
                </div>
                <div>
                    <?= form_label('<strong>Dirección</strong>', 'direccion', array('class' => 'control-label')); ?>
                    <?= form_input(array("name" => "direccion", "value" => $cliente->clie_direccion, "class" => "form-control input-sm")); ?>
                </div>	
                
                
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
            <button type="submit" class="btn btn-primary">Guardar</button>
        </div>
        </form>
    </div>
</div>