<div id="page-wrapper" class="page-filter">
    <div class="page-header col-md-12">
        <div class="row page-header-title">
            <div class="col-sm-6">
                <h3><?= $titulo  ?></h3></div>
            <div class="col-sm-6 oculto text-right" role="group" aria-label="...">
				<a href="<?php echo base_url() ?>Configuracion/crear_sucursal<?= isset($direccion)?$direccion:'' ?>/#1" title="Crear" class="crearproducto btn btn-danger btn-sm">
                    <i class="fa fa-plus fa-fw"></i>
                    Crear <?= $titulo  ?>
                </a>
            </div>
        </div>
        <div class="row page-header-content">
        	<div class="col-md-12">

    <form class="ocform form-inline">
        <div class="nosel">
            <div class="form-group">
                <input type="text" class="form-control input-sm" name="search[value]" id="filtro" placeholder="Criterio" value="">
            </div>
            <div class="form-group">
                <button type="submit" class="btn btn-primary btn-sm">
                    <i class="glyphicon glyphicon-search"></i>
                    Filtrar
                </button>
            </div>
        </div>
    </form>
    </div>
		</div>
    </div>
    <div class="page-content">
        <div class="col-md-12">
    <?php  echo $this->Model_general->genDataTable('mitabla', $columns, true,true); ?>
    
    </div>
    </div>
</div>