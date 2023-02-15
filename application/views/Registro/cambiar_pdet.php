
<script>
    adiciones = <?= $adic ?>;
</script>
<div class="modal-dialog modal-local" role="document">
    <div class="modal-content">
        <form id="frm-gen_orden" class="form-horizontal" action="<?=base_url()?>Registro/pdet_guardar/<?= $pdet->id ?>" method="post">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="gridSystemModalLabel"></h4>
            </div>
            <div class="modal-body">
                <input type="hidden" name="paqu_id" value="">
                <div class="alert alert-danger errorOrd hidden" role="alert">
                    <span class="text">Error:</span>
                </div>
                <div class="row">
                    <div class="col-sm-12">
                        <div class="form-group">
                            <div class="col-sm-3">
                                <div class="">
                                    <label for="fecha">Fecha</label>
                                    <div class='input-group'>
                                        <input id="fecha" name="fecha" type="text" value="<?= $pdet->fecha ?>" class="form-control fecha input-sm" disabled />
                                        <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <label for="serv_id">Servicio</label>
                                <input id="serv_name" name="serv_name" type="hidden" value="<?= $pdet->serv ?>" class="form-control input-sm" />
                                <?= form_dropdown('serv_id', $servicios, $pdet->serv_id, array('class' => 'form-control input-sm', "disabled" => "disabled")); ?>
                            </div>
                            <div class="col-sm-2">
                                <div class="">
                                    <label for="pax">Pax</label>
                                    <div class='input-group'>
                                        <input id="pax" name="pax" type="text" value="<?= $pdet->pax ?>" class="form-control pax input-sm" />
                                        <span class="input-group-addon"><span class="glyphicon glyphicon-user"></span></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <label for="precio">Precio</label>
                                <input id="precio" name="precio" type="text" value="<?= $pdet->precio ?>" class="form-control precio input-sm" />
                            </div>
                            <div class="col-sm-2">
                                <label for="precio">Total</label>
                                <input id="total" name="total" type="text" value="<?= $pdet->total ?>" class="form-control total input-sm" readonly/>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-2 col-sm-offset-6">
                                <label for="pax">Almuerzos</label>
                                <div class='input-group'>
                                    <input id="lunch" name="lunch" type="text" value="<?= $pdet->lunch ?>" class="form-control lunch input-sm" />
                                    <span class="input-group-addon"><span class="glyphicon glyphicon-user"></span></span>
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <label for="lunch_pre">Costo almuerzo</label>
                                <input id="lunch_pre" name="lunch_pre" type="text" value="<?= $pdet->lunch_pre ?>" class="form-control lunch_pre input-sm"/>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="clearfix"></div>
                <div class="form-group adides">
                    <div class="col-sm-5">
                        <div class="adic-items"></div>
                        <button type="button" class="btn btn-primary pull-left btn-xs btn-adides" data-val="1"><span class="glyphicon glyphicon-plus"></span> Adiciones</button>
                    </div>
                    <div class="col-sm-5">
                        <div class="desc-items"></div>
                        <button type="button" class="btn btn-primary pull-left btn-xs btn-adides" data-val="2"><span class="glyphicon glyphicon-minus"></span> Descuentos</button>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <div class="col-sm-12">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-primary guardar">Guardar</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div id="clonables" class="hide">
    <div class="adides-item">
        <div class="form-group">
            <div class="col-sm-8">
                <div class="input-group">
                    <span class="input-group-btn">
                        <button type="button" class="btn btn-danger btn-sm" data-href="/delete.php?id=23" onclick="confirm_delete(this)"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span></button>
                    </span>
                    <input type="text" class="form-control input-sm adides-nombre">
                    <input type="hidden" class="adides-id">
                </div>
                
            </div>
            <div class="col-sm-4">
                <input type="text" name="" class="form-control input-sm adides-precio">
            </div>
        </div>
    </div>
</div>
<script>
$(document).ready(function(){
    if(adiciones.length > 0){
        $.each(adiciones, function(i, add) {
            console.log(add);
            $nuevafila = $('#clonables .adides-item').clone();
            if(add.tipo == 'ADICION'){
                name = "adic_nombre[]";
                precio = "adic_precio[]";
                id = "adic_id[]";
                find = ".adic-items";
            }else{
                name = "desc_nombre[]";
                precio = "desc_precio[]";
                id = "desc_id[]";
                find = ".desc-items";
            }
            
            $nuevafila.find('.adides-id').attr("name",id).val(add.id);
            $nuevafila.find('.adides-precio').attr("name",precio).val(add.monto).on('change', updateRow);
            $nuevafila.find('.adides-nombre').attr("name",name).val(add.descripcion);

            $('.adides').find(find).append($nuevafila);
        });
    }

    $('.btn-adides').on('click', adicionales);
    $('.pax, .precio, .lunch, .lunch_pre').on('change', updateRow);
})
function adicionales(){
    $nuevafila = $('#clonables .adides-item').clone();
    var padre = $('.adides');
            
    if($(this).attr("data-val") == '1'){
        name = "adic_nombre[]";
        precio = "adic_precio[]";
        find = ".adic-items";
        id = "adic_id[]";
    }else{
        name = "desc_nombre[]";
        precio = "desc_precio[]";
        find = ".desc-items";
        id = "desc_id[]";
    }
    $nuevafila.find('.adides-nombre').attr("name",name);
    $nuevafila.find('.adides-precio').attr("name",precio).on('change', updateRow);
    $nuevafila.find('.adides-id').attr("name",id);
    padre.find(find).append($nuevafila);
    updateRow()
    return false;   
}
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
function updateRow() {
    console.log("updateRow");
    var pax = $('input[name="pax"]').val();
        precio = $('input[name="precio"]').val();
        lunch = $('input[name="lunch"]').val();
        lunch_pre = $('input[name="lunch_pre"]').val();
        adicion = $('input[name="adic_precio[]"]');
        descuento = $('input[name="desc_precio[]"]');
    if(!esNumeroPositivo(pax) || pax == '') { pax = 0; $('input[name="pax"]').dval(0); }
    if(!esNumeroPositivo(precio) || precio == '') { precio = 0; $('input[name="precio"]').dval(0);}
    if(!esNumeroPositivo(lunch) || lunch == '') { lunch = 0; $('input[name="lunch"]').dval(0); }
    if(!esNumeroPositivo(lunch_pre) || lunch_pre == '') { lunch_pre = 0; $('input[name="lunch_pre"]').dval(0);}
    
    var importe = 0;
    var adiciones = 0;
    var descuentos = 0;
    var adic = [];
    var desc = [];
    if(adicion.length > 0){
        adicion.each(function(i, elem) {
            var numero = $(elem).val();
            if(!esNumeroPositivo(numero) || numero == '') { numero = 0; $(this).dval(0); }
            adiciones += parseFloat(numero);
            adic[i] = [$(this),numero];
        })
        for (var i = adic.length - 1; i >= 0; i--) {
            adic[i][0].dval(adic[i][1]);
        }
    }
    if(descuento.length > 0){
        descuento.each(function(i, elem) {
            var numero = $(elem).val();
            if(!esNumeroPositivo(numero) || numero == '') { numero = 0; $(this).dval(0); }
            descuentos += parseFloat(numero);
            desc[i] = [$(this),numero];
        })
        
        for (var i = desc.length - 1; i >= 0; i--) {
            desc[i][0].dval(desc[i][1]);
        }
    }
    if (esNumeroPositivo(pax) && esNumeroPositivo(precio)) {
        importe = (pax * precio) + (lunch * lunch_pre) + (adiciones - descuentos);
    }
    llenarrow({total:importe,precio:precio,pax:pax, lunch:lunch, lunch_pre:lunch_pre},$('#frm-gen_orden'));
}
function llenarrow(ar , padre){
    $.each(ar,function(item,val){
        if(item == "pax" || item == "lunch")
            $item = padre.find('input[name="'+item+'"]').val(val);
        else
            $item = padre.find('input[name="'+item+'"]').dval(val);
    })
}
</script>