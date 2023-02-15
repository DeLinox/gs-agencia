<div class="modal-dialog" role="document">
    <div class="modal-content">
        <form class="form-horizontal" action="<?= base_url() ?>Cuenta/guardar_ajust/<?= $cuenta->cuen_id ?>" method="post">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title" id="gridSystemModalLabel"><?= $titulo ?></h4>
        </div>
        <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <?= form_label('<strong>Cuenta</strong>', 'cuenta', array('class' => 'control-label')); ?>
                        <?= form_input(array("name" => "cuenta", "id" => "cuenta","value" => $cuenta->cuen_banco, "class" => "form-control input-sm", "readonly" => "readonly")); ?>
                    </div>
                    <div class="col-md-6">
                        <?= form_label('<strong>Moneda</strong>', 'moneda', array('class' => 'control-label')); ?>
                        <?= form_input(array("name" => "moneda", "id" => "moneda", "value" => $cuenta->cuen_moneda, "class" => "form-control input-sm", "readonly" => "readonly")); ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <?= form_label('<strong>Caja (Sistema)</strong>', 'caja_sistema', array('class' => 'control-label')); ?>
                        <?= form_input(array("name" => "caja_sistema", "id" => "caja_sistema","value" => $cuenta->cuen_monto, "class" => "form-control input-sm", "readonly" => "readonly")); ?>
                    </div>
                    <div class="col-md-6">
                        <?= form_label('<strong>Caja (Real)</strong>', 'caja_real', array('class' => 'control-label')); ?>
                        <?= form_input(array("name" => "caja_real", "id" => "caja_real", "class" => "form-control input-sm", "onkeyup"=>"calcular()")); ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <?= form_label('<strong>Retirar (sis)</strong>', 'caja_retirar', array('class' => 'control-label')); ?>
                        <?= form_input(array("name" => "caja_retirar", "id" => "caja_retirar","value" => 0, "class" => "form-control input-sm", "readonly" => "readonly")); ?>
                    </div>
                    <div class="col-md-6">
                        <?= form_label('<strong>Agregar (sis)</strong>', 'caja_agregar', array('class' => 'control-label')); ?>
                        <?= form_input(array("name" => "caja_agregar", "id" => "caja_agregar", "value" => 0, "class" => "form-control input-sm", "readonly" => "readonly")); ?>
                    </div>
                </div>
                <div>
                    <?= form_label('<strong>Descripción</strong>', 'Producto', array('class' => 'control-label')); ?>
                    <?= form_textarea(array("name" => "descripcion", "value" => '', "class" => "form-control input-sm",'rows'=> '3',)); ?>
                </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
            <button type="submit" class="btn btn-primary">Guardar</button>
        </div>
        </form>
        
    </div>
</div>