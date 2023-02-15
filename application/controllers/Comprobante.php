<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Comprobante extends CI_Controller {
	var $configuracion;
	function __construct() {
		parent::__construct();
		if(!$this->session->userdata('authorized')){
			redirect(base_url()."login");
		}
		$this->usua_id = $this->session->userdata('authorized');
        $this->configuracion = $this->db->query("SELECT * FROM usuario WHERE usua_id='{$this->usua_id}'")->row();
        $this->load->model("Model_general");
        $this->permisos  = $this->Model_general->getPermisos($this->usua_id);
        $this->editar = $this->permisos[4]->nivel_acceso;
		$this->load->library('Cssjs');
		$this->load->model("Model_general");
		$this->load->helper('Form');
	}
	
	
	
	function migrar_guardar(&$edb,$tabla, $datas) {
        if (isset($datas)) {
            $edb->set($datas);
            $edb->insert($tabla);
            $id = $edb->insert_id();
            if ($edb->trans_status() === FALSE) {
                return FALSE;
            } else {
                return $datos = array("id" => $id);
            }
        } else {
            return FALSE;
        }
    }
	
	
	function migrar($id){
		/*$edb = $this->load->database('efactura', TRUE);
		$cab = $this->db->query("SELECT * FROM venta WHERE vent_id='{$id}'")->row();
		
		$erow = $edb->query("SELECT * FROM venta WHERE vent_serie='{$cab->vent_serie}' AND vent_numero='{$cab->vent_numero}'")->row();
	
		
		//if(isset($erow->vent_id)) die("ya existe un registro en factura.");
		
		$detalle = $this->db->query("SELECT *, DATE_FORMAT(deta_fechaserv, '%d/%m/%Y') fecha 
									FROM venta_detalle WHERE deta_vent_id='{$id}' ORDER BY deta_fechaserv ASC")->result();
		
		
		$serie = $cab->vent_serie;
		$numero = str_pad($cab->vent_numero, 8, "0", STR_PAD_LEFT);
		$file = "20448725791-{$serie}-{$numero}";
		
		$factura = array("vent_clie_rsocial" => $cab->vent_clie_rsocial,
							"vent_clie_direccion" => $cab->vent_clie_direccion,
		                    "vent_clie_email" => $cab->vent_clie_email,
		                    "vent_desc_global" => '0',//$cab->vent_desc_global,
							"vent_fecha"=> $cab->vent_fecha,
							"vent_comp_id" => $cab->vent_tcom_id,
		                    "vent_nc_comp_id" => null,//$cab->vent_nc_comp_id,
							"vent_serie"=> $cab->vent_serie,
		                    "vent_nc_serie"=> null,//$cab->vent_nc_serie,
							"vent_numero" => $cab->vent_numero,
		                    "vent_nc_numero" => null,//$cab->vent_nc_numero,
		                    "vent_nc_debi_id" => null,//$cab->vent_nc_debi_id,
		                    "vent_nc_cred_id" => null,//$cab->vent_nc_cred_id,
							"vent_moneda" => $cab->vent_moneda,
							"vent_clie_docu_id"=> $cab->vent_clie_tdoc_id,
							"vent_total"=> $cab->vent_total,
							"vent_valor"=> $cab->vent_subtotal,//$cab->vent_valor,
		                    "vent_file"=> $file,
							"vent_subtotal"=> $cab->vent_subtotal,
							"vent_descuento"=> '0',//$cab->vent_descuento,
							"vent_igv"=> $cab->vent_igv,
							"vent_clie_id"=> $cab->vent_clie_id,
		                    "vent_detraccion"=> 'NO',//$cab->vent_detraccion, /////////////////////// observado
		                    "vent_exterior"=> 'NO',//$cab->vent_exterior, ////////////////////////// observado
		                    "vent_descripcion"=> $cab->vent_obs,//$cab->vent_descripcion,
		                    "vent_gravada"=> $cab->vent_subtotal,//$cab->vent_gravada,   ///////////////////////////////
		                    "vent_exonerada"=> '0',//$cab->vent_exonerada,  /////////////////////
		                    "vent_exportacion"=> '0',//$cab->vent_exportacion,  //////////////////////
		                    "vent_inafecta"=> '0',//$cab->vent_inafecta,  /////////////////////////
		                    //"vent_genera_archivo"=> null,//$cab->vent_genera_archivo,
							"vent_clie_num_documento" => $cab->vent_clie_tdoc_nro,
							"vent_fact_situ" => "1",
							"vent_fact_gsitu" => "1"
							);
		
		$edb->trans_begin();
		if (($meta = $this->migrar_guardar($edb,"venta", $factura)) == TRUE):
			$vent_id = $meta['id'];
			foreach($detalle as $det){
			    $cantidad = ($cab->vent_tipo == "PRIVADO")?1:$det->deta_pax;
			    $valor = $det->deta_valor/$cantidad;
			    $precio = ($cab->vent_tipo == "PRIVADO")?$det->deta_total:$det->deta_fprecio;
				$item = array("deta_vent_id" => $vent_id,
							"deta_descripcion" => $det->deta_serv_name." - ".$det->deta_descripcion." - ".$det->deta_file." - ".$det->fecha,
							"deta_cantidad" => $cantidad,
							"deta_valor" => $valor,
							"deta_unidad"=>'ZZ',
							"deta_precio" => $precio,
							"deta_descuento" => '0',///////////
							"deta_afec_id" => '10',//$det->deta_afec_id,
							"deta_esgratuita" => 'NO',//$det->deta_esgratuita,
							"deta_igv" => $det->deta_igv,
							"deta_importe" => $det->deta_total,
							"deta_prod_id" => 3 //$det->deta_serv_id
					);
				if($this->migrar_guardar($edb,"venta_detalle", $item)==FALSE){
					echo "error al registrar detalle";
					$edb->trans_rollback();
				}
			}
			$this->Model_general->guardar_edit_registro("venta",array("vent_esfacturador" => '1', "vent_idFacturador" => $vent_id),array("vent_id" => $id));
			
			$edb->trans_commit();
			$resp["exito"] = true;
			$resp["mensaje"] = "Comprobante enviado con exito";
		else:
			$edb->trans_rollback();
			$resp["exito"] = false;
			$resp["mensaje"] = "Ocurrio un error, intentenlo mas tarde";
		endif;
		echo json_encode($resp);*/

        $res["exito"] = false;
        $res["mensaje"] = "No realizar se puede realizar esta accion en pruebas";
        
        echo json_encode($res);

	}
    function comp_listado() {
        $this->load->helper('Funciones');
        $this->load->library('Ssp');
        $this->load->library('Cssjs');

        $json = isset($_GET['json']) ? $_GET['json'] : false;
        $fecha = 'DATE_FORMAT(vent_fecha,"%d/%m/%Y")';
		$fechafact = 'DATE_FORMAT(vent_fechafactura,"%d/%m/%Y")';
        $fechacobro = 'DATE_FORMAT(vent_cobrofecha,"%d/%m/%Y")';
        $serie = 'CONCAT(vent_serie, "-",vent_numero)';
        //$saldo = "IF(vent_escobrado = '0',CONCAT('<font class=red><b>',(vent_total - vent_cobrado)),'<font class=green><b>COBRADO')";
        $saldo = "IF(vent_escobrado = '0',CONCAT('<font class=red><b>',(vent_total - vent_cobrado)),CONCAT('<font class=green><b>',IF((vent_total - vent_cobrado) < 0,(vent_total - vent_cobrado),'COBRADO')))";
		$facturador = "IF(vent_esfacturador = '0',CONCAT('<font class=red><b>','Sin enviar'),CONCAT('<font class=green><b>','Enviado'))";

        $columns = array(
             array('db' => 'vent_id',            'dt' => 'ID',           "field" => "vent_id"),
            array('db' => $fecha,               'dt' => 'FECHA',        "field" => $fecha),
            array('db' => $serie,               'dt' => 'SERIE',        "field" => $serie),
            array('db' => 'vent_clie_rsocial',  'dt' => 'CLIENTE',      "field" => "vent_clie_rsocial"),
            array('db' => 'tdoc_nombre',        'dt' => 'DOCUMENTO',    "field" => "tdoc_nombre"),
            array('db' => 'vent_clie_tdoc_nro', 'dt' => 'NUMERO',       "field" => "vent_clie_tdoc_nro"),
			array('db' => 'vent_moneda',        'dt' => 'MONEDA',       "field" => 'vent_moneda'),
            array('db' => 'vent_total',         'dt' => 'TOTAL',        "field" => 'vent_total'),
            array('db' => 'vent_cobrado',       'dt' => 'COBRADO',      "field" => 'vent_cobrado'),
            array('db' => $saldo,               'dt' => 'SALDO',        "field" => $saldo),
			array('db' => 'vent_numfactura',    'dt' => 'NRO FACTURA',	"field" => 'vent_numfactura'),
			array('db' => $fechafact,     		'dt' => 'FECHA FACTURA',"field" => $fechafact),
			array('db' => $facturador,               'dt' => 'FACTURADOR',"field" => $facturador),
            array('db' => 'vent_obs',           'dt' => 'OBSERVACIONES',"field" => 'vent_obs'),
            array('db' => $fechacobro,    		'dt' => 'FECHA COBRO',  "field" => $fechacobro),
            array('db' => "vent_cobrodesc",    'dt' => 'FECHA DESC',    "field" => 'vent_cobrodesc'),
            //array('db' => 'vent_total - IF(SUM(cobr_monto) <> "",SUM(cobr_monto),0)', 'dt' => 'Saldo', "field" => 'vent_total - IF(SUM(cobr_monto) <> "",SUM(cobr_monto),0)'),
            array('db' => 'vent_id',            'dt' => 'DT_RowId',     "field" => "vent_id"),
			array('db' => 'vent_idFacturador',  'dt' => 'DT_CompId',    "field" => "vent_idFacturador"),
            array('db' => 'vent_escobrado',     'dt' => 'DT_RowCobr',   "field" => "vent_escobrado"),
			array('db' => 'vent_cobrado',     'dt' => 'DT_RowCobrado',   "field" => "vent_cobrado"),
            array('db' => 'vent_esliquidacion', 'dt' => 'DT_Liquidacion',"field" => "vent_esliquidacion"),
			array('db' => 'vent_esfacturador',  'dt' => 'DT_RowFacturador',"field" => "vent_esfacturador"),
            array('db' => $this->editar,     	'dt' => 'DT_Permisos',	"field" => $this->editar),
			array('db' => $this->usua_id,            'dt' => 'DT_UsuaId',     "field" => $this->usua_id)
        );

        if ($json) {

            $json = isset($_GET['json']) ? $_GET['json'] : false;

            $table = 'venta';
            $primaryKey = 'vent_id';

            $sql_details = array(
                'user' => $this->db->username,
                'pass' => $this->db->password,
                'db' => $this->db->database,
                'host' => $this->db->hostname
            );

            $condiciones = array();

            $joinQuery = "FROM venta LEFT JOIN documento_tipo ON tdoc_id = vent_clie_tdoc_id";
            $where = "";
            if (!empty($_POST['desde'])&&!empty($_POST['hasta'])){
                $condiciones[] = "vent_fecha >='".$_POST['desde']."' AND vent_fecha <='".$_POST['hasta']."'";
            }
            
            if (!empty($_POST['moneda']))
                $condiciones[] = "vent_moneda='".$_POST['moneda']."'";

            if (!empty($_POST['cliente']))
                $condiciones[] = "vent_clie_id='".$_POST['cliente']."'";
            
            $group = "vent_id";
            $where = count($condiciones) > 0 ? implode(' AND ', $condiciones) : "";
            echo json_encode(
                    $this->ssp->simple($_POST, $sql_details, $table, $primaryKey, $columns, $joinQuery, $where, $group)
            );
            exit(0);
        }
        
        $datos["moneda"] = array_merge(array(''=>'* Monedas'),$this->Model_general->enum_valores('venta','vent_moneda'));
        $datos['columns'] = $columns;
        $datos["contacto"] = $this->Model_general->getOptions('cliente', array("clie_id", "clie_rsocial"),'* Contacto');

        $this->cssjs->add_js(base_url().'assets/js/Comprobante/comp_listado.js?v=3.7',false,false);
        $this->load->view('header');
        $this->load->view('menu');
        $this->load->view($this->router->fetch_class().'/'.$this->router->fetch_method(), $datos);
        $this->load->view('footer');
    }
	function cobr_listado() {
		$this->load->helper('Funciones');
        $this->load->library('Ssp');
        $this->load->library('Cssjs');

        $json = isset($_GET['json']) ? $_GET['json'] : false;

        $columns = array(
            array('db' => 'cobr_id',            'dt' => 'ID',			"field" => "cobr_id"),
            array('db' => 'IF(cobr_liqu_id IS NULL,vent_clie_rsocial, liqu_clie_rsocial)',  'dt' => 'Cliente',      "field" => "IF(cobr_liqu_id IS NULL,vent_clie_rsocial, liqu_clie_rsocial)"),
            array('db' => 'IF(cobr_liqu_id IS NULL,CONCAT(vent_serie," - ",vent_numero), CONCAT("LIQ-",liqu_numero))', 'dt' => 'Numero',"field" => 'IF(cobr_liqu_id IS NULL,CONCAT(vent_serie," - ",vent_numero), CONCAT("LIQ-",liqu_numero))'),
            array('db' => 'cobr_monto',         'dt' => 'Monto',     	"field" => "cobr_monto"),
            array('db' => 'cobr_moneda',    	'dt' => 'Moneda',       "field" => "cobr_moneda"),
            array('db' => 'DATE_FORMAT(cobr_fechareg,"%d/%m/%Y %h:%i %p")', 'dt' => 'Fecha', "field" => 'DATE_FORMAT(cobr_fechareg,"%d/%m/%Y %h:%i %p")'),
            array('db' => 'cobr_observacion',   'dt' => 'Observacion',  "field" => "cobr_observacion"),
            array('db' => 'cobr_id',        	'dt' => 'DT_RowId',     "field" => "cobr_id")
        );

        if ($json) {

            $json = isset($_GET['json']) ? $_GET['json'] : false;

            $table = 'cobro';
            $primaryKey = 'cobr_id';

            $sql_details = array(
                'user' => $this->db->username,
                'pass' => $this->db->password,
                'db' => $this->db->database,
                'host' => $this->db->hostname
            );

            $condiciones = array();

            $joinQuery = "FROM cobro LEFT JOIN liquidacion ON liqu_id = cobr_liqu_id LEFT JOIN venta ON vent_id = cobr_comp_id";
            $where = "";
            if (!empty($_POST['desde'])&&!empty($_POST['hasta'])){
                $desde = $_POST['desde']." 00:00:00";
                $hasta = $_POST['hasta']." 23:59:00";
                $condiciones[] = "cobr_fechareg >='".$desde."' AND cobr_fechareg <='".$hasta."'";
            }
            if (!empty($_POST['cliente']))
                $condiciones[] = "liqu_clie_id='".$_POST['cliente']."'";
            /*
            if (!empty($_POST['moneda']))
                $condiciones[] = "paqu_moneda='".$_POST['moneda']."'";

            if (!empty($_POST['estado']))
                $condiciones[] = "paqu_estado='".$_POST['estado']."'";
            */
            
            $where = count($condiciones) > 0 ? implode(' AND ', $condiciones) : "";
            echo json_encode(
                    $this->ssp->simple($_POST, $sql_details, $table, $primaryKey, $columns, $joinQuery, $where)
            );
            exit(0);
        }
        
        $datos["moneda"] = array_merge(array(''=>'* Monedas'),$this->Model_general->enum_valores('venta','vent_moneda'));
        $datos['columns'] = $columns;
        $datos["contacto"] = $this->Model_general->getOptions('cliente', array("clie_id", "clie_rsocial"),'* Contacto');

        $this->cssjs->add_js(base_url().'assets/js/Comprobante/cobr_listado.js',false,false);
        $this->cssjs->add_js(base_url().'assets/js/calendar.js',false,false);
        $this->load->view('header');
        $this->load->view('menu');
        $this->load->view($this->router->fetch_class().'/'.$this->router->fetch_method(), $datos);
        $this->load->view('footer');
	}
	public function nextnum($serie){
        $this->db->select('MAX(vent_numero) as max');
        $this->db->from('venta');
        $this->db->where("vent_serie='{$serie}'");
        $query = $this->db->get();
        $row = $query->row();
        $numero = $row->max+1;
        return $numero;
    }
	
	public function getnext($serie){
		echo json_encode(array('numero'=>$this->nextnum($serie)));
	}
	public function getSerie($comp){
        $sucu = $this->db->SELECT("suus_suco_id sucu_id")->FROM("sucursal_usuario")->WHERE('suus_usua_id', 2)->get();
        if($sucu->num_rows() > 0){
            $sucu = $sucu->row();
            $serie = $this->SELECT("suco_serie")->FROM('sucursal_comprobantes')->WHERE(array("suco_comp_id" => $comp, "suco_suco_id" => $sucu->sucu_id))->get();
            if($serie->num_rows() > 0){
                $serie = $serie->row();
                return $serie->suco_serie;
            }else
                return $this->serieDefault($comp);
        }else
            return $this->serieDefault($comp);
    }

	function comp_form($id = '') {
		
		$this->load->helper('Funciones');

		if($id == ''){
	        $venta = array('vent_id' => '',
	        				 'vent_fecha' => date('d/m/Y'),
	                         'vent_tcom_id' => 1,
	                         'vent_serie' => 'F0001',
	                         'vent_numero' => $this->nextnum('F0001'),
	                         'vent_clie_id' => '',
	                         'vent_clie_direccion' => '',
	                         'vent_clie_rsocial' => '',
	                         'vent_clie_tdoc_id' => "",
	                         'vent_clie_tdoc_nro' => "",
	                         'vent_clie_email' => "",
	                         'vent_moneda' => "SOLES",
	                         'vent_subtotal' => "",
	                         'vent_igv' =>"",
	                         'vent_obs' =>"",
							 'vent_tipo' =>"LOCAL",
	        				 'vent_total' =>""
	       	);
	        $datos["venta"] = (object)$venta;
	        $datos["detas"] = json_encode(array());
        }else{
        	$datos["venta"] = $this->Model_general->getVenta($id);
        	$datos["detas"] = json_encode($this->Model_general->getDetaVenta($id));
        }
        $datos["moneda"] = $this->Model_general->enum_valores('venta','vent_moneda');
	    $datos["comprobantes"] = $this->Model_general->getOptions('comprobante_tipo', array("tcom_id", "tcom_nombre"));
	    $datos["documentos"] = $this->Model_general->getOptions('documento_tipo', array("tdoc_id", "tdoc_nombre"),'* Documento');

        $datos['titulo'] = "Registrar comproante";

        $this->load->library('Ssp');
        $this->load->library('Cssjs');
        $this->cssjs->add_js(base_url().'assets/js/Comprobante/form.js?v=3.1',false,false);
        $this->load->view('header');
        $this->load->view('menu');
        $this->load->view('Comprobante/formulario', $datos);
        $this->load->view('footer');
	}
	function to_form() {

        $seleccionados = $this->input->get('sel');

        //$paquetes = $this->db->query("SELECT * FROM paquete_detalle JOIN paquete ON deta_paqu_id = paqu_id WHERE deta_id IN (".$seleccionados.") ORDER BY deta_id DESC")->result();
        
        $paquetes = $this->db->query("SELECT PD.*, P.paqu_nombre, P.paqu_file, P.paqu_igvafect FROM paquete_detalle as PD JOIN paquete as P ON paqu_id = deta_paqu_id WHERE deta_paqu_id IN (".$seleccionados.")")->result();
       
        $paquete = $this->db->select("DATE_FORMAT(paqu_fecha, '%d/%m/%Y') as fecha, paqu_moneda, paqu_clie_id, paqu_tipo")->where("paqu_id", $paquetes[0]->deta_paqu_id)->get("paquete")->row();

        $detas = array();
        foreach ($paquetes as $i => $val) {
            $serv = $this->db->where("serv_id", $val->deta_serv_id)->get("servicio")->row();
            
            $adiciones = $this->adicionales($val->deta_id, 'ADICION');
            $descuentos = $this->adicionales($val->deta_id, 'DESCUENTO');

            $detas[$i]['deta_id'] = '';
            $detas[$i]['deta_vent_id'] = '';
            $detas[$i]['deta_pdet_id'] = $val->deta_id;
            $detas[$i]['deta_descripcion'] = $val->paqu_nombre;
            $detas[$i]['deta_pax'] = $val->deta_pax;
            $detas[$i]['deta_precio'] = $val->deta_precio;
			$detas[$i]['deta_fprecio'] = "";
			$detas[$i]['deta_file'] = $val->paqu_file;
            $detas[$i]['deta_serv_id'] = $val->deta_serv_id;
            $detas[$i]['deta_serv_name'] = $serv->serv_descripcion;
            $detas[$i]['deta_hote_id'] = $val->deta_hote_id;
            $detas[$i]['deta_valor'] = "0.00";
            $detas[$i]['deta_hotel'] = $val->deta_hotel;
            $detas[$i]['deta_total'] = $val->deta_total;
            $detas[$i]['deta_fechaserv'] = date('d/m/Y', strtotime($val->deta_fechaserv));
            $detas[$i]['deta_lunch'] = $val->deta_lunch;
            $detas[$i]['deta_igv'] = '';
            $detas[$i]['deta_lunch_efect'] = $val->deta_lunch;
            $detas[$i]['deta_lunch_prec'] = $val->deta_lunch_pre;
            
            $detas[$i]['deta_adic'] = ($adiciones)?$adiciones->descripcion:'';
            $detas[$i]['deta_adic_val'] = ($adiciones)?$adiciones->monto:0;
            $detas[$i]['deta_desc'] = ($descuentos)?$descuentos->descripcion:'';
            $detas[$i]['deta_desc_val'] = ($descuentos)?$descuentos->monto:0;
        }
        $cliente = $this->db->where("clie_id", $paquete->paqu_clie_id)->get("cliente")->row();
        $venta = new stdClass();
        $venta->vent_id = '';
        $venta->vent_fecha = date("d/m/Y");
        $venta->vent_tcom_id = 1;
        $venta->vent_moneda = $paquete->paqu_moneda;
        $venta->vent_serie = 'F001';
        $venta->vent_numero = $this->nextnum('F001');
        $venta->vent_clie_id = $paquete->paqu_clie_id;
        $venta->vent_clie_direccion = $cliente->clie_direccion;
        $venta->vent_clie_rsocial = $cliente->clie_rsocial;
        $venta->vent_clie_tdoc_id = $cliente->clie_tdoc_id;
        $venta->vent_clie_tdoc_nro = $cliente->clie_doc_nro;
        $venta->vent_clie_email = $cliente->clie_email;
        $venta->vent_subtotal = '';
        $venta->vent_igv = '';
        $venta->vent_obs = '';
        $venta->vent_total = '';
		$venta->vent_tipo = $paquete->paqu_tipo;

        $datos["moneda"] = $this->Model_general->enum_valores('ordenserv','orde_moneda');
        $datos["comprobantes"] = $this->Model_general->getOptions('comprobante_tipo', array("tcom_id", "tcom_nombre"));
        $datos["documentos"] = $this->Model_general->getOptions('documento_tipo', array("tdoc_id", "tdoc_nombre"),'* Documento');
        $datos["detas"] = json_encode($detas); 
        $datos["venta"] = $venta;
        $datos['titulo'] = "Generar comproante";

        $this->load->library('Ssp');
        $this->load->library('Cssjs');
        $this->cssjs->add_js(base_url().'assets/js/Comprobante/form.js?v=3.1',false,false);
        $this->load->view('header');
        $this->load->view('menu');
        $this->load->view('Comprobante/formulario', $datos);
        $this->load->view('footer');
	}
    public function adicionales($id, $tipo){
        $this->db->select("GROUP_CONCAT(padi_descripcion) as descripcion, SUM(padi_monto) as monto");
        $this->db->from("paquete_adicion");
        $this->db->where(array('padi_pdet_id' => $id, 'padi_tipo' => $tipo));
        $this->db->group_by("padi_pdet_id");
        return $this->db->get()->row();
    }
	private function validarComprobante(){
        $this->load->library('Form_validation');
        $this->form_validation->set_rules('serie', 'Serie', 'required');
        $this->form_validation->set_rules('numero', 'Número', 'required');
        /*
        if(in_array($this->input->post('comprobante'),array(1))){
            $this->form_validation->set_rules('rsocial', 'Razon Social', 'required');
            $this->form_validation->set_rules('direccion', 'Dirección', 'required');
            $this->form_validation->set_rules('docnum', 'Número de documento', 'required');
            if($this->input->post('comprobante')=="01"){
                $this->form_validation->set_rules('docnum', 'Número de documento', 'required|exact_length[11]');
                $this->form_validation->set_rules('documento', 'Tipo de documento', 'regex_match[/6/]',array('regex_match'=>"El tipo de documento tiene que ser RUC"));
            }
        }
        */
        $this->form_validation->set_rules('nombre_grupo[]', 'Nombre / Grupo', 'required');
        
        if ($this->form_validation->run() == FALSE){        
            $this->Model_general->dieMsg(array('exito'=>false,'mensaje'=>validation_errors()));
        }
    }
	function guardar($id = ''){
		
		$this->load->helper('Funciones');

        $this->validarComprobante();        
        $comprobante = $this->input->post('comprobante');
        $serie = $this->input->post('serie');
        $numero = str_pad($this->input->post('numero'), 8, "0", STR_PAD_LEFT);
		$moneda = $this->input->post('vent_moneda');
        $fecha = $this->input->post('vent_fecha');
        $documento = $this->input->post('documento');
        $docnum = $this->input->post('docnum');
		$rsocial = $this->input->post('rsocial');
		$direccion = $this->input->post('direccion');
        $email = $this->input->post('email');
		$total = $this->input->post('total_total');
		$total_igv = $this->input->post('total_igv');
		$sub = $this->input->post('total_sub');
		$id_cliente = $this->input->post('clie_id');
		$obs = $this->input->post('observaciones');
		$tiposev = $this->input->post('tipo');
		$seleccionados = explode(",",$this->input->post('seleccionados'));
        /**/

		$factura = array("vent_fecha"=> $this->Model_general->fecha_to_mysql($fecha),
						"vent_tcom_id" => $comprobante,
						"vent_serie"=> $serie,
						"vent_numero" => $numero,
						"vent_clie_id"=> $id_cliente,
						"vent_clie_direccion" => $direccion,
						"vent_clie_rsocial" => $rsocial,
						"vent_clie_tdoc_id"=> $documento,
						"vent_clie_tdoc_nro" => $docnum,			
                        "vent_clie_email" => $email,
                        "vent_moneda" => $moneda,
                        "vent_subtotal"=> $sub,
                        "vent_usua"=> $this->usua_id,
                        "vent_igv"=> $total_igv,
                        "vent_obs"=> $obs,
                        "vent_total"=> $total,
						"vent_tipo"=> $tiposev
        );

        $deta_pdet_id = $this->input->post('deta_pdet_id');
        $detalle = $this->input->post('nombre_grupo');
        $serv_id = $this->input->post('servicio');
		$serv_nombre = $this->input->post('servicio_nombre');
        $precio = $this->input->post('serv_prec');
		$fprecio = $this->input->post('fprecio');
		$dfile = $this->input->post('file');
        $pax = $this->input->post('pax');
        $lunch = $this->input->post('lunch');
        $lunch_efect = $this->input->post('lunch_efect');
        $lunch_prec = $this->input->post('lunch_prec');
        $adicional = $this->input->post('adicion');
        $adicional_val = $this->input->post('adicion_val');
        $descuento = $this->input->post('descuento');
        $descuento_val = $this->input->post('descuento_val');
        $deta_igv = $this->input->post('igv');
        $deta_total = $this->input->post('total');
        $fecha_serv = $this->input->post('fecha');
        $detalle_id = $this->input->post('deta_id');
        $valor = $this->input->post('valor');
        
		if(empty($id)){
			$this->db->trans_begin();
			if (($meta = $this->Model_general->guardar_registro("venta", $factura)) == TRUE):
			    foreach ($seleccionados as $paqu_id) {
                    $vpaq = array("vent_id" => $meta["id"],
                                    "paqu_id" => $paqu_id
                    );
                    $this->Model_general->guardar_registro("venta_paquete", $vpaq);
					$this->actualizarEscomprobante($paqu_id,1);
                }
	            for ($i=0; $i < count($serv_id); $i++) { 
	            	$item = array("deta_vent_id" => $meta['id'],
	            				"deta_pdet_id" => $deta_pdet_id[$i],
                                "deta_descripcion" => $detalle[$i],
	            				"deta_serv_id" => $serv_id[$i],
								"deta_serv_name" => $serv_nombre[$i],
                                "deta_precio" => $precio[$i],
								"deta_fprecio" => $fprecio[$i],
								"deta_file" => $dfile[$i],
                                "deta_pax" => $pax[$i],
                                "deta_pax" => $pax[$i],
                                "deta_hote_id" => '',
                                "deta_hotel" => '',
                                "deta_lunch" => $lunch[$i],
                                "deta_lunch_efect" => $lunch_efect[$i],
                                "deta_lunch_prec" => $lunch_prec[$i],
                                "deta_adic" => $adicional[$i],
                                "deta_adic_val" => $adicional_val[$i],
                                "deta_desc" => $descuento[$i],
                                "deta_desc_val" => $descuento_val[$i],
                                "deta_igv" => $deta_igv[$i],
                                "deta_total" => $deta_total[$i],
                                "deta_valor" => $valor[$i],
                                "deta_fechaserv" => $this->Model_general->fecha_to_mysql($fecha_serv[$i])
	            		);
                    if($this->Model_general->guardar_registro("venta_detalle", $item)==FALSE){
                        $this->db->trans_rollback();
                        $this->Model_general->dieMsg(array('exito'=>false,'mensaje'=>'Error al guardar los datos'));
                    }
	            }
	        else:
				$this->db->trans_rollback();
                $this->Model_general->dieMsg(array('exito'=>false,'mensaje'=>'Error al guardar los datos'));
	        endif;
	        $this->Model_general->add_log("CREAR",5,"Creacion de Comprobante ".$serie."-".$numero);
            $this->db->trans_commit();
			$id = $meta['id'];           
        }else{
            $condicion_factura = "vent_id = ".$id;
            $this->db->trans_begin();
            if (($meta = $this->Model_general->guardar_edit_registro("venta", $factura, $condicion_factura)) == TRUE):
				/*
                $this->db->select("deta_id, deta_pdet_id");
                $this->db->where('deta_vent_id',$id);
                $this->db->from("venta_detalle");
                $actuales = $this->db->get()->result();
                foreach ($actuales as $key => $value) {
                    if (!in_array($value->deta_id, $detalle_id)) {
	                  	if($this->Model_general->borrar(array("deta_id" => $value->deta_id), 'venta_detalle')){
	                  		if($value->deta_pdet_id != '')
	                  			$this->actualizarEscomprobante($value->deta_pdet_id,0);
	                    }else{
	                    	$this->db->trans_rollback();
	                		$this->Model_general->dieMsg(array('exito'=>false,'mensaje'=>'Error al guardar los datos'));
	                    }
                    }
                }
				*/
                for ($i=0; $i < count($detalle); $i++) {
                  	$condicion_items = "deta_id = ".$detalle_id[$i];
                  	
                    $item = array("deta_vent_id" => $id,
                                "deta_pdet_id" => $deta_pdet_id[$i],
                                "deta_descripcion" => $detalle[$i],
                                "deta_serv_id" => $serv_id[$i],
								"deta_serv_name" => $serv_nombre[$i],
                                "deta_precio" => $precio[$i],
								"deta_fprecio" => $fprecio[$i],
                                "deta_pax" => $pax[$i],
                                "deta_pax" => $pax[$i],
                                "deta_hote_id" => '',
                                "deta_hotel" => '',
                                "deta_lunch" => $lunch[$i],
                                "deta_lunch_efect" => $lunch_efect[$i],
                                "deta_lunch_prec" => $lunch_prec[$i],
                                "deta_adic" => $adicional[$i],
                                "deta_adic_val" => $adicional_val[$i],
                                "deta_desc" => $descuento[$i],
                                "deta_desc_val" => $descuento_val[$i],
                                "deta_igv" => $deta_igv[$i],
                                "deta_total" => $deta_total[$i],
                                "deta_valor" => $valor[$i],
                                "deta_fechaserv" => $this->Model_general->fecha_to_mysql($fecha_serv[$i])
                        );
					/*
                    if(empty($detalle_id[$i])){
                        if($this->Model_general->guardar_registro("venta_detalle", $item) != false){
                        	if($deta_paqu_id[$i] != '')
                        		$this->actualizarEscomprobante($deta_paqu_id[$i],1);
                        }else{
	                    	$this->db->trans_rollback();
	                		$this->Model_general->dieMsg(array('exito'=>false,'mensaje'=>'Error al guardar los datos'));
	                    }
                    }else{
                        if($this->Model_general->guardar_edit_registro("venta_detalle", $item, $condicion_items) == false){
                        	$this->db->trans_rollback();
	                		$this->Model_general->dieMsg(array('exito'=>false,'mensaje'=>'Error al guardar los datos'));
                        }
                    }
					*/
					if(!empty($detalle_id[$i])){
                        if($this->Model_general->guardar_edit_registro("venta_detalle", $item, $condicion_items) == false){
                        	$this->db->trans_rollback();
	                		$this->Model_general->dieMsg(array('exito'=>false,'mensaje'=>'Error al guardar los datos'));
                        }
                        
                    }
                }
            else:
               $this->Model_general->dieMsg(array('exito'=>false,'mensaje'=>'Error al guardar los datos'));
               $this->db->trans_rollback();
            endif;
            $this->Model_general->add_log("EDITAR",5,"Edición de Comprobante ".$serie."-".$numero);
            $this->db->trans_commit();
        }
        $this->Model_general->dieMsg(array('exito'=>true,'mensaje'=>'','id'=>$id));
	}
	public function actualizarEscomprobante($paqu_id = '', $estado){
        $this->Model_general->guardar_edit_registro("paquete", array("paqu_escomprobante" => $estado), array("paqu_id" => $paqu_id));
		$this->Model_general->guardar_edit_registro("paquete_detalle", array("deta_escomprobante" => $estado), array("deta_paqu_id" => $paqu_id));
	}
	public function comp_eliminar($comp_id = ''){
		$consulta = $this->db->where("vent_id", $comp_id)->get("venta_paquete");

        $venta = $this->db->where("vent_id",$comp_id)->get("venta")->row();
        if($venta->vent_escobrado == '1' || $venta->vent_cobrado > 0){
            $json["exito"] = false;
            $json["mensaje"] = "El Comprobante tiene cobros registrados o ya esta cobrado";
        }else{
    		$this->db->trans_start();
    		if($consulta->num_rows() > 0){
    			foreach ($consulta->result() as $key => $val) {
    				$this->actualizarEscomprobante($val->paqu_id, 0);
    			}
    		}
            $this->Model_general->add_log("ELIMINAR",5,"Eliminación de Comprobante ".$venta->vent_serie."-".$venta->vent_numero);
    		$this->Model_general->borrar(array("vent_id" => $comp_id), "venta");
    		$this->db->trans_complete();
    		if ($this->db->trans_status() === FALSE){
    			$json["exito"] = false;
    			$json["mensaje"] = "Ocurrio un error";
    		}else{
    			$json["exito"] = true;
    			$json["mensaje"] = "Datos borrados con exito";
    		}
        }
		echo json_encode($json);
	}
	
	public function comp_cobrar($id){
        $this->load->helper('Funciones');
        
        $datos["moneda"] = $this->Model_general->enum_valores('cuenta_movimiento','movi_moneda');

        $datos["comprobante"] = $this->Model_general->getCompTotal($id);
        $datos["documentos"] = $this->Model_general->getOptionsWhere("comprobante_tipo",array("tcom_id","tcom_nombre"),array("tcom_id<>"=>'07', "tcom_id<>"=>'08'));
        $datos["cuentas"] = $this->Model_general->getOptions("cuenta",array("cuen_id","cuen_banco"),'* Cuenta');

        $this->load->view('Comprobante/form_cobrar', $datos);
    }
    public function guardar_cobro($id = ''){
        $pagado = $this->input->post('pagado');
        $cancelado = $this->input->post('cancelado');
        $saldo = $this->input->post('saldo');
        $descripcion = $this->input->post('descripcion');
        $moneda = $this->input->post('moneda');
        $total = $this->input->post('total');
        $documento = $this->input->post('documento');
        $serie = $this->input->post('serie');
        $numero = $this->input->post('numero');
        $fecha = $this->Model_general->fecha_to_mysql($this->input->post('fecha'));
        $cuenta = $this->input->post('cuenta');
        $codigo_cuen = $this->input->post('codigo-cuen');

        $comp = $this->db->where("vent_id", $id)->get("venta")->row();

        if($comp->vent_escobrado == 1){
            $json['exito'] = false;
            $json['mensaje'] = "El comprobante ya esta cancelado";
        }else{
            if($cuenta == '' || $codigo_cuen == '' || $pagado == ''){
                $json['exito'] = false;  
                $json['mensaje'] = "Cuenta, Código y Pagado son obligatorios";
                echo json_encode($json);
                exit(0);
            }
            $this->db->trans_start();

            $this->Model_general->actualizarCaja(3, "INGRESO", $documento, $serie, $numero, "cobro de : ".$comp->vent_serie."-".$comp->vent_numero, $pagado, $moneda, $this->usua_id, $id, '', $cuenta,$codigo_cuen,$fecha,$descripcion);
            $desc = ($comp->vent_cobrodesc != "")?$comp->vent_cobrodesc." / ".$descripcion:$descripcion;
			$prev = array("vent_cobrofecha" => $fecha, "vent_cobrodesc" => $desc);
			if(($cancelado + $pagado) >= $total){
                $dte = array("vent_escobrado" => '1', "vent_cobrado" => ($cancelado + $pagado));
            }else{
                $dte = array("vent_cobrado" => ($cancelado + $pagado));
            }
            $dte = array_merge($dte,$prev);
            $this->Model_general->guardar_edit_registro("venta", $dte, array('vent_id' => $id));
            $this->actualizaPagoPaquete($id,1,$fecha,"Cobrado en ".$comp->vent_serie."-".$comp->vent_numero);
            $this->Model_general->add_log("COBRO",5,"Cobro de Comprobante ".$comp->vent_serie."-".$comp->vent_numero." ".$pagado." ".$moneda.", Código de caja: ".$codigo_cuen);
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE){
                $json['exito'] = false;
                $json['mensaje'] = "Error al guardar los datos";
            }else{
                $json['exito'] = true;  
                $json['mensaje'] = "Datos guardados con exito";
            }
        }
        echo json_encode($json);
    }
	public function cancelar_cobro($id=''){
        $fecha = date("Y-m-d");
        
        $comp = $this->db->select('CONCAT(vent_serie,"-",vent_numero," : ",vent_clie_rsocial) as numero, 
									vent_escobrado, vent_numero, vent_total, vent_moneda, 
									vent_esliquidacion, vent_cobrado')->where("vent_id", $id)->get("venta")->row();

        $mov = $this->db->where(array("movi_ref_id" => $id, "movi_tipo_id" => 3))->get("cuenta_movimiento");
        if($comp->vent_cobrado > 0){
			if($comp->vent_esliquidacion == '0'){
				$mov = $mov->row();

				$this->db->trans_start();
				$movimiento = $this->Model_general->actualizarCaja(1, 'EGRESO', "", "", "", "Anulacion de cobro de ".$comp->numero, $comp->vent_cobrado, $comp->vent_moneda, $this->usua_id, $id, '', $mov->movi_cuen_id,"000000",$fecha,"Amulacion de cobro");
						
				$dte = array("vent_escobrado" => '0', "vent_cobrado" => "0", "vent_cobrodesc" => null, "vent_cobrofecha" => null);

				$this->Model_general->guardar_edit_registro("venta", $dte, array('vent_id' => $id));
				$this->Model_general->guardar_edit_registro("cuenta_movimiento", array("movi_file" => "000000"), array('movi_id' => $mov->movi_id));

				$this->actualizaPagoPaquete($id,0,$fecha,"");
				$this->Model_general->add_log("COBRO",5,"Anulacion de cobro de Hoja de liquidación ".$comp->numero." ".$comp->vent_cobrado." ".$comp->vent_moneda.", Código de caja: ANULADO");

				$this->db->trans_complete();
				if ($this->db->trans_status() === FALSE){
					$json['exito'] = false;
					$json['mensaje'] = "Error al guardar los datos";
				}else{
					$json['exito'] = true;  
					$json['mensaje'] = "Datos guardados con exito";
				}
			}else{
				$json['exito'] = false;  
				$json['mensaje'] = "No es posible cancelar cobro, por que el comprobante esta en una liquidación";
			}
		}else{
			$json['exito'] = false;  
			$json['mensaje'] = "no hay cobros que cancelar";
		}
        
        echo json_encode($json);
    }
    public function actualizaPagoPaquete($vent_id, $estado,$fecha,$obs){
        $consulta = $this->db->where("vent_id",$vent_id)->get("venta_paquete")->result();
        foreach ($consulta as $row) {
			$this->Model_general->actualizaPaqueteDetalle($row->paqu_id,$estado);
            if($estado == 1){
                $this->db->query("UPDATE paquete SET paqu_escobrado = '{$estado}', paqu_cobrado = paqu_total, paqu_cobrofecha = '{$fecha}', paqu_cobrodesc = '{$obs}' WHERE paqu_id = '{$row->paqu_id}'");    
            }else{
                $this->db->query("UPDATE paquete SET paqu_escobrado = '{$estado}', paqu_cobrado = 0, paqu_cobrofecha = NULL, paqu_cobrodesc = NULL WHERE paqu_id = '{$row->paqu_id}'");
            }
        }
    }
    function comp_exportar($id){

        $hasta = $this->input->get('hasta');
        
        $this->db->select("DATE_FORMAT(vent_fecha,'%d/%m/%Y') AS fecha, CONCAT(vent_serie,'-',vent_numero) as serie, vent_clie_rsocial as rsocial, vent_clie_direccion as clie_direccion, vent_moneda, vent_subtotal as subtotal, vent_igv as igv, vent_total as total");
        $this->db->from("venta");
        $this->db->where("vent_id", $id);
        $venta = $this->db->get()->row();

        $this->db->select("DATE_FORMAT(VD.deta_fechaserv,'%d/%m/%Y') as fecha, VD.deta_descripcion as grupo, PD.deta_guia, IF(PD.deta_subserv_name <> '',CONCAT(S.serv_abrev,'/',PD.deta_subserv_name), S.serv_abrev)  as serv, VD.deta_valor as monto, VD.deta_igv as igv, VD.deta_total as total, VD.deta_pax as pax, PD.deta_guia as guia, PD.deta_hotel as hotel, VD.deta_lunch as lunch, PQ.paqu_file as filer");
        $this->db->from("venta_detalle as VD");
        $this->db->join("paquete_detalle as PD","PD.deta_id = VD.deta_pdet_id");
        $this->db->join("paquete as PQ","PQ.paqu_id = PD.deta_paqu_id");
        $this->db->join("servicio as S","S.serv_id = VD.deta_serv_id");
        $this->db->where("VD.deta_vent_id",$id);
        $detalle = $this->db->get()->result();


        $this->load->library("Excel");
        $excel = new Excel();
        $objPHPExcel = $excel->excel_init();
        //--------------------  Estilos  ----------------------------
        $bordeb = array(
            'borders' => array(
                'bottom' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            )
        );
        $bordes = array(
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                )
            ));

        $fillgray = array(
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => 'E4E7E9')
            )
        );
        
        
        $objPHPExcel->getActiveSheet()->getStyle('D')->applyFromArray($fillgray);
        $objPHPExcel->getActiveSheet()->getStyle('H')->applyFromArray($fillgray);
        
        $objPHPExcel->getActiveSheet()
                ->setCellValue('A1', 'FECHA')
                ->setCellValue('A2', 'NUM / SERIE')
                ->setCellValue('A3', 'CLIENTE')
                ->setCellValue('A4', 'DIRECCION')
                ->setCellValue('A5', 'MONEDA');
        $objPHPExcel->getActiveSheet()
                ->setCellValue('B1', $venta->fecha)
                ->setCellValue('B2', $venta->serie)
                ->setCellValue('B3', $venta->rsocial)
                ->setCellValue('B4', $venta->clie_direccion)
                ->setCellValue('B5', $venta->vent_moneda);
        /*
        $objPHPExcel->getActiveSheet()->getStyle('F')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
        $objPHPExcel->getActiveSheet()->getStyle('D')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);      
        */   
        
        $objPHPExcel->getActiveSheet()
                ->setCellValue('A7', 'FECHA')
                ->setCellValue('B7', 'SERV')
                ->setCellValue('C7', 'PAX')
                ->setCellValue('D7', 'NOMBRE / GRUPO')
                ->setCellValue('E7', 'GUIA')
                ->setCellValue('F7', 'HOTEL')
                ->setCellValue('G7', 'LUNCH')
                ->setCellValue('H7', 'FILE')
                ->setCellValue('I7', 'MONTO')
                ->setCellValue('J7', 'IGV')
                ->setCellValue('K7', 'TOTAL');
        $ini = 8;
        $index = 0;
        
        foreach($detalle as $fila){
            $nro = $index+$ini;
            $index++;
            $objPHPExcel->getActiveSheet()
                        ->setCellValue("A$nro", $fila->fecha)
                        ->setCellValue("B$nro", $fila->serv)
                        ->setCellValue("C$nro", $fila->pax)
                        ->setCellValue("D$nro", $fila->grupo)
                        ->setCellValue("E$nro", $fila->guia)
                        ->setCellValue("F$nro", $fila->hotel)
                        ->setCellValue("G$nro", $fila->lunch)
                        ->setCellValue("H$nro", $fila->filer)
                        ->setCellValue("I$nro", $fila->monto)
                        ->setCellValue("J$nro", $fila->igv)
                        ->setCellValue("K$nro", $fila->total);
        }
        foreach(range('A','H') as $nro){
            $objPHPExcel->getActiveSheet()->getColumnDimension($nro)->setAutoSize(true);
        }
        
        $fin = $index+$ini;
        $objPHPExcel->getActiveSheet()
                        ->setCellValue("I$fin", $venta->subtotal)
                        ->setCellValue("J$fin", $venta->igv)
                        ->setCellValue("K$fin", $venta->total);
        $objPHPExcel->getActiveSheet()->getStyle("E$ini:G$fin")->getNumberFormat()->setFormatCode('#,##0.00'); 
        $objPHPExcel->getActiveSheet()->getStyle("I$ini:K$fin")->getNumberFormat()->setFormatCode('#,##0.00'); 

        $objPHPExcel->getActiveSheet()->getStyle("A$ini:A$fin")->getNumberFormat()->setFormatCode('dd/mm/yyyy');
        
        
        $excel->excel_output($objPHPExcel, $venta->serie);
    }
    public function comp_toLiquidacion($id=''){
        $subtotal = 0;
        $sel = $this->input->get("sel");
        if($id == ""){
            $comprobantes = $this->getSeleccionadosComp($sel);
            
            $clie = $comprobantes[0];
            
            $liqu = new stdClass;
            $liqu->liqu_id = "";
            $liqu->liqu_numero = $this->nextnumCompLiqu();
            $liqu->liqu_clie_id = $clie->vent_clie_id;
            $liqu->liqu_clie_rsocial = $clie->vent_clie_rsocial;
            $liqu->liqu_clie_docid = $clie->vent_clie_tdoc_id;
            $liqu->liqu_clie_docnro = $clie->vent_clie_tdoc_nro;
            $liqu->liqu_moneda = $clie->vent_moneda;
            $liqu->liqu_fecha = date("Y-m-d");
            $liqu->liqu_subtotal = 0.00;
            $liqu->liqu_impuesto = 0.00;
            $liqu->liqu_total = 0.00;
            $liqu->liqu_impu_porcent = 0.00;
            $liqu->liqu_observacion = '';
            $datos["titulo"] = "Generar Liquidación";
        }else{
            $datos["titulo"] = "Editar Liquidación";
            $liqu = $this->db->where("liqu_id", $id)->get("venta_liquidacion")->row();
            $comprobantes = $this->db->query("SELECT vent_escobrado, DATE_FORMAT(vent_fecha, '%d/%m/%Y') vent_fecha, CONCAT(vent_serie,'-',vent_numero) serie, vent_igv, vent_subtotal, vent_total FROM venta WHERE vent_id IN (SELECT deta_comp_id FROM venta_liquidacionDetalle WHERE deta_liqu_id = '{$id}')")->result();
        }
        $html = "<table class='table table-striped table-bordered'><thead>";
        $html .= "<tr><th>Fecha</th><th>Comprobante</th><th>IGV</th><th>Sub Total</th><th>Total</th></tr></thead><tbody>";
        foreach ($comprobantes as $i => $row) {
            if($row->vent_escobrado != 1){
                $html .= "<tr>";
                $html .= "<td class='col-sm-1'>".$row->vent_fecha."</td>";
                $html .= "<td>".$row->serie."</td>";
                $html .= "<td class='mone col-sm-1'>".$row->vent_igv."</td>";
                $html .= "<td class='mone col-sm-1'>".$row->vent_subtotal."</td>";
                $html .= "<td class='mone col-sm-1'>".$row->vent_total."</td>";
                $html .= "</tr>";
                $subtotal += $row->vent_total;
            }
        }
        $html .= "</table>";

        $datos["sel"] = $sel;
        $datos["sub_total"] = $subtotal;
        $datos["desc"] = "0.00";
        $datos["total"] = $subtotal;
        $datos["table"] = $html;
        
        $datos["liqu"] = $liqu;
        $datos["documentos"] = $this->Model_general->getOptions('documento_tipo', array("tdoc_id", "tdoc_nombre"),'* Documento');
        $datos["moneda"] = $this->Model_general->enum_valores('liquidacion','liqu_moneda');

        $this->load->helper('Funciones');
        $this->load->model("Model_general");
        $this->load->library('Ssp');
        $this->load->library('Cssjs');
        $this->cssjs->add_js(base_url().'assets/js/Comprobante/gen_liqu.js?v=1.2',false,false);
        $script['js'] = $this->cssjs->generate_js();
        $this->load->view('header', $script);
        $this->load->view('menu');
        $this->load->view($this->router->fetch_class().'/form_genliqu', $datos);
        $this->load->view('footer');
    }
    public function getSeleccionadosComp($sel){
        $this->db->select("vent_id, DATE_FORMAT(vent_fecha, '%d/%m/%Y') vent_fecha, CONCAT(vent_serie,'-',vent_numero) serie, vent_igv, vent_subtotal, vent_total, vent_clie_id, vent_clie_rsocial, vent_clie_tdoc_id, vent_clie_tdoc_nro, vent_moneda, vent_escobrado");
        $this->db->where_in("vent_id", explode(",", $sel));
        return $this->db->get("venta")->result();
    }
    public function getLiquidacion($liqu_id=''){

        if($liqu_id != ''){
            $this->db->select("live_id, live_numero, DATE_FORMAT(live_fechareg, '%d/%m/%Y') live_fechareg, live_clie_id, live_clie_rsocial, live_clie_tdoc_id, live_clie_tdoc_nro, live_igv, live_subtotal, live_total, live_moneda, live_saldo_anterior, live_obs");
            $this->db->from("liquidacion_venta");
            $this->db->where("live_id",$liqu_id);
            $liqu = $this->db->get()->row();
        }else{

        }
    }
    /*
    public function actualizaPaquetes(){
        $paquetes = $this->db->get("venta")->result();
        foreach ($paquetes as $i => $row) {
            $detas = $this->db->where("deta_vent_id",$row->vent_id)->get("venta_detalle")->result();
            foreach ($detas as $j => $deta) {
                $this->db->select("paqu_tipo, paqu_id");
                $this->db->from("paquete_detalle");
                $this->db->join("paquete","paqu_id = deta_paqu_id");
                $this->db->where("deta_id",$deta->deta_pdet_id);
                $consulta = $this->db->get()->row();
                $this->db->where(array("vent_id" => $row->vent_id,"paqu_id" => $consulta->paqu_id));
                $verif = $this->db->get("venta_paquete");
                if($verif->num_rows() > 0){
                    echo "hubo repeticiones</br>";
                }else{
                    if($this->Model_general->guardar_registro("venta_paquete",array("vent_id" => $row->vent_id,"paqu_id" => $consulta->paqu_id)))
                        echo "todo bien we</br>";
                    else
                        echo "mal :( </br>";    
                }
            }
        }
    }
	public function actualizaTipo(){
        $ventas = $this->db->get("venta")->result();
        foreach ($ventas as $i => $venta) {
            $deta = $this->db->where("deta_vent_id", $venta->vent_id)->get("venta_detalle")->row();
            $paqu_deta = $this->db->where("deta_id", $deta->deta_pdet_id)->get("paquete_detalle")->row();
            $paquete = $this->db->where("paqu_id", $paqu_deta->deta_paqu_id)->get("paquete")->row();
            $this->Model_general->guardar_edit_registro("venta", array("vent_tipo" => $paquete->paqu_tipo), array("vent_id" => $venta->vent_id));
        }
    }
    */
    public function nextnumCompLiqu(){
        $this->db->select('MAX(liqu_numero) as max');
        $this->db->from('venta_liquidacion');
        $query = $this->db->get();
        $row = $query->row();
        $numero = $row->max+1;
        return $numero;
    }
    public function guardar_liquidacion($liqu_id=''){
        $seleccionados = explode(",", $this->input->post("seleccionados"));
        $tdoc = $this->input->post("documento");
        $tdocnum = $this->input->post("docnum");
        $rsocial = $this->input->post("rsocial");
        $clie_id = $this->input->post("clie_id");
        $moneda = $this->input->post("moneda");
        $numero = $this->input->post("numero");
        $obs = $this->input->post("observacion");
        $porcentaje = ($this->input->post("porcentaje")) ? $this->input->post("porcentaje") : 0;
        $total_sub = $this->input->post("total_sub");
        $total_imp = $this->input->post("total_igv");
        $total_total = $this->input->post("total_total");
        //$fecha = $this->Model_general->fecha_to_mysql($this->input->post('fecha'));
        $fecha = $this->input->post('fecha');

        $data = array("liqu_numero" => $numero,
                        "liqu_clie_id" => $clie_id,
                        "liqu_clie_docid" => $tdoc,
                        "liqu_clie_docnro" => $tdocnum,
                        "liqu_moneda" => $moneda,
                        "liqu_fecha" => $fecha,
                        "liqu_sub_total" => $total_sub,
                        "liqu_impuesto" => $total_imp,
                        "liqu_total" => $total_total,
                        "liqu_impu_porcent" => $porcentaje,
                        "liqu_observacion" => $obs,
                        "liqu_usuario" => $this->usua_id
        );
        $this->db->trans_begin();
        if($liqu_id != ""){
            $where = array("liqu_id" => $liqu_id);
            if($this->Model_general->guardar_edit_registro("venta_liquidacion", $data, $where)){
                $resp["exito"] = true;
                $resp["mensaje"] = "Guardado con exito";
            }else{
                $resp["exito"] = false;
                $resp["mensaje"] = "Ocurrio un error";
            }
        }else{
            if(($liqu = $this->Model_general->guardar_registro("venta_liquidacion", $data)) != false){
                foreach ($seleccionados as $i => $sel) {
                    $deta = array("deta_liqu_id" => $liqu["id"],
                                    "deta_comp_id" => $sel);
                    if($this->Model_general->guardar_registro("venta_liquidacionDetalle", $deta) == false){
                        $this->db->trans_rollback();
                        $resp["exito"] = false;
                        $resp["mensaje"] = "Ocurrio un error, intentelo más tarde";      
                        $this->Model_general->dieMsg($resp);
                    }

                    $vdeta = array("vent_esliquidacion" => '1');
                    $vwhere = array("vent_id" => $sel);
                    if($this->Model_general->guardar_edit_registro("venta", $vdeta,$vwhere) == false){
                        $this->db->trans_rollback();
                        $resp["exito"] = false;
                        $resp["mensaje"] = "Ocurrio un error, intentelo más tarde";      
                        $this->Model_general->dieMsg($resp);
                    }
                }
            }else{
                $this->db->trans_rollback();
                $resp["exito"] = false;
                $resp["mensaje"] = "Ocurrio un error, intentelo más tarde";
                $this->Model_general->dieMsg($resp);  
            }
        }
        $this->db->trans_commit();
        $resp["exito"] = true;
        $resp["mensaje"] = "Guardado con exito";
        $this->Model_general->dieMsg($resp);  
    }
    public function comp_listadoLiquidacion(){
        $this->load->helper('Funciones');
        $this->load->library('Ssp');
        $this->load->library('Cssjs');

        $json = isset($_GET['json']) ? $_GET['json'] : false;
        $fecha = 'DATE_FORMAT(liqu_fecha,"%d/%m/%Y")';
        $cobrofecha = 'DATE_FORMAT(liqu_cobrofecha,"%d/%m/%Y")';
        $serie = 'CONCAT("LIQC", "-",liqu_numero)';
        //$saldo = "IF(vent_escobrado = '0',CONCAT('<font class=red><b>',(vent_total - vent_cobrado)),'<font class=green><b>COBRADO')";
        $saldo = "IF(liqu_escobrado = '0',CONCAT('<font class=red><b>',(liqu_total - liqu_cobrado)),CONCAT('<font class=green><b>',IF((liqu_total - liqu_cobrado) < 0,(liqu_total - liqu_cobrado),'COBRADO')))";

        $columns = array(
            array('db' => 'liqu_id',            'dt' => 'ID',           "field" => "liqu_id"),
            array('db' => $fecha,               'dt' => 'FECHA',        "field" => $fecha),
            array('db' => $serie,               'dt' => 'SERIE',        "field" => $serie),
            array('db' => 'clie_rsocial',       'dt' => 'CLIENTE',      "field" => "clie_rsocial"),
            array('db' => 'liqu_moneda',        'dt' => 'MONEDA',       "field" => "liqu_moneda"),
            array('db' => 'liqu_total',         'dt' => 'TOTAL',        "field" => 'liqu_total'),
            array('db' => 'liqu_cobrado',       'dt' => 'COBRADO',      "field" => 'liqu_cobrado'),
            array('db' => $saldo,               'dt' => 'SALDO',        "field" => $saldo),
            array('db' => 'liqu_observacion',   'dt' => 'OBSERVACION',  "field" => 'liqu_observacion'),
            array('db' => $cobrofecha,          'dt' => 'COBRO FECHA',  "field" => $cobrofecha),
            array('db' => 'liqu_cobrodesc',     'dt' => 'COBRO OBS',    "field" => 'liqu_cobrodesc'),
            array('db' => 'liqu_id',            'dt' => 'DT_RowId',     "field" => "liqu_id"),
            array('db' => 'liqu_escobrado',     'dt' => 'DT_RowCobr',   "field" => "liqu_escobrado"),
            array('db' => $this->editar,        'dt' => 'DT_Permisos',  "field" => $this->editar)
        );

        if ($json) {

            $json = isset($_GET['json']) ? $_GET['json'] : false;

            $table = 'venta_liquidacion';
            $primaryKey = 'liqu_id';

            $sql_details = array(
                'user' => $this->db->username,
                'pass' => $this->db->password,
                'db' => $this->db->database,
                'host' => $this->db->hostname
            );

            $condiciones = array();

            $joinQuery = "FROM venta_liquidacion LEFT JOIN cliente ON clie_id = liqu_clie_id";
            $where = "";
            if (!empty($_POST['desde'])&&!empty($_POST['hasta'])){
                $condiciones[] = "liqu_fecha >='".$_POST['desde']."' AND liqu_fecha <='".$_POST['hasta']."'";
            }
            
            if (!empty($_POST['moneda']))
                $condiciones[] = "liqu_moneda='".$_POST['moneda']."'";

            if (!empty($_POST['cliente']))
                $condiciones[] = "liqu_clie_id='".$_POST['cliente']."'";
            $where = count($condiciones) > 0 ? implode(' AND ', $condiciones) : "";
            echo json_encode(
                    $this->ssp->simple($_POST, $sql_details, $table, $primaryKey, $columns, $joinQuery, $where)
            );
            exit(0);
        }
        
        $datos["moneda"] = array_merge(array(''=>'* Monedas'),$this->Model_general->enum_valores('venta','vent_moneda'));
        $datos['columns'] = $columns;
        $datos["contacto"] = $this->Model_general->getOptions('cliente', array("clie_id", "clie_rsocial"),'* Contacto');

        $this->cssjs->add_js(base_url().'assets/js/Comprobante/comp_listadoLiquidacion.js?v=2.8',false,false);
        $this->load->view('header');
        $this->load->view('menu');
        $this->load->view($this->router->fetch_class().'/'.$this->router->fetch_method(), $datos);
        $this->load->view('footer');        
    }
    function comp_formLiquidaciones($id = '') {
        
        $this->load->helper('Funciones');

        $datos["venta"] = $this->Model_general->getVenta($id);
        $datos["detas"] = json_encode($this->Model_general->getDetaVenta($id));

        $datos["moneda"] = $this->Model_general->enum_valores('venta','vent_moneda');
        $datos["comprobantes"] = $this->Model_general->getOptions('comprobante_tipo', array("tcom_id", "tcom_nombre"));
        $datos["documentos"] = $this->Model_general->getOptions('documento_tipo', array("tdoc_id", "tdoc_nombre"),'* Documento');

        $datos['titulo'] = "Registrar comproante";

        $this->load->library('Ssp');
        $this->load->library('Cssjs');
        $this->cssjs->add_js(base_url().'assets/js/Comprobante/form.js?v=2.5',false,false);
        $this->load->view('header');
        $this->load->view('menu');
        $this->load->view('Comprobante/formulario', $datos);
        $this->load->view('footer');
    }
    public function comp_cobrarLiquidacion($id){
        $this->load->helper('Funciones');
        
        $datos["moneda"] = $this->Model_general->enum_valores('cuenta_movimiento','movi_moneda');

        $datos["liquidacion"] = $this->Model_general->getLiquCompTotal($id);
        $datos["documentos"] = $this->Model_general->getOptionsWhere("comprobante_tipo",array("tcom_id","tcom_nombre"),array("tcom_id<>"=>'07', "tcom_id<>"=>'08'));
        $datos["cuentas"] = $this->Model_general->getOptions("cuenta",array("cuen_id","cuen_banco"),'* Cuenta');

        $this->load->view('Comprobante/form_cobrarLiqu', $datos);
    }
    public function guardar_cobroLiquidacion($id = ''){
        $moneda = $this->input->post('moneda');
        $documento = $this->input->post('documento');
        $serie = $this->input->post('serie');
        $numero = $this->input->post('numero');
        $cuenta = $this->input->post('cuenta');
        $codigo_cuen = $this->input->post('codigo-cuen');
        $total = $this->input->post('total');
        $cancelado = $this->input->post('cancelado');
        $saldo = $this->input->post('saldo');
        $fecha = $this->Model_general->fecha_to_mysql($this->input->post('fecha'));
        $pagado = $this->input->post('pagado');
        $descripcion = $this->input->post('descripcion');

        $comp = $this->db->where("liqu_id", $id)->get("venta_liquidacion")->row();

        if($comp->liqu_escobrado == 1){
            $json['exito'] = false;
            $json['mensaje'] = "La liquidacion ya esta cancelada";
        }else{
            if($cuenta == '' || $codigo_cuen == '' || $pagado == ''){
                $json['exito'] = false;  
                $json['mensaje'] = "Cuenta, Código y Pagado son obligatorios";
                echo json_encode($json);
                exit(0);
            }
            $this->db->trans_start();

            $this->Model_general->actualizarCaja(8, "INGRESO", $documento, $serie, $numero, "cobro de : LIQC - ".$comp->liqu_numero, $pagado, $moneda, $this->usua_id, $id, '', $cuenta,$codigo_cuen,$fecha,$descripcion);
            $prev = array("liqu_cobrofecha" => $fecha, "liqu_cobrodesc" => $descripcion);
            if(($cancelado + $pagado) >= $total){
                $dte = array("liqu_escobrado" => '1', "liqu_cobrado" => ($cancelado + $pagado));
                $this->actualizaCobroComprobante($id,1,$fecha,"Cobrado en LIQC-".$comp->liqu_numero);
            }else{
                $dte = array("liqu_cobrado" => ($cancelado + $pagado));
            }
            $dte = array_merge($dte,$prev);
            $this->Model_general->guardar_edit_registro("venta_liquidacion", $dte, array('liqu_id' => $id));
            
            $this->Model_general->add_log("COBRO",5,"Cobro de Comprobante LIQC-".$comp->liqu_numero." ".$pagado." ".$moneda.", Código de caja: ".$codigo_cuen);
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE){
                $json['exito'] = false;
                $json['mensaje'] = "Error al guardar los datos";
            }else{
                $json['exito'] = true;  
                $json['mensaje'] = "Datos guardados con exito";
            }
        }
        echo json_encode($json);
    }
    public function actualizaCobroComprobante($liqu_id, $estado,$fecha,$obs){
        $consulta = $this->db->where("deta_liqu_id",$liqu_id)->get("venta_liquidacionDetalle")->result();
        foreach ($consulta as $row) {
            if($estado == 1){
                $this->db->query("UPDATE venta SET vent_escobrado = '{$estado}', vent_cobrado = vent_total, vent_cobrofecha = '{$fecha}', vent_cobrodesc = '{$obs}' WHERE vent_id = '{$row->deta_comp_id}'");    
            }else{
                $this->db->query("UPDATE venta SET vent_escobrado = '{$estado}', vent_cobrado = 0, vent_cobrofecha = NULL, vent_cobrodesc = NULL WHERE vent_id = '{$row->deta_comp_id}'");
            }
            $this->actualizaPagoPaquete($row->deta_comp_id,$estado,$fecha,$obs);
        }
    }

    //comp_eliminar(comp_id)
    public function comp_eliminarLiquidacion($liqu_id = ''){
        $consulta = $this->db->where("deta_liqu_id", $liqu_id)->get("venta_liquidacionDetalle");

        $liquidacion = $this->db->where("liqu_id",$liqu_id)->get("venta_liquidacion")->row();
        if($liquidacion->liqu_escobrado == '1' || $liquidacion->liqu_cobrado > 0){
            $json["exito"] = false;
            $json["mensaje"] = "La liquidacion tiene cobros registrados o ya esta cobrado";
        }else{
            $this->db->trans_start();
            if($consulta->num_rows() > 0){
                foreach ($consulta->result() as $key => $val) {
                    $data = array("vent_esliquidacion" => 0);
                    $where = array("vent_id" => $val->deta_comp_id);
                    $this->Model_general->guardar_edit_registro("venta",$data,$where);
                }
            }
            $this->Model_general->add_log("ELIMINAR",5,"Eliminación de Liquidacion de Comprobantes LIQC - ".$liquidacion->liqu_numero);
            $this->Model_general->borrar(array("liqu_id" => $liqu_id), "venta_liquidacion");
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE){
                $json["exito"] = false;
                $json["mensaje"] = "Ocurrio un error";
            }else{
                $json["exito"] = true;
                $json["mensaje"] = "Datos borrados con exito";
            }
        }
        echo json_encode($json);
    }
	public function reporte_excelComprobantes(){
        $desde = $this->input->post("desde");
        $hasta = $this->input->post("hasta");
        $cliente = $this->input->post("cliente");
        $moneda = $this->input->post("moneda");

        $this->db->select("DATE_FORMAT(vent_fecha,'%d/%m/%Y') fecha, CONCAT(vent_serie, '-',vent_numero) serie, vent_clie_rsocial cliente, tdoc_nombre tdoc, vent_clie_tdoc_nro docnum, vent_total, vent_cobrado, IF(vent_escobrado = '0',(vent_total - vent_cobrado),IF((vent_total - vent_cobrado) < 0,(vent_total - vent_cobrado),'COBRADO')) saldo, IF(vent_esfacturador = '0','Sin enviar','Enviado') facturador, vent_obs obs, DATE_FORMAT(vent_cobrofecha,'%d/%m/%Y') cobrofecha, vent_cobrodesc cobrodesc, vent_escobrado escobrado, vent_moneda moneda, vent_numfactura numfactura");
        $this->db->from("venta");
        $this->db->join("documento_tipo","tdoc_id = vent_clie_tdoc_id", "LEFT");
        if ($desde != "" && $hasta != ""){
            $this->db->where("vent_fecha >=",$desde);
            $this->db->where("vent_fecha <=",$hasta);
        }
        if ($moneda != "")
            $this->db->where("vent_moneda",$moneda);
        if ($cliente != "")
            $this->db->where("vent_clie_id",$cliente);
        $this->db->order_by("vent_fecha","ASC");
        $this->db->group_by("vent_id");
        $detalle = $this->db->get()->result();

        $this->load->library("Excel");
        $excel = new Excel();
        $objPHPExcel = $excel->excel_init();
        //--------------------  Estilos  ----------------------------
        $bordeb = array(
            'borders' => array(
                'bottom' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            )
        );
        $bordes = array(
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                )
            ));

        $fillgray = array(
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => 'E4E7E9')
            )
        );
        $mal = array(
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => 'FFC7CE')
            )
        );
        $bien = array(
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => 'C6EFCE')
            )
        );

        $verde = array(
            'font'  => array(
                'bold'  => true,
                'color' => array('rgb' => '00B050')
        ));
        $rojo = array(
            'font'  => array(
                'bold'  => true,
                'color' => array('rgb' => 'FF0000')
        ));
        

        $objPHPExcel->getActiveSheet()->getStyle('D')->applyFromArray($fillgray);
        $objPHPExcel->getActiveSheet()->getStyle('H')->applyFromArray($fillgray);

        $objPHPExcel->getActiveSheet()
                ->setCellValue('A1', 'FECHA')
                ->setCellValue('B1', 'SERIE')
                ->setCellValue('C1', 'CLIENTE')
                ->setCellValue('D1', 'DOCUMENTO')
                ->setCellValue('E1', 'NUMERO')
                ->setCellValue('F1', 'MONEDA')
                ->setCellValue('G1', 'TOTAL')
                ->setCellValue('H1', 'COBRADO')
                ->setCellValue('I1', 'SALDO')
                ->setCellValue('J1', 'FACTURADOR')
                ->setCellValue('K1', 'OBSERVACIONES')
                ->setCellValue('L1', 'FECHA COBRO')
                ->setCellValue('M1', 'COBRO OBS');
        
        $objPHPExcel->getActiveSheet()->getStyle('F')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
        $objPHPExcel->getActiveSheet()->getStyle('D')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);      
        $objPHPExcel->getActiveSheet()->freezePane('A2');
        
        $ini = 3;
        $index = 0;
        
        foreach($detalle as $fila){
            $nro = $index+$ini;
            $index++;
            if($fila->escobrado == 0){
                $color = $rojo;
            }else{
                $color = $verde;
            }
            $objPHPExcel->getActiveSheet()
                        ->setCellValue("A$nro", $fila->fecha)
                        ->setCellValue("B$nro", $fila->serie)
                        ->setCellValue("C$nro", $fila->cliente)
                        ->setCellValue("D$nro", $fila->tdoc)
                        ->setCellValue("E$nro", $fila->docnum)
                        ->setCellValue("F$nro", $fila->moneda)
                        ->setCellValue("G$nro", $fila->vent_total)
                        ->setCellValue("H$nro", $fila->vent_cobrado)
                        ->setCellValue("I$nro", $fila->saldo)
                        ->setCellValue("J$nro", $fila->facturador." / ".$fila->numfactura)
                        ->setCellValue("K$nro", $fila->obs)
                        ->setCellValue("L$nro", $fila->cobrofecha)
                        ->setCellValue("M$nro", $fila->cobrodesc);

            $objPHPExcel->getActiveSheet()->getStyle("I$nro")->applyFromArray($color);
            /*
            if($fila->estado == "CONFIRMADO")
                $objPHPExcel->getActiveSheet()->getStyle("A$nro")->applyFromArray($bien);
            else if($fila->estado == "ANULADO")
                $objPHPExcel->getActiveSheet()->getStyle("A$nro")->applyFromArray($mal);
            */
        }

        foreach(range('A','M') as $nro)
            $objPHPExcel->getActiveSheet()->getColumnDimension($nro)->setAutoSize(true);
        
        $fin = $index+$ini-1;
        $objPHPExcel->getActiveSheet()->getStyle("G$ini:I$fin")->getNumberFormat()->setFormatCode('#,##0.00'); 
        $objPHPExcel->getActiveSheet()->getStyle("A$ini:A$fin")->getNumberFormat()->setFormatCode('dd/mm/yyyy');
        $objPHPExcel->getActiveSheet()->getStyle("L$ini:L$fin")->getNumberFormat()->setFormatCode('dd/mm/yyyy');
        
        
        $excel->excel_output($objPHPExcel, 'COMPROBANTES '.$desde." - ".$hasta);

    }
	public function vercomp($id){
    	$datos['id'] = $id;
        $this->load->view('Comprobante/vercomp',$datos);
    }
	function comp_selExcel(){
        $sel = ($this->input->get('sel')!="undefined" && $this->input->get('sel')!="")?explode(",",$this->input->get('sel')):"";
        
        $this->db->select("DATE_FORMAT(V.vent_fechafactura,'%d/%m/%Y') AS fecha, COMP.tcom_abrev AS ctipo, V.vent_serie AS serie, V.vent_numero AS numero, V.vent_clie_tdoc_nro as docid_nro, V.vent_clie_rsocial AS rsocial, V.vent_moneda as moneda,V.vent_total AS total,V.vent_igv AS igv,V.vent_subtotal AS subtotal, V.vent_id, V.vent_numfactura numfactura");
        $this->db->from("venta V");
        $this->db->join("comprobante_tipo COMP","COMP.tcom_id = V.vent_tcom_id");
        if($sel != "")
            $this->db->where_in("V.vent_id", $sel);
        $this->db->group_by('V.vent_id');
        $this->db->order_by("V.vent_tcom_id","ASC");
        $this->db->order_by("V.vent_serie","ASC");
        $this->db->order_by("V.vent_numero","ASC");
        $documentos = $this->db->get()->result();

        $this->load->library("Excel");
        $excel = new Excel();
        $objPHPExcel = $excel->excel_init();
        //--------------------  Estilos  ----------------------------
        $bordeb = array(
            'borders' => array(
                'bottom' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            )
        );
        $bordes = array(
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                )
            ));

        $fillgray = array(
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => 'E4E7E9')
            )
        );
        $green = array(
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => 'C6EFCE')
            )
        );
        
        $center = array(
           'alignment' => array(
               'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
               'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
           ) 
        );
        
        /*
        $objPHPExcel->getActiveSheet()->mergeCells('A1:A2');
        $objPHPExcel->getActiveSheet()->mergeCells('B1:B2');
        $objPHPExcel->getActiveSheet()->mergeCells('C1:C2');
        $objPHPExcel->getActiveSheet()->mergeCells('D1:D2');
        $objPHPExcel->getActiveSheet()->mergeCells('E1:E2');
        $objPHPExcel->getActiveSheet()->mergeCells('F1:F2');
        $objPHPExcel->getActiveSheet()->mergeCells('G1:G2');
        $objPHPExcel->getActiveSheet()->mergeCells('H1:H2');
        $objPHPExcel->getActiveSheet()->mergeCells('I1:I2');
        $objPHPExcel->getActiveSheet()->mergeCells('J1:J2');
        $objPHPExcel->getActiveSheet()->mergeCells('K1:K2');
        $objPHPExcel->getActiveSheet()->mergeCells('L1:L2');
        $objPHPExcel->getActiveSheet()->mergeCells('M1:M2');
        **/
        $objPHPExcel->getActiveSheet()->getStyle('A1:M2')->applyFromArray($center);

        $objPHPExcel->getActiveSheet()
                ->setCellValue('A1', 'SERVICIO')
                ->setCellValue('B1', 'PAX')
                ->setCellValue('C1', 'NOMBRE / GRUPO')
                ->setCellValue('D1', 'GUIA')
                ->setCellValue('E1', 'HOTEL')
                ->setCellValue('F1', 'LUNCH')
                ->setCellValue('G1', 'FILE')
                ->setCellValue('H1', 'MONTO')
                ->setCellValue('I1', 'IGV')
                ->setCellValue('J1', 'TOTAL')
                ->setCellValue('K1', 'FACTURA')
                ->setCellValue('L1', 'FECHA DE EMISION')
                ->setCellValue('M1', 'OBSERVACIONES');
                
        $objPHPExcel->getActiveSheet()->getStyle('F')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
        $objPHPExcel->getActiveSheet()->getStyle('D')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);      
        $objPHPExcel->getActiveSheet()->freezePane('A3');
        
        $ini = 3;
        $index = 0;

        foreach($documentos as $fila){
            
            $this->db->select("IF(S.serv_abrev = '',S.serv_descripcion,S.serv_abrev) servicio, VD.deta_pax pax, P.paqu_nombre grupo, PD.deta_guia guia, PD.deta_hotel hotel, VD.deta_lunch_efect lunch, P.paqu_file file, VD.deta_valor monto, VD.deta_igv igv, VD.deta_total total");
            $this->db->from("venta_detalle VD");
            $this->db->join("paquete_detalle PD", "PD.deta_id = VD.deta_pdet_id","left");
            $this->db->join("paquete P", "P.paqu_id = PD.deta_paqu_id");
            $this->db->join("servicio S", "S.serv_id = PD.deta_serv_id");
            $this->db->where("deta_vent_id", $fila->vent_id);
            $detalles = $this->db->get()->result();
            
            foreach($detalles as $deta){
                $nro = $index+$ini;
                $objPHPExcel->getActiveSheet()
                        ->setCellValue("A$nro", $deta->servicio)
                        ->setCellValue("B$nro", $deta->pax)
                        ->setCellValue("C$nro", $deta->grupo)
                        ->setCellValue("D$nro", $deta->guia)
                        ->setCellValue("E$nro", $deta->hotel)
                        ->setCellValue("F$nro", $deta->lunch)
                        ->setCellValue("G$nro", $deta->file)
                        ->setCellValue("H$nro", $deta->monto)
                        ->setCellValue("I$nro", $deta->igv)
                        ->setCellValue("J$nro", $deta->total)
                        ;
                $index++;
            }
            
            $nro = $index+$ini;
            
            //$objPHPExcel->getActiveSheet()->mergeCells("B$nro:C$nro");
            //$objPHPExcel->getActiveSheet()->getStyle('A1:O2')->applyFromArray($center);
            $objPHPExcel->getActiveSheet()
                        ->setCellValue("A$nro", $fila->serie."-".$fila->numero)
                        ->setCellValue("H$nro", $fila->subtotal)
                        ->setCellValue("I$nro", $fila->igv)
                        ->setCellValue("J$nro", $fila->total)
						->setCellValue("K$nro", $fila->numfactura)
                        ->setCellValue("L$nro", $fila->fecha);
            $objPHPExcel->getActiveSheet()->getStyle("A$nro:M$nro")->applyFromArray($green);
            $index++;
            
        }

        
        foreach(range('A','M') as $rag){
            $objPHPExcel->getActiveSheet()->getColumnDimension($rag)->setAutoSize(true);
        }
        $fin = $index+$ini-1;
        $objPHPExcel->getActiveSheet()->getStyle("H$ini:J$fin")->getNumberFormat()->setFormatCode('#,##0.00'); 
        $objPHPExcel->getActiveSheet()->getStyle("L$ini:L$fin")->getNumberFormat()->setFormatCode('dd/mm/yyyy');
        
        $excel->excel_output($objPHPExcel, 'RESUMEN ');
    }
	public function actualizarComprobantes(){

        $res["exito"] = false;
        $res["mensaje"] = "No realizar se puede realizar esta accion en pruebas";
        
        echo json_encode($res);

        /*
        $edb = $this->load->database('efactura', TRUE);
        
        $comprobantes = $this->db->query("SELECT vent_id, vent_idFacturador from venta where (vent_numfactura IS NULL OR vent_numfactura = '') AND vent_idFacturador != 0")->result();
		
        if(COUNT($comprobantes) > 0){
            //$this->db->trans_start();
            $cant = 0;
            foreach ($comprobantes as $i => $row) {
                $venta = $edb->query("SELECT CONCAT(vent_serie,'-',vent_numero) serie, vent_fecha fecha FROM venta WHERE vent_id = {$row->vent_idFacturador}");
				if($venta->num_rows() > 0){
					$venta = $venta->row();
					$this->db->query("UPDATE venta SET vent_numfactura = '".$venta->serie."',vent_fechafactura = '".$venta->fecha."'  WHERE vent_id = {$row->vent_id}");
					$cant++;
				}
				
            }
            $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE){
                $res["exito"] = false;
                $res["mensaje"] = "Ocurrio un error 0 comprobantes afectados";
            }else{
                $res["exito"] = true;
                $res["mensaje"] = $cant." comprobantes afectados";
            }
        }else{
            $res["exito"] = false;
            $res["mensaje"] = "no hay comprobantes que actualizar";
        }
        echo json_encode($res);*/
    }
    function comp_excelLiquidacion($liqu_id){
        //$this->db->query("SELECT GROUP_CONCAT(deta_comp_id) comprobantes FROM venta_liquidacionDetalle WHERE deta_liqu_id = {$liqu_id}");
        $this->db->select("GROUP_CONCAT(deta_comp_id) comprobantes");
        $this->db->where("deta_liqu_id", $liqu_id);
        $this->db->group_by("deta_liqu_id");
        $liqu_deta = $this->db->get("venta_liquidacionDetalle")->row();

        $sel = (COUNT($liqu_deta) > 0)?explode(",",$liqu_deta->comprobantes):"";
        
        $this->db->select("DATE_FORMAT(V.vent_fechafactura,'%d/%m/%Y') AS fecha, COMP.tcom_abrev AS ctipo, V.vent_serie AS serie, V.vent_numero AS numero, V.vent_clie_tdoc_nro as docid_nro, V.vent_clie_rsocial AS rsocial, V.vent_moneda as moneda,V.vent_total AS total,V.vent_igv AS igv,V.vent_subtotal AS subtotal, V.vent_id, V.vent_numfactura numfactura");
        $this->db->from("venta V");
        $this->db->join("comprobante_tipo COMP","COMP.tcom_id = V.vent_tcom_id");
        if($sel != "")
            $this->db->where_in("V.vent_id", $sel);
        $this->db->group_by('V.vent_id');
        $this->db->order_by("V.vent_tcom_id","ASC");
        $this->db->order_by("V.vent_serie","ASC");
        $this->db->order_by("V.vent_numero","ASC");
        $documentos = $this->db->get()->result();

        $this->load->library("Excel");
        $excel = new Excel();
        $objPHPExcel = $excel->excel_init();
        //--------------------  Estilos  ----------------------------
        $bordeb = array(
            'borders' => array(
                'bottom' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            )
        );
        $bordes = array(
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                )
            ));

        $fillgray = array(
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => 'E4E7E9')
            )
        );
        $green = array(
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => 'C6EFCE')
            )
        );
        
        $center = array(
           'alignment' => array(
               'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
               'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
           ) 
        );
        
        $objPHPExcel->getActiveSheet()->getStyle('A1:M2')->applyFromArray($center);

        $objPHPExcel->getActiveSheet()
                ->setCellValue('A1', 'SERVICIO')
                ->setCellValue('B1', 'PAX')
                ->setCellValue('C1', 'NOMBRE / GRUPO')
                ->setCellValue('D1', 'GUIA')
                ->setCellValue('E1', 'HOTEL')
                ->setCellValue('F1', 'LUNCH')
                ->setCellValue('G1', 'FILE')
                ->setCellValue('H1', 'MONTO')
                ->setCellValue('I1', 'IGV')
                ->setCellValue('J1', 'TOTAL')
                ->setCellValue('K1', 'FACTURA')
                ->setCellValue('L1', 'FECHA DE EMISION')
                ->setCellValue('M1', 'OBSERVACIONES');
                
        $objPHPExcel->getActiveSheet()->getStyle('F')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
        $objPHPExcel->getActiveSheet()->getStyle('D')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);      
        $objPHPExcel->getActiveSheet()->freezePane('A3');
        
        $ini = 3;
        $index = 0;

        foreach($documentos as $fila){
            
            $this->db->select("IF(S.serv_abrev = '',S.serv_descripcion,S.serv_abrev) servicio, VD.deta_pax pax, P.paqu_nombre grupo, PD.deta_guia guia, PD.deta_hotel hotel, VD.deta_lunch_efect lunch, P.paqu_file file, VD.deta_valor monto, VD.deta_igv igv, VD.deta_total total");
            $this->db->from("venta_detalle VD");
            $this->db->join("paquete_detalle PD", "PD.deta_id = VD.deta_pdet_id","left");
            $this->db->join("paquete P", "P.paqu_id = PD.deta_paqu_id");
            $this->db->join("servicio S", "S.serv_id = PD.deta_serv_id");
            $this->db->where("deta_vent_id", $fila->vent_id);
            $detalles = $this->db->get()->result();
            
            foreach($detalles as $deta){
                $nro = $index+$ini;
                $objPHPExcel->getActiveSheet()
                        ->setCellValue("A$nro", $deta->servicio)
                        ->setCellValue("B$nro", $deta->pax)
                        ->setCellValue("C$nro", $deta->grupo)
                        ->setCellValue("D$nro", $deta->guia)
                        ->setCellValue("E$nro", $deta->hotel)
                        ->setCellValue("F$nro", $deta->lunch)
                        ->setCellValue("G$nro", $deta->file)
                        ->setCellValue("H$nro", $deta->monto)
                        ->setCellValue("I$nro", $deta->igv)
                        ->setCellValue("J$nro", $deta->total)
                        ;
                $index++;
            }
            
            $nro = $index+$ini;
            
            //$objPHPExcel->getActiveSheet()->mergeCells("B$nro:C$nro");
            //$objPHPExcel->getActiveSheet()->getStyle('A1:O2')->applyFromArray($center);
            $objPHPExcel->getActiveSheet()
                        ->setCellValue("A$nro", $fila->serie."-".$fila->numero)
                        ->setCellValue("H$nro", $fila->subtotal)
                        ->setCellValue("I$nro", $fila->igv)
                        ->setCellValue("J$nro", $fila->total)
						->setCellValue("k$nro", $fila->numfactura)
                        ->setCellValue("L$nro", $fila->fecha);
            $objPHPExcel->getActiveSheet()->getStyle("A$nro:M$nro")->applyFromArray($green);
            $index++;
            
        }

        
        foreach(range('A','M') as $rag){
            $objPHPExcel->getActiveSheet()->getColumnDimension($rag)->setAutoSize(true);
        }
        $fin = $index+$ini-1;
        $objPHPExcel->getActiveSheet()->getStyle("H$ini:J$fin")->getNumberFormat()->setFormatCode('#,##0.00'); 
        $objPHPExcel->getActiveSheet()->getStyle("L$ini:L$fin")->getNumberFormat()->setFormatCode('dd/mm/yyyy');
        
        $excel->excel_output($objPHPExcel, 'LIQUIDACION DE COMPROBANTES');
    }
}
