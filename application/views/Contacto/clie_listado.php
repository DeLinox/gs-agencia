<div id="page-wrapper" class="page-filter">
    <div class="page-header col-md-12">
        <div class="row page-header-title">
            <input id="tipo_usuario" value="<?= $this->session->userdata('authorizedadmin') ?>" type="hidden">
            <div class="col-sm-6">
                <h3>Clientes </h3>
            </div>
            <?php if($this->editar > 1): ?>
            <div class="col-sm-6 text-right" role="group" aria-label="...">
                <a href="<?php echo base_url() ?>Contacto/crear_clie" title="Crear" class="crear btn btn-danger btn-sm pull-right">
                    <i class="glyphicon glyphicon-plus"></i>
                    Registrar COntacto
                </a>
            </div>
            <?php endif ?>
        </div>
        <div class="row page-header-content">
            <div class="col-md-12">
                <form class="ocform form-inline" action="<?= base_url() ?>Contacto/reporte_excelContactos" method="POST">
                    <div class="form-group">
                        <input type="text" class="form-control input-sm" name="search[value]" id="filtro" placeholder="Cliente" value="">
                    </div>
                    <div class="form-group">
                    </div>
                    
                    <div class="form-group">
                        <?php echo form_dropdown('documento', $documentos, '', array('class' => 'form-control input-sm', 'id' => 'documento')); ?>
                    </div>
                    <div class="form-group">
                        <?php echo form_dropdown('tipo', $tipos, '', array('class' => 'form-control input-sm', 'id' => 'tipo')); ?>
                    </div>
                    <div class="form-group">
                        <?php echo form_dropdown('reserva', $reserva, '', array('class' => 'form-control input-sm', 'id' => 'tipo')); ?>
                    </div>
					<div class="form-group">
                        <?php echo form_dropdown('reporte', $reporte, '', array('class' => 'form-control input-sm', 'id' => 'reporte')); ?>
                    </div>
                    <div class="form-group pull-right">
                        <button class="btn btn-sm btn-success" type="submit">
                            <span class="glyphicon glyphicon-download-alt"></span> Exportar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="page-content">
            <?php echo genDataTable('mitabla', $columns, true, true); ?>
    </div>
</div>