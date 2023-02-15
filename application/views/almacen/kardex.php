<div id="page-wrapper" class="page-filter">
    <div class="page-header col-md-12">
        <div class="row page-header-title">
            <div class="col-sm-6">
                <h3>Kardex Costo Promedio</h3></div>
        </div>
        <div class="row page-header-content">
        	<div class="col-md-12">
            <form class="ocform form-inline">
				<div class="nosel">
					<div class="form-group">
						<?= form_dropdown('sucursal', $sucursal, '', array('class' => 'form-control input-sm')); ?>
					</div>
					<div class="form-group">
						<button type="submit" class="btn btn-primary btn-sm">
							<i class="glyphicon glyphicon-search"></i>
							Filtrar
						</button>
					</div>

						<div class="form-group">
							<button type="button" class="btn btn-primary btn-sm" id="btn-report-excel">
								<i class="glyphicon glyphicon-download-alt"></i>
								Excel
							</button>
						</div>
				</div>
			</form>
		</div>
		</div>
    </div>
    <div class="page-content">
        <div class="col-md-12">
        	<table class="table table-bordered">
        		<tr>
        			<th rowspan="2">FECHA</th>
        			<th rowspan="2">DESCRIPCION</th>
        			<th rowspan="2">DOCUMENTO</th>
        			<th rowspan="2">SERIE</th>
        			<th rowspan="2">NUMERO</th>
        			<th rowspan="2">TIPO</th>
        			<th rowspan="2">UNIDAD</th>
        			<th colspan="3">INGRESOS</th>
        			<th colspan="3">SALIDAS</th>
        			<th colspan="3">SALDO</th>
        		</tr>
        		<tr>
        			<th>CANTIDAD</th>
        			<th>COSTO</th>
        			<th>TOTAL</th>
        			<th>CANTIDAD</th>
        			<th>COSTO</th>
        			<th>TOTAL</th>
        			<th>CANTIDAD</th>
        			<th>COSTO</th>
        			<th>TOTAL</th>
        		</tr>
        		<?php foreach($kardex as $row): ?>
        		<tr>
        			<td><?php echo $row->kard_fechareg; ?></td>
        			<td><?php echo $row->kard_prod_id; ?></td>
        			<td><?php echo $row->kard_comp_id; ?></td>
        			<td><?php echo $row->kard_serie; ?></td>
        			<td><?php echo $row->kard_numero; ?></td>
        			<td><?php echo $row->kard_tipo; ?></td>
        			<td><?php echo ""; ?></td>
        			<td><?php echo $row->kard_ing_cantidad; ?></td>
        			<td><?php echo $row->kard_ing_costo; ?></td>
        			<td><?php echo $row->kard_ing_total; ?></td>
        			<td><?php echo $row->kard_egr_cantidad; ?></td>
        			<td><?php echo $row->kard_egr_costo; ?></td>
        			<td><?php echo $row->kard_egr_total; ?></td>
        			<td><?php echo $row->kard_sal_cantidad; ?></td>
        			<td><?php echo $row->kard_sal_costo; ?></td>
        			<td><?php echo $row->kard_sal_total; ?></td>
        		</tr>
        		<?php endforeach; ?>
        	</table>

		</div>
    </div>
</div>