<div id="page-wrapper" class="page-filter">
    <div class="page-header col-md-12">
        <div class="row page-header-title">
            <input id="tipo_usuario" value="<?= $this->session->userdata('authorizedadmin') ?>" type="hidden">
            <div class="col-sm-6">
                <h3>Comprobantes </h3>
            </div>
        </div>
        <div class="row page-header-content">
            <div class="col-md-12">
                <form class="ocform form-inline" action="<?= base_url() ?>Liquidacion/reporte_excelLiquidaciones" method="POST">
                    <div class="onsel form-inline hidden">
                    </div>
                    <div class="nosel">
                        <div class="form-group ">
                            <div class="dropdown filter">
                                <button class="btn btn-sm btn-default dropdown-toggle" type="button" data-toggle="dropdown">
                                    <span class="glyphicon glyphicon-menu-hamburger"></span>
                                    <span id="desFilter"></span>
                                    <span class="caret"></span>
                                </button>
                                <div class="dropdown-menu">
                                    <div class="pad10">
                                        <p>
                                            <label>Moneda</label>
                                        </p>
                                        <p>
                                            <?php echo form_dropdown('moneda', $moneda, '', array('class' => 'form-control input-sm', 'id' => 'moneda')); ?>
                                        </p>
                                        <p>
                                            <label>Estado</label>
                                        </p>
                                        <p>
                                            <label>Comprobante</label>
                                        </p>
                                        <p>
                                            <?php echo form_dropdown('comprobantes', $comprobantes,'',array('class' => 'form-control input-sm'));?>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <input type="text" class="form-control input-sm" name="search[value]" id="filtro" placeholder="Cliente" value="">
                        </div>
                        <div class="form-group">
                        </div>
                        <input type="hidden" name="desde" id="desde" />
                        <input type="hidden" name="hasta" id="hasta" />
                        <div class="form-group">
                            <div id="reportrange" class="pull-right" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc;">
                                <i class="glyphicon glyphicon-calendar fa fa-calendar"></i>&nbsp;
                                <span></span> <b class="caret"></b>
                            </div>
                        </div>
                        <div class="form-group">
                        </div>
                        <div class="form-group">
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
            <?php echo genDataTable('mitabla', $columns, true, true); ?>
    </div>
</div>