<div id="page-wrapper" class="page-filter">
    <div class="page-header col-md-12">
        <div class="row page-header-title">
            <input id="tipo_usuario" value="<?= $this->session->userdata('authorizedadmin') ?>" type="hidden">
            <div class="col-sm-6">
                <h3>Proveedores </h3>
            </div>
            <?php if($this->editarprov > 1): ?>
            <div class="col-sm-6 text-right" role="group" aria-label="...">
                <a href="<?php echo base_url() ?>Contacto/crear_provEmp" title="Crear proveedor" class="crear btn btn-danger btn-sm pull-right">
                    <i class="glyphicon glyphicon-plus"></i>
                    Registrar Proveedor
                </a>
            </div>
            <?php endif ?>
        </div>
        <div class="row page-header-content">
            <div class="col-md-12">
                <form class="ocform form-inline">
                    <div class="form-group">
                        <input type="text" class="form-control input-sm" name="search[value]" id="filtro" placeholder="Cliente" value="">
                    </div>
                    <div class="form-group">
                    </div>
                    
                    <div class="form-group">
                        <?php echo form_dropdown('documento', $documentos, '', array('class' => 'form-control input-sm', 'id' => 'documento')); ?>
                    </div>
                    <div class="form-group">
                        <?php echo form_dropdown('tipoc', $tipoc, '', array('class' => 'form-control input-sm', 'id' => 'tipoc')); ?>
                    </div>
                    <div class="form-group pull-right">
                        <a href="<?= base_url() ?>Contacto/prov_listado" class="btn btn-sm btn-primary">
                            <span class="glyphicon glyphicon-share"></span> Contactos
                        </a>
                        <a id="btn-report-excel" href="#" class="btn btn-sm btn-success">
                            <span class="glyphicon glyphicon-download-alt"></span> Emportar
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="page-content">
            <?php echo genDataTable('mitabla', $columns, true, true); ?>
    </div>
</div>