<div id="page-wrapper" class="page-filter">
    <div class="page-header col-md-12">
        <div class="row page-header-title">
            <input id="tipo_usuario" value="<?= $this->session->userdata('authorizedadmin') ?>" type="hidden">
            <div class="col-sm-6">
                <h3><?= $titulo ?></h3>
            </div>
        </div>
        <div class="row page-header-content">
            <div class="col-md-12">
                <form class="ocform form-inline" action="<?= base_url() ?>Flota/reporte_excelCombustibles" method="POST">
                    <div class="nosel">
                        <div class="form-group">
                            <input type="text" class="form-control input-sm" name="search[value]" id="filtro" placeholder="Buscar" value="">
                        </div>
						<div class="form-group">
                            <?php echo form_dropdown('contacto',$contacto, '', array('class' => 'form-control input-sm','id' => 'contacto')); ?>
                        </div>
						<div class="form-group">
                            <?php echo form_dropdown('tipo',$tipo, '', array('class' => 'form-control input-sm','id' => 'tipo')); ?>
                        </div>
						<div class="form-group">
							<?php echo form_dropdown('servicio[]', $servicio, '', array('class' => 'selectpicker input-sm', 'multiple'=>"multiple", 'id' => 'servicio')); ?>
						</div>
						<input type="hidden" name="serv_ids"/>
                        <input type="hidden" name="desde" id="desde" value="" />
                        <input type="hidden" name="hasta" id="hasta" value="" />
                        <div class="form-group">
                            <div id="reportrange" class="pull-right" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc;">
                                <i class="glyphicon glyphicon-calendar fa fa-calendar"></i>&nbsp;
                                <span></span> <b class="caret"></b>
                            </div>
                        </div>
                        <div class="onsel form-group hidden">
                            <a class="btn btn-primary btn-sm gen_ordenPago" href="#">
                                <i class="glyphicon glyphicon-plus"></i>
                                Orden de Pago
                            </a>
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