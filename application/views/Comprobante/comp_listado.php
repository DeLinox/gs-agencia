<div id="page-wrapper" class="page-filter">
    <div class="page-header col-md-12">
        <div class="row page-header-title">
            <input id="tipo_usuario" value="<?= $this->session->userdata('authorizedadmin') ?>" type="hidden">
            <div class="col-sm-6">
                <h3>Reservas </h3>
            </div>
            <div class="col-sm-6 text-right">
				<label><strong>Suma seleccionados: </strong></label>
                <input type="text" id="sum_sel" style="background: transparent; border: none; font-weight: bold; font-size: 1.5em;" readonly value="0.00">
				<a target="_blank" class="btn btn-sm btn-primary" type="button" href="http://factura.piruw.com/jumbotravel">
					<i class="glyphicon glyphicon-log-out"></i> Facturador
				</a>			
				<a class="btn btn-sm btn-danger" type="button" href="<?= base_url() ?>Comprobante/comp_listadoLiquidacion">
					<i class="glyphicon glyphicon-log-out"></i> Liquidaciones
				</a>
            </div>
        </div>
        <div class="row page-header-content">
            <div class="col-md-12">
                <form class="ocform form-inline" action="<?= base_url() ?>Comprobante/reporte_excelComprobantes" method="POST">
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
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group hidden">
                            <input type="text" class="form-control input-sm" name="search[value]" id="filtro" placeholder="Cliente" value="">
                        </div>
                        <div class="form-group">
                            <?php echo form_dropdown('cliente', $contacto, '', array('class' => 'form-control input-sm', 'id' => 'cliente')); ?>
                        </div>
                        <input type="hidden" name="desde" id="desde" />
                        <input type="hidden" name="hasta" id="hasta" />
                        <div class="form-group">
                            <div id="reportrange" class="pull-right" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc;">
                                <i class="glyphicon glyphicon-calendar fa fa-calendar"></i>&nbsp;
                                <span></span> <b class="caret"></b>
                            </div>
                        </div>
                        <div class="form-group pull-right">
							<button class="btn btn-sm btn-success" type="submit">
								<span class="glyphicon glyphicon-download-alt"></span> Exportar
							</button>
							<button class="btn btn-sm btn-info actualizarComprobantes" type="button">
								<span class="glyphicon glyphicon-refresh"></span> Actualizar numero de factura
							</button>
						</div>
						
                    </div>
                </form>
                <div class="onsel hidden">
					<div class="form-group pull-right">
						<a class="btn btn-sm btn-success gen_selExcel" type="button">
                            <span class="glyphicon glyphicon-save"></span> Excel
                        </a>
						<a class="btn btn-sm btn-primary gen_liquidacion" type="button">
							<span class="glyphicon glyphicon-edit"></span> Generar Liquidación
						</a>
					</div>
				</div>
            </div>
        </div>
    </div>
    <div class="page-content">
            <?php echo genDataTable('mitabla', $columns, true, true); ?>
    </div>
</div>