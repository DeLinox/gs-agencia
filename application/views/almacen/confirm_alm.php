
<div class="modal-dialog" role="document">
    <div class="modal-content">
		<form id="frm-pago" class="form-horizontal" action="<?=base_url()?>almacen/alm_guardar/<?= $movimiento->movi_id ?>" method="post">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" id="gridSystemModalLabel"></h4>
			</div>
			<div class="modal-body">
				<div class="alert alert-danger error hidden" role="alert">
					<span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
					<span class="sr-only">Error:</span>
				</div>
				<table class="table table-striped table-bordered">
					<h5><?= strtolower($movimiento->tipo_tipo) ?> de productos</h5>
					<thead>
						<tr>
							<th>Producto</th>
							<th>Cantidad</th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ($productos as $val) {
							echo "<tr>";
							echo "<td>".$val->deta_descripcion."</td>";
							echo "<td>".round($val->deta_cantidad)."</td>";
							echo "</tr>";
						} ?>
					</tbody>
				</table>					

			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
				<button type="submit" class="btn btn-primary">Guardar</button>
			</div>
		</form>
    </div>
</div>
