<!DOCTYPE html>
<html lang="es">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
		<title>Panel de inicio - MCTramite</title>
		<link rel="stylesheet" href="<?= base_url() ?>assets/plg/bootstrap-3.3.7-dist/css/bootstrap.min.css" />
        <link rel="stylesheet" href="<?= base_url() ?>assets/plg/bootstrap-3.3.7-dist/css/bootstrap-theme.min.css" />
        <link rel="stylesheet" href="<?= base_url() ?>assets/plg/DataTables-1.10.13/css/dataTables.bootstrap.min.css">
		<link rel="<?= base_url() ?>assets/plg/bootstrap-3.3.7-dist/css/buttons.dataTables.min.css">
        <link rel="stylesheet" href="<?= base_url() ?>assets/plg/daterangepicker/daterangepicker.css">
        <link rel="stylesheet" href="<?= base_url() ?>assets/plg/select2-4.0.3/dist/css/select2.min.css" />
        <link rel="stylesheet" href="<?= base_url() ?>assets/css/datepicker.css" />
        <link rel="stylesheet" href="<?= base_url() ?>assets/plg/datetimepicker/build/css/bootstrap-datetimepicker.min.css" />
        <link rel="stylesheet" href="<?= base_url() ?>assets/plg/multiselect/css/bootstrap-multiselect.css" />
		<link href="<?=base_url() ?>assets/css/estilo.css?v=2.5" rel="stylesheet" media="all">
		<?php 
			$css = $this->cssjs->generate_css();
			echo isset($css)?$css:""; 
		?>
		<script src="<?= base_url() ?>assets/plg/jquery-1.11.3.min.js"></script>
        <script src="<?= base_url() ?>assets/plg/jquery.autosize.min.js"></script>
        <script src="<?= base_url() ?>assets/plg/bootstrap-3.3.7-dist/js/bootstrap.min.js"></script>
        <script src="<?= base_url() ?>assets/plg/DataTables-1.10.13/js/jquery.dataTables.min.js"></script>
		<script src="<?= base_url() ?>assets/plg/DataTables-1.10.13/js/dataTables.buttons.min.js"></script>
		<script src="<?= base_url() ?>assets/plg/DataTables-1.10.13/js/jszip.min.js"></script>
		<script src="<?= base_url() ?>assets/plg/DataTables-1.10.13/js/buttons.html5.min.js"></script>
        <script src="<?= base_url() ?>assets/plg/DataTables-1.10.13/js/dataTables.bootstrap.min.js"></script>
        <script src="<?= base_url() ?>assets/plg/select2-4.0.3/dist/js/select2.js"></script>
        <script src="<?= base_url() ?>assets/plg/daterangepicker/moment.min.js"></script>
        <script src="<?= base_url() ?>assets/plg/daterangepicker/daterangepicker.js"></script>
        <script src="<?= base_url() ?>assets/plg/datetimepicker/build/js/bootstrap-datetimepicker.min.js"></script>
		<script src="<?=base_url() ?>assets/plg/datepicker.js"></script>
		<script src="<?=base_url() ?>assets/plg/multiselect/js/bootstrap-multiselect.js"></script>
		<script src="<?=base_url() ?>assets/js/comun.js?v=2.3"></script>
		<script src="<?=base_url() ?>assets/js/scripts.js?v=2"></script>
		<script src="<?=base_url() ?>assets/js/multiselect.js?v=2"></script>
		<?php 
			$js = $this->cssjs->generate_js();
			echo isset($js)?$js:""; 
		?>
	</head>
	<body>	
	