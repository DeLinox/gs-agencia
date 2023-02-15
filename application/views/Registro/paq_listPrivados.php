<div id="page-wrapper" class="page-filter">
    <div class="page-header col-md-12">
        <div class="row page-header-title">
            <input id="tipo_usuario" value="<?= $this->session->userdata('authorizedadmin') ?>" type="hidden">
            <div class="col-sm-6">
                <h3>Reservas </h3>
            </div>
            <div class="col-sm-6 text-right" role="group" aria-label="...">
                <div class="dropdown">
                    <a class="btn btn-sm btn-primary" type="button" href="<?= base_url() ?>Registro/paq_listado/">
                        <i class="glyphicon glyphicon-share-alt"></i> Reservas
                    </a>
                </div>
            </div>
        </div>
        <div class="row page-header-content">
            <div class="col-md-12">
                <form class="ocform form-inline" action="<?= base_url() ?>Registro/reporte_excelPrivados" method="POST">
                    <div class="nosel">
                        <div class="form-group hidden">
                            <input type="text" class="form-control input-sm" name="search[value]" id="filtro" placeholder="Buscar" value="">
                        </div>
                        <div class="form-group">
                            <input type="text" class="form-control input-sm" name="busqueda" id="busqueda" placeholder="Buscar Grupo / Nombre">
                        </div>
                        <div class="form-group">
                            <?php echo form_dropdown('contacto', $contacto, '', array('class' => 'form-control input-sm', 'id' => 'contacto')); ?>
                        </div>
                        
                        <input type="hidden" class="form-control input-sm" name="serv_ids" value="<?= $servicios[0]->serv_id ?>">
                        <input type="hidden" name="desde" id="desde" value="<?php echo $poststr['desde'] ?>" />
                        <input type="hidden" name="hasta" id="hasta" value="<?php echo $poststr['hasta'] ?>" />
                        <div class="form-group">
                            <div id="reportrange" class="pull-right" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc;">
                                <i class="glyphicon glyphicon-calendar fa fa-calendar"></i>&nbsp;
                                <span></span> <b class="caret"></b>
                            </div>
                        </div>
                        <!--
                        <?php foreach ($servicios as $i => $serv): ?>
                            <button type="button" data-id="<?= $serv->serv_id ?>" class="btn-cuen <?= ($i == 0)?'active':'' ?>"><?= $serv->serv_abrev ?></button>    
                        <?php endforeach ?>
                        -->
                        <div class="onsel form-group hidden">
                            <div class="onsel form-group hidden">
                        </div>
                        </div>
                        <div class="form-group pull-right">
                            <button class="btn btn-sm btn-success" type="submit">
                                <span class="glyphicon glyphicon-download-alt"></span> Exportar
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="page-content">
        <div class="table-responsive">
            <?php echo genDataTable('mitabla', $columns, true, true); ?>
        </div>
    </div>
</div>