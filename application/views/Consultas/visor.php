<div class="text-center">
	<h1>Consulta tu Comprobante Electrónico</h1>
	<p class="lead">SERVICIOS TURISTICOS ALL WAYS TRAVEL TITICACA PERU S.A.C.</p>
  </div>
<div class="row">

	<div class="col-xs-offset-2 col-xs-8">
		<div class="text-center">
			<a href="<?php echo base_url()?>Consultas/getpdf/<?php echo $id; ?>/<?php echo $cadena ?>" class="btn btn-success">PDF</a>
			<a href="<?php echo base_url()?>Consultas/xml/<?php echo $id; ?>/<?php echo $cadena ?>" class="btn btn-success">XML</a>
			<a href="<?php echo base_url()?>Consultas/cdr/<?php echo $id; ?>/<?php echo $cadena ?>" class="btn btn-success">CDR</a>
			<a href="#" class="btn btn-success" id="bprint">Imprimir</a>
		</div>
		<br>
		<div class="text-center">
			<iframe src="<?php echo base_url()?>Consultas/pdf/<?php echo $id; ?>/<?php echo $cadena ?>" id="inprint" width="900" height="700"></iframe>
		</div>
		<br>
		<div class="text-center">
			<p>Copyright © 2017 Punored - Todos los derechos reservados</p>
			<p>¿Quieres convertirte en emisor electrónico? Contactenos para mayor información.</p>
			<p><a href="http://gruposistemas.com">www.gruposistemas.com</a></p>
		</div>
	</div>
	
	
</div>

<script>
	$('#bprint').click(function(){
		document.getElementById('inprint').contentWindow.print();
	});
</script>