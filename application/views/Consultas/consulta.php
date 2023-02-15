<div class="text-center">
	<h1>Consulta tu Comprobante Electrónico</h1>
	<p class="lead">SERVICIOS TURISTICOS ALL WAYS TRAVEL TITICACA PERU S.A.C.</p>
  </div>
<div class="row">
	
	<div class="col-xs-offset-2 col-xs-8">
	<h3>Individual</h3>
<form class="form-horizontal" method="get" action="<?=base_url()?>Consultas/consultar">
  <div class="form-group form-group-lg">
    <label for="comprobante" class="col-xs-3 control-label">Email</label>
    <div class="col-xs-8">
      <select class="form-control" id="comprobante" name="comprobante">
		<option value="">Seleccione un tipo de comprobante</option>
		<option value="01">Factura</option>
		<option value="03">Boleta</option>
		<option value="07">Nota de credito</option>
		<option value="08">Nota de debito</option>
	  </select>
    </div>
  </div>
  <div class="form-group form-group-lg">
    <label for="serie" class="col-xs-3 control-label">Serie y Número</label>
    <div class="col-xs-2">
      <input type="text" class="form-control" id="serie" name="serie" placeholder="F001">
    </div>
	<div class="col-xs-3">
      <input type="text" class="form-control" id="numero" name="numero" placeholder="2554">
    </div>
  </div>
  <div class="form-group form-group-lg">
    <label for="fecha" class="col-sm-3 control-label">Fecha</label>
    <div class="col-sm-8">
      <input type="text" class="form-control" id="fecha" name="fecha" placeholder="<?php echo date("d/m/Y"); ?>">
    </div>
  </div>
  <div class="form-group form-group-lg">
    <label for="total" class="col-sm-3 control-label">Monto Total</label>
    <div class="col-sm-8">
      <input type="text" class="form-control" id="total" name="total" placeholder="1102.00">
    </div>
  </div>
  <div class="form-group form-group-lg">
    <div class="col-sm-offset-3 col-sm-10">
      <button type="submit" class="btn btn-success btn-lg">Buscar comprobante</button>
    </div>
  </div>
</form>
</div>

<br>
<br>
	<div class="col-xs-offset-2 col-xs-8">
	<h3>Empresa</h3>
<form class="form-horizontal" method="post" action="<?=base_url()?>Consultas/comprobar">
  <div class="form-group form-group-lg">
    <label for="ruc" class="col-sm-3 control-label">RUC</label>
    <div class="col-sm-8">
      <input type="text" class="form-control" id="ruc" name="ruc" placeholder="00000000000">
    </div>
  </div>
  <div class="form-group form-group-lg">
    <label for="password" class="col-sm-3 control-label">Contraseña</label>
    <div class="col-sm-8">
      <input type="password" class="form-control" id="password" name="password" placeholder="******">
    </div>
  </div>
  <div class="form-group form-group-lg">
    <div class="col-sm-offset-3 col-sm-10">
      <button type="submit" class="btn btn-danger btn-lg">Listar comprobantes</button>
    </div>
  </div>
</form>
</div>
</div>