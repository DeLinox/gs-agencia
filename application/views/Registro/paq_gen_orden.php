<div id="page-wrapper" class="page-filter">
    <div class="page-header col-md-12">
        <div class="row page-header-title">
            <div class="col-sm-6">
                <h3><?= $titulo ?> </h3></div>
        </div>
        <div class="row page-header-content">
		</div>
    </div>
    <input type="hidden" id="paqu_id" value="<?= $paquete->paqu_id ?>">
    <div class="page-content">
        <div class="col-md-12">
			<table class="table table-bordered">
				<tr>
					<th>Contacto</th>
					<td><?= $paquete->cliente ?></td>
					<th>Fecha</th>
					<td><?= $paquete->fecha ?></td>
					<td rowspan="3"><?= $paquete->observacion ?></td>
				</tr>
				<tr>
					<th>Nombre Grupo</th>
					<td><?= $paquete->grupo ?></td>
					<th>Moneda</th>
					<td><?= $paquete->moneda ?></td>
				</tr>
				<tr>
					<th>Cantidad</th>
					<td><?= $paquete->pax." pax" ?></td>
					<th>Estado</th>
					<td><?= $paquete->estado ?></td>
				</tr>
			</table>
				
		</div>
		<div class="col-md-6">
			<h3>Servicios del Paquete</h3>
			
			<table class="table table-striped table-bordered" id="paquetes">
				<thead>
					<tr>
						<th><input type="checkbox" name="all[]" class="all" value=""></th>
						<th>DETALLES</th>
						<th>FECHA HORA</th>
						<th>TOTAL</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($detas as $row): ?>
					<tr>
						<td><input type="checkbox" name="sel[]" class="sel" value="<?= $row->deta_id ?>"></td>
						<td><?= $row->deta_descripcion ?></td>
						<td><?= $row->deta_fechaserv ?></td>
						<td><?= $row->deta_precio ?></td>
					</tr>
					<?php endforeach; ?>
				</tbody>
			</table>			
			<button title="Generar Orden de servicio" class="btn btn-primary btn-sm btn-genOrden">Generar Ord. Servicio</button>
		</div>
		<div class="col-md-6">
			<h3>Ordenes de Servicio</h3>
			<table class="table table-striped table-bordered" id="ord_servs">
				<thead>
					<tr>
						<th>NRO</th>
						<th class="col-sm-6">PROVEEDOR - DETALLES</th>
						<th>TOTAL</th>
						<th>ESTADO</th>
						<th>OPC</th>
					</tr>
				</thead>
				<tbody>
					
				</tbody>
			</table>			
			
		</div>
    </div>
</div>