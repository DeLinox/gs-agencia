<div class="modal-dialog" role="document">
    <div class="modal-content">
        <form class="form-horizontal" action="<?= base_url() ?>cuenta/guardar/<?= $cuenta->cuen_id ?>" method="post">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title" id="gridSystemModalLabel">Registrar Producto</h4>
        </div>
        <div class="modal-body">
                <div class="alert alert-danger mensaje oculto"></div>
                <div>
                    <?= form_label('<strong>Banco</strong>', 'banco', array('class' => 'control-label')); ?>
                    <?= form_input(array("name" => "banco", "value" => $cuenta->cuen_banco, "class" => "form-control input-sm")); ?>
                </div>	
                <div>
                    <?= form_label('<strong>Titular</strong>', 'titular', array('class' => 'control-label')); ?>
                    <?= form_input(array("name" => "titular", "value" => $cuenta->cuen_titular, "class" => "form-control input-sm")); ?>
                </div>  
                <div class="row">
                    <div class="col-sm-6">
                        <?= form_label('<strong>Número</strong>', 'numero', array('class' => 'control-label')); ?>
                        <?= form_input(array("name" => "numero", "value" => $cuenta->cuen_numero, "class" => "form-control input-sm")); ?>    
                    </div>
                    <div class="col-sm-6">
                        <?= form_label('<strong>CCI</strong>', 'cci', array('class' => 'control-label')); ?>
                        <?= form_input(array("name" => "cci", "value" => $cuenta->cuen_cci, "class" => "form-control input-sm")); ?>    
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-6">
                        <?= form_label('<strong>Código</strong>', 'moneda', array('class' => 'control-label')); ?>
                        <?= form_input(array("name" => "codigo", "value" => $cuenta->cuen_codigo, "class" => "form-control input-sm")); ?>
                    </div>    
                    <div class="col-sm-6">
                        <?= form_label('<strong>Moneda</strong>', 'moneda', array('class' => 'control-label')); ?>
                        <?= form_dropdown('moneda', array('1'=>'SOLES','2'=>'DOLARES'), $cuenta->cuen_moneda, array('class' => 'form-control input-sm')); ?>
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