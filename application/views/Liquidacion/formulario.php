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
            <input type="hidden" id="id_liqu" value="<?= $liquidacion->liqu_id ?>">
            <div class="alert alert-danger error hidden" role="alert">
                <span class="text">Error:</span>
            </div>
            
            <form action="<?= base_url() ?>liquidacion/guardar/<?= $liquidacion->liqu_id ?>" method="post" id="vender">
                <input type="hidden" name="seleccionados" value="<?= $this->input->get("sel") ?>">
                <br>

                <div class="row">
                    <div class="col-sm-9">
                        <div class="form-group">
                            <div class="row">
                                <div class="col-xs-2">
                                    <label for="documento">Documento</label>
                                    <?= form_dropdown('documento', $documentos, $liquidacion->liqu_clie_tdoc_id, array('class' => 'form-control input-sm', "id" => "documento")); ?>
                                </div>
                                <div class="col-xs-2">
                                    <label for="documento">.</label>
                                    <input id="docnum" name="docnum" value="<?php echo $liquidacion->liqu_clie_doc_nro; ?>" type="text" class="form-control input-sm" placeholder="00000000000">
                                </div>
                                <div class="col-xs-6">
                                    <label for="rsocial">Nombre / Razón social</label>
                                    <input id="rsocial" name="rsocial" type="text" value="<?= $liquidacion->liqu_clie_rsocial ?>" class="form-control input-sm" placeholder="Razón Social" readonly />
                                    <input id="clie_id" name="clie_id" type="hidden" value="<?= $liquidacion->liqu_clie_id ?>" />
                                </div>
                                <div class="col-xs-2">
                                    <label for="moneda">Moneda</label>
                                    <?= form_dropdown('moneda', $moneda, $liquidacion->liqu_moneda, array('class' => 'form-control input-sm', "id" => "moneda")); ?>
                                </div>
                                
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-xs-4">
                                    <label for="numero">Numero de Liquidación</label>
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <input type="text" name="numero" value="<?= $liquidacion->liqu_numero ?>" class="form-control input-sm text-right" readonly>        
                                        </div>
                                        <div class="col-sm-6">
                                            <input type="text" name="clie_numero" value="<?= $liquidacion->liqu_clie_num ?>" class="form-control input-sm text-right" readonly>        
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xs-8">
                                    <label for="observacion">Observaciones</label>
                                    <input type="text" name="observacion" value="<?= $liquidacion->liqu_obs ?>" class="form-control input-sm">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="row">
                            <div class="col-sm-7">
                                <label for="observacion">Saldo anterior</label>
                            </div>
                            <div class="col-sm-5">
                                <label for="observacion">Monto</label>
                            </div>
                        </div>
                        <div class="saldos">
                            <?php foreach ($saldos as $i => $row): $sim = ($row['moneda'] == 'SOLES')?'S/.':'$';?>
                            <div class="form-group">
                                <input name="s_liqu_id[]" type="hidden" class="form-control" value="<?= $row['liqu_id'] ?>" readonly />
                                <div class="row">
                                    <div class="col-sm-7">
                                        <input name="s_file[]" type="text" class="form-control" value="<?= $row['file'] ?>" readonly />
                                    </div>
                                    <div class="col-sm-5">
                                        <div class="input-group">
                                            <span class="input-group-addon" style="padding:0">S/.</span>
                                            <input name="s_monto[]" type="text" class="form-control text-right" value="<?= $row['monto'] ?>" readonly />   
                                        </div>     
                                    </div>
                                </div>
                            </div>
                            <?php endforeach ?>
                        </div>
						<div class="row">
                            <div class="form-check">
                                <input name="incluye_saldo" type="checkbox" class="form-check-input" id="anterior" <?= ($liquidacion->liqu_incluyesaldo == 'SI')?"checked":"" ?>>
                                <label class="form-check-label" for="anterior">Incluir saldo anteriores</label>
                            </div>
                        </div>
                    </div>  
                </div>

                <div class="fact-wrap">
                    <div class="fact-head row">
                        <div class="col-sm-4">
                            <div class="row">
                                <div class="col-sm-2">
                                    OPCIONES
                                </div>
                                <div class="col-sm-7">
                                    Excursion
                                </div>
                                <div class="col-sm-3">
                                    N° Pax
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-8">
                            <div class="row">
                                <div class="col-sm-2">
                                    Fecha
                                </div>
                                <div class="col-sm-3">
                                    Nombre
                                </div>
                                <div class="col-sm-7">
                                    <div class="row">
                                        <div class="col-sm-3">
                                            Precio
                                        </div>
                                        <div class="col-sm-3">
                                            Lunch
                                        </div>
                                        <div class="col-sm-3">
                                            P Lunch
                                        </div>
                                        <div class="col-sm-3">
                                            Importe
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="sortable clearfix">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-8">
                        <a class="btn btn-default btn-sm agregarfila hidden" href="#">
                            <span class="glyphicon glyphicon-plus-sign" aria-hidden="true"></span> Agregar sevicio
                        </a>
                        <button type="submit" value="Guardar" class="btn btn-success btn-sm">
                            <span class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></span> Guardar
                        </button>
                        <a class="btn btn-danger btn-sm" href="<?= base_url() ?>Registro/reg_auxiliar">
                            <span class="glyphicon glyphicon-remove" aria-hidden="true"></span> Cancelar
                        </a>
                    </div>
                    <div class="col-md-4">
                        <div class="fact-total">
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
            <input type="hidden" name="pdet_id[]" value="" />
            <div class="col-sm-4">
                <div class="row">
                    <div class="col-sm-3 formdrown">
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
                                <li>
                                    <label> Guia</label>
                                    <input type="text" name="guia[]" value="" class="form-control input-sm" disabled/>
                                </li>
                                <li>
                                    <label> Hotel</label>
                                    <input type="text" id="hotel" name="hotel[]" value="" class="form-control input-sm" disabled/>
                                </li>
                                <li>
                                    <label> Lunch original</label>
                                    <input type="text" id="lunch" name="lunch[]" value="" class="form-control input-sm" disabled/>
                                </li>
                                <li class="oculto">
                                    <label> Cobrado al PAX</label>
                                    <input type="text" id="cobrado_pax" name="cobrado_pax[]" value="" class="form-control input-sm" disabled/>
                                </li>
								<!--
                                <li>
                                    <label> Adic / Desc</label>
                                    <input type="text" id="adides" name="adides[]" value="" class="form-control input-sm" disabled/>
                                </li>
								-->
								<input type="hidden" id="tipo" name="tipo[]" value="" class="form-control input-sm" disabled/>
								<li>
                                    <label> Adiciones</label>
                                    <input type="text" id="adicion" name="adicion[]" value="" class="form-control input-sm" disabled/>
                                </li>
								<li>
                                    <label> Adiciones val</label>
                                    <input type="text" id="adicion_val" name="adicion_val[]" value="" class="form-control input-sm" disabled/>
                                </li>
								<li>
                                    <label> Descuento</label>
                                    <input type="text" id="descuento" name="descuento[]" value="" class="form-control input-sm" disabled/>
                                </li>
								<li>
                                    <label> Descuento val</label>
                                    <input type="text" id="descuento_val" name="descuento_val[]" value="" class="form-control input-sm" disabled/>
                                </li>
								<input type="hidden" id="tipo" name="tipo[]" value="" class="form-control input-sm" disabled/>
                            </ul>
                        </div>
                    </div>

                    <div class="col-sm-6">
                        <input type="hidden" name="serv_id[]" class="serv_id">
                        <input type="hidden" name="serv_name[]" class="serv_name">
                        <select name="servicio[]" class="form-control deta_servicio" id="servicio"></select>
                    </div>
                    <div class="col-sm-3">
                        <input type="" name="pax[]" value="1" class="form-control input-sm text-right" disabled/>
                    </div>
                </div>
            </div>

            <div class="col-sm-8">
                <div class="row">
                    <div class="col-sm-2">
                        <div class='input-group'>
                            <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                            <input id="fecha" name="fecha[]" type="text" class="form-control fecha input-sm" disabled />
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <input type="text" name="nombre[]" value="" class="form-control input-sm" disabled/>
                    </div>
                    <div class="col-sm-7">
                        <div class="row">
                            <div class="col-sm-3">
                                <div class="input-group">
                                    <span class="input-group-addon msimb">S/.</span>
                                    <input type="text" id="serv_prec" name="serv_prec[]" value="0.00" class="form-control input-sm text-right" decimales="2" disabled/>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <input type="text" name="lunch_efect[]" value="0" class="form-control input-sm text-right" disabled />
                            </div>
                            <div class="col-sm-3">
                                <div class="input-group">
                                    <span class="input-group-addon msimb">S/.</span>
                                    <input type="text" id="lunch_prec" name="lunch_prec[]" value="0.00" class="form-control input-sm text-right" decimales="2" disabled/>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="input-group">
                                    <span class="input-group-addon msimb">S/.</span>
                                    <input type="text" id="total" name="total[]" value="0.00" class="form-control input-sm text-right" decimales="2" disabled/>
                                </div>
                            </div>
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