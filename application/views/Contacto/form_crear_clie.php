<div class="modal-dialog" role="document">
    <div class="modal-content">
        <form class="form-horizontal" action="<?= base_url() ?>Contacto/guardar_clie/<?= $cliente->clie_id ?>" method="post" id="frm-newClient">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title" id="gridSystemModalLabel"></h4>
        </div>
        <div class="modal-body">
            <div class="alert alert-danger error hidden" role="alert">
                <span class="text">Error:</span>
            </div>
			<div class="row">
                <div class="col-sm-12">
                    <?= form_label('<strong>Razón social</strong>', 'rsocial', array('class' => 'control-label')); ?>
                    <?= form_input(array("name" => "rsocial", "value" => $cliente->clie_rsocial, "class" => "form-control input-sm")); ?>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-6">
                    <?= form_label('<strong>Nombre / Razón comercial</strong>', 'rcomercial', array('class' => 'control-label')); ?>
                    <?= form_input(array("name" => "rcomercial", "value" => $cliente->clie_rcomercial, "class" => "form-control input-sm")); ?>
                </div>
                <div class="col-sm-6">
                    <?= form_label('<strong>Gerente / Dueño</strong>', 'gerente', array('class' => 'control-label')); ?>
                    <?= form_input(array("name" => "gerente", "value" => $cliente->clie_gerente, "class" => "form-control input-sm")); ?>
                </div>
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
                    <?= form_label('<strong>Teléfono</strong>', 'telefono', array('class' => 'control-label')); ?>
                    <?= form_input(array("name" => "telefono", "value" => $cliente->clie_telefono, "class" => "form-control input-sm")); ?>
                </div>
                <div class="col-sm-6">
                    <div class="row">
                        <div class="col-sm-6">        
                            <?= form_label('<strong>Facturación</strong>', 'facturacion', array('class' => 'control-label')); ?>
                            <?= form_dropdown('facturacion', $facturacion, $cliente->clie_facturacion, array('class' => 'form-control input-sm')); ?>
                        </div>
                        <div class="col-sm-6">        
                            <?= form_label('<strong>Liquidación</strong>', 'liquidacion', array('class' => 'control-label')); ?>
                            <?= form_dropdown('liquidacion', $liquidacion, $cliente->clie_liquidacion, array('class' => 'form-control input-sm')); ?>
                        </div>
                    </div>
                    
                </div>
            </div>
            <div class="row">
                <div class="col-sm-6">
                    <?= form_label('<strong>TIpo</strong>', 'tipo', array('class' => 'control-label')); ?>
                    <?= form_dropdown('tipo', $tipo, $cliente->clie_tipo, array('class' => 'form-control input-sm')); ?>
                </div>
                <div class="col-sm-6">
                    <?= form_label('<strong>Reporte</strong>', 'reporte', array('class' => 'control-label')); ?>
					<?= form_dropdown('reporte', $reporte, $cliente->clie_reporte, array('class' => 'form-control input-sm')); ?>
                    <?= form_input(array("name" => "abrev", "value" => $cliente->clie_abrev, "class" => "form-control input-sm hidden")); ?>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-6">
                    <?= form_label('<strong>Correo</strong>', 'email1', array('class' => 'control-label')); ?>
                    <?= form_input(array("name" => "email", "value" => $cliente->clie_email, "class" => "form-control input-sm")); ?>
                </div>
                <div class="col-sm-6">
                    <?= form_label('<strong>Activo</strong>', 'estado', array('class' => 'control-label')); ?>
                    <?= form_dropdown('estado', array("1" => "SI", "0" => "NO"), $cliente->clie_activo, array('class' => 'form-control input-sm')); ?>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-6">
                    <?= form_label('<strong>Dirección</strong>', 'direccion', array('class' => 'control-label')); ?>
                    <?= form_input(array("name" => "direccion", "value" => $cliente->clie_direccion, "class" => "form-control input-sm")); ?>
                </div>	
				<div class="col-sm-6">
					<?= form_label('<strong>Reserva Tipo</strong>', 'reserv_tipo', array('class' => 'control-label')); ?>
					<?= form_dropdown('reserv_tipo', $reserv_tipo, $cliente->clie_reserv_tipo, array('class' => 'form-control input-sm')); ?>
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