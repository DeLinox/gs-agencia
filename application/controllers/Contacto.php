<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Contacto extends CI_Controller {

    function __construct() {
        parent::__construct();
        
        if(!$this->session->userdata('authorized')){
            redirect(base_url()."login");
        }
        $this->usua_id = $this->session->userdata('authorized');
        $this->configuracion = $this->db->query("SELECT * FROM usuario WHERE usua_id='{$this->usua_id}'")->row();
        $this->permisos  = $this->Model_general->getPermisos($this->usua_id);
        $this->editar = $this->permisos[13]->nivel_acceso;
        $this->editarprov = $this->permisos[8]->nivel_acceso;
        $this->editarhote = $this->permisos[9]->nivel_acceso;
        $this->load->model("Model_general");
    }
    public function clie_listado($value=''){
        $this->load->helper('Funciones');
        $this->load->library('Ssp');
        $this->load->library('Cssjs');

        $json = isset($_GET['json']) ? $_GET['json'] : false;

        $columns = array(
            array('db' => 'clie_id',        'dt' => 'ID',          "field" => 'clie_id'),
            array('db' => 'clie_rsocial',   'dt' => 'RAZON SOCIAL',     "field" => "clie_rsocial"),
			array('db' => 'clie_rcomercial',   'dt' => 'RAZON COMERCIAL',     "field" => "clie_rcomercial"),
            array('db' => 'clie_telefono',  'dt' => 'TELEFONO',    "field" => "clie_telefono"),
            array('db' => 'clie_gerente',   'dt' => 'GERENTE / DUEÑO',"field" => "clie_gerente"),
            //array('db' => 'clie_abrev',     'dt' => 'CODIGO',      "field" => "clie_abrev"),
			array('db' => 'clie_reporte',     'dt' => 'REPORTE',      "field" => "clie_reporte"),
            array('db' => 'tdoc_nombre',    'dt' => 'DOCUMENTO',   "field" => "tdoc_nombre"),
            array('db' => 'clie_doc_nro',   'dt' => 'NUMERO',      "field" => "clie_doc_nro"),
			array('db' => 'clie_email',     'dt' => 'EMAIL',      "field" => "clie_email"),
            array('db' => 'clie_direccion', 'dt' => 'DIRECCION',   "field" => "clie_direccion"),
            array('db' => 'clie_facturacion', 'dt' => 'FACTURACION',   "field" => "clie_facturacion"),
            array('db' => 'clie_liquidacion', 'dt' => 'LIQUIDACION',   "field" => "clie_liquidacion"),
            array('db' => 'clie_tipo',      'dt' => 'TIPO',        "field" => "clie_tipo"),
            array('db' => 'clie_reserv_tipo','dt' => 'RESERVA',    "field" => "clie_reserv_tipo"),
            array('db' => 'clie_id',        'dt' => 'DT_RowId',    "field" => "clie_id"),
            array('db' => $this->editar,     'dt' => 'DT_Permisos',    "field" => $this->editar)
        );

        if ($json) {

            $json = isset($_GET['json']) ? $_GET['json'] : false;

            $table = 'clieente';
            $primaryKey = 'clie_id';

            $sql_details = array(
                'user' => $this->db->username,
                'pass' => $this->db->password,
                'db' => $this->db->database,
                'host' => $this->db->hostname
            );

            $condiciones = array();

            $joinQuery = "FROM cliente LEFT JOIN documento_tipo ON clie_tdoc_id = tdoc_id";
            $where = "";
            /*
            if (!empty($_POST['search']))
                $condiciones[] = "clie_rsocial LIKE '%".$_POST['search']."%' ";
            */
            if (!empty($_POST['documento']))
                $condiciones[] = "clie_tdoc_id='".$_POST['documento']."'";
			if (!empty($_POST['reporte']))
                $condiciones[] = "clie_reporte='".$_POST['reporte']."'";

            if (!empty($_POST['tipo']))
                $condiciones[] = "clie_tipo='".$_POST['tipo']."'";
            if (!empty($_POST['reserva']))
                $condiciones[] = "clie_reserv_tipo='".$_POST['reserva']."'";

            $where = count($condiciones) > 0 ? implode(' AND ', $condiciones) : "";
            echo json_encode(
                    $this->ssp->simple($_POST, $sql_details, $table, $primaryKey, $columns, $joinQuery, $where)
            );
            exit(0);
        }
        $datos["documentos"] = $this->Model_general->getOptions('documento_tipo', array("tdoc_id", "tdoc_nombre"),'* Documento');
        $datos["tipos"] = array_merge(array(''=>'* Tipo'),$this->Model_general->enum_valores('cliente','clie_tipo'));
		$datos["reporte"] = array_merge(array(''=>'* Reporte'),$this->Model_general->enum_valores('cliente','clie_reporte'));
        $datos["reserva"] = array_merge(array(''=>'* Reserva'),$this->Model_general->enum_valores('cliente','clie_reserv_tipo'));
        $datos['columns'] = $columns;

        $this->cssjs->add_js(base_url().'assets/js/Contacto/list_clie.js?v=1.0',false,false);
        $this->load->view('header');
        $this->load->view('menu');
        $this->load->view($this->router->fetch_class().'/'.$this->router->fetch_method(), $datos);
        $this->load->view('footer');
    }
    public function buscar_clie() {
        $responese = new StdClass;
        $search = isset($_GET['q']) ? $_GET["q"] : '';
        $tipo = isset($_GET['t']) ? $_GET['t'] : '';
        $datos = array();
        
        if($tipo == 'buscar'){
            //$doc_num = $_GET['num'];
            //$producto = $this->Model_general->select2("cliente", array("clie_docnum" => $doc_num));
            $producto = $this->db->like("clie_rcomercial", $search)->get("cliente")->result();
        }else{
            //$producto = $this->Model_general->select2("cliente", array("clie_rsocial" => $search, "clie_abrev" => $search));
			if($tipo == 'PRIVADOR'){
				$producto = $this->db->like("CONCAT(clie_rcomercial,clie_abrev)", $search)->get("cliente")->result();
			}else{
				$tipo = ($tipo == 'LOCAL')?'RECEPTIVO':'LOCAL';
				$producto = $this->db->like("CONCAT(clie_rcomercial,clie_abrev)", $search)->where("clie_reserv_tipo <>", $tipo)->get("cliente")->result();
			}
            
        }
        foreach ($producto as $value) {
            $datos[] = array("id" => $value->clie_id, "text" => $value->clie_rcomercial,"rsocial" => $value->clie_rsocial, "direccion" => $value->clie_direccion, "docnum" => $value->clie_doc_nro, "docu" => $value->clie_tdoc_id, "email" => $value->clie_email, "codigo" => $value->clie_abrev, "facturacion" => $value->clie_facturacion);
        }
        
        $responese->total_count = count($producto);
        $responese->incomplete_results = false;
        $responese->items = $datos;
        echo json_encode($responese);
    }
    public function sbuscar_clie() {
        $this->load->helper('Funciones');
        $cliente = new stdClass();
        $cliente->clie_id = 0;
        $cliente->clie_rsocial = "";
        $cliente->clie_abrev = "";
        $cliente->clie_tdoc_id = "";
        $cliente->clie_doc_nro = "";
        $cliente->clie_direccion = "";
        $cliente->clie_tipo = "";
        $cliente->clie_activo = "";
        $cliente->clie_email = "";
        $datos["docu_options"] = $this->Model_general->getOptions('documento_tipo',array('tdoc_id','tdoc_nombre'));
        $datos["tipo"] = $this->Model_general->enum_valores('cliente','clie_tipo');
        $datos['cliente'] = $cliente;
        $this->load->view('Contacto/form_buscar_clie', $datos);
    }
    public function crear_clie($clie_id=0) {
        $this->load->helper('Funciones');
        $cliente = new stdClass();
        if ($clie_id == 0) {
            $cliente->clie_id = 0;
            $cliente->clie_rsocial = "";
			$cliente->clie_rcomercial = "";
            $cliente->clie_abrev = "";
            $cliente->clie_gerente = "";
            $cliente->clie_tdoc_id = "";
            $cliente->clie_doc_nro = "";
            $cliente->clie_tipo = "";
            $cliente->clie_telefono = "";
            $cliente->clie_facturacion = "NO";
            $cliente->clie_liquidacion = "NO";
            $cliente->clie_activo = "";
            $cliente->clie_reserv_tipo = "AMBOS";
            $cliente->clie_direccion = "";
            $cliente->clie_email = "";
			$cliente->clie_reporte = "LOCAL";
        }else{
            $this->db->where('clie_id',$clie_id);
            $this->db->from('cliente');
            $cliente = $this->db->get()->row();
        }
        $datos["docu_options"] = $this->Model_general->getOptions('documento_tipo',array('tdoc_id','tdoc_nombre'));
        $datos["tipo"] = $this->Model_general->enum_valores('cliente','clie_tipo');
		$datos["reporte"] = $this->Model_general->enum_valores('cliente','clie_reporte');
        $datos["reserv_tipo"] = $this->Model_general->enum_valores('cliente','clie_reserv_tipo');
        $datos["facturacion"] = $this->Model_general->enum_valores('cliente','clie_facturacion');
        $datos["liquidacion"] = $this->Model_general->enum_valores('cliente','clie_liquidacion');
        $datos['cliente'] = $cliente;
        $this->load->view('Contacto/form_crear_clie', $datos);
    }
    public function eliminar_clie($clie_id = ''){
        $clie = $this->db->where("clie_id",$clie_id)->get("cliente")->row();

        $paqu_clie = $this->db->select("GROUP_CONCAT(CONCAT(paqu_prefijo,'-',paqu_numero) SEPARATOR '\n') as contiene")->where("paqu_clie_id",$clie_id)->group_by("paqu_clie_id")->get("paquete");
        if($paqu_clie->num_rows() > 0){
            $json["exito"] = false;
            $json["mensaje"] = "No es posible eliminar al contacto por que esta registrado en: \n".$paqu_clie->row()->contiene;
        }else{
            if($this->Model_general->borrar(array("clie_id" => $clie_id), "cliente")){
                $this->Model_general->add_log("ELIMINAR",14,"Eliminación de Contacto: ".$clie->clie_rsocial);
                $json["exito"] = true;
                $json["mensaje"] = "Cliente eliminado con exito";
            }else{
                $json["exito"] = false;
                $json["mensaje"] = "Ocurrio un error, intemtelo mas tarde";
            }
        }
        echo json_encode($json);
    }
    public function verifica_codigo($codigo){
        $consulta = $this->db->where("clie_abrev",$codigo)->get("cliente");
        if($consulta->num_rows() > 0) return true;
        else return false;
    }
    function guardar_clie($clie_id = 0) {

        $this->load->library('Form_validation');
        $this->form_validation->set_rules('rsocial', 'Nombre / Razón social', 'required');
        //$this->form_validation->set_rules('abrev', 'Codigo', 'required');
        if ($this->form_validation->run() == FALSE){        
            $this->Model_general->dieMsg(array('exito'=>false,'mensaje'=>validation_errors()));
        }
        /*
        if($clie_id == '0'){
            if($this->verifica_codigo($this->input->post('abrev'))){
                $this->Model_general->dieMsg(array('exito'=>false,'mensaje'=>"El codigo ya existe"));
            }
        }
        */
        $rsocial = $this->input->post('rsocial');
		$rcomercial = $this->input->post('rcomercial');
        $documento = $this->input->post('documento');
        $docnum = $this->input->post('docnum');
        $tipo = $this->input->post('tipo');
        $reserv_tipo = $this->input->post('reserv_tipo');
        $email = $this->input->post('email');
        $gerente = $this->input->post('gerente');
        $abrev = $this->input->post('abrev');
        $facturacion = $this->input->post('facturacion');
        $liquidacion = $this->input->post('liquidacion');
        $telefono = $this->input->post('telefono');
        $estado = $this->input->post('estado');
        $direccion = $this->input->post('direccion');
		$reporte = $this->input->post('reporte');

        $datos = array("clie_tdoc_id" => $documento,
            "clie_rsocial" => $rsocial,
			"clie_rcomercial" => $rcomercial,
            "clie_abrev" => $abrev,
            "clie_doc_nro" => $docnum,
            "clie_direccion" => $direccion,
            "clie_tipo" => $tipo,
            "clie_telefono" => $telefono,
            "clie_facturacion" => $facturacion,
            "clie_liquidacion" => $liquidacion,
            "clie_gerente" => $gerente,
            "clie_activo" => $estado,
            "clie_email" => $email,
			"clie_reporte" => $reporte,
            "clie_reserv_tipo" => $reserv_tipo
        );
        if ($clie_id!='0') {
            $condicion = array("clie_id" => $clie_id);
            if ($this->Model_general->guardar_edit_registro("cliente", $datos,
            $condicion) == TRUE):
                $this->Model_general->add_log("EDITAR",14,"Edición de Contacto: ".$rsocial);
                $json['exito'] = true;
                $json['mensaje'] = "Se guardo correctamente";
            else:
                $json['exito'] = false;
                $json['mensaje'] = "Error al actualizar los datos";
            endif;
        }
        else {
            if (($meta = $this->Model_general->guardar_registro("cliente", $datos)) == TRUE):
                $this->Model_general->add_log("CREAR",14,"Registro de Contacto: ".$rsocial);
                $json['exito'] = true;
                $json['datos'] = array_merge(array('clie_id'=>$meta['id']),$datos);
                $json['mensaje'] = "Cliente agregado con exito";
            else:
                $json['exito'] = false;
                $json['mensaje'] = "Error al guardar los datos";
            endif;
        }
        echo json_encode($json);
    }
    public function buscar_guia(){
        $responese = new StdClass;
        $search = isset($_GET['q']) ? $_GET["q"] : '';
        $datos = array();
        $guia = $this->Model_general->select2("guia", array("guia_nombres" => $search));    
        foreach ($guia["items"] as $value) {
            $datos[] = array("id" => $value->guia_id, "text" => $value->guia_nombres);
        }
        
        $responese->total_count = $guia["total_count"];
        $responese->incomplete_results = false;
        $responese->items = $datos;
        echo json_encode($responese);
    }
    public function crear_guia($guia_id=0) {
        $this->load->helper('Funciones');
        $guia = new stdClass();
        if ($guia_id == 0) {
            $guia->guia_id = 0;
            $guia->guia_nombres = '';
            $guia->guia_tdoc_id = '';
            $guia->guia_doc_nro = '';
            $guia->guia_telefono = '';
            $guia->guia_direccion = '';
            $guia->guia_email = '';
        }else{
            $this->db->where('guia_id',$guia_id);
            $this->db->from('guia');
            $guia = $this->db->get()->row();
        }
        $datos["docu_options"] = $this->Model_general->getOptions('documento_tipo',array('tdoc_id','tdoc_nombre'));
        $datos['guia'] = $guia;
        $this->load->view('Contacto/form_crear_guia', $datos);
    }
    public function guardar_guia($guia_id = 0){
        $nombres = $this->input->post('nombres');
        $documento = $this->input->post('documento');
        $docnum = $this->input->post('docnum');
        $telefono = $this->input->post('telefono');
        $email = $this->input->post('email');
        $direccion = $this->input->post('direccion');

        $datos = array("guia_nombres" => $nombres,
            "guia_tdoc_id" => $documento,
            "guia_doc_nro" => $docnum,
            "guia_telefono" => $telefono,
            "guia_direccion" => $direccion,
            "guia_email" => $email
        );
        if ($guia_id!='0') {
            $condicion = array("guia_id" => $guia_id);
            if ($this->Model_general->guardar_edit_registro("guia", $datos, $condicion) == TRUE):
                $json['exito'] = true;
            else:
                $json['exito'] = false;
                $json['mensaje'] = "Error al actualizar los datos";
            endif;
        }
        else {
            $cons = $this->db->from('guia')->where('guia_doc_nro', $docnum)->get();
            if($cons->num_rows() > 0){
                $json['exito'] = false;
                $json['mensaje'] = "El guia ya existe";
            }else{
                if (($meta = $this->Model_general->guardar_registro("guia", $datos)) == TRUE):
                    $json['exito'] = true;
                    $json['datos'] = array_merge(array('guia_id'=>$meta['id']),$datos);
                    $json['mensaje'] = "Cliente agregado con exito";
                else:
                    $json['exito'] = false;
                    $json['mensaje'] = "Error al guardar los datos";
                endif;
            }
        }
        echo json_encode($json);
    }
    public function guia_listado($value=''){
        $this->load->helper('Funciones');
        $this->load->library('Ssp');
        $this->load->library('Cssjs');

        $json = isset($_GET['json']) ? $_GET['json'] : false;

        $columns = array(
            array('db' => 'guia_id',        'dt' => 'ID',       "field" => "guia_id"),
            array('db' => 'guia_nombres',   'dt' => 'NOMBRE',   "field" => "guia_nombres"),
            array('db' => 'tdoc_nombre',    'dt' => 'DOCUMENTO',"field" => "tdoc_nombre"),
            array('db' => 'guia_doc_nro',   'dt' => 'NUMERO',   "field" => "guia_doc_nro"),
            array('db' => 'guia_telefono',  'dt' => 'TELEFONO', "field" => "guia_telefono"),
            array('db' => 'guia_direccion', 'dt' => 'DIRECCION',"field" => "guia_direccion"),
            array('db' => 'guia_id',        'dt' => 'DT_RowId', "field" => "guia_id")
        );
        if ($json) {

            $json = isset($_GET['json']) ? $_GET['json'] : false;

            $table = 'guia';
            $primaryKey = 'guia_id';

            $sql_details = array(
                'user' => $this->db->username,
                'pass' => $this->db->password,
                'db' => $this->db->database,
                'host' => $this->db->hostname
            );

            $condiciones = array();

            $joinQuery = "FROM guia LEFT JOIN documento_tipo ON guia_tdoc_id = tdoc_id";
            $where = "";
            /*
            if (!empty($_POST['search']))
                $condiciones[] = "clie_rsocial LIKE '%".$_POST['search']."%' ";
            if (!empty($_POST['tipo']))
                $condiciones[] = "clie_tipo='".$_POST['tipo']."'";
            */
            if (!empty($_POST['documento']))
                $condiciones[] = "guia_tdoc_id='".$_POST['documento']."'";

            $where = count($condiciones) > 0 ? implode(' AND ', $condiciones) : "";
            echo json_encode(
                    $this->ssp->simple($_POST, $sql_details, $table, $primaryKey, $columns, $joinQuery, $where)
            );
            exit(0);
        }
        $datos['columns'] = $columns;

        $this->cssjs->add_js(base_url().'assets/js/Contacto/list_guia.js',false,false);
        $this->load->view('header');
        $this->load->view('menu');
        $this->load->view($this->router->fetch_class().'/'.$this->router->fetch_method(), $datos);
        $this->load->view('footer');
    }
    public function eliminar_guia($guia_id = ''){
        if($this->Model_general->borrar(array("guia_id" => $guia_id), "guia")){
            $json["exito"] = true;
            $json["mensaje"] = "Guia eliminado con exito";
        }else{
            $json["exito"] = false;
            $json["mensaje"] = "Ocurrio un error, intentelo mas tarde";
        }
        json_encode($json);
    }

    public function buscar_hotel(){
        $responese = new StdClass;
        $search = isset($_GET['q']) ? $_GET["q"] : '';
        $datos = array();
        $hotel = $this->Model_general->select2("hotel", array("hote_nombre" => $search), "hote_nombre");
        foreach ($hotel["items"] as $value) {
            $datos[] = array("id" => $value->hote_id, "text" => $value->hote_nombre);
        }
        
        $responese->total_count = $hotel["total_count"];
        $responese->incomplete_results = false;
        $responese->items = $datos;
        echo json_encode($responese);
    }
    public function crear_hotel($hote_id=0) {
        $this->load->helper('Funciones');
        $hotel = new stdClass();
        if ($hote_id == 0) {
            $hotel->hote_id = 0;
            $hotel->hote_nombre = '';
            $hotel->hote_contacto = '';
            $hotel->hote_email = '';
            $hotel->hote_telefono = '';
            $hotel->hote_direccion = '';
        }else{
            $this->db->where('hote_id',$hote_id);
            $this->db->from('hotel');
            $hotel = $this->db->get()->row();
        }
        $datos['hotel'] = $hotel;
        $this->load->view('Contacto/form_crear_hotel', $datos);
    }
    public function guardar_hotel($hote_id = 0){
        $nombre = $this->input->post('nombre');
        $contacto = $this->input->post('contacto');
        $email = $this->input->post('email');
        $telefono = $this->input->post('telefono');
        $direccion = $this->input->post('direccion');

        $datos = array("hote_nombre" => $nombre,
            "hote_contacto" => $contacto,
            "hote_email" => $email,
            "hote_telefono" => $telefono,
            "hote_direccion" => $direccion
        );
        if ($hote_id != '0') {
            $condicion = array("hote_id" => $hote_id);
            if ($this->Model_general->guardar_edit_registro("hotel", $datos, $condicion) == TRUE):
                $this->Model_general->add_log("EDITAR",10,"Edición de hotel: ".$nombre);
                $json['exito'] = true;
                $json['mensaje'] = "Hotel editado con exito";
            else:
                $json['exito'] = false;
                $json['mensaje'] = "Error al actualizar los datos";
            endif;
        }
        else {
            if (($meta = $this->Model_general->guardar_registro("hotel", $datos)) == TRUE):
                $this->Model_general->add_log("CREAR",10,"Registro de hotel: ".$nombre);
                $json['exito'] = true;
                $json['datos'] = array_merge(array('hote_id'=>$meta['id']),$datos);
                $json['mensaje'] = "Hotel agregado con exito";
            else:
                $json['exito'] = false;
                $json['mensaje'] = "Error al guardar los datos";
            endif;
        }
        echo json_encode($json);
    }
    public function prov_listado($value=''){
        $this->load->helper('Funciones');
        $this->load->library('Ssp');
        $this->load->library('Cssjs');

        $json = isset($_GET['json']) ? $_GET['json'] : false;
        $tipo = "(SELECT GROUP_CONCAT(tipo_denom) FROM provserv JOIN proveedor_tipo ON tipo_id = pserv_id where pprov_id = prov_id)";
        $columns = array(
            array('db' => 'prov_id',        'dt' => 'ID',           "field" => "prov_id"),
            array('db' => 'emp_rsocial',    'dt' => 'PROVEEDOR',    "field" => "emp_rsocial"),
            array('db' => 'prov_rsocial',   'dt' => 'CONTACTO',     "field" => "prov_rsocial"),
            array('db' => $tipo,            'dt' => 'SERVICIO',     "field" => $tipo),
            array('db' => 'prov_telefono',       'dt' => 'TELEFONO',    "field" => "prov_telefono"),
            array('db' => 'prov_email',     'dt' => 'EMAIL',        "field" => "prov_email"),
            array('db' => 'emp_tipo',       'dt' => 'TIPO',         "field" => "emp_tipo"),
            array('db' => 'prov_id',        'dt' => 'DT_RowId',     "field" => "prov_id"),
            array('db' => $this->editarprov,'dt' => 'DT_Permisos',   "field" => $this->editarprov)
        );

        if ($json) {

            $json = isset($_GET['json']) ? $_GET['json'] : false;

            $table = 'proveedor';
            $primaryKey = 'prov_id';

            $sql_details = array(
                'user' => $this->db->username,
                'pass' => $this->db->password,
                'db' => $this->db->database,
                'host' => $this->db->hostname
            );

            $condiciones = array();

            $joinQuery = "FROM proveedor LEFT JOIN proveedor_empresa ON emp_id = prov_emp_id";
            $where = "";
            if (!empty($_POST['tipoc']))
                $condiciones[] = "emp_tipo='".$_POST['tipoc']."'";        
            if (!empty($_POST['documento']))
                $condiciones[] = "emp_tdoc_id='".$_POST['documento']."'";
            $where = count($condiciones) > 0 ? implode(' AND ', $condiciones) : "";
            echo json_encode(
                    $this->ssp->simple($_POST, $sql_details, $table, $primaryKey, $columns, $joinQuery, $where)
            );
            exit(0);
        }
        $datos["documentos"] = $this->Model_general->getOptions('documento_tipo', array("tdoc_id", "tdoc_nombre"),'* Documento');
        $datos["tipo"] = $this->Model_general->getOptions('proveedor_tipo', array("tipo_id", "tipo_denom"),'* Tipo');
        $datos["tipoc"] = array_merge(array(''=>'* Tipo'),$this->Model_general->enum_valores('proveedor','prov_tipo'));
        $datos['columns'] = $columns;

        $this->cssjs->add_js(base_url().'assets/js/Contacto/list_prov.js?v=1.0',false,false);
        $this->load->view('header');
        $this->load->view('menu');
        $this->load->view($this->router->fetch_class().'/'.$this->router->fetch_method(), $datos);
        $this->load->view('footer');
    }
    public function prov_EmpresaListado($value=''){
        $this->load->helper('Funciones');
        $this->load->library('Ssp');
        $this->load->library('Cssjs');

        $json = isset($_GET['json']) ? $_GET['json'] : false;
        $columns = array(
            array('db' => 'emp_id',             'dt' => 'ID',          "field" => "emp_id"),
            array('db' => 'emp_rsocial',        'dt' => 'NOMBRE / RAZON SOCIAL',"field" => "emp_rsocial"),
            array('db' => 'tdoc_nombre',        'dt' => 'DOCUMENTO',    "field" => "tdoc_nombre"),
            array('db' => 'emp_tdoc_nro',       'dt' => 'NUMERO DOC',   "field" => 'emp_tdoc_nro'),
            array('db' => 'emp_telefono',       'dt' => 'TELEFONO',     "field" => "emp_telefono"),
            array('db' => 'emp_direccion',      'dt' => 'DIRECCION',    "field" => "emp_direccion"),
            array('db' => 'emp_id',             'dt' => 'DT_RowId',     "field" => "emp_id"),
            array('db' => $this->editarprov,    'dt' => 'DT_Permisos',  "field" => $this->editarprov)
        );

        if ($json) {

            $json = isset($_GET['json']) ? $_GET['json'] : false;

            $table = 'proveedor_empresa';
            $primaryKey = 'emp_id';

            $sql_details = array(
                'user' => $this->db->username,
                'pass' => $this->db->password,
                'db' => $this->db->database,
                'host' => $this->db->hostname
            );

            $condiciones = array();

            $joinQuery = "FROM proveedor_empresa LEFT JOIN documento_tipo ON tdoc_id = emp_tdoc_id";
            $where = "";
            /*
            if (!empty($_POST['search']))
                $condiciones[] = "clie_rsocial LIKE '%".$_POST['search']."%' ";
            */
            if (!empty($_POST['tipoc']))
                $condiciones[] = "emp_tipo='".$_POST['tipoc']."'";        
            if (!empty($_POST['documento']))
                $condiciones[] = "emp_tdoc_id='".$_POST['documento']."'";
            $where = count($condiciones) > 0 ? implode(' AND ', $condiciones) : "";
            echo json_encode(
                    $this->ssp->simple($_POST, $sql_details, $table, $primaryKey, $columns, $joinQuery, $where)
            );
            exit(0);
        }
        $datos["documentos"] = $this->Model_general->getOptions('documento_tipo', array("tdoc_id", "tdoc_nombre"),'* Documento');
        $datos["tipo"] = $this->Model_general->getOptions('proveedor_tipo', array("tipo_id", "tipo_denom"),'* Tipo');
        $datos["tipoc"] = array_merge(array(''=>'* Tipo'),$this->Model_general->enum_valores('proveedor','prov_tipo'));
        $datos['columns'] = $columns;

        $this->cssjs->add_js(base_url().'assets/js/Contacto/list_provEmp.js?v=1.0',false,false);
        $this->load->view('header');
        $this->load->view('menu');
        $this->load->view($this->router->fetch_class().'/'.$this->router->fetch_method(), $datos);
        $this->load->view('footer');
    }
    public function buscar_prov($prov_id=0) {
        $this->load->helper('Funciones');
        $proveedor = new stdClass();
        $proveedor->prov_id = 0;
        $proveedor->prov_rsocial = "";
        $proveedor->prov_contacto = "";
        $proveedor->prov_tdoc_id = "";
        $proveedor->prov_doc_nro = "";
        $proveedor->prov_direccion = "";
        $proveedor->prov_activo = "";
            $proveedor->prov_email = "";
        $datos["docu_options"] = $this->Model_general->getOptions('documento_tipo',array('tdoc_id','tdoc_nombre'));
        $datos['proveedor'] = $proveedor;
        $this->load->view('Contacto/form_buscar_prov', $datos);
    }
    public function sbuscar_prov($value=''){
        $responese = new StdClass;
        $search = isset($_GET['q']) ? $_GET["q"] : '';
        $num = isset($_GET['num']) ? $_GET["num"] : '';
        $serv = isset($_GET['s']) ? $_GET["s"] : '';
        $datos = array();
        if($search == 'num') 
            $this->db->where("prov_doc_nro",$num);
        else 
            $this->db->like('prov_rsocial', $search);
        if($serv != ''){
            //$this->db->where("prov_tipo",$serv);
            $this->db->join("provserv","pprov_id = prov_id AND pserv_id = '".$serv."'");
        }
        $proveedor = $this->db->get('proveedor')->result();
        foreach ($proveedor as $value) {
            $datos[] = array("id" => $value->prov_id, "text" => $value->prov_rsocial, "contacto" => $value->prov_contacto, "doc" => $value->prov_tdoc_id, "docnum" => $value->prov_doc_nro, "direccion" => $value->prov_direccion, "email" => $value->prov_email);
        }
        
        $responese->total_count = count($proveedor);
        $responese->incomplete_results = false;
        $responese->items = $datos;
        echo json_encode($responese);
    }
    public function crear_prov($prov_id=0) {
        $this->load->helper('Funciones');
        $proveedor = new stdClass();
        if ($prov_id == 0) {
            $proveedor->prov_id = 0;
            $proveedor->prov_rsocial = "";
            $proveedor->prov_activo = "";
            $proveedor->prov_email = "";
            $proveedor->prov_telefono = "";
            $proveedor->prov_emp_id = "";
            $proveedor->prov_combustible = "NO";
            $proveedor->servicios = array();
        }else{
            $this->db->where('prov_id',$prov_id);
            $this->db->from('proveedor');
            $proveedor = $this->db->get()->row();
            $servicios = $this->db->where("pprov_id",$proveedor->prov_id)->get("provserv")->result();
            $ids_serv = array();
            if(count($servicios) > 0){
                foreach ($servicios as $serv) {
                    $ids_serv[] = $serv->pserv_id;
                }
            }
            $proveedor->servicios = $ids_serv;
        }
        $datos["docu_options"] = $this->Model_general->getOptions('documento_tipo',array('tdoc_id','tdoc_nombre'));
        $datos["empresas"] = $this->Model_general->getOptions('proveedor_empresa',array('emp_id','emp_rsocial'));
        $datos["tipos"] = $this->Model_general->getData("proveedor_tipo", array('tipo_id','tipo_denom'));
        $datos["tipo_serv"] = $this->Model_general->enum_valores('proveedor','prov_tipo');
        $datos['proveedor'] = $proveedor;
        
        $this->load->view('Contacto/form_crear_prov', $datos);
    }
    public function crear_provEmp($emp_id=0) {
        $this->load->helper('Funciones');
        $empresa = new stdClass();
        if ($emp_id == 0) {
            $empresa->emp_id = 0;
            $empresa->emp_rsocial = "";
            $empresa->emp_tdoc_id = "";
            $empresa->emp_tdoc_nro = "";
            $empresa->emp_telefono = "";
            $empresa->emp_direccion = "";
            $empresa->emp_tipo = "";
        }else{
            $this->db->where('emp_id',$emp_id);
            $this->db->from('proveedor_empresa');
            $empresa = $this->db->get()->row();
        }
        $datos["docu_options"] = $this->Model_general->getOptions('documento_tipo',array('tdoc_id','tdoc_nombre'));
        $datos["tipo_serv"] = $this->Model_general->enum_valores('proveedor_empresa','emp_tipo');
        $datos['empresa'] = $empresa;
        
        $this->load->view('Contacto/form_crear_provEmp', $datos);
    }
    function guardar_prov($prov_id = 0) {
        $rsocial = $this->input->post('rsocial');
        /*$contacto = $this->input->post('contacto');
        $documento = $this->input->post('documento');
        $docnum = $this->input->post('docnum');
        */
        $telefono = $this->input->post('telefono');
        $email = $this->input->post('email');
        $estado = $this->input->post('estado');
        $tipo = $this->input->post('tipo');
		$empresa = $this->input->post('empresa');
		$combustible = $this->input->post('combustible');
        /*
        $direccion = $this->input->post('direccion');
        $tipo_prov = $this->input->post('tipo_prov');
        */
        
        $datos = array("prov_rsocial" => $rsocial,
            "prov_activo" => $estado,
            "prov_email" => $email,
            "prov_telefono" => $telefono,
			"prov_emp_id" => $empresa,
			"prov_combustible" => $combustible
        );

        if ($prov_id!='0') {
            $condicion = array("prov_id" => $prov_id);
            if ($this->Model_general->guardar_edit_registro("proveedor", $datos, $condicion) == TRUE):
                $this->asignarServicios($prov_id, $tipo);
                $this->Model_general->add_log("EDITAR",9,"Edición de contacto de proveeedor: ".$rsocial);
                $json['exito'] = true;
                $json['mensaje'] = "Datos guardados con exito";
            else:
                $json['exito'] = false;
                $json['mensaje'] = "Error al actualizar los datos";
            endif;
        } else {
            if (($meta = $this->Model_general->guardar_registro("proveedor", $datos)) == TRUE):
                $this->asignarServicios($meta['id'], $tipo);
                $this->Model_general->add_log("CREAR",9,"Registro de contacto de proveeedor: ".$rsocial);
                $json['exito'] = true;
                $json['datos'] = array_merge(array('prov_id'=>$meta['id']),$datos);
                $json['mensaje'] = "Proveedor agregado con exito";
            else:
                $json['exito'] = false;
                $json['mensaje'] = "Error al guardar los datos";
            endif;
        }
        echo json_encode($json);
    }
    function guardar_provEmp($emp_id = 0) {
        $rsocial = $this->input->post('rsocial');
        $documento = $this->input->post('documento');
        $docnum = $this->input->post('docnum');
        $direccion = $this->input->post('direccion');
        $telefono = $this->input->post('telefono');
        $tipo_prov = $this->input->post('tipo_prov');
        
        $datos = array("emp_tdoc_id" => $documento,
            "emp_rsocial" => $rsocial,
            "emp_tdoc_nro" => $docnum,
            "emp_direccion" => $direccion,
            "emp_telefono" => $telefono,
            "emp_tipo" => $tipo_prov
        );

        if ($emp_id!='0') {
            $condicion = array("emp_id" => $emp_id);
            if ($this->Model_general->guardar_edit_registro("proveedor_empresa", $datos, $condicion) == TRUE):
                $this->Model_general->add_log("EDITAR",9,"Edición de proveeedor: ".$rsocial);
                $json['exito'] = true;
                $json['mensaje'] = "Datos guardados con exito";
            else:
                $json['exito'] = false;
                $json['mensaje'] = "Error al actualizar los datos";
            endif;
        } else {
            $cons = $this->db->from('proveedor_empresa')->where('emp_tdoc_nro', $docnum)->get();
            if($cons->num_rows() > 0 && $docnum != ""){
                $json['exito'] = false;
                $json['mensaje'] = "El numero de documento del proveedor ya existe";
            }else{
                if (($meta = $this->Model_general->guardar_registro("proveedor_empresa", $datos)) == TRUE):
                    $this->Model_general->add_log("CREAR",9,"Registro de proveeedor: ".$rsocial);
                    $json['exito'] = true;
                    $json['datos'] = array_merge(array('prov_id'=>$meta['id']),$datos);
                    $json['mensaje'] = "Proveedor agregado con exito";
                else:
                    $json['exito'] = false;
                    $json['mensaje'] = "Error al guardar los datos";
                endif;
            }
        }
        echo json_encode($json);
    }
    public function asignarServicios($prov_id='', $servicios){
        $this->db->where('pprov_id',$prov_id);
        $this->db->where_not_in('pserv_id',$servicios);
        $this->db->delete('provserv');
        if(isset($servicios)){
            foreach ($servicios as $i => $serv_id) {
                $data = array("pprov_id" => $prov_id, "pserv_id" => $serv_id);
                $consulta = $this->db->where($data)->get("provserv");
                if($consulta->num_rows() < 1)
                    $this->Model_general->guardar_registro("provserv", $data);
            }
        }
    }
    public function eliminar_prov($prov_id = ''){
        
        if($this->Model_general->borrar(array("prov_id" => $prov_id), "proveedor")){
            $json["exito"] = true;
            $json["mensaje"] = "Proveedor eliminado con exito";
        }else{
            $json["exito"] = false;
            $json["mensaje"] = "Ocurrio un error, intemtelo mas tarde";
        }
        json_encode($json);
    }
    public function hote_listado($value=''){
        $this->load->helper('Funciones');
        $this->load->library('Ssp');
        $this->load->library('Cssjs');

        $json = isset($_GET['json']) ? $_GET['json'] : false;

        $columns = array(
            array('db' => 'hote_id',        'dt' => 'ID',       "field" => "hote_id"),
            array('db' => 'hote_nombre',    'dt' => 'PROVEEDOR',"field" => "hote_nombre"),
            array('db' => 'hote_contacto',  'dt' => 'CONTACTO', "field" => "hote_contacto"),
            array('db' => 'hote_telefono',  'dt' => 'TELEFONO',"field" => "hote_telefono"),
            array('db' => 'hote_direccion', 'dt' => 'DIRECCION',   "field" => "hote_direccion"),
            array('db' => 'hote_id',        'dt' => 'DT_RowId', "field" => "hote_id"),
            array('db' => $this->editarhote,'dt' => 'DT_Permisos', "field" => $this->editarhote)
        );

        if ($json) {

            $json = isset($_GET['json']) ? $_GET['json'] : false;

            $table = 'hotel';
            $primaryKey = 'hote_id';

            $sql_details = array(
                'user' => $this->db->username,
                'pass' => $this->db->password,
                'db' => $this->db->database,
                'host' => $this->db->hostname
            );

            $condiciones = array();

            $joinQuery = "FROM hotel";
            $where = "";
            /*
            if (!empty($_POST['search']))
                $condiciones[] = "clie_rsocial LIKE '%".$_POST['search']."%' ";
            if (!empty($_POST['documento']))
                $condiciones[] = "prov_tdoc_id='".$_POST['documento']."'";
            if (!empty($_POST['tipo']))
                $condiciones[] = "clie_tipo='".$_POST['tipo']."'";
            */
            $where = count($condiciones) > 0 ? implode(' AND ', $condiciones) : "";
            echo json_encode(
                    $this->ssp->simple($_POST, $sql_details, $table, $primaryKey, $columns, $joinQuery, $where)
            );
            exit(0);
        }
        $datos['columns'] = $columns;

        $this->cssjs->add_js(base_url().'assets/js/Contacto/list_hote.js?v=1.0',false,false);
        $this->load->view('header');
        $this->load->view('menu');
        $this->load->view($this->router->fetch_class().'/'.$this->router->fetch_method(), $datos);
        $this->load->view('footer');
    }
    public function crear_hote($hote_id=0) {
        $this->load->helper('Funciones');
        $hotel = new stdClass();
        if ($hote_id == 0) {
            $hotel->hote_id = 0;
            $hotel->hote_nombre = "";
            $hotel->hote_contacto = "";
            $hotel->hote_email = "";
            $hotel->hote_telefono = "";
            $hotel->hote_direccion = "";
        }else{
            $this->db->where('hote_id',$hote_id);
            $this->db->from('hotel');
            $hotel = $this->db->get()->row();
        }
        $datos['hotel'] = $hotel;
        $this->load->view('Contacto/form_crear_hote', $datos);
    }
    function guardar_hote($hote_id = 0) {
        $nombre = $this->input->post('nombre');
        $contacto = $this->input->post('contacto');
        $email = $this->input->post('email');
        $telefono = $this->input->post('telefono');
        $direccion = $this->input->post('direccion');

        $datos = array("hote_nombre" => $nombre,
            "hote_contacto" => $contacto,
            "hote_email" => $email,
            "hote_telefono" => $telefono,
            "hote_direccion" => $direccion,
        );
        if ($hote_id!='0') {
            $condicion = array("hote_id" => $hote_id);
            if ($this->Model_general->guardar_edit_registro("hotel", $datos, $condicion) == TRUE):
                $this->Model_general->add_log("EDITAR",10,"Edición de hotel: ".$nombre);
                $json['exito'] = true;
                $json['mensaje'] = "Hotel editado correctamente";
            else:
                $json['exito'] = false;
                $json['mensaje'] = "Error al actualizar los datos";
            endif;
        }
        else {
            if (($meta = $this->Model_general->guardar_registro("hotel", $datos)) == TRUE):
                $this->Model_general->add_log("CREAR",10,"Registro de hotel: ".$nombre);
                $json['exito'] = true;
                $json['datos'] = array_merge(array('hote_id'=>$meta['id']),$datos);
                $json['mensaje'] = "Hotel agregado con exito";
            else:
                $json['exito'] = false;
                $json['mensaje'] = "Error al guardar los datos";
            endif;
        }
        echo json_encode($json);
    }
    public function eliminar_hote($prov_id = ''){
        
        if($this->Model_general->borrar(array("hote_id" => $prov_id), "hotel")){
            $json["exito"] = true;
            $json["mensaje"] = "Hotel eliminado con exito";
        }else{
            $json["exito"] = false;
            $json["mensaje"] = "Ocurrio un error, intentelo mas tarde";
        }
        json_encode($json);
    }
    public function buscar_cont() {
        $responese = new StdClass;
        $search = isset($_GET['q']) ? $_GET["q"] : '';
        $cliente = isset($_GET['c']) ? $_GET["c"] : '';
        $datos = array();
        
        if($search == 'num'){
            $doc_num = $_GET['num'];
            $contacto = $this->Model_general->select2("contacto", array("cont_tdoc_nro" => $doc_num, "cont_clie_id" => $cliente));
        }else{
            $contacto = $this->Model_general->select2("contacto", array("cont_nombres" => $search, "cont_clie_id" => $cliente));    
        }
        foreach ($contacto["items"] as $value) {
            $datos[] = array("id" => $value->cont_id, "text" => $value->cont_nombres, "docnum" => $value->cont_tdoc_nro, "docu" => $value->cont_tdoc_id, "telefono" => $value->cont_telefono);
        }
        
        $responese->total_count = $contacto["total_count"];
        $responese->incomplete_results = false;
        $responese->items = $datos;
        echo json_encode($responese);
    }    
	public function reporte_excelContactos(){
        $documento = $this->input->post("documento");
        $tipo = $this->input->post("tipo");
        $reserva = $this->input->post("reserva");
        $search = $this->input->post("search")["value"];

        $this->db->select("clie_rsocial rsocial, clie_rsocial rcomercial, clie_telefono telefono, clie_gerente gerente, clie_abrev codigo, tdoc_nombre documento, clie_email email, clie_direccion direccion, clie_tipo tipo, clie_reserv_tipo reserva, clie_doc_nro numero");
        $this->db->from("cliente");
        $this->db->join("documento_tipo","clie_tdoc_id = tdoc_id","LEFT");
        if($documento != "")
            $this->db->where("clie_tdoc_id",$documento);
        if($tipo != "")
            $this->db->where("clie_tipo",$tipo);
        if($reserva != "")
            $this->db->where("clie_reserv_tipo",$reserva);
        if($search != ""){
            $this->db->like("clie_rsocial",$searc);
            $this->db->like("clie_rcomercial",$searc);
        }
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

        $objPHPExcel->getActiveSheet()
                ->setCellValue('A1', 'RAZON SOCIAL')
                ->setCellValue('B1', 'RAZON COMERCIAL')
                ->setCellValue('C1', 'TELEFONO')
                ->setCellValue('D1', 'GERENTE / DUEÑO')
                ->setCellValue('E1', 'CODIGO')
                ->setCellValue('F1', 'DOCUMENTO')
                ->setCellValue('G1', 'NUMERO')
                ->setCellValue('H1', 'EMAIL')
                ->setCellValue('I1', 'DIRECCION')
                ->setCellValue('J1', 'TIPO')
                ->setCellValue('K1', 'RESERVA');
        
        $objPHPExcel->getActiveSheet()->getStyle('C')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
        $objPHPExcel->getActiveSheet()->freezePane('A2');
        
        $ini = 3;
        $index = 0;
        
        foreach($detalle as $fila){
            $nro = $index+$ini;
            $index++;

            $objPHPExcel->getActiveSheet()
                        ->setCellValue("A$nro", $fila->rsocial)
                        ->setCellValue("B$nro", $fila->rcomercial)
                        ->setCellValue("C$nro", $fila->telefono)
                        ->setCellValue("D$nro", $fila->gerente)
                        ->setCellValue("E$nro", $fila->codigo)
                        ->setCellValue("F$nro", $fila->documento)
                        ->setCellValue("G$nro", $fila->numero)
                        ->setCellValue("H$nro", $fila->email)
                        ->setCellValue("I$nro", $fila->direccion)
                        ->setCellValue("J$nro", $fila->tipo)
                        ->setCellValue("K$nro", $fila->reserva);
        }


        foreach(range('A','K') as $nro)
            $objPHPExcel->getActiveSheet()->getColumnDimension($nro)->setAutoSize(true);
        
        $fin = $index+$ini-1;

        $excel->excel_output($objPHPExcel, 'Contactos');

    }

}

