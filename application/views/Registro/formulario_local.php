<style type="text/css">
    .total { font-size: 22px; float: right; padding-right: 15px}
    .total input { width: 100px; text-align: right; border: 0; }
    .form-check{ position: absolute; bottom: 15px; }
</style>
<script>
    detas = <?= $detas ?>;
</script>
<div class="modal-dialog modal-local" id="mdl-local" role="document">
    <div class="modal-content">
        <form id="frm-gen_orden" class="form-horizontal" action="<?=base_url()?>Registro/paq_guardar/<?= $paqu_id ?>" method="post">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="gridSystemModalLabel"><?= $titulo ?></h4>
            </div>
            <div class="modal-body">
                <input type="hidden" name="paqu_id" value="">
                <div class="alert alert-danger errorOrd hidden" role="alert">
                    <span class="text">Error:</span>
                </div>
                <div class="row">
                    <div class="col-sm-8">
                        <div class="form-group">
                            <div class="col-sm-5">
                                <label for="rsocial">Contacto</label>
                                <div class="input-group">
                                    <input type="hidden" id="clie_id" value="<?= $paquete->paqu_clie_id ?>">
                                    <input type="hidden" name="clie_rsocial" id="clie_rsocial" value="<?= $paquete->paqu_clie_rsocial ?>">
                                    <input type="hidden" name="clie_abrev" id="clie_codigo" value="<?= $paquete->paqu_clie_codigo ?>">
                                    <select id="cliente_local" name="cliente" class="form-control input-sm" ></select>
                                    
                                    <span class="input-group-btn">
                                    <button type="button" class="btn btn-default newclient btn-sm" href="<?php echo base_url() ?>contacto/crear_clie" title="Crear nuevo cliente">
                                            <span class="glyphicon glyphicon-plus-sign"></span>
                                    </button>
                                    </span>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <label for="endose">Endose</label>
                                <input class="form-control input-sm" type="text" id="cont_nombres" name="cont_nombres" value="<?= $paquete->paqu_endose ?>">
                            </div>
                            <div class="col-sm-4">
                                <div class="">
                                    <label for="comprobante">Moneda</label>
                                    <?= form_dropdown('moneda_local', $moneda, $paquete->paqu_moneda, array('class' => 'form-control input-sm', "id" => "moneda_local")); ?>
                                </div>
                            </div>
                            <div class="col-sm-3 hidden">
                                <div class="">
                                    <label for="fecha">Fecha de emision</label>
                                    <div class='input-group'>
                                        <input id="fecha" name="fecha" type="text" value="<?= $paquete->paqu_fecha ?>" class="form-control fecha input-sm" />
                                        <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-6">
                                <div class="">
                                    <label for="grupo">Grupo / Nombre</label>
                                    <input id="grupo" name="grupo" type="text" value="<?= $paquete->paqu_nombre ?>" class="form-control input-sm" placeholder="Grupo" />
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <div class="">
                                    <label for="file">File</label>
                                    <input id="file" name="file" type="text" value="<?= $paquete->paqu_file ?>" class="form-control input-sm" placeholder="File" />
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="">
                                    <label for="comprobante">Estado</label>
                                    <?= form_dropdown('estado', $estado, $paquete->paqu_estado, array('class' => 'form-control input-sm', "id" => "estado")); ?>
                                </div>
                            </div>
                            <input type="hidden" name="tipop" value="<?= $paquete->paqu_tipo ?>" id="tipop">
                        </div>
                    </div>
                    <div class="col-sm-4"">
                        <div class="total clearfix form-group">
                            <span>SUB TOTAL</span>
                            <i class="msimb">S/.</i>
                            <span class="moneda_t"><input type="text" name="total_sub" id="total_sub" value="<?= $paquete->paqu_subtotal ?>" decimales="2"  class="vale" readonly=""></span>
                        </div>
                        <div class="total clearfix form-group">
                            <span>IGV</span>
                            <i class="msimb">S/.</i>
                            <span class="moneda_t"><input type="text" name="total_igv" id="total_igv" value="<?= $paquete->paqu_igv ?>" decimales="2"  class="vale" readonly=""></span>
                        </div>
                        <div class="total clearfix form-group">
                            <span>TOTAL</span>
                            <i class="msimb">S/.</i>
                            <span class="moneda_t" id="total"><input type="text" name="total_total" id="total_total" value="<?= $paquete->paqu_total ?>" decimales="2"  class="vale" readonly=""></span>
                        </div>
                        <div class="form-check hidden">
                          <input name="cons_igv" <?= ($paquete->paqu_igvafect == 'SI')?'checked':'' ?> class="form-check-input" type="checkbox" value="<?= $paquete->paqu_igvafect ?>" id="checkigv">
                          <label class="form-check-label" for="checkigv">
                            <strong>IGV</strong>
                          </label>
                        </div>
                    </div>
                </div>
                


                <div class="clearfix"></div>
                <div class="sortable">
                    <div class="serv-item">
                        <div class="serv-deta">
                        <div class="form-group">
                            <input type="hidden" name="deta_id[0]" value="" />
                            <input type="hidden" name="posicion[0]" class="posicion" value="0" />
                            <div class="col-sm-4">
                                <label for="servicio">Servicio</label>
                                <div class="input-group">
                                    <input name="serv_nombre[0]" type="hidden" class="serv_nombre" />
                                    <?= form_dropdown('servicio[0]', '', '', array('class' => 'form-control servicio input-sm', "id" => "servicio_local")); ?>
                                    
                                    <span class="input-group-btn">
                                    <button class="btn btn-default addservicio btn-sm" href="<?php echo base_url() ?>registro/crear_servicio" title="Agregar Servicio">
                                        <span class="glyphicon glyphicon-plus-sign"></span>
                                    </button>
                                    </span>
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <label for="direccion">Fecha</label>
                                <div class='input-group'>
                                    <input type='text' name="deta_fecha[0]" class="form-control datepicker" id="deta_fecha" placeholder="dd/mm/yyy" />
                                    <span class="input-group-addon input-sm"><span class="glyphicon glyphicon-calendar"></span></span>
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <label for="direccion">Hora</label>
                                <div class='input-group'>
                                    <input type='text' name="deta_hora[0]" class="form-control timepicker" id="deta_hora" placeholder="hh:mm M" />
                                    <span class="input-group-addon input-sm"><span class="glyphicon glyphicon-time"></span></span>
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <label for="direccion">Pax</label>
                                <input type="text" name="pax[0]" value="0" class="form-control pax input-sm" />
                            </div>
                            <div class="col-sm-2">
                                <label for="direccion">Precio</label>
                                <input type="text" name="precio[0]" value="0.00" class="form-control input-sm" />
                            </div>
                            <div class="clearfix"></div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-4">
                                <label for="direccion">Guía</label>
                                <input type="text" name="deta_guia[0]" value="" class="form-control input-sm">
                            </div>
                            <div class="col-sm-4 oculto sub_serv">
                                <label for="direccion">Sub servicio</label>
                                <input type="hidden" name="sub_servname[0]" value="">
                                <?= form_dropdown('sub_servicio[0]', '', '', array('class' => 'form-control sub_servicio input-sm', "id" => "sub_servicio_local")); ?>
                            </div>
                            <div class="clearfix"></div>
                        </div>
                        
                        <div class="form-group">
                            <div class="col-sm-4">
                                <label for="direccion">Hotel</label>
                                <div class="input-group">
                                    <input name="hotel_nombre[0]" type="hidden" class="hotel_nombre" />
                                    
                                    <?= form_dropdown('hotel[0]', '', '', array('class' => 'form-control hotel input-sm', "id" => "hotel_local")); ?>

                                    <span class="input-group-btn">
                                    <button class="btn btn-default addhotel btn-sm" href="<?php echo base_url() ?>contacto/crear_hotel" title="Agregar Hotel">
                                        <span class="glyphicon glyphicon-plus-sign"></span>
                                    </button>
                                    </span>
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <label for="deta_lunch">Almuerzos</label>
                                <div class='input-group'>
                                    <input type='text' name="deta_lunch[0]" class="form-control" id="deta_lunch" value="0" />
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <label for="deta_lunch_pre">Costo Almuerzo</label>
                                <div class='input-group'>
                                    <input type='text' name="deta_lunch_pre[0]" class="form-control lunch_prec" id="deta_lunch_pre" value="0.00" />
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <label for="comprobante">Tipo</label>
                                <?= form_dropdown('tipo_serv[0]', $tipo_serv, '', array('class' => 'form-control input-sm', "id" => "tipo_serv")); ?>
                            </div>
                            <div class="col-sm-2 oculto">
                                <label for="importe">TOTAL</label>
                                <input type="text" name="importe[0]" value="0.00" class="form-control input-sm importe" readonly=""/>
                            </div>
                            <div class="col-sm-2">
                                <label for="importe"><b>Cobrado al PAX</b></label>
                                <input type="text" name="cobrado_pax" value="<?= $paquete->paqu_cobrado_pax ?>" class="form-control input-sm cobrado_pax"/>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-7">
                                <textarea style="height:30px" id="detalle" name="detalle[0]" class="form-control" placeholder="Alguna observacion para tener en cuenta"></textarea>
                            </div>
                            <div class="col-sm-2">
                                <input type="text" name="bus[0]" class="input-sm form-control" placeholder="Bus de llegada" />
                            </div>
                            <div class="col-sm-3">
                                <div class="">
                                    <?= form_dropdown('prioridad[0]', $prioridad, '', array('class' => 'form-control input-sm', "id" => "prioridad")); ?>
                                </div>
                            </div>
                        </div>
                        <input type="hidden" name="color[0]" value="0" />
                        <div class="form-group tercero hidden">
                            <div class="col-sm-7">
                                <input type="text" name="t_nombre[0]" class="input-sm form-control" placeholder="Tercero nombre" />
                            </div>
                            <div class="col-sm-2">
                                <input type="text" name="t_monto[0]" class="input-sm form-control" value="0.00" />
                            </div>
                        </div>
                        <hr style="border-color: #111; margin-top: 10px; margin-bottom: 10px;">
                        <div class="form-group tercero">
                            <div class="col-sm-12">
                                <div class="prov-items"></div>
                            </div>
                            <div class="col-sm-12" style="margin-top: 5px">
                                <button type="button" class="btn btn-primary pull-left btn-xs btn-prov" data-val="2"><span class="glyphicon glyphicon-plus"></span> Proveedores</button>
                            </div>
                        </div>
                        <hr style="border-color: #111; margin-top: 10px; margin-bottom: 10px;">
                        <div class="form-group">
                            <div class="col-sm-5">
                                <div class="adic-items"></div>
                                <button type="button" class="btn btn-primary pull-left btn-xs btn-adides" data-val="1"><span class="glyphicon glyphicon-plus"></span> Adiciones</button>
                            </div>
                            <div class="col-sm-5">
                                <div class="desc-items"></div>
                                <button type="button" class="btn btn-primary pull-left btn-xs btn-adides" data-val="2"><span class="glyphicon glyphicon-minus"></span> Descuentos</button>
                            </div>
                            <div class="col-sm-2">
                                <div class="pull-left">
                                  <input class="form-check-input complementary" type="checkbox" value="" name="complementary[0]" id="complementary">
                                  <label class="form-check-label" for="complementary">
                                    Complementary
                                  </label>
                                </div>
                                <!--
                                <button type="button" class="btn btn-success pull-right btn-xs btn-adicionales"><span class="glyphicon glyphicon-eye-open"></span> Adiciones y descuentos</button>
                                -->
                            </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <div class="col-sm-6">
                    <div class="row">
                        <div class="col-sm-6">
                            <input type="hidden" name="num_images" id="num_images">
                                <div class="files"></div>
                                <div class="form-group">
                                    <button style="margin-left: 15px" type="button" class="btn btn-success btn-xs pull-left" id="add-image">
                                        Agregar Imagen
                                    </button>
                                </div>        
                        </div>
                        <div class="col-sm-6">
                            <?php foreach ($imagenes as $i => $img): ?>
                                <div class="form-group">
                                    <a target="_blank" class="" href="<?= base_url() ?>assets/img/files/<?= $img->paim_imagen ?>">IMAGEN<?= $i+1 ?> </a> 
                                    <button onclick="confirm_delete_image(this)" type="button" style="margin-left: 10px" data-id="<?= $img->paim_id ?>" class="btn btn-xs btn-danger "> <span class="glyphicon glyphicon-trash"></span></button>
                                </div>
                            <?php endforeach ?>        
                        </div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-primary guardar">Guardar</button>
                    <button type="button" class="btn btn-primary guardar cerrar">Guardar y Cerrar</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div id="clonables" class="hide">
    <div class="prov-item">
        <div class="row">
            <input type="hidden" class="prov_paqu_id">
            <div class="col-sm-3">
                <div class="input-group">
                    <span class="input-group-btn">
                        <button type="button" class="btn btn-danger btn-sm" data-href="/delete.php?id=23" onclick="confirm_deleteprov(this)"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span></button>
                    </span>
                    <select class="form-control input-sm prov_tipo" required></select>
                </div>
            </div>
            <div class="col-sm-3">
                <select class="prov_id form-control input-sm" disabled required></select>
            </div>
            <div class="col-sm-1">
                <input type="text" class="prov_cantidad form-control input-sm" placeholder="#" value="1" required>
            </div>
            <div class="col-sm-2">
                <input type="text" class="prov_precio form-control input-sm" placeholder="Precio" value="0.00" dval="0.00" required>
            </div>
			<div class="col-sm-3">
				<?= form_dropdown('', $moneda, '', array('class' => 'form-control input-sm prov_moneda')); ?>
			</div>
            <div class="col-sm-12">
                <input type="text" class="prov_descripcion form-control input-sm" placeholder="Descripci&oacute;n">
            </div>
        </div>
    </div>
    <div class="image-item">
        <div class="form-group">
            <input type="file" name="imagen"> 
        </div>
    </div>
    <div class="adides-item">
        <div class="form-group">
            <div class="">
                <div class="col-sm-8">
                    <div class="input-group">
                        <span class="input-group-btn">
                            <button type="button" class="btn btn-danger btn-sm" data-href="/delete.php?id=23" onclick="confirm_delete(this)"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span></button>
                        </span>
                        <input type="text" class="form-control input-sm adides-nombre">
                        <input type="hidden" class="adic_id">
                    </div>
                    
                </div>
                <div class="col-sm-4">
                    <input type="text" name="" class="form-control input-sm adides-precio">
                </div>
            </div>
        </div>
    </div>
</div>
<script>
function confirm_delete($this){
    actual = $this;
    if(confirm("Realmente desea eliminar el elemento?")){
        $(actual).parents('.adides-item').remove();
        updateRow();

    }
}
function confirm_delete_image($this){
    actual = $this;
    if(confirm("Realmente desea eliminar la imagen?")){
        
        $.ajax({
            type: "POST",
            dataType: "json",
            url: "<?= base_url() ?>" + "Registro/eliminar_imagen/"+$(actual).attr("data-id"),
            success: function(data) {
                if(data.exito){
                    $(actual).parents('.form-group').remove();
                }else{
                    alert(data.mensaje);
                }
            }
        });
    }
}
</script>