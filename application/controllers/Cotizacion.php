<?php 
/**
* 
*/
class Cotizacion extends CI_Controller
{
    var $configuracion;
	function __construct() {
        parent::__construct();
        if(!$this->session->userdata('authorized')){
            redirect(base_url()."login");
        }
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
            array('db' => 'coti_id',            'dt' => 'ID',           "field" => "coti_id"),
            array('db' => 'coti_clie_rsocial',  'dt' => 'Cliente',     	"field" => "coti_clie_rsocial"),
            array('db' => 'coti_fecha',   		'dt' => 'Fecha',     	"field" => "coti_fecha"),
			array('db' => "CONCAT(coti_serie,'-',coti_numero)", 'dt' => 'Número', "field" => "CONCAT(coti_serie,'-',coti_numero)"),
            array('db' => 'coti_total',         'dt' => 'Total',       	"field" => "coti_total"),
            array('db' => 'coti_id',            'dt' => 'DT_RowId',    	"field" => "coti_id"),
            array('db' => 'coti_email_send',    'dt' => 'DT_EmailSend',	"field" => "coti_email_send")
        );

        if ($json) {

            $json = isset($_GET['json']) ? $_GET['json'] : false;

            $table = 'cotizacion';
            $primaryKey = 'coti_id';

            $sql_details = array(
                'user' => $this->db->username,
                'pass' => $this->db->password,
                'db' => $this->db->database,
                'host' => $this->db->hostname
            );

            $condiciones = array();
            $joinQuery = "FROM cotizacion";
            $where = "";
            if (!empty($_POST['desde'])&&!empty($_POST['hasta'])){
                $condiciones[] = "coti_fecha >='".$_POST['desde']."' AND coti_fecha <='".$_POST['hasta']."'";
            }

            $where = count($condiciones) > 0 ? implode(' AND ', $condiciones) : "";
            echo json_encode(
                    $this->ssp->simple($_POST, $sql_details, $table, $primaryKey, $columns, $joinQuery, $where)
            );
            exit(0);
        }
        $datos['columns'] = $columns;

        $this->cssjs->set_path_js(base_url() . "assets/js/cotizacion/");
        $this->cssjs->add_js('listado');
        $script['js'] = $this->cssjs->generate_js();
        $this->load->view('header', $script);
        $this->load->view('menu');
        $this->load->view('cotizacion/listado', $datos);
        $this->load->view('footer');
    }
    /*
    public function ver($id){
        $datos['id'] = $id;
        $this->load->view('Cotizacion/ver',$datos);
    }
    */
    public function vercomp($id){
        $datos['id'] = $id;
        $this->load->view('Cotizacion/vercomp',$datos);
    }
    public function pdf($id){
        $coti = $this->db->query("SELECT * FROM cotizacion WHERE coti_id='{$id}'")->row();
        $arch = $coti->coti_serie."-".$coti->coti_numero;
        $file = "files/REPO/{$arch}.pdf";
        if(!file_exists($file)) die("No se ha encontrado el arhivo digital.");
        header('Content-type: application/pdf');
        header('Content-Disposition: inline; filename="'.$arch.'.pdf"');
        readfile($file);


    }
	
	
    public function nextnum($serie){
        $this->db->select('MAX(coti_numero) as max');
        $this->db->from('cotizacion');
        $this->db->where("coti_serie='{$serie}'");
        $query = $this->db->get();
        $row = $query->row();
        $numero = $row->max+1;
        return $numero;
    }
	
	public function getnext($tipo,$serie){
		echo json_encode(array('numero'=>$this->nextnum($tipo,$serie)));
	}
    
    public function crear() {
        $this->load->database();
        $this->load->helper('Funciones');
        $this->load->model("Model_general");
        $serie="P001";

        $cotizacion = array(
                         'coti_serie' => $serie,
                         'coti_nc_serie' => '',
                         'coti_nc_numero' => '',
                         'coti_nc_cred_id' => '',
                         'coti_nc_debi_id' => '',
                         'coti_numero' => $this->nextnum($serie),
                         'coti_fecha' => date('d/m/Y'),
                         'coti_clie_docu_id' => "",
                         'coti_clie_num_documento' => '',
                         'coti_clie_rsocial' => '',
                         'coti_clie_direccion' => '',
                         'coti_moneda' => '',
                         'coti_clie_id' => '',
                         'coti_clie_email' => '',
                         'coti_sucu_id' => '',
                         'coti_desc_global' => '0.00',
                         'coti_descripcion' => '',
                         'clie_selected_data'=>'');
        
        $datos["tipo_detalle"] = $this->Model_general->getOptions('maestra_afectacion', array("afec_id", "afec_nombre"));
        $datos["documentos"] = $this->Model_general->getOptions('maestra_documentos', array("docu_id", "docu_nombre"));
        $datos["gratuita_select"] = $this->Model_general->enum_valores('cotizacion_detalle','deta_esgratuita');
        $datos["sucursales"] = $this->Model_general->getOptions('sucursal', array("sucu_id", "sucu_nombre"));
        $datos["cmb_unidad"] = $this->Model_general->getOptions('unidad', array("unid_sigla", "unid_nombre"));
        $datos["moneda"] = $this->Model_general->enum_valores('cotizacion','coti_moneda');
        $datos["cotizacion"] = (object)$cotizacion;
        $datos["productos"] = json_encode(array());
        $datos["id_cotizacion"] = '';
        $datos["id"] = "";

        $datos['titulo'] = "Registrar cotización";
        $this->load->library('Ssp');
        $this->load->library('Cssjs');
        $this->cssjs->set_path_js(base_url() . "assets/js/Cotizacion/");
        $this->cssjs->add_js('form');
        $script['js'] = $this->cssjs->generate_js();
        $this->load->view('header', $script);
        $this->load->view('menu');
        $this->load->view('cotizacion/formulario', $datos);
        $this->load->view('footer');
    }
    public function edit($id=0){
        $this->load->database();
        $this->load->helper('Funciones');
        $this->load->model("Model_general");
        $this->load->library('Ssp');
        $this->load->library('Cssjs');
        $this->cssjs->set_path_js(base_url() . "assets/js/Cotizacion/");
        $this->cssjs->add_js('form');

        $datos["documentos"] = $this->Model_general->getOptions('maestra_documentos', array("docu_id", "docu_nombre"));
        
        $cotizacion = $this->Model_general->getCotizacionById($id);
        $fecha = date_create($cotizacion->coti_fecha);
        $cotizacion->coti_fecha = date_format($fecha, 'd/m/Y');


        $arr_clie = array('id'=>$cotizacion->coti_clie_id,
        	'text'=>$cotizacion->coti_clie_rsocial,
        	'docnum'=>$cotizacion->coti_clie_num_documento,
        	'direccion'=>$cotizacion->coti_clie_direccion,
        	'documento'=>$cotizacion->coti_clie_docu_id
        	);

        $cotizacion->clie_selected_data = json_encode($arr_clie);

        $datos["cotizacion"] = $cotizacion;
        $datos["id_cotizacion"] = $id;
        $datos["id"] = "";
        $productos = $this->Model_general->getProductosByCotizacion($id);
        //$datos["tipo_detalle"] = $this->Model_general->enum_valores('cotizacion_detalle','deta_tipo');
        $datos["tipo_detalle"] = $this->Model_general->getOptions('maestra_afectacion', array("afec_id", "afec_nombre"));
        $datos["gratuita_select"] = $this->Model_general->enum_valores('cotizacion_detalle','deta_esgratuita');
        $datos["moneda"] = $this->Model_general->enum_valores('cotizacion','coti_moneda');
        $datos["productos"] = json_encode($productos);
        $datos["sucursales"] = $this->Model_general->getOptions('sucursal', array("sucu_id", "sucu_nombre"));
        $datos["cmb_unidad"] = $this->Model_general->getOptions('unidad', array("unid_sigla", "unid_nombre"));
        
        $datos['titulo'] = "Editar";
        $script['js'] = $this->cssjs->generate_js();
        $this->load->view('header', $script);
        $this->load->view('menu');
        $this->load->view('Cotizacion/Formulario', $datos);
        $this->load->view('footer');
    }

    public function getCotizacion(){
        $this->load->database();
        $this->load->model("Model_general");
        $id_cotizacion = $this->input->post('id_cotizacion');
        $cotizacion = $this->Model_general->getCotizacionById($id_cotizacion);
        echo json_encode($cotizacion);   
    }

    private function validarComprobante(){
        $this->load->library('Form_validation');
        $this->form_validation->set_rules('serie', 'Serie', 'required');
        $this->form_validation->set_rules('numero', 'Número', 'required');

        $this->form_validation->set_rules('rsocial', 'Razon Social', 'required');
        $this->form_validation->set_rules('detalle[]', 'Detalle del comprobante', 'required');
        
        if ($this->form_validation->run() == FALSE){        
            $this->Model_general->dieMsg(array('exito'=>false,'mensaje'=>validation_errors()));
        }
    }

	public function guardar($id=''){
		
		
        $this->load->helper('Funciones');

        $this->validarComprobante();
		
        $comprobante = $this->input->post('comprobante');
        $serie = $this->input->post('serie');
        $numero = str_pad($this->input->post('numero'), 8, "0", STR_PAD_LEFT);
		$fecha = dateToMysql($this->input->post('fecha'));
		$documento = $this->input->post('documento');
		$docnum = $this->input->post('docnum');
		$moneda = $this->input->post('coti_moneda');
		$rsocial = $this->input->post('rsocial');
		$direccion = $this->input->post('direccion');
        $email = $this->input->post('email');
        $desc_global = $this->input->post('desc_global');
		$total = $this->input->post('total_total');
		$total_igv = $this->input->post('total_igv');
		$valor = $this->input->post('total_valor');
        $exoneradas = $this->input->post('total_exoneradas');
        $inafectas = $this->input->post('total_inafectas');
        $gravadas = $this->input->post('total_gravadas');
		$descuento = $this->input->post('total_descuentos');
		$sub = $this->input->post('total_sub');
		$id_cliente = $this->input->post('clie_id');
        $enviar_email = ($this->input->post('enviar_email') == 1) ? 1 : 2;
        $descripcion = $this->input->post('descripcion');
        $sucursal = $this->input->post('sucursal');
        

        $nc_comprobante = in_array($this->input->post('comprobante'),array('07','08'))?$this->input->post('nc_comprobante'):null ;
        $nc_serie = in_array($this->input->post('comprobante'),array('07','08'))?$this->input->post('nc_serie'):null ;
        $nc_numero = in_array($this->input->post('comprobante'),array('07','08'))?$this->input->post('nc_numero'):null ;
        $credito_tipo = $this->input->post('comprobante') == '07'?$this->input->post('credito_tipo'):null ;
        $debito_tipo = $this->input->post('comprobante') == '08'?$this->input->post('debito_tipo'):null ;


        /*Variables axiliares*/
        $idsopre = $this->input->post('sopre');
        $json['from'] = $this->input->post('from')=='1'?true:false;

		$factura = array("coti_clie_rsocial" => $rsocial,
    					"coti_clie_direccion" => $direccion,
                        "coti_clie_email" => $email,
                        "coti_desc_global" => $desc_global,
    					"coti_fecha"=> $fecha,
    					"coti_serie"=> $serie,
    					"coti_numero" => $numero,
    					"coti_moneda" => $moneda,
    					"coti_clie_docu_id"=> $documento,
    					"coti_total"=> $total,
    					"coti_valor"=> $valor,
    					"coti_subtotal"=> $sub,
    					"coti_descuento"=> $descuento,
    					"coti_igv"=> $total_igv,
    					"coti_clie_id"=> $id_cliente,
                        "coti_sucu_id"=> $sucursal,
                        "coti_descripcion"=> $descripcion,
                        "coti_gravada"=> $gravadas,
                        "coti_exonerada"=> $exoneradas,
                        "coti_inafecta"=> $inafectas,
    					"coti_clie_num_documento" => $docnum);
        //detalles

		$gratuita = $this->input->post('gratuita'); //si esta marcado es "on"
		$detalle = $this->input->post('detalle');
		$tipo = $this->input->post('tipo');
		$cantidad = $this->input->post('cantidad');
		$valor = $this->input->post('valor');
        $unidad = $this->input->post('unidad');
        $codigo = $this->input->post('codigo');
		$precio = $this->input->post('precio');
		$descuento = $this->input->post('descuento');
		$igv = $this->input->post('igv');
		$importe = $this->input->post('importe');
		$prod_id = $this->input->post('producto');
		if(empty($id)){
			$this->db->trans_begin();
			if (($meta = $this->Model_general->guardar_registro("cotizacion", $factura)) == TRUE):
	            for ($i=0; $i < count($detalle); $i++) { 
	            	$item = array("deta_coti_id" => $meta['id'],
	            				"deta_descripcion" => $detalle[$i],
	            				"deta_cantidad" => $cantidad[$i],
	            				"deta_valor" => $valor[$i],
                                "deta_unidad"=>$unidad[$i],
	            				"deta_precio" => $precio[$i],
	            				"deta_descuento" => $descuento[$i],
	            				"deta_afec_id" => $tipo[$i],
	            				"deta_esgratuita" => $gratuita[$i],
	            				"deta_igv" => $igv[$i],
	            				"deta_importe" => $importe[$i],
	            				"deta_prod_id" => $prod_id[$i],
                                "deta_codigo"=>$codigo[$i]
	            		);
                    if($reg = $this->Model_general->guardar_registro("cotizacion_detalle", $item)==TRUE){
               
                    }else{
                        $this->Model_general->dieMsg(array('exito'=>false,'mensaje'=>'Error al guardar los datos'));
                        $this->db->trans_rollback();
                    }
	            }
	        else:
	            $this->Model_general->dieMsg(array('exito'=>false,'mensaje'=>'Error al guardar los datos'));
				$this->db->trans_rollback();
	        endif;
            $this->db->trans_commit();

			$id = $meta['id'];           
        }else{
            $condicion_factura = "coti_id = ".$id;
            $detalle_id = $this->input->post('deta_id');
            if (($meta = $this->Model_general->guardar_edit_registro("cotizacion", $factura, $condicion_factura)) == TRUE):

                $this->db->select("deta_id, deta_cantidad, deta_precio, deta_prod_id");
                $this->db->where('deta_coti_id',$id);
                $this->db->from("cotizacion_detalle");
                $actuales = $this->db->get()->result();
                foreach ($actuales as $key => $value) {
                    if (!in_array($value->deta_id, $detalle_id)) {
                        if($this->Model_general->borrar(array('deta_id' => $value->deta_id), 'cotizacion_detalle')){
                         
                        }
                    }
                }
                for ($i=0; $i < count($detalle); $i++) { 
                    
                    $condicion_items = "deta_id = ".$detalle_id[$i];
                    $item = array("deta_coti_id " => $id,
                                "deta_descripcion" => $detalle[$i],
                                "deta_cantidad" => $cantidad[$i],
                                "deta_unidad"=>$unidad[$i],
                                "deta_valor" => $valor[$i],
                                "deta_esgratuita" => $gratuita[$i],
                                "deta_afec_id" => $tipo[$i], 
                                "deta_precio" => $precio[$i],
                                "deta_igv" => $igv[$i],
                                "deta_importe" => $importe[$i],
                                "deta_descuento" => $descuento[$i],
                                "deta_prod_id" => $prod_id[$i],
                                "deta_codigo"=>$codigo[$i]
                    );


                    if(empty($detalle_id[$i])){
                        if($this->Model_general->guardar_registro("cotizacion_detalle", $item) != false){

                        }
                    }else{
                        $stock_prod = $this->db->select('prod_stock')->from('producto')->where('prod_id',$prod_id[$i])->get()->row();
                        
                        $deta_cantidad = $this->db->select('deta_cantidad')->from('cotizacion_detalle')->where('deta_id',$detalle_id[$i])->get()->row();
                        $deta_cantidad = (int)$deta_cantidad->deta_cantidad;

                        if($this->Model_general->guardar_edit_registro("cotizacion_detalle", $item, $condicion_items) != false){
                     
                        }
                    }
                }
            else:
               $this->Model_general->dieMsg(array('exito'=>false,'mensaje'=>'Error al guardar los datos'));
            endif;


        }
        if($enviar_email == 1&&!empty($email)){
            $this->enviar_comprobante($id,$email);
        }
        $this->genera_pdf($id);
        $this->Model_general->dieMsg(array_merge(array('exito'=>true,'mensaje'=>'','id'=>$id,'tipo'=>$comprobante),$json));
	}

    function eliminar($id){
        $this->Model_general->borrar(array('coti_id' => $id), 'cotizacion');
        die(json_encode(array('exito'=>true,'mensaje'=>'')));
    }
	public function enviar_comprobante($id=0,$correo,$body=""){

        $this->load->library('Mymail');

        $documento = $this->genera_pdf($id);
        $this->genera_pdf($id);

        $configuracion = $this->db->query("SELECT * from configuracion where conf_id = 1")->row();
        $cotizacion = $this->db->query("SELECT coti_serie,coti_numero,DATE_FORMAT(coti_fecha,'%d/%m/%Y') coti_fecha,coti_total from cotizacion where coti_id={$id}")->row();

        $mail = new PHPMailer() ;
		
		$bodye = '
		<br>
<br>
Para descargar el comprobante electrónico, seguir el siguiente enlace.
<br>
<br>
<a href="'.base_url().'Inicio/visor/'.$id.'/'.md5($configuracion->conf_ruc.$id."MCFACT").'">Ver Comprobante</a>
<br>
<br>
Si desea consultar los comprobantes individuales o por empresa, siga el siguiente enlace.
<br>
<br>
<a href="'.base_url().'Inicio">Consultar comprobantes</a>
<br>
<br>
';
		
        $body = empty($body)?$this->configuracion->conf_mail_body.$bodye:$body.$bodye;

        $mail->IsSMTP();
        $mail->Timeout  =   10;
        $mail->Host     = $configuracion->conf_mail_host;
        $mail->Port     = $configuracion->conf_mail_port;  
        $mail->SMTPAuth = true;
        $mail->SMTPSecure = "tls"; 
        $mail->SMTPDebug  = 0; 
        $mail->CharSet = "UTF-8";
        $mail->From     = $configuracion->conf_mail_user;
        $mail->FromName = $configuracion->conf_rsocial;
        $mail->Subject  = $this->configuracion->conf_mail_subject;
        $mail->AltBody  = "Adjunto"; 
        $mail->MsgHTML($body);
        $mail->AddStringAttachment($documento,$cotizacion->coti_numero.".pdf",'base64');

        $correos = preg_split('/[,;]/',$correo);
        foreach($correos as $correo){
            $mail->AddAddress(trim($correo),'');
        }
        $mail->SMTPAuth = true;

        $mail->Username = $configuracion->conf_mail_user;
        $mail->Password = $configuracion->conf_mail_password; 
        
        if($mail->Send()){           
            $this->db->query("UPDATE cotizacion SET coti_email_send=1 WHERE coti_id='{$id}'");
            echo json_encode(array('exito'=>true,'mensaje'=>"Envio con exito, ¡Gracias!"));
        }else{
            echo json_encode(array('exito'=>false,'mensaje'=>$mail->ErrorInfo));
        }
        
    }

    public function confirm_correo($id){
        $correo = $this->input->post("correo");
        $body = $this->input->post("body");
        $cotizacion = $this->db->query("SELECT coti_clie_email from cotizacion where coti_id='$id'")->row();
        $datos['cotizacion'] = $cotizacion;
        $datos['id'] = $id;
        if(!isset($correo)){
            $this->load->view('cotizacion/confirm_correo', $datos);
        }else{
            $this->enviar_comprobante($id,$correo,$body);
        }
    }

    public function enviarcorreo($id){
        $cotizacion = $this->db->query("SELECT coti_clie_email from cotizacion where coti_id='$id'")->row();
        $this->enviar_comprobante($id,$cotizacion->coti_clie_email,"");
    }

    public function genera_pdf($id=0){
        $this->load->library('numl');
        $coti = $this->Model_general->getCotizacionById($id);
        
        $fecha = date_create($coti->coti_fecha);
        $coti->coti_fecha = date_format($fecha, 'd/m/Y');

        $productos = $this->Model_general->getProductosByCotizacion($id);
        
        $readnumber = $this->numl->NUML(floor($coti->coti_total));
        $nada = explode('.',number_format($coti->coti_total,2, '.', ''));
        $nada = $nada[1];
        $total_textual = strtoupper($readnumber) . ' CON ' . $nada . '/100 ' . (($coti->coti_moneda == "SOLES") ? " SOLES" : "DOLARES AMERICANOS");

        $this->load->library('pdf');
        
        $this->pdf = new Pdf();
        $this->pdf->AddPage();
        $this->pdf->AliasNbPages();
        $this->pdf->SetTitle($coti->coti_serie."-".$coti->coti_numero);

        $this->pdf->SetFont('Arial', 'B', 7);

        $this->pdf->Image(base_url().'assets/img/global.png', 10, 07, 40,0 , 'PNG');
        $this->pdf->SetLeftMargin(40);
        /*$html = "<font face='helvetica' color='#777777'>{$this->configuracion->conf_rsocial}</font><br>";*/
        $html = "<font color='#ff0000' size='14' color='#333366'>      {$this->configuracion->conf_ncomercial}</font><br>";
        $html .= "<font size='10' color='#777777'>               {$this->configuracion->conf_impr_direccion}</font><br>";
        $html .= "<font size='9' color='#777777'>            t: 51-51-366172 / 364470     f: 51-51-351470</font><br>";
        //$html .= "<font size='9' color='#777777'>          e: reservas@qelqatani.com     w: qelqatani.com</font><br>";
        $this->pdf->WriteHTML(utf8_decode($html));
        $this->pdf->SetFillColor(255);

        $this->pdf->SetLeftMargin(40);
        $this->pdf->tbr = 3.5;
        $html = "<br><font color='#777777' size='7'>          {$this->configuracion->conf_impr_contactos}<br>";
        $html .= "                                {$this->configuracion->conf_impr_telefonos}<br>";
        $html .= "                                      {$this->configuracion->conf_impr_web}</font>";
        $this->pdf->WriteHTML(utf8_decode($html));
        $this->pdf->SetFillColor(255);

        $this->pdf->SetTextColor(30,30,30);
        $this->pdf->SetY(10);
        $this->pdf->SetLeftMargin(125);
        $this->pdf->SetFont('Arial', 'B', 8);
        
        $this->pdf->Cell(75,8,'R.U.C. '.$this->configuracion->conf_ruc,'',1,'C');
        $this->pdf->SetFillColor('240','240','240'); 
        $this->pdf->Cell(75,8,utf8_decode('PEDIDO'),'',1,'C',true);
        $this->pdf->Cell(75,8,utf8_decode($coti->coti_serie." - ".$coti->coti_numero),'',1,'C');
        $this->pdf->RoundedRect(125, 10, 75,25, 1, '1234', 'B');

        $this->pdf->SetLeftMargin(10);
        
        $this->pdf->SetFont('Arial', 'B', 8);

        preg_match_all("/.{1,70}[^ ]*/",$coti->coti_clie_rsocial,$rs);
        $coti->coti_clie_rsocial = implode("\r\n",$rs[0]);
        $asr = array();
        if(preg_match("/\n/",$coti->coti_clie_rsocial)){ ///  para saltos de linea
                $asr = explode("\n",($coti->coti_clie_rsocial));
                $coti->coti_clie_rsocial = $asr[0];
                $hline = 3;
                $this->pdf->Ln(2);
        }

        $this->pdf->Ln(5);
        $this->pdf->Cell(20,5,utf8_decode('Señor(es):'),0,0,'L');
        $this->pdf->SetFont('');
        $this->pdf->Cell(125,5,utf8_decode($coti->coti_clie_rsocial),0,0,'L');
        $this->pdf->SetFont('','B','');
        $this->pdf->Cell(25,5,'Fecha:',0,0,'L');
        $this->pdf->SetFont('');
        $this->pdf->Cell(20,5,$coti->coti_fecha,0,0,'R');

        $this->pdf->Ln();
         if(count($asr)>0){
            unset($asr[0]);
            foreach($asr as $desc){
                $this->pdf->Cell(20,$hline,'','',0,'C');
                $this->pdf->Cell(135,$hline,utf8_decode($desc),0,0,'L');
                $this->pdf->Ln();
            }
            $this->pdf->Ln(1);
        }

        if($coti->coti_clie_docu_id=='0') $coti->docu_nombre = 'Documento';
        
        $l_doc = 23;
        if($coti->coti_clie_docu_id=='4' || $coti->coti_clie_docu_id=='A') 
            $l_doc = 33;

        preg_match_all("/.{1,70}[^ ]*/",$coti->coti_clie_direccion,$ar);
        $coti->coti_clie_direccion = implode("\r\n",$ar[0]);
        $ds = array();
        if(preg_match("/\n/",$coti->coti_clie_direccion)){ ///  para saltos de linea
                $ds = explode("\n",($coti->coti_clie_direccion));
                $coti->coti_clie_direccion = $ds[0];
                $hline = 3;
                $this->pdf->Ln(2);
        }

        $this->pdf->SetFont('','B','');
        $this->pdf->Cell(20,5,utf8_decode('Dirección:'),0,0,'L');
        $this->pdf->SetFont('');
        $this->pdf->Cell(125,5,utf8_decode(str_replace("–","-",$coti->coti_clie_direccion)),0,0,'L');     
        $this->pdf->SetFont('','B','');
        $this->pdf->Cell(25,5,utf8_decode($coti->docu_nombre).": ",0,0,'L');
        $this->pdf->SetFont('');
        $this->pdf->Cell(20,5,$coti->coti_clie_num_documento,0,0,'R');
        
        if(count($ds)>0){
            $this->pdf->Ln();
            unset($ds[0]);
            foreach($ds as $desc){
                $this->pdf->Cell(20,$hline,'','',0,'C');
                $this->pdf->Cell(135,$hline,utf8_decode($desc),0,0,'L');
                $this->pdf->Ln(1);
            }
        }
        $header = array('CANT.','UNIDAD' ,'DESCRIPCION', 'P. UNITARIO','PRECIO DE VENTA');
        $w = array(15, 15,105, 25, 30);
        $this->pdf->Ln(8);
        $this->pdf->SetFont('','B','');
        $this->pdf->SetFillColor('200','200','200'); 
        for($i = 0; $i < count($header); $i++)
            $this->pdf->Cell($w[$i],5,$header[$i],0,0,'C',true);
        $this->pdf->Ln();
        $this->pdf->SetFont('');

        $indice = 0;

        if(!empty($movi->movi_descripcion)){
            $tmp_producto[] = (object)array(
                'deta_descripcion'=>($movi->movi_descripcion),
                'deta_cantidad'=>'',
                'deta_precio'=>'0',
                'deta_importe'=>'',
                'deta_prod_codigo'=>'',
                'deta_unidad'=>''
                );
            $productos = array_merge((array) $productos, $tmp_producto);
        }


        $lineas = 0;
        foreach ($productos as $num => $det) {
            $numero = 0;

        
            preg_match_all("/.{1,60}[^ ]*/",$det->deta_descripcion,$arra);
            $det->deta_descripcion = implode("\r\n",$arra[0]);
            

            $hline = 7;
            $dess = array();
            
            if(preg_match("/\n/",$det->deta_descripcion)){ ///  para saltos de linea
                $dess = explode("\n",$det->deta_descripcion);
                $det->deta_descripcion = $dess[0];
                $hline = 3;
                $this->pdf->Ln(2);
            }
            

            $det->deta_cantidad = empty($det->deta_cantidad)?'':ROUND($det->deta_cantidad);
            $det->deta_precio = empty($det->deta_precio)?'':number_format($det->deta_precio,2,'.','');

            $this->pdf->Cell($w[0],$hline,$det->deta_cantidad,'',0,'C');
            $this->pdf->Cell($w[1],$hline,$det->deta_unidad,'',0,'C');
            $this->pdf->Cell($w[2],$hline,utf8_decode($det->deta_descripcion),'',0,'L');
            $this->pdf->Cell($w[3],$hline,$det->deta_precio,'',0,'R');
            $this->pdf->Cell($w[4],$hline,$det->deta_importe,'',0,'R');
            $this->pdf->Ln();
            $lineas++;
            
            if(count($dess)>0){
                unset($dess[0]);
                foreach($dess as $desc){
                    $this->pdf->Cell($w[0],$hline,'',0,0,'C');
                    $this->pdf->Cell($w[1],$hline,'',0,0,'C');
                    $this->pdf->Cell($w[2],$hline,utf8_decode($desc),0,0,'L');
                    $this->pdf->Ln();
                    $lineas++;
                }
            }
            
            $indice++;
        }

        $this->pdf->Ln();
        $this->pdf->SetFont('','B','');
        $this->pdf->Cell(160,5,'Subtotal',0,0,'R');
        $this->pdf->SetFont('');
        $this->pdf->Cell(30,5,$coti->coti_valor,0,0,'R');
        $this->pdf->Ln();

        $this->pdf->SetFont('','B','');
        $this->pdf->Cell(140,5,'',0,0,'C');
        $this->pdf->Cell(20,5,'IGV 18%',0,0,'R');
        $this->pdf->SetFont('');
        $this->pdf->Cell(30,5,$coti->coti_igv,0,0,'R');

        $this->pdf->Ln();
        $this->pdf->Cell(190,0.2,'','',1,'R',true);
        $this->pdf->Ln();

        $this->pdf->SetTextColor(0,75,140);
        $this->pdf->SetFontSize(10);
        $this->pdf->SetFont('','B','');
        $this->pdf->Cell(160,10,'TOTAL',0,0,'R');
        $this->pdf->SetFont('');
        $this->pdf->Cell(30,10,($coti->coti_moneda=='DOLARES'?'$ ':'S/ ').$coti->coti_total,0,0,'R');
        $this->pdf->Ln();
        $this->pdf->Cell(190,0.2,'','',1,'R',true);


        $this->pdf->SetTextColor(0,0,0);
        $this->pdf->SetFontSize(7);
        $this->pdf->Ln();
        $this->pdf->SetFont('','B','');
        $this->pdf->Cell(15,7,'SON: ','',0,'L');
        $this->pdf->SetFont('');
        $this->pdf->Cell(175,7,utf8_decode($total_textual),'',1,'L');
        $this->pdf->Cell(190,0.2,'','',1,'R',true);

        
        
        $file = $coti->coti_serie."-".$coti->coti_numero;
        $this->pdf->Output("files/REPO/{$file}.pdf",'F');

    }
    public function reporte_excel(){
        $hasta = $this->input->get('hasta');
        $desde = $this->input->get('desde');
        /*
        $moneda = $this->input->get('moneda');
        $tipo = $this->input->get('comprobantes');
        $situacion = $this->input->get('situacion');
        */
        $search = $this->input->get('search');
        
        
        $this->db->select("DATE_FORMAT(C.coti_fecha,'%d/%m/%Y') AS fecha, 'PEDIDO' AS ctipo, C.coti_serie AS serie, C.coti_numero AS numero, DOC.docu_nombre as documento, C.coti_clie_num_documento as docid_nro, C.coti_clie_rsocial AS rsocial, IF(C.coti_moneda ='SOLES','S','D') as moneda, C.coti_total AS total, C.coti_descripcion as vdesc,, GROUP_CONCAT(DISTINCT VD.deta_descripcion ORDER BY VD.deta_id ASC) AS detalle");
        $this->db->from("cotizacion C");
        $this->db->join("cotizacion_detalle VD","VD.deta_coti_id = C.coti_id");
        $this->db->join("maestra_documentos DOC","DOC.docu_id = C.coti_clie_docu_id");
        $this->db->where("C.coti_fecha BETWEEN '$desde' AND '$hasta'".($search != ""? " AND (C.coti_clie_rsocial LIKE '%$search%' OR C.coti_serie LIKE '%$search%' OR C.coti_numero LIKE '%$search%')":""));
        $this->db->group_by('C.coti_id');
        $this->db->order_by("C.coti_serie","ASC");
        $this->db->order_by("C.coti_numero","ASC");
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
        
        $objPHPExcel->getActiveSheet()->getStyle('D')->applyFromArray($fillgray);
        $objPHPExcel->getActiveSheet()->getStyle('H')->applyFromArray($fillgray);

        $objPHPExcel->getActiveSheet()->getStyle('J')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('CEF1C9');
        $objPHPExcel->getActiveSheet()->getStyle('R')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('FFF9CE');

        $objPHPExcel->getActiveSheet()
                ->setCellValue('A1', 'FECHA')
                ->setCellValue('B1', 'TIPO')
                ->setCellValue('C1', 'SERIE')
                ->setCellValue('D1', 'NUMERO')
                ->setCellValue('E1', 'DOCUMENTO')
                ->setCellValue('F1', 'NUMERO')
                ->setCellValue('G1', 'RAZON SOCIAL')
                ->setCellValue('H1', 'MONEDA')
                ->setCellValue('I1', 'TOTAL')
                ->setCellValue('J1', 'DESCRIPCION')
                ->setCellValue('K1', 'DETALLE');
        
        $objPHPExcel->getActiveSheet()->getStyle('F')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
        $objPHPExcel->getActiveSheet()->getStyle('D')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);      
        $objPHPExcel->getActiveSheet()->freezePane('A2');
        
        $ini = 3;
        $index = 0;
        
        foreach($documentos as $fila){
            $nro = $index+$ini;
            $index++;
            $objPHPExcel->getActiveSheet()
                        ->setCellValue("A$nro", $fila->fecha)
                        ->setCellValue("B$nro", $fila->ctipo)
                        ->setCellValue("C$nro", $fila->serie)
                        ->setCellValue("D$nro", $fila->numero)
                        ->setCellValue("E$nro", $fila->documento)
                        ->setCellValue("F$nro", $fila->docid_nro)
                        ->setCellValue("G$nro", $fila->rsocial)
                        ->setCellValue("H$nro", $fila->moneda)
                        ->setCellValue("I$nro", $fila->total)
                        ->setCellValue("J$nro", $fila->vdesc)
                        ->setCellValue("K$nro", $fila->detalle);
            }
            foreach(range('A','Q') as $nro)
            {
                $objPHPExcel->getActiveSheet()->getColumnDimension($nro)->setAutoSize(true);
            }
        
        $fin = $index+$ini-1;
        $objPHPExcel->getActiveSheet()->getStyle("I$ini:I$fin")->getNumberFormat()->setFormatCode('#,##0.00'); 
        $objPHPExcel->getActiveSheet()->getStyle("A$ini:A$fin")->getNumberFormat()->setFormatCode('dd/mm/yyyy');
        
        $excel->excel_output($objPHPExcel, 'PEDIDOS '.$desde." - ".$hasta);
    }
}