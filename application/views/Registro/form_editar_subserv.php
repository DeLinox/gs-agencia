<div class="modal-dialog" role="document">
    <div class="modal-content">
        <form class="form-horizontal" action="<?= base_url() ?>Registro/guardar_subserv/<?= $clse_id ?>/<?= $clie_id ?>" method="post" id="frm-newClient">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="gridSystemModalLabel"></h4>
            </div>
            <div class="modal-body fact">
                <div class="alert alert-danger error hidden" role="alert">
                    <span class="text">Error:</span>
                </div>
                <h3><strong>Cliente: </strong><?= $clie_rsocial ?></h3>
                <h3><strong>Servicio: </strong><?= $serv_name ?></h3>
                <div class="fact-wrap">
                    <div class="fact-head">
                        <div class="row">
                            <div class="col-sm-2"><strong>Opciones</strong></div>
                            <div class="col-sm-5"><strong>Sub Servicio</strong></div>
                            <div class="col-sm-3"><strong>Moneda</strong></div>
                            <div class="col-sm-2"><strong>Precio</strong></div>

                        </div>
                    </div>
                    <div class="sortable clearfix"></div>
                    <div class="form-group">
                        <div class="col-sm-12">
                            <button type="button" class="btn btn-success pull-left btn-xs btn-add" data-val="1"><span class="glyphicon glyphicon-plus"></span> Agregar sub servicio</button>
                        </div>

                    </div>
                    
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                <button type="submit" class="btn btn-primary">Guardar</button>
            </div>
        </form>
    </div>
</div>
<div id="clonables" class="hide">
    <div class="fact-item">
        <input type="hidden" name="sub_id[]" value=''>
        <div class="row">
            <div class="col-sm-2">
                    <button type="button" class="btn btn-danger btn-sm" data-href="/delete.php?id=23" onclick="confirm_delete(this)"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span></button>
            </div>
            <div class="col-sm-5">
                <input type="text" name="sub_name[]" class="form-control input-sm" value='' placeholder="Nombre del sub servicio">
            </div>
            <div class="col-sm-3">
                <?= form_dropdown('sub_moneda[]', $monedas, "SOLES", array('class' => 'form-control input-sm', "id" => "sub_moneda")); ?>
            </div>
            
            <div class="col-sm-2">
                <input type="text" name="sub_prec[]" class="form-control input-sm" value='0.00'>
            </div>
        </div>
    </div>    
</div>
<script type="text/javascript">
    $(document).ready(function(){
        var servs = <?= $sub_serv ?>;
        if(servs.length < 1)
            adicional();
        else{
            $.each(servs, function(i, elem) {
                $nuevafila = $('#clonables .fact-item').clone();
                $nuevafila.find("input[name='sub_prec[]']").val(elem.clsu_monto).on('change', ajust_prec);
                $nuevafila.find("input[name='sub_name[]']").val(elem.clsu_descripcion);
                $nuevafila.find("input[name='sub_id[]']").val(elem.clsu_id);
                $nuevafila.find("select[name='sub_moneda[]']").val(elem.clsu_moneda);
                $nuevafila.hide();
                $nuevafila.appendTo('.sortable');
                $nuevafila.fadeIn(); 
            });   
        }
        $(".btn-add").on('click', adicional);

    })
    function adicional(){
        $nuevafila = $('#clonables .fact-item').clone();
        $nuevafila.find("input[name='sub_prec[]']").on('change', ajust_prec);
        $nuevafila.hide();
        $nuevafila.appendTo('.sortable');
        $nuevafila.fadeIn();
        return false;   
    }
    function confirm_delete($this){
        actual = $this;
        if(confirm("Realmente desea eliminar el elemento?")){
            $(actual).parents('.fact-item').remove();
        }
    }
    function ajust_prec() {
        $(this).dval($(this).val());
        return false;
    }
</script>