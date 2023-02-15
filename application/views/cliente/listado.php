<div id="page-wrapper" class="page-filter">
    <div class="page-header col-md-12">
        <div class="row page-header-title">
            <div class="col-sm-6">
                <h3>Clientes </h3></div>
            <div class="col-sm-6 text-right" role="group" aria-label="...">
                <a href="<?php echo base_url() ?>cliente/crear/#1" title="Crear" class="crearproducto btn btn-danger btn-sm">
                    <i class="glyphicon glyphicon-plus"></i>
                    Registrar cliente
                </a>
            </div>
        </div>
        <div class="row page-header-content">
            <div class="col-md-12">
    <input type="hidden" id="session-data" value="<?= $this->session->userdata('authorizedadmin') ?>">
    <form class="ocform form-inline">
        <div class="nosel">
            <div class="form-group">
                <input type="text" class="form-control input-sm" name="search[value]" id="filtro" placeholder="Cliente" value="">
            </div>
            <div class="form-group">
                <button type="submit" class="btn btn-primary btn-sm">
                    <i class="glyphicon glyphicon-search"></i>
                    Filtrar
                </button>
            </div>
            <div class="form-group">
                <button type="button" class="btn btn-primary btn-sm" id="btn-report-excel">
                    <i class="glyphicon glyphicon-download-alt"></i> Excel
                </button>
            </div>
        </div>
    </form>
    </div></div>
    </div>
    <div class="page-content">
        <div class="col-md-12">
    <?php  echo $this->Model_general->genDataTable('mitabla', $columns, true,true); ?>
   </div>
    </div>
</div>