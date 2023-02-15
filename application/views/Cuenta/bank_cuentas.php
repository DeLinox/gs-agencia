
<div id="page-wrapper">
    <div class="page-header col-md-12">
        <div class="row page-header-title">
            <div class="col-sm-6">
                <h3>Cuentas y Bancos</h3>
            </div>
            
        </div>
    </div>
    <div class="page-content serv">
        <div class="col-md-12">
            <div class="alert alert-danger error hidden" role="alert">
                <span class="text">Error:</span>
            </div>
            <ul class="nav nav-pills">
                <li class="active"><a data-toggle="pill" href="#home">CUENTAS</a></li>
                <li><a data-toggle="pill" href="#menu1">SUBIR PAGOS</a> </li>
            </ul>

            <div class="tab-content">
                <div id="home" class="tab-pane fade in active tbl-center">
                    <h3>Cuentas registradas</h3>
                    <div class="col-md-12">
                        <h3>TARIFAS</h3>
                        <table class="table table-striped table-bordered" id="cuentas">
                            <thead>
                                <tr>
                                    <th>CODIGO</th>
                                    <th>BANCO</th>
                                    <th>TITULAR</th>
                                    <th>NUMERO</th>
                                    <th>CCI</th>
                                    <th>MONEDA</th>
                                    <th>MONTO</th>
                                    <th>ULT. MOV.</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>    
                    </div>
                </div>
                <div id="menu1" class="tab-pane fade">

                    <h3>Seleccionar archivo para subir pagos</h3>
                    <div class="row">
                        <div class="col-md-4">
                            <?php if($this->editar > 1): ?>
                            <form id="frm-pagos" method="POST" action="<?= base_url() ?>cuenta/test_subida">
                                <div class="form-group">
                                    <input type="file" name="archivo" id="archivo">    
                                </div>
                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary">Subir pagos</button>
                                </div>
                            </form>        
                            <?php else: ?>
                            <p><strong>Usted no tiene los permisos necesarios para subir pagos</strong></p>
                            <?php endif ?>
                        </div>
                        <div class="col-md-8 respuesta-pagos"></div>
                    </div>
                    
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function(){
        
    })
</script>