<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/font-awesome/4.2.0/css/font-awesome.min.css">
<div class="modal-dialog" role="document">
    <div class="modal-content">
        <form class="form-horizontal" action="<?= base_url() ?>Contacto/guardar_prov/<?= $proveedor->prov_id ?>" method="post" id="frm-newClient">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title" id="gridSystemModalLabel"></h4>
        </div>
        <div class="modal-body">
                <div>
                    <?= form_label('<strong>Proveedor</strong>', 'empresa', array('class' => 'control-label')); ?>
                    <?= form_dropdown('empresa', $empresas, $proveedor->prov_emp_id, array('class' => 'form-control input-sm')); ?>
                </div>
                <div>
                    <?= form_label('<strong>Nombre</strong>', 'rsocial', array('class' => 'control-label')); ?>
                    <?= form_input(array("name" => "rsocial", "value" => $proveedor->prov_rsocial, "class" => "form-control input-sm")); ?>
                </div>
                <div class="row">
                    <div class="col-sm-6">
                        <?= form_label('<strong>Correo</strong>', 'email', array('class' => 'control-label')); ?>
                        <?= form_input(array("name" => "email", "value" => $proveedor->prov_email, "class" => "form-control input-sm")); ?>
                    </div>
                    <div class="col-sm-6">
                        <?= form_label('<strong>Telefono</strong>', 'telefono', array('class' => 'control-label')); ?>
                        <?= form_input(array("name" => "telefono", "value" => $proveedor->prov_telefono, "class" => "form-control input-sm")); ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-6">
                        <?= form_label('<strong>Activo</strong>', 'estado', array('class' => 'control-label')); ?>
                        <?= form_dropdown('estado', array("1" => "SI", "0" => "NO"), $proveedor->prov_activo, array('class' => 'form-control input-sm')); ?>
                    </div>
                    <div class="col-sm-6">
                        <?= form_label('<strong>Combustible</strong>', 'combustible', array('class' => 'control-label')); ?>
                        <?= form_dropdown('combustible', array("SI" => "SI", "NO" => "NO"), $proveedor->prov_combustible, array('class' => 'form-control input-sm')); ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12">
                        <?php foreach ($tipos as $key => $val): 
                            if (in_array($val->tipo_id, $proveedor->servicios)) $checked = "checked";
                            else $checked = "";
                        ?>
                            <div class="checkbox checkbox-danger checkbox-inline">
                                <input <?= $checked ?> name="tipo[]" id="<?= $val->tipo_id.$val->tipo_denom ?>" type="checkbox" value="<?= $val->tipo_id ?>">
                                <label for="<?= $val->tipo_id.$val->tipo_denom ?>">
                                    <?= $val->tipo_denom ?>
                                </label>
                            </div>
                        <?php endforeach ?>
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