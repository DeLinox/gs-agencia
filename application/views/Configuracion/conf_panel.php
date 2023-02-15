
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
            <ul class="nav nav-pills">
                <li class="active"><a data-toggle="pill" href="#home">USUARIOS</a></li>
                <li><a data-toggle="pill" href="#menu1">TARIFAS</a></li>
                <li><a data-toggle="pill" href="#menu2">TIPOS PROVEEDOR</a></li>
                <li><a data-toggle="pill" href="#menu3">TIPOS TRANSPORTE</a></li>
            </ul>

            <div class="tab-content">
                <div id="home" class="tab-pane fade in active tbl-center">
                    <div class="col-sm-12 text-right">
                        <?php if($this->editar > 1): ?>
                        <a title="Crear Usuario" class="btn btn-danger btn-sm agregar_usuario" href="<?= base_url() ?>Configuracion/conf_new_usuario"><i class="glyphicon glyphicon-plus"></i> Crear Usuario</a>
                        <?php endif ?>
                    </div>
                    <h3>Listado de Usuarios registrados</h3>

                    <table class="table table-striped table-bordered" id="tbl-usuarios">
                        <thead>
                            <tr>
                                <th rowspan="2"></th>
                                <th rowspan="2">NOMBRES</th>
                                <th rowspan="2">TELEFONO</th>
                                <th rowspan="2">CORREO</th>
                                <th rowspan="2">USUARIO</th>
                                <th rowspan="2">RESERVA</th>
								<th rowspan="2">ESTADO</th>
                                <!--<th colspan="4">PERMISOS</th>-->
                            </tr>
                            <!--
                            <tr>
                                <th>ADMI</th>
                                <th>RESE</th>
                                <th>VENT</th>
                                <th>PAGO</th>
                            </tr>
                            -->
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
                <div id="menu1" class="tab-pane fade">
                    <h3></h3>
                    <div class="col-md-8 col-md-offset-2">
                        <div class="form-inline">
                            <div class="form-group">
                                <label>Cliente: </label>
                            </div>
                            <div class="form-group" style="min-width: 300px;">
                                <input type="hidden" id="facturacion">
                                <select class="form-control" id="cmbClientes"></select>
                            </div>
                        </div>
                        <h3>TARIFAS</h3>
                        <table class="table table-striped table-bordered" id="tarifas_2">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>CLIENTE</th>
                                    <th>SERVICIO</th>
                                    <th>TARIFA</th>
                                    <th>ALMUERZO</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>    
                    </div>
                </div>
                <div id="menu2" class="tab-pane fade">
                    <h3></h3>
                    <div class="col-md-6 col-md-offset-3">
                        <div class="col-sm-12 text-right">
                            <?php if($this->editar > 1): ?>
                            <a title="Crear Tipo proveedor" class="btn btn-danger btn-sm agregar_prov" href="<?= base_url() ?>Configuracion/conf_new_tipoProv"><i class="glyphicon glyphicon-plus"></i> Crear Tipo Proveedor</a>
                            <?php endif ?>
                        </div>
                        <h3>TIPOS</h3>
                        <table class="table table-striped table-bordered" id="tipos_prov">
                            <thead>
                                <tr>
                                    <th class="col-sm-1">#</th>
                                    <th>NOMBRE TIPO</th>
                                    <th class="col-sm-2"></th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>    
                    </div>
                </div>
                <div id="menu3" class="tab-pane fade">
                    <h3></h3>
                    <div class="col-md-6 col-md-offset-3">
                        <div class="col-sm-12 text-right">
                            <?php if($this->editar > 1): ?>
                            <a title="Crear Tipo proveedor" class="btn btn-danger btn-sm agregar_trans" href="<?= base_url() ?>Configuracion/conf_new_tipoTrans"><i class="glyphicon glyphicon-plus"></i> Crear Tipo Transporte</a>
                            <?php endif ?>
                        </div>
                        <h3>TIPOS</h3>
                        <table class="table table-striped table-bordered" id="tipos_trans">
                            <thead>
                                <tr>
                                    <th class="col-sm-1">#</th>
                                    <th>NOMBRE TIPO</th>
                                    <th class="col-sm-2"></th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>    
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function(){
        $('.agregar_prov').on('click', function(){
            $(this).load_dialog({
                title: $(this).attr('title'),
                loaded: function(dlg) {
                    $(dlg).find('form').submit(function() {
                        $(dlg).find('.error').addClass('hidden')
                        $(this).formPost(true, function(data) {
                            if (data.exito == false) {
                                $(dlg).find('.error').removeClass('hidden').html(data.mensaje);
                            } else {
                                dlg.modal('hide');
                                alert(data.mensaje);
                                getTipoProv();
                            }
                        });
                        return false;
                    });
                }
            });
            return false;
        })
        $('.agregar_usuario').on('click', function(){
            $(this).load_dialog({
                title: $(this).attr('title'),
                script: baseurl + 'assets/js/Usuario/form.js',
                loaded: function(dlg) {
                    $(dlg).find('form').submit(function() {
                        $(dlg).find('.error').addClass('hidden')
                        $(this).formPost(true, function(data) {
                            if (data.exito == false) {
                                $(dlg).find('.error').removeClass('hidden').html(data.mensaje);
                            } else {
                                alert(data.mensaje);
                                dlg.modal('hide');
                                getUsuarios();
                            }
                        });
                        return false;
                    });
                }
            });
            return false;
        })
    })
</script>