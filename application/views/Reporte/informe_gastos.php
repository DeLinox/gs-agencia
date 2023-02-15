
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
            <form class="ocform form-inline">
                <div class="nosel">
                <div class="form-group">
                    <input type="text" name="search" class="form-control input-sm" placeholder="Buscar contacto">
                </div>
				<div class="form-group">
                    <input type="hidden" name="desde" id="desde" />
                    <input type="hidden" name="hasta" id="hasta" />
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
					<button type="button" class="btn btn-success btn-sm informeGastos">
                        <i class="glyphicon glyphicon-save-file"></i>
                        Exportar
                    </button>
                </div>
                </div>
            </form>
            </br>
            <table class="table table-striped table-bordered table-hover" id="cuadro_ingresos">
                <thead>
                    <tr>
                        <th></th>
                        <th colspan="3" class="text-center">SOLES</th>
                        <th colspan="3" class="text-center">DOLARES</th>
                    </tr>
                    <tr>
                        <th class="col-sm-6">PROVEEDOR</th>
                        <th class="text-center">PAGADO</th>
                        <th class="text-center">SALDO</th>
                        <th class="text-center">TOTAL</th>
                        <th class="text-center">PAGADO</th>
                        <th class="text-center">SALDO</th>
                        <th class="text-center">TOTAL</th>
                    </tr>
                </thead>
            </table>
            <div class="panel-group">
                <div class="panel panel-info">
                  <div class="panel-heading">
                    <a data-toggle="collapse" href="#PROPIO" class="link-panel">
                        <table class="table" id="tbl-PROPIO">
                            <tr>
                                <th class="col-sm-6">PROPIO</th>
                                <th class="text-center mone col-sm-1"><span class="s_cobrado">0.00</span></th>
                                <th class="text-center mone col-sm-1"><span class="s_saldo">0.00</span></th>
                                <th class="text-center mone col-sm-1"><span class="s_total">0.00</span></th>
                                <th class="text-center mone col-sm-1"><span class="d_cobrado">0.00</span></th>
                                <th class="text-center mone col-sm-1"><span class="d_saldo">0.00</span></th>
                                <th class="text-center mone col-sm-1"><span class="d_total">0.00</span></th>
                            </tr>
                        </table>
                    </a>
                  </div>
                  <div id="PROPIO" class="panel-collapse collapse">
                </div>
                <div class="panel panel-success">
                  <div class="panel-heading">
                    <a data-toggle="collapse" href="#TERCERO" class="link-panel">
                        <table class="table" id="tbl-TERCERO">
                            <tr>
                                <th class="col-sm-6">TERCERO</th>
                                <th class="text-center mone col-sm-1"><span class="s_cobrado">0.00</span></th>
                                <th class="text-center mone col-sm-1"><span class="s_saldo">0.00</span></th>
                                <th class="text-center mone col-sm-1"><span class="s_total">0.00</span></th>
                                <th class="text-center mone col-sm-1"><span class="d_cobrado">0.00</span></th>
                                <th class="text-center mone col-sm-1"><span class="d_saldo">0.00</span></th>
                                <th class="text-center mone col-sm-1"><span class="d_total">0.00</span></th>
                            </tr>
                        </table>
                    </a>
                  </div>
                  <div id="TERCERO" class="panel-collapse collapse">
                    
                  </div>
                </div>
              </div>
            
        </div>
    </div>
</div>