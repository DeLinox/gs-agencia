<div id="page-wrapper" class="page-filter">
    <div class="page-header col-md-12">
        <div class="row page-header-title">
            <input id="tipo_usuario" value="<?= $this->session->userdata('authorizedadmin') ?>" type="hidden">
            <div class="col-sm-6">
                <h3>Guias </h3>
            </div>
            <div class="col-sm-6 text-right" role="group" aria-label="...">
                <a href="<?php echo base_url() ?>Contacto/crear_guia" title="Crear guia" class="crear btn btn-danger btn-sm pull-right">
                    <i class="glyphicon glyphicon-plus"></i>
                    Registrar Guia
                </a>
            </div>
        </div>
        <div class="row page-header-content">
            <div class="col-md-12">
                <form class="ocform form-inline">
                    <div class="form-group">
                        <input type="text" class="form-control input-sm" name="search[value]" id="filtro" placeholder="Hotel" value="">
                    </div>
                    <div class="form-group">
                    </div>
                    <div class="form-group pull-right">
                        <div class="dropdown">
                            <button class="btn btn-sm btn-success dropdown-toggle" type="button" data-toggle="dropdown">
                                <span class="glyphicon glyphicon-download-alt"></span> Exportar
                            </button>
                            <ul class="dropdown-menu  dropdown-menu-right">
                                <li><a href="#" id="btn-report-excel">  Excel </a></li>
                            </ul>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="page-content">
            <?php echo genDataTable('mitabla', $columns, true, true); ?>
    </div>
</div>