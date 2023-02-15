<div id="page-wrapper" class="page-filter">
    <div class="page-header col-md-12">
        <div class="row page-header-title">
            <input id="tipo_usuario" value="<?= $this->session->userdata('authorizedadmin') ?>" type="hidden">
            <div class="col-sm-6">
                <h3 style="display: inline-block;"><?php echo $titulo; ?></h3>
            </div>
            <?php $this->load->view("Reporte/opc_reportes") ?>
            <div class="col-sm-6 text-right" role="group" aria-label="...">
            </div>
        </div>
        <div class="row page-header-content">
            <div class="col-md-12">
                <form class="ocform form-inline">
                    
                    <div class="nosel">
                        <div class="form-group">
                            <input type="text" class="form-control input-sm" name="search[value]" id="filtro" placeholder="Cliente" value="">
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
							<?php echo form_dropdown('usuario', $usuarios,'',array('class' => 'form-control input-sm'));?>
                        </div>
						<div class="form-group">
							<?php echo form_dropdown('modulo', $modulos,'',array('class' => 'form-control input-sm'));?>
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