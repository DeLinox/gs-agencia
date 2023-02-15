<div class="modal-dialog" role="document">
    <div class="modal-content">
        <form class="form-horizontal" action="<?= base_url() ?>Registro/guardar_tarifa/<?= $serv_id ?>/<?= $clie_id ?>" method="post" id="frm-newClient">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title" id="gridSystemModalLabel"></h4>
        </div>
        <div class="modal-body">
            <div class="alert alert-danger error hidden" role="alert">
                <span class="text">Error:</span>
            </div>
            <table class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>SERVICIO</th>
                        <th>CLIENTE</th>
                        <th>MONEDA</th>
                        <th class="col-sm-2">TARIFA</th>
                        <th class="col-sm-2">ALMUERZO</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><?= $serv_name ?></td>
                        <td><?= $clie_rsocial ?></td>
                        <td>
                            <?= form_dropdown('moneda', $monedas, $serv_moneda, array('class' => 'form-control input-sm', "id" => "moneda")); ?>
                        </td>
                        <td><input class="form-control" type="text" name="serv_prec" value="<?= $precio ?>"></td>
                        <td><input class="form-control" type="text" name="serv_lunch" value="<?= $lunch ?>"></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
            <button type="submit" class="btn btn-primary">Guardar</button>
        </div>
        </form>
    </div>
</div>
<script type="text/javascript">
    $('input[name="serv_prec"], input[name="serv_lunch"]').change(function(){
        $(this).dval($(this).val());
    })
</script>