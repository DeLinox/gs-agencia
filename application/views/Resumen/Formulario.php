
<div id="page-wrapper">
    <div class="page-header col-md-12">
        <div class="row page-header-title">
            <div class="col-sm-12">
                <h3><?php echo $titulo; ?> resumenes de boletas</h3>
            </div>
        </div>
    </div>
    <div class="page-content fact">
        <div class="col-md-12">

            <form action="<?=base_url()?>Resumen/guardar/<?= $id ?>" method="post" id="subir">
                <div class="row">
                    <div class="col-md-6 col-md-offset-3">
                        <div class="form-group">
                            <label for="enviofecha">Fecha de comunicación</label>
                            <input id="enviofecha" name="enviofecha" type="text" value="<?= $resumen->resu_enviofecha ?>" class="form-control fecha text-right"  />
                        </div>

                        <div class="form-group">
                            <label for="comprobantes">Comprobantes</label>
                            <select id="comprobantes" name="comprobantes[]" class="form-control" data='<?=$comps_data?>' init='<?=$comps_init?>' multiple="multiple">
                            </select>
                        </div>

         
                        <button type="submit" value="Guardar" class="btn btn-success" >
                            <span class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></span> Guardar
                        </button>

                  </div>
                </div>
            </form>
            <input type="hidden" id="cta" value="<?php echo $this->configuracion->conf_cta; ?>">
            
        </div>
    </div>
</div>
<!-- Elementos clonables -->
