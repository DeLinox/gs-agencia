<div class="modal-dialog" role="document">
    <div class="modal-content">
        <form class="form-horizontal" action="<?= base_url() ?>producto/guardar/<?= $producto->prod_id ?>" method="post">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title" id="gridSystemModalLabel">Registrar Producto</h4>
        </div>
        <div class="modal-body">
                <div>
                    <?= form_label('<strong>Nombre producto</strong>', 'nombre', array('class' => 'control-label')); ?>
                    <?= form_input(array("name" => "nombre", "value" => $producto->prod_nombre, "class" => "form-control input-sm")); ?>
                </div>	
                <div>
                    <?= form_label('<strong>Categoria</strong>', 'categoria', array('class' => 'control-label')); ?>
                    <?= form_dropdown('categoria', $producto->cate_options, $producto->prod_cate_id, array('class' => 'form-control input-sm')); ?>
                </div>
                <div>
                    <?= form_label('<strong>Tipo</strong>', 'tipo', array('class' => 'control-label')); ?>
                    <?= form_dropdown('tipo', array('1'=>'PRODUCTO','2'=>'SERVICIO'), $producto->prod_tipo, array('class' => 'form-control input-sm')); ?>
                </div>
                <div>
                    <?= form_label('<strong>Código</strong>', 'codigo', array('class' => 'control-label')); ?>
                    <?= form_input(array("name" => "codigo", "value" => $producto->prod_codigo, "class" => "form-control input-sm")); ?>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <?= form_label('<strong>Valor</strong>', 'valor', array('class' => 'control-label')); ?>
                        <?= form_input(array("name" => "valor", "id" => "prod-valor","onchange"=>"changeValor(this.value)", "value" => $producto->prod_valor, "class" => "form-control input-sm")); ?>
                    </div>
                    <div class="col-md-6">
                        <?= form_label('<strong>Precio</strong>', 'precio', array('class' => 'control-label')); ?>
                        <?= form_input(array("name" => "precio", "id" => "prod-precio","onchange"=>"changePrecio(this.value)", "value" => $producto->prod_precio, "class" => "form-control input-sm")); ?>
                    </div>
                </div>
                <div>
                    <?= form_label('<strong>Tipo igv</strong>', 'igvtipo', array('class' => 'control-label')); ?>
                    <?= form_dropdown('igvtipo', array('1'=>'GRAVADA','2'=>'EXONERADA','3'=>'INAFECTA'), $producto->prod_igvtipo, array('class' => 'form-control input-sm')); ?>
                </div>
                <div>
                    <?= form_label('<strong>Tipo unidad</strong>', 'unid_id', array('class' => 'control-label')); ?>
                    <?= form_dropdown('unid_id', $producto->unidad_options, $producto->prod_unidad, array('class' => 'form-control input-sm')); ?>
                </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
            <button type="submit" class="btn btn-primary">Guardar</button>
        </div>
        </form>
        
    </div>
</div>