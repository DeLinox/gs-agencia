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
            <form id="frm-gen-orden" method="POST" class="form-horizontal" action="<?= base_url() ?>Liquidacion/guardar_orden_pago/<?= $liqu->liqu_id ?>" method="post">
                <div class="modal-body">
                    <div class="alert alert-danger errorOrd hidden" role="alert">
                        <span class="text">Error:</span>
                    </div>
                    <input type="hidden" name="seleccionados" value="<?= $seleccionados ?>">
                    <div class="row">
                        <div class="col-sm-9">
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-xs-2">
                                        <label for="documento">Documento</label>
                                        <?= form_dropdown('documento', $documentos, $liqu->liqu_clie_tdoc_id, array('class' => 'form-control input-sm', "id" => "documento")); ?>
                                    </div>
                                    <div class="col-xs-2">
                                        <label for="documento">.</label>
                                        <input id="docnum" name="docnum" value="<?php echo $liqu->liqu_clie_doc_nro; ?>" type="text" class="form-control input-sm" placeholder="00000000000">
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
                                    <div class="col-xs-4">
                                        <label for="numero">Numero de Liquidación</label>
                                            <div class="row">
                                                <div class="col-sm-6">
                                                    <input type="text" name="numero" value="<?= $liqu->liqu_numero ?>" class="form-control input-sm text-right">   
                                                </div>
                                                <div class="col-sm-6">
                                                    <input type="text" name="clie_numero" value="<?= $liqu->liqu_clie_num ?>" class="form-control input-sm text-right">
                                                </div>
                                            </div>
                                    </div>
                                    <div class="col-xs-8">
                                        <label for="observacion">Observaciones</label>
                                        <input type="text" name="observacion" value="<?= $liqu->liqu_obs ?>" class="form-control input-sm">
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
                                <div class="col-sm-12">
                                <?php foreach ($saldos as $i => $row): $sim = ($row['moneda'] == 'SOLES')?'S/.':'$';?>
                                <div class="form-group">
                                    <input name="s_liqu_id[]" type="hidden" value="<?= $row['liqu_id'] ?>" readonly />
                                    <div class="row">
                                        <div class="col-sm-7">
                                            <input name="s_file[]" type="text" class="form-control input-sm" value="<?= $row['file'] ?>" readonly />
                                        </div>
                                        <div class="col-sm-5">
                                            <div class="input-group">
                                                <span class="input-group-addon" style="padding:0">S/.</span>
                                                <input name="s_monto[]" type="text" class="form-control input-sm text-right" value="<?= $row['monto'] ?>" />   
                                            </div>     
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-check">
                                        <input name="incluye_saldo" type="checkbox" class="form-check-input" id="anterior" <?= ($liqu->liqu_incluyesaldo == 'SI')?"checked":"" ?>>
                                        <label class="form-check-label" for="anterior">Incluir saldo anteriores</label>
                                    </div>
                                </div>
                            </div>
                        </div>  
                    </div>
                    <table class="table table-striped table-bordered table-genOrdPago">
                        <thead>
                            <tr>
                                <th>FILE</th>
                                <th>GRUPO / NOMBRE</th>
                                <th>FECHA</th>
                                <th>N° PAX</th>
                                <th>EXCURSION</th>
                                <th>PU</th>
                                <th>TOTAL</th>
                                <th class="col-sm-1">TOTAL A PAGAR</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                                $almuerzos = 0;
                                $paxs = 0;
                                foreach ($detas as $i => $val):
                                    $rowspan =  count($val->detalles);
                            ?>
                            <tr>
                                <td rowspan="<?= $rowspan ?>"><?= $val->lpaq_file ?></td>
                                <td rowspan="<?= $rowspan ?>"><?= $val->lpaq_nombre ?></td>
                                <?php 

                                foreach ($val->detalles as $i => $det): 
                                    if($rowspan > 1 && $i > 0)
                                        echo "<tr>";
                                ?>
                                    <td class="edt"><?= $det->fecha ?></td>
                                    <td class="edt"><?= $det->pax ?></td>
                                    <td class="edt"><?= $det->servicio ?></td>
                                    <td class="edt"><?= $det->precio ?></td>
                                    <td class="edt"><?= $det->total ?></td>
                                <?php
                                    if($i == 0)
                                        echo '<td rowspan="'.$rowspan.'">'.$val->lpaq_total.'</td>';
                                    if($rowspan > 1 && $i == 0)
                                        echo "</tr>";
                                endforeach;
                                ?>
                            </tr>
                            <?php endforeach ?>
                            <tr>
                                <th class="text-right" colspan="7">TOTAL</th>
                                <th> <input readonly type="text" class="total_total" name="total" value="<?= number_format($total, 2, '.', ' ') ?>">   </th>
                                <input readonly type="hidden" id="total_last" value="<?= $total ?>">
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </div>
            </form>

        </div>
    </div>
</div>
