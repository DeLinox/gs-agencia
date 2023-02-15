<div id="page-wrapper" class="page-filter">
    <div class="page-header col-md-12">
        <div class="row page-header-title">
            <input id="tipo_usuario" value="<?= $this->session->userdata('authorizedadmin') ?>" type="hidden">
            <div class="col-sm-6">
                <h3>Comprobantes </h3>
            </div>
            <!--
            <div class="col-sm-6 text-right" role="group" aria-label="...">
                <a href="<?php echo base_url() ?>Comprobante/form/" title="Crear" class="crear btn btn-danger btn-sm pull-right">
                    <i class="glyphicon glyphicon-plus"></i>
                    Registrar comprobante
                </a>
            </div>
            -->
        </div>
        <div class="row page-header-content">
            <div class="col-md-12">
                <form class="ocform form-inline" action="<?= base_url() ?>Cuenta/reporte_excelMovimientos" method="POST">
                    <div class="nosel">
                        <div class="form-group hidden">
                            <input type="text" class="form-control input-sm" name="search[value]" id="filtro" placeholder="Cliente" value="">
                        </div>
                        <div class="form-group">
                            <?php echo form_dropdown('tipo', $tipo, '', array('class' => 'form-control input-sm', 'id' => 'tipo')); ?>
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
						</div>
                        <input type="hidden" name="cuenta" value="<?= $cuentas[0]->cuen_id ?>">
                        <?php foreach ($cuentas as $i => $btn): ?>
                            <?php 
                                $class = "";
                                if($i == 0) {$simb = " S/"; $class = "active";}
                                else if($i == 1) $simb = " $";
                                else $simb = "";
                             ?>
                            <button type="button" data-id="<?= $btn->cuen_id ?>" class="btn-cuen <?= $class ?>"><?= $btn->cuen_codigo.$simb ?></button>    
                        <?php endforeach ?>
                    </div>
                </form>

            </div>
        </div>
    </div>
    <div class="page-content">
            <?php echo genDataTable('mitabla', $columns, true, true); ?>
    </div>
</div>