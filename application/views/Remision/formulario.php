<script>
    productos = <?= $productos ?>;
</script>
<style type="text/css">
    .trl{padding-left: 0;padding-right: 0;}
</style>
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

            <input type="hidden" id="id_remi" value="<?= $id_remi ?>">
            

            <div class="alert alert-danger error hidden" role="alert">
                <span class="text">Error:</span>
            </div>
            <input type="hidden" id="cta" value="<?php echo $this->configuracion->conf_cta; ?>">
            <form action="<?= base_url() ?>remision/guardar/<?= $id_remi ?>" method="post" id="vender">
                <br>
                
                <div class="row">
                    
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="rsocial">Nombre / Razón social</label>
                            <div class="input-group">
                                <input id="rsocial" name="rsocial" type="text" value="<?= $remi->remi_clie_rsocial ?>" class="form-control input-sm" placeholder="Razón Social" />
                                <input id="clie_id" name="clie_id" type="hidden" value="<?= $remi->remi_clie_id ?>"/>
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
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label for="serie">Número</label>
                            <div class="input-group">
                                <input id="serie" name="serie" type="text" value="<?= $remi->remi_serie ?>" class="form-control input-sm text-right"  />
                                <span class="input-group-btn" style="width:5px;"></span>
                                <input id="numero" name="numero" type="text" value="<?= $remi->remi_numero ?>" class="form-control input-sm text-right"  />
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label for="fecha">Fecha de emision</label>
                            <div class='input-group'>
                                <input id="fecha" name="fecha" type="text" value="<?= $remi->remi_fecha ?>" class="form-control fecha input-sm"  />
                                <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <div class="row">
                                <div class="col-xs-6">
                                    <label for="documento">Documento</label>
                                    <?= form_dropdown('documento', $documentos, $remi->remi_clie_docu_id, array('class' => 'form-control input-sm', "id" => "documento")); ?>
                                </div>
                                <div class="col-xs-6">
                                    <label for="documento">.</label>
                                    <div class="input-group">
                                        <input id="docnum" name="docnum" value="<?php echo $remi->remi_clie_num_documento; ?>" type="text" class="form-control input-sm" placeholder="00000000000">
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
                            <label for="correo1">.</label>
                            <div class="formdrown">
                                <div class="btn-group" style="margin-right: 20px;">
                                    <button type="button" class="btn btn-primary btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <span class="glyphicon glyphicon-cog" aria-hidden="true"></span> Opciones Adicionales
                                        <span class="caret"></span>
                                        <span class="sr-only">Toggle Dropdown</span>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><label><input name="exterior" id="exterior" type="checkbox" value="<?= ($remi->remi_exterior == 'SI') ? 1 : 2 ?>" <?php if ($remi->remi_exterior == 'SI') echo "checked" ?> disabled="disabled"> Exportación</label></li>
                                        <li><label for="direccion">Descripción</label><br><textarea id="descripcion" name="descripcion" class="form-control"><?= $remi->remi_descripcion ?></textarea></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="correo1">E-Mail</label>
                            <input id="email" name="email" type="text" value="<?= $remi->remi_clie_email ?>" class="form-control input-sm" placeholder="usuario@dominio.com" />
                        </div>  
                    </div>
                    
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="correo1">.</label>
                            <div class="formdrown">
                                <div class="btn-group" style="margin-right: 20px;">
                                    <button type="button" class="btn btn-primary btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <span class="glyphicon glyphicon-cog" aria-hidden="true"></span> Opciones Adicionales
                                        <span class="caret"></span>
                                        <span class="sr-only">Toggle Dropdown</span>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><label><input name="exterior" id="exterior" type="checkbox" value="<?= ($remi->remi_exterior == 'SI') ? 1 : 2 ?>" <?php if ($remi->remi_exterior == 'SI') echo "checked" ?> disabled="disabled"> Exportación</label></li>
                                        <li class="hidden"><label><input name="genera_archivo" id="genera_archivo" type="checkbox" value=""> Generar Comprobante</label></li>
                                        <li><label for="direccion">Descripción</label><br><textarea id="descripcion" name="descripcion" class="form-control"><?= $remi->remi_descripcion ?></textarea></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                -->
                <div class="row">
                    <div class="col-sm-12 sub-title">
                        <h4>Datos de traslado</h4>    
                    </div>
                    <div class="col-sm-3">
                         <label for="motivo_tras">Motivo de traslado</label>
                        <?= form_dropdown('motivo_tras', $motivo_tras, '1', array('class' => 'form-control input-sm', "id" => "documento")); ?>
                    </div>
                    <div class="col-sm-3">
                        <label for="peso_bruto">Peso bruto (KGM)</label>
                        <input type="text" name="peso_bruto" id="peso_bruto" value="<?= $remi->remi_peso_bruto ?>" class="form-control">
                    </div>
                    <div class="col-sm-3">
                        <label for="num_bultos">Número de bultos</label>
                        <input type="text" name="num_bultos" id="num_bultos" value="<?= $remi->remi_bultos ?>" class="form-control">
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label for="fecha_tras">Fecha inicio traslado</label>
                            <div class='input-group'>
                                <input id="fecha_tras" name="fecha_tras" type="text" value="<?= $remi->remi_fecha_initras ?>" class="form-control fecha input-sm"  />
                                <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-6 trl">
                        <div class="col-sm-12 sub-title">
                            <h4>Transportista</h4>    
                        </div>
                        <input type="hidden" id="id_transportista" value="<?= $remi->remi_id_trans ?>">
                        <input type="hidden" id="id_conductor" value="<?= $remi->remi_id_conduc ?>">
                        
                        <div class="col-sm-6">
                            <label for="inicio">Denominación</label>
                            <select name="trans_id"  class="form-control input-sm transportista" id="trans_id">
                            </select>
                        </div>

                        <div class="hidden">
                            <input type="text" name="docnum_trans" id="docnum_trans" value="<?= $remi->remi_docnum_trans ?>">
                            <input type="text" name="doc_trans" id="doc_trans" value="<?= $remi->remi_doc_trans ?>">
                            <input type="text" name="denom_trans" id="denom_trans" value="<?= $remi->remi_denom_trans ?>">
                        </div>
                        <div class="col-sm-6">
                            <label for="placa_trans">Transportista placa</label>
                            <input type="text" name="placa_trans" id="placa_trans" value="<?= $remi->remi_placa_trans ?>" class="form-control" disabled="disabled">
                        </div>
                    </div>
                    <div class="col-sm-6 trl">
                        <div class="col-sm-12 sub-title">
                            <h4>Conductor</h4>    
                        </div>
                        <div class="col-sm-12">
                            <label for="inicio">Nombre</label>
                            <select name="cond_id" class="form-control input-sm conductor" id="cond_id">
                            </select>
                        </div>
                        <div class="hidden">
                            <input type="text" name="doc_conduc" id="doc_conduc" value="<?= $remi->remi_doc_conduc ?>">
                            <input type="text" name="docnum_conduc" id="docnum_conduc" value="<?= $remi->remi_docnum_conduc ?>">
                            <input type="text" name="nombre_conduc" id="nombre_conduc" value="<?= $remi->remi_nombre_conduc ?>">
                        </div>
                    </div>
                </div>
                <div class="row">
                    
                </div>
                <div class="row">
                    <div class="col-sm-6 trl">
                        <div class="col-sm-12 sub-title">
                            <h4>Punto de partida</h4>    
                        </div>
                        <input type="hidden" name="deta_inicio" id="deta_inicio" value="<?= $remi->remi_deta_inicio ?>">
                        <input type="hidden" name="deta_fin" id="deta_fin" value="<?= $remi->remi_deta_fin ?>">
                        <input type="hidden" id="id_inicio" value="<?= $remi->remi_inicio ?>">
                        <input type="hidden" id="id_fin" value="<?= $remi->remi_fin ?>">
                        <div class="col-sm-6">
                            <label for="inicio">UBIGEO</label>
                            <select name="inicio" class="form-control input-sm ubigeo" id="inicio">
                            </select>
                        </div>
                        <div class="col-sm-6">
                            <label for="dir_ini">Dirección</label>
                            <input type="text" name="dir_ini" id="dir_ini" value="<?= $remi->remi_dir_ini ?>" class="form-control">
                        </div>
                    </div>
                    <div class="col-sm-6 trl">
                        <div class="col-sm-12 sub-title">
                            <h4>Punto de llegada</h4>    
                        </div>
                        <div class="col-sm-6">
                            <label for="fin">UBIGEO</label>
                            <select name="fin" class="form-control input-sm ubigeo" id="fin">
                            </select>
                        </div>
                        <div class="col-sm-6">
                            <label for="dir_fin">Dirección</label>
                            <input type="text" name="dir_fin" id="dir_fin" value="<?= $remi->remi_dir_fin ?>" class="form-control">
                        </div>
                    </div>
                </div>

                <div class="fact-wrap">
                    <div class="fact-head row">
                        <div class="col-sm-1">
                            OPCIONES
                        </div>
                        <div class="col-sm-8">
                            PRODUCTO / SERVICIO
                        </div>
                        <div class="col-sm-3">
                            <div class="row">
                                <div class="col-sm-4">
                                    Unid.
                                </div>
                                <div class="col-sm-4">
                                    Cod.
                                </div>
                                <div class="col-sm-4">
                                    CANT.
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
                </div>
            </div>

            <div class="col-sm-3">
                <div class="">
                    <select name="producto[]" class="form-control deta_producto" id="producto">
                    </select>
                </div>
            </div>

            <div class="col-sm-5">
                <textarea style="height:25px" type="text" name="detalle[]" class="form-control deta_detalle input-sm" disabled></textarea>
            </div>

            <div class="col-sm-3">
                <div class="row">
                    <div class="col-sm-4">
                        <input type="text" name="unidad[]" value="" class="form-control input-sm" disabled />
                    </div>
                    
                    <div class="col-sm-4">
                        <input type="text" name="codigo[]" value="" class="form-control input-sm" disabled />
                    </div>
                    
                    <div class="col-sm-4">
                        <input type="text" name="cantidad[]" value="1" class="form-control input-sm text-right" decimales="10" disabled/>
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