<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/font-awesome/4.2.0/css/font-awesome.min.css">
<div class="modal-dialog" role="document">
    <div class="modal-content">
        <form class="form-horizontal" action="<?= base_url() ?>Contacto/guardar_provEmp/<?= $empresa->emp_id ?>" method="post" id="frm-newClient">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title" id="gridSystemModalLabel"></h4>
        </div>
        <div class="modal-body">
                <div>
                    <?= form_label('<strong>Razón social</strong>', 'rsocial', array('class' => 'control-label')); ?>
                    <?= form_input(array("name" => "rsocial", "value" => $empresa->emp_rsocial, "class" => "form-control input-sm")); ?>
                </div>
                <div class="row">
                    <div class="col-sm-6">
                        <?= form_label('<strong>Telefono</strong>', 'telefono', array('class' => 'control-label')); ?>
                        <?= form_input(array("name" => "telefono", "value" => $empresa->emp_telefono, "class" => "form-control input-sm")); ?>
                    </div>
                    <div class="col-sm-6">
                        <?= form_label('<strong>Dirección</strong>', 'direccion', array('class' => 'control-label')); ?>
                        <?= form_input(array("name" => "direccion", "value" => $empresa->emp_direccion, "class" => "form-control input-sm")); ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-6">
                        <?= form_label('<strong>Documento</strong>', 'documento', array('class' => 'control-label')); ?>
                        <?= form_dropdown('documento', $docu_options, $empresa->emp_tdoc_id, array('class' => 'form-control input-sm')); ?>
                    </div>
                    <div class="col-sm-6">
                        <?= form_label('<strong>Numero de documento</strong>', 'docnum', array('class' => 'control-label')); ?>
                        <?= form_input(array("name" => "docnum", "value" => $empresa->emp_tdoc_nro, "class" => "form-control input-sm")); ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-6">
                        <?= form_label('<strong>Tipo</strong>', 'tipo_prov', array('class' => 'control-label')); ?>
                        <?= form_dropdown('tipo_prov', $tipo_serv, $empresa->emp_tipo, array('class' => 'form-control input-sm')); ?>
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