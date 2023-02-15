<div id="page-wrapper" class="page-filter">
    <div class="page-header col-md-12">
        <div class="row page-header-title">
            <div class="col-sm-6">
                <h3>Comunicación de bajas</h3></div>
            <div class="col-sm-6 text-right" role="group" aria-label="...">
                    <a href="<?php echo base_url()?>Baja/crear/#1" title="Crear" class="crear btn btn-danger btn-sm">
                    <i class="fa fa-plus fa-fw"></i>
                    Registrar una comunicacion de baja
                </a>
            </div>
        </div>
        <div class="page-header-content">
    <form class="ocform form-inline">
        <div class="form-group">
            <input type="text" class="form-control input-sm" name="search[value]" id="filtro" placeholder="Cliente" value="">
        </div>
        <div class="form-group">
            <?=form_dropdown('comprobantes', $comprobantes,'',array('class' => 'form-control input-sm'));?>
        </div>

        
        <input type="hidden" name="desde" id="desde"/>
        <input type="hidden" name="hasta" id="hasta"/>
        <div class="form-group">
            <div id="reportrange" class="pull-right" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc;">
                <i class="glyphicon glyphicon-calendar fa fa-calendar"></i>&nbsp;
                <span></span> <b class="caret"></b>
            </div>
        </div>
        <!--<div class="form-group">
            <label>SUNAT: </label>
            <?=form_dropdown('archivo', $archivo,'',array('class' => 'form-control input-sm'));?>
        </div>-->
        <div class="form-group">
            <?=form_dropdown('estado', $estado,'',array('class' => 'form-control input-sm'));?>
        </div>
        <div class="form-group">
            <button type="submit" class="btn btn-primary btn-sm">
                <i class="glyphicon glyphicon-search"></i>
                Filtrar
            </button>
        </div>
    </form>
    </div>
    </div>
    <div class="page-content">
        <div class="col-md-12">
	<?php  echo $this->Model_general->genDataTable('mitabla', $columns, true,true); ?>
</div>
</div>
</div>
<div class="modal fade" id="confirm-archivo" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel">Confirmación</h4>
            </div>
            <div class="modal-body">
                <p>¿Relmente desea generar el archivo?</p>
                <p class="debug-url"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                <a class="btn btn-danger btn-ok">Enviar a la Sunat</a>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="confirm-correo" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel">Confirmación</h4>
            </div>
            <div class="modal-body">
                
                <input class="form-control" type="text" id="corre-confirmar" value="" name="correo">
                
            </div>
            <div class="modal-footer">
                <button  type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger btn-ok">Enviar correo</button>
            </div>
        </div>
    </div>
</div>
<script>

    

</script>