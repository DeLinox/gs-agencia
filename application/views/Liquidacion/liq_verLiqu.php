
<div class="modal-dialog" style="width:900px;">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title" id="myModalLabel">Hoja de liquidación</h4>
        </div>
        <div class="modal-body" style="height:500px;overflow:scroll;">
            <iframe frameborder="0" src="<?php echo base_url() ?>Liquidacion/liq_generaPDF/<?php echo $id; ?>/true" width="100%"  height="450" style="padding-top:1px;"></iframe>
        </div>
        <div class="modal-footer">
            <button  type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
        </div>
    </div>
</div>