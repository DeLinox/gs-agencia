<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Proveedores extends CI_Controller {

    function __construct() {
        parent::__construct();
        
        if(!$this->session->userdata('authorized')){
            redirect(base_url()."login");
        }
        $this->usua_id = $this->session->userdata('authorized');
        $this->configuracion = $this->db->query("SELECT * FROM usuario WHERE usua_id='{$this->usua_id}'")->row();
        $this->permisos  = $this->Model_general->getPermisos($this->usua_id);
        $this->load->model("Model_general");
    }
    public function listado() {
        $this->load->helper('Funciones');
        $this->load->database();
        //$this->load->library('Framework');
        $this->load->library('Ssp');
        $this->load->library('Cssjs');
        
        $json = isset($_GET['json']) ? $_GET['json'] : false;

        $columns = array(
            array('db' => 'prov_id',            'dt' => 'ID',               "field" => "prov_id"),
            array('db' => 'prov_rsocial',       'dt' => 'Proveedor',        "field" => "prov_rsocial"),
            array('db' => 'docu_nombre',        'dt' => 'Documento',        "field" => "docu_nombre"),
            array('db' => 'prov_docnum',        'dt' => 'Numero',           "field" => "prov_docnum"),
            array('db' => 'prov_direccion',     'dt' => 'Direccion',        "field" => "prov_direccion"),
            array('db' => 'prov_email',         'dt' => 'Email',            "field" => "prov_email"),
            array('db' => 'prov_telefono',      'dt' => 'Teléfono',         "field" => "prov_telefono"),
            array('db' => 'prov_id',            'dt' => 'DT_RowId',         "field" => "prov_id")
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
            $joinQuery = "FROM proveedor INNER JOIN maestra_documentos ON prov_docu_id=docu_id";
            $where = "";
            $condiciones[]="";

            $where = count($condiciones) > 0 ? implode(' AND ', $condiciones) : "";
            echo json_encode(
                    $this->ssp->simple($_POST, $sql_details, $table, $primaryKey, $columns, $joinQuery, $where)
            );
            exit(0);
        }
        $datos['columns'] = $columns;

        $this->cssjs->set_path_js(base_url() . "assets/js/Proveedor/");
        $this->cssjs->add_js('listado');
        $script['js'] = $this->cssjs->generate_js();
        $this->load->view('header', $script);
        $this->load->view('menu');
        $this->load->view('Proveedor/listado', $datos);
        $this->load->view('footer');
    }
    

    public function buscar() {
        $responese = new StdClass;
        $search = isset($_GET['q']) ? $_GET["q"] : '';
        $datos = array();
        if($search == 'num'){
            $doc_num = $_GET['num'];
            $producto = $this->Model_general->select2("proveedor", array("prov_docnum" => $doc_num));
        }else{
            $producto = $this->Model_general->select2("proveedor", array("prov_rsocial" => $search));    
        }
        foreach ($producto["items"] as $value) {
            $datos[] = array("id" => $value->prov_id, "text" => $value->prov_rsocial, "direccion" => $value->prov_direccion, "docnum" => $value->prov_docnum, "docu" => $value->prov_docu_id, "email" => $value->prov_email);
        }
        $responese->total_count = $producto["total_count"];
        $responese->incomplete_results = false;
        $responese->items = $datos;
        echo json_encode($responese);
        
    }

    public function crear($prov_id=0) {
        $this->load->helper('Funciones');
        $proveedor = new stdClass();
        if ($prov_id == 0) {
            $proveedor->prov_id = 0;
            $proveedor->prov_rsocial = "";
            $proveedor->prov_docnum = "";
            $proveedor->prov_direccion = "";
            $proveedor->prov_docu_id = "";
            $proveedor->prov_email = "";
            $proveedor->prov_telefono = "";
        }else{
            $this->db->select('prov_id, prov_rsocial, prov_docnum, prov_docu_id, prov_direccion, prov_email, prov_telefono');
            $this->db->where('prov_id',$prov_id);
            $this->db->from('proveedor');
            $proveedor = $this->db->get()->row();
            $proveedor->prov_id = $proveedor->prov_id;
            $proveedor->prov_rsocial = $proveedor->prov_rsocial;
            $proveedor->prov_docnum = $proveedor->prov_docnum;
            $proveedor->prov_direccion = $proveedor->prov_direccion;
            $proveedor->prov_docu_id = $proveedor->prov_docu_id;
            $proveedor->prov_email = $proveedor->prov_email;
            $proveedor->prov_telefono = $proveedor->prov_telefono;
        }
        $datos["docu_options"] = $this->Model_general->getOptions('maestra_documentos',array('docu_id','docu_nombre'));
        $datos['proveedor'] = $proveedor;
        $this->load->view('Proveedor/form_crear', $datos);
    }
    public function buscar_v() {
        $this->load->helper('Funciones');
        $cliente = new stdClass();
        $cliente->clie_rsocial = "";
        $cliente->clie_id = "";
        $cliente->clie_docnum = "";
        $cliente->clie_direccion = "";
        $cliente->clie_docu_id = "";
        $datos["docu_options"] = $this->Model_general->getOptions('maestra_documentos',array('docu_id','docu_nombre'));
        $datos['cliente'] = $cliente;
        $this->load->view('Cliente/form_buscar', $datos);
    }

    function guardar($prov_id=0) {
        $rsocial = $this->input->post('rsocial');
        $direccion = $this->input->post('direccion');
        $docnum = $this->input->post('docnum');
        $documento = $this->input->post('documento');
        $email= $this->input->post('email');
        $telefono= $this->input->post('telefono');

        $datos = array("prov_docu_id" => $documento,
            "prov_rsocial" => $rsocial,
            "prov_docnum" => $docnum,
            "prov_email" => $email,
            "prov_direccion" => $direccion,
            "prov_telefono" => $telefono);
        if ($prov_id!='0') {
            $this->load->database();
            if ($this->Model_general->guardar_edit_registro("proveedor", $datos, array("prov_id" => $prov_id)) == TRUE):
                $json['exito'] = true;
            else:
                $json['exito'] = false;
                $json['mensaje'] = "Error al actualizar los datos";
            endif;
        }else {
            if (($meta = $this->Model_general->guardar_registro("proveedor", $datos)) == TRUE):
                $json['exito'] = true;
                $json['datos'] = array_merge(array('prov_id'=>$meta['id']),$datos);
            else:
                $json['exito'] = false;
                $json['mensaje'] = "Error al guardar los datos";
            endif;
        }
        echo json_encode($json);
    }
    function mostrar() {
        $rsocial = $this->input->post('srsocial');
        $direccion = $this->input->post('sdireccion');
        $docnum = $this->input->post('sdocnum');
        $documento = $this->input->post('sdocumento');
        $email = $this->input->post('semail');
        $prov_id = $this->input->post('clie_id');

        $datos = array("clie_docu_id" => $documento,
            "clie_rsocial" => $rsocial,
            "clie_docnum" => $docnum,
            "clie_email" => $email,
            "clie_direccion" => $direccion);
        
        $json['exito'] = true;
        $json['datos'] = array_merge(array('clie_id'=>$prov_id),$datos);
        echo json_encode($json);
    }
    function eliminar($id){
        $this->db->query("DELETE FROM proveedor WHERE prov_id={$id}");
        die(json_encode(array('exito'=>true,'mensaje'=>'')));
    }
}

