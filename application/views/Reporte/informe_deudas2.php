
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
            <form class="ocform form-inline" action="<?= base_url() ?>Reporte/repo_informeDeudas3" method="POST">
                <div class="nosel">
                <div class="form-group">
                    <input type="text" name="search" class="form-control input-sm" value="<?= $form['search'] ?>" placeholder="Buscar contacto">
                </div>
                <div class="form-group">
                    <?php echo form_dropdown('treserva', $treserv, $form['treserva'], array('class' => 'form-control input-sm')); ?>
                </div>
                <div class="form-group">
                    <?php echo form_dropdown('tcontacto', $tcont, $form['tcontacto'], array('class' => 'form-control input-sm')); ?>
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
                        <th class="text-center">COBRADO (S/)</th>
                        <th class="text-center">SALDO (S/)</th>
                        <th class="text-center">TOTAL (S/)</th>
                        <th class="text-center">COBRADO ($)</th>
                        <th class="text-center">SALDO ($)</th>
                        <th class="text-center">TOTAL ($)</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($clientes as $i => $clie): ?>
                    <tr>    
                        <td></td>
                        <td><?= $clie['cliente'] ?></td>
                        <td class='mone'><?= $clie['s_cobrado'] ?></td>
                        <td class='mone'><?= $clie['s_saldo'] ?></td>
                        <td class='mone'><?= $clie['s_total'] ?></td>
                        <td class='mone'><?= $clie['d_cobrado'] ?></td>
                        <td class='mone'><?= $clie['d_saldo'] ?></td>
                        <td class='mone'><?= $clie['d_total'] ?></td>
                        <td><?= $clie['clie_id'] ?></td>
                    </tr>
                    <?php endforeach; ?>
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