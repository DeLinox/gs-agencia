<script>
    detas = <?= $detas ?>;
</script>
<div class="modal-dialog" role="document" style="min-width: 1000px;">
    <div class="modal-content">
        <form id="frm-gen_orden" class="form-horizontal" action="<?=base_url()?>Registro/guardar_orden/<?= $orden->orde_id ?>" method="post">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="gridSystemModalLabel"></h4>
            </div>
            <div class="modal-body">
                <input type="hidden" name="paqu_id" value="<?= $orden->orde_paqu_id ?>">
                <div class="alert alert-danger errorOrd hidden" role="alert">
                    <span class="text">Error:</span>
                </div>
                <div class="row">
                    <div class="col-sm-6">
                        <div class="col-sm-4">
                            <?= form_label('<strong>Orden Numero</strong>', 'numero', array('class' => 'control-label')); ?>
                            <?= form_input(array("name" => "numero", "value" => $orden->orde_nro, "class" => "form-control input-sm")); ?>
                        </div>
                        <div class="col-sm-4">
                            <?= form_label('<strong>Fecha Emision</strong>', 'fecha_emi', array('class' => 'control-label')); ?>
                            <div class="input-group">
                                <input id="fecha_emi" name="fecha_emi" type="text" class="form-control fecha" value="<?= $orden->orde_fecha ?>" placeholder="dd/mm/aaaa" />
                                <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <?= form_label('<strong>Moneda</strong>', 'moneda', array('class' => 'control-label')); ?>
                            <?= form_dropdown('moneda', $monedas, $orden->orde_moneda, array('class' => 'form-control input-sm')); ?>
                        </div>
                        <div class="col-sm-12">
                            <?= form_label('<strong>Proveedor</strong>', 'proveedor', array('class' => 'control-label')); ?>
                            <?= form_dropdown('proveedor', $proveedores, $orden->orde_prov_id, array('class' => 'form-control input-sm')); ?>
                        </div>
                        <div class="col-sm-12">
                            <?= form_label('<strong>Nombre de Pasajero / Grupo</strong>', 'nombre', array('class' => 'control-label')); ?>
                            <?= form_input(array("name" => "nombre", "value" => $orden->orde_nombre, "class" => "form-control input-sm")); ?>
                        </div>
                    </div>
                </div>
                
                <div class="">
                    <div class="row hhd">
                        <div class="fact-head">
                            <div class="col-sm-2">
                                FECHA
                            </div>
                            <div class="col-sm-7">
                                PRODUCTO / SERVICIO
                            </div>
                            <div class="col-sm-2">
                                COSTO NETO
                            </div>
                            <div class="col-sm-1"></div>
                        </div>
                    </div>
                </div>
                <div class="row hhs">
                    
                    <div class="sortable clearfix"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-sm agregarfila pull-left">
                    <span class="glyphicon glyphicon-plus-sign" aria-hidden="true"></span> Agregar producto
                </button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                <button type="submit" class="btn btn-primary">Guardar</button>
            </div>
        </form>
    </div>
</div>
<div id="clonables" class="hide">
    <div class="serv-item form-group">
        <input type="hidden" name="deta_id[]" value="" />
        <div class="col-sm-2">
            <div class="input-group">
                <input id="deta_fecha" name="deta_fecha[]" type="text" class="form-control deta_fecha"  placeholder="dd/mm/aaaa" />
                <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
            </div>
        </div>
        <div class="col-sm-7">
            <textarea id="deta_serv" name="deta_serv[]" type="text" class="form-control deta_serv"  placeholder="Descripcion del servicio" rows="1"></textarea>
        </div>
        <div class="col-sm-2">
            <div class="input-group">
                <span class="input-group-addon simbolo">S/</span>
                <input id="deta_total" name="deta_total[]" type="text" class="form-control deta_total text-right"/>
            </div>
        </div>
        <div class="col-sm-1">
            <button type="button" class="btn btn-danger btn-sm borrarItem"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span></button>
        </div>
    </div>
</div>
