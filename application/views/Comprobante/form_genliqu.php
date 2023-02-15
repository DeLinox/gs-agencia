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
            <form id="frm-gen-orden" method="POST" class="form-horizontal" action="<?= base_url() ?>Comprobante/guardar_liquidacion/<?= $liqu->liqu_id ?>" method="post">
                <div class="alert alert-danger errorOrd hidden" role="alert">
                    <span class="text">Error:</span>
                </div>
                <input type="hidden" name="seleccionados" value="<?= $sel ?>">
                <input name="fecha" value="<?php echo $liqu->liqu_fecha ?>" type="date" class="oculto">
                <div class="form-group">
                    <div class="row">
                        <div class="col-xs-2">
                            <label for="documento">Documento</label>
                            <?= form_dropdown('documento', $documentos, $liqu->liqu_clie_docid, array('class' => 'form-control input-sm', "id" => "documento")); ?>
                        </div>
                        <div class="col-xs-2">
                            <label for="documento">.</label>
                            <input id="docnum" name="docnum" value="<?php echo $liqu->liqu_clie_docnro; ?>" type="text" class="form-control input-sm" placeholder="00000000000">
                        </div>
                        <div class="col-xs-6">
                            <label for="rsocial">Nombre / Razón social</label>
                            <input id="rsocial" name="rsocial" type="text" value="<?= $liqu->liqu_clie_rsocial ?>" class="form-control input-sm" placeholder="Razón Social" />
                            <input id="clie_id" name="clie_id" type="hidden" value="<?= $liqu->liqu_clie_id ?>" />
                        </div>
                        <div class="col-xs-2">
                            <label for="moneda">Moneda</label>
                            <?= form_dropdown('moneda', $moneda, $liqu->liqu_moneda, array('class' => 'form-control input-sm', "id" => "moneda")); ?>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <div class="row">
                        <div class="col-xs-2">
                            <label for="numero">Número de Liquidación</label>
                            <input type="text" name="numero" value="<?= $liqu->liqu_numero ?>" class="form-control input-sm text-right">
                        </div>
                        <div class="col-xs-10">
                            <label for="observacion">Observaciones</label>
                            <input type="text" name="observacion" value="<?= $liqu->liqu_observacion ?>" class="form-control input-sm">
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <?= $table ?>
                </div>
                <input type="hidden" id="dsubtotal" value="<?= $total ?>">
                <div class="form-group">
                    <div class="row">
                        <div class="col-sm-8">
                            <div class="row">
                                <div class="col-sm-6">
                                    <a type="button" href="<?= base_url() ?>Comprobante/comp_listado/" class="btn btn-danger">Cancelar</a>
                                    <button type="submit" class="btn btn-primary">Guardar</button>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-inline">
                                      <div class="form-group pull-right">
                                        <label>Total Descuento</label>
										<input type="tetx" name="descuentos" class="form-control input-sm text-right" value="<?= $liqu->liqu_impuesto ?>">
                                      </div>
                                    
                                    </div>    
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 fact">
                            <div class="fact-total">
                                <div class="subtotal clearfix">
                                    <span>Sub Total Ventas</span>
                                    <i class="msimb">S/</i>
                                    <span class=""><input type="text" name="total_sub" id="total_sub" value="<?= $sub_total ?>" class="" decimales="2" readonly=""></span>
                                </div>
                                <div class="igv clearfix">
                                    <span>Descuentos</span>
                                    <i class="msimb">S/</i>
                                    <span class="moneda"><input type="text" name="total_igv" id="total_igv" value="<?= $desc ?>" class="vale" decimales="2" readonly=""></span>
                                </div>
                                <div class="total clearfix">
                                    <span>Importe Total</span>
                                    <i class="msimb">S/</i>
                                    <span class="moneda" id="total"><input type="text" name="total_total" id="total_total" value="<?= $total ?>" decimales="2" class="vale" readonly=""></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
