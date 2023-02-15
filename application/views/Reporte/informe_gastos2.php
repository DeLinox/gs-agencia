
<div id="page-wrapper">
    <div class="page-header col-md-12">
        <div class="row page-header-title">
            <div class="col-sm-6">
                <h3 style="display: inline-block;"><?php echo $titulo; ?></h3>
            </div>
            <?php $this->load->view("Reporte/opc_reportes") ?>
        </div>
    </div>
    <div class="page-content serv">
        <div class="col-md-10 col-md-offset-1">
            <div class="alert alert-danger error hidden" role="alert">
                <span class="text">Error:</span>
            </div>
            <form class="ocform form-inline" action="<?= base_url() ?>Reporte/repo_informeGastos2" method="POST">
                <div class="nosel">
				<div class="form-group">
                    <?php echo form_dropdown('tipo', $tipo, $form['tipo'], array('class' => 'form-control input-sm')); ?>
                </div>
                <div class="form-group">
                    <?php echo form_dropdown('spropio', $spropio, $form['spropio'], array('class' => 'form-control input-sm hidden')); ?>
                </div>
                <div class="form-group">
                    <?php echo form_dropdown('stercero', $stercero, $form['stercero'], array('class' => 'form-control input-sm hidden')); ?>
                </div>
                <div class="form-group">
                    <input type="hidden" name="desde" id="desde" value="<?= $form['desde'] ?>" />
                    <input type="hidden" name="hasta" id="hasta" value="<?= $form['hasta'] ?>"/>
                    <div class="form-group">
                        <div id="reportrange" class="pull-right" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc;">
                            <i class="glyphicon glyphicon-calendar fa fa-calendar"></i>&nbsp;
                            <span></span> <b class="caret"></b>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="glyphicon glyphicon-search"></i>
                        Filtrar
                    </button>
                </div>
                </div>
            </form>
            </br>
            <table class="table table-striped table-bordered table-hover" id="cuadro_ingresos">
                <thead>
                    <tr>
                        <th></th>
						<th></th>
                        <th colspan="3" class="text-center">SOLES</th>
                        <th colspan="3" class="text-center">DOLARES</th>
                    </tr>
                    <tr>
                        <th></th>
                        <th class="col-sm-6">CONTACTO</th>
                        <th class="text-center">PAGADO (S/)</th>
                        <th class="text-center">SALDO (S/)</th>
                        <th class="text-center">TOTAL (S/)</th>
                        <th class="text-center">PAGADO ($)</th>
                        <th class="text-center">SALDO ($)</th>
                        <th class="text-center">TOTAL ($)</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(isset($detalles)): foreach ($detalles as $i => $prov): ?>
                    <tr>    
                        <td></td>
                        <td><?= $prov['proveedor'] ?></td>
                        <td class='mone'><?= $prov['s_cobrado'] ?></td>
                        <td class='mone'><?= $prov['s_saldo'] ?></td>
                        <td class='mone'><?= $prov['s_total'] ?></td>
                        <td class='mone'><?= $prov['d_cobrado'] ?></td>
                        <td class='mone'><?= $prov['d_saldo'] ?></td>
                        <td class='mone'><?= $prov['d_total'] ?></td>
                        <td><?= $prov['prov_id'] ?></td>
                    </tr>
                    <?php endforeach; endif; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="2" style="text-align:right">TOTAL:</th>
                        <th class='mone'></th>
                        <th class='mone'></th>
                        <th class='mone'></th>
                        <th class='mone'></th>
                        <th class='mone'></th>
                        <th class='mone'></th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>