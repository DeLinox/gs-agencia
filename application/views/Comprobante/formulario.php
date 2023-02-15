<script>
detas = <?= $detas ?>;
</script>
<div id="page-wrapper">
    <div class="page-header col-md-12">
        <div class="row page-header-title">
            <div class="col-sm-6">
                <h3><?php echo $titulo; ?></h3>
            </div>
        </div>
    </div>
    <div class="page-content fact">
        <div class="col-md-12">
            <input type="hidden" id="id_venta" value="<?= $venta->vent_id ?>">
            <div class="alert alert-danger error hidden" role="alert">
                <span class="text">Error:</span>
            </div>
            
            <form action="<?= base_url() ?>comprobante/guardar/<?= $venta->vent_id ?>" method="post" id="vender">
                <input type="hidden" name="seleccionados" value="<?= $this->input->get("sel") ?>">
				<input type="hidden" name="tipo" value="<?= $venta->vent_tipo ?>">
                <br>
                <div class="row">
                    <div class="col-xs-3">
                        <label for="comprobante">Comprobante</label>
                        <?= form_dropdown('comprobante', $comprobantes, $venta->vent_tcom_id, array('class' => 'form-control input-sm', "id" => "comprobante")); ?>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label for="serie">Número</label>
                            <div class="input-group">
                                <input id="serie" name="serie" type="text" value="<?= $venta->vent_serie ?>" class="form-control input-sm text-right" />
                                <span class="input-group-btn" style="width:5px;"></span>
                                <input id="numero" name="numero" type="text" value="<?= $venta->vent_numero ?>" class="form-control input-sm text-right" />
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-2">
                        <div class="form-group">
                            <label for="moneda">Moneda</label>
                            <?= form_dropdown('vent_moneda', $moneda, $venta->vent_moneda, array('class' => 'form-control input-sm', "id" => "moneda")); ?>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label for="fecha">Fecha de emision</label>
                            <div class='input-group'>
                                <input id="vent_fecha" name="vent_fecha" type="text" value="<?= $venta->vent_fecha ?>" class="form-control fecha input-sm" />
                                <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12">
                        <div class="form-group">
                            <label for="rsocial">Nombre / Razón social</label>
                            <div class="input-group">
                                <input id="rsocial" name="rsocial" type="text" value="<?= $venta->vent_clie_rsocial ?>" class="form-control input-sm" placeholder="Razón Social" />
                                <input id="clie_id" name="clie_id" type="hidden" value="<?= $venta->vent_clie_id ?>" />
                                <span class="input-group-btn">
                                    <a title="Buscar proveedor" class="btn btn-default searchprov btn-sm" href="<?php echo base_url() ?>contacto/sbuscar_clie">
                                        <span class="glyphicon glyphicon-search"></span>
                                </a>
                                <a class="btn btn-default newclient btn-sm" href="<?php echo base_url() ?>cliente/crear">
                                        <span class="glyphicon glyphicon-plus-sign"></span>
                                    </a>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <div class="row">
                                <div class="col-xs-6">
                                    <label for="documento">Documento</label>
                                    <?= form_dropdown('documento', $documentos, $venta->vent_clie_tdoc_id, array('class' => 'form-control input-sm', "id" => "documento")); ?>
                                </div>
                                <div class="col-xs-6">
                                    <label for="documento">.</label>
                                    <div class="input-group">
                                        <input id="docnum" name="docnum" value="<?php echo $venta->vent_clie_tdoc_nro; ?>" type="text" class="form-control input-sm" placeholder="00000000000">
                                        <div class="input-group-btn">
                                            <a href="#" id="completar" class="btn btn-default btn-sm"><span class="glyphicon glyphicon-eye-open"></span></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="direccion">Dirección</label>
                            <input id="direccion" name="direccion" type="text" value="<?= $venta->vent_clie_direccion ?>" class="form-control input-sm" placeholder="Dirección" />
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input id="email" name="email" type="text" value="<?= $venta->vent_clie_email ?>" class="form-control input-sm"/>
                        </div>
                    </div>
					<div class="col-sm-6">
                        <div class="form-group">
                            <label for="observaciones">Observaciones</label>
                            <input id="observaciones" name="observaciones" type="text" value="<?= $venta->vent_obs ?>" class="form-control input-sm"/>
                        </div>
                    </div>
                </div>
                <div class="fact-wrap">
                    <div class="fact-head row">
                        <div class="col-sm-1">
                            OPCIONES
                        </div>
                        <div class="col-sm-4">
                            <div class="row">
                                <div class="col-sm-5">
                                    Fecha
                                </div>
                                <div class="col-sm-7">
                                    Servicio
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-2">
                            Nombre / Grupo
                        </div>
                        <div class="col-sm-1">
                            Serv. Prec.
                        </div>
                        <div class="col-sm-1">
                            pax
                        </div>
                        <div class="col-sm-1">
                            Lunch
                        </div>
                        <div class="col-sm-1">
                            Lunch Prec.
                        </div>
                        <div class="col-sm-1">
                            Total
                        </div>
                    </div>
                    <div class="sortable clearfix">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-8">
                        <a class="btn btn-default btn-sm agregarfila" href="#">
                            <span class="glyphicon glyphicon-plus-sign" aria-hidden="true"></span> Agregar sevicio
                        </a>
                        <button type="submit" value="Guardar" class="btn btn-success btn-sm">
                            <span class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></span> Guardar
                        </button>
                    </div>
                    <div class="col-md-4">
                        <div class="fact-total">
                            <div class="subtotal clearfix">
                                <span>Sub Total Ventas</span>
                                <i class="msimb">S/.</i>
                                <span class="moneda"><input type="text" name="total_sub" id="total_sub" value="0.00" class="vale" decimales="2" readonly=""></span>
                            </div>
                            <div class="hidden">
                            <div class="gravadas clearfix">
                                <span>Gravadas</span>
                                <i class="msimb">S/.</i>
                                <span class="moneda"><input type="text" name="total_gravadas" id="total_gravadas" value="0.00" decimales="2"  class="vale" readonly=""></span>
                            </div>
                            <div class="gratuitas clearfix">
                                <span>Gratuitas</span>
                                <i class="msimb">S/.</i>
                                <span class="moneda"><input type="text" name="total_gratuitas" id="total_gratuitas" value="0.00" decimales="2"  class="vale" readonly=""></span>
                            </div>
                            <div class="exoneradas clearfix">
                                <span>Exoneradas</span>
                                <i class="msimb">S/.</i>
                                <span class="moneda"><input type="text" name="total_exoneradas" id="total_exoneradas" value="0.00" decimales="2"  class="vale" readonly=""></span>
                            </div>
                            <div class="inafectas clearfix">
                                <span>Inafectas</span>
                                <i class="msimb">S/.</i>
                                <span class="moneda"><input type="text" name="total_inafectas" id="total_inafectas" value="0.00" decimales="2"  class="vale" readonly=""></span>
                            </div>
                            <div class="exportacion clearfix">
                                <span>Exportación</span>
                                <i class="msimb">S/.</i>
                                <span class="moneda"><input type="text" name="total_exportas" id="total_exportas" value="0.00" decimales="2"  class="vale" readonly=""></span>
                            </div>
                            <div class="descuentos clearfix">
                                <span>Descuentos</span>
                                <i class="msimb">S/.</i>
                                <span class="moneda"><input type="text" name="total_descuentos" id="total_descuentos" value="0.00" decimales="2"  class="vale" readonly=""></span>
                            </div>
                            <div class="valorventa clearfix">
                                <span>Valor de Venta</span>
                                <i class="msimb">S/.</i>
                                <span class="moneda"><input type="text" name="total_valor" id="total_valor" value="0.00" class="vale" decimales="2"  readonly=""></span>
                            </div>
                            </div>
                            <div class="igv clearfix">
                                <span>IGV (18%)</span>
                                <i class="msimb">S/.</i>
                                <span class="moneda"><input type="text" name="total_igv" id="total_igv" value="0.00" class="vale" decimales="2"  readonly=""></span>
                            </div>
                            <div class="total clearfix">
                                <span>Importe Total</span>
                                <i class="msimb">S/.</i>
                                <span class="moneda" id="total"><input type="text" name="total_total" id="total_total" value="0.00" decimales="2"  class="vale" readonly=""></span>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- Elementos clonables -->
<div id="clonables" class="hide">
    <div class="fact-item">
        <div class="row">
            <input type="hidden" name="deta_id[]" value="" />
            <input type="hidden" name="deta_vent_id[]" value="" />
            <input type="hidden" name="deta_pdet_id[]" value="" />
            <div class="col-sm-1 formdrown">
                <div class="btn-group">
					<!--
                    <button type="button" class="btn btn-danger btn-sm" data-href="/delete.php?id=23" data-toggle="modal" data-target="#confirm-delete">
                        <span class="glyphicon glyphicon-trash borrarItem" aria-hidden="true"></span>
                    </button>
					-->
                    <button type="button" class="btn btn-primary btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <span class="caret"></span>
                        <span class="sr-only">Toggle Dropdown</span>
                    </button>
                    <ul class="dropdown-menu">
                        <li class="hidden"><label> Descuento</label><input type="text" style="" name="descuento[]" value="0.00" class="form-control text-right input-sm" decimales="2" disabled /></li>
                        <li>
                            <label> IGV (18%)</label>
                            <input type="text" name="igv[]" value="0.00" class="form-control text-right input-sm" decimales="2" readonly />
                        </li>
                        <li>
                            <label> Lunch</label>
                            <input type="text" id="lunch" name="lunch[]" value="0.00" class="form-control 
                        text-right input-sm" decimales="10" disabled/>
                        </li>
                        <li>
                            <label> Adicion</label>
                            <textarea id="adicion" name="adicion[]" class="form-control input-sm" disabled rows="2"></textarea>
                        </li>
                        <li>
                            <label> Adicion monto</label>
                            <input type="text" id="adicion_val" name="adicion_val[]" value="0.00" class="form-control text-right input-sm" decimales="10" disabled/>
                        </li>
                        <li>
                            <label> Descuento</label>
                            <textarea id="descuento" name="descuento[]" class="form-control input-sm" disabled rows="2"></textarea>
                        </li>
                        <li>
                            <label> Descuento val</label>
                            <input type="text" id="descuento_val" name="descuento_val[]" value="0.00" class="form-control 
                        text-right input-sm" decimales="10" disabled/>
                        </li>

                    </ul>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="row">
                    <div class="col-sm-5">
                        <div class="input-group">
                            <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                            <input id="fecha" name="fecha[]" type="text" class="form-control fecha input-sm">
                        </div>
                    </div>
                    <div class="col-sm-7">
                        <select name="servicio[]" class="form-control deta_servicio" id="servicio"></select>
						<input type="hidden" name="servicio_nombre[]">
                    </div>
                    
                </div>
            </div>
            <div class="col-sm-3">
                <textarea rows="1" type="text" name="nombre_grupo[]" class="form-control deta_nombre_grupo input-sm" disabled></textarea>
            </div>
            <div class="col-sm-1">
                <input id="serv_prec" name="serv_prec[]" value="0.00" decimales="10" type="text" class="form-control input-sm">
				<input id="fprecio" name="fprecio[]" type="hidden">
            </div>
            <div class="col-sm-1">
                <input id="pax" name="pax[]" type="text" class="form-control input-sm">
				<input id="file" name="file[]" type="hidden">
            </div>
            <div class="col-sm-1">
                <input id="lunch_efect" name="lunch_efect[]" type="text" class="form-control input-sm">
            </div>
            <div class="col-sm-1">
                <input id="lunch_prec" value="0.00" decimales="10" name="lunch_prec[]" type="text" class="form-control input-sm">
            </div>
            <div class="col-sm-1">
                <input id="valor" value="0.00" decimales="10" name="valor[]" type="text" class="form-control input-sm">
                <input type="hidden" id="total" value="0.00" decimales="10" name="total[]">
            </div>
        </div>
    </div>
</div>
<!-- Fin de elementos clonables -->
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
        $this.parents('.fact-item').fadeOut('slow', function() {
            $this.parents('.fact-item').remove();
            updateTotal();
        });
        $dlg.modal('hide')
    });
});
</script>