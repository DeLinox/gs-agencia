<div class="modal-dialog" role="document">
    <div class="modal-content">
        <form class="form-horizontal" action="<?= base_url() ?>producto/guardar_cat/<?= $categoria->cat_id ?>" method="post">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title" id="gridSystemModalLabel">Registrar Categoría</h4>
        </div>
        <div class="modal-body">
                <div>
                    <?= form_label('<strong>Nombre categoría</strong>', 'nombre', array('class' => 'control-label')); ?>
                    <?= form_input(array("name" => "nombre", "value" => $categoria->cat_nombre, "class" => "form-control input-sm")); ?>
                </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
            <button type="submit" class="btn btn-primary">Guardar</button>
        </div>
        </form>
        
    </div>
</div>