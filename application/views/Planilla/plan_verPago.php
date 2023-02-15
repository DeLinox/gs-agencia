<div class="modal-dialog" style="width:800px;">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title" id="myModalLabel">Ver factura</h4>
        </div>
        <div class="modal-body" style="height:500px;">
            <iframe frameborder="0" src="<?= base_url() ?>Planilla/plan_generaPdfPago/<?= $plan_id ?>" width="100%"  height="100%" style="padding-top:1px;"></iframe>
        </div>
        <div class="modal-footer">
            
        </div>
    </div>
</div>