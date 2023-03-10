<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Liquidacion extends CI_Controller {
	var $configuracion;
	function __construct() {
		parent::__construct();
		if(!$this->session->userdata('authorized')){
			redirect(base_url()."login");
		}
		$this->usua_id = $this->session->userdata('authorized');
        $this->configuracion = $this->db->query("SELECT * FROM usuario WHERE usua_id='{$this->usua_id}'")->row();
        $this->permisos  = $this->Model_general->getPermisos($this->usua_id);
        $this->editar = $this->permisos[5]->nivel_acceso;
        $this->load->model("Model_general");
		$this->load->library('Cssjs');
		$this->load->model("Model_general");
		$this->load->helper('Form');
	}

	function liqu_listado() {
		$this->load->helper('Funciones');
        $this->load->library('Ssp');
        $this->load->library('Cssjs');

       $json = isset($_GET['json']) ? $_GET['json'] : false;
        $lnumero = 'CONCAT("LIQ-",liqu_numero)';
        $fecha = 'DATE_FORMAT(liqu_fechareg, "%d/%m/%Y %h:%i %p")';
        $cfecha = 'DATE_FORMAT(liqu_cobrofecha, "%d/%m/%Y")';
        $saldo = "IF(liqu_estado = 'PENDIENTE',CONCAT('<font class=red><b>',(liqu_total - liqu_cobrado)),CONCAT('<font class=green><b>',IF((liqu_total - liqu_cobrado) < 0,(liqu_total - liqu_cobrado),'COBRADO')))";
        $columns = array(
            array('db' => 'liqu_id',			'dt' => 'ID',           "field" => "liqu_id"),
            array('db' => $fecha,               'dt' => 'Fecha',        "field" => $fecha),
            array('db' => $lnumero,             'dt' => 'LNumero',      "field" => $lnumero),
            array('db' => 'liqu_clie_rsocial',  'dt' => 'Cliente',      "field" => "liqu_clie_rsocial"),
            array('db' => 'liqu_total',         'dt' => 'TOTAL',        "field" => "liqu_total"),
            array('db' => 'liqu_cobrado',       'dt' => 'COBRADO',      "field" => "liqu_cobrado"),
            array('db' => $saldo,               'dt' => 'SALDO',        "field" => $saldo),
            array('db' => 'liqu_obs',           'dt' => 'Observaciones',"field" => 'liqu_obs'),
            array('db' => $cfecha,           'dt' => 'C/Fecha',"field" => $cfecha),
            array('db' => 'liqu_cobrodesc',           'dt' => 'C/Descripcion',"field" => 'liqu_cobrodesc'),
            array('db' => 'liqu_estado',        'dt' => 'DT_RowEst',    "field" => "liqu_estado"),
			array('db' => 'liqu_cobrado',        'dt' => 'DT_RowCobrado',    "field" => "liqu_cobrado"),
            array('db' => 'liqu_id',        	'dt' => 'DT_RowId',     "field" => "liqu_id"),
            array('db' => $this->editar,            'dt' => 'DT_Permisos',     "field" => $this->editar),
            array('db' => $this->usua_id,            'dt' => 'DT_UsuaId',     "field" => $this->usua_id)
        );

        if ($json) {

            $json = isset($_GET['json']) ? $_GET['json'] : false;

            $table = 'liquidacion';
            $primaryKey = 'liqu_id';

            $sql_details = array(
                'user' => $this->db->username,
                'pass' => $this->db->password,
                'db' => $this->db->database,
                'host' => $this->db->hostname
            );

            $condiciones = array();

            $joinQuery = "FROM liquidacion";
            $where = "";
            
            if (!empty($_POST['desde'])&&!empty($_POST['hasta'])){
                $condiciones[] = "liqu_fechareg >='".$_POST['desde']." 00:00:00"."' AND liqu_fechareg <='".$_POST['hasta']." 23:59:00"."'";
            }            
            /*
            if (!empty($_POST['usuario']))
                $condiciones[] = "paqu_usua_id='".$_POST['usuario']."'";

            if (!empty($_POST['moneda']))
                $condiciones[] = "paqu_moneda='".$_POST['moneda']."'";

            if (!empty($_POST['estado']))
                $condiciones[] = "paqu_estado='".$_POST['estado']."'";
            */
            
            $where = count($condiciones) > 0 ? implode(' AND ', $condiciones) : "";
            echo json_encode(
                    $this->ssp->simple($_POST, $sql_details, $table, $primaryKey, $columns, $joinQuery, $where,'liqu_id')
            );
            exit(0);
        }
        $datos["comprobantes"] = $this->Model_general->getOptions('comprobante_tipo', array("tcom_id", "tcom_nombre"),'* Usuario');
        $datos["moneda"] = array_merge(array(''=>'* Monedas'),$this->Model_general->enum_valores('venta','vent_moneda'));
        $datos['columns'] = $columns;

        $this->cssjs->add_js(base_url().'assets/js/Liquidacion/listado.js?v=2.5',false,false);
        
        $this->load->view('header');
        $this->load->view('menu');
        $this->load->view($this->router->fetch_class().'/'.$this->router->fetch_method(), $datos);
        $this->load->view('footer');
	}

	function liqu_form($id = '') {
		
		$this->load->helper('Funciones');

		$liquidacion = $this->db->where("liqu_id", $id)->get("liquidacion")->row();
        if($liquidacion->liqu_tipo == 'RECEPTIVO'){
            $this->liqu_ordPago($liquidacion);
        }else{
            $detas = $this->Model_general->getDetaLiqu($id);
            $datos["saldos"] = $this->SaldoAnterior($liquidacion->liqu_clie_id);
            $datos["moneda"] = $this->Model_general->enum_valores('liquidacion','liqu_moneda');
            $datos["comprobantes"] = $this->Model_general->getOptions('comprobante_tipo', array("tcom_id", "tcom_nombre"));
            $datos["documentos"] = $this->Model_general->getOptions('documento_tipo', array("tdoc_id", "tdoc_nombre"),'* Documento');
            $datos["detas"] = json_encode($detas); 
            $datos["liquidacion"] = $liquidacion;
            $datos['titulo'] = "Editar Hoja de Liquidacion";

            $this->load->library('Ssp');
            $this->load->library('Cssjs');
            $this->cssjs->add_js(base_url().'assets/js/Liquidacion/form.js?v=1.8',false,false);
            $this->load->view('header');
            $this->load->view('menu');
            $this->load->view('Liquidacion/formulario', $datos);
            $this->load->view('footer');
        }
	}
	function to_form() {
        $seleccionados = $this->input->get('sel');
        $detalles = $this->db->query("SELECT PD.*, P.paqu_nombre, P.paqu_cobrado_pax, P.paqu_total, P.paqu_tipo tipo FROM paquete_detalle as PD JOIN paquete as P ON paqu_id = deta_paqu_id WHERE deta_paqu_id IN (".$seleccionados.")")->result();

        $detas = array();
        foreach ($detalles as $i => $val) {
			$adides = $this->Model_general->get_adidesDeta($val->deta_id, "paquete_adicion","padi_descripcion","padi_monto","padi_tipo","padi_pdet_id");
			
			$adiciones = $this->adicionales($val->deta_id, 'ADICION');
            $descuentos = $this->adicionales($val->deta_id, 'DESCUENTO');
			
            $servicio = $this->db->where("serv_id", $val->deta_serv_id)->get("servicio")->row()->serv_descripcion;
            $detas[$i]['id'] = '';
            $detas[$i]['pdet_id'] = $val->deta_id;
            $detas[$i]['serv_id'] = $val->deta_serv_id;
            $detas[$i]['serv_name'] = $servicio;
            $detas[$i]['serv_prec'] = $val->deta_precio;
            $detas[$i]['cobrado_pax'] = $val->paqu_cobrado_pax;
            $detas[$i]['pax'] = $val->deta_pax;
            $detas[$i]['fecha'] = date('d/m/Y', strtotime($val->deta_fechaserv));
            //$detas[$i]['guia'] = $val->deta_guia_nombre;
            $detas[$i]['guia'] = '';
            $detas[$i]['hotel'] = $val->deta_hotel;
            $detas[$i]['nombre'] = $val->paqu_nombre;
            $detas[$i]['lunch'] = $val->deta_lunch;
            $detas[$i]['lunch_efect'] = $val->deta_lunch;
            $detas[$i]['lunch_prec'] = $val->deta_lunch_pre;
            $detas[$i]['total'] = $val->deta_total;
			$detas[$i]['tipo'] = $val->tipo;
			$detas[$i]['adicionales'] = $adides;
			
			$detas[$i]['deta_adic'] = ($adiciones)?$adiciones->descripcion:'';
            $detas[$i]['deta_adic_val'] = ($adiciones)?$adiciones->monto:0;
            $detas[$i]['deta_desc'] = ($descuentos)?$descuentos->descripcion:'';
            $detas[$i]['deta_desc_val'] = ($descuentos)?$descuentos->monto:0;
        }
        $seleccionados = explode(',', $seleccionados);
        $this->db->select("paqu_clie_id as cliente, paqu_moneda as moneda");
        $this->db->from("paquete");
        $this->db->where("paqu_id", $seleccionados[0]);
        $paqu = $this->db->get()->row();
        $saldos = $this->SaldoAnterior($paqu->cliente);
        $cliente = $this->db->where("clie_id", $paqu->cliente)->get("cliente")->row();
        $liquidacion = new StdClass;
        $liquidacion->liqu_id = '';
        $liquidacion->liqu_clie_id = $cliente->clie_id;
        $liquidacion->liqu_clie_rsocial = $cliente->clie_rsocial;
        $liquidacion->liqu_clie_tdoc_id = $cliente->clie_tdoc_id;
        $liquidacion->liqu_clie_doc_nro = $cliente->clie_doc_nro;
        $liquidacion->liqu_moneda = $paqu->moneda;
        $liquidacion->liqu_obs = '';
		$liquidacion->liqu_incluyesaldo = "NO";
        $liquidacion->liqu_clie_num = $this->nextnumclie($cliente->clie_id);
        $liquidacion->liqu_numero = $this->nextnum($cliente->clie_id);
        $datos["saldos"] = $saldos;
        $datos["moneda"] = $this->Model_general->enum_valores('liquidacion','liqu_moneda');
        $datos["comprobantes"] = $this->Model_general->getOptions('comprobante_tipo', array("tcom_id", "tcom_nombre"));
        $datos["documentos"] = $this->Model_general->getOptions('documento_tipo', array("tdoc_id", "tdoc_nombre"),'* Documento');
        $datos["detas"] = json_encode($detas); 
        $datos["liquidacion"] = $liquidacion;
        $datos['titulo'] = "Generar Hoja de Liquidacion";

        $this->load->library('Ssp');
        $this->load->library('Cssjs');
        $this->cssjs->add_js(base_url().'assets/js/Liquidacion/form.js?v=1.8',false,false);
        $this->load->view('header');
        $this->load->view('menu');
        $this->load->view('Liquidacion/formulario', $datos);
        $this->load->view('footer');
	}
	public function adicionales($id, $tipo){
        $this->db->select("GROUP_CONCAT(padi_descripcion) as descripcion, SUM(padi_monto) as monto");
        $this->db->from("paquete_adicion");
        $this->db->where(array('padi_pdet_id' => $id, 'padi_tipo' => $tipo));
        $this->db->group_by("padi_pdet_id");
        return $this->db->get()->row();
    }
	public function SaldoAnterior($clie_id){
        $liqu = $this->db->where(array("liqu_clie_id" => $clie_id,"liqu_estado" => "PENDIENTE"))->get("liquidacion");
        $saldo = array();
        if($liqu->num_rows() > 0){
            $liqu = $liqu->result();    
            foreach ($liqu as $i => $row) {
                $saldo[$i]["file"] = "N?? ".$row->liqu_numero." - ".$row->liqu_clie_num;
                $saldo[$i]["monto"] = $row->liqu_total-$row->liqu_cobrado;
                $saldo[$i]["liqu_id"] = $row->liqu_id;
                $saldo[$i]["moneda"] = $row->liqu_moneda;
            }
        }
        return $saldo;
    }
	private function validarLiquidacion(){
        $this->load->library('Form_validation');
        $this->form_validation->set_rules('rsocial', 'Razon Social', 'required');
        $this->form_validation->set_rules('documento', 'Documento', 'required');


        if($this->input->post('documento') != 0)
            $this->form_validation->set_rules('docnum', 'N??mero de documento', 'required');
        if ($this->form_validation->run() == FALSE){        
            $this->Model_general->dieMsg(array('exito'=>false,'mensaje'=>validation_errors()));
        }
    }
    public function nextnum($clie_id){
        $this->db->select('MAX(liqu_numero) as max');
        $this->db->from('liquidacion');
        //$this->db->where("liqu_clie_id='{$clie_id}'");
        $query = $this->db->get();
        $row = $query->row();
        $numero = $row->max+1;
        return $numero;
    }
    public function nextnumclie($clie_id,$cons=''){
        $this->db->select('MAX(liqu_clie_num) as max');
        $this->db->from('liquidacion');
        $this->db->where("liqu_clie_id='{$clie_id}'");
        $query = $this->db->get();
        $row = $query->row();
        $numero = $row->max+1;
        if($cons == '')
            return $numero;
        else{
            $resp["numero"] = $numero;
            echo json_encode($resp);
        }
    }
	function guardar($id = ''){
		
		$this->load->helper('Funciones');

        $this->validarLiquidacion();

        
        
		$documento = $this->input->post('documento');
		$docnum = $this->input->post('docnum');
		$rsocial = $this->input->post('rsocial');
		$id_cliente = $this->input->post('clie_id');
        $direccion = $this->input->post('direccion');
        $total_total = $this->input->post('total_total');
        $moneda = $this->input->post('moneda');
        

        if($id == ''){
            $liquidacion = $this->db->where("liqu_numero", $this->input->post('numero') )->get("liquidacion")->row();
            if($liquidacion){
                $this->Model_general->dieMsg(array('exito'=>false,'mensaje'=> 'El numero de liquidacion ya existe' )); 
            }
        }
        else{
            $liquidacion = $this->db->where("liqu_id", $id)->get("liquidacion")->row();

            if($liquidacion->liqu_numero == $this->input->post('numero')){
                $this->Model_general->dieMsg(array('exito'=>false,'mensaje'=> 'No se puede modificar el numero de liquidacion' ));
            }
        }

        $numero = $this->input->post('numero');
        


        $clie_num = $this->input->post('clie_numero');
        
        $saldo = $this->input->post('saldo');
        $obs = $this->input->post('observacion');
        $paquetes = explode(",",$this->input->post('seleccionados'));
		$s_incluye = ($this->input->post('incluye_saldo'))?"SI":"NO";
        /**/
        
		$liquidacion = array("liqu_clie_id"=> $id_cliente,
                        "liqu_numero" => $numero,
                        "liqu_clie_num" => $clie_num,
						"liqu_clie_rsocial" => $rsocial,
						"liqu_clie_tdoc_id"=> $documento,
						"liqu_clie_doc_nro" => $docnum,
                        "liqu_total" => $total_total,
                        "liqu_moneda" => $moneda,
                        "liqu_fechareg" => date('Y-m-d H:i:s'),
                        "liqu_usua" => $this->usua_id,
                        "liqu_obs" => $obs,
						"liqu_saldo_anterior" => 0,
						"liqu_incluyesaldo" => $s_incluye
        );
        $deta_id = $this->input->post('deta_id');
        $pdet = $this->input->post('pdet_id');
		$serv_id = $this->input->post('serv_id');
        $serv_name = $this->input->post('serv_name');
        $serv_prec = $this->input->post('serv_prec');
        $pax = $this->input->post('pax');
		$fecha = $this->input->post('fecha');
        $guia = $this->input->post('guia');
        $hotel = $this->input->post('hotel');
        $nombre = $this->input->post('nombre');
        $lunch = $this->input->post('lunch');
        $lunch_efect = $this->input->post('lunch_efect');
        $lunch_prec = $this->input->post('lunch_prec');
		$total = $this->input->post('total');
		$cobrado_pax = $this->input->post('cobrado_pax');
		$tipo = $this->input->post('tipo');
		$adicion = $this->input->post('adicion');
		$adicion_val = $this->input->post('adicion_val');
		$descuento = $this->input->post('descuento');
		$descuento_val = $this->input->post('descuento_val');
		
        $s_monto = ($this->input->post('s_monto'))?$this->input->post('s_monto'):"";
        $s_liqu_id = ($this->input->post('s_liqu_id'))?$this->input->post('s_liqu_id'):"";
        
		if(empty($id)){
			$this->db->trans_begin();
			if (($meta = $this->Model_general->guardar_registro("liquidacion", $liquidacion)) == TRUE):
			    foreach ($paquetes as $paqu_id) {
                    $lpaq = array("lpaq_liqu_id" => $meta["id"],
                                    "lpaq_paqu_id" => $paqu_id
                    );
                    $this->Model_general->guardar_registro("liquidacion_paqu", $lpaq);
                    $this->actualizarPaqueteEstado($paqu_id, 1);
                }
				if($s_incluye == "SI"){
                    if($s_liqu_id != ""){   
                        $s_total = $this->actualizaLiquAnterior($s_monto,$s_liqu_id,$numero,$clie_num);
                        $ddt = array("liqu_saldo_anterior" => $s_total);
                        $ddcond = array("liqu_id" => $meta["id"]);
                        $this->Model_general->guardar_edit_registro("liquidacion", $ddt, $ddcond);
                    }
                }
	            for ($i=0; $i < count($serv_id); $i++) { 
	            	$item = array("deta_liqu_id" => $meta['id'],
                                "deta_pdet_id" => $pdet[$i],
                                "deta_serv_id" => $serv_id[$i],
                                "deta_serv_name" => $serv_name[$i],
                                "deta_serv_prec" => $serv_prec[$i],
                                "deta_pax" => $pax[$i],
	            				"deta_fecha" => $this->Model_general->fecha_to_mysql($fecha[$i]),
                                "deta_guia" => $guia[$i],
                                "deta_hotel" => $hotel[$i],
                                "deta_nombre" => $nombre[$i],
                                "deta_lunch" => $lunch[$i],
                                "deta_lunch_efect" => $lunch_efect[$i],
                                "deta_lunch_prec" => $lunch_prec[$i],
                                "deta_total" => $total[$i],
                                "deta_cobrado_pax" => $cobrado_pax[$i],
                                "deta_tipo" => $tipo[$i],
								"deta_adicion" => $adicion[$i],
								"deta_adicion_val" => $adicion_val[$i],
								"deta_descuento" => $descuento[$i],
								"deta_descuento_val" => $descuento_val[$i]
	            		);
                    if($this->Model_general->guardar_registro("liquidacion_detalle", $item)==FALSE){
                        $this->db->trans_rollback();
                        $this->Model_general->dieMsg(array('exito'=>false,'mensaje'=>'Error al guardar los datos'));
                    }
	            }
	        else:
				$this->db->trans_rollback();
                $this->Model_general->dieMsg(array('exito'=>false,'mensaje'=>'Error al guardar los datos'));
	        endif;
            //$this->verifica_paqueteEstado($pdet, "1");
            $this->Model_general->add_log("CREAR",6,"Creacion de Hoja de liquidaci??n LIQ-".str_pad($numero, 8, "0", STR_PAD_LEFT));
            $this->db->trans_commit();
			$id = $meta['id'];           
        }else{

            $condicion_liquidacion = "liqu_id = ".$id;
            $this->db->trans_begin();
            if (($meta = $this->Model_general->guardar_edit_registro("liquidacion", $liquidacion, $condicion_liquidacion)) == TRUE):
                
                $actuales = $this->db->where('deta_liqu_id',$id)->get("liquidacion_detalle")->result();
                if(count($actuales) > 0){
                    foreach ($actuales as $val) {
                        if(!in_array($val->deta_id, $deta_id)){
                            //$this->actualizarPaqueteEstado($val->deta_pdet_id, "deta_esliquidacion", '0');
                            $this->db->where('deta_id',$val->deta_id);
                            $this->db->delete('liquidacion_detalle');
                        }
                    }
                }

                for ($i=0; $i < count($serv_id); $i++) {

                  	$condicion_items = "deta_id = ".$deta_id[$i];                  	
                    $item = array("deta_liqu_id" => $id,
                                "deta_pdet_id" => $pdet[$i],
                                "deta_serv_id" => $serv_id[$i],
                                "deta_serv_name" => $serv_name[$i],
                                "deta_serv_prec" => $serv_prec[$i],
                                "deta_pax" => $pax[$i],
                                "deta_fecha" => $this->Model_general->fecha_to_mysql($fecha[$i]),
                                "deta_guia" => $guia[$i],
                                "deta_hotel" => $hotel[$i],
                                "deta_nombre" => $nombre[$i],
                                "deta_lunch" => $lunch[$i],
                                "deta_lunch_efect" => $lunch_efect[$i],
                                "deta_lunch_prec" => $lunch_prec[$i],
                                "deta_total" => $total[$i],
                                "deta_cobrado_pax" => $cobrado_pax[$i],
                                "deta_tipo" => $tipo[$i],
								"deta_adicion" => $adicion[$i],
								"deta_adicion_val" => $adicion_val[$i],
								"deta_descuento" => $descuento[$i],
								"deta_descuento_val" => $descuento_val[$i]
                        );
                    if(empty($deta_id[$i])){
                        if($this->Model_general->guardar_registro("liquidacion_detalle", $item) == false){
                        	$this->db->trans_rollback();
                            $this->Model_general->dieMsg(array('exito'=>false,'mensaje'=>'Error al guardar los datos'));
                        }
                    }else{
                        if($this->Model_general->guardar_edit_registro("liquidacion_detalle", $item, $condicion_items) == false){
                        	$this->db->trans_rollback();
	                		$this->Model_general->dieMsg(array('exito'=>false,'mensaje'=>'Error al guardar los datos'));
                        }
                    }
                }
            else:
               $this->Model_general->dieMsg(array('exito'=>false,'mensaje'=>'Error al guardar los datos'));
               $this->db->trans_rollback();
            endif;
            //$this->verifica_paqueteEstado($pdet, "0");
            $this->Model_general->add_log("EDITAR",6,"Edici??n de Hoja de liquidaci??n LIQ-".str_pad($numero, 8, "0", STR_PAD_LEFT));
            $this->db->trans_commit();
        }
        $this->Model_general->dieMsg(array('exito'=>true,'mensaje'=>'','id'=>$id));
	}
	public function actualizaLiquAnterior($s_monto,$s_liqu_id,$numero,$clie_num){
        $total = 0;
        $fecha = date('Y-m-d');
        $desc = "Saldo pasa a: LIQ N?? ".str_pad($numero, 8, "0", STR_PAD_LEFT)." - ".str_pad($clie_num, 4, "0", STR_PAD_LEFT);
        
        foreach ($s_liqu_id as $i => $liqu_id) {
            $total += $s_monto[$i];
            $this->db->query("UPDATE liquidacion SET liqu_cobrado = liqu_total, liqu_estado = 'PAGADO', liqu_cobrofecha = '{$fecha}', liqu_cobrodesc = '{$desc}' WHERE liqu_id = $liqu_id");
            $liqu = $this->db->where("liqu_id",$liqu_id)->get("liquidacion")->row();
            $paqu_desc = "Cobrado en LIQ N?? ".$liqu->liqu_numero;
            $this->actualizaPagoPaquete($liqu_id, 1,$fecha,$paqu_desc);
        }
        return $total;
    }
    /*
    public function verifica_paqueteEstado($ids,$estado){
        $paquetes = $this->get_seleccionados(implode(",",$ids));
        foreach ($paquetes as $i => $paq) {
            $detas = $this->db->select("COUNT(deta_id) as num")->where("deta_paqu_id", $paq->paqu_id)->get("paquete_detalle")->row();
            $liqui = $this->db->select("COUNT(deta_id) as num")->where("deta_paqu_id", $paq->paqu_id, "deta_esliquidacion",$estado)->get("paquete_detalle")->row();
            $condicion = array("paqu_id" => $paq->paqu_id);
            if($detas == $liqui){
                $datas = array("paqu_esliquidacion" => $estado);
                $this->Model_general->guardar_edit_registro("paquete", $datas, $condicion);
            }else{
                if($paq->paqu_esliquidacion == "1"){
                    $this->Model_general->guardar_edit_registro("paquete", array("paqu_esliquidacion" => "0"), $condicion);
                }
            }
            
        }
    }
    */
    public function liq_pdf($id){
        $datos['id'] = $id;
        $this->load->view('Liquidacion/liq_verLiqu',$datos);
    }

	public function eliminar($liqu_id = ''){
		$consulta = $this->db->where("lpaq_liqu_id", $liqu_id)->get("liquidacion_paqu");
        
        $liquidacion = $this->db->where("liqu_id", $liqu_id)->get("liquidacion")->row();
        if($liquidacion->liqu_estado == 'PAGADO' || $liquidacion->liqu_cobrado > 0){
            $json["exito"] = false;
            $json["mensaje"] = "La liquidacion tiene cobros registrados o ya esta cobrado";
        }else{
    		$this->db->trans_start();
    		if($consulta->num_rows() > 0){
    			foreach ($consulta->result() as $key => $val) {
                    $this->actualizarPaqueteEstado($val->lpaq_paqu_id,"0");
    			}
    		}
            $this->Model_general->add_log("ELIMINAR",6,"Eliminaci??n de Hoja de liquidaci??n LIQ-".$liquidacion->liqu_numero);
    		$this->Model_general->borrar(array("liqu_id" => $liqu_id), "liquidacion");
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
    public function actualizarPaqueteEstado($paqu_id = '', $estado){
        $this->Model_general->guardar_edit_registro("paquete", array("paqu_esliquidacion" => $estado), array("paqu_id" => $paqu_id));
        $this->Model_general->guardar_edit_registro("paquete_detalle", array("deta_esliquidacion" => $estado), array("deta_paqu_id" => $paqu_id));
    }
    public function getSaldoAnterior($clie, $numero){
        $this->db->select("liqu_numero as numero, liqu_saldo");
        $this->db->from("liquidacion");
        $this->db->where("liqu_estado",'PENDIENTE');
        $this->db->where("liqu_clie_id",$clie);
        $this->db->where("liqu_numero <",$numero);
        $this->db->where_not_in("liqu_numero", $numero);
        return $this->db->get()->result();
    }
    public function liq_generaPDF($liqu_id = 0, $file=false){

        $this->db->select("liqu_numero as numero, liqu_saldo_anterior as saldo, liqu_clie_rsocial as rsocial, liqu_clie_id as clie_id, liqu_tipo as tipo, DATE_FORMAT(liqu_fechareg,'%d/%m/%Y') as fecha, liqu_moneda as moneda,liqu_clie_num as clie_numero, liqu_clie_num as clie_num, liqu_usua");
        $this->db->from('liquidacion');
        $this->db->where("liqu_id", $liqu_id);
        $liquidacion = $this->db->get()->row();

        $simbolo = ($liquidacion->moneda == "SOLES")?"S/ ":"$ ";

        if($liquidacion->tipo == 'RECEPTIVO'){

            $lpaquetes = $this->db->where("lpaq_liqu_id",$liqu_id)->get("liquidacion_paqu")->result();
            
            $ddt = $this->get_liqu_paquete($liqu_id);
            $datos["detas"] = $ddt["paqu"];
            $datos["total"] = $ddt["total"];
            $datos["liqu"] = $liquidacion;

            $datos["seleccionados"] = "";
            $datos["titulo"] = "Generar Orden de Pago";
            $simb = ($liquidacion->moneda == 'SOLES')?"S/ ":"$ ";
            $anterior = ($liquidacion->saldo > 0)?'Saldo anterior: '.$simb.number_format($liquidacion->saldo, 2, '.', ' '):'';
            $html ='';
            
            $html.= '<h2 style="text-align:center;">Liquidacion para: '.$liquidacion->rsocial.'</h2>';

            $html.= '<table><tr><th><strong>Liquidacion N??  '.$liquidacion->numero.' - '.$liquidacion->clie_num.'</strong></th><th align="right"><strong>'.$anterior.'&nbsp;&nbsp;&nbsp;&nbsp;Fecha  : '.$liquidacion->fecha.'</strong>&nbsp;&nbsp;</th></tr></table>';

            $html.= '</br></br><table cellpadding="2" cellspacing=".5"  class="table table-striped table-bordered table-genOrdPago">
                        <thead >
                            <tr style="background-color:#4078a5; color: #fff;">
                                <th align="center" border=".5"  width="50"><strong>FILE</strong></th>
                                <th align="center" border=".5" width="120"><strong>GRUPO / NOMBRE</strong></th>
                                <th align="center" border=".5" width="50"><strong>FECHA</strong></th>
                                <th align="center" border=".5" width="25"><strong>PAX</strong></th>
                                <th align="center" border=".5" width="120"><strong>EXCURSION</strong></th>
                                <th align="center" border=".5" width="50"><strong>PU</strong></th>
                                <th align="center" border=".5" width="50"><strong>TOTAL</strong></th>
                                <th align="center" border=".5" class="col-sm-1"><strong>TOTAL A PAGAR</strong></th>
                            </tr>
                        </thead>
                        <tbody border="1">';
                             
                                $almuerzos = 0;
                                $paxs = 0;
                                foreach ($datos['detas'] as $i => $val):
                                    $rowspan =  count($val->detalles);
                           
                                
                                foreach ($val->detalles as $j => $det): 
                                    $html.= '<tr>';
                                if(count($val->lpaq_nombre)>=25){
                                        $val->lpaq_nombre = substr($val->lpaq_nombre,0,25).'...';   
                                    }
                                 if(count($det->servicio)>=25)   
                                    $det->servicio = substr($det->servicio,0,25).'...';
                                    if($j == 0){
                                 $html.='<td width="50" style="border-top:.5 solid black; border-left: .5 solid black;border-right:.5 solid black;">'. $val->lpaq_file.'</td>
                                        <td width="120" style="border-top:.5 solid black; border-left: .5 solid black;border-right:.5 solid black;">'. $val->lpaq_nombre.'</td>';
                                    }else{
                                        $html.='<td style="border-left: .5 solid black;border-right:.5 solid black;"></td><td style="border-left: .5 solid black;border-right:.5 solid black;"></td>';
                                    }
                                    
                                
                                    $html.='<td width="50" align="center" style="border: .5 solid black;"  class="edt">'. $det->fecha .'</td>
                                            <td width="25" align="center" style="border: .5 solid black;" class="edt">'. $det->pax.'</td>
                                            <td width="120" style="border: .5 solid black;" class="edt">'. 
                                            $det->servicio.
                                            '</td>
                                            <td width="50" align="right" style="border: .5 solid black;" class="edt">'. $det->precio.'</td>
                                            <td width="50" align="right" style="border: .5 solid black;" class="edt">'.$det->total.'</td>';
                                
                                    if($j == 0){
                                        $html.='<td align="right" style="border-top:.5 solid black; border-left: .5 solid black;border-right:.5 solid black;">'.$val->lpaq_total.'</td>';
                                    }else{
                                        $html.='<td style="border-left: .5 solid black;border-right:.5 solid black;"></td style="border-left: .5 solid black;border-right:.5 solid black;">';
                                    }
                                    $html.='</tr>';
                                endforeach;
                            endforeach;

                            

                            $html.='<tr border="1">
                                <th style="border-top: 1 solid black;" align="right"class="text-right" colspan="7">TOTAL</th>
                                <th align="right" border="1"><strong> '.$simb.number_format($datos['total']+$liquidacion->saldo, 2, '.', ' ') .'</strong></th>
                            </tr>
                        </tbody>
                    </table>';

            $html = str_replace('</br>','<br />',$html);
           
            $this->load->library('tcpdf');
            $this->pdf = new TCPDF('P', PDF_UNIT, '', true, 'UTF-8', false);
            $this->pdf->setPrintHeader(false);
            $this->pdf->SetCreator(PDF_CREATOR);
            $this->pdf->SetAuthor('grupo sistemas');
            $this->pdf->SetTitle('liquidacion receptivo');
			$this->pdf->AddPage();
            $this->pdf->SetFont('', 'B', 10);
            $this->pdf->Cell(60, 5, 'EMPRESA JUMBO', 0, 1, 'L');
            $this->pdf->Cell(60, 5, 'TRAVEL E.I.R.L.', 0, 1, 'L');
            $this->pdf->SetY(10);
            $this->pdf->SetHeaderData('', '', '                                 LIQUIDACION PARA : '.$liquidacion->rsocial,'         LIQ N??   '.$liquidacion->numero.'            Fecha  : '.$liquidacion->fecha.'');
            $this->pdf->SetFont('helvetica', '', 7.5);
            $this->pdf->Image(base_url().'assets/img/logo_jumbo.jpg', 164, 8, 35,0 ,'','','',true, 100);
            $this->pdf->Ln();
            $this->pdf->WriteHTML($html);
            $this->pdf->SetFont('','B',8);
            $this->pdf->Cell(190,6,'GRACIAS POR ELEGIRNOS NOS ESFORZAMOS CADA DIA POR BRINDARLE UN MEJOR SERVICIO',0,0,'C');
            $this->pdf->Ln(25);
            $this->pdf->Cell(95,6,'______________________',0,0,'C');
            $this->pdf->Cell(95,6,'_________________________________',0,0,'C');
            $this->pdf->Ln();
            $this->pdf->Cell(95,6,'CLIENTE',0,0,'C');
            $this->pdf->Cell(95,6,'GERENCIA JUMBO TRAVEL',0,0,'C');

            $this->pdf->Output('LIQ N?? '.$liquidacion->numero.' '.$liquidacion->fecha.'.pdf', 'I');
        }else{
            $detalle = $this->Model_general->getDetaLiqu($liqu_id);
			

            $user = $this->Model_general->getUsuarios($liquidacion->liqu_usua);

            $this->load->library('pdf');
            $this->pdf = new Pdf();
            $this->pdf->AddPage();
            $this->pdf->AliasNbPages();
            $this->pdf->SetTitle("HOJA DE LIQUIDACION");
            $this->pdf->set_user($user->nombres);
            $this->pdf->SetLeftMargin(10);
            
            $this->pdf->SetFont('Arial', '', 8);
            $this->pdf->Image(base_url().'assets/img/logo_jumbo.jpg', 164, 5, 35,0 , 'JPG');
            $html = "<font size='12' color='#34495E'><strong>EMPRESA JUMBO</strong></font><br>";
            $html .= "<font size='12' color='#34495E'><strong>TRAVEL E.I.R.L.</strong></font><br>";
            $this->pdf->SetY(7);
            $this->pdf->WriteHTML(utf8_decode($html));
            $this->pdf->tbr = 3.5;
            $this->pdf->SetY(10);
            $this->pdf->SetFont('Arial', 'B', 16);

            $this->pdf->Cell(190,8,utf8_decode('LIQUIDACION PARA '.$liquidacion->rsocial),0,0,'C');
            $this->pdf->Ln();
            $this->pdf->SetFont('', '', 8);
            $this->pdf->Cell(20,8,utf8_decode('LIQ N?? '.$liquidacion->numero.' - '.$liquidacion->clie_numero),0,0,'L');
            $total_total = 0;

            $adicionn = "";
            
            if($liquidacion->saldo != 0 && $liquidacion->saldo != ""){
                $adicionn .= "Saldo Anterior: ".$simbolo.number_format($liquidacion->saldo,2,'.',' ');
            }
            
            $adicionn .= "        Fecha: ".$liquidacion->fecha;
            $this->pdf->Cell(170,7,utf8_decode($adicionn),0,0,'R');                

            $header = array('FECHA', 'PAX', 'EXCURSION', 'NOMBRE', 'LUNCH', 'HOTEL', 'ENDOSE', 'TOTAL');
            $this->pdf->Ln(8);
            $w = array(15, 7, 30, 35, 10, 42, 35, 15);
            $this->pdf->SetFont('','B','');
            $this->pdf->SetFillColor('70','130','180'); 
            $this->pdf->SetTextColor(255);
            for($i = 0; $i < count($header); $i++)
                $this->pdf->Cell($w[$i],6,utf8_decode($header[$i]),0,0,'C',true);
            $this->pdf->Ln();
            $this->pdf->SetFont('');
            $this->pdf->SetTextColor(0);

            $indice = 0;
            $lineas = 0;
            foreach ($detalle as $num => $det) {
                $total_total += $det->total;
				$guia1 = ($det->guia != "")?" / ".$det->guia:"";
				$det->nombre = $det->nombre.$guia1;
                preg_match_all("/.{1,20}[^ ]*/",$det->hotel,$arra);
                $det->hotel = implode("\r\n",$arra[0]);
                $hline = 7;
                $dess = array();
                if(preg_match("/\n/",$det->hotel)){
                    $dess = explode("\n",$det->hotel);
                    $det->hotel = $dess[0];
                    $hline = 3;
                    $this->pdf->Ln(2);
                }

                preg_match_all("/.{1,15}[^ ]*/",$det->nombre,$arra);
                $det->nombre = implode("\r\n",$arra[0]);
                $hline = 7;
                $dess1 = array();
                if(preg_match("/\n/",$det->nombre)){
                    $dess1 = explode("\n",$det->nombre);
                    $det->nombre = $dess1[0];
                    $hline = 3;
                    $this->pdf->Ln(2);
                }
				if($det->paqu_estado == 'ANULADO')
					$this->pdf->SetTextColor(255,0,0);
                $this->pdf->Cell($w[0],$hline,$det->fecha,'',0,'C');
                $this->pdf->Cell($w[1],$hline,$det->pax,'',0,'C');
                $this->pdf->Cell($w[2],$hline,utf8_decode($det->serv_name),'',0,'C');
                $this->pdf->Cell($w[3],$hline,utf8_decode($det->nombre),'',0,'C');
                $this->pdf->Cell($w[4],$hline,utf8_decode($det->lunch_efect),'',0,'C');
                $this->pdf->Cell($w[5],$hline,utf8_decode($det->hotel),'',0,'C');
                $this->pdf->Cell($w[6],$hline,utf8_decode($det->endose),'',0,'C');
                $this->pdf->Cell($w[7],$hline,$det->total,'',0,'R');
                $this->pdf->Ln();
                $lineas++;
                if(count($dess)>0 || count($dess1)>0){
                    $max = (count($dess) > count($dess1))?count($dess):count($dess1);

                    for ($i=1; $i < $max; $i++) { 
                        $this->pdf->Cell($w[0],$hline,'','',0,'C');
                        $this->pdf->Cell($w[1],$hline,'','',0,'C');
                        $this->pdf->Cell($w[2],$hline,'','',0,'C');
                        $this->pdf->Cell($w[3],$hline,(isset($dess1[$i]))?utf8_decode($dess1[$i]):'','',0,'C');
                        $this->pdf->Cell($w[4],$hline,'','',0,'C');
                        $this->pdf->Cell($w[5],$hline,(isset($dess[$i]))?utf8_decode($dess[$i]):'','',0,'C');
                        $this->pdf->Cell($w[6],$hline,'','',0,'C');
                        $this->pdf->Cell($w[7],$hline,'','',0,'C');   
                        $this->pdf->Ln();
                        $lineas++;
                    }
                    $this->pdf->Ln(2);
                 }
				 if($det->paqu_estado == 'ANULADO')
					$this->pdf->SetTextColor(0,0,0);
                $this->pdf->line(10,$this->pdf->GetY(),200,$this->pdf->GetY());
                $indice++;
            }
            
            $this->pdf->SetTextColor(29,112,183);
            $this->pdf->SetFont('','B','');
            $this->pdf->Cell(170,6,'TOTAL A PAGAR',0,0,'R');
            $this->pdf->SetFont('');
            $this->pdf->Cell(20,6,$simbolo.number_format($total_total + $liquidacion->saldo,2,'.',' '),0,0,'R');
            $this->pdf->Ln();
            $this->pdf->SetTextColor(0,0,0);
            $this->pdf->SetFont('','B','');
            $this->pdf->Cell(190,6,'GRACIAS POR ELEGIRNOS NOS ESFORZAMOS CADA DIA POR BRINDARLE UN MEJOR SERVICIO',0,0,'C');
            $this->pdf->Ln(25);
            $this->pdf->Cell(95,6,'______________________',0,0,'C');
            $this->pdf->Cell(95,6,'_________________________________',0,0,'C');
            $this->pdf->Ln();
            $this->pdf->Cell(95,6,'CLIENTE',0,0,'C');
            $this->pdf->Cell(95,6,'GERENCIA JUMBO TRAVEL',0,0,'C');
			
            $archivo = 'LIQ N?? '.$liquidacion->numero.' - '.$liquidacion->rsocial;
            if($file==false){
                $doc = $this->pdf->Output($archivo,'S');
                return $doc;        
            }else{
                $this->pdf->Output($archivo,'I');
            }
        }
    }
    public function liq_generaPDF_old($liqu_id = 0, $file=false){

        $this->db->select("liqu_numero as numero, liqu_clie_rsocial as rsocial, liqu_clie_id as clie_id");
        $this->db->from('liquidacion');
        $this->db->where("liqu_id", $liqu_id);
        $liquidacion = $this->db->get()->row();
        $saldo = $this->getSaldoAnterior($liquidacion->clie_id, $liquidacion->numero);
        
        $detalle = $this->Model_general->getDetaLiqu($liqu_id);

        $this->load->library('pdf');
        $this->pdf = new Pdf();
        $this->pdf->AddPage();
        $this->pdf->AliasNbPages();
        $this->pdf->SetTitle("HOJA DE LIQUIDACION");

        $this->pdf->SetLeftMargin(10);
        
        $this->pdf->SetFont('Arial', 'B', 20);

        $this->pdf->Cell(190,8,utf8_decode('LIQUIDACION PARA '.$liquidacion->rsocial),0,0,'C');
        $this->pdf->Ln();
        $this->pdf->SetFont('', '', 8);
        $this->pdf->Cell(20,8,utf8_decode('LIQ N?? '.$liquidacion->numero),0,0,'L');
        $total_total = 0;
        foreach ($saldo as $key => $val) {
            $total_total += $val->liqu_saldo;
            if($key == 0)
                $this->pdf->Cell(150,7,utf8_decode('SALDO: LIQ N?? '.$val->numero),0,0,'R');
            else
                $this->pdf->Cell(170,7,utf8_decode('LIQ N?? '.$val->numero),0,0,'R');
            $this->pdf->Cell(20,7,$val->liqu_saldo,0,0,'R');
            $this->pdf->Ln(4);
        }
        $header = array('FECHA', 'N?? PAX', 'EXCURSION', 'NOMBRE', 'GUIA', 'LUNCH', 'HOTEL', 'TOTAL');
        $this->pdf->Ln(8);
        $w = array(15, 15, 40, 35, 30, 15, 20, 20);
        $this->pdf->SetFont('','B','');
        $this->pdf->SetFillColor('70','130','180'); 
        $this->pdf->SetTextColor(255);
        for($i = 0; $i < count($header); $i++)
            $this->pdf->Cell($w[$i],6,utf8_decode($header[$i]),0,0,'C',true);
        $this->pdf->Ln();
        $this->pdf->SetFont('');
        $this->pdf->SetTextColor(0);

        $indice = 0;
        $lineas = 0;
        foreach ($detalle as $num => $det) {
            $total_total += $det->total;

            $numero = 0;
            preg_match_all("/.{1,20}[^ ]*/",$det->nombre,$arra);
            $det->nombre = implode("\r\n",$arra[0]);
            $hline = 7;
            $dess = array();
            if(preg_match("/\n/",$det->nombre)){
                $dess = explode("\n",$det->nombre);
                $det->nombre = $dess[0];
                $hline = 3;
                $this->pdf->Ln(2);
            }

            $this->pdf->Cell($w[0],$hline,$det->fecha,'',0,'C');
            $this->pdf->Cell($w[1],$hline,$det->pax,'',0,'C');
            $this->pdf->Cell($w[2],$hline,utf8_decode($det->serv_name),'',0,'C');
            $this->pdf->Cell($w[3],$hline,utf8_decode($det->nombre),'',0,'C');
            $this->pdf->Cell($w[4],$hline,utf8_decode($det->guia),'',0,'C');
            $this->pdf->Cell($w[5],$hline,utf8_decode($det->lunch_efect),'',0,'C');
            $this->pdf->Cell($w[6],$hline,utf8_decode($det->hotel),'',0,'C');
            $this->pdf->Cell($w[7],$hline,$det->total,'',0,'R');
            $this->pdf->Ln();
            $lineas++;
            
            if(count($dess)>0){
                unset($dess[0]);
                foreach($dess as $desc){
                    $this->pdf->Cell($w[0],$hline,'','',0,'C');
                    $this->pdf->Cell($w[1],$hline,'','',0,'C');
                    $this->pdf->Cell($w[2],$hline,'','',0,'C');
                    $this->pdf->Cell($w[3],$hline,utf8_decode($desc),'',0,'C');
                    $this->pdf->Cell($w[4],$hline,'','',0,'C');
                    $this->pdf->Cell($w[5],$hline,'','',0,'C');
                    $this->pdf->Cell($w[6],$hline,'','',0,'C');
                    $this->pdf->Ln();
                    $lineas++;
                }
                $this->pdf->Ln(2);
             }
            $this->pdf->line(10,$this->pdf->GetY(),200,$this->pdf->GetY());
            $indice++;
        }
        
        $this->pdf->SetTextColor(29,112,183);
        $this->pdf->SetFont('','B','');
        $this->pdf->Cell(170,6,'TOTAL A PAGAR',0,0,'R');
        $this->pdf->SetFont('');
        $this->pdf->Cell(20,6,number_format($total_total,2,'.',','),0,0,'R');
        $this->pdf->Ln();
        
        $archivo = 'LIQ N?? '.$liquidacion->numero.' - '.$liquidacion->rsocial;
        if($file==false){
            $doc = $this->pdf->Output($archivo,'S');
            return $doc;        
        }else{
            $this->pdf->Output($archivo,'I');
        }
    }
    public function liq_cobrar($id){
        $this->load->helper('Funciones');
        
        $datos["moneda"] = $this->Model_general->enum_valores('cuenta_movimiento','movi_moneda');
        $datos["liquidacion"] = $this->Model_general->getLiquTotal($id);
        $datos["documentos"] = $this->Model_general->getOptionsWhere("comprobante_tipo",array("tcom_id","tcom_nombre"),array("tcom_id<>"=>'07', "tcom_id<>"=>'08'));
        $datos["cuentas"] = $this->Model_general->getOptions("cuenta",array("cuen_id","cuen_banco"),'* Cuenta');
        $this->load->view('Liquidacion/form_cobrar', $datos);
    }
    public function guardar_cobro($id = ''){
        $pagado = $this->input->post('pagado');
        $cancelado = $this->input->post('cancelado');
        $saldo = $this->input->post('saldo');
        $observacion = $this->input->post('observacion');
        $moneda = $this->input->post('moneda');
        $total = $this->input->post('total');
        $tdoc = $this->input->post('documento');
        $serie = $this->input->post('serie');
        $numero = $this->input->post('numero');
        $fecha = $this->Model_general->fecha_to_mysql($this->input->post('fecha'));

        $cuenta = $this->input->post('cuenta');
        $codigo_cuen = $this->input->post('codigo-cuen');
        if($cuenta == '' || $codigo_cuen == '' || $pagado == ''){
            $json['exito'] = false;  
            $json['mensaje'] = "Cuenta, C??digo y Pagado son obligatorios";
            echo json_encode($json);
            exit(0);
        }
        
        $liq = $this->db->select('CONCAT("LIQ-",liqu_numero," : ",liqu_clie_rsocial) as numero, liqu_estado, liqu_numero, liqu_cobrodesc')->where("liqu_id", $id)->get("liquidacion")->row();
        if($liq->liqu_estado == 'PAGADO'){
            $json['exito'] = false;  
            $json['mensaje'] = "La liquidaci??n ya esta cobrada";
            echo json_encode($json);
            exit(0);   
        }
        $this->db->trans_start();
        
        $movimiento = $this->Model_general->actualizarCaja(1, 'INGRESO', $tdoc, $serie, $numero, "Cobro de ".$liq->numero, $pagado, $moneda, $this->usua_id, $id, '', $cuenta,$codigo_cuen,$fecha,$observacion);

        $desc = ($liq->liqu_cobrodesc != "")?$liq->liqu_cobrodesc." / ".$observacion:$observacion;
        $prev = array("liqu_cobrofecha" => $fecha,"liqu_cobrodesc" => $desc);
        if(($cancelado + $pagado) >= $total){
            $dte = array("liqu_estado" => 'PAGADO', "liqu_cobrado" => ($cancelado + $pagado));
			$this->actualizaPagoPaquete($id,1,$fecha,"cobrado en LIQ N?? ".$liq->liqu_numero);
        }else{
            $dte = array("liqu_cobrado" => ($cancelado + $pagado));
        }
        $dte = array_merge($dte,$prev);

        $this->Model_general->guardar_edit_registro("liquidacion", $dte, array('liqu_id' => $id));
        $this->Model_general->add_log("COBRO",6,"Cobro de Hoja de liquidaci??n ".$liq->numero." ".$pagado." / ".$total." ".$moneda.", C??digo de caja: ".$codigo_cuen);

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE){
            $json['exito'] = false;
            $json['mensaje'] = "Error al guardar los datos";
        }else{
            $json['exito'] = true;  
            $json['mensaje'] = "Datos guardados con exito";
        }
        echo json_encode($json);
    }
	public function cancelar_cobroLiquidacion($id = ''){
        $fecha = date("Y-m-d");
        
        $liq = $this->db->select('CONCAT("LIQ-",liqu_numero," : ",liqu_clie_rsocial) as numero, liqu_estado, liqu_numero, liqu_total, liqu_moneda, liqu_cobrado')->where("liqu_id", $id)->get("liquidacion")->row();
		
        $mov = $this->db->where(array("movi_ref_id" => $id, "movi_tipo_id" => 1))->get("cuenta_movimiento");
		
		if($mov->num_rows() > 0){
            $mov = $mov->result();
			
			
			if($liq->liqu_cobrado <= 0){
				$json['exito'] = false;  
				$json['mensaje'] = "No hay cobros que cancelar";
				echo json_encode($json);
				exit(0);   
			}
			$this->db->trans_start();
			
			foreach($mov as $row){
				$this->Model_general->actualizarCaja(1, 'EGRESO', "", "", "", "Anulacion de cobro de ".$liq->numero, $liq->liqu_total, $liq->liqu_moneda, $this->usua_id, $id, '', $row->movi_cuen_id,"000000",$fecha,"Amulacion de cobro");
				$this->Model_general->guardar_edit_registro("cuenta_movimiento", array("movi_file" => "000000"), array('movi_id' => $row->movi_id));
			}		
			$dte = array("liqu_estado" => 'PENDIENTE', "liqu_cobrado" => "0", "liqu_cobrodesc" => null, "liqu_cobrofecha" => null);
			$this->Model_general->guardar_edit_registro("liquidacion", $dte, array('liqu_id' => $id));
			
			

			$this->actualizaPagoPaquete($id,0,$fecha,"");
			$this->Model_general->add_log("COBRO",6,"Anulacion de cobro de Hoja de liquidaci??n ".$liq->numero." ".$liq->liqu_total." ".$liq->liqu_moneda.", C??digo de caja: ANULADO");

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
            $json['mensaje'] = "La liquidacion fue pasada como saldo";
		}
        echo json_encode($json);
    }
    public function actualizaPagoPaquete($liqu_id, $estado,$fecha,$obs){
        $consulta = $this->db->where("lpaq_liqu_id",$liqu_id)->get("liquidacion_paqu")->result();
        foreach ($consulta as $row) {
			$this->Model_general->actualizaPaqueteDetalle($row->lpaq_paqu_id,$estado);
            if($estado == 1){
                $this->db->query("UPDATE paquete SET paqu_escobrado = '{$estado}', paqu_cobrado = paqu_total, paqu_cobrofecha = '{$fecha}', paqu_cobrodesc = '{$obs}' WHERE paqu_id = '{$row->lpaq_paqu_id}'");
            }else{
                $this->db->query("UPDATE paquete SET paqu_escobrado = '{$estado}', paqu_cobrado = 0, paqu_cobrofecha = NULL, paqu_cobrodesc = NULL WHERE paqu_id = '{$row->lpaq_paqu_id}'");
            }
        }
    }
    public function get_seleccionados($sel){
        $paquetes = $this->db->query("SELECT P.paqu_prefijo, P.paqu_esliquidacion, CHAR(P.paqu_letra) as paqu_letra, P.paqu_id, P.paqu_moneda, P.paqu_tipo, P.paqu_clie_id, P.paqu_clie_rsocial, P.paqu_numero, P.paqu_nombre, P.paqu_total, SUM(D.deta_total) as suma, GROUP_CONCAT(D.deta_id) as detas FROM paquete_detalle as D JOIN paquete as P ON paqu_id = deta_paqu_id WHERE deta_id IN (".$sel.") GROUP BY paqu_id")->result();
        return $paquetes;
    }
    public function get_seleccionadosPaqu($sel){
        $paquetes = $this->db->query("SELECT P.paqu_prefijo, P.paqu_id, P.paqu_moneda, P.paqu_tipo, P.paqu_clie_id, P.paqu_clie_rsocial, P.paqu_numero, P.paqu_nombre, P.paqu_total, P.paqu_total as suma, GROUP_CONCAT(D.deta_id) as detas FROM paquete as P JOIN paquete_detalle as D ON paqu_id = deta_paqu_id WHERE P.paqu_id IN (".$sel.") GROUP BY P.paqu_id ORDER BY P.paqu_numero ASC ")->result();
        return $paquetes;
    }
    public function get_detaPaqu($deta_id){
        $this->db->select("deta_pax as pax, DATE_FORMAT(deta_fechaserv, '%d/%m/%Y') as fecha, deta_precio as precio, deta_servicio as servicio, deta_total as total, deta_guia as guia, deta_hotel as hotel, deta_serv_id as serv_id, deta_lunch as lunch, deta_lunch_pre as lunch_pre");
                $this->db->where("deta_id", $deta_id);
                $this->db->order_by("deta_fechaserv", "ASC");
        $cons = $this->db->get("paquete_detalle")->row();
        return $cons;
    }
    public function liqu_genReceptivo(){
        $seleccionados = $this->input->get('sel');
        $paquetes = $this->get_seleccionadosPaqu($seleccionados);

        $detas = array();
        $total = 0;
        foreach ($paquetes as $i => $val) {
            $dt = new stdClass();
            $dt->paqu_id = $val->paqu_id;
            $dt->paqu_file = $val->paqu_prefijo."-".$val->paqu_numero;
            $dt->paqu_nombre = $val->paqu_nombre;
            
            $detalles = explode(',', $val->detas);
            $det_inf = array();
            $suma = $val->paqu_total;
            foreach ($detalles as $det) {
                $cons = $this->get_detaPaqu($det);               

                $cons->fecha = "<a title='Editar Servicio' class='pdet_id oculto' href='".base_url()."Registro/cambiar_pdet/".$det."'></a><strong>".$cons->fecha."</strong>";
                
                $adides = $this->Model_general->adiciones_pagoGen($det, "paquete_adicion","padi_descripcion","padi_monto","padi_tipo","padi_pdet_id");
                $adic_desc = $adides["desc"];
                $adic_monto = $adides["monto"];
                //$suma += $val->paqu_total;
                
                $cons->total = "<strong>".number_format($cons->total, 2, '.', ' ')."</strong></br>".number_format($cons->lunch_pre*$cons->lunch, 2, '.', ' ').$adic_monto;
                
                $cons->servicio = "<strong>".$cons->servicio."</strong></br>Lunch(x".$cons->lunch.")".$adic_desc;
                $cons->precio = "<strong> - </strong></br>".$cons->lunch_pre;

                $det_inf[] = $cons;
            }

            $dt->detalles = $det_inf;
            //$dt->paqu_total = $val->paqu_total;
            $total += $suma;
            $dt->paqu_total = number_format($val->paqu_total, 2, '.', ' ');
            $detas[$i] = $dt;
        }
        
        $datos["total"] = $total;
        $indicador = count($paquetes)-1;
        $clie = $this->db->select("clie_tdoc_id, clie_doc_nro")->where("clie_id", $paquetes[$indicador]->paqu_clie_id)->get("cliente")->row();
        
        $detas = (object)$detas;
        $liqu = new stdClass();
        $liqu->liqu_id = '';
        $liqu->liqu_moneda = $paquetes[$indicador]->paqu_moneda;
        $liqu->liqu_numero = $this->nextnum($paquetes[$indicador]->paqu_clie_id);
        $liqu->liqu_clie_num = $this->nextnumclie($paquetes[$indicador]->paqu_clie_id);
        $liqu->liqu_clie_id = $paquetes[$indicador]->paqu_clie_id;
        $liqu->liqu_clie_rsocial = $paquetes[$indicador]->paqu_clie_rsocial;
        $liqu->liqu_clie_tdoc_id = $clie->clie_tdoc_id;
        $liqu->liqu_clie_doc_nro = $clie->clie_doc_nro;
        $liqu->liqu_obs = "";
		$liqu->liqu_incluyesaldo = "SI";
        $liqu->liqu_saldo_anterior = 0.00;
        $saldos = $this->SaldoAnterior($paquetes[$indicador]->paqu_clie_id);
        $datos["saldos"] = $saldos;
        $datos["documentos"] = $this->Model_general->getOptions('documento_tipo', array("tdoc_id", "tdoc_nombre"),'* Documento');
        $datos["moneda"] = $this->Model_general->enum_valores('liquidacion','liqu_moneda');
        
        $datos["detas"] = $detas;
        $datos["liqu"] = $liqu;
        $datos["seleccionados"] = $seleccionados;
        $datos["titulo"] = "Generar Hoja de liquidaci??n (receptivo)";
        $this->load->helper('Funciones');
        $this->load->model("Model_general");
        $this->load->library('Ssp');
        $this->load->library('Cssjs');
        $this->cssjs->add_js(base_url().'assets/js/Liquidacion/ordPago_gen.js?v=2.4',false,false);
        $script['js'] = $this->cssjs->generate_js();
        $this->load->view('header', $script);
        $this->load->view('menu');
        $this->load->view($this->router->fetch_class().'/'.$this->router->fetch_method(), $datos);
        $this->load->view('footer');
        
    }
    public function liqu_ordPago($liquidacion=''){

        $lpaquetes = $this->db->where("lpaq_liqu_id",$liquidacion->liqu_id)->get("liquidacion_paqu")->result();
		$saldos = $this->SaldoAnterior($liquidacion->liqu_clie_id);
        $datos["saldos"] = $saldos;
        $ddt = $this->get_liqu_paquete($liquidacion->liqu_id);
		
        $datos["detas"] = $ddt["paqu"];
        $datos["total"] = $ddt["total"];
        $datos["liqu"] = $liquidacion;
        $datos["seleccionados"] = "";
        $datos["titulo"] = "Generar Orden de Pago";
        $datos["documentos"] = $this->Model_general->getOptions('documento_tipo', array("tdoc_id", "tdoc_nombre"),'* Documento');
        $datos["moneda"] = $this->Model_general->enum_valores('liquidacion','liqu_moneda');
        
        $this->load->helper('Funciones');
        $this->load->model("Model_general");
        $this->load->library('Ssp');
        $this->load->library('Cssjs');
        $this->cssjs->add_js(base_url().'assets/js/Liquidacion/ordPago_gen.js?v=2.4',false,false);
        $script['js'] = $this->cssjs->generate_js();
        $this->load->view('header', $script);
        $this->load->view('menu');
        $this->load->view($this->router->fetch_class().'/ordPago_edit', $datos);
        $this->load->view('footer');
    }
	
    public function get_liqu_paquete($liqu_id){
        $this->db->select("lpaq_id, lpaq_file, lpaq_nombre, lpaq_total");
        $this->db->from("liquidacion_paqu");
        $this->db->where("lpaq_liqu_id", $liqu_id);
        $paquetes = $this->db->get()->result();
		
        $total = 0;
        foreach ($paquetes as $key => $value) {
            $this->db->where("deta_lpaq_id", $value->lpaq_id);
            $detas = $this->db->get("liquidacion_detalle")->result();
            $det_aux = array();
            foreach ($detas as $i => $det) {
                $adides = $this->Model_general->adiciones_pagoGen($det->deta_id,"liquidacion_adicion","ladi_descripcion","ladi_monto","ladi_tipo","ladi_ldet_id");
                
                $detalles = new StdClass;
                $detalles->pax = $det->deta_pax;
                $detalles->fecha = "<a title='Editar Servicio' class='pdet_id oculto' href='".base_url()."Liquidacion/cambiar_ldet/".$det->deta_id."'></a>".$this->Model_general->mysql_to_fecha($det->deta_fecha);
                $detalles->precio = "<strong> - </strong></br>".number_format($det->deta_lunch_prec, 2, '.', ' ');
                $detalles->servicio = "<strong>".$det->deta_serv_name."</strong></br>Lunch(x".$det->deta_lunch_efect.")".$adides["desc"];
                $detalles->total = "<strong>".number_format($det->deta_total, 2, '.', ' ')."</strong></br>".number_format($det->deta_lunch_prec * $det->deta_lunch_efect, 2, '.', ' ').$adides["monto"];
                $detalles->guia = $det->deta_guia;
                $detalles->hotel = $det->deta_hotel;
                $detalles->serv_id = $det->deta_serv_id;
                $det_aux[$i] = $detalles;
            }
            //$total += $det->deta_total;
			$total += $value->lpaq_total;
            $value->detalles = $det_aux;
            $value->lpaq_total = number_format($value->lpaq_total, 2, '.', ' ');
        }
        $resp["paqu"] = $paquetes;
        $resp["total"] = $total;
        return $resp;
    }
    public function cambiar_ldet($ldet_id = ''){
        $this->db->select("deta_id as id, deta_lunch_efect as lunch, deta_lunch_prec as lunch_pre, deta_serv_id as serv_id, deta_serv_name as serv, deta_serv_prec as precio, DATE_FORMAT(deta_fecha, '%d/%m/%Y') as fecha, deta_pax as pax, deta_total as total");
        $pdet = $this->db->where("deta_id", $ldet_id)->get("liquidacion_detalle")->row();

        $this->db->select("ladi_id as id, ladi_ldet_id as pdet_id, ladi_descripcion as descripcion, ladi_monto as monto, ladi_tipo as tipo");
        $adiciones =  $this->db->where("ladi_ldet_id", $ldet_id)->get("liquidacion_adicion")->result();
        $servicio = $this->Model_general->getOptions("servicio",array("serv_id", "serv_descripcion"));

        $data["pdet"] = $pdet;
        $data["adic"] = json_encode($adiciones);
        $data["servicios"] = $servicio;
        $this->load->view($this->router->fetch_class().'/'.$this->router->fetch_method(), $data);
    }
    public function ldet_guardar($deta_id = ''){

        $fecha = $this->Model_general->fecha_to_mysql($this->input->post("fecha"));
        $serv_name = $this->input->post("serv_name");
        $serv_id = $this->input->post("serv_id");
        $pax = $this->input->post("pax");
        $precio = $this->input->post("precio");
        $total = $this->input->post("total");
        $lunch = $this->input->post("lunch");
        $lunch_pre = $this->input->post("lunch_pre");

        $adic_name = $this->input->post("adic_nombre");
        $adic_id = $this->input->post("adic_id");
        $adic_precio = $this->input->post("adic_precio");
        $desc_name = $this->input->post("desc_nombre");
        $desc_id = $this->input->post("desc_id");
        $desc_precio = $this->input->post("desc_precio");

        $datas = array("deta_fecha" => $fecha,
                        "deta_serv_name" => $serv_name,
                        "deta_serv_id" => $serv_id,
                        "deta_pax" => $pax,
                        "deta_serv_prec" => $precio,
                        "deta_total" => $total,
                        "deta_lunch_efect" => $lunch,
                        "deta_lunch_prec" => $lunch_pre
        );
        $where = array("deta_id" => $deta_id);
        $this->db->trans_begin();
        if($this->Model_general->guardar_edit_registro("liquidacion_detalle", $datas, $where)){
            
            if(!$this->actualiza_precios($deta_id)){
                $this->db->trans_rollback();
                $resp["exito"] = false;
                $resp["mensaje"] = "Ocurrio un error, intentelo m??s tarde(act precios).";
                $this->Model_general->dieMsg($resp);
            }
            
            if(isset($adic_id) || isset($desc_id)){
                $this->db->where('ladi_ldet_id', $deta_id);
                if(isset($adic_id))
                    $this->db->where_not_in('ladi_id',$adic_id);
                if(isset($desc_id))
                    $this->db->where_not_in('ladi_id',$desc_id);
                $this->db->delete('liquidacion_adicion');
            }
            if(isset($adic_precio)){
                if(!$this->addAdiciones($adic_precio, $adic_name, "ADICION", $deta_id, $adic_id)){
                    $this->db->trans_rollback();
                    $resp["exito"] = false;
                    $resp["mensaje"] = "Ocurrio un error, intentelo m??s tarde(adicioes).";
                    $this->Model_general->dieMsg($resp);
                }
            }
            if(isset($desc_precio)){
                if(!$this->addAdiciones($desc_precio, $desc_name, "DESCUENTO", $deta_id, $desc_id)){
                    $this->db->trans_rollback();
                    $resp["exito"] = false;
                    $resp["mensaje"] = "Ocurrio un error, intentelo m??s tarde(descuento).";
                    $this->Model_general->dieMsg($resp);
                }
            }
        }else{
            $this->db->trans_rollback();
            $resp["exito"] = false;
            $resp["mensaje"] = "Ocurrio un error, intentelo m??s tarde(general).";
            $this->Model_general->dieMsg($resp);
        }
        $this->db->trans_commit();
        $resp["exito"] = true;
        $resp["mensaje"] = "Reserva actualizada con exito.";
        echo json_encode($resp);
    }
    public function actualiza_precios($deta_id=''){
        $resp = true;

        $this->db->select("LP.lpaq_liqu_id as liqu_id, LP.lpaq_id as lpaq_id");
        $this->db->from("liquidacion_detalle as LD");
        $this->db->join("liquidacion_paqu as LP", "LD.deta_lpaq_id = LP.lpaq_id");
        $this->db->group_by("deta_lpaq_id");
        $d_paqu = $this->db->where("LD.deta_id", $deta_id)->get()->row();
        
        $t_lpaq = $this->db->query("SELECT SUM(deta_total) AS total FROM liquidacion_detalle WHERE deta_lpaq_id = {$d_paqu->lpaq_id}")->row();
        
        $data1 = array("lpaq_total" => $t_lpaq->total);
        if($this->Model_general->guardar_edit_registro("liquidacion_paqu", $data1, array("lpaq_id" => $d_paqu->lpaq_id))){
            $cons1 = $this->db->select("SUM(lpaq_total) as total")->where("lpaq_liqu_id", $d_paqu->liqu_id)->group_by("lpaq_liqu_id")->get("liquidacion_paqu")->row();

            $data2 = array("liqu_total" => $cons1->total);
            if(!$this->Model_general->guardar_edit_registro("liquidacion", $data2, array("liqu_id" => $d_paqu->liqu_id)))
                $resp = false;
        }else{
            $resp = false;
        }
        return $resp;
    }
    public function addAdiciones($precio='', $nombre, $tipo, $pdet,$adic_id = ''){
        $resp = true;
        for ($j=0; $j < count($precio); $j++) { 
            
            $item_adic = array("ladi_ldet_id" => $pdet,
                                "ladi_descripcion" => $nombre[$j],
                                "ladi_monto" => $precio[$j],
                                "ladi_tipo" => $tipo
            );
            if($adic_id != ''){
                if(empty($adic_id[$j])){
                    if($this->Model_general->guardar_registro("liquidacion_adicion", $item_adic)==FALSE){
                        $resp = false;
                    }
                }else{
                    if($this->Model_general->guardar_edit_registro("liquidacion_adicion", $item_adic, array("ladi_id" => $adic_id[$j]))==FALSE){
                        $resp = false;

                    }       
                }
                 
            }else{
                if($this->Model_general->guardar_registro("liquidacion_adicion", $item_adic)==FALSE){
                    $resp = false;
                }
            }
        }
        return $resp;
    }
    public function guardar_orden_pago($liqu_id = ''){
        
        $numero = $this->input->post("numero");
        $moneda = $this->input->post("moneda");
        $clie_id = $this->input->post("clie_id");
        $clie_rsocial = $this->input->post("rsocial");
        $doc_id = $this->input->post("documento");
        $clie_num = $this->input->post("clie_numero");
        $doc_nro = $this->input->post("docnum");
        $total = $this->input->post("total");

        $saldo = $this->input->post("saldo");
        $obs = $this->input->post("observacion");

        $sel = $this->input->post("seleccionados");
        $s_monto = ($this->input->post('s_monto'))?$this->input->post('s_monto'):"";
        $s_liqu_id = ($this->input->post('s_liqu_id'))?$this->input->post('s_liqu_id'):"";
		$s_incluye = ($this->input->post('incluye_saldo'))?"SI":"NO";

        $ldata = array("liqu_numero" => $numero,
                        "liqu_clie_id" => $clie_id,
                        "liqu_clie_num" => $clie_num,
                        "liqu_clie_rsocial" => $clie_rsocial,
                        "liqu_clie_tdoc_id" => $doc_id,
                        "liqu_clie_doc_nro" => $doc_nro,
                        "liqu_total" => floatval(str_replace(" ","",$total)),
                        "liqu_estado" => "PENDIENTE",
                        "liqu_moneda" => $moneda,
                        "liqu_usua" => $this->usua_id,
                        "liqu_tipo" => "RECEPTIVO",
                        "liqu_saldo_anterior" => $saldo,
                        "liqu_obs" => $obs,
						"liqu_saldo_anterior" => 0,
						"liqu_incluyesaldo" => $s_incluye
        );
        
        $this->db->trans_begin();

        if($liqu_id != ''){
            $condicion = array("liqu_id" => $liqu_id);
            
            if($this->Model_general->guardar_edit_registro("liquidacion", $ldata, $condicion)){
                $this->Model_general->add_log("EDITAR",6,"Edici??n de Hoja de liquidaci??n LIQ-".str_pad($numero, 8, "0", STR_PAD_LEFT));
            }
        }else{
            $paquetes = $this->get_seleccionadosPaqu($sel);
            $lfecha = array("liqu_fechareg" => date('Y-m-d H:i:s'));
            $ldata = array_merge($ldata,$lfecha);

            if(($liq = $this->Model_general->guardar_registro("liquidacion", $ldata))){
				if($s_incluye == "SI"){
					if($s_liqu_id != ""){
						$s_total = $this->actualizaLiquAnterior($s_monto,$s_liqu_id,$numero,$clie_num);
						$ddt = array("liqu_saldo_anterior" => $s_total);
						$ddcond = array("liqu_id" => $liq["id"]);
						$this->Model_general->guardar_edit_registro("liquidacion", $ddt, $ddcond);
					}
				}
                foreach ($paquetes as $paq) {
                    $liq_paq = array("lpaq_liqu_id" => $liq["id"],
                                        "lpaq_paqu_id" => $paq->paqu_id,
                                        "lpaq_file" => $paq->paqu_prefijo."-".$paq->paqu_numero,
                                        "lpaq_nombre" => $paq->paqu_nombre,
                                        "lpaq_total" => $paq->suma
                    );
                    $this->actualizarPaqueteEstado($paq->paqu_id,"1");
                    if(($lpaq = $this->Model_general->guardar_registro("liquidacion_paqu", $liq_paq))){
                        if( !$this->addDetalles_liquidacion(explode(',', $paq->detas), $lpaq["id"],$liq["id"],$paq->paqu_nombre) ){
                            $this->db->trans_rollback();
                            $resp["exito"] = false;
                            $resp["mensaje"] = "Ocurrio un error, intenlo m??s tarde";
                            $this->Model_general->dieMsg($resp);
                        }
                    }else{
                        $this->db->trans_rollback();
                        $resp["exito"] = false;
                        $resp["mensaje"] = "Ocurrio un error, intenlo m??s tarde";
                        $this->Model_general->dieMsg($resp);
                    }
                }
            }else{
                $this->db->trans_rollback();        
                $resp["exito"] = false;
                $resp["mensaje"] = "Ocurrio un error, intenlo m??s tarde";
                $this->Model_general->dieMsg($resp);
            }
            $this->Model_general->add_log("CREAR",6,"Creaci??n de Hoja de liquidaci??n LIQ-".str_pad($numero, 8, "0", STR_PAD_LEFT));
        }
        $resp["exito"] = true;
        $resp["mensaje"] = "Datos guardados con exito";
        $resp["direccion"] = base_url()."Liquidacion/liqu_listado";
        $this->db->trans_commit();
        echo json_encode($resp);
    }
    /*
    public function actualizaPaquete($paqu_id='',$estado){
        $resp = true;

        $tabla = "paquete";
        $datas = array("paqu_esliquidacion" => $estado);
        $condicion = array("paqu_id" => $paqu_id);
        if($this->Model_general->guardar_edit_registro($tabla, $datas, $condicion)){
            $tabla = "paquete_detalle";
            $datas = array("deta_esliquidacion" => $estado);
            $condicion = array("deta_paqu_id" => $paqu_id);
            if(!$this->Model_general->guardar_edit_registro($tabla, $datas, $condicion))
                $resp = false;
        }else{
            $resp = false;
        }
        return $resp;
    }
    */
    public function addAdiciones_liquidacion($adiciones,$ldet){
        $resp = true;
        foreach ($adiciones as $add) {
            $ladi = array("ladi_ldet_id" => $ldet,
                            "ladi_descripcion" => $add->padi_descripcion,
                            "ladi_monto" => $add->padi_monto,
                            "ladi_tipo" => $add->padi_tipo
            );
            if(!$this->Model_general->guardar_registro("liquidacion_adicion", $ladi)){
                $resp = false;
            }
        }
        return $resp;
    }
    public function addDetalles_liquidacion($detas, $lpaq,$liqu_id,$nombre){
        $resp = true;
        foreach ($detas as $det) {

            $cons = $this->get_detaPaqu($det);
            $liq_det = array("deta_liqu_id" => $liqu_id,
                                "deta_lpaq_id" => $lpaq,
                                "deta_pdet_id" => $det,
                                "deta_serv_id" => $cons->serv_id,
                                "deta_serv_name" => $cons->servicio,
                                "deta_serv_prec" => $cons->precio,
                                "deta_pax" => $cons->pax,
                                "deta_fecha" => $this->Model_general->fecha_to_mysql($cons->fecha),
                                "deta_guia" => $cons->guia,
                                "deta_hotel" => $cons->hotel,
                                "deta_nombre" => $nombre,
                                "deta_lunch" => $cons->lunch,
                                "deta_lunch_efect" => $cons->lunch,
                                "deta_lunch_prec" => $cons->lunch_pre,
                                "deta_total" => $cons->total
            );
            if(($ldet = $this->Model_general->guardar_registro("liquidacion_detalle", $liq_det))){
                $adiciones = $this->db->where("padi_pdet_id", $det)->get("paquete_adicion")->result();

                if(count($adiciones) > 0){
                    if(!$this->addAdiciones_liquidacion($adiciones, $ldet["id"])){
                        $resp = false;
                    }
                }
            }else{
                $resp = false;
            }
        }
        return $resp;
    }
    /*
    public function actualiza_nuimero_clientes(){
        $liquidaciones = $this->db->get("liquidacion")->result();
        foreach ($liquidaciones as $key => $val) {
            if($val->liqu_clie_num == 0){
                $numero = $this->nextnumclie($val->liqu_clie_id);
                $data = array("liqu_clie_num" => $numero);
                $where = array("liqu_id" => $val->liqu_id);
                $this->Model_general->guardar_edit_registro("liquidacion", $data, $where);
            }
        }
    }
    public function actualizaPaquetes(){
        $paquetes = $this->db->get("liquidacion")->result();
        foreach ($paquetes as $i => $row) {
            $detas = $this->db->where("deta_liqu_id",$row->liqu_id)->get("liquidacion_detalle")->result();
            foreach ($detas as $j => $deta) {
                $this->db->select("paqu_tipo, paqu_id");
                $this->db->from("paquete_detalle");
                $this->db->join("paquete","paqu_id = deta_paqu_id");
                $this->db->where("deta_id",$deta->deta_pdet_id);
                $consulta = $this->db->get()->row();
                if($consulta->paqu_tipo == "LOCAL"){
                    if($this->Model_general->guardar_registro("liquidacion_paqu",array("lpaq_liqu_id" => $row->liqu_id,"lpaq_paqu_id" => $consulta->paqu_id)))
                        echo "todo bien we</br>";
                    else
                        echo "mal :( </br>";
                }
            }
        }
    }
    */
	public function reporte_excelLiquidaciones(){
        $desde = $this->input->post("desde");
        $hasta = $this->input->post("hasta");
        $search = $this->input->post("search")["value"];

        $this->db->select("DATE_FORMAT(liqu_fechareg, '%d/%m/%Y %h:%i %p') fecha, CONCAT('LIQ - ',liqu_numero) serie, liqu_clie_rsocial cliente, liqu_total total, liqu_cobrado cobrado, IF(liqu_estado = 'PENDIENTE',(liqu_total - liqu_cobrado),IF((liqu_total - liqu_cobrado) < 0,(liqu_total - liqu_cobrado),'COBRADO')) saldo, liqu_obs obs, DATE_FORMAT(liqu_cobrofecha,'%d/%m/%Y') cobrofecha, liqu_cobrodesc cobrodesc, liqu_estado escobrado, liqu_moneda moneda");
        $this->db->from("liquidacion");
        if ($desde != "" && $hasta != ""){
            $this->db->where("liqu_fechareg >=",$desde." 00:00:00");
            $this->db->where("liqu_fechareg <=",$hasta." 59:59:00");
        }
        if ($search != "")
            $this->db->like("liqu_clie_rsocial",$cliente);
        $this->db->order_by("liqu_fechareg","ASC");
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
                ->setCellValue('D1', 'MONEDA')
                ->setCellValue('E1', 'TOTAL')
                ->setCellValue('F1', 'COBRADO')
                ->setCellValue('G1', 'SALDO')
                ->setCellValue('H1', 'OBSERVACIONES')
                ->setCellValue('I1', 'FECHA COBRO')
                ->setCellValue('J1', 'FECHA OBS');
        
        $objPHPExcel->getActiveSheet()->getStyle('F')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
        $objPHPExcel->getActiveSheet()->getStyle('D')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);      
        $objPHPExcel->getActiveSheet()->freezePane('A2');
        
        $ini = 3;
        $index = 0;
        
        foreach($detalle as $fila){
            $nro = $index+$ini;
            $index++;
            if($fila->escobrado == 'PENDIENTE'){
                $color = $rojo;
            }else{
                $color = $verde;
            }
            $objPHPExcel->getActiveSheet()
                        ->setCellValue("A$nro", $fila->fecha)
                        ->setCellValue("B$nro", $fila->serie)
                        ->setCellValue("C$nro", $fila->cliente)
                        ->setCellValue("D$nro", $fila->moneda)
                        ->setCellValue("E$nro", $fila->total)
                        ->setCellValue("F$nro", $fila->cobrado)
                        ->setCellValue("G$nro", $fila->saldo)
                        ->setCellValue("H$nro", $fila->obs)
                        ->setCellValue("I$nro", $fila->cobrofecha)
                        ->setCellValue("J$nro", $fila->cobrodesc);

            $objPHPExcel->getActiveSheet()->getStyle("G$nro")->applyFromArray($color);
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
        $objPHPExcel->getActiveSheet()->getStyle("E$ini:G$fin")->getNumberFormat()->setFormatCode('#,##0.00'); 
        $objPHPExcel->getActiveSheet()->getStyle("A$ini:A$fin")->getNumberFormat()->setFormatCode('dd/mm/yyyy');
        $objPHPExcel->getActiveSheet()->getStyle("I$ini:I$fin")->getNumberFormat()->setFormatCode('dd/mm/yyyy');
        
        
        $excel->excel_output($objPHPExcel, 'LIQUIDACIONES '.$desde." - ".$hasta);

    }
	public function actualizaPaquetesCobros(){
        $consulta = $this->db->query("SELECT liqu_id,liqu_numero, liqu_estado, paqu_escobrado, paqu_cobrado, paqu_total, paqu_id from paquete 
                            join liquidacion_paqu on lpaq_paqu_id = paqu_id
                            join liquidacion on liqu_id = lpaq_liqu_id
                            where liqu_estado = 'PENDIENTE' AND paqu_escobrado = 1")->result();
        $this->db->trans_start();
        foreach ($consulta as $i => $row) {
            $this->db->query("update paquete set paqu_escobrado = 0, paqu_cobrado = 0, paqu_cobrodesc = NUll, 
                                paqu_cobrofecha = NULL
                                where paqu_id = {$row->paqu_id}");
        }
        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE){
            echo "algo salio mal we";
        }else{
            echo "Pues todo bien we";
        }
    }
}
