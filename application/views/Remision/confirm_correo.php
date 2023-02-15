<div class="modal-dialog" role="document">
    <div class="modal-content">
        <form class="form-horizontal" action="<?= base_url() ?>Venta/confirm_correo/<?php echo $id; ?>" method="post">

        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title" id="gridSystemModalLabel">Enviar factura electronica</h4>
        </div>
        <div class="modal-body">
<div class="pad20">
            <div class="form-group">
                <label for="exampleInputEmail1">Email address</label>
                <input class="form-control" type="text" id="corre-confirmar" value="<?php echo $venta->vent_clie_email; ?>" name="correo">
              </div>
              <div class="form-group">
                <label for="exampleInputEmail1">Mensaje</label>
                <textarea class="form-control" name="body"><?php echo $this->configuracion->conf_mail_body; ?></textarea>
              </div>
        </div>

            
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
            <button type="submit" class="btn btn-primary">Enviar</button>
        </div>
</form>
    </div>
</div>