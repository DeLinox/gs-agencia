<div id="page-wrapper" class="page-filter">
    <div class="page-header col-md-12">
        <div class="row page-header-title">
            <input id="tipo_usuario" value="<?= $this->session->userdata('authorizedadmin') ?>" type="hidden">
            <div class="col-sm-6">
                <h3>Reservas </h3>
            </div>
            <div class="col-sm-6 text-right" role="group" aria-label="...">
                <a type="button" href="<?= base_url() ?>Registro/paq_listPrivados" class="btn btn-primary btn-sm"><span class="glyphicon glyphicon-share-alt"></span> Servicios Privados</a>
                <?php if ($this->editar > 1): ?>
                <div class="dropdown" style="display: inline-block;">
                    <button class="btn btn-sm btn-danger reporte_excel dropdown-toggle" type="button" data-toggle="dropdown">
                        <i class="glyphicon glyphicon-plus"></i> Crear reserva
                    </button>
                    <ul class="dropdown-menu  dropdown-menu-right">
                        <li><a href="<?php echo base_url() ?>Registro/paq_crear/" title="Crear" class="crear btn btn-sm">Receptivo</a></li>
                        <li><a href="<?php echo base_url() ?>Registro/paq_crear_local/" title="Crear reserva local" class="crear btn btn-sm crear_local">Local</a></li>
                        <li><a href="<?php echo base_url() ?>Registro/paq_crear_privado/" title="Crear servicio provado" class="crear btn btn-sm crear_privado">Servicio privado</a></li>
                        <li><a href="<?php echo base_url() ?>Registro/reserva_rapida/" title="Crear reserva rápida" class="crear btn btn-sm crear_rapido">Reserva Rápida</a></li>
                    </ul>
                </div>
                <?php endif ?>
            </div>
        </div>
        <div class="row page-header-content">
            <div class="col-md-12">
                <form class="ocform form-inline" action="<?= base_url() ?>Registro/reporte_excelReservas" method="POST">
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
                                            <label>Contacto </label>
                                        </p>
                                        <p>
                                            <?php echo form_dropdown('contacto', $contacto,$poststr['contacto'], array('class' => 'form-control input-sm', 'id' => 'contacto')); ?>
                                        </p>
                                        <p>
                                            <label>Servicio</label>
                                        </p>
                                        <p>
                                            <?php echo form_dropdown('scontacto', $servicio,$poststr['scontacto'],array('class' =>'form-control input-sm'));?>
                                        </p>
                                        <p>
                                            <label>Estado</label>
                                        </p>
                                        <p>
                                            <?php echo form_dropdown('estado', $estado, $poststr['estado'], array('class' => 'form-control input-sm', 'id' => 'estado')); ?>
                                        </p>
                                        <p>
                                            <label>Usuario</label>
                                        </p>
                                        <p>
                                            <?php echo form_dropdown('usuario', $usuario,$poststr['usuario'],array('class' => 'form-control input-sm'));?>
                                        </p>
                                        <p>
                                            <label>Orden de servicio</label>
                                        </p>
                                        <p>
                                            <?php echo form_dropdown('det_orde', $gen_orden,$poststr['det_orde'],array('class' => 'form-control input-sm'));?>
                                        </p>
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
                                            <?php echo form_dropdown('paqu_cobrado', array(""=> "* Cobrado", "1"=>"SI","0"=>"NO"),"",array('class' => 'form-control input-sm'));?>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <input type="text" class="form-control input-sm" name="search[value]" id="filtro" placeholder="Buscar" value="<?php echo $poststr['search']['value']?>">
                        </div>
                        <div class="form-group">
                            <?php echo form_dropdown('tipo', $tipo, $poststr['tipo'], array('class' => 'form-control input-sm', 'id' => 'tipo')); ?>
                        </div>
                        <!--
                        <div class="form-group">
                            <?php echo form_dropdown('servicio[]', $servicio, '', array('class' => 'selectpicker', 'multiple'=>"multiple", 'id' => 'servicio')); ?>
                        </div>
                        -->
                        <input type="hidden" class="form-control input-sm" name="serv_ids" value="<?php echo $poststr['serv_ids'] ?>">
                        <input type="hidden" name="desde" id="desde" value="<?php echo $poststr['desde'] ?>" />
                        <input type="hidden" name="hasta" id="hasta" value="<?php echo $poststr['hasta'] ?>" />
                        <div class="form-group">
                            <div id="reportrange" class="pull-right" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc;">
                                <i class="glyphicon glyphicon-calendar fa fa-calendar"></i>&nbsp;
                                <span></span> <b class="caret"></b>
                            </div>
                        </div>
                        <?php foreach ($servicios as $i => $serv): ?>
                            <button type="button" data-id="<?= $serv->serv_id ?>" class="btn-cuen"><?= $serv->serv_abrev ?></button>    
                        <?php endforeach ?>


                        <div class="onsel form-group hidden">
                            <a class="btn btn-primary btn-sm gen_ord_serv" href="#">
                                <i class="glyphicon glyphicon-refresh"></i>
                                Ord. Servicio
                            </a>
                        </div>
                        <div class="form-group pull-right">
                            <button class="btn btn-sm btn-success" type="submit">
                                <span class="glyphicon glyphicon-download-alt"></span> Exportar
                            </button>
                        </div>
                        <?php if ($this->editar > 1): ?>
                        <div class="form-group pull-right onsel hidden">
                            <div class="dropdown">
                                <button class="btn btn-sm btn-default reporte_excel dropdown-toggle" type="button" data-toggle="dropdown">
                                    <span class="glyphicon glyphicon-refresh"></span> Estado <span class="caret"></span>
                                </button>
                                <ul class="dropdown-menu  dropdown-menu-right">
                                    <li><a href="#" data-acc="PENDIENTE" class="btn-estado">  Pendiente </a></li>
                                    <li><a href="#" data-acc="CONFIRMADO"  class="btn-estado">  Confirmado </a></li>
                                    <li><a href="#" data-acc="ANULADO" class="btn-estado">  Anulado </a></li>
                                </ul>
                            </div>
                        </div>
                        <?php endif ?>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="page-content">
            <?php echo genDataTable('mitabla', $columns, true, true); ?>
    </div>
</div>