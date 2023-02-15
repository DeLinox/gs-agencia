<div class="modal-dialog" role="document">
    <div class="modal-content">
        <form class="form-horizontal" action="<?= base_url() ?>Contacto/guardar_hotel/<?= $hotel->hote_id ?>" method="post" id="frm-newClient">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title" id="gridSystemModalLabel"></h4>
        </div>
        <div class="modal-body">
                <div>
                    <?= form_label('<strong>Nombre</strong>', 'nombre', array('class' => 'control-label')); ?>
                    <?= form_input(array("name" => "nombre", "value" => $hotel->hote_nombre, "class" => "form-control input-sm")); ?>
                </div>
                <div class="row">
                    <div class="col-sm-6">
                        <?= form_label('<strong>Contacto</strong>', 'contacto', array('class' => 'control-label')); ?>
                        <?= form_input(array("name" => "contacto", "value" => $hotel->hote_contacto, "class" => "form-control input-sm")); ?>
                    </div>
                    <div class="col-sm-6">
                        <?= form_label('<strong>Email</strong>', 'email', array('class' => 'control-label')); ?>
                        <?= form_input(array("name" => "email", "value" => $hotel->hote_email, "class" => "form-control input-sm")); ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-6">
                        <?= form_label('<strong>Telefono</strong>', 'telefono', array('class' => 'control-label')); ?>
                        <?= form_input(array("name" => "telefono", "value" => $hotel->hote_telefono, "class" => "form-control input-sm")); ?>
                    </div>
                    <div class="col-sm-6">
                        <?= form_label('<strong>Dirección</strong>', 'direccion', array('class' => 'control-label')); ?>
                    <?= form_input(array("name" => "direccion", "value" => $hotel->hote_direccion, "class" => "form-control input-sm")); ?>
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