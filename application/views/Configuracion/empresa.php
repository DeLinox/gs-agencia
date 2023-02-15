<div id="page-wrapper" class="page-filter">
    <div class="page-header col-md-12">
        <div class="row page-header-title">
            <div class="col-sm-6">
                <h3><?= $titulo  ?></h3>
            </div>
        </div>
    </div>
    <div class="page-content">
        <div class="col-md-12">
            <div id="msj-success" class="alert alert-success"></div>
            <div id="msj-danger" class="alert alert-danger"></div>
            <div class="col-xs-2"> <!-- required for floating -->
                                <!-- Nav tabs -->
                <ul class="nav nav-tabs tabs-left">
                    <li class="active"><a href="#home" data-toggle="tab">Datos Generales</a></li>
                    <li><a href="#profile" data-toggle="tab">Logotipo</a></li>
                </ul>
            </div>

            <div class="col-xs-10">
                <!-- Tab panes -->
                <div class="tab-content">
                    <div class="tab-pane active" id="home">
                        <form id="frm-empresa" class="form-horizontal" method="POST" action="<?= base_url() ?>configuracion/guardar_empresa">
                            <div class="clearfix"></div>
                            <h3>Datos Generales</h3>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label col-sm-2" for="rsocial">Razón social:</label>
                                    <div class="col-sm-10">
                                        <input value="<?= $conf->conf_rsocial ?>" type="text" class="form-control input-sm" id="rsocial" placeholder="Razón social" name="rsocial">
                                    </div>
                                </div>    
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label col-sm-2" for="direccion">Dirección:</label>
                                    <div class="col-sm-10">
                                        <input value="<?= $conf->conf_direccion ?>" type="text" class="form-control input-sm" id="direccion" placeholder="Dirección" name="direccion">
                                    </div>
                                </div>    
                            </div>
                            <div class="clearfix"></div>
                            <h3>Datos de Contacto</h3>                
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label col-sm-2" for="telefonos">Teléfonos:</label>
                                    <div class="col-sm-10">
                                        <input value="<?= $conf->conf_impr_telefonos ?>" type="text" class="form-control  input-sm" id="telefonos" placeholder="Telefono" name="telefonos">
                                    </div>
                                </div>    
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label col-sm-2" for="contactos">Contactos:</label>
                                    <div class="col-sm-10">
                                        <input value="<?= $conf->conf_impr_contactos ?>" type="text" class="form-control input-sm" id="contactos" placeholder="Contactos" name="contactos">
                                    </div>
                                </div>    
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label col-sm-2" for="web">Web:</label>
                                    <div class="col-sm-10">
                                        <input value="<?= $conf->conf_impr_web ?>" type="text" class="form-control input-sm" id="web" placeholder="Página web" name="web">
                                    </div>
                                </div>    
                            </div>
                            <div class="clearfix"></div>
                            <h3>Configuración de Correo</h3>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label col-sm-2" for="correo">Correo:</label>
                                    <div class="col-sm-10">          
                                        <input value="<?= $conf->conf_mail_user ?>" type="mail" class="form-control input-sm" id="correo" placeholder="Correo electronico" name="correo">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label col-sm-2" for="password">Contraseña:</label>
                                    <div class="col-sm-10">          
                                        <input value="<?= $conf->conf_mail_password ?>" type="password" class="form-control input-sm" id="password" placeholder="Contraseña" name="password">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label col-sm-2" for="subject">Subject:</label>
                                    <div class="col-sm-10">          
                                        <input value="<?= $conf->conf_mail_subject ?>" type="text" class="form-control input-sm" id="subject" placeholder="Subject" name="subject">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label col-sm-2" for="cuerpo">Cuerpo:</label>
                                    <div class="col-sm-10">          
                                        <input value='<?= $conf->conf_mail_body ?>' type="text" class="form-control input-sm" id="cuerpo" placeholder="Cuerpo" name="cuerpo">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">        
                                    <div class="col-sm-offset-2 col-sm-10">
                                        <button type="submit" class="btn btn-success">Guardar</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="tab-pane" id="profile">
                        <form id="frm-logotipo" enctype="multipart/form-data" class="form-horizontal" method="POST" action="<?= base_url() ?>configuracion/guardar_logo">
                            <div class="clearfix"></div>
                            <h3>Logotipo</h3>
                            <div class="col-md-12" id="cambiar_logo">
                                <div class="form-group">
                                    <?= '<img src="data:image/jpeg;base64,'.base64_encode($conf->conf_impr_logo).'"/>'; ?>
                                    <br>
                                    <br>
                                    <button type="button" class="btn btn-primary btn-sm" id="btn-cambiar">Cambiar</button>
                                </div>
                            </div>
                            <div class="col-md-12" id="agregar_logo">
                                <div class="form-group">
                                    <input type="file" name="logotipo" id="logotipo">
                                    <br>

                                    <button type="button" class="btn btn-danger btn-sm" id="btn-cancelar">Cancelar</button>
                                    <div class="col-sm-offset-2 col-sm-10">
                                        <button type="submit" class="btn btn-success">Guardar</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>