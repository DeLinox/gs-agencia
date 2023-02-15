
<div id="page-wrapper">
    <div class="page-header col-md-12">
        <div class="row page-header-title">
            <div class="col-sm-6">
                <h3><?php echo $titulo; ?></h3>
            </div>
            <?php if($this->editar > 1): ?>
            <div class="col-sm-6 text-right">
                <a title="Crear Servicio" class="btn btn-danger btn-sm aditar_serv" href="<?= base_url() ?>registro/crear_servicio"><i class="glyphicon glyphicon-plus"></i> Crear Servicio</a>
            </div>
            <?php endif ?>
        </div>
    </div>
    <div class="page-content serv">
        <div class="col-md-10 col-md-offset-1">
            <div class="alert alert-danger error hidden" role="alert">
                <span class="text">Error:</span>
            </div>
            <div class="col-md-12">
                <h3>SERVICIOS ACTUALES</h3>
                <table class="table table-striped table-bordered" id="servicios">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>NOMBRE</th>
                            <th>CODIGO</th>
                            <th>HORA</th>
                            <th>TIPO</th>
                            <th class="col-sm-2">OPCIONES</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($servicios as $i => $co): ?>
                            <tr>
                                <td><?= $i+1 ?></td>
                                <td><?= $co->nombre ?></td>
                                <td><?= $co->abrev ?></td>
                                <td><?= $co->hora ?></td>
                                <td><?= $co->tipo ?></td>
                                <td>
                                    <?php if($this->editar > 1): ?>
                                    <a title="Editar: <?= $co->nombre ?>" class="btn btn-primary btn-sm aditar_serv" href="<?= base_url() ?>registro/crear_servicio/<?= $co->id ?>"><span class="glyphicon glyphicon-pencil"></span> Editar</a>
                                    <a href="<?= base_url() ?>servicio/eliminar/<?= $co->id ?>" class="btn btn-danger btn-sm eliminar"><span class="glyphicon glyphicon-trash"></span> Eliminar</a>
                                    <?php endif ?>
                                </td>
                            </tr>
                        <?php endforeach ?>
                    </tbody>
                </table>    
            </div>
        </div>
    </div>
</div>
