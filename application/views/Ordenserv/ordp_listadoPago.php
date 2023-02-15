<div id="page-wrapper" class="page-filter">
    <div class="page-header col-md-12">
        <div class="row page-header-title">
            <input id="tipo_usuario" value="<?= $this->session->userdata('authorizedadmin') ?>" type="hidden">
            <div class="col-sm-6">
                <h3>Ordedes de Pago </h3>
            </div>
            <!--
            <div class="col-sm-6 text-right" role="group" aria-label="...">
                <div class="dropdown">
                    <a class="btn btn-sm btn-danger" type="button" href="<?= base_url() ?>Ordenserv/ord_proveedores">
                        <i class="glyphicon glyphicon-log-out"></i> Proveedores
                    </a>
                </div>
            </div>
            -->
        </div>
        <div class="row page-header-content">
            <div class="col-md-12">
                <form class="ocform form-inline">
                    <div class="form-group">
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
                                        <select name="estado" class="form-control input-sm" id="estado">
                                            <option value="" selected="selected">* Estado</option>
                                            <option value="PA">Pagado</option>
                                            <option value="PE">Pendiente</option>
                                        </select>
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
                    <input type="hidden" name="desde" id="desde" value="<?php echo $poststr['desde'] ?>" />
                        <input type="hidden" name="hasta" id="hasta" value="<?php echo $poststr['hasta'] ?>" />
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