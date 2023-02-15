<style type="text/css">
    .total { font-size: 20px; text-align: right; }
    .total input { width: 100px; text-align: right; border: 0; }
    .form-check{ position: absolute; bottom: 15px; }
</style>
<script>
detas = <?= $detas ?>;
</script>
<div id="page-wrapper">
    <div class="page-header col-md-12">
        <div class="row page-header-title" style="background: #e5565a; color: #fff;">
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
            <form action="<?= base_url() ?>Registro/paq_guardar/<?= $paquete->paqu_id ?>" method="post" id="vender">
                <div class="col-sm-8">
                    <div class="row">
                    <div class="form-group">
                        <div class="row">
                            <div class="col-sm-6">
                                <label for="rsocial">Contacto</label>
                                <input type="hidden" id="clie_id" value="<?= $paquete->paqu_clie_id ?>">
                                <input type="hidden" name="clie_rsocial" id="clie_rsocial" value="<?= $paquete->paqu_clie_rsocial ?>">
                                <input type="hidden" name="clie_abrev" id="clie_codigo" value="<?= $paquete->paqu_clie_codigo ?>">
                                <div class="input-group">
                                    
                                    <select name="cliente" id="cliente" class="form-control input-sm"></select>
                                    <span class="input-group-btn">
                                    <a class="btn btn-default newclient btn-sm" href="<?php echo base_url() ?>contacto/crear_clie" title="Crear nuevo cliente">
                                            <span class="glyphicon glyphicon-plus-sign"></span>
                                        </a>
                                    </span>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <label for="endose">Endose</label>
                                <input class="form-control input-sm" type="text" id="cont_nombres" name="cont_nombres" value="<?= $paquete->paqu_endose ?>">
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label for="comprobante">Moneda</label>
                                    <?= form_dropdown('moneda', $moneda, $paquete->paqu_moneda, array('class' => 'form-control input-sm', "id" => "moneda")); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-sm-6">
                                <label for="grupo">Nombre / Grupo</label>
                                <input id="grupo" name="grupo" type="text" value="<?= $paquete->paqu_nombre ?>" class="form-control input-sm" placeholder="Grupo" />
                            </div>
                            <div class="col-sm-3">
                                <label for="file">File</label>
                                <input id="file" name="file" type="text" value="<?= $paquete->paqu_file ?>" class="form-control input-sm" placeholder="File" />
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label for="comprobante">Estado</label>
                                    <?= form_dropdown('estado', $estado, $paquete->paqu_estado, array('class' => 'form-control input-sm', "id" => "estado")); ?>
                                </div>
                            </div>
                            <input type="hidden" name="tipop" value="<?= $paquete->paqu_tipo ?>" id="tipop">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-6">
                            <input type="hidden" name="num_images" id="num_images">
                                <div class="files"></div>
                                <div class="form-group">
                                    <button style="margin-left: 15px" type="button" class="btn btn-success btn-xs" id="add-image">
                                        Agregar Imagen
                                    </button>
                                </div>        
                        </div>
                        <div class="col-sm-6">
                            <?php foreach ($imagenes as $i => $img): ?>
                                <div class="form-group">
                                    <a target="_blank" href="<?= base_url() ?>assets/img/files/<?= $img->paim_imagen ?>">IMAGEN<?= $i+1 ?> </a> 
                                    <button onclick="confirm_delete_image(this)" type="button" style="margin-left: 10px" data-id="<?= $img->paim_id ?>" class="btn btn-xs btn-danger"> <span class="glyphicon glyphicon-trash"></span></button>
                                </div>
                            <?php endforeach ?>        
                        </div>
                    </div>
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
                    <div class="form-check">
                      <input name="cons_igv" <?= ($paquete->paqu_igvafect == 'SI')?'checked':'' ?> class="form-check-input" type="checkbox" value="<?= $paquete->paqu_igvafect ?>" id="checkigv">
                      <label class="form-check-label" for="checkigv">
                        <strong>IGV</strong>
                      </label>
                    </div>
                </div>
                <div class="clearfix"></div>
                <input id="fecha" name="fecha" type="hidden" value="<?= $paquete->paqu_fecha ?>" class="form-control fecha input-sm" />
                <div class="serv-wrap">
                    <div class="sortable clearfix">
                    </div>
                </div>
                <div class="row" style="margin-left:25px">
                    <div class="col-xs-6">
                        <a class="btn btn-default btn-sm agregarfila" href="#">
                            <span class="glyphicon glyphicon-plus-sign" aria-hidden="true"></span> Agregar producto
                        </a>
                    </div>
                    <div class="col-xs-6 text-right">
                        <button type="submit" value="Guardar" class="btn btn-success btn-sm">
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
                <input type="text" class="prov_cantidad form-control input-sm" placeholder="#" value="1">
            </div>
            <div class="col-sm-1">
                <input type="text" class="prov_precio form-control input-sm" placeholder="Precio" value="0.00" dval="0.00">
            </div>
			<div class="col-sm-1">
				<?= form_dropdown('', $moneda, '', array('class' => 'form-control input-sm prov_moneda')); ?>
			</div>
            <div class="col-sm-3">
                <input type="text" class="prov_descripcion form-control input-sm" placeholder="Descripción">
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
            <div class="row">
                <div class="col-sm-8">
                    <div class="input-group">
                        <span class="input-group-btn">
                            <button type="button" class="btn btn-danger btn-sm" data-href="/delete.php?id=23" onclick="confirm_delete(this)"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span></button>
                        </span>
                        <input type="text" name="" class="form-control input-sm adides-nombre">
                        <input type="hidden" class="adic_id">
                    </div>
                </div>
                <div class="col-sm-4">
                    <input type="text" name="" class="form-control input-sm adides-precio">
                </div>
            </div>
        </div>
    </div>

    <div class="serv-item">
        <div class="serv-opts">
            <button type="button" class="btn btn-danger btn-sm" data-href="/delete.php?id=23" data-toggle="modal" data-target="#confirm-delete">
                <span class="glyphicon glyphicon-trash borrarItem" aria-hidden="true"></span>
            </button>
        </div>
        <div class="serv-deta">
            <input type="hidden" name="deta_id[]" value="" />
            <input type="hidden" name="posicion[]" class="posicion" value="" />
            <div class="form-group">
                <div class="row">
                    <div class="col-sm-4">
                        <label for="servicio">Servicio</label>
                        <div class="input-group">
                            <input name="serv_nombre[]" type="hidden" class="serv_nombre" />

                            <?= form_dropdown('servicio[]', $servicio, '', array('class' => 'form-control input-sm servicio', "id" => "servicio")); ?>

                            <span class="input-group-btn">
                            <a class="btn btn-default addservicio btn-sm" href="<?php echo base_url() ?>registro/crear_servicio" title="Agregar Servicio">
                                <span class="glyphicon glyphicon-plus-sign"></span>
                            </a>
                            </span>
                        </div>
                    </div>
                    <div class="col-sm-8">
                        <div class="row">
                            <div class="col-sm-4">
                                <label for="direccion">Guia</label>
                                <input type="text" name="deta_guia[]" class="form-control input-sm">
                            </div>
                            <div class="col-sm-2">
                                <label for="direccion">Pax</label>
                                <div class='input-group'>
                                    <input type="text" name="pax[]" value="0" class="form-control input-sm pax" />
                                    <span class="input-group-addon"><span class="glyphicon glyphicon-user"></span></span>
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <label for="deta_lunch">Almuerzos</label>
                                <div class='input-group'>
                                    <input type='text' name="deta_lunch[]" value="0" class="form-control" id="deta_lunch" />
                                    <span class="input-group-addon"><span class="glyphicon glyphicon-user"></span></span>
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <label for="deta_lunch">Por Pax</label>
                                <input type="text" name="precio[]" value="0.00" class="form-control precio input-sm" />
                            </div>
                            <div class="col-sm-2">
                                <label for="deta_lunch_pre"><strong>Total Servicio</strong></label>
                                <input type='hidden' name="deta_lunch_pre[]" class="form-control lunch_prec" id="deta_lunch_pre" value="0.00" />
                                <input type="text" name="importe[]" value="0.00" class="form-control input-sm importe"/>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm-4 oculto sub_serv">
                    <label for="direccion">Sub servicio</label>
                    <input type="hidden" name="sub_servname[]" value="">
                    <?= form_dropdown('sub_servicio[]', '', '', array('class' => 'form-control sub_servicio input-sm', "id" => "sub_servicio_local")); ?>
                </div>
                <div class="clearfix"></div>
            </div>
            <div class="form-group">
                <div class="row">
                    <div class="col-sm-4">
                        <label for="direccion">Hotel</label>
                        <div class="input-group">
                            <span class="input-group-btn">
                                <input type="checkbox" value="1" name="chk_hotel[]">
                            </span>
                            <input name="hotel_nombre[]" type="hidden" class="hotel_nombre" />
                            <?= form_dropdown('hotel[]', $hotel, '', array('class' => 'form-control input-sm hotel')); ?>
                            <span class="input-group-btn">
                            <a class="btn btn-default addhotel btn-sm" href="<?php echo base_url() ?>contacto/crear_hotel" title="Agregar Hotel">
                                <span class="glyphicon glyphicon-plus-sign"></span>
                            </a>
                            </span>
                        </div>
                    </div>
                    
                    <div class="col-sm-2">
                        <label for="comprobante">Tipo</label>
                        <?= form_dropdown('tipo_serv[]', $tipo_serv, '', array('class' => 'form-control input-sm', "id" => "moneda")); ?>
                    </div>
                    
                    <div class="col-sm-2">
                        <label for="direccion">Fecha Salida</label>
                        <div class='input-group'>
                            <input type='text' name="deta_fecha[]" class="form-control datepicker" id="deta_fecha" placeholder="dd/mm/yyy" />
                            <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                        </div>
                    </div>

                    <div class="col-sm-2">
                        <label for="direccion">Hora Salida</label>
                        <div class='input-group'>
                            <input type='text' name="deta_hora[]" class="form-control timepicker" id="deta_hora" placeholder="hh:mm M" />
                            <span class="input-group-addon"><span class="glyphicon glyphicon-time"></span></span>
                        </div>
                    </div>
                    <div class="col-sm-2">
                        <label for="direccion">Fecha Llegada</label>
                        <div class='input-group'>
                            <input type='text' name="deta_fecha_llegada[]" value="" class="form-control datepicker" id="deta_fecha_llegada" placeholder="dd/mm/yyy" />
                            <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="row">
                    <div class="col-sm-6">
                        <textarea style="height:30px" type="text" name="detalle[]" class="form-control deta_detalle input-sm" placeholder="Observación"></textarea>
                    </div>
                    <div class="col-sm-2">
                        <input type="text" name="bus[]" class="input-sm form-control" placeholder="Bus de llegada" />
                    </div>
                    <div class="col-sm-2">
                        <input type="text" name="bus_salida[]" class="input-sm form-control" placeholder="Bus de salida" />
                    </div>
                    <div class="col-sm-2">
                        <?= form_dropdown('prioridad[]', $prioridad, '', array('class' => 'form-control input-sm', "id" => "prioridad")); ?>
                    </div>
                </div>
            </div>
            <hr style="border-color: #111; margin-top: 10px; margin-bottom: 10px;">
            <div class="form-group tercero oculto">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="prov-items"></div>
                    </div>
                    <div class="col-sm-12">
                        <button type="button" class="btn btn-primary pull-left btn-xs btn-prov" data-val="2"><span class="glyphicon glyphicon-plus"></span> Proveedores</button>
                    </div>
                </div>
            </div>
             <hr style="border-color: #111; margin-top: 10px; margin-bottom: 10px;">
            <div class="form-group">
                <div class="row">
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
                            <label for="direccion">Color</label>
                            <?= form_dropdown('color[]', array("0"=>"* Ninguno","1"=>"Naranja","2"=>"Amarillo","3"=>"Celeste"), '', array('class' => 'form-control input-sm', "id" => "color")); ?>
                          <input class="form-check-input complementary" type="checkbox" value="" name="complementary[]" id="complementary">
                          <label class="form-check-label" for="complementary">
                            Complementary
                          </label>
                        </div>
                        <!--
                        <button type="button" class="btn btn-success pull-right btn-xs btn-adicionales"><span class="glyphicon glyphicon-eye-open"></span> Adiciones y descuentos</button>
                        -->
                    </div>
                    <!--
                    <div class="adicionales">
                        <div class="col-sm-6">
                            <label for="adicion">Adición</label>
                            <input type="text" name="adicion[]" class="form-control input-sm"/>
                        </div>
                        <div class="col-sm-2">
                            <label for="adicion_val">Monto</label>
                            <input type="text" name="adicion_val[]" value="0.00" class="form-control input-sm"/>
                        </div>
                        <div class="col-sm-6 col-sm-offset-4">
                            <label for="descuento">Descuento</label>
                            <input type="text" name="descuento[]" class="form-control input-sm"/>
                        </div>
                        <div class="col-sm-2">
                            <label for="descuento">Monto</label>
                            <input type="text" name="descuento_val[]" value="0.00" class="form-control input-sm"/>
                        </div>
                    </div>
                    -->
                </div>
            </div>
            
             <div class="clearfix"></div>
        </div>
    </div>
</div>

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
<div class="modal fade" id="confirm-delete2" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
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
$('#confirm-delete').on('show.bs.modal', function(e) {
    $this = $(e.relatedTarget);
    $dlg = $(e.delegateTarget);
    $(this).find('.btn-ok').on('click', function() {
        $this.parents('.serv-item').fadeOut('slow', function() {
            $this.parents('.serv-item').remove();
            updateTotal();
        });
        $dlg.modal('hide')
    });
});
/*
$('#confirm-delete2').on('show.bs.modal', function(e) {

    $this = $(e.relatedTarget);
    $dlg = $(e.delegateTarget);
    $(this).find('.btn-ok').on('click', function() {
        $this.parents('.adides-item').fadeOut('slow', function() {
            $this.parents('.adides-item').remove();
        });
        $dlg.modal('hide')
        updateRow();
    });
});
*/
function confirm_delete($this){
    $('#confirm-delete2').modal('show');
    chan = $($this).closest('.serv-item').find('.precio');
    actual = $this;
    
    $dlg = $('#confirm-delete2');
    $dlg.find('.btn-ok').on('click', function() {
        $(actual).parents('.adides-item').remove();
        chan.change();
        $dlg.modal('hide')
    });
}
function confirm_deleteprov($this){
    $('#confirm-delete2').modal('show');
    actual = $this;
    
    $dlg = $('#confirm-delete2');
    $dlg.find('.btn-ok').on('click', function() {
        $(actual).parents('.prov-item').remove();
        $dlg.modal('hide')
    });
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