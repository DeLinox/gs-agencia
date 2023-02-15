<div id="page-wrapper" class="page-filter">
    <div class="page-header col-md-12">
        <div class="row page-header-title">
            <input id="tipo_usuario" value="<?= $this->session->userdata('authorizedadmin') ?>" type="hidden">
            <div class="col-sm-6">
                <h3>Reservas </h3>
            </div>
            <div class="col-sm-6 text-right" role="group" aria-label="...">
                <label style="color: #FF0000; "><strong>Saldos: </strong></label>
                <input type="text" id="sum_sal" style="color: #FF0000; background: transparent; border: none; font-weight: bold; font-size: 1.5em; width: 70px;" readonly value="0.00">
                <label><strong>Suma seleccionados: </strong></label>
                <input type="text" id="sum_sel" style="background: transparent; border: none; font-weight: bold; font-size: 1.5em; width: 70px;" readonly value="0.00">
                <div class="dropdown" style="float: right;">
                    <a class="btn btn-sm btn-danger" type="button" href="<?= base_url() ?>Registro/reg_pagos">
                        <i class="glyphicon glyphicon-log-out"></i> Proveedores
                    </a>
                </div>
            </div>
        </div>
        <div class="row page-header-content">
            <div class="col-md-12">
                <form class="ocform form-inline" action="<?= base_url() ?>Registro/reporte_excelAuxiliar" method="POST">
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
                                            <label>Comprobante</label>
                                        </p>
                                        <p>
                                            <?php echo form_dropdown('det_comp', $gen_comp,$poststr['det_comp'],array('class' => 'form-control input-sm'));?>
                                        </p>
                                        <p>
                                            <label>Hoja de liquidacion</label>
                                        </p>
                                        <p>
                                            <?php echo form_dropdown('det_liqu', $gen_liqu,$poststr['det_liqu'],array('class' => 'form-control input-sm'));?>
                                        </p>
                                        <p>
                                            <label>Cobrado</label>
                                        </p>
                                        <p>
                                            <?php echo form_dropdown('paqu_cobrado', $gen_cobrado,"",array('class' => 'form-control input-sm'));?>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <input type="text" class="form-control input-sm" name="search[value]" id="filtro" placeholder="Buscar" value="">
                        </div>
                        <div class="form-group hidden">
                            <input type="text" class="form-control input-sm" name="busqueda" placeholder="Grupo/Nombre/File/Contacto/Fileref">
                        </div>
                        <div class="form-group">
                            <?php echo form_dropdown('contacto', $contacto, '', array('class' => 'form-control input-sm', 'id' => 'contacto')); ?>
                        </div>
                        <div class="form-group">
                            <?php echo form_dropdown('tipo', $tipo, $usua_tipo, array('class' => 'form-control input-sm', 'id' => 'tipo')); ?>
                        </div>
                        
                        
                        
                        <input type="hidden" name="serv_ids" value="<?= $servicios[0]->serv_id ?>">
                        <input type="hidden" name="desde" id="desde" />
                        <input type="hidden" name="hasta" id="hasta" />
                        <div class="form-group">
                            <div id="reportrange" class="pull-right" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc;">
                                <i class="glyphicon glyphicon-calendar fa fa-calendar"></i>&nbsp;
                                <span></span> <b class="caret"></b>
                            </div>
                        </div>
                        <div class="form-group">
						<!-- $servicios[0]->serv_id -->
						
                            <?php echo form_dropdown('servicio[]', $servicio, '', array('class' => 'selectpicker input-sm', 'multiple'=>"multiple", 'id' => 'servicio')); ?>
                        </div>
                        <!--
                        <?php foreach ($servicios as $i => $serv): ?>
                            <button type="button" data-id="<?= $serv->serv_id ?>" class="btn-cuen <?= ($i == 0)?'active':'' ?>"><?= $serv->serv_abrev ?></button>    
                        <?php endforeach ?>
                        -->
                        <div class="onsel form-group hidden">
                            <div class="onsel form-group hidden">
								<a class="btn btn-success btn-sm gen_comprobante" href="#">
									<i class="glyphicon glyphicon-edit"></i>
									Comprobante
								</a>
								
								<div class="dropdown" style="float: left; margin-right: 10px">
									<button class="btn btn-sm btn-info reporte_excel dropdown-toggle" type="button" data-toggle="dropdown">
										<i class="glyphicon glyphicon-plus"></i> Hoja de liquidación
									</button>
									<ul class="dropdown-menu  dropdown-menu-right">
										<li><a class="btn btn-sm gen_liquidacion" href="#"> Local</a></li>
										<li><a class="btn btn-sm gen_ord_pago" href="#"> Receptivo</a></li>
									</ul>
								</div>
							</div>
                        </div>
                        <div class="form-group pull-right">                  
							<button class="btn btn-sm btn-success reporte_excel dropdown-toggle" type="button" data-toggle="dropdown">
								<i class="glyphicon glyphicon-download-alt"></i> Exportar Excel
							</button>
							<ul class="dropdown-menu  dropdown-menu-right">
								<li><a id="btn-report-regAux" href="#" class="btn btn-sm">Reg. Auxiliar</a></li>
								<li><a id="btn-report-regCobros" href="#" class="crear btn btn-sm">Cobros</a></li>
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