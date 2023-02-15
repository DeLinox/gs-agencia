
<style>
	.table td, .table th{padding: 2px}
</style>
<div class="modal-dialog modal-local" id="mdl-local"  role="document">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title" id="gridSystemModalLabel">Datos del paquete</h4>
        </div>
        <div class="modal-body">
            <div class="row">
                <?= $html ?>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
        </div>
    </div>
</div>