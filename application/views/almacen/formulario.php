<script>
	productos = <?= $productos ?>
</script>
<div id="page-wrapper">
    <div class="page-header col-md-12">
        <div class="row page-header-title">
            <div class="col-sm-12">
                <h3><?php echo $titulo; ?></h3>
            </div>
        </div>
    </div>
    <div class="page-content fact">
        <div class="col-md-12">
            <input type="hidden" id="id_movi" value="<?= $id_movi ?>">

            <div class="alert alert-danger error hidden" role="alert">
                <span class="text">Error:</span>
            </div>
            <input type="hidden" id="cta" value="<?php echo $this->configuracion->conf_cta; ?>">
            <form action="<?= base_url() ?>Almacen/guardar/<?= $id_movi ?>" method="post" id="vender">
                <br>
                <input type="hidden" name="sopre" value="<?= $id ?>">
                <input type="hidden" id="movi_clase" name="movi_clase" value="<?= $movi_clase ?>">
                <input type="hidden" name="from" value="<?= (isset($sopre) ? '1' : '0') ?>">
                <div class="row">
                    <div class="col-sm-2">
                        <div class="form-group">
                            <label for="comprobante">Tipo de <?php echo $peq_titulo; ?></label>
                            <?= form_dropdown('tipo_mov', $tipo_mov, $movi->movi_tipo_id, array('class' => 'form-control input-sm', "id" => "tipo_mov")); ?>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label for="serie">Número</label>
                            <div class="input-group">
                                <input id="serie" name="serie" type="text" value="<?= $movi->movi_serie ?>" class="form-control input-sm text-right" readonly="readonly" />
                                <span class="input-group-btn" style="width:5px;"></span>
                                <input id="numero" name="numero" type="text" value="<?= $movi->movi_numero ?>" class="form-control input-sm text-right" readonly="readonly" />
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-2">
                        <div class="form-group">
                            <label for="comprobante">Sucursal</label>
                            <?= form_dropdown('sucursal', $sucursal, $movi->movi_sucu_id, array('class' => 'form-control input-sm', "id" => "sucursal")); ?>
                        </div>
                    </div>
                    <?php if($movi_clase != 3): ?>
                    <div class="col-sm-2">
                        <div class="form-group">
                            <label for="comprobante">Moneda</label>
							<?= form_dropdown('t_moneda', $moneda, $movi->movi_moneda, array('class' => 'form-control input-sm', "id" => "t_moneda")); ?>
                        </div>
                    </div>
                    <?php else: ?>
                    <div class="col-sm-2">
                        <div class="form-group">
                            <label for="comprobante">Sucursar traslado</label>
                            <?= form_dropdown('sucursalt', $sucursal, $movi->movi_sucu_id_t, array('class' => 'form-control input-sm', "id" => "sucursalt")); ?>
                        </div>
                    </div>
                    <?php endif; ?>
                    <div class="col-sm-2">
                        <div class="form-group">
                            <label for="fecha">Fecha de emision</label>
                            <input id="fecha" name="fecha" type="text" value="<?= $movi->movi_fecha ?>" class="form-control fecha input-sm text-right"  />
                        </div>
                    </div>
                    

                </div>
                <div class="<?= $movi_clase != 3?'':'oculto' ?>">
                    <div class="row">
                    <div class="col-sm-2">
                        <div class="form-group">
                            <label for="comprobante">Comprobante</label>
                            <?= form_dropdown('comprobante', $comprobantes, $movi->movi_comp_id, array('class' => 'form-control input-sm', "id" => "comprobante")); ?>
                        </div>
                    </div>

                    <div class="col-sm-2">
                        <div class="form-group">
                            <label for="correo1">Serie</label>
                            <input id="comp_serie" name="comp_serie" type="text" value="<?= $movi->movi_comp_serie ?>" class="form-control input-sm text-right" placeholder="X0001" />
                        </div>  
                    </div>
                    <div class="col-sm-2">
                        <div class="form-group">
                            <label for="correo1">Numero</label>
                            <input id="comp_numero" name="comp_numero" type="text" value="<?= $movi->movi_comp_numero ?>" class="form-control input-sm" placeholder="00000000" />
                        </div>  
                    </div>
                    <div class="col-sm-6 hidden">
                        <div class="form-group">
                            <label for="correo1">E-Mail</label>
                            <input id="email" name="email" type="text" value="<?= $movi->movi_prov_email ?>" class="form-control input-sm" placeholder="usuario@dominio.com" />
                        </div>  
                    </div>
                </div>
                <div class="form-group">
                    <label for="serie">Razón social</label>
                    <input type="hidden" id="cliente" name="cliente" type="text" value="<?= $movi->movi_prov_id ?>"/>
                    <div class="input-group">
                        <input id="rsocial" name="rsocial" type="text" value="<?= $movi->movi_prov_rsocial ?>" class="form-control input-sm" placeholder="Razón Social" />
                        <span class="input-group-btn">
                            <a class="btn btn-default searchclient btn-sm" href="<?php echo base_url() ?>cliente/buscar_v">
                                <span class="glyphicon glyphicon-search"></span>
                            </a>
                            <a class="btn btn-default newclient btn-sm" href="<?php echo base_url() ?>cliente/crear">
                                <span class="glyphicon glyphicon-plus-sign"></span>
                            </a>
                        </span>
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">

                            <div class="row">
                                <div class="col-xs-6">
                                    <label for="documento">Documento</label>
									<?= form_dropdown('documento', $documentos, $movi->movi_prov_docu_id, array('class' => 'form-control input-sm', "id" => "documento")); ?>
                                </div>
                                <div class="col-xs-6">
                                    <label for="documento">.</label>
                                    <div class="input-group">
                                        <input id="docnum" name="docnum" value="<?php echo $movi->movi_prov_num_documento; ?>" type="text" class="form-control input-sm" placeholder="00000000000">
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
                            <input id="direccion" name="direccion" type="text" value="<?= $movi->movi_prov_direccion ?>" class="form-control input-sm" placeholder="Dirección" />
                        </div>
                    </div>
                </div>

                
                </div>
                <div class="fact-wrap">
                    <div class="fact-head row">
                        <div class="col-sm-1">
                            OPCIONES
                        </div>
                        <div class="col-sm-6">
                            PRODUCTO / SERVICIO
                        </div>
                        <div class="col-sm-5">
                            <div class="row">
                                <div class="col-sm-3">
                                    CANT.
                                </div>
                                <div class="col-sm-3">
                                    <?= $denom ?>
                                </div>
                                <div class="col-sm-3">
                                    Unid.
                                </div>
                                <div class="col-sm-3">
                                    IMPORTE
                                </div> 
                            </div>
                        </div>

                    </div>
                    <div class="sortable clearfix">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-8">

                        <a class="btn btn-default btn-sm agregarfila" href="#">
                            <span class="glyphicon glyphicon-plus-sign" aria-hidden="true"></span> Agregar producto
                        </a>
                        <button type="submit" value="Guardar" class="btn btn-success btn-sm" >
                            <span class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></span> Guardar
                        </button>

                    </div>
                    <div class="col-md-4">
                        <div class="fact-total">
                            <div class="subtotal clearfix">
                                <span>Sub Total movis</span>
                                <i class="msimb">S/.</i>
                                <span class="moneda"><input type="text" name="total_sub" id="total_sub" value="0.00" class="vale" decimales="2" readonly=""></span>
                            </div>
                            <div class="descuentos clearfix">
                                <span>Gravadas</span>
                                <i class="msimb">S/.</i>
                                <span class="moneda"><input type="text" name="total_gravadas" id="total_gravadas" value="0.00" decimales="2"  class="vale" readonly=""></span>
                            </div>
                            <div class="descuentos clearfix">
                                <span>Gratuitas</span>
                                <i class="msimb">S/.</i>
                                <span class="moneda"><input type="text" name="total_gratuitas" id="total_gratuitas" value="0.00" decimales="2"  class="vale" readonly=""></span>
                            </div>
                            <div class="descuentos clearfix">
                                <span>Exoneradas</span>
                                <i class="msimb">S/.</i>
                                <span class="moneda"><input type="text" name="total_exoneradas" id="total_exoneradas" value="0.00" decimales="2"  class="vale" readonly=""></span>
                            </div>
                            <div class="descuentos clearfix">
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
                            <div class="valormovi clearfix">
                                <span>Valor de movi</span>
                                <i class="msimb">S/.</i>
                                <span class="moneda"><input type="text" name="total_valor" id="total_valor" value="0.00" class="vale" decimales="2"  readonly=""></span>
                            </div>
                            <div class="igv clearfix">
                                <span>IGV</span>
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
            <input type="hidden" name="moneda[]" value="" />
            <div class="col-sm-1 formdrown">
                <div class="btn-group">
                    <button type="button" class="btn btn-danger btn-sm" data-href="/delete.php?id=23" data-toggle="modal" data-target="#confirm-delete">
                        <span class="glyphicon glyphicon-trash borrarItem" aria-hidden="true"></span>
                    </button>
                    <button type="button" class="btn btn-primary btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <span class="caret"></span>
                        <span class="sr-only">Toggle Dropdown</span>
                    </button>
                    <ul class="dropdown-menu">
                        <li><label> Descuento</label><input type="text" style="" name="descuento[]" value="0.00" class="form-control text-right input-sm" decimales="2" disabled /></li>
                        <li><label> Gratuita</label><?= form_dropdown('gratuita[]', $gratuita_select, 'NO', array('class' => 'form-control input-sm', "id" => "gratuita", 'disabled' => '')); ?></li>
                        <li><label> IGV</label><input type="text" name="igv[]" value="0.00" class="form-control text-right input-sm" decimales="2" readonly /></li>
                        <li><label> Valor</label><input type="text" id="valor" name="valor[]" value="0.00" class="form-control text-right input-sm" decimales="10" disabled/></li>
                        <li><label> Tipo</label><?= form_dropdown('tipo[]', $tipo_detalle, '10', array('class' => 'form-control input-sm', "id" => "tipo", 'disabled' => '')); ?></li>
                    </ul>
                </div>
            </div>

            <div class="col-sm-6">
                <div class="input-group">
                    <select name="producto[]" class="form-control deta_producto" id="producto">
                    </select>
                    <span class="input-group-btn">
                        <a class="btn btn-default crearproducto btn-sm" href="<?php echo base_url() ?>productos/crear">
                            <span class="glyphicon glyphicon-plus-sign"></span>
                        </a>
                    </span>
                </div>
            </div>

            <div class="col-sm-3 hidden">
                <textarea style="height:25px" type="text" name="detalle[]" class="form-control deta_detalle input-sm" disabled></textarea>
            </div>

            <div class="col-sm-5">
                <div class="row">
                    <div class="col-sm-3">
                        <input type="text" name="cantidad[]" value="1" class="form-control input-sm text-right" decimales="10" disabled/>
                    </div>
                    <div class="col-sm-3">
                        <div class="input-group">
                            <span class="input-group-addon msimb">S/.</span>
                            <input type="text" id="precio" name="precio[]" value="0.00" class="form-control input-sm text-right" decimales="2" disabled/>
                        </div>

                    </div>
                    <div class="col-sm-3">
                        <?= form_dropdown('unidad[]', $cmb_unidad, '',array('class' => 'form-control input-sm', "id" => "unidad", 'disabled' => '', 'readonly' => '')); ?>
                    </div>
                    <div class="col-sm-3">
                        <div class="input-group">
                            <span class="input-group-addon msimb">S/.</span>
                            <input type="text" name="importe[]" value="0.00" class="form-control input-sm text-right" decimales="2" readonly />
                        </div>
                    </div>
                </div>
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
	$('#confirm-delete').on('show.bs.modal', function (e) {
		$this = $(e.relatedTarget);
		$dlg = $(e.delegateTarget);
		$(this).find('.btn-ok').on('click', function () {
			$this.parents('.fact-item').fadeOut('slow', function () {
				$this.parents('.fact-item').remove();
				updateTotal();
			});
			$dlg.modal('hide')
		});
	});
</script>