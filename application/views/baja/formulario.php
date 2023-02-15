<div id="page-wrapper">
    <div class="page-header col-md-12">
        <div class="row page-header-title">
            <div class="col-sm-12">
                <h3><?php echo $titulo; ?> baja de comprobante</h3>
            </div>
        </div>
    </div>
    <div class="page-content fact">
        <div class="col-md-12">
    
    <input type="hidden" id="id" value="<?= $id ?>">
    
	<div class="alert alert-danger error hidden" role="alert">
		<span class="text">Error:</span>
	</div>
    <input type="hidden" id="cta" value="<?php echo $this->configuracion->conf_cta; ?>">
	<form action="<?=base_url()?>Baja/guardar/<?= $id ?>" method="post" id="bajar">
        <div class="row">
            <div class="col-md-6 col-md-offset-3">
                <div class="form-group">
                    <label for="enviofecha">Fecha de comunicación</label>
                    <input id="enviofecha" name="enviofecha" type="text" value="<?= $baja->baja_enviofecha ?>" class="form-control fecha text-right"  />
                </div>

				<div class="row">
					<div class="col-md-2">
						<div class="form-group">
							<label for="enviofecha">Serie</label>
							<input type="text" class="form-control" id="sagregar" placeholder="F001">
						</div>
					</div>
					<div class="col-md-5">
						<div class="form-group">
							<label for="comprobantes">Número</label>
							<input type="text" class="form-control" id="nagregar" placeholder="1">
						</div>
					</div>
					<div class="col-md-2">
						 <div class="form-group">
						    <label for="comprobantes">&nbsp</label>
							<a href="#" class="btn btn-success btn-sm form-control" id="bagregar">Agregar</a>
						 </div>
					</div>
				</div>
				
                <div class="form-group">
                    <label for="comprobantes">Comprobantes</label>
                    <select id="comprobantes" name="comprobantes[]" class="form-control" data='<?=$comps_data?>' init='<?=$comps_init?>' multiple="multiple">
                    </select>
                </div>

				<!--
				<div class="form-group">
                    <label for="onum">ó Dar de baja Numeracion</label>
                    <input id="onum" name="onum" type="text" value="" class="form-control" placeholder="B001-00000001"  />
                </div>
				-->
                <div class="form-group">
                    <label for="descripcion">Descripción</label>
                    <textarea id="descripcion" name="descripcion" class="form-control"><?= $baja->baja_descripcion ?></textarea>
                </div> 

                <button type="submit" value="Guardar" class="btn btn-primary" >
                    <span class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></span> Guardar
                </button>

          </div>
        </div>
    </form>
    </div>
    </div>
</div>