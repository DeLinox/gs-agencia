<?php 
/**
* 
*/
class Resumen extends CI_Controller
{
    var $configuracion;

	function __construct() {
        parent::__construct();
        if(!$this->session->userdata('authorized')){
            redirect(base_url()."login");
        }
        //$user = isset($_SESSION['user'])?$_SESSION['user']:'';
        //if($user != 'sopre') die("acceso no permitido, primiero inicie el Sopre, luego vuelva a intentar");
        $this->load->database();
        $this->configuracion = $this->db->query("SELECT * FROM configuracion")->row();
        $this->permisos  = $this->Model_general->getPermisos($this->usua_id);
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
            array('db' => 'resu_id',            'dt' => 'ID',       "field" => "resu_id"),
            array('db'=>"COUNT(*)",             'dt'=>'Docus',      'field'=>"COUNT(*)"),
            array('db'=>"resu_file",            'dt'=>'File',       'field'=>"resu_file"),
            array('db'=>"CONCAT('RC-',resu_ifecha,'-',resu_numero)",'dt'=>'BajaID','field'=>"CONCAT('RC-',resu_ifecha,'-',resu_numero)"),
            array('db' => 'vent_fecha', 'dt' => 'DocuFecha',        "field" => "vent_fecha"),
            array('db' => "GROUP_CONCAT(vent_serie,'-',vent_numero)",'dt' => 'Número',"field" =>"GROUP_CONCAT(vent_serie,'-',vent_numero)"),
            array('db' => 'resu_enviofecha',    'dt' => 'EnvioFecha',"field" => "resu_enviofecha"),
            array('db' => 'situ_nombre',        'dt' => 'Situación', "field" => "situ_nombre"),
            array('db' => 'resu_id',            'dt' => 'DT_RowId',  "field" => "resu_id"),
            array('db' => 'resu_fact_situ',     'dt' => 'DT_Estado', "field" => "resu_fact_situ"),
        );

        if ($json) {

            $json = isset($_GET['json']) ? $_GET['json'] : false;

            $table = 'resumen';
            $primaryKey = 'resu_id';

            $sql_details = array(
                'user' => $this->db->username,
                'pass' => $this->db->password,
                'db' => $this->db->database,
                'host' => $this->db->hostname
            );

            $condiciones = array();
            $joinQuery = "FROM resumen JOIN resumen_detalle ON deta_resu_id=resu_id
            JOIN venta ON vent_id=deta_vent_id
            JOIN factura_situacion ON situ_id=resu_fact_situ";
            $where = "";
            if (!empty($_POST['desde'])&&!empty($_POST['hasta'])){
                $condiciones[] = "resu_enviofecha >='".$_POST['desde']."' AND resu_enviofecha <='".$_POST['hasta']."'";
            }
            if (!empty($_POST['estado']))
                $condiciones[] = "resu_fact_situ='".$_POST['estado']."'";

            $where = count($condiciones) > 0 ? implode(' AND ', $condiciones) : "";
            echo json_encode(
                    $this->ssp->simple($_POST, $sql_details, $table, $primaryKey, $columns, $joinQuery, $where,'resu_id')
            );
            exit(0);
        }
        $datos["comprobantes"] = $this->Model_general->getOptions('maestra_comprobantes', array("comp_id", "comp_nombre"),'* Comprobantes');
        $datos["archivo"] = array_merge(array('* Envios'),$this->Model_general->enum_valores('resumen','resu_genera_archivo'));
        $datos["estado"] = $this->Model_general->getOptions('factura_situacion', array("situ_id", "situ_nombre"),'* SUNAT');
        $datos['columns'] = $columns;

        $this->cssjs->set_path_js(base_url() . "assets/js/Resumen/");
        $this->cssjs->add_js('listado');
        $script['js'] = $this->cssjs->generate_js();
        $this->load->view('header', $script);
        $this->load->view('menu');
        $this->load->view('Resumen/Listado', $datos);
        $this->load->view('footer');
    }
	
    public function nextnum($ifecha){
        $this->db->select('MAX(resu_numero) as max');
        $this->db->from('resumen');
        $this->db->where("resu_ifecha='{$ifecha}'");
        $query = $this->db->get();
        $row = $query->row();
        $numero = $row->max+1;
        return $numero;
    }
	
	public function getnext($ifecha){
		echo json_encode(array('numero'=>$this->nextnum($ifecha)));
	}

    public function crear() {
        $this->load->database();
        $this->load->helper('Funciones');
        $this->load->model("Model_general");
        $resumen = array('resu_comp_id' => '',

                         'resu_enviofecha' => date('d/m/Y'),
                         );

        $datos["resumen"] = (object)$resumen;
        $datos["id"] = '';

        $datos['comps_data'] = json_encode(array());
        $datos['comps_init'] = json_encode(array());

        $datos['titulo'] = "Registrar";
        $this->load->library('Ssp');
        $this->load->library('Cssjs');
        $this->cssjs->set_path_js(base_url() . "assets/js/Resumen/");
        $this->cssjs->add_js('form');
        $script['js'] = $this->cssjs->generate_js();
        $this->load->view('header', $script);
        $this->load->view('menu');
        $this->load->view('Resumen/Formulario', $datos);
        $this->load->view('footer');
    }

    public function actualizar() {
        $this->load->database();
        $this->load->helper('Funciones');
        $this->load->model("Model_general");


        $sql = "SELECT * FROM venta WHERE vent_resu_generado=0 AND (vent_fecha BETWEEN DATE_ADD(CURDATE(), INTERVAL -30 DAY) AND DATE_ADD(CURDATE(), INTERVAL -1 DAY)) AND (vent_comp_id=3 OR vent_nc_comp_id=3) ORDER BY vent_fecha ASC,vent_serie ASC,vent_numero ASC";
        //$sql = "SELECT * FROM venta WHERE vent_resu_generado=0 AND vent_fecha='2018-01-01' AND (vent_comp_id=3 OR vent_nc_comp_id=3) ORDER BY vent_fecha ASC,vent_serie ASC,vent_numero ASC";
        $rows = $this->db->query($sql)->result();
		foreach($rows as $row){
            if($row->vent_fact_situ<2){
                die("Error al crear archivo, tiene que generar primero los XMLs de las boletas de venta, <a href='".base_url()."Resumen/listado"."'>Regresar</a>");
            }
        }
        $enviofecha = date("Y-m-d");
        $ifecha = date("Ymd");
        
        $oldfecha = "";
        $oldid = "";
        $contador = 0;
        foreach($rows as $row){
            if($oldfecha!=$row->vent_fecha||$contador>=500){
                $contador=0;
                $oldfecha = $row->vent_fecha;
                
                $numero = $this->nextnum($ifecha);
                $numero = str_pad($numero,3, "0", STR_PAD_LEFT);
                $file = "{$this->configuracion->conf_ruc}-RC-{$ifecha}-{$numero}";
                $registro = array("resu_enviofecha" => $enviofecha,
                                "resu_ifecha" => $ifecha,
                                "resu_docufecha" => $oldfecha,
                                "resu_numero" => $numero,
                                "resu_file"=> $file,
                                "resu_fact_situ"=>'1'
                                );
                
                //die("UPDATE venta SET vent_resu_generado=1 WHERE vent_id='{$row->vent_id}'");
                if (($meta = $this->Model_general->guardar_registro("resumen", $registro)) == FALSE){
                    $this->Model_general->dieMsg(array('exito'=>false,'mensaje'=>'Error al guardar los datos'));
                }
                $oldid = $meta['id'];
                //echo $oldid."---";
            }
            $contador++;
            $this->db->query("UPDATE venta SET vent_resu_generado=1 WHERE vent_id='{$row->vent_id}'");
            $this->Model_general->guardar_registro("resumen_detalle", array('deta_resu_id'=>$oldid,'deta_vent_id'=>$row->vent_id));
            //echo $row->vent_serie." ".$row->vent_numero."<br>";
            
        }
        header("location:".base_url()."Resumen/listado");

    }


public function actualizararchivos() {
        $this->load->database();
        $this->load->helper('Funciones');
        $this->load->model("Model_general");


        $sql = "SELECT * FROM venta WHERE vent_resu_generado=0 AND (vent_fecha BETWEEN DATE_ADD(CURDATE(), INTERVAL -30 DAY) AND CURDATE()) AND (vent_comp_id=3 OR vent_nc_comp_id=3) ORDER BY vent_fecha ASC,vent_serie ASC,vent_numero ASC";
       
        $rows = $this->db->query($sql)->result();

       foreach($rows as $row){
            if($row->vent_fact_situ<2){
                file_get_contents(base_url()."Venta/crearArchivo/{$row->vent_id}/1");
                file_get_contents(base_url()."Venta/firmarArchivo/{$row->vent_id}/1");
            }
        }

    }

    public function edit($id=0){
        $this->load->database();
        $this->load->helper('Funciones');
        $this->load->model("Model_general");
        $this->load->library('Ssp');
        $this->load->library('Cssjs');
        $this->cssjs->set_path_js(base_url() . "assets/js/Resumen/");
        $this->cssjs->add_js('form');

       
        $resumen = $this->db->query("SELECT * FROM resumen WHERE resu_id='{$id}'")->row();
        $enviofecha = date_create($resumen->resu_enviofecha);
        $docufecha = date_create($resumen->resu_docufecha);
        $resumen->resu_enviofecha = date_format($enviofecha, 'd/m/Y');
        $resumen->resu_docufecha = date_format($docufecha, 'd/m/Y');
		
		
		$ventas = $this->db->query("SELECT * FROM resumen_detalle JOIN venta ON vent_id=deta_vent_id WHERE deta_resu_id='{$id}'")->result();
        $datas = array();
        $inits = array();
        foreach($ventas as $venta){
            $datas[] = array('id'=>$venta->vent_id,'text'=>$venta->vent_serie.'-'.$venta->vent_numero);
            $inits[] = $venta->vent_id;
        }
        $datos['comps_data'] = json_encode($datas);
        $datos['comps_init'] = json_encode($inits);
		
        $datos["resumen"] = $resumen;
        $datos["id"] = $id;

        
        $datos['titulo'] = "Editar";
        $script['js'] = $this->cssjs->generate_js();
        $this->load->view('header', $script);
        $this->load->view('menu');
        $this->load->view('Resumen/Formulario', $datos);
        $this->load->view('footer');
    }

	public function guardar($id=''){
		
		$this->load->library('Form_validation');
        $this->load->helper('Funciones');

        $this->form_validation->set_rules('enviofecha', 'Fecha de envio', 'required');
        
        if ($this->form_validation->run() == FALSE){		
            $this->Model_general->dieMsg(array('exito'=>false,'mensaje'=>validation_errors()));
        }

        $enviofecha = dateToMysql($this->input->post('enviofecha'));
        $ifecha = str_replace("-","",$enviofecha);
		$numero = $this->nextnum($ifecha);
        $numero = str_pad($numero,3, "0", STR_PAD_LEFT);
		
		$comprobantes = $this->input->post('comprobantes');



		
        $file = "{$this->configuracion->conf_ruc}-RC-{$ifecha}-{$numero}";

		$exist = $this->db->query("SELECT * FROM resumen WHERE resu_ifecha='{$ifecha}' AND resu_numero='{$numero}'".(empty($id)?'':" AND resu_id!={$id}"))->row();

        if(isset($exist->resu_numero)&&!empty($exist->resu_numero)){
            $this->Model_general->dieMsg(array('exito'=>false,'mensaje'=>'Ya existe la numeración'));
		}

		$registro = array("resu_enviofecha" => $enviofecha,
                        "resu_ifecha" => $ifecha,
                        "resu_numero" => $numero,
                        "resu_file"=> $file
						);

		if(empty($id)){
			$registro = array_merge($registro,array("resu_fact_situ"=>'1'));
			if (($meta = $this->Model_general->guardar_registro("resumen", $registro)) == FALSE){
                $this->Model_general->dieMsg(array('exito'=>false,'mensaje'=>'Error al guardar los datos'));
            }
            $id = $meta['id'];
			foreach($comprobantes as $comp){
                $this->Model_general->guardar_registro("resu_detalle", array('deta_resu_id'=>$id,'deta_vent_id'=>$comp));
            }
        }else{
        	$condicion_registro= "resu_id = ".$id;
        	if (($meta = $this->Model_general->guardar_edit_registro("resumen", $registro, $condicion_registro)) == FALSE){
                $this->Model_general->dieMsg(array('exito'=>false,'mensaje'=>'Error al guardar los datos'));
            }
			$sql = "DELETE FROM resumen_detalle WHERE deta_resu_id='{$id}'";
            $this->db->query($sql);
            foreach($comprobantes as $comp){
                $this->Model_general->guardar_registro("resumen_detalle", array('deta_resu_id'=>$id,'deta_vent_id'=>$comp));
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

        $resumen =  $this->db->query("SELECT * FROM resumen WHERE resu_id='{$id}'")->row();

        if(in_array($resumen->resu_fact_situ, array(3,4,5))) return true;

        if($accion=='xml'&&in_array($resumen->resu_fact_situ,array(1,6))){
            $this->crearArchivo($id);
            $this->firmarArchivo($id);
        }

        if($accion=='sunat'&&$resumen->resu_fact_situ==2){
            $this->enviarServidor($id);
        }

        if($accion=='sunat'&&in_array($resumen->resu_fact_situ,array(8,9))){
            $this->sacarEstado($id);
        }
        return true;
    }


    public function enviarServidor($id){
        $this->load->helper('firmar');
        $resumen =  $this->db->query("SELECT * FROM resumen WHERE resu_id='{$id}'")->row();
        $file = "files/FIRMA/{$resumen->resu_file}.xml";
        if(!file_exists($file)) die(json_encode(array('exito'=>false,'mensaje'=>'No hay archivo')));
        $str_xml = file_get_contents($file);
        $bin_zip = generarZip(array("{$resumen->resu_file}.xml"=>$str_xml));
        file_put_contents("files/ENVIO/{$resumen->resu_file}.zip",$bin_zip);
        $params = array('fileName' => "{$resumen->resu_file}.zip", 'contentFile' => $bin_zip);
        
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
            $this->Model_general->guardar_edit_registro("resumen",array('resu_fact_situ'=>$codigo,'resu_ticket'=>$result->ticket,'resu_fact_envi'=>date('Y-m-d H:i:s')),"resu_id = '{$id}'");
        }else{
            echo json_encode(array('exito'=>false,'mensaje'=>$result->value));
            exit(0);
        }
    }

    public function sacarEstado($id){
        $this->load->helper('firmar');
        $resumen =  $this->db->query("SELECT * FROM resumen WHERE resu_id='{$id}'")->row();
        $file = "files/FIRMA/{$resumen->resu_file}.xml";
        $params = array('ticket' => "{$resumen->resu_ticket}");

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

            $value = "No especificado.";
            if($estado->code=='0'||$estado->code=='99'){
                file_put_contents("files/RPTA/R{$resumen->resu_file}.zip", $estado->result->status->content);
                $res = getResponse("files/RPTA/R{$resumen->resu_file}.zip");
                $value = $res['cbc:ReferenceID'].' '.$res['cbc:ResponseCode'].' '.$res['cbc:Description'];
            }

            $codigo = 10;
            if($estado->code==0)$codigo = 3;
            if($estado->code==98)$codigo = 9;
            if($estado->code==99)$codigo = 10;
            
            $this->Model_general->guardar_edit_registro("resumen",array('resu_fact_situ'=>$codigo,'resu_fact_obse'=>$value),"resu_id = '{$id}'");
			if($codigo==3){
                $sql = "SELECT deta_vent_id FROM resumen_detalle WHERE deta_resu_id='{$id}'";
                $ventas = $this->db->query($sql)->result();
                $strsql = array();
                foreach($ventas as $venta){
                $strsql[]= $venta->deta_vent_id;
                }
                $strsql = implode(",",$strsql);

                $sql = "UPDATE venta SET vent_fact_situ=3,vent_fact_obse='Por resumen: {$resumen->resu_ifecha}-{$resumen->resu_numero}' WHERE vent_id IN({$strsql})";
                $this->db->query($sql);
                if($this->db->affected_rows()<=0) die(json_encode(array('exito'=>true,'mensaje'=>'Guardado pero comprobante relaconado no encontrado o no hizo cambios.')));
                else die(json_encode(array('exito'=>true,'mensaje'=>$this->db->affected_rows().' comprobantes relacionado afectado.')));
            }
            
        }else{
            echo json_encode(array('exito'=>false,'mensaje'=>$estado->code." : ".$estado->value));
            exit(0);
        }
    }


public function ponerResumen($id){
       $resumen =  $this->db->query("SELECT * FROM resumen WHERE resu_id='{$id}'")->row();
                $sql = "SELECT deta_vent_id FROM resumen_detalle WHERE deta_resu_id='{$id}'";
                $ventas = $this->db->query($sql)->result();
$strsql = array();
                foreach($ventas as $venta){
$strsql[]= $venta->deta_vent_id;
}
$strsql = implode(",",$strsql);

                $sql = "UPDATE venta SET vent_fact_situ=3,vent_fact_obse='Por resumen: {$resumen->resu_ifecha}-{$resumen->resu_numero}' WHERE vent_id IN({$strsql})";
                $this->db->query($sql);
                if($this->db->affected_rows()<=0) die(json_encode(array('exito'=>true,'mensaje'=>'Guardado pero comprobante relaconado no encontrado o no hizo cambios.')));
                else die(json_encode(array('exito'=>true,'mensaje'=>$this->db->affected_rows().' comprobantes relacionado afectado.')));
    }

    public function firmarArchivo($id){
        $this->load->helper('firmar');
        $resumen =  $this->db->query("SELECT * FROM resumen WHERE resu_id='{$id}'")->row();
        $file = "files/TEMP/{$resumen->resu_file}.xml";
        $file_pfx = "{$this->configuracion->conf_sunat_certificado}";
        $dom = formatoXML($file);
        $str_xml = firmarPFX($dom,$file_pfx,$this->configuracion->conf_sunat_certi_password);

        $data = file_get_contents($file);
        preg_match('/<ds:DigestValue>(.+?)<\/ds:DigestValue>/',$str_xml,$arr);
        $digestvalue = $arr[1];
        $this->Model_general->guardar_edit_registro("resumen",array('resu_digestvalue'=>$digestvalue),"resu_id = '{$id}'");

        file_put_contents("files/FIRMA/{$resumen->resu_file}.xml", $str_xml);
        $this->Model_general->guardar_edit_registro("resumen",array('resu_fact_situ'=>2,'resu_fact_gene'=>date('Y-m-d H:i:s')),"resu_id = '$id'");
    }


    function getXML($id){
        $venta = $this->db->query("SELECT * FROM resumen WHERE resu_id='{$id}'")->row();
        $file = "files/FIRMA/{$venta->resu_file}.xml";
        if(!file_exists($file)) die("No se ha encontrado el arhivo digital.");
        header('Content-Type: application/octet-stream');
        header("Content-Transfer-Encoding: Binary"); 
        header("Content-disposition: attachment; filename=\"" . basename($file) . "\""); 
        readfile($file); // do the double-download-dance (dirty but worky)
    }
    function getCDR($id){
        $venta = $this->db->query("SELECT * FROM resumen WHERE resu_id='{$id}'")->row();
        $file = "files/RPTA/R{$venta->resu_file}.zip";

        if(!file_exists($file)){
          die("No se ha encontrado el arhivo digital.".$file);  
        } 
        header('Content-Type: application/octet-stream');
        header("Content-Transfer-Encoding: Binary"); 
        header("Content-disposition: attachment; filename=\"" . basename($file) . "\""); 
        readfile($file); // do the double-download-dance (dirty but worky)
    }

    public function crearArchivo($id){
        $resumen = $this->db->query("SELECT * FROM resumen WHERE resu_id='{$id}'")->row();
        $file = "files/TEMP/{$resumen->resu_file}.xml";

        $ventas = $this->db->query("SELECT * FROM venta JOIN resumen_detalle ON deta_vent_id=vent_id WHERE deta_resu_id='{$id}'")->result();
        $comprobantes = array();

        foreach($ventas as $i=>$venta){
            $venta->vent_clie_num_documento = str_replace(array('/','-'),array('',''),$venta->vent_clie_num_documento);
            if(empty($venta->vent_clie_num_documento))$venta->vent_clie_num_documento='00000000';
            $venta->vent_clie_num_documento = str_pad($venta->vent_clie_num_documento, 8, "00", STR_PAD_LEFT);
            $comprobantes[] = array(
                'linea'=>$i+1,
                'tipoDocumento'=>$venta->vent_comp_id,
                'identificador'=>$venta->vent_serie.'-'.$venta->vent_numero,
                'nroDocumento'=>$venta->vent_numero,
                'tipoDocumentoCliente'=>$venta->vent_clie_docu_id,
                'numeroDocumentoCliente'=>$venta->vent_clie_num_documento,
                'tipoReferencia'=>$venta->vent_nc_comp_id,
                'comprobanteReferencia'=>$venta->vent_nc_serie.'-'.$venta->vent_nc_numero,
                'estado'=>1,
                'importeTotal'=>$venta->vent_total,
                'totalGravadas'=>$venta->vent_gravada,
                'totalExoneradas'=>$venta->vent_exonerada,
                'totalInafectas'=>$venta->vent_inafecta,
                'totalGratuitas'=>$venta->vent_gratuitas,
                'sumatoriaOtrosCargso'=>'0.00',
                'totalISC'=>'0.00',
                'totalIGV'=>$venta->vent_igv,
                'totalOtrosTributos'=>'0.00'
            );
        }
   
        $datos = array(
            'ublVersionIdSwf'=>"2.0",
            'CustomizationIdSwf'=>"1.1",
            'idResumen'=>"RC-{$resumen->resu_ifecha}-{$resumen->resu_numero}",
            'fechaEmisionDocumentos'=> $resumen->resu_docufecha,
            'fechaEnvioResumen'=>$resumen->resu_enviofecha,
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
        $this->load->view('Venta/plantillas/ConvertirRBoletasXML', $datos);
        $result = ob_get_contents();
        ob_end_clean();
        file_put_contents($file, $result);
        
    }

}
 ?>