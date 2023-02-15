<div class="modal-dialog" role="document">
    <div class="modal-content">
        
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title" id="gridSystemModalLabel"></h4>
        </div>
        <div class="modal-body">
            <div class="table-responsive">
                <table class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>FILE</th>
                            <th>MONEDA</th>
                            <th>PAGADO</th>
                            <th>SALDO</th>
                            <th>TOTAL</th>
                        </tr>
                    </thead>
                    <tbody><?= $table ?></tbody>
                </table>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
        </div>
    </div>
</div>