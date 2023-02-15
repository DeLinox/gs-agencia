<div id="page-wrapper" class="page-filter">
    <div class="page-header col-md-12">
        <div class="row page-header-title">
            <div class="col-sm-6">
                <h3><?php echo $titulo ?> </h3></div>
            
        </div>
        <div class="row page-header-content">
        	<div class="col-md-12">
            <form class="ocform form-inline" id="frm-pend" action="<?= base_url() ?>venta/comp_pendientes" method="POST">
				<div class="nosel">
					<div class="form-group">
						<input type="text" class="form-control input-sm" name="search" id="filtro" placeholder="Cliente" value="">
					</div>
					<div class="form-group">
		                <?=form_dropdown('comprobantes', $comprobantes,'',array('id'=>'comprobantes','class' => 'form-control input-sm'));?>
		            </div>
		            <!--
					<input type="hidden" name="desde" id="desde"/>
					<input type="hidden" name="hasta" id="hasta"/>
					<div class="form-group">
						<div id="reportrange" class="pull-right" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc;">
							<i class="glyphicon glyphicon-calendar fa fa-calendar"></i>&nbsp;
							<span></span> <b class="caret"></b>
						</div>
					</div>
					-->
					<div class="form-group">
						<button type="submit" class="btn btn-primary btn-sm">
							<i class="glyphicon glyphicon-search"></i>
							Filtrar
						</button>
					</div>
					<?php if ($this->session->userdata('authorizedadmin') == 2): ?>
						<div class="form-group">
							<button type="button" class="btn btn-primary btn-sm" id="btn-report-excel">
								<i class="glyphicon glyphicon-download-alt"></i>
								Excel
							</button>
						</div>
					<?php endif; ?>
				</div>
			</form>
		</div>
		</div>
    </div>
    <div class="page-content">
        <div class="col-md-12">
				<div class="alert alert-danger error hidden" role="alert">
					<span class="text">Error:</span>
				</div>
				<table class="table table-striped table-bordered" id="tbl-pendientes">
					<thead>
						<tr>
							<th class="col-sm-1"></th>
							<th>ID</th>
							<th>Cliente</th>
							<th>Número</th>
							<th>Fecha</th>
							<th>Credito</th>
							<th>Plazo</th>
							<th>Saldo</th>
							<th>Total</th>
						</tr>
					</thead>
					<tbody></tbody>
				</table>

				<div class="alert alert-info procesos">

				</div>

				<div class="alert alert-danger mensajes">
					<ul class="list-group">
					</ul>
				</div>
		</div>
    </div>
</div>