<div id="page-wrapper" class="page-filter">
    <div class="page-header col-md-12">
        <div class="row page-header-title">
            <input id="tipo_usuario" value="<?= $this->session->userdata('authorizedadmin') ?>" type="hidden">
            <div class="col-sm-6">
                <h3><?php echo $titulo ?></h3>
            </div>
            <div class="col-sm-6 text-right" role="group" aria-label="...">
                    <button class="btn btn-sm btn-success reporte_excel" type="button">
                        <i class="glyphicon glyphicon-save"></i> Excel
                    </button>
            </div>
        </div>
        <div class="row page-header-content">
            <div class="col-md-12">
                <form class="ocform form-inline">  
                    <div class="nosel">
                        <div class="form-group">
                            <div class='input-group'>
                                <input type='text' name="fecha" class="form-control datepicker" id="fecha" placeholder="dd/mm/yyy" value="<?= $fecha ?>" />
                                <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="page-content">
        <?php 
            foreach ($servicio as $i => $serv): 
                if($i % 2 == 0) echo "<div class='row'>";
        ?>
            <div class="col-sm-6">
                <table class="table table-striped table-bordered tbl-serv" id="serv<?php echo $serv->serv_id; ?>">
                    <thead>
                        <tr>
                            <th class="serv<?php echo ($i%4)+1; ?>" colspan="<?php echo count($tbl_head) ?>"><?php echo strtoupper($serv->serv_descripcion); ?></th>
                        </tr>
                        <tr>
                        <?php foreach ($tbl_head as $head): ?>
                            <th><?php echo $head; ?></th>
                        <?php endforeach ?>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        <?php 
            if($i % 2 != 0) echo "</div>";
            endforeach;
        ?>
        <div class="col-sm-6">
            <table class="table table-striped table-bordered tbl-serv" id="servPriv">
                <thead>
                    <tr>
                        <th class="serv3" colspan="<?php echo count($tbl_head) ?>">PRIVADOS</th>
                    </tr>
                    <tr>
                    <?php foreach ($tbl_head as $head): ?>
                        <th><?php echo $head; ?></th>
                    <?php endforeach ?>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>