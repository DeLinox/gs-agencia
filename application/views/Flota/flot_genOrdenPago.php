
<div id="page-wrapper">
    <div class="page-header col-md-12">
        <div class="row page-header-title">
            <div class="col-sm-6">
                <h3><?php echo $titulo; ?></h3>
            </div>
        </div>
    </div>
    <div class="page-content serv">
        <div class="col-md-12">
            <div class="alert alert-danger error hidden" role="alert">
                <span class="text">Error:</span>
            </div>
            <form id="frm-gen-orden" class="form-horizontal" action="<?=base_url()?>flota/guardar_ordenPago" method="post">
                <div class="modal-body">
                    <div class="alert alert-danger errorOrd hidden" role="alert">
                        <span class="text">Error:</span>
                    </div>
                        <div class="form-group">
                            <div class="col-sm-9">
                                <?= form_label('<strong>Proveedor</strong>', 'servicio', array('class' => 'control-label')); ?>
                                <input type="hidden" name="prov_id" value="<?= $servicios[0]->prov_id ?>">
                                <input readonly class="form-control input-sm" type="text" name="prov_name" value="<?= $servicios[0]->prov_name ?>">
                            </div>
                            <div class="col-sm-3">
                                <?= form_label('<strong>Orden Numero</strong>', 'numero', array('class' => 'control-label')); ?>
                                <?= form_input(array("name" => "numero", "value" => $numero, "class" => "form-control input-sm", "readonly" => "readonly")); ?>
                            </div>
                            <div class="col-sm-3 hidden">
                                <?= form_label('<strong>Fecha Emision</strong>', 'fecha', array('class' => 'control-label')); ?>
                                <div class="input-group">
                                    <input id="fecha" name="fecha" type="text" class="form-control fecha" value="<?= $fecha ?>"/>
                                    <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                                </div>
                            </div>
                            <div class="col-sm-4 hidden">
                                <input type="hidden" name="moneda" value="<?= $moneda ?>">
                            </div>
                            
                        </div>
                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>FECHA</th>
                                <th>SERVICIO</th>
                                <th>CAPITAN / RESPONSABLE</th>
                                <th>M</th>
                                <th>GALONES</th>
                                <th>PRECIO (GAL)</th>
                                <th>TOTAL</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                                $total_total = 0;
                                foreach ($servicios as $i => $val):   
                                    $total_total += $val->total;
                            ?>
                            <tr>
                                <input type="hidden" name="deta_id[]" value="<?= $val->id ?>">
                                <td><?= $val->fecha ?></td>
                                <td><?= $val->servicio ?></td>
                                <td><?= $val->responsable ?></td>
                                <td><?= ($val->moneda == 'SOLES')?'S/':'$' ?></td>
                                <td class="text-right"><?= $val->cantidad ?></td>
                                <td class="text-right"><?= number_format($val->precio, 2, '.', ' ') ?></td>
                                <td class="text-right"><?= number_format($val->total, 2, '.', ' ') ?></td>
                            </tr>
                            <?php endforeach ?>
                            <tr>
                                <th colspan="6" class="text-right">Total a pagar </th>
                                <th class="text-right"><?= ($val->moneda == 'SOLES'?'S/ ':'$ ').number_format($total_total, 2, '.', ' ') ?></th>
                            </tr>
                        </tbody>
                    </table>
                    <input type="hidden" name="total" value="<?= $total_total ?>">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </div>
            </form>

        </div>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function(){
        $('form').submit(function(e){
            e.preventDefault();
            $.gs_loader.show();
            $.ajax({
                dataType: "json",
                url: $(this).attr("action"),
                type: $(this).attr("method"),
                data: $(this).serialize(),
                success: function(data){
                    $.gs_loader.hide();
                    if(data.exito){
                        alert(data.mensaje);
                        window.location=baseurl+'Flota/flot_listado'; 
                    }else{
                        $(".error").html(data.mensaje);
                        $(".error").removeClass("hidden");
                    }
                },

            })
            return false;
        })
    })
</script>

