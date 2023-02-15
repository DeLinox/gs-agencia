<div class="modal-dialog" role="document">
    <div class="modal-content">
        <form class="form-horizontal" action="<?= base_url() ?>Contacto/guardar_guia/<?= $guia->guia_id ?>" method="post" id="frm-newClient">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title" id="gridSystemModalLabel"></h4>
        </div>
        <div class="modal-body">
                <div>
                    <?= form_label('<strong>Nombres</strong>', 'nombres', array('class' => 'control-label')); ?>
                    <?= form_input(array("name" => "nombres", "value" => $guia->guia_nombres, "class" => "form-control input-sm")); ?>
                </div>
                <div class="row">
                    <div class="col-sm-6">
                        <?= form_label('<strong>Documento</strong>', 'documento', array('class' => 'control-label')); ?>
                        <?= form_dropdown('documento', $docu_options, $guia->guia_tdoc_id, array('class' => 'form-control input-sm')); ?>
                    </div>
                    <div class="col-sm-6">
                        <?= form_label('<strong>Numero de documento</strong>', 'docnum', array('class' => 'control-label')); ?>
                        <?= form_input(array("name" => "docnum", "value" => $guia->guia_doc_nro, "class" => "form-control input-sm")); ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-6">
                        <?= form_label('<strong>Telefono</strong>', 'telefono', array('class' => 'control-label')); ?>
                        <?= form_input(array("name" => "telefono", "value" => $guia->guia_telefono, "class" => "form-control input-sm")); ?>
                    </div>
                    <div class="col-sm-6">
                        <?= form_label('<strong>Correo</strong>', 'email', array('class' => 'control-label')); ?>
                        <?= form_input(array("name" => "email", "value" => $guia->guia_email, "class" => "form-control input-sm")); ?>
                    </div>
                </div>
                <div>
                    <?= form_label('<strong>Dirección</strong>', 'direccion', array('class' => 'control-label')); ?>
                    <?= form_input(array("name" => "direccion", "value" => $guia->guia_direccion, "class" => "form-control input-sm")); ?>
                </div>	
                
                
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
            <button type="submit" class="btn btn-primary">Guardar</button>
        </div>
        </form>
    </div>
</div>