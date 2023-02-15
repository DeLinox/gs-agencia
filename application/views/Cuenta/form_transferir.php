<div class="modal-dialog" role="document">
    <div class="modal-content">
        <form class="form-horizontal" action="<?= base_url() ?>Cuenta/guardar_trans/<?= $cuenta->cuen_id ?>" method="post">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title" id="gridSystemModalLabel"><?= $titulo ?></h4>
        </div>
        <input type="hidden" name="valcajaorigen" value="<?= $cuenta->cuen_monto  ?>">
        <input type="hidden" name="valcajadestino" value="<?= '0'  ?>">

        <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <?= form_label('<strong>Cuenta Origen</strong>', 'cuenta_origen', array('class' => 'control-label')); ?>
                        <?= form_input(array("name" => "cuenta_origen", "id" => "cuenta_origen","value" => $cuenta->cuen_banco, "class" => "form-control input-sm", "readonly" => "readonly")); ?>
                    </div>
                    <div class="col-md-6">
                        <?= form_label('<strong>Cuenta Destino</strong>', 'cuenta_destino', array('class' => 'control-label')); ?>
                        <?= form_dropdown('cuenta_destino', $cuentas, '', array('id'=> 'cuenta_destino','class' => 'form-control input-sm')); ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <?= form_label('<strong>Monto</strong>', 'Banco', array('class' => 'control-label')); ?>
                        <?= form_input(array("name" => "montotrans","id" => "montotrans", "value" => '', "class" => "form-control input-sm")); ?>
                    </div>
                </div>
                <h5 style="border-bottom: 2px solid #999;">CUENTA DE ORIGEN</h5>
                <div class="row">
                    <div class="col-md-6">
                        <?= form_label('<strong>Cuenta</strong>', 'cuenta_origen', array('class' => 'control-label')); ?>
                        <?= form_input(array("name" => "ori_cuenta", "value" => $cuenta->cuen_banco, "class" => "form-control input-sm", "readonly" => "readonly")); ?>
                    </div>
                    <div class="col-md-6">
                        <?= form_label('<strong>Moneda</strong>', 'moneda', array('class' => 'control-label')); ?>
                        <?= form_input(array("name" => "ori_moneda","class" => "form-control input-sm", "value" => $cuenta->cuen_moneda, "readonly" => "readonly")); ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <?= form_label('<strong>Total en Cuenta</strong>', '', array('class' => 'control-label')); ?>
                        <?= form_input(array("name" => "ori_monto","value" => $cuenta->cuen_monto, "class" => "form-control input-sm", "readonly" => "readonly")); ?>
                    </div>
                    <div class="col-md-6">
                        <?= form_label('<strong>Monto a Transferir</strong>', '', array('class' => 'control-label')); ?>
                        <?= form_input(array("name" => "ori_trans","value" => 0, "class" => "form-control input-sm", "readonly" => "readonly")); ?>
                    </div>
                </div>
                <h5 style="border-bottom: 2px solid #999;">CUENTA DE DESTINO</h5>
                <div class="row">
                    <div class="col-md-6">
                        <?= form_label('<strong>Cuenta</strong>', 'cuenta_destino', array('class' => 'control-label')); ?>
                        <?= form_input(array("id" => "dest_cuenta", "name" => "dest_cuenta","value" => '', "class" => "form-control input-sm", "readonly" => "readonly")); ?>
                    </div>
                    <div class="col-md-6">
                        <?= form_label('<strong>Moneda</strong>', 'moneda', array('class' => 'control-label')); ?>
                        <?= form_input(array("class" => "form-control input-sm", "id" => "dest_moneda", "name" => "dest_moneda","value" => '', "readonly" => "readonly")); ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <?= form_label('<strong>Total en Cuenta</strong>', '', array('class' => 'control-label')); ?>
                        <?= form_input(array("value" => 0, "id" => "dest_monto", "name" => "dest_monto","class" => "form-control input-sm", "readonly" => "readonly")); ?>
                    </div>
                    <div class="col-md-6">
                        <?= form_label('<strong>Monto a Recibir</strong>', '', array('class' => 'control-label')); ?>
                        <?= form_input(array("value" => 0, "id" => "dest_recibir", "name" => "dest_recibir", "class" => "form-control input-sm", "readonly" => "readonly")); ?>
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
<script type="text/javascript">
    $(document).ready(met_trans());
</script>