<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Menu
{
	var $menu;
	function __construct() {
    }
    public function init($conf){
    	$p1 = 1;
    	$p2 = 1;
    	$p3 = 1;
    	$p4 = 1;

    	if($p1>0||$p2>0||$p3>0||$p4>0)
    	$this->menu[] = array(
					"name"=>"Vista general",
					"base"=>"Dashboard\/_",
					"icon"=>"glyphicon glyphicon-dashboard",
					"url"=>"Dashboard",
					);
    	if($conf[0]->nivel_acceso > 0)
		$this->menu[] = array(
					"name"=>"Reservas",
					"base"=>"Registro\/paq_",
					"icon"=>"glyphicon glyphicon-cloud",
					"url"=>"Registro/paq_listado/",
					"more"=>"Registro/paq_crear/",
					);
		if($conf[0]->nivel_acceso > 0)
		$this->menu[] = array(
					"name"=>"Registro Auxiliar",
					"base"=>"Registro\/reg_",
					"icon"=>"glyphicon glyphicon-cloud",
					"url"=>"Registro/reg_auxiliar/"
					);
	    
		if($conf[1]->nivel_acceso > 0)
		$this->menu[] = array(
					"name"=>"Hoja de servicio",
					"base"=>"Hservicio\/hser_",
					"icon"=>"glyphicon glyphicon-cloud",
					"url"=>"Hservicio/hser_listado/"
					);
		if($conf[2]->nivel_acceso > 0)
		$this->menu[] = array(
					"name"=>"Ordenes de Servicio",
					"base"=>"Ordenserv\/ord_",
					"icon"=>"glyphicon glyphicon-tasks",
					"url"=>"Ordenserv/ord_listado/",
					);
		if($conf[3]->nivel_acceso > 0)
		$this->menu[] = array(
					"name"=>"Ordenes de Pago",
					"base"=>"Ordenserv\/ordp_",
					"icon"=>"glyphicon glyphicon-tasks",
					"url"=>"Ordenserv/ordp_listadoPago/",
					);
		if($conf[4]->nivel_acceso > 0)
		$this->menu[] = array(
					"name"=>"Comprobantes",
					"base"=>"Comprobante\/comp_",
					"icon"=>"glyphicon glyphicon-shopping-cart",
					"url"=>"Comprobante/comp_listado/",
					);
		if($conf[5]->nivel_acceso > 0)
		$this->menu[] = array(
					"name"=>"Hojas de Liquidacion",
					"base"=>"Liquidacion\/liqu_",
					"icon"=>"glyphicon glyphicon-shopping-cart",
					"url"=>"Liquidacion/liqu_listado/",
					);
		/*
		if($p1>0||$p3>0)
		$this->menu[] = array(
					"name"=>"Cobros",
					"base"=>"Comprobante\/cobr_",
					"icon"=>"glyphicon glyphicon-import",
					"url"=>"Comprobante/cobr_listado"
					);
		if($p1>0||$p4>0)
		$this->menu[] = array(
					"name"=>"Pagos",
					"base"=> "Registro\/pago_",
					"icon"=>"glyphicon glyphicon-export",
					"url"=>"Registro/pago_listado"
					);
		*/
		if($conf[6]->nivel_acceso > 0)
		$this->menu[] = array(
					"name"=>"Caja y Bancos",
					"base"=> "Cuenta\/bank_",
					"icon"=>"glyphicon glyphicon-export",
					"url"=>"Cuenta/bank_cuentas"
					);
		if($conf[6]->nivel_acceso > 0)
		$this->menu[] = array(
					"name"=>"Movimiento Dinero",
					"base"=> "Cuenta\/cuen_",
					"icon"=>"glyphicon glyphicon-export",
					"url"=>"Cuenta/cuen_movimiento"
					);
	    if($conf[7]->nivel_acceso > 0)
		$this->menu[] = array(
					"name"=>"Unidades",
					"base"=>"Flota\/flot_",
					"icon"=>"glyphicon glyphicon-cloud",
					"url"=>"Flota/flot_listado/"
					);
		if($conf[13]->nivel_acceso > 0)
		$this->menu[] = array(
					"name"=>"Contactos",
					"base"=>"Contacto\/clie_",
					"icon"=>"glyphicon glyphicon-briefcase",
					"url"=>"Contacto/clie_listado"
					);
		/*
		if($p1>0||$p2>0||$p3>0||$p4>0)
		$this->menu[] = array(
					"name"=>"Registro",
					"base"=>"Registro\/serv_",
					"icon"=>"glyphicon glyphicon-briefcase",
					"url"=>"Registro/serv_listado"
					);
		*/
		if($conf[8]->nivel_acceso > 0)
		$this->menu[] = array(
					"name"=>"Proveedores",
					"base"=>"Contacto\/prov_",
					"icon"=>"glyphicon glyphicon-send",
					"url"=>"Contacto/prov_listado"
					);
		if($conf[9]->nivel_acceso > 0)
		$this->menu[] = array(
					"name"=>"Hoteles",
					"base"=>"Contacto\/hote_",
					"icon"=>"glyphicon glyphicon-globe",
					"url"=>"Contacto/hote_listado"
					);
		/*
		if($p1>0||$p2>0||$p3>0||$p4>0)
		$this->menu[] = array(
					"name"=>"Guias",
					"base"=>"Contacto\/guia_",
					"icon"=>"glyphicon glyphicon-user",
					"url"=>"Contacto/guia_listado"
					);
					*/
		if($conf[10]->nivel_acceso > 0)
		$this->menu[] = array(
					"name"=>"Servicios",
					"base"=>"Servicio\/serv_",
					"icon"=>"glyphicon glyphicon-user",
					"url"=>"Servicio/serv_listado"
					);
		if($conf[11]->nivel_acceso > 0)
		$this->menu[] = array(
					"name"=>"Reportes",
					"base"=>"Reporte\/repo_",
					"icon"=>"glyphicon glyphicon-stats",
					"url"=>"Reporte/repo_cuadroIngresos"
					);
		if($conf[12]->nivel_acceso > 0)
		$this->menu[] = array(
					"name"=>"Configuración",
					"base"=>"Configuracion\/conf_",
					"icon"=>"glyphicon glyphicon-wrench",
					"url"=>"Configuracion/conf_panel"
					);
		if($conf[15]->nivel_acceso > 0)
		$this->menu[] = array(
					"name"=>"Planilla",
					"base"=>"Planilla\/plan_",
					"icon"=>"glyphicon glyphicon-wrench",
					"url"=>"Planilla/plan_listado"
					);
    }
    public function getArray ()
    {
        return $this->menu;
    }

}