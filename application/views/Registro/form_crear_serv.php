<div class="modal-dialog" role="document">
    <div class="modal-content">
        <form class="form-horizontal" action="<?= base_url() ?>Registro/guardar_servicio/<?= $servicio->id ?>" method="post" id="frm-newClient">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title" id="gridSystemModalLabel"></h4>
        </div>
        <div class="modal-body">
            <div class="alert alert-danger error hidden" role="alert">
                <span class="text">Error:</span>
            </div>
                <div>
                    <?= form_label('<strong>Descripción</strong>', 'descripcion', array('class' => 'control-label')); ?>
                    <?= form_input(array("name" => "descripcion", "value" => $servicio->nombre, "class" => "form-control input-sm")); ?>
                </div>
                <div class="row">
                    <div class="col-sm-4">
                        <?= form_label('<strong>Tipo</strong>', 'tipo', array('class' => 'control-label')); ?>
                        <?= form_dropdown('tipo', $tipo, $servicio->tipo, array('class' => 'form-control input-sm')); ?>
                    </div>
                    <div class="col-sm-4">
                        <?= form_label('<strong>Hora del servicio</strong>', 'hora', array('class' => 'control-label')); ?>
                        <?= form_input(array("name" => "hora", "value" => $servicio->hora, "class" => "form-control input-sm timepicker")); ?>
                    </div>
                    <div class="col-sm-4">
                        <?= form_label('<strong>Codigo</strong>', 'abrev', array('class' => 'control-label')); ?>
                        <?= form_input(array("name" => "abrev", "value" => $servicio->abrev, "class" => "form-control input-sm")); ?>
                    </div>
                    <!--
                    <div class="col-sm-4">
                        <?= form_label('<strong>Activo</strong>', 'estado', array('class' => 'control-label')); ?>
                        <?= form_dropdown('estado', array("1" => "SI", "0" => "NO"), $servicio->habilitado, array('class' => 'form-control input-sm')); ?>
                    </div>
                    -->
                </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
            <button type="submit" class="btn btn-primary">Guardar</button>
        </div>
        </form>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function() {
        $('.timepicker').datetimepicker({
            format: 'LT'
        });
    })
</script>