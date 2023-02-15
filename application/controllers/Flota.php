<?php 
class Flota extends CI_Controller
{
    var $configuracion;
    var $titulos;
	function __construct() {
        parent::__construct();
        if(!$this->session->userdata('authorized')){
            redirect(base_url()."login");
        }
        $this->usua_id = $this->session->userdata('authorized');
        $this->configuracion = $this->db->query("SELECT * FROM usuario WHERE usua_id='{$this->usua_id}'")->row();
        $this->permisos  = $this->Model_general->getPermisos($this->usua_id);
    }

    public function flot_listado() {
        $this->load->helper('Funciones');
        $this->load->library('Ssp');
        $this->load->library('Cssjs');
        $json = isset($_GET['json']) ? $_GET['json'] : false;

        $fecha = 'DATE_FORMAT(sepr_fecha,"%d/%m/%Y")';
        $hora = 'IF(sepr_hora IS NULL,"--:-- --",DATE_FORMAT(sepr_hora,"%h:%i %p"))';
        $fecha_hora = 'CONCAT('.$fecha.'," ",'.$hora.')';
		
		$fecha_pago = 'DATE_FORMAT(sepr_pagofecha,"%d/%m/%Y")';
        $saldo = "IF(sepr_espagado = '0',CONCAT('<font class=red><b>',(sepr_combu_total - sepr_pagado)),CONCAT('<font class=green><b>',IF((sepr_combu_total - sepr_pagado) < 0,(sepr_combu_total - sepr_pagado),'COBRADO')))";
        
        $grupo = 'CONCAT(paqu_nombre,"</br><strong>",IF(paqu_tipo = "LOCAL","",deta_bus))';
        $file = 'IF(sepr_orde_id IS NOT NULL,CONCAT("ORD-",orde_numero),CONCAT(paqu_prefijo,"-",paqu_numero))';
        $servicio = 'IF(sepr_orde_id IS NOT NULL,orde_servicio,deta_servicio)';
        $moneda = 'IF(sepr_moneda = "SOLES","S/","$")';
        $proveedor = "CONCAT(emp_rsocial,' - ',prov_rsocial)";
        $columns = array(
            array('db' => "sepr_id",            'dt' => 'ID',       	"field" => "sepr_id"),
            array('db' => $file,                'dt' => 'FILE',     	"field" => $file),
            array('db' => $fecha_hora,          'dt' => 'FECHA',    	"field" => $fecha_hora),
            array('db' => "prov_rsocial",       'dt' => 'PROVEEDOR', 	"field" => "prov_rsocial"),
            array('db' => $servicio,            'dt' => 'SERVICIO', 	"field" => $servicio),
			array('db' => "clie_rcomercial",    'dt' => 'CONTACTO', 	"field" => "clie_rcomercial"),
			array('db' => "sepr_servicio",      'dt' => 'OBSERVACION', 	"field" => "sepr_servicio"),
            array('db' => "sepr_responsable",   'dt' => 'CAPITAN / RESPONSABLE',"field" => "sepr_responsable"),
            array('db' => "sepr_combu_galones", 'dt' => 'GALONES',  	"field" => "sepr_combu_galones"),
            array('db' => "sepr_combu_precio",  'dt' => 'PRECIO',   	"field" => "sepr_combu_precio"),
            array('db' => "sepr_combu_total",   'dt' => 'TOTAL',    	"field" => "sepr_combu_total"),
            array('db' => $saldo,               'dt' => 'SALDO',    	"field" => $saldo),
            array('db' => $fecha_pago,          'dt' => 'FECHA P', 		"field" => $fecha_pago),
            array('db' => "sepr_pagodesc",      'dt' => 'PAGO DESC',	"field" => "sepr_pagodesc"),
            array('db' => 'sepr_id',            'dt' => 'DT_RowId', 	"field" => "sepr_id"),
			array('db' => 'sepr_esorden',       'dt' => 'DT_RowOrden', 	"field" => "sepr_esorden"),
            array('db' => 'sepr_espagado',      'dt' => 'DT_Estado',	"field" => "sepr_espagado")
        );

        if ($json) {

            $json = isset($_GET['json']) ? $_GET['json'] : false;

            $table = 'servicio_proveedor';
            $primaryKey = 'sepr_id';

            $sql_details = array(
                'user' => $this->db->username,
                'pass' => $this->db->password,
                'db' => $this->db->database,
                'host' => $this->db->hostname
            );

            $condiciones = array();

            $joinQuery = "FROM servicio_proveedor 
							INNER JOIN proveedor ON sepr_prov_id = prov_id AND prov_combustible = 'SI' 
							INNER JOIN proveedor_empresa ON emp_id = prov_emp_id 
							LEFT JOIN proveedor_tipo ON sepr_tipo = tipo_id 
							LEFT JOIN ordenserv ON orde_id = sepr_orde_id 
							LEFT JOIN paquete_detalle ON deta_id = sepr_pdet_id 
							LEFT JOIN paquete ON paqu_id = deta_paqu_id
							LEFT JOIN cliente ON clie_id = paqu_clie_id";
            $where = "";
            if (!empty($_POST['desde'])&&!empty($_POST['hasta'])){
                $condiciones[] = "sepr_fecha >='".$_POST['desde']."' AND sepr_fecha <='".$_POST['hasta']."'";
            }
            if (!empty($_POST['serv_ids'])){
                $array= implode(',', array_map('intval', explode(',', $_POST['serv_ids'])));
				$condiciones[] = "IF(sepr_orde_id IS NOT NULL,orde_serv_id IN (".$array."),deta_serv_id IN (".$array."))";
            }
			
			if (!empty($_POST['contacto']))
                $condiciones[] = "sepr_prov_id='".$_POST['contacto']."'";
			if (!empty($_POST['tipo']))
                $condiciones[] = "sepr_tipo='".$_POST['tipo']."'";
			/*
            if (!empty($_POST['moneda']))
                $condiciones[] = "paqu_moneda='".$_POST['moneda']."'";
            if (!empty($_POST['estado']))
			*/            
            $where = count($condiciones) > 0 ? implode(' AND ', $condiciones) : "";
            echo json_encode(
                    $this->ssp->simple($_POST, $sql_details, $table, $primaryKey, $columns, $joinQuery, $where)
            );
            exit(0);
        }
		
        $datos["usuario"] = $this->Model_general->getOptions('usuario', array("usua_id", "usua_nombres"),'* Usuario');
        $datos["contacto"] = $this->getContacto();
        $datos["servicio"] = $this->getServicio();
		//$datos["proveedor"] = $this->Model_general->getOptions('proveedor_empresa', array("emp_id", "emp_rsocial"),'* Proveedor');
        $datos["moneda"] = array_merge(array(''=>'* Monedas'),$this->Model_general->enum_valores('paquete','paqu_moneda'));
        $datos["tipo"] = $this->Model_general->getOptions('proveedor_tipo', array("tipo_id", "tipo_denom"),'* Tipo');
        $datos["estado"] = array_merge(array(''=>'* Estado'),$this->Model_general->enum_valores('paquete','paqu_estado'));
        $datos['columns'] = $columns;
        $datos['titulo'] = "Transportes";
		
        $this->cssjs->add_js(base_url().'assets/js/Flota/listado.js?v=2.1',false,false);
        $this->load->view('header');
        $this->load->view('menu');
        $this->load->view($this->router->fetch_class().'/'.$this->router->fetch_method(), $datos);
        $this->load->view('footer');
    }   
	public function getContacto(){
		$this->db->select("prov_id, prov_rsocial");
		$this->db->from("proveedor");
		$this->db->join("proveedor_empresa", "prov_emp_id = emp_id");
		$this->db->where("emp_tipo", "PROPIO");
		$contactos = $this->db->get()->result();
		
		$contacto = array();
		$contacto[""] = "* Contactos";
		foreach($contactos as $cont){
			$contacto[$cont->prov_id] = $cont->prov_rsocial;
		}
		return $contacto;
	}
	public function getServicio(){
		$consulta = $this->db->query("SELECT serv_id, serv_descripcion FROM servicio
							WHERE serv_id IN (SELECT IF(sepr_orde_id IS NOT NULL,orde_serv_id,deta_serv_id) from servicio_proveedor
												LEFT JOIN ordenserv ON orde_id = sepr_orde_id LEFT JOIN paquete_detalle ON deta_id = sepr_pdet_id)
							ORDER BY serv_id ASC")->result();
		$servicio = array();			
		$servicio[""] = "* Servicios";
		foreach($consulta as $row){
				$servicio[$row->serv_id] = $row->serv_descripcion;
		}
		
		return $servicio;
	}
    public function editar($id=''){
        $this->load->helper('Funciones');
        $this->db->where("sepr_id", $id);
        $orde = $this->db->get("servicio_proveedor")->row();
        if($orde->sepr_combu_precio == ""){
            //$ult_costo = $this->db->query("SELECT sepr_combu_precio FROM servicio_proveedor WHERE sepr_combu_precio IS NOT NULL ORDER BY sepr_id ASC LIMIT 1");
			$ult_costo = $this->db->query("SELECT sepr_combu_precio FROM servicio_proveedor WHERE sepr_combu_precio > 0 ORDER BY sepr_id ASC LIMIT 1");
            if($ult_costo->num_rows() > 0)
                $ult_costo = $ult_costo->row()->sepr_combu_precio;
            else
                $ult_costo = "0.00";
            $orde->sepr_combu_precio = $ult_costo;
        }
        $datos['orde'] = $orde;
        $this->load->view('Flota/formulario', $datos);
    }
    public function validar_cambios(){
        $this->load->library('Form_validation');
        $this->form_validation->set_rules('precio', 'Precio', 'required');
        $this->form_validation->set_rules('responsable', 'Responsable', 'required');
        $this->form_validation->set_rules('galones', 'Galones', 'required');
        $this->form_validation->set_rules('total', 'Total', 'required');
        if ($this->form_validation->run() == FALSE){        
            $this->Model_general->dieMsg(array('exito'=>false,'mensaje'=>validation_errors()));
        }
    }
    public function guardar($id=''){
        $this->validar_cambios();
        if($id!=''){
            $responsable = $this->input->post("responsable");
            $precio = $this->input->post("precio");
            $galones = $this->input->post("galones");
            $total = $this->input->post("total");
			$obs = $this->input->post("obs");
            $datas = array("sepr_combu_galones" => $galones,
                            "sepr_combu_precio" => $precio,
                            "sepr_combu_total" => $total,
							"sepr_servicio" => $obs,
                            "sepr_responsable" => $responsable);
            $where = array("sepr_id" => $id);
            if($this->Model_general->guardar_edit_registro("servicio_proveedor", $datas, $where)){
                $resp["exito"] = true;
                $resp["mensaje"] = "Datos guardados cone exito";    
            }else{
                $resp["exito"] = false;
                $resp["mensaje"] = "Algo salio mal";    
            }
        }else{
            $resp["exito"] = false;
            $resp["mensaje"] = "Algo salio mal";
        }
        echo json_encode($resp);
    }
	public function pagar($id=''){
        $this->load->helper('Funciones');
        $this->db->where("sepr_id", $id);
        $orde = $this->db->get("servicio_proveedor")->row();
        $datos['orde'] = $orde;

        $arr_doc = array(0,1,2,3,9,11);
        $this->db->where_in("tcom_id", $arr_doc);
        $doc = $this->db->get("comprobante_tipo")->result();

        $documentos = array();
        foreach ($doc as $i => $d) {
            $documentos[$d->tcom_id] = $d->tcom_nombre;
        }
        $datos["moneda"] = $this->Model_general->enum_valores('servicio_proveedor','sepr_moneda');
        $datos['documentos'] = $documentos;
        $datos["cuentas"] = $this->Model_general->getOptions("cuenta",array("cuen_id","cuen_banco"),'* Cuenta');
        $this->load->view('Flota/form_pagar', $datos);
    }
    public function guardar_pago($id=''){
        $this->load->helper('Funciones');

        $this->db->from("servicio_proveedor");
        $this->db->join("proveedor", "prov_id = sepr_prov_id","LEFT");
        $this->db->where("sepr_id", $id);
        $orde = $this->db->get()->row();
        if($orde->sepr_combu_total == ''){
            $json['exito'] = false;  
            $json['mensaje'] = "No tiene un total asignado, primero edite este servicio";
            echo json_encode($json);
            exit(0);
        }
        if($orde->sepr_espagado == 1){
            $json["exito"] = false;
            $json["mensaje"] = "Ya esta pagado";
        }else{
            $documento = $this->input->post("documento");
            $serie = $this->input->post("serie");
            $numero = $this->input->post("numero");
            $cuenta = $this->input->post("cuenta");
            $codigo_cuen = $this->input->post("codigo_cuen");
            $moneda = $this->input->post("moneda");
            $total = $this->input->post("total");
            $cancelado = $this->input->post("cancelado");
            $pagado = $this->input->post("pagado");
            $saldo = $this->input->post("saldo");
            $obs = $this->input->post("observacion");
            $fecha = $this->Model_general->fecha_to_mysql($this->input->post('fecha'));

            if($cuenta == '' || $codigo_cuen == '' || $pagado == ''){
                $json['exito'] = false;  
                $json['mensaje'] = "Cuenta, Código y Pagado son obligatorios";
                echo json_encode($json);
                exit(0);
            }

            $this->db->trans_start();

            $this->Model_general->actualizarCaja(7, "SALIDA", $documento, $serie, $numero, "Pago de combustible: ".$orde->prov_rsocial, $pagado, $moneda, $this->usua_id, $id, '', $cuenta,$codigo_cuen,$fecha,$obs);

            //$desc = $obs.", Código de caja: ".$codigo_cuen." / ".$orde->sepr_pagodesc;
			$desc = ($orde->sepr_pagodesc != "")?$orde->sepr_pagodesc." / ".$obs:$obs;
            $prev = array("sepr_pagofecha" => $fecha, "sepr_pagodesc" => $desc);
            if(($cancelado + $pagado) >= $total){
                $dte = array("sepr_espagado" => '1', "sepr_pagado" => ($cancelado + $pagado));
            }else{
                $dte = array("sepr_pagado" => ($cancelado + $pagado));
            }
            $dte = array_merge($dte,$prev);
            $this->Model_general->guardar_edit_registro("servicio_proveedor", $dte, array('sepr_id' => $id));
            
            $this->Model_general->add_log("PAGO",16,"Pago de combustible: ".$orde->prov_rsocial." ".$pagado." ".$moneda.", Código de caja: ".$codigo_cuen);
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
	public function reporte_excelCombustibles(){
        $desde = $this->input->post("desde");
        $hasta = $this->input->post("hasta");
		/*
        $detalle = $this->db->query("SELECT DATE_FORMAT(sepr_fecha,'%d/%m/%Y') fecha, 
							IF(sepr_orde_id IS NOT NULL,CONCAT('ORD - ',orde_numero),CONCAT(paqu_prefijo,' - ',paqu_numero)) file, 
							prov_rsocial contacto, IF(sepr_orde_id IS NOT NULL,orde_servicio,deta_servicio) servicio, sepr_responsable responsable, sepr_servicio observacion, 
							sepr_combu_galones galones, sepr_combu_precio precio, sepr_combu_total total,
							IF(sepr_espagado = '0',(sepr_combu_total - sepr_pagado),IF((sepr_combu_total - sepr_pagado) < 0,(sepr_combu_total - sepr_pagado),'COBRADO')) saldo,
							DATE_FORMAT(sepr_pagofecha,'%d/%m/%Y') fecha, sepr_pagodesc pdesc 
							FROM servicio_proveedor 
							INNER JOIN proveedor ON sepr_prov_id = prov_id AND prov_combustible = 'SI' 
							INNER JOIN proveedor_empresa ON emp_id = prov_emp_id 
							LEFT JOIN proveedor_tipo ON sepr_tipo = tipo_id 
							LEFT JOIN ordenserv ON orde_id = sepr_orde_id 
							LEFT JOIN paquete_detalle ON deta_id = sepr_pdet_id 
							LEFT JOIN paquete ON paqu_id = deta_paqu_id
							WHERE sepr_fecha >= '{$desde}' AND sepr_fecha <= '{$hasta}'".
							($serv_ids != ''?" AND IF(sepr_orde_id IS NOT NULL,orde_serv_id IN (".$serv_ids."),deta_serv_id IN (".$serv_ids."))":"").
							($contacto != ''?" AND sepr_prov_id = '{$contacto}'":"").
							($search != ''?" AND prov_rsocial LIKE %'{$contacto}'%":""))->result();
		echo $this->db->last_query();
		*/
		$where = "";
		if (!empty($_POST['desde'])&&!empty($_POST['hasta'])){
			$condiciones[] = "sepr_fecha >='".$_POST['desde']."' AND sepr_fecha <='".$_POST['hasta']."'";
		}
		if (!empty($_POST['serv_ids'])){
			$array= implode(',', array_map('intval', explode(',', $_POST['serv_ids'])));
			$condiciones[] = "IF(sepr_orde_id IS NOT NULL,orde_serv_id IN (".$array."),deta_serv_id IN (".$array."))";
		}
		
		if (!empty($_POST['contacto']))
			$condiciones[] = "sepr_prov_id='".$_POST['contacto']."'";
		if (!empty($_POST['tipo']))
			$condiciones[] = "sepr_tipo='".$_POST['tipo']."'";
		$where = count($condiciones) > 0 ? implode(' AND ', $condiciones) : "";
		
		$file = "IF(sepr_orde_id IS NOT NULL,CONCAT('ORD-',orde_numero),CONCAT(paqu_prefijo,'-',paqu_numero))";
        $fecha_hora = "CONCAT(DATE_FORMAT(sepr_fecha,'%d/%m/%Y'),' ',IF(sepr_hora IS NULL,'--:-- --',DATE_FORMAT(sepr_hora,'%h:%i %p')))";
		$saldo = "IF(sepr_espagado = '0', (sepr_combu_total - sepr_pagado), IF((sepr_combu_total - sepr_pagado) < 0, (sepr_combu_total - sepr_pagado),'COBRADO'))";
		$fecha_pago = "DATE_FORMAT(sepr_pagofecha,'%d/%m/%Y')";
		
		$detalle = $this->db->query("SELECT $file as file, $fecha_hora as fechahora, prov_rsocial, IF(sepr_orde_id IS NOT NULL,orde_servicio,deta_servicio) servicio,
											clie_rcomercial contacto, sepr_servicio observacion, sepr_responsable capitan, sepr_combu_galones galones,
											sepr_combu_precio precio, sepr_combu_total total, $saldo as saldo, $fecha_pago as fecha_pago, sepr_pagodesc pago_desc
							FROM servicio_proveedor 
							INNER JOIN proveedor ON sepr_prov_id = prov_id AND prov_combustible = 'SI' 
							INNER JOIN proveedor_empresa ON emp_id = prov_emp_id 
							LEFT JOIN proveedor_tipo ON sepr_tipo = tipo_id 
							LEFT JOIN ordenserv ON orde_id = sepr_orde_id 
							LEFT JOIN paquete_detalle ON deta_id = sepr_pdet_id 
							LEFT JOIN paquete ON paqu_id = deta_paqu_id
							LEFT JOIN cliente ON clie_id = paqu_clie_id
							WHERE $where")->result();
        
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
        /*
        $objPHPExcel->getActiveSheet()->getStyle('D')->applyFromArray($fillgray);
        $objPHPExcel->getActiveSheet()->getStyle('H')->applyFromArray($fillgray);
		*/
        $objPHPExcel->getActiveSheet()
                ->setCellValue('A1', 'FILE')
                ->setCellValue('B1', 'FECHA')
				->setCellValue('C1', 'PROVEEDOR')
                ->setCellValue('D1', 'SERVICIO')
                ->setCellValue('E1', 'CONTACTO')
                ->setCellValue('F1', 'OBSERVACION')
                ->setCellValue('G1', 'CAPITAN / RESPONSABLE')
                ->setCellValue('H1', 'GALONES')
                ->setCellValue('I1', 'PRECIO')
				->setCellValue('J1', 'TOTAL')
				->setCellValue('K1', 'SALDO')
				->setCellValue('L1', 'FECHA PAGO')
				->setCellValue('M1', 'PAGO DESCRIPCION');
        /*
        $objPHPExcel->getActiveSheet()->getStyle('B:D')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
		$objPHPExcel->getActiveSheet()->getStyle('G:H')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
		*/
        $objPHPExcel->getActiveSheet()->freezePane('A2');
        
        $ini = 3;
        $index = 0;
        foreach($detalle as $fila){
            $nro = $index+$ini;
            $index++;
            $objPHPExcel->getActiveSheet()
                        ->setCellValue("A$nro", $fila->file)
                        ->setCellValue("B$nro", $fila->fechahora)
						->setCellValue("C$nro", $fila->prov_rsocial)
						->setCellValue("D$nro", $fila->servicio)
                        ->setCellValue("E$nro", $fila->contacto)
                        ->setCellValue("F$nro", $fila->observacion)
                        ->setCellValue("G$nro", $fila->capitan)
                        ->setCellValue("H$nro", $fila->galones)
                        ->setCellValue("I$nro", $fila->precio)
						->setCellValue("J$nro", $fila->total)
						->setCellValue("K$nro", $fila->saldo)
						->setCellValue("L$nro", $fila->fecha_pago)
						->setCellValue("M$nro", $fila->pago_desc);
            }

            foreach(range('A','M') as $nro)
                $objPHPExcel->getActiveSheet()->getColumnDimension($nro)->setAutoSize(true);
        
        $fin = $index+$ini-1;
        $objPHPExcel->getActiveSheet()->getStyle("I$ini:K$fin")->getNumberFormat()->setFormatCode('#,##0.00'); 
        $objPHPExcel->getActiveSheet()->getStyle("B$ini:B$fin")->getNumberFormat()->setFormatCode('dd/mm/yyyy');
		$objPHPExcel->getActiveSheet()->getStyle("L$ini:L$fin")->getNumberFormat()->setFormatCode('dd/mm/yyyy');
        
        
        $excel->excel_output($objPHPExcel, 'Reporte unidades '.$desde." - ".$hasta);
	}
	public function valida_preOrdenPago(){
        $seleccionados = implode(",", $this->input->post("sel"));
        $resp = "";
        $proveedores = $this->db->query("SELECT sepr_prov_id from servicio_proveedor where sepr_id IN ({$seleccionados}) group by sepr_prov_id")->result();
        $monedas = $this->db->query("SELECT sepr_moneda from servicio_proveedor where sepr_id IN ({$seleccionados}) group by sepr_moneda")->result();
        if(COUNT($proveedores) > 1)
            $resp = "No es posible generar una orden para diferentes proveedores";
        if(COUNT($monedas) > 1)
            $resp = "No es posible generar una orden con diferentes monedas";
        echo $resp;
    }
    public function nextnumOrdPago(){
        $this->db->select('MAX(orde_numero) as max');
        $this->db->from('ordenpago');
        $query = $this->db->get();    
        $row = $query->row();
        $numero = $row->max+1;
        return $numero;
    }
    public function flot_genOrdenPago(){
        $this->load->helper('Funciones');
        $this->load->library('Ssp');
        $this->load->library('Cssjs');
        $json = isset($_GET['json']) ? $_GET['json'] : false;

        //$servicios = $this->db->query("SELECT * FROM servicio_proveedor JOIN proveedor ON prov_id = sepr_prov_id WHERE sepr_id IN (".$seleccionados.")")->result();

        $seleccionados = $this->input->get('sel');
        $servicios = $this->Model_general->get_detasOrdFlot($seleccionados);
        $datos["titulo"] = "Generar Orden de Pago";
        $datos["servicios"] = $servicios;
        $datos["moneda"] = $servicios[0]->moneda;
        $datos["numero"] = $this->nextnumOrdPago();
        $datos["fecha"] = date("Y-m-d");

        $this->load->view('header');
        $this->load->view('menu');
        $this->load->view('Flota/'.$this->router->fetch_method(), $datos);
        $this->load->view('footer');
    }

    public function guardar_ordenPago($id_ord=''){
        if($id_ord != ''){

        }else{
            $prov_id = $this->input->post("prov_id");
            $prov_name = $this->input->post("prov_name");
            $numero = $this->input->post("numero");
            $fecha = $this->input->post("fecha");
            $moneda = $this->input->post("moneda");
            $total = $this->input->post("total");
            $deta_ids = $this->input->post("deta_id");
            
            $ordPago = array("orde_numero" => $numero,
                                "orde_fechareg" => $fecha,
                                "orde_prov_id" => $prov_id,
                                "orde_prov_name" => $prov_name,
                                "orde_total" => $total,
                                "orde_moneda" => $moneda,
                                "orde_usua" => $this->usua_id,
                                "orde_flota" => "1"
            );
            $this->db->trans_begin();
            if (($meta = $this->Model_general->guardar_registro("ordenpago", $ordPago)) == TRUE){
                $detas = $this->Model_general->get_detasOrdFlot($deta_ids,true);

                foreach ($detas as $i => $val) {
                    $item = array("deta_orde_id" => $meta["id"],
                                    "deta_sepr_id" => $val->id,
                                    "deta_prov_id" => $val->prov_id,
                                    "deta_fecha" => $this->Model_general->fecha_to_mysql($val->fecha),
                                    "deta_hora" => $this->Model_general->time_to_mysql($val->hora),
                                    "deta_tipo" => $val->tipo_id,
                                    "deta_tipo_name" => $val->tipo,
                                    "deta_referencia" => $val->referencia,
                                    "deta_servicio" => $val->servicio,
                                    "deta_precio" => $val->precio,
                                    "deta_cantidad" => $val->cantidad,
                                    "deta_total" => $val->total,
                                    "deta_moneda" => $val->moneda,
                                    "deta_guia" => $val->responsable
                    );
                    if (!$this->Model_general->guardar_registro("ordenpago_detalle", $item)){
                        $resp["exito"] = false;
                        $resp["mensaje"] = "Algo salio mal, intentelo más tarde";
                        $this->Model_general->dieMsg($resp);
                    }else{
                        if(!$this->actualizaEstadosepr($val->id,"1")){
                            $resp["exito"] = false;
                            $resp["mensaje"] = "Algo salio mal, intentelo más tarde";
                            $this->Model_general->dieMsg($resp);
                        }
                    }
                }
                $this->Model_general->add_log("CREAR",4,"Creación de Orden de pago OP-".str_pad($numero,8,"0", STR_PAD_LEFT));
            }else{
                $this->db->trans_rollback();
                $resp["exito"] = false;
                $resp["mensaje"] = "Algo salio mal, intentelo más tarde";
                $this->Model_general->dieMsg($resp);
            }
            $this->db->trans_commit();
            $resp["exito"] = true;
            $resp["mensaje"] = "Guardado con exito";
            $this->Model_general->dieMsg($resp);
        }
    }
    public function actualizaEstadosepr($id='',$estado){
        $resp = true;
        $datas = array("sepr_esorden" => $estado);
        $condicion = array("sepr_id" => $id);
        if(!$this->Model_general->guardar_edit_registro("servicio_proveedor", $datas, $condicion))
            $resp = false;
        return $resp;
    }
    public function elim_ordenPago($id=''){
        $detas = $this->db->where("deta_orde_id", $id)->get("ordenpago_detalle")->result();
        $orde = $this->db->where("orde_id", $id)->get("ordenpago")->row();
        if($orde->orde_espagado == 1 || $orde->orde_pagado > 0){
            $json["exito"] = false;
            $json["mensaje"] = "No es posible eliminar, la orden esta pagada o tiene pagos registrados";
        }else{
            $this->db->trans_start();
            foreach ($detas as $i => $det) {
                $this->actualizaEstadosepr($det->deta_sepr_id,"0");
            }
            $this->Model_general->add_log("ELIMINAR",4,"Eliminación de Orden de pago OP-".$orde->orde_numero);
            $this->Model_general->borrar(array("orde_id" => $id), "ordenpago");
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
}