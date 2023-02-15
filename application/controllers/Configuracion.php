<?php 
/**
* 
*/
class Configuracion extends CI_Controller
{
    var $configuracion;
	function __construct() {
        parent::__construct();
        if(!$this->session->userdata('authorized')){
            redirect(base_url()."login");
        }
        $this->usua_id = $this->session->userdata('authorized');
        $this->configuracion = $this->db->query("SELECT * FROM usuario WHERE usua_id='{$this->usua_id}'")->row();
        $this->permisos  = $this->Model_general->getPermisos($this->usua_id);
        $this->editar = $this->permisos[12]->nivel_acceso;
    }
    function conf_panel(){
        $this->load->helper('Funciones');
        $this->load->database();
        //$this->load->library('Framework');
        $this->load->library('Ssp');
        $this->load->library('Cssjs');
        $datos["titulo"] = "Configuraciones";

        $servicios = $this->Model_general->getServicios();
        $this->cssjs->add_js(base_url().'assets/js/Configuracion/panel.js?v=2.2',false,false);
        $script['js'] = $this->cssjs->generate_js();
        $this->load->view('header', $script);
        $this->load->view('menu');
        $this->load->view($this->router->fetch_class().'/'.$this->router->fetch_method(), $datos);
        $this->load->view('footer');
    }
    public function getServicios(){
        $servicios = $this->Model_general->getServicios();
        $tabla = "";
        foreach ($servicios as $i => $co){
            $tabla .= '<tr>';
                $tabla .= '<td class="ind">'.$co->id.'</td>';
                $tabla .= '<td>'.$co->nombre.'</td>';
                $tabla .= '<td>'.$co->abrev.'</td>';
                $tabla .= '<td>'.$co->hora.'</td>';
                $tabla .= '<td>'.$co->tipo.'</td>';
            $tabla .= '</tr>';
        }
        $data["html"] = $tabla;
        echo json_encode($data);
    }
    public function conf_usuarios($value=''){
        $usuarios = $this->Model_general->getUsuarios();
        $html = "";
        foreach ($usuarios as $key => $usu) {
            if($this->editar > 1)
                $btn = '<a title="Editar Usuario" class="btn btn-success btn-sm aditar" href="'.base_url().'Configuracion/conf_new_usuario/'.$usu->id.'"><i class="glyphicon glyphicon-edit"></i></a> <a title="Editar permisos de usuario" class="btn btn-info btn-sm aditar_permisos" href="'.base_url().'Configuracion/conf_permisos_usuario/'.$usu->id.'"><i class="glyphicon glyphicon-star"></i></a>';
            else $btn = "";
			$habilitado = $usu->habilitado == '1' ? "<span class='text-success'><strong>HABILITADO</strong></span>" : "<span class='text-danger'><strong>BLOQUEADO</strong></span>";
            $html .= "<tr>";
            $html .= '<td>'.$btn.'</td>';
            $html .= "<td>".$usu->nombres."</td>";
            $html .= "<td>".$usu->cel."</td>";
            $html .= "<td>".$usu->email."</td>";
            $html .= "<td>".$usu->user."</td>";
            $html .= "<td>".($usu->tipo=='1'?"LOCAL":"RECEPTIVO")."</td>";
			$html .= "<td>".$habilitado."</td>";
            /*
            $html .= "<td>".($usu->adm=='1'?"<i style='color:#008000' class='glyphicon glyphicon-ok'></i>":"<i style='color:#FF0000' class='glyphicon glyphicon-remove'></i>")."</td>";
            $html .= "<td>".($usu->res=='1'?"<i style='color:#008000' class='glyphicon glyphicon-ok'></i>":"<i style='color:#FF0000' class='glyphicon glyphicon-remove'></i>")."</td>";
            $html .= "<td>".($usu->ven=='1'?"<i style='color:#008000' class='glyphicon glyphicon-ok'></i>":"<i style='color:#FF0000' class='glyphicon glyphicon-remove'></i>")."</td>";
            $html .= "<td>".($usu->pag=='1'?"<i style='color:#008000' class='glyphicon glyphicon-ok'></i>":"<i style='color:#FF0000' class='glyphicon glyphicon-remove'></i>")."</td>";
            */
            $html .= "</tr>";
        }
        $data["html"] = $html;
        echo json_encode($data);
    }
    public function conf_new_usuario($usuario=''){
        if($usuario != ''){
            $usua = $this->Model_general->getUsuarios($usuario);    
        }else{
            $usua = new stdClass();
            $usua->id = '';
            $usua->nombres = '';
            $usua->user = '';
            $usua->email = '';
            $usua->nacimiento = '';
            $usua->cel = '';
            $usua->tipo = '1';
            $usua->habilitado = 'NO';
        }
        $data["usua"] = $usua;
        $data["modulos"] = $this->Model_general->getData("modulo", array("mod_id, mod_nombre"));
        $this->load->view('Usuario/new_usuario',$data);
    }
    
    public function conf_permisos_usuario($usuario=''){
        
        $datos["niveles"] = array("0" => "Ninguno", "1" => "Ver", "2" => "Ver y Editar");
        $datos["permisos"] = $this->Model_general->getPermisos($usuario);
        $datos["id"] = $usuario;
        $this->load->view('Configuracion/permisos_usuario',$datos);
    }

    public function guardar_permisos($usua_id=''){
        $modulo = $this->input->post("modulo");
        $nivel = $this->input->post("nivel");

        $this->db->trans_start();
        foreach ($modulo as $i => $mod) {
            $where = array("usua_id" => $usua_id, "mod_id" => $mod);
            $verif = $this->db->where($where)->get("modulo_usuario");
            $data = array_merge($where, array("nivel_acceso" => $nivel[$i]));
            if($verif->num_rows() > 0){
                $where = array("usua_id" => $usua_id, "mod_id" => $mod);
                $this->Model_general->guardar_edit_registro("modulo_usuario", $data, $where);
            }else{
                $this->Model_general->guardar_registro("modulo_usuario", $data);
            }
        }
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE){
            $json['exito'] = false;
            $json['mensaje'] = "Error al guardar los datos";
        }else{
            $json['exito'] = true;  
            $json['mensaje'] = "Permisos guardados correctamente";
        }
        echo json_encode($json);
    }

    public function conf_new_tipoProv($tipo=''){
        if($tipo != ''){
            $tipo = $this->db->select("tipo_id as id, tipo_denom as denom")->where("tipo_id", $tipo)->get("proveedor_tipo")->row();
        }else{
            $tipo = new stdClass();
            $tipo->id = '';
            $tipo->denom = '';
        }
        
        $data["tipo"] = $tipo;
        $this->load->view('Configuracion/form_crear_tipoProv',$data);

    }
    public function conf_delete_tipoProv($id=''){
        $tipo = $this->db->where("tipo_id",$id)->get("proveedor_tipo")->row();
        $has = $this->db->where("pserv_id",$id)->get("provserv");
        if($has->num_rows() > 1){
            $json["exito"] = false;
            $json["mensaje"] = "No se puede elimnar por que hay proveedores que tienes este tipo.";
        }else{
            $cons = $this->Model_general->borrar(array("tipo_id" => $id), "proveedor_tipo");
            if($cons){
                $this->Model_general->add_log("ELIMINAR",13,"Eliminación de tipo de proveedor: ".$tipo->tipo_denom);
                $json["exito"] = true;
                $json["mensaje"] = "Eliminado con exito";
            }else{
                $json["exito"] = false;
                $json["mensaje"] = "Ocurrio un error";
            }
        }
        echo json_encode($json);
    }
    public function guardar_tipoProv($id=''){
        
        $this->load->library('Form_validation');
        $this->form_validation->set_rules('tipo_nombre', 'Tipo proveedor', 'required');
        
        if ($this->form_validation->run() == FALSE)
            $this->Model_general->dieMsg(array('exito'=>false,'mensaje'=>validation_errors()));

        $tipo = $this->input->post("tipo_nombre");
        $data = array("tipo_denom" => $tipo);

        if($id != ''){
            $condicion = array('tipo_id' => $id);
            if($this->Model_general->guardar_edit_registro("proveedor_tipo", $data, $condicion)){
                $this->Model_general->add_log("EDITAR",13,"Edición de tipo de proveedor: ".$tipo);
                $json["exito"] = true;
                $json["mensaje"] = "Datos guardados con exito";    
            }else{
                $json["exito"] = false;
                $json["mensaje"] = "Ocurrio un error";    
            }
        }else{
            if($this->Model_general->guardar_registro("proveedor_tipo", $data)){
                $this->Model_general->add_log("CREAR",13,"Registro de tipo de proveedor: ".$tipo);
                $json["exito"] = true;
                $json["mensaje"] = "Datos guardados con exito";    
            }else{
                $json["exito"] = false;
                $json["mensaje"] = "Ocurrio un error";    
            }
        }
        echo json_encode($json);
    }
    public function tipo_proveedor(){
        $tipos = $this->db->get("proveedor_tipo")->result();
        $html = "";
        foreach ($tipos as $key => $tipo) {
            if($this->editar > 1)
                $btn = '<a title="Editar Tipo" class="btn btn-success btn-sm aditar_prov" href="'.base_url().'Configuracion/conf_new_tipoProv/'.$tipo->tipo_id.'"><i class="glyphicon glyphicon-edit"></i></a> <a title="Eliminar" class="btn btn-danger btn-sm eliminar_tipo" href="'.base_url().'Configuracion/conf_delete_tipoProv/'.$tipo->tipo_id.'"><i class="glyphicon glyphicon-trash"></i></a>';
            else $btn = "";
            $html .= "<tr>";
            $html .= "<td>".($key+1)."</td>";
            $html .= "<td>".$tipo->tipo_denom."</td>";
            $html .= '<td>'.$btn.'</td>';
            $html .= "</tr>";
        }
        $data["html"] = $html;
        echo json_encode($data);
    }
    public function tipo_transporte(){
        $tipos = $this->db->get("tipo_transporte")->result();
        $html = "";
        foreach ($tipos as $key => $tipo) {
            if($this->editar > 1)
                $btn = '<a title="Editar Tipo" class="btn btn-success btn-sm aditar_trans" href="'.base_url().'Configuracion/conf_new_tipoTrans/'.$tipo->tipo_id.'"><i class="glyphicon glyphicon-edit"></i></a> <a title="Eliminar" class="btn btn-danger btn-sm eliminar_trans" href="'.base_url().'Configuracion/conf_delete_tipoTrans/'.$tipo->tipo_id.'"><i class="glyphicon glyphicon-trash"></i></a>';
            else $btn = "";
            $html .= "<tr>";
            $html .= "<td>".($key+1)."</td>";
            $html .= "<td>".$tipo->tipo_nombre."</td>";
            $html .= '<td>'.$btn.'</td>';
            $html .= "</tr>";
        }
        $data["html"] = $html;
        echo json_encode($data);
    }
    public function conf_new_tipoTrans($tipo=''){
        if($tipo != ''){
            $tipo = $this->db->select("tipo_id as id, tipo_nombre as denom")->where("tipo_id", $tipo)->get("tipo_transporte")->row();
        }else{
            $tipo = new stdClass();
            $tipo->id = '';
            $tipo->denom = '';
        }
        
        $data["tipo"] = $tipo;
        $this->load->view('Configuracion/form_crear_tipoTrans',$data);

    }
    public function conf_delete_tipoTrans($id=''){

        $has = $this->db->where("deta_emba_id",$id)->get("paquete_detalle");
        if($has->num_rows() > 0){
            $json["exito"] = false;
            $json["mensaje"] = "No es posible eliminar este tipo de transporte por que esta registrado en una o más reservas";
        }else{
            $tipo = $this->db->where("tipo_id",$id)->get("tipo_transporte")->row();
            $cons = $this->Model_general->borrar(array("tipo_id" => $id), "tipo_transporte");
            if($cons){
                $this->Model_general->add_log("ELIMINAR",13,"Eliminación de tipo de transporte: ".$tipo->tipo_nombre);
                $json["exito"] = true;
                $json["mensaje"] = "Eliminado con exito";
            }else{
                $json["exito"] = false;
                $json["mensaje"] = "Ocurrio un error";
            }
        }
        echo json_encode($json);
    }
    public function guardar_tipoTrans($id=''){
        
        $this->load->library('Form_validation');
        $this->form_validation->set_rules('tipo_nombre', 'Tipo transporte', 'required');
        
        if ($this->form_validation->run() == FALSE)
            $this->Model_general->dieMsg(array('exito'=>false,'mensaje'=>validation_errors()));

        $tipo = $this->input->post("tipo_nombre");
        $data = array("tipo_nombre" => $tipo);

        if($id != ''){
            $condicion = array('tipo_id' => $id);
            if($this->Model_general->guardar_edit_registro("tipo_transporte", $data, $condicion)){
                $this->Model_general->add_log("EDITAR",13,"Edición de tipo de transporte: ".$tipo);
                $json["exito"] = true;
                $json["mensaje"] = "Datos guardados con exito";
            }else{
                $json["exito"] = false;
                $json["mensaje"] = "Ocurrio un error";
            }
        }else{
            if($this->Model_general->guardar_registro("tipo_transporte", $data)){
                $this->Model_general->add_log("CREAR",13,"Registro de tipo de transporte: ".$tipo);
                $json["exito"] = true;
                $json["mensaje"] = "Datos guardados con exito";
            }else{
                $json["exito"] = false;
                $json["mensaje"] = "Ocurrio un error";
            }
        }
        echo json_encode($json);
    }
    /***********************************************************************/
    /*
    /*
    /*
    /*                  PERTENECE AL SISTEMA DE GMERCANTES
    /*
    /*
    /*
    /***********************************************************************/
    public function sucursales() {
        $this->load->helper('Funciones');
        $this->load->database();
        $this->load->library('Ssp');
        $this->load->library('Cssjs');

        $json = isset($_GET['json']) ? $_GET['json'] : false;

        $columns = array(
            array('db' => 'sucu_id',        'dt' => 'ID',       "field" => "sucu_id"),
            array('db' => 'sucu_nombre',  	'dt' => 'Nombre',   "field" => "sucu_nombre"),
            array('db' => 'sucu_direccion', 'dt' => 'Fecha',    "field" => "sucu_direccion"),
            array('db' => 'sucu_id',        'dt' => 'DT_RowId', "field" => "sucu_id")
        );

        if ($json) {

            $json = isset($_GET['json']) ? $_GET['json'] : false;

            $table = 'sucursal';
            $primaryKey = 'sucu_id';

            $sql_details = array(
                'user' => $this->db->username,
                'pass' => $this->db->password,
                'db' => $this->db->database,
                'host' => $this->db->hostname
            );

            $condiciones = array();
            $joinQuery = "";
            $where = "";

            $where = count($condiciones) > 0 ? implode(' AND ', $condiciones) : "";
            echo json_encode(
                    $this->ssp->simple($_POST, $sql_details, $table, $primaryKey, $columns, $joinQuery, $where)
            );
            exit(0);
        }
        $datos['columns'] = $columns;
        $datos['titulo'] = 'Sucursales';

        $this->cssjs->set_path_js(base_url() . "assets/js/Configuracion/");
        $this->cssjs->add_js('sucu_list');
        $script['js'] = $this->cssjs->generate_js();
        $this->load->view('header', $script);
        $this->load->view('menu');
        $this->load->view('configuracion/sucu_list', $datos);
        $this->load->view('footer');
    }
    public function series() {
        $this->load->helper('Funciones');
        $this->load->library('Ssp');
        $this->load->library('Cssjs');

        $comprobantes = $this->Model_general->getComprobantes();
        $sucursales = $this->Model_general->getData('sucursal', array('sucu_id','sucu_nombre','sucu_direccion','sucu_ubigeo','sucu_departamento','sucu_provincia','sucu_distrito'));
        $sucu_comp = array();
        foreach ($sucursales as $sucu) {
            $data = array();
            foreach ($comprobantes as $comp) {
                $data['comp_id'] = $comp->comp_id;
                $data['comp_nombre'] = $comp->comp_nombre;

                $this->db->select('suco_serie');
                $this->db->from('sucursal_comprobantes');
                $this->db->where('suco_comp_id', $comp->comp_id);
                $this->db->where('suco_suco_id', $sucu->sucu_id);
                $serie = $this->db->get();
                $serie = $serie->num_rows() > 0 ? $serie->row() : '';
                $data['serie'] = $serie != '' ? $serie->suco_serie : '';
                $sucu_comp[] = array_merge(array('sucu_id' => $sucu->sucu_id, 'sucu_nombre' => $sucu->sucu_nombre), $data);
            }
        }
        $datos['sucu_comp'] = (object)$sucu_comp;
        $datos['sucursales'] = $sucursales;
        $datos['comprobantes'] = count($comprobantes);
        $datos['titulo'] = 'Series';

        $this->cssjs->set_path_js(base_url() . "assets/js/Configuracion/");
        $this->cssjs->add_js('form_series');
        $script['js'] = $this->cssjs->generate_js();
        $this->load->view('header', $script);
        $this->load->view('menu');
        $this->load->view('configuracion/series', $datos);
        $this->load->view('footer');
    }
    public function guardar_series($value=''){
        $series = $this->input->post('serie');
        $sucursales = $this->input->post('sucu_id');
        $comprobantes = $this->input->post('comp_id');

        $this->db->trans_begin();

        for ($i=0; $i < count($series); $i++) { 
            //echo $sucursales[$i]." : ".$comprobantes[$i]." -> ".$series[$i]."<br>";
            
            $consulta = $this->Model_general->getData('sucursal_comprobantes', array('suco_serie'), array('suco_comp_id' => $comprobantes[$i], 'suco_suco_id' => $sucursales[$i]));
            if(count($consulta) > 0){
                if($this->Model_general->guardar_edit_registro('sucursal_comprobantes', array('suco_serie' => $series[$i]), array('suco_comp_id' => $comprobantes[$i], 'suco_suco_id' => $sucursales[$i])) == false){
                    $this->db->trans_rollback();
                    $this->Model_general->dieMsg(array('exito'=>false,'mensaje'=>'Error al guardar los datos'));
                }
            }else{
                if($this->Model_general->guardar_registro('sucursal_comprobantes', array('suco_serie' => $series[$i], 'suco_comp_id' => $comprobantes[$i], 'suco_suco_id' => $sucursales[$i])) == false){
                    $this->db->trans_rollback();
                    $this->Model_general->dieMsg(array('exito'=>false,'mensaje'=>'Error al guardar los datos'));
                }
            }
        }
        $this->db->trans_commit();
        $this->Model_general->dieMsg(array('exito'=>true,'mensaje'=>'Datos guardados con exito'));
    }
    public function crear_sucursal() {
        $this->load->database();
        $this->load->helper('Funciones');
        $this->load->model("Model_general");

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
                         'coti_desc_global' => '0.00',
                         'coti_descripcion' => '',
                         'clie_selected_data'=>'');
        
        $datos["tipo_detalle"] = $this->Model_general->getOptions('maestra_afectacion', array("afec_id", "afec_nombre"));
        $datos["documentos"] = $this->Model_general->getOptions('maestra_documentos', array("docu_id", "docu_nombre"));
        $datos["gratuita_select"] = $this->Model_general->enum_valores('cotizacion_detalle','deta_esgratuita');
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
    public function empresa(){
        //$conf = new stdClass();
        $conf = $this->Model_general->getData('configuracion', array('conf_rsocial, conf_ncomercial, conf_direccion, conf_impr_direccion, conf_impr_telefonos, conf_impr_contactos, conf_impr_web, conf_mail_user, conf_mail_body, conf_mail_subject, conf_mail_password, conf_impr_logo'));
        $datos['conf'] = $conf[0];
        $datos['titulo'] = 'Configurar datos de la empresa';

        $this->load->library('Ssp');
        $this->load->library('Cssjs');
        $this->cssjs->set_path_js(base_url() . "assets/js/Configuracion/");
        $this->cssjs->add_js('form');
        $script['js'] = $this->cssjs->generate_js();
        $this->load->view('header', $script);
        $this->load->view('menu');
        $this->load->view('configuracion/empresa', $datos);
        $this->load->view('footer');   
    }
    public function guardar_empresa(){
        $rsocial = $this->input->post('rsocial');
        $direccion = $this->input->post('direccion');
        $telefonos = $this->input->post('telefonos');
        $contactos = $this->input->post('contactos');
        $web = $this->input->post('web');
        $correo = $this->input->post('correo');
        $password = $this->input->post('password');
        $subject = $this->input->post('subject');
        $body = $this->input->post('cuerpo');

        $datos = array("conf_rsocial" => $rsocial,
            "conf_direccion" => $direccion,
            "conf_impr_telefonos" => $telefonos,
            "conf_impr_contactos" => $contactos,
            "conf_impr_web" => $web,
            "conf_mail_user" => $correo,
            "conf_mail_password" => $password,
            "conf_mail_subject" => $subject,
            "conf_mail_body" => $body
        );

        

        /*
        if($password != '')
            $datos = array_merge($datos, array("conf_mail_password" => $password));
        */
        $condicion = array('conf_id' => 1);
        if ($this->Model_general->guardar_edit_registro("configuracion", $datos, $condicion) == TRUE):
            $json['exito'] = true;
            $json['mensaje'] = "Datos guardados con exito";
        else:
            $json['exito'] = false;
            $json['mensaje'] = "Error al actualizar los datos";
        endif;

        echo json_encode($json);

    }
    public function guardar_logo(){

        $logotipo = 'logotipo';
        $config['upload_path'] = "./assets/img/";
        $config['file_name'] = "logotipo";
        $config['allowed_types'] = "gif|jpg|jpeg|png";
        $this->load->library('upload', $config);
        
        if (!$this->upload->do_upload($logotipo)) {
            //*** ocurrio un error
            $data['uploadError'] = $this->upload->display_errors();
            $json['exito'] = false;
            $json['mensaje'] = $this->upload->display_errors();
        }else{
            $data = array("upload_data" => $this->upload->data());
            $archivo = base_url().'assets/img/'.$data['upload_data']['file_name'];
            $datos = file_get_contents($archivo);
            $condicion = array('conf_id' => 1);
            if ($this->Model_general->guardar_edit_registro("configuracion", array('conf_impr_logo' => $datos), $condicion) == TRUE):
                $json['exito'] = true;
                $json['mensaje'] = "Datos guardados con exito";
            else:
                $json['exito'] = false;
                $json['mensaje'] = "Error al actualizar los datos";
            endif;
        }
        echo json_encode($json);    
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
        $this->form_validation->set_rules('email', 'Correo electronico', 'required');
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
		$id_cliente = $this->input->post('cliente');
        $enviar_email = ($this->input->post('enviar_email') == 1) ? 1 : 2;
        $descripcion = $this->input->post('descripcion');
        

        $nc_comprobante = in_array($this->input->post('comprobante'),array('07','08'))?$this->input->post('nc_comprobante'):null ;
        $nc_serie = in_array($this->input->post('comprobante'),array('07','08'))?$this->input->post('nc_serie'):null ;
        $nc_numero = in_array($this->input->post('comprobante'),array('07','08'))?$this->input->post('nc_numero'):null ;
        $credito_tipo = $this->input->post('comprobante') == '07'?$this->input->post('credito_tipo'):null ;
        $debito_tipo = $this->input->post('comprobante') == '08'?$this->input->post('debito_tipo'):null ;


        /*Variables axiliares*/
        $idsopre = $this->input->post('sopre');
        $json['from'] = $this->input->post('from')=='1'?true:false;
        /**/

        //Verifica si hay conflicto
        /*$exist = $this->db->query("SELECT date_format(coti_fecha,'%d/%m/%Y') coti_fecha,coti_serie,coti_numero FROM cotizacion WHERE coti_serie='{$serie}' AND coti_anulado='NO' AND (coti_numero='{$numero}' OR (coti_numero>'{$numero}' AND coti_fecha<'{$fecha}') OR (coti_numero<'{$numero}' AND coti_fecha>'{$fecha}')) ORDER BY coti_fecha DESC LIMIT 1")->result();
        if(count($exist)>0){
            $text = "";
            foreach($exist as $row){
                $text .= "[{$row->coti_serie}-{$row->coti_numero}-{$row->coti_fecha}] ";
            }
            $this->Model_general->dieMsg(array('exito'=>false,'mensaje'=>'Hay conflictos con documentos: '.$text));
        }*/
        

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
	            				"deta_prod_id" => $prod_id[$i]
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
                                "deta_prod_id" => $prod_id[$i]
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

    public function genera_pdf($id=0,$file=false,$local=false){
        $this->load->library('numl');
        $venta = $this->Model_general->getCotizacionById($id);
        $fecha = date_create($venta->coti_fecha);
        $venta->coti_fecha = date_format($fecha, 'd/m/Y');
        $datos["venta"] = $venta;
        $datos["id_venta"] = $id;
        $productos = $this->Model_general->getProductosByCotizacion($id);
        //$configuracion = $this->db->query("SELECT * from configuracion where conf_id = 1")->row();
        $datos["detalle"] = $productos;
        $readnumber = $this->numl->NUML(floor($venta->coti_total));
        $nada = explode('.',number_format($venta->coti_total,2, '.', ''));
        $nada = $nada[1];
        $total_textual = strtoupper($readnumber) . ' CON ' . $nada . '/100 ' . (($venta->coti_moneda == "SOLES") ? " SOLES" : "DOLARES AMERICANOS");
        $datos["total_textual"] = $total_textual;
        $simb = ($venta->coti_moneda=='DOLARES'?'$ ':'S/. ');

        $this->load->library('pdf');
        
        $this->pdf = new Pdf();
        $this->pdf->AddPage();
        $this->pdf->AliasNbPages();
        $this->pdf->SetTitle($venta->coti_numero);

        $this->pdf->SetFont('Arial', 'B', 7);

        $this->pdf->Image(base_url().'assets/img/logo_empresa.png', 10, 07, 35,0 , 'PNG');
        $this->pdf->SetLeftMargin(40);
        /*$html = "<font face='helvetica' color='#777777'>{$this->configuracion->conf_rsocial}</font><br>";*/
        $html = "<font color='#ff0000' size='18' color='#333366'>        {$this->configuracion->conf_ncomercial}</font><br>";
        $html .= "<font size='10' color='#777777'>      VENTA DE ALIMENTOS BALANCEADOS</font><br>";
        $html .= "<font size='10' color='#777777'>      PARA PECES Y OTROS RELACIONADOS</font><br>";
//        $html .= "<font size='9' color='#777777'>      {$this->configuracion->conf_impr_direccion}</font>";
                $html .= "<font size='9' color='#777777'>           Av. 29 de Junio Mz. 13 Lote 2 - Barrio Villa</font><br>";
                $html .= "<font size='9' color='#777777'>             El Salvador Pomata - Chucuito - Puno</font><br>";
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
        $this->pdf->Cell(75,8,utf8_decode("COTIZACIÓN  ").utf8_decode($venta->coti_serie."-".$venta->coti_numero),'',1,'C',true);
        $this->pdf->Cell(75,8,'15 de Noviembre del 2017','',1,'C');
        $this->pdf->RoundedRect(125, 10, 75,25, 1, '1234', 'B');

        $this->pdf->SetLeftMargin(10);
        
        $this->pdf->SetFont('Arial', 'B', 8);
        $this->pdf->Ln(5);
        $this->pdf->Cell(20,5,utf8_decode('Señor(es):'),0,0,'L');
        $this->pdf->Ln(5);
        $this->pdf->Cell(110,5,utf8_decode($venta->coti_clie_rsocial),0,0,'L');
        $this->pdf->Ln();
        $this->pdf->Cell(110,5,utf8_decode(str_replace("–","-",$venta->coti_clie_direccion)),0,0,'L');
        $this->pdf->Ln();
        $this->pdf->Cell(30,5,utf8_decode($venta->docu_nombre).': '.$venta->coti_clie_num_documento,0,0,'L');

        $header = array('CANT.', 'UND.', 'DESCRIPCION', 'P. UNITARIO', 'IMPORTE');
        $w = array(15, 15, 105, 25, 30);
        $this->pdf->Ln(8);
        $this->pdf->SetFont('','B','');
        $this->pdf->SetFillColor('200','200','200'); 
        for($i = 0; $i < count($header); $i++)
            $this->pdf->Cell($w[$i],5,$header[$i],1,0,'C',true);
        $this->pdf->Ln();
        $this->pdf->SetFont('');

        $indice = 0;
 //print_r($productos);
        if(!empty($venta->coti_descripcion)){
            $tmp_producto[] = (object)array(
                'deta_descripcion'=>$venta->coti_descripcion,
                'deta_cantidad'=>'',
                'deta_descuento'=>'',
                'deta_precio'=>'0',
                'deta_importe'=>'',
                'deta_unidad'=>''
                );
            $productos = array_merge((array) $productos, $tmp_producto);
        }

        $lineas = 0;
        foreach ($productos as $num => $det) {
            $numero = 0;


            preg_match_all("/.{1,50}[^ ]*/",$det->deta_descripcion,$arra);
            $det->deta_descripcion = implode("\r\n",$arra[0]);


            $hline = 7;
            $dess = array();
            if(preg_match("/\n/",$det->deta_descripcion)){ ///  para saltos de linea
                $dess = explode("\n",utf8_decode($det->deta_descripcion));
                $det->deta_descripcion = $dess[0];
                //$hline = 5;
                //$this->pdf->Ln(2);
            }

            $det->deta_cantidad = empty($det->deta_cantidad)?'':ROUND($det->deta_cantidad);
            $det->deta_precio = empty($det->deta_precio)?'':number_format($det->deta_precio,2,'.','');

            $this->pdf->Cell($w[0],$hline,$det->deta_cantidad,'L',0,'C');
            $this->pdf->Cell($w[1],$hline,$det->deta_unidad,'L',0,'C');
            $this->pdf->Cell($w[2],$hline,$det->deta_descripcion,'L',0,'L');
            $this->pdf->Cell($w[3],$hline,$simb.$det->deta_precio,'L',0,'R');
            $this->pdf->Cell($w[4],$hline,$simb.$det->deta_importe,'LR',0,'R');
            $this->pdf->Ln();
            $lineas++;
            if(count($dess)>0){
                unset($dess[0]);
                foreach($dess as $ind=>$desc){
                    $hline = 7;
                    $this->pdf->Cell($w[0],$hline,'','L',0,'C');
                    $this->pdf->Cell($w[1],$hline,'','L',0,'C');
                    $this->pdf->Cell($w[2],$hline,$desc,'L',0,'L');
                    $this->pdf->Cell($w[3],$hline,'','L',0,'L');
                    $this->pdf->Cell($w[4],$hline,'','LR',0,'L');
                    $this->pdf->Ln();
                    $lineas++;
                    
                }
                //$this->pdf->Ln(1);
            }
            $indice++;
        }

        for($i=$lineas;$i<=24;$i++){
            $this->pdf->Cell($w[0],$hline,'','L',0,'C');
            $this->pdf->Cell($w[1],$hline,'','L',0,'C');
            $this->pdf->Cell($w[2],$hline,'','L',0,'L');
            $this->pdf->Cell($w[3],$hline,'','L',0,'R');
            $this->pdf->Cell($w[4],$hline,'','LR',0,'R');
            $this->pdf->Ln();
        }


        $this->pdf->Cell(140,5,'SON: '.utf8_decode($total_textual),'T',0,'L');
        $this->pdf->Cell(20,5,'Subtotal',1,0,'R');
        $this->pdf->Cell(30,5,$simb.$venta->coti_valor,1,0,'R');
        $this->pdf->Ln(5);
        $this->pdf->Cell(140,5,'',0,0,'R');
        $this->pdf->Cell(20,5,'IGV 18%',1,0,'R');
        $this->pdf->Cell(30,5,$simb.$venta->coti_igv,1,0,'R');
        $this->pdf->Ln(5);
        $this->pdf->Cell(140,5,'',0,0,'R');
        $this->pdf->Cell(20,5,'Total',1,0,'R');
        $this->pdf->Cell(30,5,$simb. $venta->coti_total,1,0,'R');
$this->pdf->Ln(15);
        


        $this->pdf->Cell(190,5,utf8_decode('Para mas información www.gruposistemas.com'),'',1,'C');
        
        $archivo = "{$venta->coti_numero}.pdf";
        if($file==false){
            $doc = $this->pdf->Output($archivo,'S');
            return $doc;        
        }else{
            if($local==true){
                $this->pdf->Output("files/REPO/{$venta->coti_numero}.pdf",'F');
            }else{
                $this->pdf->Output($archivo,'I');
            }
        }
        
    }
}