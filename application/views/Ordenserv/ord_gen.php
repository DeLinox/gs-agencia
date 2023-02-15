<script>
    adic = <?= $adic ?>
</script>

<div id="page-wrapper">
    <div class="page-header col-md-12">
        <div class="row page-header-title">
            <div class="col-sm-6">
                <h3><?php echo $titulo; ?></h3>
            </div>
        </div>
    </div>
    <div class="page-content serv">
        <div class="col-md-12">
            <div class="alert alert-danger error hidden" role="alert">
                <span class="text">Error:</span>
            </div>
            <form id="frm-gen-orden" class="form-horizontal" action="<?=base_url()?>Ordenserv/guardar_orden/<?= $orden->orde_id ?>" method="post">
                <div class="modal-body">
                    <input type="hidden" name="paqu_id" value="<?= $orden->orde_paqu_id ?>">
                    <div class="alert alert-danger errorOrd hidden" role="alert">
                        <span class="text">Error:</span>
                    </div>
                        <input type="hidden" name="total_total" value="<?= $orden->orde_total ?>">
                        <div class="form-group">
                            <div class="col-sm-2">
                                <?= form_label('<strong>Orden Numero</strong>', 'numero', array('class' => 'control-label')); ?>
                                <?= form_input(array("name" => "numero", "value" => $orden->orde_numero, "class" => "form-control input-sm", "readonly" => "readonly")); ?>
                            </div>
                            <div class="col-sm-4">
                                <?= form_label('<strong>Servicio</strong>', 'servicio', array('class' => 'control-label')); ?>
                                <select id="servicio" name="servicio" class="form-control input-sm" ></select>
                            </div>
                            <div class="col-sm-6">
                                <?= form_label('<strong>Observaciones</strong>', 'moneda', array('class' => 'control-label')); ?>
                                <textarea name="observacion" class="input-sm form-control"><?= $orden->orde_observacion ?></textarea>
                            </div>
                            <div class="col-sm-3 hidden">
                                <?= form_label('<strong>Fecha Emision</strong>', 'fecha_emi', array('class' => 'control-label')); ?>
                                <div class="input-group">
                                    <input id="fecha_emi" name="fecha_emi" type="text" class="form-control fecha" value="<?= $orden->orde_fecha ?>" placeholder="dd/mm/aaaa" />
                                    <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                                </div>
                            </div>
                            <div class="col-sm-4 hidden">
                                <?= form_label('<strong>Moneda</strong>', 'moneda', array('class' => 'control-label')); ?>
                                <?= form_dropdown('moneda', $monedas, $orden->orde_moneda, array('class' => 'form-control input-sm')); ?>
                            </div>
                            

                            
                        </div>
                        <input type="hidden" id="serv_id" name="serv_id" value="<?= $orden->orde_serv_id ?>">
                        <input type="hidden" id="serv_name" name="serv_name" value="<?= $orden->orde_servicio ?>">
                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>FECHA</th>
                                <th>SERVICIO</th>
                                <th>FILE</th>
                                <th>N° PAX</th>
                                <th>GRUPO / NOMBRE</th>
                                <th>GUIA</th>
                                <th>HOTEL</th>
                                <th>HORA</th>
                                <th>LUNCH</th>
                                <th>CONTACTO</th>
                                <th>ENDOSE</th>
                                <th>OBSERVACIONES</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                                $almuerzos = 0;
                                $paxs = 0;
                                foreach ($detas as $i => $val):   
                                    $almuerzos += $val->deta_lunch;
                                    $paxs += $val->deta_pax;
                            ?>
                            <tr>
                                <input type="hidden" name="paqu_deta_id[]" value="<?= $val->deta_pdet_id ?>">
                                <td><?= $val->deta_fecha ?></td>
                                <td><?= $val->deta_servicio ?></td>
                                <td><?= $val->deta_file ?></td>
                                <td><?= $val->deta_pax ?></td>
                                <td><?= $val->deta_nombres ?></td>
                                <td><?= $val->deta_guia ?></td>
                                <td><?= $val->deta_hotel ?></td>
                                <td><?= $val->deta_hora ?></td>
                                <td><?= $val->deta_lunch ?></td>
                                <td><?= $val->deta_contacto ?></td>
                                <td><?= $val->deta_endose ?></td>
                                <td><?= $val->deta_obs ?></td>
                            </tr>
                            <?php endforeach ?>
                            <tr>
                                <th colspan="2">Total N° Pax</th>
                                <th><?= $paxs ?></th>
                                <td></td>
                                <th colspan="2">Total Lunch</th>
                                <th><?= $almuerzos  ?></th>
                                <td colspan="3"></td>
                            </tr>
                        </tbody>
                    </table>

                    <div class="form-group">
                        <div class="col-sm-3">
                            <strong>Servicio</strong>
                        </div>
                        <div class="col-sm-3">
                            <strong>Proveedor</strong>
                        </div>
                        <div class="col-sm-3">
                            <div class="row">
                                <!--
                                <div class="col-sm-6">
                                    <strong>Estado</strong>
                                </div>
                                -->
                                <div class="col-sm-6">
                                    <strong>Moneda</strong>
                                </div>  
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="row">
                                <div class="col-sm-6">
                                    <strong>Cantidad</strong>
                                </div>
                                <div class="col-sm-6">
                                    <strong>Costo</strong>
                                </div>  
                            </div>
                        </div>
                    </div>
                    <div class="serv_adicionales">
                        
                    </div>
                    <div class="form-group">
                        <div class="col-sm-12">
                            <button type="button" class="btn btn-warning btn-sm agregaradic pull-left"><span class="glyphicon glyphicon-plus-sign" aria-hidden="true"></span> Servicio adicional</button>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </div>
            </form>

        </div>
    </div>
</div>

<div id="clonables" class="hide">
    <div class="serv-item form-group">
        <input type="hidden" name="deta_id[]" value="" />
        <input type="hidden" name="deta_pdet_id[]" value="" />
        <input type="hidden" name="deta_endose[]" value="" />
        <input type="hidden" name="deta_file[]" value="" />
        <input type="hidden" name="deta_hora[]" value="" />
        <input type="hidden" name="deta_fecha[]" value="" />
        <input type="hidden" name="deta_lunch[]" value="" />

        <div class="col-sm-1">
            <input id="deta_pax" name="deta_pax[]" type="text" class="form-control deta_pax"/>
        </div>
        <div class="col-sm-3">
            <input id="deta_nombres" name="deta_nombres[]" type="text" class="form-control deta_nombres"/>
        </div>
        <div class="col-sm-2">
            <input id="deta_hotel" name="deta_hotel[]" type="text" class="form-control deta_hotel"/>
        </div>
        <div class="col-sm-3">
            <input id="deta_contacto" name="deta_contacto[]" type="text" class="form-control deta_contacto"/>
        </div>
        <div class="col-sm-3">
            <input id="deta_obs" name="deta_obs[]" type="text" class="form-control deta_obs"/>
        </div>
    </div>
    <div class="adic-item">
        <input type="hidden" name="adic_id[]" value="" />
        <div class="serv-opts">
            <button type="button" class="btn btn-danger btn-sm" data-href="/delete.php?id=23" data-toggle="modal" data-target="#confirm-delete">
                <span class="glyphicon glyphicon-trash borrarItem" aria-hidden="true"></span>
            </button>
        </div>
        <div class="adic-deta">
        <div class="form-group"  style="margin-bottom: 4px">
            <div class="col-sm-3">
                <select name="tservicio[]" class="form-control adic_tservicio" id="tservicio"></select>
            </div>
            <div class="col-sm-3">
                <select name="proveedor[]" class="form-control adic_proveedor" id="proveedor"></select>
            </div>
            <div class="col-sm-2">
                <?= form_dropdown('moneda[]', $monedas, '', array('class' => 'form-control input-sm', "id" => "moneda")); ?>
            </div>
            <div class="col-sm-2">
                <input class="form-control input-sm text-right" value="0" type="text" name="adicional_cant[]" id="adicional_cant">
            </div>
            <div class="col-sm-2">
                <div class="input-group">
                    <span class="input-group-addon simbolo input-sm">S/</span>
                    <input class="form-control input-sm text-right" value="0.00" type="text" name="adicional_precio[]" id="adicional_precio">
                </div>
            </div>
        </div>
        <div class="form-group" style="margin-bottom: 0px;">
            <div class="col-sm-6">
                <input class="form-control input-sm" type="text" name="adicional_deta[]" id="adicional_deta" placeholder="descripcion del servicio">
            </div>
            <div class="col-sm-2">
                <input class="form-control input-sm" type="text" name="add_guia[]" id="add_guia" placeholder="Guia">
            </div>
            <div class="col-sm-2">
                <div class='input-group'>
                    <input type='text' name="add_fecha[]" class="form-control datepicker" id="add_fecha" placeholder="dd/mm/yyy" />
                    <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                </div>
            </div>
            <div class="col-sm-2">
                <div class='input-group'>
                    <input type='text' name="add_hora[]" class="form-control timepicker" id="add_hora" placeholder="hh:mm M" />
                    <span class="input-group-addon"><span class="glyphicon glyphicon-time"></span></span>
                </div>
            </div>
            
            
        </div>
        </div>
    </div>
</div>
<div class="modal fade" id="confirm-delete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel">Confirmación</h4>
            </div>
            <div class="modal-body">
                <p>¿Relmente desea borrar el registro?</p>
                <p class="debug-url"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                <a class="btn btn-danger btn-ok">Borrar</a>
            </div>
        </div>
    </div>
</div>
<script>
$('#confirm-delete').on('show.bs.modal', function(e) {
    $this = $(e.relatedTarget);
    $dlg = $(e.delegateTarget);
    $(this).find('.btn-ok').on('click', function() {
        $this.parents('.adic-item').fadeOut('slow', function() {
            $this.parents('.adic-item').remove();
        });
        $dlg.modal('hide')
    });
});
</script>