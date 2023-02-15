<?php 
/**
* 
*/
class Almacen extends CI_Controller
{
    var $configuracion;
    var $titulos;
	function __construct() {
        parent::__construct();
        if(!$this->session->userdata('authorized')){
            redirect(base_url()."login");
        }
        $this->load->database();
        $this->configuracion = $this->db->query("SELECT * FROM configuracion")->row();
        $this->load->model("Model_general");
        $this->titulos = array(
                    '1'=>'Ingreso',
                    '2'=>'Salida',
                    '3'=>'Traslado'
                );
        $consult = $this->db->from("venta")->where("vent_pagado = 'NO'")->get()->result();
        $this->pendientes = count($consult);
    }

    public function index(){
        $this->load->view('header');
        $this->load->view('menu');
        $this->load->view('footer');
    }    

    public function buscar() {
        $this->load->helper('Funciones');
        $responese = new StdClass;
        $search = isset($_GET['q']) ? $_GET["q"] : '';
        $f = isset($_GET['f']) ? $_GET["f"] : '';

        $filtro = array("vent_file" => $search);
        if(!empty($f)){
            $f = dateToMysql($f);
            $filtro['vent_fecha'] = $f;
        }
        

        $datos = array();


        $producto = $this->Model_general->select2("venta", $filtro,'vent_id DESC');    
        //print_r($producto);
        foreach ($producto["items"] as $value) {
            $datos[] = array("id" => $value->vent_id, "text" => ($value->vent_serie."-".$value->vent_numero) );
        }
        $responese->total_count = $producto["total_count"];
        $responese->incomplete_results = false;
        $responese->items = $datos;
        echo json_encode($responese);
        
    }

    public function listado($idcomp=1) {
        $this->load->helper('Funciones');
        $this->load->database();
        $this->load->library('Ssp');
        $this->load->library('Cssjs');

        $tipo = $idcomp ==1?'INGRESO':'EGRESO';

        $json = isset($_GET['json']) ? $_GET['json'] : false;

        $columns = array(
            array('db' => 'movi_id',            'dt' => 'ID',           "field" => "movi_id"),
            array('db' => 'movi_prov_rsocial',	'dt' => 'Proveedor',    "field" => "movi_prov_rsocial"),
            array('db' => 'comp_nombre',   		'dt' => 'Comprobante',  "field" => "comp_nombre"),
            array('db' => 'movi_fecha',   		'dt' => 'Fecha',     	"field" => "movi_fecha"),
			array('db' => "CONCAT(movi_serie,'-',movi_numero)", 'dt' => 'Número', "field" => "CONCAT(movi_serie,'-',movi_numero)"),
            array('db' => 'movi_total',         'dt' => 'Total',       	"field" => "movi_total"),
            array('db' => 'movi_id',            'dt' => 'DT_RowId',    	"field" => "movi_id"),
            array('db' => 'movi_caja',          'dt' => 'DT_Caja',      "field" => "movi_caja"),
            array('db' => 'movi_almacen',       'dt' => 'DT_Alm',       "field" => "movi_almacen")
        );

        if ($json) {

            $json = isset($_GET['json']) ? $_GET['json'] : false;

            $table = 'movimiento';
            $primaryKey = 'movi_id';

            $sql_details = array(
                'user' => $this->db->username,
                'pass' => $this->db->password,
                'db' => $this->db->database,
                'host' => $this->db->hostname
            );

            $condiciones = array();
            $joinQuery = "FROM movimiento JOIN maestra_comprobantes ON comp_id=movi_comp_id";
            $where = "";
            if (!empty($_POST['desde'])&&!empty($_POST['hasta'])){
                $condiciones[] = "movi_fecha >='".$_POST['desde']."' AND movi_fecha <='".$_POST['hasta']."'";
            }
            $condiciones[] = "movi_clase='".$tipo."'";

           if (!empty($_POST['sucursal']))
                $condiciones[] = "movi_sucu_id='".$_POST['sucursal']."'";
            /*
            if (!empty($_POST['archivo']))
                $condiciones[] = "vent_genera_archivo='".$_POST['archivo']."'";
            if (!empty($_POST['estado']))
                $condiciones[] = "situ_grup_id='".$_POST['estado']."'";*/

            $where = count($condiciones) > 0 ? implode(' AND ', $condiciones) : "";
            echo json_encode(
                    $this->ssp->simple($_POST, $sql_details, $table, $primaryKey, $columns, $joinQuery, $where)
            );
            exit(0);
        }
        $datos["comprobantes"] = $this->Model_general->getOptions('maestra_comprobantes', array("comp_id", "comp_nombre"),'* Comprobantes');
        $datos["sucursales"] = $this->Model_general->getOptions('sucursal', array("sucu_id", "sucu_nombre"),'* Almacenes');
        $datos['columns'] = $columns;

        $datos['titulo'] = $this->titulos[$idcomp];
        $datos['idcomp'] = $idcomp;

        $this->cssjs->set_path_js(base_url() . "assets/js/Almacen/");
        $this->cssjs->add_js('listado');
        $script['js'] = $this->cssjs->generate_js();
        $this->load->view('header', $script);
        $this->load->view('menu');
        $this->load->view('almacen/listado', $datos);
        $this->load->view('footer');
    }
    public function inventario(){
        $this->load->helper('Funciones');
        $this->load->database();
        $this->load->library('Ssp');
        $this->load->library('Cssjs');

        $json = isset($_GET['json']) ? $_GET['json'] : false;

        $columns = array(
            array('db' => 'sucu_nombre',        'dt' => 'Almacen',         "field" => "sucu_nombre"),
            array('db' => 'prod_nombre',        'dt' => 'Producto',        "field" => "prod_nombre"),
            array('db' => 'stoc_cantidad',      'dt' => 'stoc_cantidad',   "field" => "stoc_cantidad"),
            array('db' => 'stoc_reg_fingreso',  'dt' => 'Último ingreso',  "field" => "stoc_reg_fingreso"),
            array('db' => 'stoc_reg_fsalida',   'dt' => 'Última salida',   "field" => "stoc_reg_fsalida")
        );

        if ($json) {

            $json = isset($_GET['json']) ? $_GET['json'] : false;

            $table = 'stock';
            $primaryKey = 'stoc_sucu_id';

            $sql_details = array(
                'user' => $this->db->username,
                'pass' => $this->db->password,
                'db' => $this->db->database,
                'host' => $this->db->hostname
            );

            $condiciones = array();
            

            $joinQuery = "FROM stock JOIN producto ON prod_id = stoc_prod_id LEFT JOIN sucursal ON sucu_id = stoc_sucu_id";
            $where = "";

            if (!empty($_POST['sucursal']))
                $condiciones[] = "stoc_sucu_id='".$_POST['sucursal']."'";

            $where = count($condiciones) > 0 ? implode(' AND ', $condiciones) : "";
            echo json_encode(
                    $this->ssp->simple($_POST, $sql_details, $table, $primaryKey, $columns, $joinQuery, $where)
            );
            exit(0);
        }
        $datos['columns'] = $columns;

        $datos['titulo'] = "Inventario de productos";
        

        $datos["sucursal"] = $this->Model_general->getOptions('sucursal', array("sucu_id", "sucu_nombre"),'* Almacenes');

        $this->cssjs->set_path_js(base_url() . "assets/js/Almacen/");
        $this->cssjs->add_js('inventario');
        $script['js'] = $this->cssjs->generate_js();
        $this->load->view('header', $script);
        $this->load->view('menu');
        $this->load->view('almacen/inventario', $datos);
        $this->load->view('footer');
    }
    public function ver($id,$bool=false){

        $datos['id'] = $id;
        $datos['bool'] = $bool;
        if($bool){
            $this->load->helper('Funciones');
            $this->load->database();
            $this->load->library('Ssp');
            $this->load->library('Cssjs');
            $this->cssjs->set_path_js(base_url() . "assets/js/Venta/");
            $this->cssjs->add_js('listado');
            $datos['js'] = $this->cssjs->generate_js();
            $this->load->view('header', $datos);
        }
        $venta = $this->db->query("SELECT * FROM venta WHERE vent_id='{$id}'")->row();
        $datos['venta'] = $venta;
        $this->load->view('Venta/ver',$datos);
        if($bool)$this->load->view('footer');
    }

    public function vercomp($id){
        $datos['id'] = $id;
        $this->load->view('Almacen/vercomp',$datos);
    }
    public function pdf($id){
        $movi = $this->db->query("SELECT * FROM movimiento WHERE movi_id='{$id}'")->row();
        $archivo = $movi->movi_serie."-".$movi->movi_numero;
        $file = "files/REPO/{$archivo}.pdf";
        if(!file_exists($file)) die("No se ha encontrado el arhivo digital.");
        header('Content-type: application/pdf');
        header('Content-Disposition: inline; filename="'.$archivo.'.pdf"');
        readfile($file);
    }
	
	
    public function nextnum($tipo,$serie){
        $this->db->select('MAX(movi_numero) as max');
        $this->db->from('movimiento');
        //$this->db->where("movi_tipo_id=$tipo AND movi_serie='{$serie}'");
        $this->db->where("movi_serie='{$serie}'");
        $query = $this->db->get();
        $row = $query->row();
        $numero = $row->max+1;
        return $numero;
    }
	
	public function getnext($tipo,$serie){
		echo json_encode(array('numero'=>$this->nextnum($tipo,$serie)));
	}
    
    public function crear($idcomp) {
        $this->load->database();
        $this->load->helper('Funciones');
        $this->load->model("Model_general");
        $serie="M001";
        $movi = array('movi_fecha' => date('d/m/Y'),
                         'movi_comp_id' => '',
                         'movi_comp_numero' => '',
                         'movi_comp_serie' => '',
                         'movi_serie' => $serie,
                         'movi_numero' => $this->nextnum($idcomp,$serie),
                         'movi_prov_id' => '',
                         'movi_sucu_id' => '',
                         'movi_sucu_id_t' => '',
                         'movi_prov_direccion' => '',
                         'movi_prov_rsocial' => '',
                         'movi_prov_docu_id' => '',
                         'movi_prov_num_documento' => '',
                         'movi_prov_email' => '',
                         'movi_moneda' => 1,
                         'movi_tipo_id' => '',
                         'movi_descripcion' => '');

        $datos["comprobantes"] = $this->Model_general->getOptions('maestra_comprobantes', array("comp_id", "comp_nombre"), "* Comprobantes");
        $datos["tipo_mov"] = $this->Model_general->getOptionsWhere('kardex_producto_tipo', array("tipo_id", "tipo_nombre"),array("tipo_tipo" => (int)$idcomp), "* Tipo");
        /*
        echo $this->db->last_query();
        print_r($datos["tipo_mov"]);
        exit(0);
        */
        $datos["denom"] = ($idcomp == 1) ? 'VALOR' : 'PRECIO';
        $datos["credito_tipo"] = $this->Model_general->getOptions('maestra_creditos', array("cred_id", "cred_nombre"));
        $datos["sucursal"] = $this->Model_general->getOptions('sucursal', array("sucu_id", "sucu_nombre"), "* Almacenes");        
        $datos["debito_tipo"] = $this->Model_general->getOptions('maestra_debitos', array("debi_id", "debi_nombre"));
        $datos["comprobantes_nota"] = select_options($this->db->query("SELECT comp_id,comp_nombre FROM maestra_comprobantes WHERE comp_id<=3")->result(),array('comp_id','comp_nombre'));
        $datos["documentos"] = $this->Model_general->getOptions('maestra_documentos', array("docu_id", "docu_nombre"));
        $datos["cmb_unidad"] = $this->Model_general->getOptions('unidad', array("unid_sigla", "unid_nombre"));
        //$datos["tipo_detalle"] = $this->Model_general->enum_valores('venta_detalle','deta_tipo');
        $datos["tipo_detalle"] = $this->Model_general->getOptions('maestra_afectacion', array("afec_id", "afec_nombre"));
        $datos["gratuita_select"] = $this->Model_general->enum_valores('venta_detalle','deta_esgratuita');
        $datos["moneda"] = $this->Model_general->enum_valores('venta','vent_moneda');
        $datos["movi"] = (object)$movi;
        $datos["productos"] = json_encode(array());
        $datos["id_movi"] = '';
        $datos["movi_clase"] = $idcomp;
        $datos["id"] = "";

        $datos['titulo'] = "Registrar ".$this->titulos[$idcomp];
        $datos['peq_titulo'] = $this->titulos[$idcomp];
        $this->load->library('Ssp');
        $this->load->library('Cssjs');
        $this->cssjs->set_path_js(base_url() . "assets/js/Almacen/");
        $this->cssjs->add_js('form');
        $script['js'] = $this->cssjs->generate_js();
        $this->load->view('header', $script);
        $this->load->view('menu');
        $this->load->view('almacen/formulario', $datos);
        $this->load->view('footer');
    }
    public function edit($id=0){
        $this->load->database();
        $this->load->helper('Funciones');
        $this->load->model("Model_general");
        $this->load->library('Ssp');
        $this->load->library('Cssjs');
        $this->cssjs->set_path_js(base_url() . "assets/js/Almacen/");
        $this->cssjs->add_js('form');

        $datos["comprobantes"] = $this->Model_general->getOptions('maestra_comprobantes', array("comp_id", "comp_nombre"));
        $datos["credito_tipo"] = $this->Model_general->getOptions('maestra_creditos', array("cred_id", "cred_nombre"));
        $datos["debito_tipo"] = $this->Model_general->getOptions('maestra_debitos', array("debi_id", "debi_nombre"));
        $datos["comprobantes_nota"] = select_options($this->db->query("SELECT comp_id,comp_nombre FROM maestra_comprobantes WHERE comp_id<=3")->result(),array('comp_id','comp_nombre'));
        $datos["documentos"] = $this->Model_general->getOptions('maestra_documentos', array("docu_id", "docu_nombre"));
        $datos["cmb_unidad"] = $this->Model_general->getOptions('unidad', array("unid_sigla", "unid_nombre"));
        
        $movi = $this->Model_general->getMovimientoById($id);
        $fecha = date_create($movi->movi_fecha);
        $movi->movi_fecha = date_format($fecha, 'd/m/Y');


        $arr_clie = array('id'=>$movi->movi_prov_id,
        	'text'=>$movi->movi_prov_rsocial,
        	'docnum'=>$movi->movi_prov_num_documento,
        	'direccion'=>$movi->movi_prov_direccion,
        	'documento'=>$movi->movi_prov_docu_id
        	);
         
        switch ($movi->movi_clase) {
            case 'INGRESO':
                $movi->movi_clase = 1;
                break;
            case 'EGRESO':
                $movi->movi_clase = 2;
                break;
            case 'TRASLADO':
                $movi->movi_clase = 3;
                break;
            default:
                $movi->movi_clase = 0;
                break;
        }

        $movi->clie_selected_data = json_encode($arr_clie);
        $datos["denom"] = ($movi->movi_clase == 1) ? 'VALOR' : 'PRECIO';
        $datos["movi"] = $movi;
        $datos["movi_clase"] = $movi->movi_clase;
        $datos['peq_titulo'] = $this->titulos[$movi->movi_clase];
        $datos["id_movi"] = $id;
        $datos["id"] = "";
        $productos = $this->Model_general->getProductosByMovimiento($id);
        //$datos["tipo_detalle"] = $this->Model_general->enum_valores('venta_detalle','deta_tipo');
        $datos["tipo_detalle"] = $this->Model_general->getOptions('maestra_afectacion', array("afec_id", "afec_nombre"));
        $datos["moneda"] = $this->Model_general->enum_valores('venta','vent_moneda');
        $datos["tipo_mov"] = $this->Model_general->getOptionsWhere('kardex_producto_tipo', array("tipo_id", "tipo_nombre"),array("
            tipo_tipo" => $movi->movi_clase));
        $datos["productos"] = json_encode($productos);
        $datos["sucursal"] = $this->Model_general->getOptions('sucursal', array("sucu_id", "sucu_nombre"));
        $datos["gratuita_select"] = $this->Model_general->enum_valores('venta_detalle','deta_esgratuita');
        
        $datos['titulo'] = "Editar ".$this->titulos[$movi->movi_clase];;
        $script['js'] = $this->cssjs->generate_js();
        $this->load->view('header', $script);
        $this->load->view('menu');
        $this->load->view('almacen/formulario', $datos);
        $this->load->view('footer');
    }

    public function pagar($id){
        $this->load->helper('Funciones');
        //$datos["venta"] = $this->db->from('venta')->where("vent_id",$id)->get()->row();
        $datos["movi"] = $this->db->where("movi_id", $id)->get('movimiento')->row();
        
        $datos["cuentas"] = $this->Model_general->getOptionsWhere("cuenta",array('cuen_id','cuen_banco'),array('cuen_activo' => 'SI'));

        
        $this->load->view('Almacen/cobrar', $datos);
    }
    public function guardar_pago($id_movi){
        
        $total = $this->input->post('total');
        $cuenta = $this->input->post('cuenta');
        $datos = array('pago_vent_id' => $id_movi,
                        'pago_monto' => $total,
                        'pago_fecha' => date('Y-m-d H:i:s'),
                        'pago_saldo' => 0,
                        'pago_cuen_id' => $cuenta
        );
        $this->db->trans_start();
        if($this->Model_general->guardar_registro("pago", $datos) != FALSE){
            
            $this->Model_general->guardar_edit_registro("movimiento", array("movi_caja" => 'SI'), array('movi_id' => $id_movi));
            $movi = $this->db->where("movi_id", $id_movi)->get("movimiento")->row();
            $arr1 = array(1,4,8);
            if(in_array($movi->movi_tipo_id,$arr1))
                $this->afectar_cuenta($cuenta, 'INGRESO', $total);
            else    
                $this->afectar_cuenta($cuenta, 'EGRESO', $total);
            
        }
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
    public function afectar_cuenta($id_cuenta,$accion, $monto){
        $cuenta = $this->db->where("cuen_id", $id_cuenta)->get("cuenta")->row();
        $nuevo_monto = ($accion == 'INGRESO' ? $cuenta->cuen_monto + $monto : $cuenta->cuen_monto - $monto);
        $this->Model_general->guardar_edit_registro("cuenta", array("cuen_monto" => $nuevo_monto), array('cuen_id' => $id_cuenta));
    }

    private function validarComprobante(){
        $this->load->library('Form_validation');
        $this->form_validation->set_rules('serie', 'Serie', 'required');
        $this->form_validation->set_rules('numero', 'Número', 'required');

        $this->form_validation->set_rules('comp_serie', 'Serie', 'required');
        $this->form_validation->set_rules('comp_numero', 'Número', 'required');
        $this->form_validation->set_rules('tipo_mov', 'Tipo', 'required');
        $this->form_validation->set_rules('sucursal', 'Sucursal', 'required');
        $this->form_validation->set_rules('comprobante', 'Comprobante', 'required');

        if($this->input->post('exterior')=='1'){
            //$this->form_validation->set_rules('rsocial', 'Razon Social', 'required');
        }else if(in_array($this->input->post('comprobante'),array('01','07','08'))){
            $this->form_validation->set_rules('fecha', 'Fecha', 'required');
            /*
            $this->form_validation->set_rules('rsocial', 'Razon Social', 'required');            
            $this->form_validation->set_rules('direccion', 'Dirección', 'required');
            $this->form_validation->set_rules('docnum', 'Número de documento', 'required');
            if($this->input->post('comprobante')=="01"){
                $this->form_validation->set_rules('docnum', 'Número de documento', 'required|exact_length[11]');
                $this->form_validation->set_rules('documento', 'Tipo de documento', 'regex_match[/6/]',array('regex_match'=>"El tipo de documento tiene que ser RUC"));
            }
            */
        }
         $this->form_validation->set_rules('detalle[]', 'Detalle del comprobante', 'required');

        if(in_array($this->input->post('comprobante'),array('07','08'))){
            $this->form_validation->set_rules('descripcion', 'Descripción', 'required');
        }
        
        if ($this->form_validation->run() == FALSE){        
            $this->Model_general->dieMsg(array('exito'=>false,'mensaje'=>validation_errors()));
        }
    }

	public function guardar($id=''){
		
		
        $this->load->helper('Funciones');
        $clase  = $this->input->post('movi_clase');
        $sucursal = $this->input->post('sucursal');
        $sucursalt = $this->input->post('sucursalt') != '' ? $this->input->post('sucursalt') : 1;
        $mvlc = $clase;
        if($clase != 3){
            $this->validarComprobante();
        }else{
            if($sucursal == $sucursalt)
                $this->Model_general->dieMsg(array('exito'=>false,'mensaje'=>'Las sucursales tienen que ser diferentes.'));
        }
        
        $comprobante = $this->input->post('comprobante');
        $num_compro = $this->input->post('comp_numero');
        $serie_compro = $this->input->post('comp_serie');
        $tipo_mov = $this->input->post('tipo_mov');
        $serie = $this->input->post('serie');
        $numero = str_pad($this->input->post('numero'), 8, "0", STR_PAD_LEFT);
		$fecha = dateToMysql($this->input->post('fecha'));
		$documento = $this->input->post('documento');
		$docnum = $this->input->post('docnum');
		$moneda = $this->input->post('t_moneda') != '' ? $this->input->post('t_moneda') : 1;
		$rsocial = $this->input->post('rsocial');
		$direccion = $this->input->post('direccion');
        $email = $this->input->post('email');
		$total = $this->input->post('total_total');
		$total_igv = $this->input->post('total_igv');
		$valor = $this->input->post('total_valor');
		$sub = $this->input->post('total_sub');
		$id_cliente  = $this->input->post('cliente');
        
        $descripcion = $this->input->post('descripcion');
        

        /*Variables axiliares*/
        $idsopre = $this->input->post('sopre');
        $json['from'] = $this->input->post('from')=='1'?true:false;
        /**/

        //Verifica si hay conflicto
        $exist = $this->db->query("SELECT date_format(movi_fecha,'%d/%m/%Y') movi_fecha,movi_serie,movi_numero FROM movimiento WHERE movi_serie='{$serie}' AND (movi_numero='{$numero}' OR (movi_numero>'{$numero}' AND movi_fecha<'{$fecha}') OR (movi_numero<'{$numero}' AND movi_fecha>'{$fecha}' )) ".(empty($id)?'':" AND movi_id!={$id}") ." ORDER BY movi_fecha DESC LIMIT 1")->result();
        if(count($exist)>0){
            $text = "";
            foreach($exist as $row){
                $text .= "[{$row->movi_serie}-{$row->movi_numero}-{$row->movi_fecha}] ";
            }
            $this->Model_general->dieMsg(array('exito'=>false,'mensaje'=>'Hay conflictos con documentos: '.$text));
        }
        

        $factura = array("movi_fecha"=> $fecha,
    					"movi_comp_id" => $comprobante,
                        "movi_comp_numero" => $num_compro,
                        "movi_comp_serie" => $serie_compro,
    					"movi_serie"=> $serie,
    					"movi_numero" => $numero,
    					"movi_prov_id"=> $id_cliente,
                        "movi_prov_direccion" => $direccion,
                        "movi_prov_rsocial" => $rsocial,
                        "movi_prov_docu_id"=> $documento,
    					"movi_prov_num_documento" => $docnum,
                        "movi_prov_email" => $email,
                        "movi_moneda" => $moneda,
                        "movi_total"=> $total,
                        "movi_valor"=> $valor,
                        "movi_subtotal"=> $sub,
                        "movi_igv"=> $total_igv,
                        "movi_descripcion"=> $descripcion,
                        "movi_clase"=> $clase,
                        "movi_sucu_id"=> $sucursal,
                        "movi_sucu_id_t"=> $sucursalt,
                        "movi_tipo_id"=> $tipo_mov
                    );
        //detalles
		$detalle = $this->input->post('detalle');
		$tipo = $this->input->post('tipo');
		$cantidad = $this->input->post('cantidad');
		$valor = $this->input->post('valor');
        $unidad = $this->input->post('unidad');
		$precio = $this->input->post('precio');
		$igv = $this->input->post('igv');
		$importe = $this->input->post('importe');
		$prod_id = $this->input->post('producto');

        $clase = $clase == 1 ? 'INGRESO' : 'EGRESO';

		if(empty($id)){
			$this->db->trans_begin();
			if (($meta = $this->Model_general->guardar_registro("movimiento", $factura)) == TRUE):
	            for ($i=0; $i < count($detalle); $i++) { 
	            	$item = array("deta_movi_id" => $meta['id'],
                                "deta_unidad"=>$unidad[$i],
                                "deta_descripcion" => $detalle[$i],
                                "deta_cantidad" => $cantidad[$i],
                                "deta_valor" => $valor[$i],
	            				"deta_precio" => $precio[$i],
	            				"deta_afec_id" => $tipo[$i],
	            				"deta_igv" => $igv[$i],
	            				"deta_importe" => $importe[$i],
                                "deta_sucu_id" => $sucursal,
	            				"deta_prod_id" => $prod_id[$i]
	            		);

                    if($reg = $this->Model_general->guardar_registro("movimiento_detalle", $item)==TRUE){
                        
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
            $condicion_factura = "movi_id = ".$id;
            $detalle_id = $this->input->post('deta_id');
            $this->db->trans_begin();
            if (($meta = $this->Model_general->guardar_edit_registro("movimiento", $factura, $condicion_factura)) == TRUE):

                $this->db->select("deta_id, deta_cantidad, deta_precio, deta_prod_id");
                $this->db->where('deta_movi_id',$id);
                $this->db->from("movimiento_detalle");
                $actuales = $this->db->get()->result();

                foreach ($actuales as $key => $value) {
                    if (!in_array($value->deta_id, $detalle_id)) {
                        if($this->Model_general->borrar(array('deta_id' => $value->deta_id), 'movimiento_detalle')){
                        }else{
                            $this->Model_general->dieMsg(array('exito'=>false,'mensaje'=>'Error al guardar los datos'));
                            $this->db->trans_rollback();
                        }
                    }
                }
                for ($i=0; $i < count($detalle); $i++) { 

                    $condicion_items = "deta_id = ".$detalle_id[$i];
                    $item = array("deta_movi_id" => $id,
                                "deta_unidad"=>$unidad[$i],
                                "deta_descripcion" => $detalle[$i],
                                "deta_cantidad" => $cantidad[$i],
                                "deta_valor" => $valor[$i],
                                "deta_precio" => $precio[$i],
                                "deta_afec_id" => $tipo[$i],
                                "deta_igv" => $igv[$i],
                                "deta_importe" => $importe[$i],
                                "deta_sucu_id" => $sucursal,
                                "deta_prod_id" => $prod_id[$i]
                        );

                    if(empty($detalle_id[$i])){
                        if($this->Model_general->guardar_registro("movimiento_detalle", $item) != false){
                            
                        }else{
                            $this->Model_general->dieMsg(array('exito'=>false,'mensaje'=>'Error al guardar los datos'));
                            $this->db->trans_rollback();
                        }
                    }else{
                        $deta_cantidad = $this->db->select('deta_cantidad')->from('movimiento_detalle')->where('deta_id',$detalle_id[$i])->get()->row();

                        $this->Model_general->guardar_edit_registro("movimiento_detalle", $item, $condicion_items);

                    }
                }
            else:
                $this->Model_general->dieMsg(array('exito'=>false,'mensaje'=>'Error al guardar los datos'));
                $this->db->trans_rollback();
            endif;
            $this->db->trans_commit();
        }
        $this->genera_pdf($id);
        $this->Model_general->dieMsg(array_merge(array('exito'=>true,'mensaje'=>'','id'=>$id,'tipo'=>intval($mvlc)),$json));
	}
    public function alm_confirm($id_movi){
        $this->load->helper('Funciones');
        $datos["movimiento"] = $this->db->select('tipo_tipo, movi_id')->from('movimiento')->join("kardex_producto_tipo", "tipo_id = movi_tipo_id")->where("movi_id", $id_movi)->get()->row();
        $datos["productos"] = $this->Model_general->getProductosByMovimiento($id_movi);
        $this->load->view('almacen/confirm_alm', $datos);
    }
    public function alm_guardar($id_movi){
        $this->db->trans_start();
        $productos = $this->Model_general->getProductosByMovimiento($id_movi);  
        $movi = $this->db->where('movi_id', $id_movi)->get("movimiento")->row();
        
        $accion = $movi->movi_clase;

        foreach ($productos as $val) {
            $costo = $val->deta_precio;
            $precio = $val->deta_precio;
            
            $this->Model_general->afectar_almacen(
                $val->deta_prod_id,
                $costo,
                $precio,
                $val->deta_cantidad, 
                $accion,
                $movi->movi_sucu_id,
                $movi->movi_comp_id,
                $movi->movi_comp_serie,
                $movi->movi_comp_numero, 
                $movi->movi_tipo_id,
				$val->deta_descripcion
                );
        }
        
        $this->Model_general->guardar_edit_registro("movimiento", array("movi_almacen" => 'SI'), array('movi_id' => $id_movi));

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

   

    function eliminar($id){


        $this->db->trans_begin();

        $comp = $this->Model_general->getVentaById($id);

        $this->db->select("deta_id, deta_cantidad, deta_precio, deta_prod_id");
        $this->db->where('deta_vent_id',$id);
        $this->db->from("venta_detalle");
        $actuales = $this->db->get();

        if($this->Model_general->borrar(array('vent_id' => $id, "vent_fact_situ" => 1), 'venta')){
            if($actuales->num_rows() > 0){
                $actuales = $actuales->result();
                foreach ($actuales as $key => $value) {

                    if($this->Model_general->borrar(array('deta_id' => $value->deta_id), 'venta_detalle')){
                        
                        $stock_prod = $this->db->select('prod_stock')->from('producto')->where('prod_id',$value->deta_prod_id)->get()->row();
                        $cant_stock = (int)$stock_prod->prod_stock + $value->deta_cantidad;
                        $kardex = array("kard_prod_id" => $value->deta_prod_id,
                                        "kard_compro_id" => '',
                                        "kard_numero" => $comp->vent_serie." - ".$comp->vent_numero,
                                        "kard_fecha" => date('Y-m-d'),
                                        "kard_precio" => $value->deta_precio,
                                        "kard_cantidad" => $value->deta_cantidad,
                                        "kard_tipo" => 1,
                                        "kard_tipo_id" => 6,
                                        "kard_ingreso" => $value->deta_cantidad,
                                        "kard_egreso" => 0.00,
                                        "kard_descripcion" => 'Venta eliminada -> '.$comp->vent_serie." - ".$comp->vent_numero,
                                        "kard_stock" => $cant_stock);
                        $prod_condicion = "prod_id = ".$value->deta_prod_id;
                       if($this->Model_general->guardar_registro("kardex_producto", $kardex)){
                            if(!$this->Model_general->guardar_edit_registro("producto", array("prod_stock" => $cant_stock), $prod_condicion)){
                                $this->Model_general->dieMsg(array('exito'=>false,'mensaje'=>'Error al eliminar los datos'));
                                $this->db->trans_rollback();
                            }
                        }else{
                            $this->Model_general->dieMsg(array('exito'=>false,'mensaje'=>'Error al eliminar los datos'));
                            $this->db->trans_rollback();
                        }
                    }else{
                        $this->Model_general->dieMsg(array('exito'=>false,'mensaje'=>'Error al eliminar los datos'));
                        $this->db->trans_rollback();
                    }
                }
            }
        }else{
            $this->Model_general->dieMsg(array('exito'=>false,'mensaje'=>'Error al eliminar los datos'));
            $this->db->trans_rollback();
        }
        
        $this->db->trans_commit();
        die(json_encode(array('exito'=>true,'mensaje'=>'')));
    }


 

    public function getNumTextual($total,$moneda){
        $this->load->library('numl');
        $readnumber = $this->numl->NUML(floor($total));
        $nada = explode('.',number_format($total,2, '.', ''));
        $nada = $nada[1];
        $total_textual = strtoupper($readnumber) . ' CON ' . $nada . '/100 ' . (($moneda == "SOLES") ? " SOLES" : "DOLARES AMERICANOS");
        return $total_textual;
    }

	

   
	function reporte_excel(){
        $hasta = $this->input->get('hasta');
        $desde = $this->input->get('desde');
        $tipo = $this->input->get('tipo') == 1 ? 'INGRESO' : 'EGRESO';
        $search = $this->input->get('search');
        
        $this->db->select("DATE_FORMAT(V.movi_fecha,'%d/%m/%Y') AS fecha, COMP.comp_abrev AS ctipo, V.movi_serie AS serie, V.movi_numero AS numero, DOC.docu_nombre as documento, V.movi_prov_num_documento as docid_nro, V.movi_prov_rsocial AS rsocial, IF(V.movi_moneda ='SOLES','S','D') as moneda, V.movi_total AS total, V.movi_descripcion as vdesc, GROUP_CONCAT(DISTINCT VD.deta_descripcion ORDER BY VD.deta_id ASC) AS detalle");
        $this->db->from("movimiento V");
        $this->db->join("maestra_comprobantes COMP","COMP.comp_id = V.movi_comp_id");
        $this->db->join("movimiento_detalle VD","VD.deta_movi_id = V.movi_id");
        $this->db->join("maestra_documentos DOC","DOC.docu_id = V.movi_prov_docu_id");
        $this->db->where("V.movi_fecha BETWEEN '$desde' AND '$hasta'".($tipo != false?" AND V.movi_clase = '$tipo'":"")." ".($search != ""? " AND (V.movi_prov_rsocial LIKE '%$search%' OR V.movi_serie LIKE '%$search%' OR V.movi_numero LIKE '%$search%')":""));
        $this->db->group_by('V.movi_id');
        $this->db->order_by("V.movi_comp_id","ASC");
        $this->db->order_by("V.movi_serie","ASC");
        $this->db->order_by("V.movi_numero","ASC");
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
        
        
        $objPHPExcel->getActiveSheet()->getStyle('I')->applyFromArray($fillgray);


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
                ->setCellValue('J1', 'DETALLE');
        
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
                        ->setCellValue("J$nro", $fila->detalle);
            }
            foreach(range('A','Q') as $nro)
            {
                $objPHPExcel->getActiveSheet()->getColumnDimension($nro)->setAutoSize(true);
            }
        
        $fin = $index+$ini-1;
        $objPHPExcel->getActiveSheet()->getStyle("I$ini:I$fin")->getNumberFormat()->setFormatCode('#,##0.00'); 
        $objPHPExcel->getActiveSheet()->getStyle("A$ini:A$fin")->getNumberFormat()->setFormatCode('dd/mm/yyyy');
        
        $excel->excel_output($objPHPExcel, 'Movimientos'.$desde." - ".$hasta);
    }
    
    function reporte_excel_inventario(){
        $almacen = $this->input->get('sucursal');
        $search = $this->input->get('search');
        
        $this->db->select("DATE_FORMAT(S.stoc_reg_fingreso,'%d/%m/%Y %h:%i %p') AS fingreso, DATE_FORMAT(S.stoc_reg_fsalida,'%d/%m/%Y %h:%i %p') AS fsalida, S.stoc_cantidad as cantidad, SU.sucu_nombre as sucursal, P.prod_nombre as producto, P.prod_codigo as codigo");
        $this->db->from("stock S");
        $this->db->join("sucursal SU","SU.sucu_id = S.stoc_sucu_id");
        $this->db->join("producto P","P.prod_id = S.stoc_prod_id");
        if($almacen != '')
            $this->db->where("SU.sucu_id", $almacen);
        if($search != '')
            $this->db->like("P.prod_nombre", $search);
        $this->db->order_by('SU.sucu_id');
        $this->db->order_by("P.prod_nombre","ASC");
        $productos = $this->db->get()->result();
        //echo $this->db->last_query();
        

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
        
        
        $objPHPExcel->getActiveSheet()->getStyle('I')->applyFromArray($fillgray);


        $objPHPExcel->getActiveSheet()
                ->setCellValue('A1', 'ALMACEN')
                ->setCellValue('B1', 'PRODUCTO')
                ->setCellValue('C1', 'CODIGO')
                ->setCellValue('D1', 'STOCK')
                ->setCellValue('E1', 'ULTIMO INGRESO')
                ->setCellValue('F1', 'ULTIMA SALIDA');
        /*
        $objPHPExcel->getActiveSheet()->getStyle('F')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
        $objPHPExcel->getActiveSheet()->getStyle('D')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);      
        $objPHPExcel->getActiveSheet()->freezePane('A2');
        */
        $ini = 3;
        $index = 0;
        
        foreach($productos as $fila){
            $nro = $index+$ini;
            $index++;
            $objPHPExcel->getActiveSheet()
                        ->setCellValue("A$nro", $fila->sucursal)
                        ->setCellValue("B$nro", $fila->producto)
                        ->setCellValue("C$nro", $fila->codigo)
                        ->setCellValue("D$nro", $fila->cantidad)
                        ->setCellValue("E$nro", $fila->fingreso)
                        ->setCellValue("F$nro", $fila->fsalida);
            }
            foreach(range('A','Q') as $nro)
            {
                $objPHPExcel->getActiveSheet()->getColumnDimension($nro)->setAutoSize(true);
            }
        
        $fin = $index+$ini-1;
        //$objPHPExcel->getActiveSheet()->getStyle("I$ini:I$fin")->getNumberFormat()->setFormatCode('#,##0.00'); 
        $objPHPExcel->getActiveSheet()->getStyle("A$ini:A$fin")->getNumberFormat()->setFormatCode('dd/mm/yyyy');
        
        $excel->excel_output($objPHPExcel, 'Stock productos'.date('d/m/Y'));
    }
    public function genera_pdf($id=0){
        $this->load->library('numl');
        $movi = $this->Model_general->getMovimientoById($id);
        
        $fecha = date_create($movi->movi_fecha);
        $movi->movi_fecha = date_format($fecha, 'd/m/Y');

        $productos = $this->Model_general->getProductosByMovimiento($id);
        
        $readnumber = $this->numl->NUML(floor($movi->movi_total));
        $nada = explode('.',number_format($movi->movi_total,2, '.', ''));
        $nada = $nada[1];
        $total_textual = strtoupper($readnumber) . ' CON ' . $nada . '/100 ' . (($movi->movi_moneda == "SOLES") ? " SOLES" : "DOLARES AMERICANOS");


        $this->load->library('pdf');
        
        $this->pdf = new Pdf();
        $this->pdf->AddPage();
        $this->pdf->AliasNbPages();
        $this->pdf->SetTitle($movi->movi_serie."-".$movi->movi_numero);

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
        $this->pdf->Cell(75,8,utf8_decode($movi->tipo_nombre),'',1,'C',true);
        $this->pdf->Cell(75,8,utf8_decode($movi->movi_serie." - ".$movi->movi_numero),'',1,'C');
        $this->pdf->RoundedRect(125, 10, 75,25, 1, '1234', 'B');

        $this->pdf->SetLeftMargin(10);
        
        $this->pdf->SetFont('Arial', 'B', 8);

        preg_match_all("/.{1,70}[^ ]*/",$movi->movi_prov_rsocial,$rs);
        $movi->movi_prov_rsocial = implode("\r\n",$rs[0]);
        $asr = array();
        if(preg_match("/\n/",$movi->movi_prov_rsocial)){ ///  para saltos de linea
                $asr = explode("\n",($movi->movi_movi_rsocial));
                $movi->movi_prov_rsocial = $asr[0];
                $hline = 3;
                $this->pdf->Ln(2);
        }

        $this->pdf->Ln(5);
        $this->pdf->Cell(20,5,utf8_decode('Señor(es):'),0,0,'L');
        $this->pdf->SetFont('');
        $this->pdf->Cell(125,5,utf8_decode($movi->movi_prov_rsocial),0,0,'L');
        $this->pdf->SetFont('','B','');
        $this->pdf->Cell(25,5,'Fecha:',0,0,'L');
        $this->pdf->SetFont('');
        $this->pdf->Cell(20,5,$movi->movi_fecha,0,0,'R');

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

        if($movi->movi_prov_docu_id=='0') $movi->docu_nombre = 'Documento';
        if($movi->movi_comp_id=='3') $movi->docu_nombre = 'Doc. identidad';
        $l_doc = 23;
        if($movi->movi_prov_docu_id=='4' || $movi->movi_prov_docu_id=='A') 
            $l_doc = 33;

        preg_match_all("/.{1,70}[^ ]*/",$movi->movi_prov_direccion,$ar);
        $movi->movi_prov_direccion = implode("\r\n",$ar[0]);
        $ds = array();
        if(preg_match("/\n/",$movi->movi_prov_direccion)){ ///  para saltos de linea
                $ds = explode("\n",($movi->movi_prov_direccion));
                $movi->movi_prov_direccion = $ds[0];
                $hline = 3;
                $this->pdf->Ln(2);
        }

        $this->pdf->SetFont('','B','');
        $this->pdf->Cell(20,5,utf8_decode('Dirección:'),0,0,'L');
        $this->pdf->SetFont('');
        $this->pdf->Cell(125,5,utf8_decode(str_replace("–","-",$movi->movi_prov_direccion)),0,0,'L');     
        $this->pdf->SetFont('','B','');
        $this->pdf->Cell(25,5,utf8_decode($movi->docu_nombre).": ",0,0,'L');
        $this->pdf->SetFont('');
        $this->pdf->Cell(20,5,$movi->movi_prov_num_documento,0,0,'R');
        
        if(count($ds)>0){
            $this->pdf->Ln();
            unset($ds[0]);
            foreach($ds as $desc){
                $this->pdf->Cell(20,$hline,'','',0,'C');
                $this->pdf->Cell(135,$hline,utf8_decode($desc),0,0,'L');
                $this->pdf->Ln(1);
            }
        }
        $header = array('CANT.', 'DESCRIPCION', 'P. UNITARIO','PRECIO DE VENTA');
        $w = array(15, 120, 25, 30);
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
            $this->pdf->Cell($w[1],$hline,utf8_decode($det->deta_descripcion),'',0,'L');
            $this->pdf->Cell($w[2],$hline,$det->deta_precio,'',0,'R');
            $this->pdf->Cell($w[3],$hline,$det->deta_importe,'',0,'R');
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
        $this->pdf->Cell(30,5,$movi->movi_valor,0,0,'R');
        $this->pdf->Ln();

        $this->pdf->SetFont('','B','');
        $this->pdf->Cell(140,5,'',0,0,'C');
        $this->pdf->Cell(20,5,'IGV 18%',0,0,'R');
        $this->pdf->SetFont('');
        $this->pdf->Cell(30,5,$movi->movi_igv,0,0,'R');

        $this->pdf->Ln();
        $this->pdf->Cell(190,0.2,'','',1,'R',true);
        $this->pdf->Ln();

        $this->pdf->SetTextColor(0,75,140);
        $this->pdf->SetFontSize(10);
        $this->pdf->SetFont('','B','');
        $this->pdf->Cell(160,10,'TOTAL',0,0,'R');
        $this->pdf->SetFont('');
        $this->pdf->Cell(30,10,($movi->movi_moneda=='DOLARES'?'$ ':'S/ ').$movi->movi_total,0,0,'R');
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

        
        
        $file = $movi->movi_serie."-".$movi->movi_numero;
        $this->pdf->Output("files/REPO/{$file}.pdf",'F');

    }
}