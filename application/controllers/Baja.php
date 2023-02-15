<?php 
/**
* 
*/
class Baja extends CI_Controller
{
    var $configuracion;

	function __construct() {
        parent::__construct();
        
        if(!$this->session->userdata('authorized')){
            redirect(base_url()."login");
        }
        $this->load->database();
        $this->configuracion = $this->db->query("SELECT * FROM configuracion")->row();
        $this->load->model("Model_general");
        $consult = $this->db->from("venta")->where("vent_pagado = 'NO'")->get()->result();
        $this->pendientes = count($consult);
    }

    public function listado() {
        $this->load->helper('Funciones');
        $this->load->database();
        $this->load->library('Ssp');
        $this->load->library('Cssjs');

        $json = isset($_GET['json']) ? $_GET['json'] : false;

        $columns = array(
            array('db' => 'baja_id','dt' => 'ID',"field" => "baja_id"),
            array('db'=>"CONCAT('RA-',baja_ifecha,'-',baja_numero)",'dt'=>'BajaID','field'=>"CONCAT('RA-',baja_ifecha,'-',baja_numero)"),
            array('db' => 'vent_fecha','dt' => 'DocuFecha',"field" => "vent_fecha"),
			array('db' => "GROUP_CONCAT(vent_serie,'-',vent_numero)",'dt' => 'Número',"field" =>"GROUP_CONCAT(vent_serie,'-',vent_numero)"),
			array('db' => "GROUP_CONCAT(deta_vent_registro)",'dt' => 'SinRegistro',"field" =>"GROUP_CONCAT(deta_vent_registro)"),
            array('db' => 'baja_enviofecha','dt' => 'EnvioFecha', "field" => "baja_enviofecha"),

            array('db' => 'situ_nombre','dt' => 'Situación',       "field" => "situ_nombre"),
			array('db' => 'baja_fact_obse','dt' => 'Observación',       "field" => "baja_fact_obse"),
            array('db' => 'baja_id',             'dt' => 'DT_RowId',    "field" => "baja_id"),
            array('db' => 'baja_fact_situ',             'dt' => 'DT_Estado',    "field" => "baja_fact_situ"),
        );

        if ($json) {

            $json = isset($_GET['json']) ? $_GET['json'] : false;

            $table = 'baja';
            $primaryKey = 'baja_id';

            $sql_details = array(
                'user' => $this->db->username,
                'pass' => $this->db->password,
                'db' => $this->db->database,
                'host' => $this->db->hostname
            );

            $condiciones = array();
            $joinQuery = "FROM baja JOIN baja_detalle ON deta_baja_id=baja_id
            LEFT JOIN venta ON vent_id=deta_vent_id
			JOIN factura_situacion ON situ_id=baja_fact_situ";
            $where = "";
            if (!empty($_POST['desde'])&&!empty($_POST['hasta'])){
                $condiciones[] = "baja_enviofecha >='".$_POST['desde']."' AND baja_enviofecha <='".$_POST['hasta']."'";
            }
            if (!empty($_POST['comprobantes']))
                $condiciones[] = "baja_comp_id='".$_POST['comprobantes']."'";
            /*if (!empty($_POST['archivo']))
                $condiciones[] = "baja_genera_archivo='".$_POST['archivo']."'";*/
            if (!empty($_POST['estado']))
                $condiciones[] = "baja_fact_situ='".$_POST['estado']."'";

            $where = count($condiciones) > 0 ? implode(' AND ', $condiciones) : "";
            echo json_encode(
                    $this->ssp->simple($_POST, $sql_details, $table, $primaryKey, $columns, $joinQuery, $where,'baja_id')
            );
            exit(0);
        }
        $datos["comprobantes"] = $this->Model_general->getOptions('maestra_comprobantes', array("comp_id", "comp_nombre"),'* Comprobantes');
        $datos["archivo"] = array_merge(array('* Envios'),$this->Model_general->enum_valores('baja','baja_genera_archivo'));
        $datos["estado"] = $this->Model_general->getOptions('factura_situacion', array("situ_id", "situ_nombre"),'* SUNAT');
        $datos['columns'] = $columns;

        $this->cssjs->set_path_js(base_url() . "assets/js/Baja/");
        $this->cssjs->add_js('listado');
        $script['js'] = $this->cssjs->generate_js();
        $this->load->view('header', $script);
        $this->load->view('menu');
        $this->load->view('baja/listado', $datos);
        $this->load->view('footer');
    }
	
    public function nextnum($ifecha){
        $this->db->select('MAX(baja_numero) as max');
        $this->db->from('baja');
        $this->db->where("baja_ifecha='{$ifecha}'");
        $query = $this->db->get();
        $row = $query->row();
        $numero = $row->max+1;
        return $numero;
    }
	
	public function getnext($ifecha){
		echo json_encode(array('numero'=>$this->nextnum($ifecha)));
	}

    public function crear($idventa="") {
        $this->load->database();
        $this->load->helper('Funciones');
        $this->load->model("Model_general");
        $baja = array('baja_comp_id' => '',
                         'baja_enviofecha' => date('d/m/Y'),
                         'baja_descripcion' => '',
                         );

        $datos["baja"] = (object)$baja;
        $datos["id"] = '';


        if(!empty($idventa)){
            $venta = $this->db->query("SELECT * FROM venta WHERE vent_id='{$idventa}'")->row();

            $datas[] = array('id'=>$venta->vent_id,'text'=>$venta->vent_serie.'-'.$venta->vent_numero);
            $inits[] = $venta->vent_id;

            $datos['comps_data'] = json_encode($datas);
            $datos['comps_init'] = json_encode($inits);
        }else{
            $datos['comps_data'] = json_encode(array());
            $datos['comps_init'] = json_encode(array());
        }

        $datos['titulo'] = "Registrar";
        $this->load->library('Ssp');
        $this->load->library('Cssjs');
        $this->cssjs->set_path_js(base_url() . "assets/js/Baja/");
        $this->cssjs->add_js('form');
        $script['js'] = $this->cssjs->generate_js();
        $this->load->view('header', $script);
        $this->load->view('menu');
        $this->load->view('baja/formulario', $datos);
        $this->load->view('footer');
    }
    public function edit($id=0){
        $this->load->database();
        $this->load->helper('Funciones');
        $this->load->model("Model_general");
        $this->load->library('Ssp');
        $this->load->library('Cssjs');
        $this->cssjs->set_path_js(base_url() . "assets/js/Baja/");
        $this->cssjs->add_js('form');

        $baja = $this->db->query("SELECT * FROM baja WHERE baja_id='{$id}'")->row();
        $enviofecha = date_create($baja->baja_enviofecha);
        $docufecha = date_create($baja->baja_docufecha);
        $baja->baja_enviofecha = date_format($enviofecha, 'd/m/Y');
        $baja->baja_docufecha = date_format($docufecha, 'd/m/Y');

        $ventas = $this->db->query("SELECT * FROM baja_detalle LEFT JOIN venta ON vent_id=deta_vent_id WHERE deta_baja_id='{$id}'")->result();
        $datas = array();
        $inits = array();
        foreach($ventas as $venta){
            $datas[] = array('id'=>$venta->deta_vent_registro,'text'=>(empty($venta->deta_vent_id)?$venta->deta_vent_registro:$venta->vent_serie.'-'.$venta->vent_numero));
            $inits[] = $venta->deta_vent_registro;
        }
        $datos['comps_data'] = json_encode($datas);
        $datos['comps_init'] = json_encode($inits);

        $datos["baja"] = $baja;
        $datos["id"] = $id;

        
        $datos['titulo'] = "Editar";
        $script['js'] = $this->cssjs->generate_js();
        $this->load->view('header', $script);
        $this->load->view('menu');
        $this->load->view('baja/formulario', $datos);
        $this->load->view('footer');
    }

	public function guardar($id=''){
		
		$this->load->library('Form_validation');
        $this->load->helper('Funciones');

        $this->form_validation->set_rules('enviofecha', 'Fecha de envio', 'required');
		$this->form_validation->set_rules('descripcion', 'Descripción', 'required');
        
        if ($this->form_validation->run() == FALSE){		
            $this->Model_general->dieMsg(array('exito'=>false,'mensaje'=>validation_errors()));
        }

        $enviofecha = dateToMysql($this->input->post('enviofecha'));
        $ifecha = str_replace("-","",$enviofecha);
        $numero = $this->nextnum($ifecha);
        $numero = str_pad($numero,3, "0", STR_PAD_LEFT);
		$comprobantes = $this->input->post('comprobantes');
        
        $descripcion = $this->input->post('descripcion');
        $file = "{$this->configuracion->conf_ruc}-RA-{$ifecha}-{$numero}";

		$exist = $this->db->query("SELECT * FROM baja WHERE baja_ifecha='{$ifecha}' AND baja_numero='{$numero}'".(empty($id)?'':" AND baja_id!={$id}"))->row();


        if(isset($exist->baja_numero)&&!empty($exist->baja_numero)){
            $this->Model_general->dieMsg(array('exito'=>false,'mensaje'=>'Ya existe la numeración'));
		}

		$registro = array("baja_enviofecha" => $enviofecha,
                        "baja_ifecha" => $ifecha,
                        "baja_numero" => $numero,
                        "baja_file"=> $file,
                        "baja_descripcion" => $descripcion
                        );

		if(empty($id)){
            $registro = array_merge($registro,array("baja_fact_situ"=>'1'));
			if (($meta = $this->Model_general->guardar_registro("baja", $registro)) == FALSE){
                $this->Model_general->dieMsg(array('exito'=>false,'mensaje'=>'Error al guardar los datos'));
            }
            $id = $meta['id'];
            foreach($comprobantes as $comp){
				
				if(preg_match("/\-/",$comp)){
					$tc = explode("-",$comp);
					$comp = strtoupper($tc[0])."-".str_pad($tc[1],8, "0", STR_PAD_LEFT);
					$this->Model_general->guardar_registro("baja_detalle", array('deta_baja_id'=>$id,'deta_vent_id'=>null,'deta_vent_registro'=>$comp));
				}else{
					$this->Model_general->guardar_registro("baja_detalle", array('deta_baja_id'=>$id,'deta_vent_id'=>$comp,'deta_vent_registro'=>$comp));
				}
            }
            
        }else{
        	$condicion_registro= "baja_id = ".$id;
        	if (($meta = $this->Model_general->guardar_edit_registro("baja", $registro, $condicion_registro)) == FALSE){
                $this->Model_general->dieMsg(array('exito'=>false,'mensaje'=>'Error al guardar los datos'));
            }
            $sql = "DELETE FROM baja_detalle WHERE deta_baja_id='{$id}'";
            $this->db->query($sql);
            foreach($comprobantes as $comp){
				
				if(preg_match("/\-/",$comp)){
					$tc = explode("-",$comp);
					$comp = strtoupper($tc[0])."-".str_pad($tc[1],8, "0", STR_PAD_LEFT);
					$this->Model_general->guardar_registro("baja_detalle", array('deta_baja_id'=>$id,'deta_vent_id'=>null,'deta_vent_registro'=>$comp));
				}else{
					$this->Model_general->guardar_registro("baja_detalle", array('deta_baja_id'=>$id,'deta_vent_id'=>$comp,'deta_vent_registro'=>$comp));
				}
            }

        }


        $this->Model_general->dieMsg(array('exito'=>true,'mensaje'=>'','id'=>$id));
	}


    function enviarSunat($id){
        $this->paraSunat($id,'xml');
        $this->paraSunat($id,'sunat');
        echo json_encode(array('exito'=>true,'mensaje'=>''));
    }




    function paraSunat($id,$accion='xml'){
        $this->load->helper('Funciones');

        $baja =  $this->db->query("SELECT * FROM baja WHERE baja_id='{$id}'")->row();

        if(in_array($baja->baja_fact_situ, array(3,4,5))) return true;

        if($accion=='xml'&&in_array($baja->baja_fact_situ,array(1,6))){
            $this->crearArchivo($id);
            $this->firmarArchivo($id);
        }

        if($accion=='sunat'&&$baja->baja_fact_situ==2){
            $this->enviarServidor($id);
        }

        if($accion=='sunat'&&in_array($baja->baja_fact_situ,array(8,9))){
            $this->sacarEstado($id);
        }
      
        return true;
    }


    public function enviarServidor($id){
        $this->load->helper('firmar');
        $baja =  $this->db->query("SELECT * FROM baja WHERE baja_id='{$id}'")->row();
        $file = "files/FIRMA/{$baja->baja_file}.xml";
        if(!file_exists($file)) die(json_encode(array('exito'=>false,'mensaje'=>'No hay archivo')));
        $str_xml = file_get_contents($file);
        $bin_zip = generarZip(array("{$baja->baja_file}.xml"=>$str_xml));
        file_put_contents("files/ENVIO/{$baja->baja_file}.zip",$bin_zip);
        $params = array('fileName' => "{$baja->baja_file}.zip", 'contentFile' => $bin_zip);
		
		$tipo = $this->configuracion->conf_sunat_tipo;
        $servidor = $servidor = $this->configuracion->conf_sunat_serv_beta;
        if($tipo=='PRODUCCION')$servidor = $this->configuracion->conf_sunat_serv_produccion;
        if($tipo=='HOMOLOGACION')$servidor = $this->configuracion->conf_sunat_serv_homologacion;

        $result = sendSummary(
            $servidor,
            $this->configuracion->conf_ruc.$this->configuracion->conf_sunat_usuario,
            $this->configuracion->conf_sunat_password,
            $params);

        if($result->error==0){
            $codigo = 8;
            $this->Model_general->guardar_edit_registro("baja",array('baja_fact_situ'=>$codigo,'baja_ticket'=>$result->ticket,'baja_fact_envi'=>date('Y-m-d H:i:s')),"baja_id = '{$id}'");
        }else{
            echo json_encode(array('exito'=>false,'mensaje'=>$result->value));
            exit(0);
        }
    }

    public function sacarEstado($id){
        $this->load->helper('firmar');
        $baja =  $this->db->query("SELECT * FROM baja WHERE baja_id='{$id}'")->row();
        $file = "files/FIRMA/{$baja->baja_file}.xml";
        $params = array('ticket' => "{$baja->baja_ticket}");
		
		$tipo = $this->configuracion->conf_sunat_tipo;
        $servidor = $servidor = $this->configuracion->conf_sunat_serv_beta;
        if($tipo=='PRODUCCION')$servidor = $this->configuracion->conf_sunat_serv_produccion;
        if($tipo=='HOMOLOGACION')$servidor = $this->configuracion->conf_sunat_serv_homologacion;

        $estado = getStatus(
            $servidor,
            $this->configuracion->conf_ruc.$this->configuracion->conf_sunat_usuario,
            $this->configuracion->conf_sunat_password,
            $params);

        
        if($estado->error==0){
            
            if($estado->code=='0'||$estado->code=='99'){
                file_put_contents("files/RPTA/R{$baja->baja_file}.zip", $estado->result->status->content);
                $res = getResponse("files/RPTA/R{$baja->baja_file}.zip");
                $value = $res['cbc:ReferenceID'].' '.$res['cbc:ResponseCode'].' '.$res['cbc:Description'];
            }

            if($estado->code==0)$codigo = 3;
            if($estado->code==98)$codigo = 9;
            if($estado->code==99)$codigo = 10;

            if($codigo==3){
                $sql = "SELECT GROUP_CONCAT(deta_vent_id) as cventas FROM baja_detalle WHERE deta_baja_id='{$id}' AND deta_vent_id IS NOT NULL";
                $ventas = $this->db->query($sql)->row();
				if(!empty($ventas->cventas)){ 

					$sql = "UPDATE venta SET vent_fact_situ=11,vent_fact_obse='Por baja: {$baja->baja_ifecha}-{$baja->baja_numero}' WHERE vent_id IN({$ventas->cventas})";
					$this->db->query($sql);
					if($this->db->affected_rows()<=0) echo (json_encode(array('exito'=>true,'mensaje'=>'Guardado pero comprobante relaconado no encontrado o no registrado electronicamente.')));
					else echo (json_encode(array('exito'=>true,'mensaje'=>$this->db->affected_rows().' comprobante relacionado afectado.')));
				}
                
            }

            $this->Model_general->guardar_edit_registro("baja",array('baja_fact_situ'=>$codigo,'baja_fact_obse'=>$value),"baja_id = '{$id}'");
        }else{
            echo json_encode(array('exito'=>false,'mensaje'=>$estado->value));
            exit(0);
        }
    }

    public function firmarArchivo($id){
        $this->load->helper('firmar');
        $baja =  $this->db->query("SELECT * FROM baja WHERE baja_id='{$id}'")->row();
        $file = "files/TEMP/{$baja->baja_file}.xml";
        $file_pfx = "{$this->configuracion->conf_sunat_certificado}";
        $dom = formatoXML($file);
        $str_xml = firmarPFX($dom,$file_pfx,$this->configuracion->conf_sunat_certi_password);

        $data = file_get_contents($file);
        preg_match('/<ds:DigestValue>(.+?)<\/ds:DigestValue>/',$str_xml,$arr);
        $digestvalue = $arr[1];
        $this->Model_general->guardar_edit_registro("baja",array('baja_digestvalue'=>$digestvalue),"baja_id = '{$id}'");

        file_put_contents("files/FIRMA/{$baja->baja_file}.xml", $str_xml);
        $this->Model_general->guardar_edit_registro("baja",array('baja_fact_situ'=>2,'baja_fact_gene'=>date('Y-m-d H:i:s')),"baja_id = '$id'");
    }

    public function crearArchivo($id){
        $baja = $this->db->query("SELECT * FROM baja WHERE baja_id='{$id}'")->row();
        $file = "files/TEMP/{$baja->baja_file}.xml";

        $bajaJSON = array();
        $ventas = $this->db->query("SELECT * FROM baja_detalle LEFT JOIN venta ON vent_id=deta_vent_id WHERE deta_baja_id='{$id}'")->result();

        foreach($ventas as $i=>$venta){
			if(empty($venta->deta_vent_id)){
				$d = explode("-",$venta->deta_vent_registro);
				$serie = $d[0];
				$numero = $d[1];
				$tipo = preg_match("/F/",$serie)?'01':'03';
				$comprobantes[] = array(
					'linea'=>$i+1,
					'tipoDocumentoBaja'=>$tipo,
					'serieDocumentoBaja'=>$serie,
					'nroDocumentoBaja'=>$numero,
					'motivoBajaDocumento'=>$baja->baja_descripcion
				);
			}else{
				$comprobantes[] = array(
					'linea'=>$i+1,
					'tipoDocumentoBaja'=>$venta->vent_comp_id,
					'serieDocumentoBaja'=>$venta->vent_serie,
					'nroDocumentoBaja'=>$venta->vent_numero,
					'motivoBajaDocumento'=>$baja->baja_descripcion
				);
			}
            

            $baja->baja_docufecha = empty($venta->vent_fecha)?$baja->baja_enviofecha:$venta->vent_fecha;
        }
        
   
        $datos = array(
            'ublVersionIdSwf'=>"2.0",
            'CustomizationIdSwf'=>"1.0",
            'idComunicacion'=>"RA-{$baja->baja_ifecha}-{$baja->baja_numero}",
            'fechaDocumentoBaja'=> $baja->baja_docufecha,
            'fechaComunicacioBaja'=>$baja->baja_enviofecha,
            'nroRucEmisorSwf'=>$this->configuracion->conf_ruc,
            'identificadorFacturadorSwf'=>"GRUPOSISTEMAS",
            'codigoFacturadorSwf'=>"123456",
            'nombreComercialSwf'=>$this->configuracion->conf_ncomercial,
            'razonSocialSwf'=>$this->configuracion->conf_rsocial,
            'identificadorFirmaSwf'=>"SignTITICACA",
            'tipDocuEmisorSwf'=>'6',
            'listaResumen'=>$comprobantes
        );

        ob_start();
        $this->load->view('Venta/plantillas/ConvertirRBajasXML', $datos);
        $result = ob_get_contents();
        ob_end_clean();
        file_put_contents($file, $result);
        
    }
function getXML($id){
        $venta = $this->db->query("SELECT * FROM baja WHERE baja_id='{$id}'")->row();
        $file = "files/FIRMA/{$venta->baja_file}.xml";
        if(!file_exists($file)) die("No se ha encontrado el arhivo digital.");
        header('Content-Type: application/octet-stream');
        header("Content-Transfer-Encoding: Binary"); 
        header("Content-disposition: attachment; filename=\"" . basename($file) . "\""); 
        readfile($file); // do the double-download-dance (dirty but worky)
    }
    function getCDR($id){
        $venta = $this->db->query("SELECT * FROM baja WHERE baja_id='{$id}'")->row();
        $file = "files/RPTA/R{$venta->baja_file}.zip";

        if(!file_exists($file)){
          die("No se ha encontrado el arhivo digital.".$file);  
        } 
        header('Content-Type: application/octet-stream');
        header("Content-Transfer-Encoding: Binary"); 
        header("Content-disposition: attachment; filename=\"" . basename($file) . "\""); 
        readfile($file); // do the double-download-dance (dirty but worky)
    }


}
 ?>