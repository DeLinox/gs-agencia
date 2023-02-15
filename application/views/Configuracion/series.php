<div id="page-wrapper" class="page-filter">
    <div class="page-header col-md-12">
        <div class="row page-header-title">
            <div class="col-sm-6">
                <h3><?= $titulo  ?></h3>
            </div>
        </div>
    </div>
    <div class="page-content">

        <div class="col-md-6 col-md-offset-3">
            <div id="msj-success" class="alert alert-success"></div>
            <div id="msj-danger" class="alert alert-danger"></div>
            <form id="frm-series" action="<?= base_url() ?>configuracion/guardar_series" method="post">
                <table class="table table-bordered">
                    <thead>
                        <th>Sucursales</th>
                        <th>Comprobantes</th>
                        <th>Series</th>
                    </thead>
                    <tbody>
                        <?php 
                        foreach ($sucu_comp as $i => $sc): ?>
                            <tr>     
                                <?php if($i % $comprobantes == 0): ?>
                                <th rowspan="<?= $comprobantes ?>"><?= $sc['sucu_nombre'] ?></th>   
                                <?php endif; ?>
                                <td><?= $sc['comp_nombre'] ?></td>
                                <td><input type="text" class="form-control" name="serie[]" value="<?= $sc['serie']  ?>"></td>
                                <input type="hidden" name="sucu_id[]" value="<?= $sc['sucu_id']  ?>">
                                <input type="hidden" name="comp_id[]" value="<?= $sc['comp_id']  ?>">
                            </tr>    
                        <?php endforeach ?>
                    </tbody>
                </table>
                <button type="submit" class="btn btn-success"><i class="glyphicon glyphicon-floppy-disk"></i> Guardar</button>
            </form>
        </div>
    </div>
</div>