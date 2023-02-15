<div id="page-wrapper" class="page-filter">
    <div class="page-header col-md-12">
        <div class="row page-header-title">
            <input id="tipo_usuario" value="<?= $this->session->userdata('authorizedadmin') ?>" type="hidden">
            <div class="col-sm-6">
                <h3>Vista general </h3>
            </div>
        </div>
        <div class="row page-header-content">
            <div class="col-md-12">
                

            </div>
        </div>
    </div>
    <div class="page-content">
            <div class="col-sm-6">
                <h3>Deudas Soles (S/)</h3>
                <table class="table table-striped table-bordered" id="tbl-generalS">
                    <thead>
                        <?php foreach ($htbl1 as $head): ?>
                            <th><?= $head ?></th>
                        <?php endforeach ?>
                    </thead>
                    <tbody>
                        
                    </tbody>
                </table>
            </div>
            <div class="col-sm-6">
                <h3>Deudas Dolares ($)</h3>
                <table class="table table-striped table-bordered" id="tbl-generalD">
                    <thead>
                        <?php foreach ($htbl1 as $head): ?>
                            <th><?= $head ?></th>
                        <?php endforeach ?>
                    </thead>
                    <tbody>
                        
                    </tbody>
                </table>
            </div>
    </div>
</div>