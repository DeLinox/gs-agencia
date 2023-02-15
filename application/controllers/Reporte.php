<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Reporte extends CI_Controller {

    function __construct() {
        parent::__construct();
        if(!$this->session->userdata('authorized')){
            redirect(base_url()."login");
        }
        $this->usua_id = $this->session->userdata('authorized');
        $this->configuracion = $this->db->query("SELECT * FROM usuario WHERE usua_id='{$this->usua_id}'")->row();
        $this->permisos  = $this->Model_general->getPermisos($this->usua_id);
        $this->load->model("Model_general");
        $this->load->library('Cssjs');
        $this->load->model("Model_general");
        $this->load->helper('Form');
    }
    public function ventas() {
        $this->load->helper('Funciones');
        $this->load->database();
        $this->load->library('Ssp');
        $this->load->library('Cssjs');

        $json = isset($_GET['json']) ? $_GET['json'] : false;
        $cur_sucu = $this->Model_general->getSucuID($this->session->userdata('authorized'));
        $columns = array(
            array('db' => 'vent_clie_rsocial',  'dt' => 'Cliente',      "field" => "vent_clie_rsocial"),
            array('db' => 'vent_fecha',         'dt' => 'Fecha',        "field" => "vent_fecha"),
            array('db' => "CONCAT(vent_serie,'-',vent_numero)", 'dt' => 'Número', "field" => "CONCAT(vent_serie,'-',vent_numero)"),
            array('db' => 'vent_total',         'dt' => 'Total',        "field" => "vent_total"),
            array('db' => 'vent_pagado',        'dt' => 'Estado',       "field" => "vent_pagado"),            
            array('db' => 'vent_pago_obs',      'dt' => 'Pago Obs',     "field" => "vent_pago_obs"),    
            array('db' => 'usua_nombres',       'dt' => 'Usuario',      "field" => "usua_nombres"),            
            array('db' => 'vent_id',            'dt' => 'DT_RowId',     "field" => "vent_id"),
            array('db' => 'vent_fact_situ',     'dt' => 'DT_Estado',    "field" => "vent_fact_situ"),
            array('db' => 'vent_email_send',    'dt' => 'DT_EmailSend', "field" => "vent_email_send"),
            array('db' => 'vent_pagado',        'dt' => 'DT_Pagado',    "field" => "vent_pagado")
        );
        
        $mon = '';

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

            $joinQuery = "FROM venta JOIN maestra_comprobantes ON comp_id=vent_comp_id LEFT JOIN usuario ON usua_id = vent_usu_id";
            $where = "";
            if (!empty($_POST['desde'])&&!empty($_POST['hasta'])){
                $condiciones[] = "vent_fecha >='".$_POST['desde']."' AND vent_fecha <='".$_POST['hasta']."'";
            }
            if (!empty($_POST['comprobantes']))
                $condiciones[] = "vent_comp_id='".$_POST['comprobantes']."'";
            //$condiciones[] = "vent_comp_id='".$idcomp."'";
            //$condiciones[] = "vent_sucu_id ='".$cur_sucu->id."'";


           if (!empty($_POST['moneda']))
                $condiciones[] = "vent_moneda='".$_POST['moneda']."'";
            if (!empty($_POST['estado']))
                $condiciones[] = "vent_pagado='".$_POST['estado']."'";

            
            $where = count($condiciones) > 0 ? implode(' AND ', $condiciones) : "";
            echo json_encode(
                    $this->ssp->simple($_POST, $sql_details, $table, $primaryKey, $columns, $joinQuery, $where)
            );
            exit(0);
        }
        $monedas = $this->Model_general->enum_valores('venta','vent_moneda');
        $totales = array();
        foreach ($monedas as $i => $m) {
            $totales[$i]['moneda'] = $m;
            $this->db->select('SUM(vent_total) AS total');
            $this->db->from('venta');
            $this->db->where('vent_moneda', $m);
            $totales[$i]['total'] = $this->db->get()->row()->total;
            
        }
        $datos['total'] = $this->db->select('SUM(vent_total) AS total')->from('venta')->get()->row();
        $datos["comprobantes"] = $this->Model_general->getOptions('maestra_comprobantes', array("comp_id", "comp_nombre"),'* Comprobantes');
        $datos["moneda"] = array_merge(array('* Monedas'),$this->Model_general->enum_valores('venta','vent_moneda'));
        $datos['columns'] = $columns;

        $datos['titulo'] = "Comprobantes";
        //$datos['idcomp'] = $idcomp;

        $this->cssjs->set_path_js(base_url() . "assets/js/Reporte/");
        $this->cssjs->add_js('list_ventas');
        $script['js'] = $this->cssjs->generate_js();
        $this->load->view('header', $script);
        $this->load->view('menu');
        $this->load->view('reporte/list_ventas', $datos);
        $this->load->view('footer');
    }
    public function getVentas(){
        $monedas = $this->Model_general->enum_valores('venta','vent_moneda');
        $pagado = $this->Model_general->enum_valores('venta','vent_pagado');    
        
        $total = array();
        foreach($monedas as $m){
            $tmp = array();
            foreach($pagado as $p){
                $this->db->select('SUM(vent_total) AS total');
                $this->db->from('venta');
                if($this->input->post('search')['value'] != '')
                    $this->db->like('vent_clie_rsocial', $this->input->post('search')['value']);
                if($this->input->post('comprobantes') != '')                    
                    $this->db->where('vent_comp_id', $this->input->post('comprobantes'));
                if($this->input->post('desde') != '' && $this->input->post('hasta') != ''){
                    $this->db->where('vent_fecha >=', $this->input->post('desde'));
                $this->db->where('vent_fecha <=', $this->input->post('hasta'));
            }
            if($this->input->post('estado') != ''){
                $this->db->where('vent_pagado', $this->input->post('estado'));
            }else{
                $this->db->where('vent_pagado', $p);
            }
            if($this->input->post('moneda') != '0'){
                $this->db->where('vent_moneda', $this->input->post('moneda'));
            }else{
                $this->db->where('vent_moneda', $m);
            }
                
            $this->db->where('vent_moneda', $m);                
                $this->db->where('vent_pagado', $p);
                $consulta = $this->db->get()->row();
                
                $tmp[$p] = $consulta->total == '' ? '0.00' : $consulta->total;   
            }

            $total[] = array_merge(array("MONEDA" => $m), $tmp);
            
        }
        $html = "";
        $si = 0;
        $no = 0;
        
        foreach($total as $t){
            $si += $t['SI'];
            $no += $t['NO'];
            $sum = $t['SI']+$t['NO'];
            $html .= "<tr><th>".$t['MONEDA']."</th><td>".$t['SI']."</td><td>".$t['NO']."</td><td>".$sum."</td></tr>";
        }
        $suma = $si + $no;
        $html .= '<tr><th>TOTAL</th><th>'.$si.'</th><th>'.$no.'</th><th>'.$suma.'</th></tr>';
        echo json_encode(array('html' => $html));
    }
    public function listado() {
        $this->load->helper('Funciones');
        $this->load->database();
        //$this->load->library('Framework');
        $this->load->library('Ssp');
        $this->load->library('Cssjs');
        
        $json = isset($_GET['json']) ? $_GET['json'] : false;

        $columns = array(
            array('db' => 'clie_id',            'dt' => 'ID',               "field" => "clie_id"),
            array('db' => 'clie_rsocial',       'dt' => 'Cliente',          "field" => "clie_rsocial"),
            array('db' => 'docu_nombre',        'dt' => 'Documento',        "field" => "docu_nombre"),
            array('db' => 'clie_docnum',        'dt' => 'Numero',           "field" => "clie_docnum"),
            array('db' => 'clie_direccion',     'dt' => 'Direccion',        "field" => "clie_direccion"),
            array('db' => 'clie_email',         'dt' => 'Email',            "field" => "clie_email"),
            array('db' => 'clie_id',            'dt' => 'DT_RowId',         "field" => "clie_id")
        );

        if ($json) {

            $json = isset($_GET['json']) ? $_GET['json'] : false;

            $table = 'cliente';
            $primaryKey = 'clie_id';

            $sql_details = array(
                'user' => $this->db->username,
                'pass' => $this->db->password,
                'db' => $this->db->database,
                'host' => $this->db->hostname
            );

            $condiciones = array();
            $joinQuery = "FROM cliente INNER JOIN maestra_documentos ON clie_docu_id=docu_id";
            $where = "";
            $condiciones[]="";

            $where = count($condiciones) > 0 ? implode(' AND ', $condiciones) : "";
            echo json_encode(
                    $this->ssp->simple($_POST, $sql_details, $table, $primaryKey, $columns, $joinQuery, $where)
            );
            exit(0);
        }
        $datos['columns'] = $columns;

        $this->cssjs->set_path_js(base_url() . "assets/js/cliente/");
        $this->cssjs->add_js('listado');
        $script['js'] = $this->cssjs->generate_js();
        $this->load->view('header', $script);
        $this->load->view('menu');
        $this->load->view('Cliente/listado', $datos);
        $this->load->view('footer');
    }
    public function ventas_cliente() {
        $this->load->helper('Funciones');
        $this->load->database();
        //$this->load->library('Framework');
        $this->load->library('Ssp');
        $this->load->library('Cssjs');
        
        $json = isset($_GET['json']) ? $_GET['json'] : false;
        $cur_sucu = $this->Model_general->getSucuID($this->session->userdata('authorized'));
        $columns = array(
            array('db' => 'vent_id',                            'dt' => 'ID',               "field" => "vent_id"),
            array('db' => 'comp_abrev',                          'dt' => 'tipo',      "field" => "comp_abrev"),
            array('db' => "CONCAT(vent_serie,'-',vent_numero)", 'dt' => 'Número',           "field" => "CONCAT(vent_serie,'-',vent_numero)"),
            array('db' => 'vent_fecha',                         'dt' => 'Fecha',            "field" => "vent_fecha"),
            array('db' => 'vent_moneda',                         'dt' => 'Mondeda',            "field" => "vent_moneda"),
            array('db' => 'vent_total',                         'dt' => 'Total',            "field" => "vent_total"),
            array('db' => 'vent_clie_rsocial',                  'dt' => 'Cliente',          "field" => "vent_clie_rsocial"),

            array('db' => 'vent_id',                            'dt' => 'DT_RowId',         "field" => "vent_id"),
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

            $joinQuery = "FROM venta JOIN maestra_comprobantes ON comp_id=vent_comp_id";
            $where = "";
            if (!empty($_POST['desde'])&&!empty($_POST['hasta'])){
                $condiciones[] = "vent_fecha >='".$_POST['desde']."' AND vent_fecha <='".$_POST['hasta']."'";
            }
            if (!empty($_POST['comprobantes']))
                $condiciones[] = "vent_comp_id='".$_POST['comprobantes']."'";
            if (!empty($_POST['cliente']))
                $condiciones[] = "vent_clie_id='".$_POST['cliente']."'";
            //$condiciones[] = "vent_comp_id='".$idcomp."'";

           /* if (!empty($_POST['moneda']))
                $condiciones[] = "vent_moneda='".$_POST['moneda']."'";
            if (!empty($_POST['archivo']))
                $condiciones[] = "vent_genera_archivo='".$_POST['archivo']."'";*/

            
            $where = count($condiciones) > 0 ? implode(' AND ', $condiciones) : "";
            echo json_encode(
                    $this->ssp->simple($_POST, $sql_details, $table, $primaryKey, $columns, $joinQuery, $where)
            );
            exit(0);
        }
        $datos["comprobantes"] = $this->Model_general->getOptions('maestra_comprobantes', array("comp_id", "comp_nombre"),'* Comprobantes');
        $datos["moneda"] = array_merge(array('* Monedas'),$this->Model_general->enum_valores('venta','vent_moneda'));
        $datos["clientes"] = $this->Model_general->getOptions('cliente', array("clie_id", "clie_rsocial"),'* Clientes');
        $datos['columns'] = $columns;

        $datos['titulo'] = "Ventas por Cliente";
        //$datos['idcomp'] = $idcomp;

        $this->cssjs->set_path_js(base_url() . "assets/js/Reporte/");
        $this->cssjs->add_js('list_vCliente');
        $script['js'] = $this->cssjs->generate_js();
        $this->load->view('header', $script);
        $this->load->view('menu');
        $this->load->view('reporte/list_vCliente', $datos);
        $this->load->view('footer');
    }
    public function ventas_empleado() {
        $this->load->helper('Funciones');
        $this->load->database();
        //$this->load->library('Framework');
        $this->load->library('Ssp');
        $this->load->library('Cssjs');
        
        $json = isset($_GET['json']) ? $_GET['json'] : false;
        $cur_sucu = $this->Model_general->getSucuID($this->session->userdata('authorized'));
        $columns = array(
            array('db' => 'vent_id',                            'dt' => 'ID',               "field" => "vent_id"),
            array('db' => 'comp_abrev',                          'dt' => 'tipo',      "field" => "comp_abrev"),
            array('db' => "CONCAT(vent_serie,'-',vent_numero)", 'dt' => 'Número',           "field" => "CONCAT(vent_serie,'-',vent_numero)"),
            array('db' => 'vent_fecha',                         'dt' => 'Fecha',            "field" => "vent_fecha"),
            array('db' => 'vent_moneda',                         'dt' => 'Mondeda',            "field" => "vent_moneda"),
            array('db' => 'vent_total',                         'dt' => 'Total',            "field" => "vent_total"),
            array('db' => 'usua_nombres',                  'dt' => 'Usuario',          "field" => "usua_nombres"),

            array('db' => 'vent_id',                            'dt' => 'DT_RowId',         "field" => "vent_id"),
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

            $joinQuery = "FROM venta JOIN maestra_comprobantes ON comp_id=vent_comp_id LEFT JOIN usuario ON vent_usu_id = usua_id";
            $where = "";
            if (!empty($_POST['desde'])&&!empty($_POST['hasta'])){
                $condiciones[] = "vent_fecha >='".$_POST['desde']."' AND vent_fecha <='".$_POST['hasta']."'";
            }
            if (!empty($_POST['comprobantes']))
                $condiciones[] = "vent_comp_id='".$_POST['comprobantes']."'";
            if (!empty($_POST['usuario']))
                $condiciones[] = "vent_usu_id='".$_POST['usuario']."'";
            //$condiciones[] = "vent_comp_id='".$idcomp."'";

           if (!empty($_POST['moneda']))
                $condiciones[] = "vent_moneda='".$_POST['moneda']."'";
            /*
            if (!empty($_POST['archivo']))
                $condiciones[] = "vent_genera_archivo='".$_POST['archivo']."'";*/

            
            $where = count($condiciones) > 0 ? implode(' AND ', $condiciones) : "";
            echo json_encode(
                    $this->ssp->simple($_POST, $sql_details, $table, $primaryKey, $columns, $joinQuery, $where)
            );
            exit(0);
        }
        $datos["comprobantes"] = $this->Model_general->getOptions('maestra_comprobantes', array("comp_id", "comp_nombre"),'* Comprobantes');
        $datos["moneda"] = array_merge(array('* Monedas'),$this->Model_general->enum_valores('venta','vent_moneda'));
        $datos["usuarios"] = $this->Model_general->getOptions('usuario', array("usua_id", "usua_nombres"),'* Usuarios');
        $datos["cmps"] = $this->Model_general->getData('maestra_comprobantes', array('comp_id', 'comp_abrev'));
        $datos['columns'] = $columns;

        $datos['titulo'] = "Ventas por Cliente";
        //$datos['idcomp'] = $idcomp;

        $this->cssjs->set_path_js(base_url() . "assets/js/Reporte/");
        $this->cssjs->add_js('list_vUsuario');
        $script['js'] = $this->cssjs->generate_js();
        $this->load->view('header', $script);
        $this->load->view('menu');
        $this->load->view('reporte/list_vUsuario', $datos);
        $this->load->view('footer');
    }
    public function ingresos_proveedor() {
        $this->load->helper('Funciones');
        $this->load->database();
        //$this->load->library('Framework');
        $this->load->library('Ssp');
        $this->load->library('Cssjs');
        
        $json = isset($_GET['json']) ? $_GET['json'] : false;
        $cur_sucu = $this->Model_general->getSucuID($this->session->userdata('authorized'));
        $columns = array(
            array('db' => 'movi_id',            'dt' => 'ID',       "field" => "movi_id"),
            array('db' => 'comp_abrev',         'dt' => 'tipo',     "field" => "comp_abrev"),
            array('db' => "CONCAT(movi_serie,'-',movi_numero)", 'dt' => 'Número', "field" => "CONCAT(movi_serie,'-',movi_numero)"),
            array('db' => 'movi_fecha',         'dt' => 'Fecha',    "field" => "movi_fecha"),
            array('db' => 'movi_moneda',        'dt' => 'Mondeda',  "field" => "movi_moneda"),
            array('db' => 'movi_total',         'dt' => 'Total',    "field" => "movi_total"),
            array('db' => 'movi_prov_rsocial',  'dt' => 'Proveedor',"field" => "movi_prov_rsocial"),
            array('db' => 'movi_id',            'dt' => 'DT_RowId', "field" => "movi_id"),
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
            if (!empty($_POST['comprobantes']))
                $condiciones[] = "movi_comp_id='".$_POST['comprobantes']."'";
            if (!empty($_POST['proveedor']))
                $condiciones[] = "movi_prov_id='".$_POST['proveedor']."'";
            $condiciones[] = "movi_clase = 'INGRESO'";

            $where = count($condiciones) > 0 ? implode(' AND ', $condiciones) : "";
            echo json_encode(
                    $this->ssp->simple($_POST, $sql_details, $table, $primaryKey, $columns, $joinQuery, $where)
            );
            exit(0);
        }
        $datos["comprobantes"] = $this->Model_general->getOptions('maestra_comprobantes', array("comp_id", "comp_nombre"),'* Comprobantes');
        $datos["moneda"] = array_merge(array('* Monedas'),$this->Model_general->enum_valores('movimiento','movi_moneda'));
        $datos["proveedores"] = $this->Model_general->getOptions('proveedor', array("prov_id", "prov_rsocial"),'* proveedores');
        $datos['columns'] = $columns;

        $datos['titulo'] = "Ingresos por proveedor";
        //$datos['idcomp'] = $idcomp;

        $this->cssjs->set_path_js(base_url() . "assets/js/Reporte/");
        $this->cssjs->add_js('list_iProveedor');
        $script['js'] = $this->cssjs->generate_js();
        $this->load->view('header', $script);
        $this->load->view('menu');
        $this->load->view('reporte/list_iProveedor', $datos);
        $this->load->view('footer');
    }
    

    public function buscar() {
        $responese = new StdClass;
        $search = isset($_GET['q']) ? $_GET["q"] : '';
        $datos = array();
        if($search == 'num'){
            $doc_num = $_GET['num'];
            $producto = $this->Model_general->select2("cliente", array("clie_docnum" => $doc_num));
        }else{
            $producto = $this->Model_general->select2("cliente", array("clie_rsocial" => $search));    
        }
        foreach ($producto["items"] as $value) {
            $datos[] = array("id" => $value->clie_id, "text" => $value->clie_rsocial, "direccion" => $value->clie_direccion, "docnum" => $value->clie_docnum, "docu" => $value->clie_docu_id, "email" => $value->clie_email);
        }
        $responese->total_count = $producto["total_count"];
        $responese->incomplete_results = false;
        $responese->items = $datos;
        echo json_encode($responese);
        
    }

    public function crear($clie_id=0) {
        $this->load->helper('Funciones');
        $cliente = new stdClass();
        if ($clie_id == 0) {
            $cliente->clie_id = 0;
            $cliente->clie_rsocial = "";
            $cliente->clie_docnum = "";
            $cliente->clie_direccion = "";
            $cliente->clie_docu_id = "";
            $cliente->clie_email = "";
        }else{
            $this->db->select('clie_id, clie_rsocial, clie_docnum, clie_docu_id, clie_direccion, clie_email');
            $this->db->where('clie_id',$clie_id);
            $this->db->from('cliente');
            $cliente = $this->db->get()->row();
            $cliente->clie_id = $cliente->clie_id;
            $cliente->clie_rsocial = $cliente->clie_rsocial;
            $cliente->clie_docnum = $cliente->clie_docnum;
            $cliente->clie_direccion = $cliente->clie_direccion;
            $cliente->clie_docu_id = $cliente->clie_docu_id;
            $cliente->clie_email = $cliente->clie_email;
        }
        $datos["docu_options"] = $this->Model_general->getOptions('maestra_documentos',array('docu_id','docu_nombre'));
        $datos['cliente'] = $cliente;
        $this->load->view('cliente/form_crear', $datos);
    }
    public function buscar_v() {
        $this->load->helper('Funciones');
        $cliente = new stdClass();
        $cliente->clie_rsocial = "";
        $cliente->clie_docnum = "";
        $cliente->clie_id = "";
        $cliente->clie_direccion = "";
        $cliente->clie_docu_id = "";
        $datos["docu_options"] = $this->Model_general->getOptions('maestra_documentos',array('docu_id','docu_nombre'));
        $datos['cliente'] = $cliente;
        $this->load->view('cliente/form_buscar', $datos);
    }

    function guardar($clie_id=0) {
        $rsocial = $this->input->post('rsocial');
        $direccion = $this->input->post('direccion');
        $docnum = $this->input->post('docnum');
        $documento = $this->input->post('documento');
        $email= $this->input->post('email');

        $datos = array("clie_docu_id" => $documento,
            "clie_rsocial" => $rsocial,
            "clie_docnum" => $docnum,
            "clie_email" => $email,
            "clie_direccion" => $direccion);
        if ($clie_id!='0') {
            $this->load->database();
            if ($this->Model_general->guardar_edit_registro("cliente", $datos, array("clie_id" => $clie_id)) == TRUE):
                $json['exito'] = true;
            else:
                $json['exito'] = false;
                $json['mensaje'] = "Error al actualizar los datos";
            endif;
        }
        else {
            if (($meta = $this->Model_general->guardar_registro("cliente", $datos)) == TRUE):
                $json['exito'] = true;
                $json['datos'] = array_merge(array('clie_id'=>$meta['id']),$datos);
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
        $clie_id = $this->input->post('clie_id');

        $datos = array("clie_docu_id" => $documento,
            "clie_rsocial" => $rsocial,
            "clie_docnum" => $docnum,
            "clie_email" => $email,
            "clie_direccion" => $direccion);
        
        $json['exito'] = true;
        $json['datos'] = array_merge(array('clie_id'=>$clie_id),$datos);
        echo json_encode($json);
    }
    function eliminar($id){
        $this->db->query("DELETE FROM cliente WHERE clie_id={$id}");
        die(json_encode(array('exito'=>true,'mensaje'=>'')));
    }
    function getResumenCliente(){
        $this->db->select("vent_clie_id, clie_rsocial, clie_id, SUM(vent_total) AS total_importe");
        $this->db->from('venta');
        $this->db->join('cliente', 'vent_clie_id = clie_id', 'left');
        if($this->input->post('desde') != '' && $this->input->post('hasta') != ''){
            $this->db->where('vent_fecha >=', $this->input->post('desde'));
        $this->db->where('vent_fecha <=', $this->input->post('hasta'));
        }
        if($this->input->post('comprobantes') != '')
            $this->db->where('vent_comp_id', $this->input->post('comprobantes'));
        if($this->input->post('cliente') != '')
            $this->db->where('vent_clie_id', $this->input->post('cliente'));
        if($this->input->post('search')['value'] != '')
            $this->db->like('vent_clie_rsocial', $this->input->post('search')['value']);
        $this->db->group_by('vent_clie_id');
        $this->db->order_by('total_importe', 'DESC');
        $consulta = $this->db->get()->result();
        $comprobantes = $this->Model_general->getData('maestra_comprobantes', array('*'));
        $html = '';
        foreach ($consulta as $i => $val) {
            $resumen_comp = '';
            $r_comp = '';
            foreach ($comprobantes as $k => $comp) {
                $this->db->select("COUNT(*) AS cantidad");
                $this->db->from('venta');
                if($this->input->post('desde') != '' && $this->input->post('hasta') != ''){
                $this->db->where('vent_fecha >=', $this->input->post('desde'));
            $this->db->where('vent_fecha <=', $this->input->post('hasta'));
            }
                $this->db->where(array("vent_clie_id" => $val->vent_clie_id, "vent_comp_id" => $comp->comp_id));
                $cnt_comp = $this->db->get()->row();
                if($this->input->post('comprobantes') != ''){
                    if($comp->comp_id == $this->input->post('comprobantes'))
                        $r_comp .= '<td>'.$cnt_comp->cantidad.'</td>';
                    else
                        $r_comp .= '<td>0</td>';
                }else
                    $r_comp .= '<td>'.$cnt_comp->cantidad.'</td>';
            }
            $res_comp = $resumen_comp;
            $ult_fecha = $this->db->select('DATE_FORMAT(vent_fecha, "%d/%m/%Y") AS ultima')->from('venta')->where('vent_clie_id', $val->vent_clie_id)->limit(1)->order_by('vent_id', 'DESC')->get()->row();
            $html .= '<tr><td>'.$val->vent_clie_id.'</td><td>'.$val->clie_rsocial.'</td><td>'.$val->total_importe.'</td><td>'.$ult_fecha->ultima.'</td>'.$r_comp.'</tr>';
            
        }
        echo json_encode(array('data' => $html));
    }
    function getResumenEmpleado(){
        
        $html = '';
        $desde = $this->input->post('desde');
        $hasta = $this->input->post('hasta');
        $usuario = $this->input->post('usuario');
        
        $comprobantes = $this->Model_general->getData('maestra_comprobantes', array('*'));
        $usuarios = $this->Model_general->getData('usuario', array('usua_id, usua_nombres'));
        $reso = array();
        foreach ($usuarios as $i => $usu) {
            if($usuario == $usu->usua_id || $usuario == ''){
                $r_comp = '';
                $r_total= 0;
                foreach ($comprobantes as $k => $comp) {
                    $this->db->select("COUNT(*) AS cantidad, SUM(vent_total) AS total");
                    $this->db->from('venta');
                    $this->db->where(array("vent_usu_id" => $usu->usua_id, "vent_comp_id" => $comp->comp_id));
                    if($this->input->post('moneda') != '0')
                        $this->db->where("vent_moneda", $this->input->post('moneda'));  
                    if($this->input->post('desde') != '' && $this->input->post('hasta') != ''){
                    $this->db->where('vent_fecha >=', $this->input->post('desde'));
                $this->db->where('vent_fecha <=', $this->input->post('hasta'));
                }
                    $cnt_comp = $this->db->get()->row();
                    if($this->input->post('comprobantes') != ''){
            if($this->input->post('comprobantes') == $comp->comp_id){
                $r_comp .= '<td>'.$cnt_comp->cantidad.'</td>';
                            $r_total += $cnt_comp->total;                    
            }else{
                $r_comp .= '<td>0</td>';
                            $r_total += 0;
            }
                    }else{
                    $r_comp .= '<td>'.$cnt_comp->cantidad.'</td>';
                        $r_total += $cnt_comp->total;                    
                    }
                    

                }
                $ult_fecha = $this->db->select('DATE_FORMAT(vent_fecha, "%d/%m/%Y") AS ultima')->from('venta')->where('vent_usu_id', $usu->usua_id)->limit(1)->order_by('vent_id', 'DESC')->get()->row();
                
                $html .= '<tr><td>'.$usu->usua_id.'</td><td>'.$usu->usua_nombres.'</td><td>'.$r_total.'</td><td>'.(count($ult_fecha) > 0 ? $ult_fecha->ultima : '').'</td>'.$r_comp.'</tr>';
            }
        }
        echo json_encode(array('data' => $html));
    }
    function reporte_ventas_empleados(){
        $hasta = $this->input->get('hasta');
        $desde = $this->input->get('desde');
        $moneda = $this->input->get('moneda');
        $tipo = $this->input->get('comprobantes');
        $search = $this->input->get('search');
        $usuario = $this->input->get('usuario');

        
        
        $this->db->select("U.usua_nombres as usuario, DATE_FORMAT(V.vent_fecha,'%d/%m/%Y') AS fecha, COMP.comp_id AS ccod, COMP.comp_abrev AS ctipo, V.vent_serie AS serie, V.vent_descripcion as vdesc,V.vent_numero AS numero, V.vent_clie_rsocial AS rsocial, V.vent_clie_num_documento as docid_nro,V.vent_total AS ingreso, 0 AS egreso, IF(V.vent_moneda ='SOLES','S','D') as moneda, GROUP_CONCAT(DISTINCT VD.deta_descripcion ORDER BY VD.deta_id ASC) AS detalle");
        $this->db->from("venta V");
        $this->db->join("maestra_comprobantes COMP","COMP.comp_id = V.vent_comp_id");
        $this->db->join("usuario U","U.usua_id = V.vent_usu_id");
        $this->db->join("venta_detalle VD","VD.deta_vent_id = V.vent_id");
        $this->db->where("V.vent_fecha BETWEEN '$desde' AND '$hasta'".($tipo != false?" AND V.vent_comp_id = '$tipo'":"")." ".($usuario != ''?" AND V.vent_usu_id = '$usuario'":"")." ".($moneda != '0'?"AND V.vent_moneda = '$moneda'":"")." ".($search != ""? " AND (V.vent_clie_rsocial LIKE '%$search%' OR V.vent_serie LIKE '%$search%' OR V.vent_numero LIKE '%$search%')":""));
        $this->db->group_by('V.vent_id');
        $this->db->order_by("V.vent_comp_id","ASC");
        $this->db->order_by("V.vent_serie","ASC");
        $this->db->order_by("V.vent_numero","ASC");
        $pago = $this->db->get()->result();

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
                ->setCellValue('A1', 'FECDOC')
                ->setCellValue('B1', 'DOC')
                ->setCellValue('C1', 'SERIE')
                ->setCellValue('D1', 'NUMERO')
                ->setCellValue('E1', 'RUC')
                ->setCellValue('F1', 'RAZON SOCIAL')
                ->setCellValue('G1', 'INGRESO')
                ->setCellValue('H1', 'MON')
                ->setCellValue('I1', 'USUARIO');


        $objPHPExcel->getActiveSheet()->getStyle('E')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
        $objPHPExcel->getActiveSheet()->freezePane('A2');
        
        $ini = 3;
        $index = 0;
        
        foreach($pago as $fila){
            $nro = $index+$ini;
            $index++;
            $objPHPExcel->getActiveSheet()
                ->setCellValue("A$nro", $fila->fecha)
                ->setCellValue("B$nro", $fila->ctipo)
                ->setCellValue("C$nro", $fila->serie)
                ->setCellValue("D$nro", $fila->numero)
                ->setCellValue("E$nro", $fila->docid_nro)
                ->setCellValue("F$nro", $fila->rsocial)
                ->setCellValue("G$nro", $fila->ingreso)
                ->setCellValue("H$nro", $fila->moneda)
                ->setCellValue("I$nro", $fila->usuario);
                
        }
        foreach(range('A','S') as $nro){
            $objPHPExcel->getActiveSheet()->getColumnDimension($nro)->setAutoSize(true);
        }
        
        $fin = $index+$ini-1;
        $objPHPExcel->getActiveSheet()->getStyle("G$ini:G$fin")->getNumberFormat()->setFormatCode('#,##0.00'); 
        /*
        $objPHPExcel->getActiveSheet()->getStyle("L$ini:O$fin")->getNumberFormat()->setFormatCode('#,##0.00'); 
        $objPHPExcel->getActiveSheet()->getStyle("R$ini:R$fin")->getNumberFormat()->setFormatCode('#,##0.00'); 
        */
        $objPHPExcel->getActiveSheet()->getStyle("A$ini:A$fin")->getNumberFormat()->setFormatCode('dd/mm/yyyy');
        $objPHPExcel->getActiveSheet()
                ->setCellValue('M1', 'USUARIO')
                ->setCellValue('N1', 'TOTAL VENTAS')
                ->setCellValue('O1', 'ULT VENTA')
                ->setCellValue('P1', 'FAC')
                ->setCellValue('Q1', 'BOL')
                ->setCellValue('R1', 'NCR')
                ->setCellValue('S1', 'NDE');
        $cmp_list = array("P", 'Q', 'R', 'S');
        $ini = 3;
        $index = 0;
        $comprobantes = $this->Model_general->getData('maestra_comprobantes', array('*'));
        $usuarios = $this->Model_general->getData('usuario', array('usua_id, usua_nombres'));
        $reso = array();
        foreach ($usuarios as $i => $usu) {
            if($usuario == $usu->usua_id || $usuario == ''){
                $r_comp = '';
                $r_total= 0;
                $ind = 0;
                $nro = $index+$ini;
                $index++;
                foreach ($comprobantes as $k => $comp) {
                    $this->db->select("COUNT(*) AS cantidad, SUM(vent_total) AS total");
                    $this->db->from('venta');
                    $this->db->where(array("vent_usu_id" => $usu->usua_id, "vent_comp_id" => $comp->comp_id));
                    if($moneda != '0')
                        $this->db->where("vent_moneda", $this->input->post('moneda'));  

                    $this->db->where('vent_fecha >=', $desde);
                    $this->db->where('vent_fecha <=', $hasta);
                    $cnt_comp = $this->db->get()->row();
                    if($tipo != ''){
                        if($tipo == $comp->comp_id){
                            $r_comp = $cnt_comp->cantidad;
                            $r_total += $cnt_comp->total;                    
                        }else{
                            $r_comp = '0';
                            $r_total += 0;
                        }
                    }else{
                        $r_comp = $cnt_comp->cantidad;
                        $r_total += $cnt_comp->total;                    
                    }
                    $ltr = $cmp_list[$ind].$nro;
                    $objPHPExcel->getActiveSheet()->setCellValue($ltr, $r_comp);
                    $ind++;
                }
                $ult_fecha = $this->db->select('DATE_FORMAT(vent_fecha, "%d/%m/%Y") AS ultima')->from('venta')->where('vent_usu_id', $usu->usua_id)->limit(1)->order_by('vent_id', 'DESC')->get()->row();

                $objPHPExcel->getActiveSheet()
                ->setCellValue("M$nro", $usu->usua_nombres)
                ->setCellValue("N$nro", $r_total)
                ->setCellValue("O$nro", (count($ult_fecha) > 0 ? $ult_fecha->ultima : ''));
            }
        }
        $fin = $index+$ini-1;
        $objPHPExcel->getActiveSheet()->getStyle("N$ini:N$fin")->getNumberFormat()->setFormatCode('#,##0.00'); 
        $objPHPExcel->getActiveSheet()->getStyle("O$ini:O$fin")->getNumberFormat()->setFormatCode('dd/mm/yyyy');
        
        $excel->excel_output($objPHPExcel, 'Ventas por empleado');
    }
    function reporte_ventas_general(){
        $hasta = $this->input->get('hasta');
        $desde = $this->input->get('desde');
        $pagado = $this->input->get('estado');
        $moneda = $this->input->get('moneda');
        $tipo = $this->input->get('comprobantes');
        $search = $this->input->get('search');

        
        $this->db->select("DATE_FORMAT(V.vent_fecha,'%d/%m/%Y') AS fecha, COMP.comp_abrev AS ctipo, V.vent_serie AS serie, V.vent_numero AS numero, V.vent_total AS total, V.vent_clie_rsocial AS rsocial, V.vent_pagado as estado, V.vent_pago_obs as pobs ,U.usua_nombres as usuario, IF(V.vent_moneda ='SOLES','S','D') as moneda");
        $this->db->from("venta V");
        $this->db->join("maestra_comprobantes COMP","COMP.comp_id = V.vent_comp_id");
        $this->db->join("usuario U","U.usua_id = V.vent_usu_id");
        $this->db->where("V.vent_fecha BETWEEN '$desde' AND '$hasta'".($tipo != false?" AND V.vent_comp_id = '$tipo'":"")." ".($moneda != '0'?"AND V.vent_moneda = '$moneda'":"")." ".($search != ""? " AND (V.vent_clie_rsocial LIKE '%$search%' OR V.vent_serie LIKE '%$search%' OR V.vent_numero LIKE '%$search%')":""));
        $this->db->group_by('V.vent_id');
        $this->db->order_by("V.vent_comp_id","ASC");
        $this->db->order_by("V.vent_serie","ASC");
        $this->db->order_by("V.vent_numero","ASC");
        $ventas = $this->db->get()->result();
        
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


        $objPHPExcel->getActiveSheet()
                ->setCellValue('A1', 'FECHA')
                ->setCellValue('B1', 'DOC')
                ->setCellValue('C1', 'SERIE')
                ->setCellValue('D1', 'NUMERO')
                ->setCellValue('E1', 'MONEDA')
                ->setCellValue('F1', 'TOTAL')
                ->setCellValue('G1', 'RAZON SOCIAL')
                ->setCellValue('H1', 'PAGADO')
                ->setCellValue('I1', 'PAGO OBS')
                ->setCellValue('J1', 'USUARIO');

        $objPHPExcel->getActiveSheet()->getStyle('D')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
        $objPHPExcel->getActiveSheet()->freezePane('A2');
        
        $ini = 3;
        $index = 0;
        
        foreach($ventas as $fila){
            $nro = $index+$ini;
            $index++;
            $objPHPExcel->getActiveSheet()
                ->setCellValue("A$nro", $fila->fecha)
                ->setCellValue("B$nro", $fila->ctipo)
                ->setCellValue("C$nro", $fila->serie)
                ->setCellValue("D$nro", $fila->numero)
                ->setCellValue("E$nro", $fila->moneda)
                ->setCellValue("F$nro", $fila->total)
                ->setCellValue("G$nro", $fila->rsocial)
                ->setCellValue("H$nro", $fila->estado)
                ->setCellValue("I$nro", $fila->pobs)
                ->setCellValue("J$nro", $fila->usuario);
        }
        foreach(range('A','J') as $nro){
        $objPHPExcel->getActiveSheet()->getColumnDimension($nro)->setAutoSize(true);
    }
        
        $fin = $index+$ini-1;
        $objPHPExcel->getActiveSheet()->getStyle("F$ini:F$fin")->getNumberFormat()->setFormatCode('#,##0.00'); 
        $objPHPExcel->getActiveSheet()->getStyle("N4:P6")->getNumberFormat()->setFormatCode('#,##0.00'); 
        /*
        $objPHPExcel->getActiveSheet()->getStyle("L$ini:O$fin")->getNumberFormat()->setFormatCode('#,##0.00'); 
        $objPHPExcel->getActiveSheet()->getStyle("R$ini:R$fin")->getNumberFormat()->setFormatCode('#,##0.00'); 
        */
        $objPHPExcel->getActiveSheet()->getStyle("A$ini:A$fin")->getNumberFormat()->setFormatCode('dd/mm/yyyy');
        
        $monedas_l = $this->Model_general->enum_valores('venta','vent_moneda');
        $pagado_l = $this->Model_general->enum_valores('venta','vent_pagado');    
        
        $total = array();
        foreach($monedas_l as $m){
            $tmp = array();
            foreach($pagado_l as $p){
                $this->db->select('SUM(vent_total) AS total');
                $this->db->from('venta');
                if($search != '')
                    $this->db->like('vent_clie_rsocial', $this->input->post('search')['value']);
                if($tipo != '')                    
                    $this->db->where('vent_comp_id', $this->input->post('comprobantes'));
                $this->db->where('vent_fecha >=', $desde);
                $this->db->where('vent_fecha <=', $hasta);
                if($pagado != ''){
                    $this->db->where('vent_pagado', $this->input->post('estado'));
                }else{
                    $this->db->where('vent_pagado', $p);
                }
                if($moneda != '0'){
                    $this->db->where('vent_moneda', $this->input->post('moneda'));
                }else{
                    $this->db->where('vent_moneda', $m);
                }
                    
                $this->db->where('vent_moneda', $m);                
                $this->db->where('vent_pagado', $p);
                $consulta = $this->db->get()->row();
                
                $tmp[$p] = $consulta->total == '' ? '0.00' : $consulta->total;   
            }

            $total[] = array_merge(array("MONEDA" => $m), $tmp);
            
        }
        
        $objPHPExcel->getActiveSheet()->getStyle('M4:M6')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('CEF1C9');
        $objPHPExcel->getActiveSheet()->getStyle('N3:P3')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('FFF9CE');
        
        $si = 0;
        $no = 0;
        $sig = 3;
        $objPHPExcel->getActiveSheet()
            ->setCellValue("M$sig", '')
            ->setCellValue("N$sig", 'PAGADO')
            ->setCellValue("O$sig", 'CREDITO')
            ->setCellValue("P$sig", 'SUMA');
        $sig++;
        foreach($total as $t){
            $si += $t['SI'];
            $no += $t['NO'];
            $sum = $t['SI']+$t['NO'];
            $objPHPExcel->getActiveSheet()
                ->setCellValue("M$sig", $t['MONEDA'])
                ->setCellValue("N$sig", $t['SI'])
                ->setCellValue("O$sig", $t['NO'])
                ->setCellValue("P$sig", $sum);
            $sig++;
        }
        $suma = $si + $no;
        $objPHPExcel->getActiveSheet()
                ->setCellValue("M$sig", 'TOTAL')
                ->setCellValue("N$sig", $si)
                ->setCellValue("O$sig", $no)
                ->setCellValue("P$sig", $suma);
        
        $excel->excel_output($objPHPExcel, 'Ventas generales');
    }
    public function reporte_excel_clientes(){
    $hasta = $this->input->get('hasta');
        $desde = $this->input->get('desde');
        $cliente = $this->input->get('clientes');
        $tipo = $this->input->get('comprobantes');
        $search = $this->input->get('search');
        
 
        $this->db->select("DATE_FORMAT(V.vent_fecha,'%d/%m/%Y') AS fecha, COMP.comp_abrev AS ctipo, V.vent_serie AS serie, V.vent_numero AS numero, V.vent_total AS total, V.vent_clie_rsocial AS rsocial, V.vent_pago_obs as pobs, IF(V.vent_moneda ='SOLES','S','D') as moneda");
        $this->db->from("venta V");
        $this->db->join("maestra_comprobantes COMP","COMP.comp_id = V.vent_comp_id");
        $this->db->where("V.vent_fecha BETWEEN '$desde' AND '$hasta'".($tipo != false?" AND V.vent_comp_id = '$tipo'":"")." ".($cliente != ''?" AND V.vent_clie_id = '$cliente'":"")." ".($search != ""? " AND (V.vent_clie_rsocial LIKE '%$search%' OR V.vent_serie LIKE '%$search%' OR V.vent_numero LIKE '%$search%')":""));
        $this->db->group_by('V.vent_id');
        $this->db->order_by("V.vent_comp_id","ASC");
        $this->db->order_by("V.vent_serie","ASC");
        $this->db->order_by("V.vent_numero","ASC");
        $ventas = $this->db->get()->result();
        
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
        

        $objPHPExcel->getActiveSheet()
                ->setCellValue('A1', 'FECHA')
                ->setCellValue('B1', 'DOC')
                ->setCellValue('C1', 'SERIE')
                ->setCellValue('D1', 'NUMERO')
                ->setCellValue('E1', 'MONEDA')
                ->setCellValue('F1', 'TOTAL')
                ->setCellValue('G1', 'RAZON SOCIAL');

        $objPHPExcel->getActiveSheet()->getStyle('D')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
        $objPHPExcel->getActiveSheet()->freezePane('A2');
        
        $ini = 3;
        $index = 0;
        
        foreach($ventas as $fila){
            $nro = $index+$ini;
            $index++;
            $objPHPExcel->getActiveSheet()
                ->setCellValue("A$nro", $fila->fecha)
                ->setCellValue("B$nro", $fila->ctipo)
                ->setCellValue("C$nro", $fila->serie)
                ->setCellValue("D$nro", $fila->numero)
                ->setCellValue("E$nro", $fila->moneda)
                ->setCellValue("F$nro", $fila->total)
                ->setCellValue("G$nro", $fila->rsocial);
        }
        foreach(range('A','L') as $nro){
        $objPHPExcel->getActiveSheet()->getColumnDimension($nro)->setAutoSize(true);
    }
        
        $fin = $index+$ini-1;
        $objPHPExcel->getActiveSheet()->getStyle("F$ini:F$fin")->getNumberFormat()->setFormatCode('#,##0.00'); 
        //$objPHPExcel->getActiveSheet()->getStyle("N4:P6")->getNumberFormat()->setFormatCode('#,##0.00'); 
        $objPHPExcel->getActiveSheet()->getStyle("A$ini:A$fin")->getNumberFormat()->setFormatCode('dd/mm/yyyy');
        

    
    $this->db->select("vent_clie_id, clie_rsocial, clie_id, SUM(vent_total) AS total_importe");
        $this->db->from('venta');
        $this->db->join('cliente', 'vent_clie_id = clie_id', 'left');
    $this->db->where('vent_fecha >=', $desde);
    $this->db->where('vent_fecha <=', $hasta);

        if($tipo != '')
            $this->db->where('vent_comp_id', $this->input->post('comprobantes'));
        if($cliente != '')
            $this->db->where('vent_clie_id', $this->input->post('cliente'));
        if($search != '')
            $this->db->like('vent_clie_rsocial', $this->input->post('search')['value']);
        $this->db->group_by('vent_clie_id');
        $this->db->order_by('total_importe', 'DESC');
        $consulta = $this->db->get()->result();
        $comprobantes = $this->Model_general->getData('maestra_comprobantes', array('*'));
        $html = '';
       

        $objPHPExcel->getActiveSheet()
                ->setCellValue('J1', 'CLIENTE')
                ->setCellValue('K1', 'TOTAL IMPORTE')
                ->setCellValue('L1', 'ULT IMPORTE')
                ->setCellValue('M1', 'FAC')
                ->setCellValue('N1', 'BOL')
                ->setCellValue('O1', 'NCR')
                ->setCellValue('P1', 'NDE');

        $cmp_list = array("M", 'N', 'O', 'P');
        $ini = 3;
        $index = 0;
         foreach ($consulta as $i => $val) {
            $resumen_comp = '';
            $r_comp = '';
            $ind = 0;
            $nro = $index+$ini;
            $index++;
            foreach ($comprobantes as $k => $comp) {
                $this->db->select("COUNT(*) AS cantidad");
                $this->db->from('venta');
            $this->db->where('vent_fecha >=', $desde);
        $this->db->where('vent_fecha <=', $hasta);
                $this->db->where(array("vent_clie_id" => $val->vent_clie_id, "vent_comp_id" => $comp->comp_id));
                $cnt_comp = $this->db->get()->row();
                if($tipo != ''){
                    if($comp->comp_id == $tipo)
                        $r_comp = $cnt_comp->cantidad;
                    else
                        $r_comp = '0';
                }else
                    $r_comp = $cnt_comp->cantidad;
            $ltr = $cmp_list[$ind].$nro;
            $objPHPExcel->getActiveSheet()->setCellValue($ltr, $r_comp);
            $ind++;
            }
            $res_comp = $resumen_comp;
            $ult_fecha = $this->db->select('DATE_FORMAT(vent_fecha, "%d/%m/%Y") AS ultima')->from('venta')->where('vent_clie_id', $val->vent_clie_id)->limit(1)->order_by('vent_id', 'DESC')->get()->row();
            //$html .= '<tr><td>'.$val->vent_clie_id.'</td><td>'.$val->clie_rsocial.'</td><td>'.$val->total_importe.'</td><td>'.$ult_fecha->ultima.'</td>'.$r_comp.'</tr>';
            
            $objPHPExcel->getActiveSheet()
                ->setCellValue("J$nro", $val->clie_rsocial)
                ->setCellValue("K$nro", $val->total_importe)
                ->setCellValue("L$nro", $ult_fecha->ultima);
            
        }
        
        $fin = $index+$ini-1;
        $objPHPExcel->getActiveSheet()->getStyle("F$ini:F$fin")->getNumberFormat()->setFormatCode('#,##0.00'); 
        //$objPHPExcel->getActiveSheet()->getStyle("N4:P6")->getNumberFormat()->setFormatCode('#,##0.00'); 
        $objPHPExcel->getActiveSheet()->getStyle("A$ini:A$fin")->getNumberFormat()->setFormatCode('dd/mm/yyyy');
        
        
        $excel->excel_output($objPHPExcel, 'Ventas por Cliente');    
    }
    public function reporte_excel_proveedores(){
    $hasta = $this->input->get('hasta');
        $desde = $this->input->get('desde');
        $proveedor = $this->input->get('proveedor');
        $tipo = $this->input->get('comprobantes');
        $search = $this->input->get('search');
        
        $this->db->select("DATE_FORMAT(V.movi_fecha,'%d/%m/%Y') AS fecha, COMP.comp_abrev AS ctipo, V.movi_comp_serie AS serie, V.movi_comp_numero AS numero, V.movi_total AS total, V.movi_prov_rsocial AS rsocial, IF(V.movi_moneda ='SOLES','S','D') as moneda");
        $this->db->from("movimiento V");
        $this->db->join("maestra_comprobantes COMP","COMP.comp_id = V.movi_comp_id");
        $this->db->where("movi_fecha BETWEEN '$desde' AND '$hasta' AND movi_clase = 'INGRESO'".($tipo != false?" AND V.movi_comp_id = '$tipo'":"")." ".($proveedor != ''?" AND V.movi_prov_id = '$proveedor'":"")." ".($search != ""? " AND (V.movi_prov_rsocial LIKE '%$search%' OR V.movi_comp_serie LIKE '%$search%' OR V.movi_comp_numero LIKE '%$search%')":""));

        $this->db->order_by("V.movi_comp_id","ASC");
        $this->db->order_by("V.movi_comp_serie","ASC");
        $this->db->order_by("V.movi_comp_numero","ASC");
        $ingresos = $this->db->get()->result();
        
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
        

        $objPHPExcel->getActiveSheet()
                ->setCellValue('A1', 'FECHA')
                ->setCellValue('B1', 'DOC')
                ->setCellValue('C1', 'SERIE')
                ->setCellValue('D1', 'NUMERO')
                ->setCellValue('E1', 'MONEDA')
                ->setCellValue('F1', 'TOTAL')
                ->setCellValue('G1', 'RAZON SOCIAL');

        $objPHPExcel->getActiveSheet()->getStyle('D')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
        $objPHPExcel->getActiveSheet()->freezePane('A2');
        
        $ini = 3;
        $index = 0;
        
        foreach($ingresos as $fila){
            $nro = $index+$ini;
            $index++;
            $objPHPExcel->getActiveSheet()
                ->setCellValue("A$nro", $fila->fecha)
                ->setCellValue("B$nro", $fila->ctipo)
                ->setCellValue("C$nro", $fila->serie)
                ->setCellValue("D$nro", $fila->numero)
                ->setCellValue("E$nro", $fila->moneda)
                ->setCellValue("F$nro", $fila->total)
                ->setCellValue("G$nro", $fila->rsocial);
        }
        foreach(range('A','L') as $nro){
        $objPHPExcel->getActiveSheet()->getColumnDimension($nro)->setAutoSize(true);
    }
        
        $fin = $index+$ini-1;
        $objPHPExcel->getActiveSheet()->getStyle("F$ini:F$fin")->getNumberFormat()->setFormatCode('#,##0.00'); 
        $objPHPExcel->getActiveSheet()->getStyle("K")->getNumberFormat()->setFormatCode('#,##0.00'); 
        //$objPHPExcel->getActiveSheet()->getStyle("N4:P6")->getNumberFormat()->setFormatCode('#,##0.00'); 
        $objPHPExcel->getActiveSheet()->getStyle("A$ini:A$fin")->getNumberFormat()->setFormatCode('dd/mm/yyyy');
    
        $this->db->select("movi_prov_id, prov_rsocial, prov_id, SUM(movi_total) AS total_importe");
        $this->db->from('movimiento');
        $this->db->join('proveedor', 'movi_prov_id = prov_id', 'left');
        $this->db->where('movi_fecha >=', $desde);
        $this->db->where('movi_fecha <=', $hasta);
        if($tipo != '')
            $this->db->where('movi_comp_id', $tipo);
        if($proveedor != '')
            $this->db->where('movi_prov_id', $proveedor);
        if($search != '')
            $this->db->like('movi_prov_rsocial', $search);
        $this->db->where('movi_clase', 'INGRESO');
        $this->db->group_by('movi_prov_id');
        $this->db->order_by('total_importe', 'DESC');
        $consulta = $this->db->get()->result();


        $comprobantes = $this->Model_general->getData('maestra_comprobantes', array('*'));
        $html = '';
       

        $objPHPExcel->getActiveSheet()
                ->setCellValue('J1', 'PROVEEDOR')
                ->setCellValue('K1', 'TOTAL IMPORTE')
                ->setCellValue('L1', 'ULT IMPORTE')
                ->setCellValue('M1', 'FAC')
                ->setCellValue('N1', 'BOL')
                ->setCellValue('O1', 'NCR')
                ->setCellValue('P1', 'NDE');

        $cmp_list = array("M", 'N', 'O', 'P');
        $ini = 3;
        $index = 0;
         foreach ($consulta as $i => $val) {
            $resumen_comp = '';
            $r_comp = '';
            $ind = 0;
            $nro = $index+$ini;
            $index++;
            foreach ($comprobantes as $k => $comp) {
                $this->db->select("COUNT(*) AS cantidad");
                $this->db->from('movimiento');
                $this->db->where('movi_fecha >=', $desde);
                $this->db->where('movi_fecha <=', $hasta);
                $this->db->where('movi_clase', 'INGRESO');
                $this->db->where(array("movi_prov_id" => $val->movi_prov_id, "movi_comp_id" => $comp->comp_id));
                $cnt_comp = $this->db->get()->row();
                if($tipo != ''){
                    if($comp->comp_id == $tipo)
                        $r_comp = $cnt_comp->cantidad;
                    else
                        $r_comp = '0';
                }else
                    $r_comp = $cnt_comp->cantidad;
            $ltr = $cmp_list[$ind].$nro;
            $objPHPExcel->getActiveSheet()->setCellValue($ltr, $r_comp);
            $ind++;
            }
            $res_comp = $resumen_comp;
            $ult_fecha = $this->db->select('DATE_FORMAT(movi_fecha, "%d/%m/%Y") AS ultima')->from('movimiento')->where('movi_prov_id', $val->movi_prov_id)->limit(1)->order_by('movi_id', 'DESC')->get()->row();
            
            $objPHPExcel->getActiveSheet()
                ->setCellValue("J$nro", $val->prov_rsocial)
                ->setCellValue("K$nro", $val->total_importe)
                ->setCellValue("L$nro", $ult_fecha->ultima);
            
        }
        
        $fin = $index+$ini-1;
        $objPHPExcel->getActiveSheet()->getStyle("F$ini:F$fin")->getNumberFormat()->setFormatCode('#,##0.00'); 
        //$objPHPExcel->getActiveSheet()->getStyle("N4:P6")->getNumberFormat()->setFormatCode('#,##0.00'); 
        $objPHPExcel->getActiveSheet()->getStyle("A$ini:A$fin")->getNumberFormat()->setFormatCode('dd/mm/yyyy');
        
        
        $excel->excel_output($objPHPExcel, 'INGRESOS POR PROVEEDOR');    
    }
    public function productos_stock(){
        $this->load->helper('Funciones');
        $this->load->database();
        $this->load->library('Ssp');
        $this->load->library('Cssjs');

        $json = isset($_GET['json']) ? $_GET['json'] : false;

        $columns = array(
            array('db' => 'sucu_nombre',        'dt' => 'Almacen',         "field" => "sucu_nombre"),
            array('db' => 'prod_nombre',        'dt' => 'Producto',         "field" => "prod_nombre"),
            array('db' => 'stoc_cantidad',      'dt' => 'stoc_cantidad',    "field" => "stoc_cantidad"),
            array('db' => 'stoc_reg_fingreso',  'dt' => 'Último ingreso',   "field" => "stoc_reg_fingreso"),
            array('db' => 'stoc_reg_fsalida',   'dt' => 'Última salida',    "field" => "stoc_reg_fsalida")
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
    function getResumenProveedor(){
        $this->db->select("movi_prov_id, movi_prov_rsocial, movi_id, SUM(movi_total) AS total_importe");
        $this->db->from('movimiento');
        $this->db->join('proveedor', 'movi_prov_id = prov_id', 'left');
        if($this->input->post('desde') != '' && $this->input->post('hasta') != ''){
            $this->db->where('movi_fecha >=', $this->input->post('desde'));
            $this->db->where('movi_fecha <=', $this->input->post('hasta'));
        }
        if($this->input->post('comprobantes') != '')
            $this->db->where('movi_comp_id', $this->input->post('comprobantes'));
        if($this->input->post('proveedor') != '')
            $this->db->where('movi_prov_id', $this->input->post('proveedor'));
        if($this->input->post('search')['value'] != '')
            $this->db->like('movi_prov_rsocial', $this->input->post('search')['value']);
        $this->db->where('movi_clase', 'INGRESO');
        $this->db->group_by('movi_prov_id');
        $this->db->order_by('total_importe', 'DESC');
        $consulta = $this->db->get()->result();
        $comprobantes = $this->Model_general->getData('maestra_comprobantes', array('*'));
        $html = '';
        foreach ($consulta as $i => $val) {
            $resumen_comp = '';
            $r_comp = '';
            foreach ($comprobantes as $k => $comp) {
                $this->db->select("COUNT(*) AS cantidad");
                $this->db->from('movimiento');
                if($this->input->post('desde') != '' && $this->input->post('hasta') != ''){
                    $this->db->where('movi_fecha >=', $this->input->post('desde'));
                    $this->db->where('movi_fecha <=', $this->input->post('hasta'));
                }
                $this->db->where('movi_clase', 'INGRESO');
                $this->db->where(array("movi_prov_id" => $val->movi_prov_id, "movi_comp_id" => $comp->comp_id));
                $cnt_comp = $this->db->get()->row();
                if($this->input->post('comprobantes') != ''){
                    if($comp->comp_id == $this->input->post('comprobantes'))
                        $r_comp .= '<td>'.$cnt_comp->cantidad.'</td>';
                    else
                        $r_comp .= '<td>0</td>';
                }else
                    $r_comp .= '<td>'.$cnt_comp->cantidad.'</td>';
            }
            $res_comp = $resumen_comp;
            $ult_fecha = $this->db->select('DATE_FORMAT(movi_fecha, "%d/%m/%Y") AS ultima')->from('movimiento')->where('movi_prov_id', $val->movi_prov_id)->limit(1)->order_by('movi_id', 'DESC')->get()->row();
            $html .= '<tr><td>'.$val->movi_prov_id.'</td><td>'.$val->movi_prov_rsocial.'</td><td>'.$val->total_importe.'</td><td>'.$ult_fecha->ultima.'</td>'.$r_comp.'</tr>';
            
        }
        echo json_encode(array('data' => $html));
    }

    function kardex(){
        $this->load->helper('Funciones');
        $this->load->library('Cssjs');

        $datos["sucursal"] = $this->Model_general->getOptions('sucursal', array("sucu_id", "sucu_nombre"),'* Almacenes');

        $sql = "SELECT * FROM kardex_producto WHERE kard_sucu_id=1";
        $datos['kardex'] = $this->db->query($sql)->result();
        $this->cssjs->set_path_js(base_url() . "assets/js/Reporte/");
        $this->cssjs->add_js('inventario');
        $script['js'] = $this->cssjs->generate_js();
        $this->load->view('header', $script);
        $this->load->view('menu');
        $this->load->view('almacen/kardex', $datos);
        $this->load->view('footer');
    }
    function repo_cuadroIngresos($json=false){
        $this->load->helper('Funciones');
        if($json){

            $moneda = $this->Model_general->enum_valores('paquete','paqu_moneda');

            $search = $this->input->post("search");
            $tipo = $this->input->post("tipo");
            $mes = $this->input->post("mes");
            $anio = $this->input->post("anio");

            $filtros = array();
            $filtros2 = array();
            if($search != "")
                $filtros[] = "paqu_clie_rsocial LIKE '%".$search."%'";
            if($tipo != "")
                $filtro[] = "paqu_tipo = '".$tipo."'";
            if($mes != "")
                $filtros[] = "MONTH(deta_fechaserv) = '".$mes."'";
            if($anio != "")
                $filtros[] = "YEAR(deta_fechaserv) = '".$anio."'";
            $filtros[] = "paqu_estado != 'ANULADO'";
            $filtros[] = "paqu_escomprobante = 0";

            $filtros = (COUNT($filtros) > 0)?implode(" AND ", $filtros):1;
            //$filtros2 = (COUNT($filtros2) > 0)?" AND ".implode(" AND ", $filtros2):'';

            $clientes = $this->db->query("SELECT paqu_clie_id, paqu_clie_rsocial FROM paquete_detalle join paquete ON deta_paqu_id = paqu_id WHERE ".$filtros." GROUP BY paqu_clie_id")->result();
            if(COUNT($clientes) > 0){
                $tabla = "";
                $saldo_s = 0;
                $cobra_s = 0;
                $total_s = 0;
                $saldo_d = 0;
                $cobra_d = 0;
                $total_d = 0;

                foreach ($clientes as $i => $clie) {
                    $deuda = array();
                    $cobrado = array();
                    foreach ($moneda as $mone) {
                        $deuda[$mone] = $this->db->query("SELECT SUM(paqu_total) as total FROM paquete_detalle join paquete ON deta_paqu_id = paqu_id WHERE paqu_moneda = '{$mone}' AND paqu_clie_id = ".$clie->paqu_clie_id." AND paqu_escobrado = 0 ".($filtros==1?"":" AND ".$filtros))->row();    
						
                        $cobrado[$mone] = $this->db->query("SELECT SUM(paqu_total) as total FROM paquete_detalle join paquete ON deta_paqu_id = paqu_id WHERE paqu_moneda = '{$mone}' AND paqu_clie_id = ".$clie->paqu_clie_id." AND paqu_escobrado = 1 ".($filtros==1?"":" AND ".$filtros))->row();
                    }
					
                    $s_saldo = ($deuda["SOLES"]->total)?$deuda['SOLES']->total:'0.00';
                    $s_cobrado = ($cobrado["SOLES"]->total)?$cobrado['SOLES']->total:'0.00';
                    $s_total = number_format($s_saldo + $s_cobrado, 2, '.', '');
                    
                    $saldo_s += $s_saldo;
                    $cobra_s += $s_cobrado;
                    $total_s += $s_total;

                    $d_saldo = ($deuda["DOLARES"]->total)?$deuda['DOLARES']->total:'0.00';
                    $d_cobrado = ($cobrado["DOLARES"]->total)?$cobrado['DOLARES']->total:'0.00';
                    $d_total = number_format($d_saldo + $d_cobrado, 2, '.', '');

                    $saldo_d += $d_saldo;
                    $cobra_d += $d_cobrado;
                    $total_d += $d_total;

                    $tabla .= "<tr>";
                    $tabla .= "<td>".$clie->paqu_clie_rsocial."</td>";
                    $tabla .= "<td class='mone'>".$s_cobrado."</td>";
                    $tabla .= "<td class='mone'>".$s_saldo."</td>";
                    $tabla .= "<td class='mone'><strong>".$s_total."</strong></td>";
                    $tabla .= "<td class='mone'>".$d_cobrado."</td>";
                    $tabla .= "<td class='mone'>".$d_saldo."</td>";
                    $tabla .= "<td class='mone'><strong>".$d_total."</strong></td>";
                    $tabla .= "</tr>";
                }
                $tabla .= "<tr>";
                $tabla .= "<td class='mone'>TOTAL</th>";
                $tabla .= "<td class='mone'><b>".number_format($cobra_s, 2, '.', ' ')."</b></td>";
                $tabla .= "<td class='mone'><b>".number_format($saldo_s, 2, '.', ' ')."</b></td>";
                $tabla .= "<td class='mone'><b>".number_format($total_s, 2, '.', ' ')."</b></td>";
                $tabla .= "<td class='mone'><b>".number_format($cobra_d, 2, '.', ' ')."</b></td>";
                $tabla .= "<td class='mone'><b>".number_format($saldo_d, 2, '.', ' ')."</b></td>";
                $tabla .= "<td class='mone'><b>".number_format($total_d, 2, '.', ' ')."</b></td>";
                $tabla .= "</tr>";
            }else{
                $tabla = "<tr style='text-align:center'><th colspan='4'><strong>No hay datos disponibles</strong></th></tr>";
            }
            
            $datos['html'] = $tabla;
            echo json_encode($datos);
            exit(0);
        }
        $datos["tipo"] = array_merge(array(''=>'* Tipo reserva'),$this->Model_general->enum_valores('paquete','paqu_tipo'));
        
        $anios[""] = "* Años";
        for ($i=2018; $i <= date('Y'); $i++) { 
            $anios[$i] = $i;
        }
        $datos["anio"] = $anios;
        $datos["mes"] = array(""=>"* Meses",1=>"Enero",2=>"Febrero",3=>"Marzo",4=>"Abril",5=>"Mayo",6=>"Junio",7=>"Julio",8=>"Agosto",9=>"Septiembre",10=>"Octubre",11=>"Noviembre",12=>"Diciembre");
        $datos['sel_mone'] = "SOLES";
        $datos['sel_tipo'] = "LOCAL";
        $datos['sel_mes'] = date("m");
        $datos['sel_anio'] = date("Y");
        $datos['titulo'] = "Cuadro de Ingresos";

        $this->load->library('Ssp');
        $this->load->library('Cssjs');
        $this->cssjs->add_js(base_url().'assets/js/Reporte/cuadro_ingresos.js',false,false);

        $this->load->view('header');
        $this->load->view('menu');
        $this->load->view('Reporte/cuadro_ingresos', $datos);
        $this->load->view('footer');
    }
    function repo_cuadroIngresosIgv($json=false){
        $this->load->helper('Funciones');
        if($json){
            $moneda = $this->Model_general->enum_valores('paquete','paqu_moneda');

            $search = $this->input->post("search");
            $tipo = $this->input->post("tipo");
            $mes = $this->input->post("mes");
            $anio = $this->input->post("anio");

            $filtros = array();
            if($search != "")
                $filtros[] = "vent_clie_rsocial LIKE '%".$search."%'";
            if($mes != "")
                $filtros[] = "MONTH(vent_fecha) = '".$mes."'";
            if($anio != "")
                $filtros[] = "YEAR(vent_fecha) = '".$anio."'";

            $filtros = (COUNT($filtros) > 0)?implode(" AND ", $filtros):1;

            $clientes = $this->db->query("SELECT vent_clie_id, vent_clie_rsocial FROM venta WHERE ".$filtros." GROUP BY vent_clie_id")->result();

            if(COUNT($clientes) > 0){
                $tabla = "";
                $saldo_s = 0;
                $cobra_s = 0;
                $total_s = 0;
                $saldo_d = 0;
                $cobra_d = 0;
                $total_d = 0;

                foreach ($clientes as $i => $clie) {
                    $deuda = array();
                    $cobrado = array();
                    foreach ($moneda as $mone) {
                        $deuda[$mone] = $this->db->query("SELECT SUM(vent_total) as total FROM venta WHERE vent_moneda = '{$mone}' AND vent_clie_id = ".$clie->vent_clie_id." AND vent_escobrado = 0 ".($filtros==1?"":" AND ".$filtros))->row();

                        $cobrado[$mone] = $this->db->query("SELECT SUM(vent_total) as total FROM venta WHERE vent_moneda = '{$mone}' AND vent_clie_id = ".$clie->vent_clie_id." AND vent_escobrado = 1 ".($filtros==1?"":" AND ".$filtros))->row();
                    }
                    
					
                    $s_saldo = ($deuda["SOLES"]->total)?$deuda['SOLES']->total:'0.00';
                    $s_cobrado = ($cobrado["SOLES"]->total)?$cobrado['SOLES']->total:'0.00';
                    $s_total = number_format($s_saldo + $s_cobrado, 2, '.', '');
                    
                    $saldo_s += $s_saldo;
                    $cobra_s += $s_cobrado;
                    $total_s += $s_total;

                    $d_saldo = ($deuda["DOLARES"]->total)?$deuda['DOLARES']->total:'0.00';
                    $d_cobrado = ($cobrado["DOLARES"]->total)?$cobrado['DOLARES']->total:'0.00';
                    $d_total = number_format($d_saldo + $d_cobrado, 2, '.', '');

                    $saldo_d += $d_saldo;
                    $cobra_d += $d_cobrado;
                    $total_d += $d_total;

                    $tabla .= "<tr>";
                    $tabla .= "<td>".$clie->vent_clie_rsocial."</td>";
                    $tabla .= "<td class='mone'>".$s_cobrado."</td>";
                    $tabla .= "<td class='mone'>".$s_saldo."</td>";
                    $tabla .= "<td class='mone'><strong>".$s_total."</strong></td>";
                    $tabla .= "<td class='mone'>".$d_cobrado."</td>";
                    $tabla .= "<td class='mone'>".$d_saldo."</td>";
                    $tabla .= "<td class='mone'><strong>".$d_total."</strong></td>";
                    $tabla .= "</tr>";
                }
                $tabla .= "<tr>";
                $tabla .= "<td class='mone'>TOTAL</th>";
                $tabla .= "<td class='mone'><b>".number_format($cobra_s, 2, '.', ' ')."</b></td>";
                $tabla .= "<td class='mone'><b>".number_format($saldo_s, 2, '.', ' ')."</b></td>";
                $tabla .= "<td class='mone'><b>".number_format($total_s, 2, '.', ' ')."</b></td>";
                $tabla .= "<td class='mone'><b>".number_format($cobra_d, 2, '.', ' ')."</b></td>";
                $tabla .= "<td class='mone'><b>".number_format($saldo_d, 2, '.', ' ')."</b></td>";
                $tabla .= "<td class='mone'><b>".number_format($total_d, 2, '.', ' ')."</b></td>";
                $tabla .= "</tr>";
            }else{
                $tabla = "<tr style='text-align:center'><th colspan='4'><strong>No hay datos disponibles</strong></th></tr>";
            }
            
            $datos['html'] = $tabla;
            echo json_encode($datos);
            exit(0);
        }
        $datos["tipo"] = array_merge(array(''=>'* Tipo reserva'),$this->Model_general->enum_valores('paquete','paqu_tipo'));
        
        $anios[""] = "* Años";
        for ($i=2018; $i <= date('Y'); $i++) { 
            $anios[$i] = $i;
        }
        $datos["anio"] = $anios;
        $datos["mes"] = array(""=>"* Meses",1=>"Enero",2=>"Febrero",3=>"Marzo",4=>"Abril",5=>"Mayo",6=>"Junio",7=>"Julio",8=>"Agosto",9=>"Septiembre",10=>"Octubre",11=>"Noviembre",12=>"Diciembre");
        $datos['sel_mone'] = "SOLES";
        $datos['sel_tipo'] = "LOCAL";
        $datos['sel_mes'] = date("m");
        $datos['sel_anio'] = date("Y");
        $datos['titulo'] = "Cuadro de Ingresos (IGV)";
        $datos['igv'] = true;

        $this->load->library('Ssp');
        $this->load->library('Cssjs');
        $this->cssjs->add_js(base_url().'assets/js/Reporte/cuadro_ingresos.js',false,false);

        $this->load->view('header');
        $this->load->view('menu');
        $this->load->view('Reporte/cuadro_ingresos', $datos);
        $this->load->view('footer');
    }
    public function repo_movimientoTuristas($json = false){
        $this->load->helper('Funciones');
        if($json){
            //$clientes = $this->Model_general->getData("cliente", array("clie_id, clie_rsocial"));
            $mes = $this->input->post("mes");
            $anio = $this->input->post("anio");
			$tipo = $this->input->post("tipo");
            $servicios = explode(",",$this->input->post("servicios"));

            $this->db->select("paqu_clie_id as clie_id, paqu_clie_rsocial as clie_rsocial");
            $this->db->from("paquete");
            $this->db->join("paquete_detalle","paqu_id = deta_paqu_id");
			$this->db->join("cliente","clie_id= paqu_clie_id");
			$this->db->where("paqu_estado",'CONFIRMADO');
			if($tipo != "")
                $this->db->where("clie_reporte",$tipo);
            if($mes != "")
                $this->db->where("MONTH(deta_fechaserv)",$mes);
            if($anio != "")
                $this->db->where("YEAR(deta_fechaserv)",$anio);
            $this->db->where_in("deta_serv_id",$servicios);
            $this->db->group_by("paqu_clie_id");
            $this->db->order_by("paqu_clie_id","ASC");
            $clientes = $this->db->get()->result();

            $servicios = $this->db->where_in("serv_id",$servicios)->get("servicio")->result();
            $html = "<thead><tr><th>Contacto</th>";
            foreach ($servicios as $j => $serv) {
                $html .= "<th>".$serv->serv_descripcion."</th>";
                $serv->sumas = 0;
            }

            $html .= "<th>Suma</th><tr><thead><tbody>";
            $suma_total = 0;
            foreach ($clientes as $i => $clie) {
                $html .= "<tr>";
                $html .= "<td>".$clie->clie_rsocial."</td>";
                $suma_clie = 0;
                
                foreach ($servicios as $j => $serv) {
                    $this->db->select("SUM(deta_pax) as total");
                    $this->db->from("paquete_detalle");
                    $this->db->join("paquete", "paqu_id = deta_paqu_id");
					$this->db->join("cliente","clie_id= paqu_clie_id");
					$this->db->where("paqu_estado",'CONFIRMADO');
					if($tipo != "")
						$this->db->where("clie_reporte",$tipo);
                    if($mes != "")
                        $this->db->where("MONTH(deta_fechaserv)",$mes);
                    if($anio != "")
                        $this->db->where("YEAR(deta_fechaserv)",$anio);
                    $this->db->where("paqu_clie_id",$clie->clie_id);
                    $this->db->where("deta_serv_id",$serv->serv_id);
                    $pax = $this->db->get()->row();
                    $serv->sumas += $pax->total;
                    $suma_clie += $pax->total;
                    $html .= "<td class='mone'>".$pax->total."</td>";
                }
                $suma_total += $suma_clie;
                $html .= "<td class='mone'>".$suma_clie."</td>";
                $html .= "</tr>";
            }
            $html .= "<tr><th>TOTAL GENERAL</th>";
            foreach ($servicios as $j => $serv) {
                $html .= "<th style='text-align:right'>".$serv->sumas."</th>";
            }
            $html .= "<th style='text-align:right'>".$suma_total."</th>";
            $html .= "</tr></tbody>";
            $resp["html"] = $html;
            echo json_encode($resp);
            exit(0);
        }
        $anios[""] = "* Años";
        for ($i=2018; $i <= date('Y'); $i++) { 
            $anios[$i] = $i;
        }
        $datos["anio"] = $anios;
        $datos["mes"] = array(""=>"* Meses",1=>"Enero",2=>"Febrero",3=>"Marzo",4=>"Abril",5=>"Mayo",6=>"Junio",7=>"Julio",8=>"Agosto",9=>"Septiembre",10=>"Octubre",11=>"Niviembre",12=>"Diciembre");
        $datos['sel_mes'] = date("m");
        $datos['sel_anio'] = date("Y");
		$datos['tipo'] = array("" => "* Tipo", "LOCAL" => "LOCAL", "RECEPTIVO" => "RECEPTIVO");
        $datos['titulo'] = "Movimiento de turistas";

        $this->load->library('Ssp');
        $this->load->library('Cssjs');
        $this->cssjs->add_js(base_url().'assets/js/Reporte/movimiento_turistas.js?v=1.0',false,false);

        $this->load->view('header');
        $this->load->view('menu');
        $this->load->view('Reporte/movimiento_turistas', $datos);
        $this->load->view('footer');   
    }
    public function get_filtros($exc = false){
        if($exc)
            $anios = array();
        else
            $anios[""] = "* Años";
        for ($i=2018; $i <= date('Y'); $i++) { 
            $anios[$i] = $i;
        }
        $datos["anio"] = $anios;
        $datos["mes"] = array(1=>"Enero",2=>"Febrero",3=>"Marzo",4=>"Abril",5=>"Mayo",6=>"Junio",7=>"Julio",8=>"Agosto",9=>"Septiembre",10=>"Octubre",11=>"Niviembre",12=>"Diciembre");
        if(!$exc)
            $datos["mes"][""] = "* Meses";

        $datos['sel_mes'] = date("m");
        $datos['sel_anio'] = date("Y");
        return $datos;
    }
    public function repo_informeDeudas($json =  false){
        if($json){
            $search = $this->input->post("search");
            //$mes = $this->input->post("mes");
            //$anio = $this->input->post("anio");
			$desde = $this->input->post("desde");
            $hasta = $this->input->post("hasta");
            $filtros = array();

            if($search != "")
                $filtros[] = "paqu_clie_rsocial LIKE '%".$search."%'";
			$filtros[] = "paqu_estado != 'ANULADO'";
            /*
            if($mes != "")
                $filtros[] = "MONTH(paqu_fecha) = '".$mes."'";
            if($anio != "")
                $filtros[] = "YEAR(paqu_fecha) = '".$anio."'";
            */
            if($desde != "" && $hasta != "")
                $filtros[] = "deta_fechaserv >='".$_POST['desde']." 00:00:00' AND deta_fechaserv <='".$_POST['hasta']." 23:59:00'";

            $filtros = (COUNT($filtros) > 0)?implode(" AND ", $filtros):1;
            
            $clientes = $this->db->query("SELECT paqu_clie_id, paqu_clie_rsocial FROM paquete join paquete_detalle on deta_paqu_id = paqu_id WHERE ".$filtros." GROUP BY paqu_clie_id")->result();

            $moneda = $this->Model_general->enum_valores('paquete','paqu_moneda');
            $tipos = $this->Model_general->enum_valores('paquete','paqu_tipo');
            $tipoCobro = array("1" => "comprobante","2" => "liquidacion","3" => "efectivo");

            if(COUNT($clientes) > 0){
                $dataTipo = array();
                foreach ($tipos as $key => $tipo) {

                    $cbrtipo = array();
                    $s_saldo = 0;
                    $s_cobrado = 0;
                    $s_total = 0;
                    $d_saldo = 0;
                    $d_cobrado = 0;
                    $d_total = 0;

                    foreach ($tipoCobro as $i => $cobro) {
                        $cbrtipo[$cobro] = $this->getTipoCobro($filtros,$moneda,$i,$tipo);
                        $s_saldo += $cbrtipo[$cobro]["s_saldo"];
                        $s_cobrado += $cbrtipo[$cobro]["s_cobrado"];
                        $s_total += $cbrtipo[$cobro]["s_total"];
                        $d_saldo += $cbrtipo[$cobro]["d_saldo"];
                        $d_cobrado += $cbrtipo[$cobro]["d_cobrado"];
                        $d_total += $cbrtipo[$cobro]["d_total"];
                    }
                    $res = array("s_saldo" => $s_saldo,
                                    "s_cobrado" => $s_cobrado,
                                    "s_total" => $s_total,
                                    "d_saldo" => $d_saldo,
                                    "d_cobrado" => $d_cobrado,
                                    "d_total" => $d_total,
                                );
                    $dataTipo[$tipo] = array_merge($cbrtipo,array("res"=>$res));
                }
            }
            echo json_encode($dataTipo);
            exit(0);
        }
        $datos['titulo'] = "Informe de deudas";
        $datos = array_merge($datos, $this->get_filtros());
        $this->load->library('Ssp');
        $this->load->library('Cssjs');
        $this->cssjs->add_js(base_url().'assets/js/Reporte/informe_deudas.js?v=1.2',false,false);
        $this->load->view('header');
        $this->load->view('menu');
        $this->load->view('Reporte/informe_deudas', $datos);
        $this->load->view('footer');
    }
	/*
	public function repo_informeDeudas3(){
        $search = (isset($_POST['search'])?$_POST['search']:"");
        $treserva = (isset($_POST['treserva'])?$_POST['treserva']:"LOCAL");
        $tcontacto = (isset($_POST['tcontacto'])?$_POST['tcontacto']:"liqu");
        $desde = (isset($_POST['desde'])?$_POST['desde']:date('Y-m-d',time()-24*60*60*7));
        $hasta = (isset($_POST['hasta'])?$_POST['hasta']:date('Y-m-d'));
        $datos["form"] = array("search" => $search,
                                "treserva" => $treserva,
                                "tcontacto" => $tcontacto,
                                "desde" => $desde,
                                "hasta" => $hasta
                            );
        $this->db->select("clie_rsocial nombre, clie_id id");
        $this->db->from("cliente");
        if($search != "")
            $this->db->like("clie_rsocial",$search);
        if($tcontacto == "comp")
            $this->db->where("clie_facturacion","SI");
        else if($tcontacto == "liqu")
            $this->db->where("clie_liquidacion","SI");
        else{
            $this->db->where(array("clie_liquidacion" => "NO","clie_facturacion" => "NO"));
        }
        $clientes = $this->db->get()->result();
        $monedas = $this->Model_general->enum_valores('paquete','paqu_moneda');
        $clies = array();
        foreach($clientes as $clie){
            $deuda = array();
            $cobrado = array();
            foreach ($monedas as $moneda) {
                if($tcontacto == "liqu"){
                    $tabla = "liquidacion";
                    $cclie = "liqu_clie_id";
                    $ccobrado = "liqu_cobrado";
                    $ctotal = "liqu_total";
                    $cmoneda = "liqu_moneda";
                    $cfecha = "DATE(liqu_fechareg)";
                }else if($tcontacto == "comp"){
                    $tabla = "venta";
                    $cclie = "vent_clie_id";
                    $ccobrado = "vent_cobrado";
                    $ctotal = "vent_total";
                    $cmoneda = "vent_moneda";
                    $cfecha = "vent_fecha";
                }
                if($tcontacto != 'efect'){
                    $this->db->select("SUM(".$ctotal.") total");
                    $this->db->from($tabla);
                    if($tcontacto == "liqu") $this->db->where("liqu_estado", "PAGADO");
                    if($tcontacto == "comp") $this->db->where("vent_escobrado", "1");
                    $this->db->where(array($cfecha." >=" => $desde,
                                            $cfecha." <=" => $hasta,
                                            $cclie => $clie->id,
                                            $cmoneda => $moneda
                                            ));
                    $cex = $this->db->get();
                    $cex = ($cex->num_rows() > 0)?$cex->row()->total:0;

                    $this->db->select("SUM(".$ctotal."-".$ccobrado.") total");
                    $this->db->from($tabla);
                    if($tcontacto == "liqu") $this->db->where("liqu_estado", "PENDIENTE");
                    if($tcontacto == "comp") $this->db->where("vent_escobrado", "0");
                    $this->db->where(array($cfecha." >=" => $desde,
                                            $cfecha." <=" => $hasta,
                                            $cclie => $clie->id,
                                            $cmoneda => $moneda
                                            ));
                    $dex = $this->db->get();
                    $dex = ($dex->num_rows() > 0)?$dex->row()->total:0;
                }else{
                    $dex = 0;
                    $cex = 0;
                }                

                $this->db->select("SUM(paqu_total) total");
                $this->db->from("paquete");
                $this->db->join("paquete_detalle", "deta_paqu_id = paqu_id");
                $this->db->where("paqu_escobrado", "1");
                $this->db->where(array("DATE(deta_fechaserv) >=" => $desde,
                                        "DATE(deta_fechaserv) <=" => $hasta,
                                        "paqu_clie_id" => $clie->id,
                                        "paqu_moneda" => $moneda,
                                        "paqu_escomprobante" => "0",
                                        "paqu_esliquidacion" => "0",
                                        "paqu_estado" => "CONFIRMADO"
                                        ));
                $cpaqu = $this->db->get();
                $cpaqu = ($cpaqu->num_rows() > 0)?$cpaqu->row()->total:0;

                $this->db->select("SUM(paqu_total-paqu_cobrado) total");
                $this->db->from("paquete");
                $this->db->join("paquete_detalle", "deta_paqu_id = paqu_id");
                $this->db->where("paqu_escobrado", "0");
                $this->db->where(array("DATE(deta_fechaserv) >=" => $desde,
                                        "DATE(deta_fechaserv) <=" => $hasta,
                                        "paqu_clie_id" => $clie->id,
                                        "paqu_moneda" => $moneda,
                                        "paqu_escomprobante" => "0",
                                        "paqu_esliquidacion" => "0",
                                        "paqu_estado" => "CONFIRMADO"
                                        ));
                $dpaqu = $this->db->get();
                $dpaqu = ($dpaqu->num_rows() > 0)?$dpaqu->row()->total:0;
                $cobrado[$moneda] = $cpaqu+$cex;
                $deuda[$moneda] = $dpaqu+$dex;
            }
            $s_saldo = $deuda["SOLES"];
            $s_cobrado = $cobrado["SOLES"];
            $s_total = $s_saldo + $s_cobrado;

            $d_saldo = $deuda["DOLARES"];
            $d_cobrado = $cobrado["DOLARES"];
            $d_total = $d_saldo + $d_cobrado;
            if($s_saldo != 0 || $s_cobrado != 0 || $d_saldo != 0 || $d_cobrado != 0)
                $clies[] = array("s_saldo" => number_format($s_saldo, 2, '.', ' '),
                                "s_cobrado" => number_format($s_cobrado, 2, '.', ' '),
                                "s_total" => number_format($s_total, 2, '.', ' '),
                                "d_saldo" => number_format($d_saldo, 2, '.', ' '),
                                "d_cobrado" => number_format($d_cobrado, 2, '.', ' '),
                                "d_total" => number_format($d_total, 2, '.', ' '),
                                "cliente" => $clie->nombre,
                                "clie_id" => $clie->id
                            );
        }
        $datos["clientes"] = $clies;
        $datos['titulo'] = "Informe de deudas Beta";
        $datos['treserv'] = $this->Model_general->enum_valores('paquete','paqu_tipo');
        $datos['tcont'] = array("comp" => "COMPROBANTE", "liqu" => "LIQUIDACION", "efect" => "EFECTIVO");
        $datos = array_merge($datos, $this->get_filtros());
        
        $this->load->library('Ssp');
        $this->load->library('Cssjs');
        $this->cssjs->add_js(base_url().'assets/js/Reporte/informe_deudas3.js?v=1.4',false,false);
        $this->load->view('header');
        $this->load->view('menu');
        $this->load->view('Reporte/informe_deudas2', $datos);
        $this->load->view('footer');
    }
	*/
	public function repo_informeDeudas3(){
        $search = (isset($_POST['search'])?$_POST['search']:"");
        $treserva = (isset($_POST['treserva'])?$_POST['treserva']:"LOCAL");
        $tcontacto = (isset($_POST['tcontacto'])?$_POST['tcontacto']:"liqu");
        $desde = (isset($_POST['desde'])?$_POST['desde']:date('Y-m-d',time()-24*60*60*7));
        $hasta = (isset($_POST['hasta'])?$_POST['hasta']:date('Y-m-d'));
        $datos["form"] = array("search" => $search,
                                "treserva" => $treserva,
                                "tcontacto" => $tcontacto,
                                "desde" => $desde,
                                "hasta" => $hasta
                            );
        $this->db->select("clie_rsocial nombre, clie_id id");
        $this->db->from("cliente");
		if($treserva != "")									//condicion agregado ultimo
            $this->db->where("clie_reporte",$treserva);
        if($search != "")
            $this->db->like("clie_rsocial",$search);
        if($tcontacto == "comp")
            $this->db->where("clie_facturacion","SI");
        else if($tcontacto == "liqu")
            $this->db->where("clie_liquidacion","SI");
        else{
            $this->db->where(array("clie_liquidacion" => "NO","clie_facturacion" => "NO"));
        }
        $clientes = $this->db->get()->result();
		
        $monedas = $this->Model_general->enum_valores('paquete','paqu_moneda');
        $clies = array();
        foreach($clientes as $clie){
            $deuda = array();
            $cobrado = array();
			
            foreach ($monedas as $moneda) {
                $cobrado[$moneda] = $this->getDeta($desde, $hasta, $clie->id, $moneda, $tcontacto, 1); // quitamos de los 2 "$treserva"
                $deuda[$moneda] = $this->getDeta($desde, $hasta, $clie->id, $moneda, $tcontacto, 0);
            }
            $s_saldo = $deuda["SOLES"];
            $s_cobrado = $cobrado["SOLES"];
            $s_total = $s_saldo + $s_cobrado;

            $d_saldo = $deuda["DOLARES"];
            $d_cobrado = $cobrado["DOLARES"];
            $d_total = $d_saldo + $d_cobrado;
            if($s_saldo != 0 || $s_cobrado != 0 || $d_saldo != 0 || $d_cobrado != 0)
                $clies[] = array("s_saldo" => number_format($s_saldo, 2, '.', ''),
                                "s_cobrado" => number_format($s_cobrado, 2, '.', ''),
                                "s_total" => number_format($s_total, 2, '.', ''),
                                "d_saldo" => number_format($d_saldo, 2, '.', ''),
                                "d_cobrado" => number_format($d_cobrado, 2, '.', ''),
                                "d_total" => number_format($d_total, 2, '.', ''),
                                "cliente" => $clie->nombre,
                                "clie_id" => $clie->id
                            );
        }
        $datos["clientes"] = $clies;
        $datos['titulo'] = "Informe de deudas";
        $datos['treserv'] = $this->Model_general->enum_valores('cliente','clie_reporte');
        $datos['tcont'] = array("comp" => "COMPROBANTE", "liqu" => "LIQUIDACION", "efect" => "EFECTIVO");
        $datos = array_merge($datos, $this->get_filtros());
        
        $this->load->library('Ssp');
        $this->load->library('Cssjs');
        $this->cssjs->add_js(base_url().'assets/js/Reporte/informe_deudas3.js?v=1.8',false,false);
        $this->load->view('header');
        $this->load->view('menu');
        $this->load->view('Reporte/informe_deudas2', $datos);
        $this->load->view('footer');
    }
	//se modifico practicamente todo para los nuevos reportes
	/*
	public function getDeta($desde, $hasta, $clie_id, $moneda, $tcontacto, $estado, $treserva){
        $condiciones = array("DATE(deta_fechaserv) >=" => $desde,
                                "DATE(deta_fechaserv) <=" => $hasta,
                                "paqu_clie_id" => $clie_id,
                                "paqu_moneda" => $moneda,
                                "paqu_tipo" => $treserva,
                                "paqu_estado" => "CONFIRMADO"
                            );
        
        if($tcontacto == 'liqu' || $tcontacto == 'comp'){
            
            if($tcontacto == "liqu")
                $condiciones2 = array("paqu_esliquidacion" => 1);
            else if($tcontacto == 'comp')
                $condiciones2 = array("paqu_escomprobante" => 1);
                
            if($estado == 1) $this->db->select("SUM(paqu_total) total");
            else $this->db->select("SUM(paqu_total - paqu_cobrado) total");
            $this->db->from("paquete");
            $this->db->join("paquete_detalle", "deta_paqu_id = paqu_id");
            $this->db->where("paqu_escobrado", $estado);
            $this->db->where($condiciones);
            $this->db->where(array("paqu_escomprobante" => 0,"paqu_esliquidacion" => 0));
            $epaqu = $this->db->get();
            $epaqu = ($epaqu->num_rows() > 0)?$epaqu->row()->total:0;
        }else if($tcontacto == 'efect'){
            $condiciones2 = array("paqu_escomprobante" => 0,"paqu_esliquidacion" => 0);
            $epaqu = 0;
        }

        if($estado == 1) $this->db->select("SUM(paqu_total) total");
        else $this->db->select("SUM(paqu_total - paqu_cobrado) total");
        $this->db->from("paquete");
        $this->db->join("paquete_detalle", "deta_paqu_id = paqu_id");
        $this->db->where("paqu_escobrado", $estado);
        $this->db->where($condiciones);
        $this->db->where($condiciones2);
        $cpaqu = $this->db->get();
        $cpaqu = ($cpaqu->num_rows() > 0)?$cpaqu->row()->total:0;


        return $cpaqu+$epaqu;
    }
	*/
	public function getDeta($desde, $hasta, $clie_id, $moneda, $tcontacto, $estado){
        if($tcontacto == "liqu"){
            $tabla = "liquidacion";
            $cclie = "liqu_clie_id";
            $ccobrado = "liqu_cobrado";
            $ctotal = "liqu_total";
            $cmoneda = "liqu_moneda";
            $cfecha = "DATE(liqu_fechareg)";
            $estadop = ($estado == 1)?"PAGADO":"PENDIENTE";
			$cescobrado = "liqu_estado";
			
			if($estado == 1) $this->db->select("SUM(vent_cobrado) total");
			else $this->db->select("SUM(vent_total - vent_cobrado) total");
			$this->db->from("venta");
			$this->db->where(array("vent_fecha >=" => $desde,
									"vent_fecha <=" => $hasta,
									"vent_clie_id" => $clie_id,
									"vent_moneda" => $moneda
									));
			$adicional = $this->db->get();
			$adicional = ($adicional->num_rows() > 0)?$adicional->row()->total:0;
        }else if($tcontacto == "comp"){
            $tabla = "venta";
            $cclie = "vent_clie_id";
            $ccobrado = "vent_cobrado";
            $ctotal = "vent_total";
            $cmoneda = "vent_moneda";
			$cfecha = "vent_fecha";
            $estadop = $estado;
			$cescobrado = "vent_escobrado";
			
			if($estado == 1) $this->db->select("SUM(liqu_cobrado) total");
			else $this->db->select("SUM(liqu_total - liqu_cobrado) total");
			$this->db->from("liquidacion");
			$this->db->where(array("DATE(liqu_fechareg) >=" => $desde,
									"DATE(liqu_fechareg) <=" => $hasta,
									"liqu_clie_id" => $clie_id,
									"liqu_moneda" => $moneda
									));
			$adicional = $this->db->get();
			$adicional = ($adicional->num_rows() > 0)?$adicional->row()->total:0;
        }
        if($tcontacto != 'efect'){
			if($estado == 1) $this->db->select("SUM(".$ccobrado.") total");
			else $this->db->select("SUM(".$ctotal."-".$ccobrado.") total");
            $this->db->from($tabla);
            //$this->db->where($cescobrado, $estadop);
            $this->db->where(array($cfecha." >=" => $desde,
                                    $cfecha." <=" => $hasta,
                                    $cclie => $clie_id,
                                    $cmoneda => $moneda
                                    ));
            $cex = $this->db->get();
            $cex = ($cex->num_rows() > 0)?$cex->row()->total:0;
        }else{
            $cex = 0;
			$adicional = 0;
        }                
		
		if($estado == 1) {
			$select = "SUM(paqu_cobrado) total";
			//$this->db->select("SUM(paqu_total) total");	
		}else {
			$select = "SUM(paqu_total - paqu_cobrado) total";
			//$this->db->select("SUM(paqu_total - paqu_cobrado) total");
		}
		/*
        $this->db->from("paquete");
        $this->db->join("paquete_detalle", "deta_paqu_id = paqu_id");
        $this->db->where("paqu_escobrado", $estado);
        $this->db->where(array("DATE(deta_fechaserv) >=" => $desde,
                                "DATE(deta_fechaserv) <=" => $hasta,
                                "paqu_clie_id" => $clie_id,
                                "paqu_moneda" => $moneda,
                                "paqu_escomprobante" => "0",
                                "paqu_esliquidacion" => "0",
                                "paqu_estado" => "CONFIRMADO"
                                ));
		$this->db->group_by("paqu_id");
        $cpaqu = $this->db->get();
		*/
		$cpaqu = $this->db->query("SELECT ".$select." from paquete 
											where paqu_id IN (select deta_paqu_id from paquete_detalle where DATE(deta_fechaserv) >= '{$desde}' and DATE(deta_fechaserv) <= '{$hasta}' group by deta_paqu_id)
											AND paqu_clie_id = {$clie_id} AND paqu_moneda = '{$moneda}' AND paqu_escomprobante = 0 AND paqu_esliquidacion = 0
											AND paqu_estado = 'CONFIRMADO'");
        $cpaqu = ($cpaqu->num_rows() > 0)?$cpaqu->row()->total:0;
		

        return $cpaqu+$cex+$adicional;
    }
	/*
    public function getDeta($desde, $hasta, $clie_id, $moneda, $tcontacto, $estado, $treserva){
        if($estado == 1){
            $this->db->select("SUM(paqu_total) total");
        }else{
            $this->db->select("SUM(paqu_total - paqu_cobrado) total");
        }
        $this->db->from("paquete");
        $this->db->join("paquete_detalle", "deta_paqu_id = paqu_id");
        $this->db->where("paqu_escobrado", $estado);
        $this->db->where(array("DATE(deta_fechaserv) >=" => $desde,
                                "DATE(deta_fechaserv) <=" => $hasta,
                                "paqu_clie_id" => $clie_id,
                                "paqu_moneda" => $moneda,
                                "paqu_tipo" => $treserva,
                                "paqu_estado" => "CONFIRMADO"
                                ));
        if($tcontacto == "liqu"){
            $this->db->where("paqu_esliquidacion",1);
        }else if($tcontacto == 'comp'){
            $this->db->where("paqu_escomprobante",1);
        }else if($tcontacto == 'efect'){
            $this->db->where(array("paqu_escomprobante" => 0,"paqu_esliquidacion" => 0));
        }
        $cpaqu = $this->db->get();
        $cpaqu = ($cpaqu->num_rows() > 0)?$cpaqu->row()->total:0;
        return $cpaqu;
    }
	*/
	function deta_informeDeudas($tcontacto, $id, $desde, $hasta){
        if($tcontacto == "liqu"){

            /* $this->db->select("CONCAT('LIQ-',liqu_numero) file, DATE_FORMAT(liqu_fechareg, '%d/%m/%Y') fecha
                                liqu_total total, liqu_moneda moneda, 
                                IF(liqu_estado = 'PENDIENTE',
                                    CONCAT('<font class=red><b>',(liqu_total - liqu_cobrado),'</b></font>'),
                                    CONCAT('<font class=green><b>',IF((liqu_total - liqu_cobrado) < 0,
                                                                    (liqu_total - liqu_cobrado),
                                                                    'COBRADO')),'</b></font>') saldo"); */
            $this->db->select("CONCAT('LIQ-',liqu_numero) file, DATE_FORMAT(liqu_fechareg, '%d/%m/%Y') fecha,
                                liqu_total total, liqu_moneda moneda, liqu_cobrado cobrado, (liqu_total - liqu_cobrado) saldo");
            $this->db->from('liquidacion');
            $this->db->where(array('liqu_clie_id' => $id, "liqu_estado" => "PENDIENTE"));
            $this->db->where('DATE(liqu_fechareg) BETWEEN "'.$desde.'" AND "'.$hasta.'"');
            $comprobantes = $this->db->get()->result();

        }else if($tcontacto == "comp"){
            $this->db->select("CONCAT(vent_serie,'-',vent_numero) file, DATE_FORMAT(vent_fecha, '%d/%m/%Y') fecha,
                                vent_total total, vent_moneda moneda, vent_cobrado cobrado, (vent_total - vent_cobrado) saldo");
            $this->db->from('venta');
            $this->db->where(array('vent_clie_id' => $id, "vent_escobrado" => "0"));
            $this->db->where('vent_fecha BETWEEN "'.$desde.'" AND "'.$hasta.'"');
            $comprobantes = $this->db->get()->result();
        }else{
            $comprobantes = array();
        }
        $this->db->select("CONCAT(paqu_prefijo,'-',paqu_numero) file, DATE_FORMAT(deta_fechaserv, '%d/%m/%Y') fecha,
                                paqu_total total, paqu_moneda moneda, paqu_cobrado cobrado, (paqu_total - paqu_cobrado) saldo");
        $this->db->from("paquete");
        $this->db->join("paquete_detalle", "deta_paqu_id = paqu_id");
        $this->db->where("paqu_escobrado", "0");
        $this->db->where(array("DATE(deta_fechaserv) >=" => $desde,
                                "DATE(deta_fechaserv) <=" => $hasta,
                                "paqu_clie_id" => $id,
                                "paqu_escomprobante" => "0",
                                "paqu_esliquidacion" => "0",
                                "paqu_estado" => "CONFIRMADO"));
		$this->db->group_by("paqu_id");
        $paquetes = $this->db->get()->result();
        $table = "<table class='table table-striped table-hover table-bordered'>";
        $table .= "<thead><tr><th>FILE</th><th>MONEDA</th><th>COBRADO</th><th>SALDO</th><th>TOTAL</th></tr></thead><tbody>";
        
        if(COUNT($comprobantes) > 0){
            foreach($comprobantes as $row){
                $table .= "<tr>";
                $table .= "<td>".$row->file."</td>";
                $table .= "<td>".$row->moneda."</td>";
                $table .= "<td>".$row->cobrado."</td>";
                $table .= "<td>".$row->saldo."</td>";
                $table .= "<td>".$row->total."</td>";
                $table .= "</tr>";
            }
        }
        if(COUNT($paquetes) > 0){
            foreach($paquetes as $row){
                $table .= "<tr>";
                $table .= "<td>".$row->file."</td>";
                $table .= "<td>".$row->moneda."</td>";
                $table .= "<td>".$row->cobrado."</td>";
                $table .= "<td>".$row->saldo."</td>";
                $table .= "<td>".$row->total."</td>";
                $table .= "</tr>";
            }
        }
        $table .= "</tbody></table>";
        $data["table"] = $table;
        $this->load->view('Reporte/deta_informeDeudas', $data);
    }
    public function getTipoCobro($filtros, $moneda,$cobro,$tipo){
        if($cobro == 1){
            $filtros = $filtros." AND paqu_escomprobante = 1";
        }else if($cobro == 2){
            $filtros = $filtros." AND paqu_esliquidacion = 1";
        }else if($cobro == 3){
            $filtros = $filtros." AND paqu_esliquidacion = 0 AND paqu_escomprobante = 0";
        }
        $filtros = $filtros." AND paqu_tipo = '".$tipo."'";
        $clientes = $this->db->query("SELECT paqu_clie_id, paqu_clie_rsocial, SUM(paqu_total) suma FROM paquete join paquete_detalle on deta_paqu_id = paqu_id WHERE ".$filtros." GROUP BY paqu_clie_id")->result();

        $fs_saldo = 0;
        $fs_cobrado = 0;
        $fs_total = 0;
        $fd_saldo = 0;
        $fd_cobrado = 0;
        $fd_total = 0;  
        $clies = array();
        foreach ($clientes as $i => $clie) {
            $deuda = array();
            $cobrado = array();
            foreach ($moneda as $mone) {
                $deuda[$mone] = $this->db->query("SELECT SUM(paqu_total) as total FROM paquete join paquete_detalle on deta_paqu_id = paqu_id WHERE paqu_clie_id = $clie->paqu_clie_id AND paqu_moneda = '{$mone}'AND paqu_escobrado = 0 ".($filtros==1?"":" AND ".$filtros))->row();    
                $cobrado[$mone] = $this->db->query("SELECT SUM(paqu_total) as total FROM paquete join paquete_detalle on deta_paqu_id = paqu_id WHERE paqu_clie_id = $clie->paqu_clie_id AND paqu_moneda = '{$mone}' AND paqu_escobrado = 1 ".($filtros==1?"":" AND ".$filtros))->row();
            }

            $s_saldo = ($deuda["SOLES"]->total)?$deuda['SOLES']->total:'0.00';
            $s_cobrado = ($cobrado["SOLES"]->total)?$cobrado['SOLES']->total:'0.00';
            $s_total = $s_saldo + $s_cobrado;

            $d_saldo = ($deuda["DOLARES"]->total)?$deuda['DOLARES']->total:'0.00';
            $d_cobrado = ($cobrado["DOLARES"]->total)?$cobrado['DOLARES']->total:'0.00';

            $d_total = $d_saldo + $d_cobrado;

            $fs_saldo += floatval($s_saldo);
            $fs_cobrado += floatval($s_cobrado);
            $fs_total += floatval($s_total);
            $fd_saldo += floatval($d_saldo);
            $fd_cobrado += floatval($d_cobrado);
            $fd_total += floatval($d_total);

            $clies[] = array("s_saldo" => $s_saldo,
                            "s_cobrado" => $s_cobrado,
                            "s_total" => $s_total,
                            "d_saldo" => $d_saldo,
                            "d_cobrado" => $d_cobrado,
                            "d_total" => $d_total,
                            "cliente" => $clie->paqu_clie_rsocial,
                            "suma" => $clie->suma
                        );
        }
        $res = array("clies" => $clies,
                        "s_saldo" => $fs_saldo,
                        "s_cobrado" => $fs_cobrado,
                        "s_total" => $fs_total,
                        "d_saldo" => $fd_saldo,
                        "d_cobrado" => $fd_cobrado,
                        "d_total" => $fd_total
                    );
        return $res;
    }
    public function repo_logs() {
        $this->load->helper('Funciones');
        $this->load->database();
        $this->load->library('Ssp');
        $this->load->library('Cssjs');

        $json = isset($_GET['json']) ? $_GET['json'] : false;
        $fecha = "DATE_FORMAT(log_fecha,'%d/%m/%Y  %h:%i %p')";
        $columns = array(
            array('db' => 'log_id',  'dt' => 'ID',      "field" => "log_id"),
            array('db' => 'usua_nombres',         'dt' => 'USUARIO',        "field" => "usua_nombres"),
            array('db' => 'log_accion',         'dt' => 'ACCION',        "field" => "log_accion"),
            array('db' => 'mod_nombre',        'dt' => 'MODULO',       "field" => "mod_nombre"),            
            array('db' => $fecha,      'dt' => 'FECHA',     "field" => $fecha),    
            array('db' => 'log_descripcion',       'dt' => 'Usuario',      "field" => "log_descripcion"),            
            array('db' => 'log_id',            'dt' => 'DT_RowId',     "field" => "log_id"),
        );
        
        $mon = '';

        if ($json) {

            $json = isset($_GET['json']) ? $_GET['json'] : false;

            $table = 'log';
            $primaryKey = 'log_id';

            $sql_details = array(
                'user' => $this->db->username,
                'pass' => $this->db->password,
                'db' => $this->db->database,
                'host' => $this->db->hostname
            );

            $condiciones = array();

            $joinQuery = "FROM log JOIN usuario ON usua_id=log_user_id JOIN modulo ON mod_id = log_modulo";
            $where = "";
            if (!empty($_POST['desde'])&&!empty($_POST['hasta'])){
                $condiciones[] = "log_fecha >='".$_POST['desde']." 00:00:00"."' AND log_fecha <='".$_POST['hasta']." 23:59:00"."'";
            }
            if (!empty($_POST['usuario']))
                $condiciones[] = "log_user_id='".$_POST['usuario']."'";
			if (!empty($_POST['modulo']))
                $condiciones[] = "log_modulo='".$_POST['modulo']."'";
            
            $where = count($condiciones) > 0 ? implode(' AND ', $condiciones) : "";
            echo json_encode(
                    $this->ssp->simple($_POST, $sql_details, $table, $primaryKey, $columns, $joinQuery, $where)
            );
            exit(0);
        }
        
        $datos['columns'] = $columns;
		$datos["usuarios"] = $this->Model_general->getOptions('usuario', array("usua_id", "usua_nombres"),'* Usuario');
		$datos["modulos"] = $this->Model_general->getOptions('modulo', array("mod_id", "mod_nombre"),'* Modulos');
        $datos['titulo'] = "Comprobantes";

        $this->cssjs->set_path_js(base_url() . "assets/js/Reporte/");
        $this->cssjs->add_js('list_log');
        $script['js'] = $this->cssjs->generate_js();
        $this->load->view('header', $script);
        $this->load->view('menu');
        $this->load->view('Reporte/list_log', $datos);
        $this->load->view('footer');
    }
	/*
    public function repo_informeGastos($json =  false){
        if($json){
            $search = $this->input->post("search");
            $desde = $this->input->post("desde");
            $hasta = $this->input->post("hasta");
            $filtros = array();

            if($search != "")
                $filtros[] = "prov_rsocial LIKE '%".$search."%'";
            if($desde != "" && $hasta != "")
                $filtros[] = "sepr_fecha >='".$_POST['desde']."' AND sepr_fecha <='".$_POST['hasta']."'";

            $filtros = (COUNT($filtros) > 0)?implode(" AND ", $filtros):1;
            
            $proveedores = $this->db->query("SELECT prov_id, prov_rsocial FROM proveedor LEFT JOIN servicio_proveedor ON sepr_prov_id = prov_id WHERE ".$filtros." GROUP BY prov_id")->result();

            $moneda = $this->Model_general->enum_valores('servicio_proveedor','sepr_moneda');
            $tipos = $this->Model_general->enum_valores('proveedor_empresa','emp_tipo');
            $tipoServ = $this->Model_general->getOptions("proveedor_tipo",array("tipo_id","tipo_denom"));
            
            if(COUNT($proveedores) > 0){
                $dataTipo = array();
                foreach ($tipos as $key => $tipo) {

                    $cbrtipo = array();
                    $s_saldo = 0;
                    $s_cobrado = 0;
                    $s_total = 0;
                    $d_saldo = 0;
                    $d_cobrado = 0;
                    $d_total = 0;

                    foreach ($tipoServ as $i => $stipo) {
                        $cbrtipo[$stipo] = $this->getGastos($filtros,$moneda,$i,$tipo);
                        $s_saldo += $cbrtipo[$stipo]["s_saldo"];
                        $s_cobrado += $cbrtipo[$stipo]["s_cobrado"];
                        $s_total += $cbrtipo[$stipo]["s_total"];
                        $d_saldo += $cbrtipo[$stipo]["d_saldo"];
                        $d_cobrado += $cbrtipo[$stipo]["d_cobrado"];
                        $d_total += $cbrtipo[$stipo]["d_total"];
                    }
                    $res = array("s_saldo" => $s_saldo,
                                    "s_cobrado" => $s_cobrado,
                                    "s_total" => $s_total,
                                    "d_saldo" => $d_saldo,
                                    "d_cobrado" => $d_cobrado,
                                    "d_total" => $d_total,
                                );
                    $dataTipo[$tipo] = array_merge($cbrtipo,array("res"=>$res));
                }
            }
            echo json_encode($dataTipo);
            exit(0);
        }
        $datos['titulo'] = "Informe de gastos";
        $this->load->library('Ssp');
        $this->load->library('Cssjs');
        $this->cssjs->add_js(base_url().'assets/js/Reporte/informe_gastos.js?v=1.2',false,false);
        $this->load->view('header');
        $this->load->view('menu');
        $this->load->view('Reporte/informe_gastos', $datos);
        $this->load->view('footer');
    }
	*/
	public function repo_informeGastos2(){
        $desde = (isset($_POST['desde'])?$_POST['desde']:date('Y-m-d',time()-24*60*60*7));
        $hasta = (isset($_POST['hasta'])?$_POST['hasta']:date('Y-m-d'));
        $tipo = (isset($_POST['tipo'])?$_POST['tipo']:'TERCERO');
        $spropio = (isset($_POST['spropio'])?$_POST['spropio']:'1');
        $stercero = (isset($_POST['spropio'])?$_POST['stercero']:'1');
        $sub = ($tipo == 'PROPIO') ? $spropio : $stercero;
        
        $datos["form"] = array("desde" => $desde,
                                "hasta" => $hasta,
                                "tipo" => $tipo,
                                "spropio" => $spropio,
                                "stercero" => $stercero
                            );
        
        
        if($tipo == "PROPIO" && $sub == ""){
            $time1=strtotime($desde);
            $time2=strtotime($hasta);
            $mes1 = intval(date("m",$time1));
            $anio1 = intval(date("Y",$time1));
            $mes2 = intval(date("m",$time2));
            $anio2 = intval(date("Y",$time2));
            
            $filtros = array("peri_mes >= " => $mes1,
                                "peri_mes <= " => $mes2,
                                "peri_anio >= " => $anio1,
                                "peri_anio <= " => $anio2,
                            );

            $this->db->select("emp_nombres nombre, emp_id id");
            $this->db->from("planilla");
            $this->db->join("planilla_periodo","peri_id = plan_peri_id");
            $this->db->join("planilla_empleado","emp_id = plan_emp_id");
            $this->db->group_by("plan_emp_id");
            $this->db->where($filtros);
            $empleados = $this->db->get()->result();

            $detalles = array();

            if(COUNT($empleados) > 0){
                foreach ($empleados as $i => $row) {
                    $cobrado = $this->getDetaGastoEmpleado($filtros, $row->id, 1);
                    $saldo = $this->getDetaGastoEmpleado($filtros, $row->id, 0);
                    $total = $saldo + $cobrado;
                    if($cobrado != 0 || $saldo != 0){
                        $detalles[] = array("s_saldo" => number_format($saldo, 2, '.', ''),
                                        "s_cobrado" => number_format($cobrado, 2, '.', ''),
                                        "s_total" => number_format($total, 2, '.', ''),
                                        "d_saldo" => number_format(0, 2, '.', ''),
                                        "d_cobrado" => number_format(0, 2, '.', ''),
                                        "d_total" => number_format(0, 2, '.', ''),
                                        "proveedor" => $row->nombre,
                                        "prov_id" => $row->id
                                    );
                    }
                }
            }
        }else{
            if($tipo == "COMB"){
                $filtros = array("sepr_fecha >= " => $desde,
                                "sepr_fecha <=" => $hasta,
                                "prov_combustible" => "SI"
                            );
                $this->db->select("prov_id id, prov_rsocial nombre");
                $this->db->from("proveedor");
                $this->db->join("servicio_proveedor", "sepr_prov_id = prov_id", "LEFT");
                $this->db->where($filtros);
                $proveedores = $this->db->group_by("sepr_prov_id")->get()->result();    
            }else{
                $filtros = array("sepr_fecha >= " => $desde,
                                "sepr_fecha <=" => $hasta,
                                "sepr_tipo" => $sub,
                                "emp_tipo" => $tipo,
                                "prov_combustible" => "NO"
                            );

                $this->db->select("prov_id id, prov_rsocial nombre");
                $this->db->from("proveedor");
                $this->db->join("proveedor_empresa","emp_id = prov_emp_id");
                $this->db->join("servicio_proveedor", "sepr_prov_id = prov_id", "LEFT");
                $this->db->where($filtros);
                $proveedores = $this->db->group_by("sepr_prov_id")->get()->result();    
            }
            
            $monedas = $this->Model_general->enum_valores('paquete','paqu_moneda');
            $detalles = array();
            
            foreach($proveedores as $prov){
                $deuda = array();
                $cobrado = array();
                foreach ($monedas as $moneda) {
                    $cobrado[$moneda] = $this->getDetaGasto($filtros, $prov->id, $moneda, 1, $tipo);
                    $deuda[$moneda] = $this->getDetaGasto($filtros, $prov->id, $moneda, 0, $tipo);
                }
                $s_saldo = $deuda["SOLES"];
                $s_cobrado = $cobrado["SOLES"];
                $s_total = $s_saldo + $s_cobrado;

                $d_saldo = $deuda["DOLARES"];
                $d_cobrado = $cobrado["DOLARES"];
                $d_total = $d_saldo + $d_cobrado;
                if($s_saldo != 0 || $s_cobrado != 0 || $d_saldo != 0 || $d_cobrado != 0){
                    $detalles[] = array("s_saldo" => number_format($s_saldo, 2, '.', ''),
                                    "s_cobrado" => number_format($s_cobrado, 2, '.', ''),
                                    "s_total" => number_format($s_total, 2, '.', ''),
                                    "d_saldo" => number_format($d_saldo, 2, '.', ''),
                                    "d_cobrado" => number_format($d_cobrado, 2, '.', ''),
                                    "d_total" => number_format($d_total, 2, '.', ''),
                                    "proveedor" => $prov->nombre,
                                    "prov_id" => $prov->id
                                );
                }
            }
        }
        

        $datos['detalles'] = $detalles;
        $datos['titulo'] = "Informe de gastos";
        $datos['tipo'] = array("PROPIO" => "PROPIOS", "TERCERO" => "TERCEROS", "COMB" => "COMBUSTIBLES");
        $datos['spropio'] = $this->Model_general->getOptions("proveedor_tipo",array("tipo_id","tipo_denom"),'Planilla');
        $datos['stercero'] = $this->Model_general->getOptions("proveedor_tipo",array("tipo_id","tipo_denom"));
        $anios = array();
        for ($i=2018; $i <= date('Y'); $i++) { 
            $anios[$i] = $i;
        }
        $datos["anio"] = $anios;
        $datos["mes"] = array(1=>"Enero",2=>"Febrero",3=>"Marzo",4=>"Abril",5=>"Mayo",6=>"Junio",7=>"Julio",8=>"Agosto",9=>"Septiembre",10=>"Octubre",11=>"Niviembre",12=>"Diciembre");

        $this->load->library('Ssp');
        $this->load->library('Cssjs');
        $this->cssjs->add_js(base_url().'assets/js/Reporte/informe_gastos2.js?v=1.4',false,false);
        $this->load->view('header');
        $this->load->view('menu');
        $this->load->view('Reporte/informe_gastos2', $datos);
        $this->load->view('footer');
    }
    public function getDetaGasto($filtros, $prov_id, $moneda, $estado, $tipo){
        if($tipo == "COMB"){
            if($estado == 1) $this->db->select("SUM(sepr_combu_total) total, sepr_prov_id");
            else $this->db->select("SUM(sepr_combu_total-sepr_pagado) total, sepr_prov_id");
        }else{
            if($estado == 1) $this->db->select("SUM(sepr_total) total, sepr_prov_id");
            else $this->db->select("SUM(sepr_total-sepr_pagado) total, sepr_prov_id");    
        }
        $this->db->from("servicio_proveedor");
        $this->db->join("proveedor", "prov_id = sepr_prov_id");
        if($tipo != "COMB")
            $this->db->join("proveedor_empresa","emp_id = prov_emp_id");
        $this->db->where(array("sepr_prov_id" => $prov_id, "sepr_moneda" => $moneda, "sepr_espagado" => $estado));
        $this->db->where($filtros);
        $res = $this->db->get()->row();
        return ($res->total != "") ? $res->total : 0;
    }
    public function getDetaGastoEmpleado($filtros, $emp_id, $estado){
        $this->db->select("SUM(plan_remuTotal) total");
        $this->db->from("planilla");
        $this->db->join("planilla_periodo","peri_id = plan_peri_id");
        $this->db->where(array("plan_emp_id" => $emp_id, "plan_espagado" => $estado));
        $this->db->where($filtros);
        $this->db->group_by("plan_emp_id");
        $res = $this->db->get();

        if($res->num_rows() > 0)
            return ($res->row()->total != "") ? $res->row()->total : 0;
        else return 0;
    }
    public function deta_informeGastos(){
        $tipo = $this->input->get("tipo");
        $id = $this->input->get("id");
        $desde = $this->input->get("desde");
        $hasta = $this->input->get("hasta");
        $spropio = $this->input->get("spropio");
        $stercero = $this->input->get("stercero");

        $html = "";

        if($tipo == "COMB"){
            $this->db->select("IF(sepr_pdet_id IS NOT NULL,CONCAT(paqu_prefijo,'-',paqu_numero), CONCAT('ORD-', orde_numero)) file, sepr_moneda moneda, sepr_pagado pagado, sepr_combu_total total, (sepr_combu_total - sepr_pagado) saldo");
            $this->db->from("servicio_proveedor");
            $this->db->join("ordenserv","orde_id = sepr_orde_id","LEFT");
            $this->db->join("paquete_detalle","deta_id = sepr_pdet_id","LEFT");
            $this->db->join("paquete","paqu_id = deta_paqu_id","LEFT");
            $this->db->where(array("sepr_prov_id" => $id,
                                    "sepr_fecha >= " => $desde,
                                    "sepr_fecha <= " => $hasta));
            $detalles = $this->db->get()->result();
        }else{
            $stipo = ($tipo == 'PROPIO') ? $spropio : $stercero;
            if($tipo == 'PROPIO' && $stipo == ""){
                $time1=strtotime($desde);
                $time2=strtotime($hasta);
                $mes1 = intval(date("m",$time1));
                $anio1 = intval(date("Y",$time1));
                $mes2 = intval(date("m",$time2));
                $anio2 = intval(date("Y",$time2));
                $filtros = array("plan_emp_id" => $id,
                                    "peri_mes >= " => $mes1,
                                    "peri_mes <= " => $mes2,
                                    "peri_anio >= " => $anio1,
                                    "peri_anio <= " => $anio2
                                );

                $this->db->select("CONCAT(peri_mesName,'-',peri_anio) file, 'SOLES' as moneda, plan_remuTotal total, IF(plan_espagado = 1,plan_remuTotal,0) pagado, IF(plan_espagado = 0,plan_remuTotal,0) saldo");
                $this->db->from("planilla");
                $this->db->join("planilla_periodo","peri_id = plan_peri_id");
                $this->db->where($filtros);
                $detalles = $this->db->get()->result();
            }else{
                $this->db->select("IF(sepr_pdet_id IS NOT NULL,CONCAT(paqu_prefijo,'-',paqu_numero), CONCAT('ORD-', orde_numero)) file, sepr_moneda moneda, sepr_pagado pagado, sepr_total total, (sepr_total - sepr_pagado) saldo");
                $this->db->from("servicio_proveedor");
                $this->db->join("ordenserv","orde_id = sepr_orde_id","LEFT");
                $this->db->join("paquete_detalle","deta_id = sepr_pdet_id","LEFT");
                $this->db->join("paquete","paqu_id = deta_paqu_id","LEFT");
                $this->db->join("proveedor","prov_id = sepr_prov_id","LEFT");
                $this->db->join("proveedor_empresa","emp_id = prov_emp_id","LEFT");
                $this->db->where(array("sepr_prov_id" => $id,
                                        "sepr_fecha >= " => $desde,
                                        "sepr_fecha <= " => $hasta,
                                        "sepr_tipo" => $stipo,
                                        "emp_tipo" => $tipo));
                $detalles = $this->db->get()->result();
            }
        }

        if(COUNT($detalles) > 0){
            foreach ($detalles as $i => $row) {
                $html .= "<tr>";
                $html .= "<td>".$row->file."</td>";
                $html .= "<td>".$row->moneda."</td>";
                $html .= "<td>".$row->pagado."</td>";
                $html .= "<td>".$row->saldo."</td>";
                $html .= "<td>".$row->total."</td>";
                $html .= "</tr>";
            }
        }
        $data["table"] = $html;
        $this->load->view('Reporte/deta_informeGastos', $data);
    }
    public function getGastos($filtros, $moneda,$cobro,$tipo){
        //$filtros = $filtros == 1?"":$filtros;

        $filtros = $filtros." AND sepr_tipo = {$cobro}";
        $filtros = $filtros." AND emp_tipo = '".$tipo."'";

        $proveedores = $this->db->query("SELECT prov_id, prov_rsocial, SUM(sepr_total) suma FROM servicio_proveedor LEFT JOIN proveedor ON sepr_prov_id = prov_id LEFT JOIN proveedor_empresa ON emp_id = prov_emp_id WHERE ".$filtros." GROUP BY sepr_prov_id")->result();

        $fs_saldo = 0;
        $fs_cobrado = 0;
        $fs_total = 0;
        $fd_saldo = 0;
        $fd_cobrado = 0;
        $fd_total = 0;  
        $clies = array();
        foreach ($proveedores as $i => $prov) {
            $deuda = array();
            $cobrado = array();
            foreach ($moneda as $mone) {
                $deuda[$mone] = $this->db->query("SELECT SUM(sepr_total) as total FROM servicio_proveedor LEFT JOIN proveedor ON sepr_prov_id = prov_id LEFT JOIN proveedor_empresa ON emp_id = prov_emp_id WHERE sepr_prov_id = $prov->prov_id AND sepr_moneda = '{$mone}'AND sepr_espagado = 0 ".($filtros==1?"":" AND ".$filtros))->row();    
                $cobrado[$mone] = $this->db->query("SELECT SUM(sepr_total) as total FROM servicio_proveedor LEFT JOIN proveedor ON sepr_prov_id = prov_id LEFT JOIN proveedor_empresa ON emp_id = prov_emp_id WHERE sepr_prov_id = $prov->prov_id AND sepr_moneda = '{$mone}' AND sepr_espagado = 1 ".($filtros==1?"":" AND ".$filtros))->row();
            }

            $s_saldo = ($deuda["SOLES"]->total)?$deuda['SOLES']->total:'0.00';
            $s_cobrado = ($cobrado["SOLES"]->total)?$cobrado['SOLES']->total:'0.00';
            $s_total = $s_saldo + $s_cobrado;

            $d_saldo = ($deuda["DOLARES"]->total)?$deuda['DOLARES']->total:'0.00';
            $d_cobrado = ($cobrado["DOLARES"]->total)?$cobrado['DOLARES']->total:'0.00';

            $d_total = $d_saldo + $d_cobrado;

            $fs_saldo += floatval($s_saldo);
            $fs_cobrado += floatval($s_cobrado);
            $fs_total += floatval($s_total);
            $fd_saldo += floatval($d_saldo);
            $fd_cobrado += floatval($d_cobrado);
            $fd_total += floatval($d_total);

            $clies[] = array("s_saldo" => $s_saldo,
                            "s_cobrado" => $s_cobrado,
                            "s_total" => $s_total,
                            "d_saldo" => $d_saldo,
                            "d_cobrado" => $d_cobrado,
                            "d_total" => $d_total,
                            "cliente" => $prov->prov_rsocial,
                            "suma" => $prov->suma
                        );
        }
        $res = array("clies" => $clies,
                        "s_saldo" => $fs_saldo,
                        "s_cobrado" => $fs_cobrado,
                        "s_total" => $fs_total,
                        "d_saldo" => $fd_saldo,
                        "d_cobrado" => $fd_cobrado,
                        "d_total" => $fd_total
                    );
        return $res;
    }
	public function repo_gastosGrafico($json = false){
        $this->load->helper('Funciones');
        if($json){

            $tipo = $this->input->get("tipo");
            $mes = $this->input->get("mes");
            $anio = $this->input->get("anio");
            $moneda = $this->input->get("moneda");

            //listado de proveedores que estan en la base de datos
            $total = 0;

            if($tipo == "COMB"){
                $this->db->select("SUM(sepr_combu_total) total, prov_rsocial tipo_denom, prov_id tipo_id");
                $this->db->from("servicio_proveedor");
                $this->db->join("proveedor","prov_id = sepr_prov_id","LEFT");
                $this->db->where(array("sepr_moneda" => $moneda,"prov_combustible" => "SI"));
                if($mes != "")
                    $this->db->where("MONTH(sepr_fecha)", $mes);
                if($anio != "")
                    $this->db->where("YEAR(sepr_fecha)", $anio);
                $this->db->group_by("sepr_prov_id");
                $detalles = $this->db->get()->result();

                if(COUNT($detalles) > 0){
                    foreach ($detalles as $i => $row)
                        $total += floatval($row->total);
                    
                    if($total > 0){
                        foreach ($detalles as $i => $row)
                            $row->porcent = round((($row->total*100)/$total),2);
                    }
                }
            }else{
                $detalles = $this->db->get("proveedor_tipo")->result();

                foreach ($detalles as $i => $row) {
                    $row->total = $this->deta_infoGastos($tipo,$mes,$anio,$row->tipo_id,$moneda);
                    $total += floatval($row->total);
                }
                if($tipo == "PROPIO" && $moneda == "SOLES"){
                    $this->db->select("SUM(plan_remuTotal) total");
                    $this->db->from("planilla");
                    $this->db->join("planilla_periodo","peri_id = plan_peri_id");
                    $this->db->where(array("peri_mes" => $mes, "peri_anio" => $anio));
                    $res = $this->db->get();
                    $plan = array();
                    if($res->num_rows() > 0){
                        if($res->row()->total != ""){
                            $plan["total"] = $res->row()->total;
                            $plan["tipo_denom"] = "Planilla";
                            $plan["tipo_id"] = "plan";
                            array_push($detalles,(object)$plan);
                            $total += intval($res->row()->total);
                        }
                    }
                }else if($tipo == 'TERCERO'){
					$this->db->select("SUM(sepr_combu_total) total");
					$this->db->from("servicio_proveedor");
					$this->db->join("proveedor","prov_id = sepr_prov_id","LEFT");
					$this->db->where(array("sepr_moneda" => $moneda,"prov_combustible" => "SI"));
					if($mes != "")
						$this->db->where("MONTH(sepr_fecha)", $mes);
					if($anio != "")
						$this->db->where("YEAR(sepr_fecha)", $anio);
					$res = $this->db->get();
                    $plan = array();
                    if($res->num_rows() > 0){
                        if($res->row()->total != ""){
                            $plan["total"] = $res->row()->total;
                            $plan["tipo_denom"] = "Combustibles";
                            $plan["tipo_id"] = "plan";
                            array_push($detalles,(object)$plan);
                            $total += intval($res->row()->total);
                        }
                    }
				}

                if($total > 0){
                    foreach ($detalles as $i => $row) {
                        $row->porcent = round((($row->total*100)/$total),2);
                    }    
                }
            }
            $data["detalles"] = $detalles;
            $data["total"] = $total;
            
            echo json_encode($data);
            exit(0);
        }

        for ($i=2018; $i <= date('Y'); $i++) { 
            $anios[$i] = $i;
        }
        $datos["anio"] = $anios;
        $datos["mes"] = array(1=>"Enero",2=>"Febrero",3=>"Marzo",4=>"Abril",5=>"Mayo",6=>"Junio",7=>"Julio",8=>"Agosto",9=>"Septiembre",10=>"Octubre",11=>"Niviembre",12=>"Diciembre");
        $datos["tipo"] = array("PROPIO"=>"PROPIOS", "TERCERO"=>"TERCEROS","COMB" => "COMBUSTIBLES");
        $datos["moneda"] = array("SOLES"=>"SOLES", "DOLARES"=>"DOLARES");
        $datos['sel_mes'] = date("m");
        $datos['sel_anio'] = date("Y");
        $datos['titulo'] = "Informe de Gastos";

        $this->load->library('Ssp');
        $this->load->library('Cssjs');
        $this->cssjs->add_js(base_url().'assets/js/Reporte/informe_gastosGraf.js?v=1.0',false,false);
        $this->cssjs->add_js(base_url().'assets/plg/highcharts/highcharts.js',false,false);
        $this->cssjs->add_js(base_url().'assets/plg/highcharts/modules/data.js',false,false);
        $this->cssjs->add_js(base_url().'assets/plg/highcharts/modules/exporting.js',false,false);

        $this->load->view('header');
        $this->load->view('menu');
        $this->load->view('Reporte/informe_gastosGraf', $datos);
        $this->load->view('footer');   
    }
	
    public function deta_infoGastos($tipo,$mes,$anio,$stipo,$moneda){
        $this->db->select("SUM(sepr_total) total");
        $this->db->from("servicio_proveedor");
        $this->db->join("proveedor","prov_id = sepr_prov_id","LEFT");
        $this->db->join("proveedor_empresa","emp_id = prov_emp_id","LEFT");
        $this->db->where(array("sepr_tipo" => $stipo, "sepr_moneda" => $moneda,"prov_combustible" => "NO"));
        if($tipo != "")
            $this->db->where("emp_tipo", $tipo);
        if($mes != "")
            $this->db->where("MONTH(sepr_fecha)", $mes);
        if($anio != "")
            $this->db->where("YEAR(sepr_fecha)", $anio);
        $res = $this->db->get();
        if($res->num_rows() > 0)
            return ($res->row()->total != "") ? $res->row()->total : 0;
        else return 0;
        
    }
	public function repo_ingresosGrafico($json = false){
        $this->load->helper('Funciones');
        if($json){
            $mes = $this->input->get("mes");
            $anio = $this->input->get("anio");
            $moneda = $this->input->get("moneda");

            //listado de proveedores que estan en la base de datos
            $total = 0;
			
			
			$directo = $this->get_totalImporte("dire", $mes, $anio, $moneda);
			$comprobante = $this->get_totalImporte("comp", $mes, $anio, $moneda);
			$liquidacion = $this->get_totalImporte("liqu", $mes, $anio, $moneda);
			
			$total = $directo + $comprobante + $liquidacion;
			
			$datas = array();
			if($total > 0){
				$datas[] = (object)array("total" => $directo, "nombre" => "Directos", "porcent" => round((($directo*100)/$total),2));
				$datas[] = (object)array("total" => $comprobante, "nombre" => "Comprobantes", "porcent" => round((($comprobante*100)/$total),2));
				$datas[] = (object)array("total" => $liquidacion, "nombre" => "Liquidaciones", "porcent" => round((($liquidacion*100)/$total),2));
			}
			
            $data["detalles"] = $datas;
            $data["total"] = $total;
            
            echo json_encode($data);
            exit(0);
        }

        for ($i=2018; $i <= date('Y')+2; $i++) { 
            $anios[$i] = $i;
        }
        $datos["anio"] = $anios;
        $datos["mes"] = array("" => "* Meses", 1=>"Enero",2=>"Febrero",3=>"Marzo",4=>"Abril",5=>"Mayo",6=>"Junio",7=>"Julio",8=>"Agosto",9=>"Septiembre",10=>"Octubre",11=>"Niviembre",12=>"Diciembre");
        $datos["tipo"] = array("PROPIO"=>"PROPIOS", "TERCERO"=>"TERCEROS","COMB" => "COMBUSTIBLES");
        $datos["moneda"] = array("SOLES"=>"SOLES", "DOLARES"=>"DOLARES");
        $datos['sel_mes'] = date("m");
        $datos['sel_anio'] = date("Y");
        $datos['titulo'] = "Informe de Gastos";

        $this->load->library('Ssp');
        $this->load->library('Cssjs');
        $this->cssjs->add_js(base_url().'assets/js/Reporte/informe_ingresosGraf.js?v=1.1',false,false);
        $this->cssjs->add_js(base_url().'assets/plg/highcharts/highcharts.js',false,false);
        $this->cssjs->add_js(base_url().'assets/plg/highcharts/modules/data.js',false,false);
        $this->cssjs->add_js(base_url().'assets/plg/highcharts/modules/exporting.js',false,false);

        $this->load->view('header');
        $this->load->view('menu');
        $this->load->view('Reporte/informe_ingresosGraf', $datos);
        $this->load->view('footer');   
    }
	
	public function get_totalImporte($tipo, $mes, $anio, $moneda, $tipor = ""){
		if($tipo == "dire"){
			$filtros = array("YEAR(DATE(deta_fechaserv))" => $anio,
								"paqu_esliquidacion" => 0,
								"paqu_escomprobante" => 0,
								"paqu_estado" => 'CONFIRMADO',
								"paqu_moneda" => $moneda
			);
			if($mes != "")
				$filtros["MONTH(DATE(deta_fechaserv))"] = $mes;
			if($tipor != "" && $tipor == "REAL") $this->db->select("SUM(paqu_total) total");
			else $this->db->select("SUM(paqu_cobrado) total");
			$this->db->from("paquete");
			$this->db->join("paquete_detalle", "deta_paqu_id = paqu_id");
		}else if($tipo == "comp"){
			$filtros = array("YEAR(vent_fecha)" => $anio,
								"vent_moneda" => $moneda
			);
			if($mes != "")
				$filtros["MONTH(vent_fecha)"] = $mes;
			
			if($tipor != "" && $tipor == "REAL") $this->db->select("SUM(vent_cobrado) total");
			else $this->db->select("SUM(vent_total) total");
			$this->db->from("venta");
		}else{
			$filtros = array("YEAR(DATE(liqu_fechareg))" => $anio,
								"liqu_moneda" => $moneda
			);
			if($mes != "")
				$filtros["MONTH(DATE(liqu_fechareg))"] = $mes;
			if($tipor != "" && $tipor == "REAL") $this->db->select("SUM(liqu_cobrado) total");
			else $this->db->select("SUM(liqu_total) total");
			$this->db->from("liquidacion");
		}
		$this->db->where($filtros);
		$consulta = $this->db->get();
		if($consulta->num_rows() > 0){
			$consulta = $consulta->row();
			if($consulta->total > 0 && $consulta->total != ""){
				return floatval($consulta->total);
			}else
				return 0;
		}else
			return 0;
	}
	public function excel_cuadroIngresos($value=''){
        $moneda = $this->Model_general->enum_valores('paquete','paqu_moneda');
        $search = $this->input->get("search");
        $tipo = $this->input->get("tipo");
        $mes = $this->input->get("mes");
        $anio = $this->input->get("anio");

        $filtros = array();
        $filtros2 = array();
		if($search != "")
			$filtros[] = "paqu_clie_rsocial LIKE '%".$search."%'";
		if($tipo != "")
			$filtros[] = "paqu_tipo = '".$tipo."'";
		if($mes != "")
			$filtros2[] = "MONTH(deta_fechaserv) = '".$mes."'";
		if($anio != "")
			$filtros2[] = "YEAR(deta_fechaserv) = '".$anio."'";
		$filtros[] = "paqu_estado != 'ANULADO'";

		$filtros = (COUNT($filtros) > 0)?implode(" AND ", $filtros):1;
		$filtros2 = (COUNT($filtros2) > 0)?" AND ".implode(" AND ", $filtros2):'';

		$clientes = $this->db->query("SELECT paqu_clie_id, paqu_clie_rsocial FROM paquete_detalle join paquete ON deta_paqu_id = paqu_id WHERE ".$filtros.$filtros2." GROUP BY paqu_clie_id")->result();

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
        
        $objPHPExcel->getActiveSheet()
                ->setCellValue('A1', 'CONCATO')
                ->setCellValue('B1', 'SOLES')
                ->setCellValue('E1', 'DOLARES');
        $objPHPExcel->getActiveSheet()
                ->setCellValue('B2', 'COBRADO')
                ->setCellValue('C2', 'SALDO')
                ->setCellValue('D2', 'TOTAL')
                ->setCellValue('E2', 'COBRADO')
                ->setCellValue('F2', 'SALDO')
                ->setCellValue('G2', 'TOTAL');
        
        $objPHPExcel->getActiveSheet()->mergeCells('A1:A2');
        $objPHPExcel->getActiveSheet()->mergeCells('B1:D1');
        $objPHPExcel->getActiveSheet()->mergeCells('E1:G1');
        $objPHPExcel->getActiveSheet()->getStyle('A1:M2')->applyFromArray($center);
        
                
        $objPHPExcel->getActiveSheet()->getStyle('F')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
        $objPHPExcel->getActiveSheet()->getStyle('D')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);      
        $objPHPExcel->getActiveSheet()->freezePane('A3');
        
        $ini = 3;
        $index = 0;

        if(COUNT($clientes) > 0){
            $tabla = "";
            $saldo_s = 0;
            $cobra_s = 0;
            $total_s = 0;
            $saldo_d = 0;
            $cobra_d = 0;
            $total_d = 0;

            foreach ($clientes as $i => $clie) {
                $nro = $index+$ini;
                $deuda = array();
                $cobrado = array();
                foreach ($moneda as $mone) {
                    $deuda[$mone] = $this->db->query("SELECT SUM(paqu_total) as total FROM paquete_detalle join paquete ON deta_paqu_id = paqu_id WHERE paqu_moneda = '{$mone}' AND paqu_clie_id = ".$clie->paqu_clie_id." AND paqu_escobrado = 0 ".($filtros==1?"":" AND ".$filtros).($filtros2==""?"":$filtros2))->row();    
					$cobrado[$mone] = $this->db->query("SELECT SUM(paqu_total) as total FROM paquete_detalle join paquete ON deta_paqu_id = paqu_id WHERE paqu_moneda = '{$mone}' AND paqu_clie_id = ".$clie->paqu_clie_id." AND paqu_escobrado = 1 ".($filtros==1?"":" AND ".$filtros).($filtros2==""?"":$filtros2))->row();
                }

                $s_saldo = ($deuda["SOLES"]->total)?$deuda['SOLES']->total:'0.00';
                $s_cobrado = ($cobrado["SOLES"]->total)?$cobrado['SOLES']->total:'0.00';
                $s_total = number_format($s_saldo + $s_cobrado, 2, '.', '');
                
                $saldo_s += $s_saldo;
                $cobra_s += $s_cobrado;
                $total_s += $s_total;

                $d_saldo = ($deuda["DOLARES"]->total)?$deuda['DOLARES']->total:'0.00';
                $d_cobrado = ($cobrado["DOLARES"]->total)?$cobrado['DOLARES']->total:'0.00';
                $d_total = number_format($d_saldo + $d_cobrado, 2, '.', '');

                $saldo_d += $d_saldo;
                $cobra_d += $d_cobrado;
                $total_d += $d_total;

                $objPHPExcel->getActiveSheet()
                                ->setCellValue("A$nro", $clie->paqu_clie_rsocial)
                                ->setCellValue("B$nro", $s_cobrado)
                                ->setCellValue("C$nro", $s_saldo)
                                ->setCellValue("D$nro", $s_total)
                                ->setCellValue("E$nro", $d_cobrado)
                                ->setCellValue("F$nro", $d_saldo)
                                ->setCellValue("G$nro", $d_total);
                $index++;
            }

            $fin = $index+$ini;
            $objPHPExcel->getActiveSheet()
                            ->setCellValue("A$fin", "TOTAL")
                            ->setCellValue("B$fin", number_format($cobra_s, 2, '.', ' '))
                            ->setCellValue("C$fin", number_format($saldo_s, 2, '.', ' '))
                            ->setCellValue("D$fin", number_format($total_s, 2, '.', ' '))
                            ->setCellValue("E$fin", number_format($cobra_d, 2, '.', ' '))
                            ->setCellValue("F$fin", number_format($saldo_d, 2, '.', ' '))
                            ->setCellValue("G$fin", number_format($total_d, 2, '.', ' '));
        }else{
            $fin = $index+$ini;
            $objPHPExcel->getActiveSheet()->mergeCells("A$ini:G$ini");
            $objPHPExcel->getActiveSheet()->setCellValue("A$ini", 'No se encontraron datos');
        }

        foreach(range('A','G') as $rag)
            $objPHPExcel->getActiveSheet()->getColumnDimension($rag)->setAutoSize(true);

        $objPHPExcel->getActiveSheet()->getStyle("B$ini:G$fin")->getNumberFormat()->setFormatCode('#,##0.00');
        $excel->excel_output($objPHPExcel, 'CUADRO DE INGRESOS');
    }
	public function excel_movimientoTuristas(){
        $mes = $this->input->get("mes");
        $anio = $this->input->get("anio");
        $servicios = explode(",",$this->input->get("servicio"));


        $this->db->select("paqu_clie_id as clie_id, paqu_clie_rsocial as clie_rsocial");
        $this->db->from("paquete");
        $this->db->join("paquete_detalle","paqu_id = deta_paqu_id");
		$this->db->where("paqu_estado !=",'ANULADO');
        if($mes != "")
            $this->db->where("MONTH(deta_fechaserv)",$mes);
        if($anio != "")
            $this->db->where("YEAR(deta_fechaserv)",$anio);
        $this->db->where_in("deta_serv_id",$servicios);
        $this->db->group_by("paqu_clie_id");
        $this->db->order_by("paqu_clie_id","ASC");
        $clientes = $this->db->get()->result();

        $servicios = $this->db->where_in("serv_id",$servicios)->get("servicio")->result();



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
        $objPHPExcel->getActiveSheet()->freezePane('A3');

        $objPHPExcel->getActiveSheet()->setCellValue('A1', 'CONCATO');
        $letras = 66;
        foreach ($servicios as $j => $serv) {
            $ltr = chr($letras)."1";
            $objPHPExcel->getActiveSheet()->setCellValue($ltr, $serv->serv_descripcion);
            $serv->sumas = 0;
            $letras++;
        }
        
        $ltr = chr($letras)."1";
        $objPHPExcel->getActiveSheet()->setCellValue($ltr, "TOTAL");

        $ini = 3;
        $index = 0;
        
        $suma_total = 0;
        foreach ($clientes as $i => $clie) {
            $nro = $index+$ini;
            
            $objPHPExcel->getActiveSheet()->setCellValue("A$nro", $clie->clie_rsocial);
            $suma_clie = 0;
            $letras = 66;
            foreach ($servicios as $j => $serv) {
                $this->db->select("SUM(deta_pax) as total");
                $this->db->from("paquete_detalle");
                $this->db->join("paquete", "paqu_id = deta_paqu_id");
				$this->db->where("paqu_estado !=",'ANULADO');
                if($mes != "")
                    $this->db->where("MONTH(deta_fechaserv)",$mes);
                if($anio != "")
                    $this->db->where("YEAR(deta_fechaserv)",$anio);
                $this->db->where("paqu_clie_id",$clie->clie_id);
                $this->db->where("deta_serv_id",$serv->serv_id);
                $pax = $this->db->get()->row();
                $serv->sumas += $pax->total;
                $suma_clie += $pax->total;

                $ltr = chr($letras).$nro;
                $objPHPExcel->getActiveSheet()->setCellValue($ltr, $pax->total);
                $letras++;
            }
            $suma_total += $suma_clie;

            $ltr = chr($letras).$nro;
            $objPHPExcel->getActiveSheet()->setCellValue($ltr, $suma_clie);
            
            $index++;
        }
        
        $fin = $index+$ini;
        $objPHPExcel->getActiveSheet()->setCellValue("A$fin", "TOTAL GENERAL");
        $letras = 66;
        foreach ($servicios as $j => $serv) {
            $ltr = chr($letras).$fin;
            $objPHPExcel->getActiveSheet()->setCellValue($ltr, $serv->sumas);
            $letras++;
        }
        $ltr = chr($letras).$fin;
        $objPHPExcel->getActiveSheet()->setCellValue($ltr, $suma_total);

        foreach(range('A','J') as $rag)
            $objPHPExcel->getActiveSheet()->getColumnDimension($rag)->setAutoSize(true);

        $excel->excel_output($objPHPExcel, 'MOVIMIENTO DE TURISTAS');
    }
	public function excel_informeDeudas(){
        $search = $this->input->get("search");
        $desde = $this->input->get("desde");
        $hasta = $this->input->get("hasta");
        $filtros = array();

        if($search != "")
            $filtros[] = "paqu_clie_rsocial LIKE '%".$search."%'";
        $filtros[] = "paqu_estado != 'ANULADO'";
        if($desde != "" && $hasta != "")
            $filtros[] = "deta_fechaserv >='".$desde." 00:00:00' AND deta_fechaserv <='".$hasta." 23:59:00'";

        $filtros = (COUNT($filtros) > 0)?implode(" AND ", $filtros):1;
        
        $clientes = $this->db->query("SELECT paqu_clie_id, paqu_clie_rsocial FROM paquete join paquete_detalle on deta_paqu_id = paqu_id WHERE ".$filtros." GROUP BY paqu_clie_id")->result();

        $moneda = $this->Model_general->enum_valores('paquete','paqu_moneda');
        $tipos = $this->Model_general->enum_valores('paquete','paqu_tipo');
        $tipoCobro = array("1" => "comprobante","2" => "liquidacion","3" => "efectivo");


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
        
        $objPHPExcel->getActiveSheet()
                ->setCellValue('A1', 'CONCATO')
                ->setCellValue('B1', 'SOLES')
                ->setCellValue('E1', 'DOLARES');
        $objPHPExcel->getActiveSheet()
                ->setCellValue('B2', 'COBRADO')
                ->setCellValue('C2', 'SALDO')
                ->setCellValue('D2', 'TOTAL')
                ->setCellValue('E2', 'COBRADO')
                ->setCellValue('F2', 'SALDO')
                ->setCellValue('G2', 'TOTAL');
        
        $objPHPExcel->getActiveSheet()->mergeCells('A1:A2');
        $objPHPExcel->getActiveSheet()->mergeCells('B1:D1');
        $objPHPExcel->getActiveSheet()->mergeCells('E1:G1');
        $objPHPExcel->getActiveSheet()->getStyle('A1:M2')->applyFromArray($center);
        
                
        $objPHPExcel->getActiveSheet()->getStyle('F')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
        $objPHPExcel->getActiveSheet()->getStyle('D')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);      
        $objPHPExcel->getActiveSheet()->freezePane('A3');
        
        $ini = 3;
        $index = 0;

        if(COUNT($clientes) > 0){
            $dataTipo = array();
            foreach ($tipos as $key => $tipo) {
                

                $cbrtipo = array();
                $s_saldo = 0;
                $s_cobrado = 0;
                $s_total = 0;
                $d_saldo = 0;
                $d_cobrado = 0;
                $d_total = 0;

                foreach ($tipoCobro as $i => $cobro) {
                    $cbrtipo[$cobro] = $this->getTipoCobro($filtros,$moneda,$i,$tipo);

                    $s_saldo += $cbrtipo[$cobro]["s_saldo"];
                    $s_cobrado += $cbrtipo[$cobro]["s_cobrado"];
                    $s_total += $cbrtipo[$cobro]["s_total"];
                    $d_saldo += $cbrtipo[$cobro]["d_saldo"];
                    $d_cobrado += $cbrtipo[$cobro]["d_cobrado"];
                    $d_total += $cbrtipo[$cobro]["d_total"];

                    foreach ($cbrtipo[$cobro]["clies"] as $j => $row) {
                        $nro = $index+$ini;    
                        $objPHPExcel->getActiveSheet()->setCellValue("A$nro", $row["cliente"])
                                                    ->setCellValue("B$nro", $row["s_cobrado"])
                                                    ->setCellValue("C$nro", $row["s_saldo"])
                                                    ->setCellValue("D$nro", $row["s_total"])
                                                    ->setCellValue("E$nro", $row["d_cobrado"])
                                                    ->setCellValue("F$nro", $row["d_saldo"])
                                                    ->setCellValue("G$nro", $row["d_total"]);
                        $index++;
                    }
                    $nro = $index+$ini;
                    $objPHPExcel->getActiveSheet()->setCellValue("A$nro", $cobro)
                                                    ->setCellValue("B$nro", $cbrtipo[$cobro]["s_cobrado"])
                                                    ->setCellValue("C$nro", $cbrtipo[$cobro]["s_saldo"])
                                                    ->setCellValue("D$nro", $cbrtipo[$cobro]["s_total"])
                                                    ->setCellValue("E$nro", $cbrtipo[$cobro]["d_cobrado"])
                                                    ->setCellValue("F$nro", $cbrtipo[$cobro]["d_saldo"])
                                                    ->setCellValue("G$nro", $cbrtipo[$cobro]["d_total"]);
                    $objPHPExcel->getActiveSheet()->getStyle("A$nro:G$nro")->applyFromArray($fillgray);
                    $index++;
                }
                $nro = $index+$ini;
                $res = array("s_saldo" => $s_saldo,
                                "s_cobrado" => $s_cobrado,
                                "s_total" => $s_total,
                                "d_saldo" => $d_saldo,
                                "d_cobrado" => $d_cobrado,
                                "d_total" => $d_total,
                            );
                $dataTipo[$tipo] = array_merge($cbrtipo,array("res"=>$res));


                $objPHPExcel->getActiveSheet()->setCellValue("A$nro", $tipo)
                                                ->setCellValue("B$nro", $s_cobrado)
                                                ->setCellValue("C$nro", $s_saldo)
                                                ->setCellValue("D$nro", $s_total)
                                                ->setCellValue("E$nro", $d_cobrado)
                                                ->setCellValue("F$nro", $d_saldo)
                                                ->setCellValue("G$nro", $d_total);
                $objPHPExcel->getActiveSheet()->getStyle("A$nro:G$nro")->applyFromArray($green);
                $index++;
            }
            $fin = $index+$ini;
        }else{
            $fin = $index+$ini;
            $objPHPExcel->getActiveSheet()->mergeCells("A$ini:G$ini");
            $objPHPExcel->getActiveSheet()->setCellValue("A$ini", 'No se encontraron datos');
        }

        foreach(range('A','G') as $rag)
            $objPHPExcel->getActiveSheet()->getColumnDimension($rag)->setAutoSize(true);

        $objPHPExcel->getActiveSheet()->getStyle("B$ini:G$fin")->getNumberFormat()->setFormatCode('#,##0.00');
        $excel->excel_output($objPHPExcel, 'INFORME DE DEUDAS '.$desde." - ".$hasta);
    }
	public function excel_informeGastos(){
        $search = $this->input->get("search");
        $desde = $this->input->get("desde");
        $hasta = $this->input->get("hasta");
        $filtros = array();

        if($search != "")
            $filtros[] = "prov_rsocial LIKE '%".$search."%'";
        if($desde != "" && $hasta != "")
            $filtros[] = "sepr_fecha >='".$desde."' AND sepr_fecha <='".$hasta."'";

        $filtros = (COUNT($filtros) > 0)?implode(" AND ", $filtros):1;
        
        $proveedores = $this->db->query("SELECT prov_id, prov_rsocial FROM proveedor LEFT JOIN servicio_proveedor ON sepr_prov_id = prov_id WHERE ".$filtros." GROUP BY prov_id")->result();

        $moneda = $this->Model_general->enum_valores('servicio_proveedor','sepr_moneda');
        $tipos = $this->Model_general->enum_valores('proveedor_empresa','emp_tipo');
        $tipoServ = $this->Model_general->getOptions("proveedor_tipo",array("tipo_id","tipo_denom"));
        
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
        
        $objPHPExcel->getActiveSheet()
                ->setCellValue('A1', 'CONCATO')
                ->setCellValue('B1', 'SOLES')
                ->setCellValue('E1', 'DOLARES');
        $objPHPExcel->getActiveSheet()
                ->setCellValue('B2', 'COBRADO')
                ->setCellValue('C2', 'SALDO')
                ->setCellValue('D2', 'TOTAL')
                ->setCellValue('E2', 'COBRADO')
                ->setCellValue('F2', 'SALDO')
                ->setCellValue('G2', 'TOTAL');
        
        $objPHPExcel->getActiveSheet()->mergeCells('A1:A2');
        $objPHPExcel->getActiveSheet()->mergeCells('B1:D1');
        $objPHPExcel->getActiveSheet()->mergeCells('E1:G1');
        $objPHPExcel->getActiveSheet()->getStyle('A1:M2')->applyFromArray($center);
        
                
        $objPHPExcel->getActiveSheet()->getStyle('F')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
        $objPHPExcel->getActiveSheet()->getStyle('D')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);      
        $objPHPExcel->getActiveSheet()->freezePane('A3');
        
        $ini = 3;
        $index = 0;

        if(COUNT($proveedores) > 0){
            $dataTipo = array();
            foreach ($tipos as $key => $tipo) {

                $cbrtipo = array();
                $s_saldo = 0;
                $s_cobrado = 0;
                $s_total = 0;
                $d_saldo = 0;
                $d_cobrado = 0;
                $d_total = 0;

                foreach ($tipoServ as $i => $stipo) {
                    
                    $cbrtipo[$stipo] = $this->getGastos($filtros,$moneda,$i,$tipo);
                    $s_saldo += $cbrtipo[$stipo]["s_saldo"];
                    $s_cobrado += $cbrtipo[$stipo]["s_cobrado"];
                    $s_total += $cbrtipo[$stipo]["s_total"];
                    $d_saldo += $cbrtipo[$stipo]["d_saldo"];
                    $d_cobrado += $cbrtipo[$stipo]["d_cobrado"];
                    $d_total += $cbrtipo[$stipo]["d_total"];

                    foreach ($cbrtipo[$stipo]["clies"] as $j => $row) {
                        $nro = $index+$ini;    
                        $objPHPExcel->getActiveSheet()->setCellValue("A$nro", $row["cliente"])
                                                    ->setCellValue("B$nro", $row["s_cobrado"])
                                                    ->setCellValue("C$nro", $row["s_saldo"])
                                                    ->setCellValue("D$nro", $row["s_total"])
                                                    ->setCellValue("E$nro", $row["d_cobrado"])
                                                    ->setCellValue("F$nro", $row["d_saldo"])
                                                    ->setCellValue("G$nro", $row["d_total"]);
                        $index++;
                    }
                    $nro = $index+$ini;
                    $objPHPExcel->getActiveSheet()->setCellValue("A$nro", $stipo)
                                                    ->setCellValue("B$nro", $cbrtipo[$stipo]["s_cobrado"])
                                                    ->setCellValue("C$nro", $cbrtipo[$stipo]["s_saldo"])
                                                    ->setCellValue("D$nro", $cbrtipo[$stipo]["s_total"])
                                                    ->setCellValue("E$nro", $cbrtipo[$stipo]["d_cobrado"])
                                                    ->setCellValue("F$nro", $cbrtipo[$stipo]["d_saldo"])
                                                    ->setCellValue("G$nro", $cbrtipo[$stipo]["d_total"]);
                    $objPHPExcel->getActiveSheet()->getStyle("A$nro:G$nro")->applyFromArray($fillgray);
                    $index++;
                }
                $nro = $index+$ini;
                $res = array("s_saldo" => $s_saldo,
                                "s_cobrado" => $s_cobrado,
                                "s_total" => $s_total,
                                "d_saldo" => $d_saldo,
                                "d_cobrado" => $d_cobrado,
                                "d_total" => $d_total,
                            );
                $objPHPExcel->getActiveSheet()->setCellValue("A$nro", $tipo)
                                                ->setCellValue("B$nro", $s_cobrado)
                                                ->setCellValue("C$nro", $s_saldo)
                                                ->setCellValue("D$nro", $s_total)
                                                ->setCellValue("E$nro", $d_cobrado)
                                                ->setCellValue("F$nro", $d_saldo)
                                                ->setCellValue("G$nro", $d_total);
                $objPHPExcel->getActiveSheet()->getStyle("A$nro:G$nro")->applyFromArray($green);
                $index++;
            }
            $fin = $index+$ini;
        }else{
            $fin = $index+$ini;
            $objPHPExcel->getActiveSheet()->mergeCells("A$ini:G$ini");
            $objPHPExcel->getActiveSheet()->setCellValue("A$ini", 'No se encontraron datos');
        }
        foreach(range('A','G') as $rag)
            $objPHPExcel->getActiveSheet()->getColumnDimension($rag)->setAutoSize(true);

        $objPHPExcel->getActiveSheet()->getStyle("B$ini:G$fin")->getNumberFormat()->setFormatCode('#,##0.00');
        $excel->excel_output($objPHPExcel, 'INFORME DE GASTOS'.$desde." - ".$hasta);
    }
	public function repo_barras(){
        
        $clientes = $this->db->query("SELECT clie_rcomercial, clie_id 
                                        from cliente 
                                        where clie_id IN (SELECT paqu_clie_id from paquete)")->result();
        $clies = array();
        foreach($clientes as $row)
            $clies[$row->clie_id] = $row->clie_rcomercial;
        $datos['titulo'] = "Reporte grafico";
        $datos['clientes'] = $clies;
		$datos["servicios"] = $this->Model_general->getOptions('servicio', array("serv_id", "serv_descripcion"),'* Servicios');
        $datos['tipo'] = array("AGENCIAS" => "AGENCIAS", "SERVICIOS" => "SERVICIOS");
        $datos['tipor'] = array("LOCAL" => "LOCAL", "RECEPTIVO" => "RECEPTIVO", "PRIVADO" => "PRIVADO");
        $datos = array_merge($datos, $this->get_filtros(true));
        
        $this->load->library('Ssp');
        $this->load->library('Cssjs');
        $this->cssjs->add_js(base_url().'assets/js/Reporte/repo_barras.js?v=1.6',false,false);
        $this->cssjs->add_js(base_url().'assets/plg/highcharts/highcharts.js',false,false);
        $this->cssjs->add_js(base_url().'assets/plg/highcharts/modules/exporting.js',false,false);
        
        $this->load->view('header');
        $this->load->view('menu');
        $this->load->view('Reporte/repo_barras', $datos);
        $this->load->view('footer');
    }
    public function data_barras(){

        $mes = $this->input->get("mes");
        $anio = $this->input->get("anio");
        $tipor = $this->input->get("tipor");
        $tipo = $this->input->get("tipo");
		$detalle = $this->input->get("detalles");

        $condiciones = array("paqu_estado"=>"CONFIRMADO", 
                                "YEAR(DATE(deta_fechaserv))" => $anio,
                                "MONTH(DATE(deta_fechaserv))" => $mes,
                                "paqu_tipo" => $tipor);
        if($tipo == "AGENCIAS"){
            
            $this->db->select("paqu_clie_rsocial nombre, SUM(deta_pax) cantidad");
            $this->db->from("paquete");
            $this->db->join("paquete_detalle", "deta_paqu_id = paqu_id");
            $this->db->where($condiciones);
			if($detalle != "")
				$this->db->where("deta_serv_id", $detalle);
            $this->db->group_by("paqu_clie_id");
			$this->db->order_by("cantidad","DESC");
            $respuesta = $this->db->get()->result();
        }else{
            $this->db->select("deta_servicio nombre, SUM(deta_pax) cantidad");
            $this->db->from("paquete_detalle");
            $this->db->join("paquete", "paqu_id = deta_paqu_id");
            $this->db->where($condiciones);
			if($detalle != "")
				$this->db->where("paqu_clie_id", $detalle);
            $this->db->group_by("deta_serv_id");
			$this->db->order_by("cantidad","DESC");
            $respuesta = $this->db->get()->result();
        }
        echo json_encode($respuesta);
    }
	public function getSeleccion(){
		$tipo = $this->input->post("tipo");
		$tipor = $this->input->post("tipor");
		$mes = $this->input->post("mes");
		$anio = $this->input->post("anio");
		
		$seleccion = $this->Model_general->get_serv_exist($tipo,$tipor, $mes, $anio);
		$data["options"] = $seleccion;
		echo json_encode($data);
	}
	public function repo_barrasComparativa(){
        
        $datos['titulo'] = "Reporte grafico comparativa";
        $datos['tipo'] = array("AGENCIAS" => "AGENCIAS", "SERVICIOS" => "SERVICIOS");
        $datos = array_merge($datos, $this->get_filtros());
        
        $this->load->library('Ssp');
        $this->load->library('Cssjs');
        $this->cssjs->add_js(base_url().'assets/js/Reporte/repo_barrasComparativa.js?v=1.7',false,false);
        $this->cssjs->add_js(base_url().'assets/plg/highcharts/highcharts.js',false,false);
        $this->cssjs->add_js(base_url().'assets/plg/highcharts/modules/data.js',false,false);
        $this->cssjs->add_js(base_url().'assets/plg/highcharts/modules/exporting.js',false,false);
        
        $this->load->view('header');
        $this->load->view('menu');
        $this->load->view('Reporte/repo_barrasComparativa', $datos);
        $this->load->view('footer');
    }
    public function data_barrasComparativa(){

        $mes = $this->input->get("mes");
        $anio = $this->input->get("anio");
        $tipo = $this->input->get("tipo");
		$detalle = $this->input->get("detalles");

        $condiciones = array("paqu_estado"=>"CONFIRMADO", 
                                "YEAR(DATE(deta_fechaserv))" => $anio);
		if($mes != "")
			$condiciones["MONTH(DATE(deta_fechaserv))"] = $mes;

        if($tipo == "AGENCIAS"){
			if($detalle != "")
				$condiciones["deta_serv_id"] = $detalle;
            $this->db->select("paqu_clie_rsocial nombre, paqu_clie_id clie_id, SUM(deta_pax) pax");
            $this->db->from("paquete");
            $this->db->join("paquete_detalle", "deta_paqu_id = paqu_id");
            $this->db->where($condiciones);
            $this->db->group_by("paqu_clie_id");
			$this->db->order_by("pax","DESC");
            $respuesta = $this->db->get()->result();

            foreach ($respuesta as $i => $row) {
                $this->db->select("SUM(deta_pax) total");
                $this->db->from("paquete");
                $this->db->join("paquete_detalle", "deta_paqu_id = paqu_id");
                $this->db->join("cliente", "clie_id = paqu_clie_id");
                $this->db->where($condiciones);
                $this->db->where(array("clie_reporte" => "LOCAL", "paqu_clie_id" => $row->clie_id));
                $local = $this->db->get()->row();
                $row->local = $local->total;

                $this->db->select("SUM(deta_pax) total");
                $this->db->from("paquete");
                $this->db->join("paquete_detalle", "deta_paqu_id = paqu_id");
                $this->db->join("cliente", "clie_id = paqu_clie_id");
                $this->db->where($condiciones);
                $this->db->where(array("clie_reporte" => "RECEPTIVO", "paqu_clie_id" => $row->clie_id));
                $receptivo = $this->db->get()->row();
                $row->receptivo = $receptivo->total;
            }
        }else{
			if($detalle != "")
				$condiciones["paqu_clie_id"] = $detalle;
            $this->db->select("deta_servicio nombre, deta_serv_id serv_id, SUM(deta_pax) pax");
            $this->db->from("paquete_detalle");
            $this->db->join("paquete", "paqu_id = deta_paqu_id");
            $this->db->where($condiciones);
            $this->db->group_by("deta_serv_id");
            $respuesta = $this->db->get()->result();

            foreach ($respuesta as $i => $row) {
                $this->db->select("SUM(deta_pax) total");
                $this->db->from("paquete_detalle");
                $this->db->join("paquete", "paqu_id = deta_paqu_id");
                $this->db->join("cliente", "clie_id = paqu_clie_id");
                $this->db->where($condiciones);
                $this->db->where(array("clie_reporte" => "LOCAL", "deta_serv_id" => $row->serv_id));
                $local = $this->db->get()->row();
                
                $row->local = $local->total;

                $this->db->select("SUM(deta_pax) total");
                $this->db->from("paquete_detalle");
                $this->db->join("paquete", "paqu_id = deta_paqu_id");
                $this->db->join("cliente", "clie_id = paqu_clie_id");
                $this->db->where($condiciones);
                $this->db->where(array("clie_reporte" => "RECEPTIVO", "deta_serv_id" => $row->serv_id));
                $receptivo = $this->db->get()->row();
                $row->receptivo = $receptivo->total;
            }
        }
        $table = "";
		$total = "";
        foreach ($respuesta as $i => $row) {
            if($row->local > 0 || $row->receptivo > 0){
				$local = ($row->local != ""?$row->local:'0');
				$receptivo = ($row->receptivo != ""?$row->receptivo:'0');
                $table .= "<tr>";
                $table .= "<td>".$row->nombre."</td>";
                $table .= "<td>".$local."</td>";
                $table .= "<td>".$receptivo."</td>";
                $table .= "</tr>";
				
				$total .= "<tr>";
                $total .= "<td>".$row->nombre."</td>";
                $total .= "<td>".($local + $receptivo)."</td>";
                $total .= "</tr>";
            }
        }
        $data["table"] = $table;
		$data["total"] = $total;
        echo json_encode($data);
    }
	function weeks_in_month($month, $year) {
        // Start of month
        $start = mktime(0, 0, 0, $month, 1, $year);
        // End of month
        $end = mktime(0, 0, 0, $month, date('t', $start), $year);
        // Start week
        $start_week = date('W', $start);
        // End week
        $end_week = date('W', $end);
        $resp["start"] = $start_week-1;
        $resp["end"] = $end_week-1;
        return $resp;
    }
    public function repo_histograma($json = false){

        $this->load->helper('Funciones');
        if($json){
            $tipo = $this->input->get("tipo");
            $tipor = $this->input->get("tipor");
            $mes = $this->input->get("mes");
            $anio = $this->input->get("anio");
			$detalle = $this->input->get("detalles");
            if($mes != "")
                $weeks = $this->weeks_in_month($mes, $anio);

            $condiciones = array("paqu_estado"=> "CONFIRMADO", 
                                    "YEAR(deta_fechaserv)" => $anio,
                                    "paqu_tipo" => $tipor,
                                );
            if($mes != "")
                $condiciones["MONTH(deta_fechaserv)"] = $mes;

            $resumen = array();
            if($tipo == "SERVICIOS"){
				if($detalle != "")
					$condiciones["paqu_clie_id"] = $detalle;
                $this->db->select("deta_serv_id id, deta_servicio nombre, SUM(deta_pax) pax");
                $this->db->from("paquete_detalle");
                $this->db->join("paquete", "paqu_id = deta_paqu_id");
                $this->db->where($condiciones);
                $this->db->group_by("deta_serv_id");
                $consulta = $this->db->get()->result();

                if(COUNT($consulta) > 0){
					
                    foreach ($consulta as $i => $row) {
                        if($mes != ""){
                            for ($j = $weeks["start"]; $j <= $weeks["end"]; $j++) { 
                                $this->db->select("SUM(deta_pax) total");
                                $this->db->from("paquete_detalle");
                                $this->db->join("paquete", "paqu_id = deta_paqu_id");
                                $this->db->where("deta_serv_id", $row->id);
                                $this->db->where("WEEK(DATE(deta_fechaserv))", $j);
								
                                $this->db->where($condiciones);
                                $semanas = $this->db->get()->row();
                                $row->detas[$j] = ($semanas->total != "") ? $semanas->total : 0;
                            }
                        }else{
                            for ($j = 1; $j <= 12; $j++) { 
                                $this->db->select("SUM(deta_pax) total");
                                $this->db->from("paquete_detalle");
                                $this->db->join("paquete", "paqu_id = deta_paqu_id");
                                $this->db->where("deta_serv_id", $row->id);
                                $this->db->where("MONTH(DATE(deta_fechaserv))", $j);
                                $this->db->where($condiciones);
                                $semanas = $this->db->get()->row();
                                $row->detas[$j] = ($semanas->total != "") ? $semanas->total : 0;
                            }
                        }
                    }
                }

            }else{
				if($detalle != "")
					$condiciones["deta_serv_id"] = $detalle;
                $this->db->select("paqu_clie_id id, paqu_clie_rsocial nombre, SUM(deta_pax) pax");
                $this->db->from("paquete");
                $this->db->join("paquete_detalle","paqu_id = deta_paqu_id");
                $this->db->where($condiciones);
                $this->db->group_by("paqu_clie_id");
                $consulta = $this->db->get()->result();

                if(COUNT($consulta) > 0){
					
                    foreach ($consulta as $i => $row) {
						if($mes != ""){
							for ($j = $weeks["start"]; $j <= $weeks["end"]; $j++) { 
								$this->db->select("SUM(deta_pax) total");
								$this->db->from("paquete_detalle");
								$this->db->join("paquete", "paqu_id = deta_paqu_id");
								$this->db->where("paqu_clie_id", $row->id);
								$this->db->where("WEEK(DATE(deta_fechaserv))", $j);
								$this->db->where($condiciones);
								$semanas = $this->db->get()->row();
								$row->detas[$j] = ($semanas->total != "") ? $semanas->total : 0;
							}
						}else{
							for ($j = 1; $j <= 12; $j++) { 
								$this->db->select("SUM(deta_pax) total");
								$this->db->from("paquete_detalle");
								$this->db->join("paquete", "paqu_id = deta_paqu_id");
								$this->db->where("paqu_clie_id", $row->id);
								$this->db->where("MONTH(DATE(deta_fechaserv))", $j);
								$this->db->where($condiciones);
								$semanas = $this->db->get()->row();
								$row->detas[$j] = ($semanas->total != "") ? $semanas->total : 0;
							}
						}
                    }
                }
            }
            echo json_encode($consulta);
            exit(0);
        }
        for ($i=2018; $i <= date('Y')+2; $i++) { 
            $anios[$i] = $i;
        }
        $datos["anio"] = $anios;
        $datos["mes"] = array("" => "* Meses", 1=>"Enero",2=>"Febrero",3=>"Marzo",4=>"Abril",5=>"Mayo",6=>"Junio",7=>"Julio",8=>"Agosto",9=>"Septiembre",10=>"Octubre",11=>"Niviembre",12=>"Diciembre");
        $datos["tipo"] = array("AGENCIAS"=>"AGENCIAS", "SERVICIOS"=>"SERVICIOS");
        $datos['tipor'] = array("LOCAL" => "LOCAL", "RECEPTIVO" => "RECEPTIVO", "PRIVADO" => "PRIVADO");
        $datos['sel_mes'] = date("m");
        $datos['sel_anio'] = date("Y");
        $datos['titulo'] = "Histograma";

        $this->load->library('Ssp');
        $this->load->library('Cssjs');
        $this->cssjs->add_js(base_url().'assets/js/Reporte/histograma.js?v=1.3',false,false);
        $this->cssjs->add_js(base_url().'assets/plg/highcharts/highcharts.js',false,false);
        $this->cssjs->add_js(base_url().'assets/plg/highcharts/modules/series-label.js',false,false);
        $this->cssjs->add_js(base_url().'assets/plg/highcharts/modules/data.js',false,false);
        $this->cssjs->add_js(base_url().'assets/plg/highcharts/modules/exporting.js',false,false);

        $this->load->view('header');
        $this->load->view('menu');
        $this->load->view('Reporte/histograma', $datos);
        $this->load->view('footer');   
    }
	public function resultados_listado($json = false){
        $this->load->helper('Funciones');
        $this->load->library('Ssp');
        $this->load->library('Cssjs');
        //$json = isset($_GET['json']) ? $_GET['json'] : false;

        if ($json) {

            $json = isset($_GET['json']) ? $_GET['json'] : false;

            $mes = $this->input->get("mes");
            $anio = $this->input->get("anio");
			$tipo = $this->input->get("tipo");

            $condicion = array("MONTH(resu_fecha)" => $mes, "YEAR(resu_fecha)" => $anio, "resu_tipo" => $tipo);
            $consulta = $this->db->where($condicion)->get("estado_resultados");

            $tabla = "";

            if($consulta->num_rows() > 0){
                $consulta = $consulta->result();
                $datas = array();
                foreach ($consulta as $i => $row) {
                    $ti_brutos = $row->resu_ventas_netas + $row->resu_otros_ingresos;
                    
                    $utilidad_bruta = $ti_brutos - ($row->resu_costo_ventas + $row->resu_planilla);
                    $utilidad_operativa = $utilidad_bruta - $row->resu_gastos_operacionales - $row->resu_gastos_administracion - $row->resu_gastos_venta;
                    $otros_ingresos = $row->resu_ingresos_financieros - $row->resu_gastos_financieros + $row->resu_otros_ingresosf - $row->resu_otros_gastos - $row->resu_inflacion;
                    $resultados = $utilidad_operativa - $otros_ingresos;
                    $moneda = $row->resu_moneda;
                    $datas["moneda"] = $moneda;
                    $datas[$moneda]["ti_brutos"] = number_format($ti_brutos, 2, '.', '');
                    $datas[$moneda]["utilidad_bruta"] = number_format($utilidad_bruta, 2, '.', '');
                    $datas[$moneda]["utilidad_operativa"] = number_format($utilidad_operativa, 2, '.', '');
                    $datas[$moneda]["otros_ingresos"] = number_format($otros_ingresos, 2, '.', '');
                    $datas[$moneda]["resultados"] = number_format($resultados, 2, '.', '');
                    foreach ($row as $idx => $value) {
                        $datas[$moneda][$idx] = $value;
                    }
                }
                $tabla .= "<tr><th colspan='5' class='text-center'>ESTADO DE RESULTADOS</th></tr>";
                $tabla .= "<tr><th>AVT. JUMBO TRAVEL</th><th colspan='3'>EJERCICIO O PERIODO</th><th>".$mes." - ".$anio."</th></tr>";
                $tabla .= "<tr><th class='text-center'>DESCRIPCION</th><th colspan='2' class='text-center'>SOLES</th><th colspan='2' class='text-center'>DOLARES</th></tr>";
                $tabla .= "<tr>";
                $tabla .= "<th>Total de ingresos Brutos</th>";
                $tabla .= "<th></th><th class='mone'>".$datas['SOLES']['ti_brutos']."</th><th></th><th class='mone'>".$datas['DOLARES']['ti_brutos']."</th>";
                $tabla .= "</tr>";
                $tabla .= "<tr>";
                $tabla .= "<td>Ventas Netas (ingresos operacionales)(Ingreso de Actividades Ordinarias)</td>";
                $tabla .= "<td class='mone'>".$datas['SOLES']['resu_ventas_netas']."</td><td></td><td class='mone'>".$datas['DOLARES']['resu_ventas_netas']."</td><td></td>";
                $tabla .= "</tr>";
                $tabla .= "<tr>";
                $tabla .= "<td>otros Ingresos Operacionales</td>";
                $tabla .= "<td class='mone'>".$datas['SOLES']['resu_otros_ingresos']."</td><td></td><td class='mone'>".$datas['DOLARES']['resu_otros_ingresos']."</td><td></td>";
                $tabla .= "</tr>";
                $tabla .= "<tr>";
                $tabla .= "<th>Utilidad Bruta</th>";
                $tabla .= "<th></th><th class='mone'>".$datas['SOLES']['utilidad_bruta']."</th><th></th><th class='mone'>".$datas['DOLARES']['utilidad_bruta']."</th>";
                $tabla .= "</tr>";
				$tabla .= "<tr>";
                $tabla .= "<td>Planillas</td>";
                $tabla .= "<td class='mone'>".$datas['SOLES']['resu_planilla']."</td><td></td><td class='mone'>".$datas['DOLARES']['resu_planilla']."</td><td></td>";
                $tabla .= "</tr>";
                $tabla .= "<tr>";
                $tabla .= "<td>Costos fijos (agua, luz,telefono, alquiler, etc)</td>";
                $tabla .= "<td class='mone'>".$datas['SOLES']['resu_costo_ventas']."</td><td></td><td class='mone'>".$datas['DOLARES']['resu_costo_ventas']."</td><td></td>";
                $tabla .= "</tr>";
                $tabla .= "<tr>";
                $tabla .= "<th>Utilidad Operativa</th>";
                $tabla .= "<th></th><th class='mone'>".$datas['SOLES']['utilidad_operativa']."</th><th></th><th class='mone'>".$datas['DOLARES']['utilidad_operativa']."</th>";
                $tabla .= "</tr>";
                $tabla .= "<tr>";
                $tabla .= "<td>Gastos Operacionales<br>(Tkts, Combustible, Guia, Almuerzos, Alimentacion, Transporte)</td>";
                $tabla .= "<td class='mone'>".$datas['SOLES']['resu_gastos_operacionales']."</td><td></td><td class='mone'>".$datas['DOLARES']['resu_gastos_operacionales']."</td><td></td>";
                $tabla .= "</tr>";
                $tabla .= "<tr>";
                $tabla .= "<td>Gastos de Administración<br>(Caja chica)</td>";
                $tabla .= "<td class='mone'>".$datas['SOLES']['resu_gastos_administracion']."</td><td></td><td class='mone'>".$datas['DOLARES']['resu_gastos_administracion']."</td><td></td>";
                $tabla .= "</tr>";
                $tabla .= "<tr>";
                $tabla .= "<td>Gastos de Venta<br>(Promoción y Publicidad)</td>";
                $tabla .= "<td class='mone'>".$datas['SOLES']['resu_gastos_venta']."</td><td></td><td class='mone'>".$datas['DOLARES']['resu_gastos_venta']."</td><td></td>";
                $tabla .= "</tr>";
                $tabla .= "<tr>";
                $tabla .= "<th>Otros Ingresos (gastos)</th>";
                $tabla .= "<th></th><th class='mone'>".$datas['SOLES']['otros_ingresos']."</th><th></th><th class='mone'>".$datas['DOLARES']['otros_ingresos']."</th>";
                $tabla .= "</tr>";
                $tabla .= "<tr>";
                $tabla .= "<td>Ingresos Financieros</td>";
                $tabla .= "<td class='mone'>".$datas['SOLES']['resu_ingresos_financieros']."</td><td></td><td class='mone'>".$datas['DOLARES']['resu_ingresos_financieros']."</td><td></td>";
                $tabla .= "</tr>";
                $tabla .= "<tr>";
                $tabla .= "<td>Gastos Financieros</td>";
                $tabla .= "<td class='mone'>".$datas['SOLES']['resu_gastos_financieros']."</td><td></td><td class='mone'>".$datas['DOLARES']['resu_gastos_financieros']."</td><td></td>";
                $tabla .= "</tr>";
                $tabla .= "<tr>";
                $tabla .= "<td>Otros Ingresos</td>";
                $tabla .= "<td class='mone'>".$datas['SOLES']['resu_otros_ingresosf']."</td><td></td><td class='mone'>".$datas['DOLARES']['resu_otros_ingresosf']."</td><td></td>";
                $tabla .= "</tr>";
                $tabla .= "<tr>";
                $tabla .= "<td>otros Gastos</td>";
                $tabla .= "<td class='mone'>".$datas['SOLES']['resu_otros_gastos']."</td><td></td><td class='mone'>".$datas['DOLARES']['resu_otros_gastos']."</td><td></td>";
                $tabla .= "</tr>";
                $tabla .= "<tr>";
                $tabla .= "<td>Resultado por Explosición a la Inflación</td>";
                $tabla .= "<td class='mone'>".$datas['SOLES']['resu_inflacion']."</td><td></td><td class='mone'>".$datas['DOLARES']['resu_inflacion']."</td><td></td>";
                $tabla .= "</tr>";
                $tabla .= "<tr>";
                $tabla .= "<th>Resultados Antes de Participaciones</th>";
                $tabla .= "<th></th><th class='mone'>".($datas['SOLES']['utilidad_operativa']-$datas['SOLES']['otros_ingresos'])."</th><th></th><th class='mone'>".($datas['DOLARES']['utilidad_operativa']-$datas['DOLARES']['otros_ingresos'])."</th>";
                $tabla .= "</tr>";
                $data["existe"] = "SI";
            }else{
                $tabla = "<tr><th colspane='5'>Periodo no creado</th></tr>";
                $data["existe"] = "NO";
            }
            $data["tabla"] = $tabla;

            echo json_encode($data);
            exit(0);
        }
        
        
        for ($i=2018; $i <= date('Y')+2; $i++) { 
            $anios[$i] = $i;
        }
		//$datos["tipos"] = $this->Model_general->enum_valores('estado_resultados','resu_tipo');
		$datos["tipos"] = array("ESPERADO" => "Estado de resultados", "REAL" => "Real");
        $datos["cur_mes"] = date("m");
        $datos["cur_anio"] = date("Y");
        $datos["anio"] = $anios;
        $datos["mes"] = array(1=>"Enero",2=>"Febrero",3=>"Marzo",4=>"Abril",5=>"Mayo",6=>"Junio",7=>"Julio",8=>"Agosto",9=>"Septiembre",10=>"Octubre",11=>"Niviembre",12=>"Diciembre");
        $this->cssjs->add_js(base_url().'assets/js/Reporte/resultados.js?v=1.8',false,false);
        $this->load->view('header');
        $this->load->view('menu');
        $this->load->view($this->router->fetch_class().'/'.$this->router->fetch_method(), $datos);
        $this->load->view('footer');
    }
    public function crear_resultado($id = ""){
        if($id != ""){
            $this->db->select("FORMAT_DATE(resu_fecha,'%m') mes, FORMAT_DATE(resu_fecha,'%m')");
        }else{
            $resu = new stdClass;
            $resu->mes = date("m");
            $resu->anio = date("Y");
            $datos['resu'] = $resu;
        }
        
        for ($i=2018; $i <= date('Y')+2; $i++) { 
            $anios[$i] = $i;
        }
        $datos["anio"] = $anios;
        $datos["mes"] = array(1=>"Enero",2=>"Febrero",3=>"Marzo",4=>"Abril",5=>"Mayo",6=>"Junio",7=>"Julio",8=>"Agosto",9=>"Septiembre",10=>"Octubre",11=>"Niviembre",12=>"Diciembre");
        $this->load->view('Reporte/form_resultado', $datos);
    }
    public function guardar_resultado(){
        $mes = $this->input->post("mes");
        $anio = $this->input->post("anio");

        $condicion = array("MONTH(resu_fecha)" => $mes, "YEAR(resu_fecha)" => $anio);
        $consulta = $this->db->where($condicion)->get("estado_resultados");

        if($consulta->num_rows() > 0){
            $resp["exito"] = false;
            $resp["mensaje"] = "El periodo ya existe";
        }else{
            $fecha = $anio."-".($mes < 10?'0'.$mes:$mes)."-01";

            
			$tipos = $this->Model_general->enum_valores('estado_resultados','resu_tipo');
			
            $this->db->trans_begin();
			foreach($tipos as $tipo){
				
				$datas = array("resu_fecha" => $fecha, "resu_tipo" => $tipo);
				
				if(!$this->Model_general->guardar_registro("estado_resultados", array_merge($datas, array("resu_moneda" => "SOLES")))){
					$resp["exito"] = false;
					$resp["mensaje"] = "Ocurrio un error, intentelo mas tarde";
					$this->db->trans_rollback();
					$this->Model_general->dieMsg($resp);
				}
				if(!$this->Model_general->guardar_registro("estado_resultados", array_merge($datas, array("resu_moneda" => "DOLARES")))){
					$resp["exito"] = false;
					$resp["mensaje"] = "Ocurrio un error, intentelo mas tarde";
					$this->db->trans_rollback();
					$this->Model_general->dieMsg($resp);
				}
			}
            $resp["exito"] = true;
            $resp["mensaje"] = "Guardado con exito";
            $this->db->trans_commit();
        }
        echo json_encode($resp);
    }
    
    public function actualiza_resultados(){
        $mes = $this->input->get("mes");
        $anio = $this->input->get("anio");
		$tipo = $this->input->get("tipo");	

        $condicion = array("MONTH(resu_fecha)" => $mes, "YEAR(resu_fecha)" => $anio, "resu_tipo" => $tipo);
        $consulta = $this->db->where($condicion)->get("estado_resultados");
		
        if($consulta->num_rows() > 0){
            
            $monedas = $this->Model_general->enum_valores("estado_resultados", "resu_moneda");
            $this->db->trans_begin();
            foreach ($monedas as $i => $moneda) {
                $directo = $this->get_totalImporte("dire", $mes, $anio, $moneda, $tipo);
                $comprobante = $this->get_totalImporte("comp", $mes, $anio, $moneda, $tipo);
                $liquidacion = $this->get_totalImporte("liqu", $mes, $anio, $moneda, $tipo);
                $ventas_vetas = $directo + $comprobante + $liquidacion;
				
                if($moneda == "SOLES"){
                    $this->db->select("SUM(plan_remuTotal) total");
                    $this->db->from("planilla");
                    $this->db->join("planilla_periodo", "peri_id = plan_peri_id");
                    $this->db->where(array("peri_mes" => $mes, "peri_anio" => $anio));
					if($tipo != "REAL")
						$this->db->where("plan_espagado", 1);
                    $planilla = $this->db->get();
                    if($planilla->num_rows() > 0){
                        $planilla = ($planilla->row()->total != "") ? $planilla->row()->total : 0;
                    }else
                        $planilla = 0;
                }else  
                    $planilla = 0;
		
				if($tipo != "REAL") $this->db->select("SUM(sepr_pagado) total");
				else $this->db->select("SUM(sepr_total) + SUM(sepr_combu_total) total");
                $this->db->from("servicio_proveedor");
                $this->db->where(array("YEAR(sepr_fecha)" => $anio, "MONTH(sepr_fecha)" => $mes, "sepr_moneda" => $moneda));
                $gastos = $this->db->get();
                if($gastos->num_rows() > 0){
                    $gastos = ($gastos->row()->total != "") ? $gastos->row()->total : 0;
                }else
                    $gastos = 0;
                
                $where = array("YEAR(resu_fecha)" => $anio, 
                                "MONTH(resu_fecha)" => $mes,
                                "resu_moneda" => $moneda,
								"resu_tipo" => $tipo,
                            );
                $datas = array("resu_ventas_netas" => $ventas_vetas,
                                "resu_planilla" => $planilla,
                                "resu_gastos_operacionales" => $gastos
                        );
                $this->Model_general->guardar_edit_registro("estado_resultados", $datas, $where);
            }
            if ($this->db->trans_status() === FALSE){
                $json["exito"] = false;
                $json["mensaje"] = "Algo salio mal, intentelo mas tarde";
                $this->db->trans_rollback();
            }else{
                $json["exito"] = true;
                $json["mensaje"] = "Actualizado con exito";
                $this->db->trans_commit();
            }
        }else{
            $json["exito"] = false;
            $json["mensaje"] = "Periodo no creado";
        }
        echo json_encode($json);
    }
	/*
    public function get_totalImporte($tipo, $mes, $anio, $moneda){
		if($tipo == "dire"){
			$filtros = array("YEAR(DATE(deta_fechaserv))" => $anio,
								"paqu_esliquidacion" => 0,
								"paqu_escomprobante" => 0,
								"paqu_moneda" => $moneda
			);
			if($mes != "")
				$filtros["MONTH(DATE(deta_fechaserv))"] = $mes;
			$this->db->select("SUM(paqu_cobrado) total");
			$this->db->from("paquete");
			$this->db->join("paquete_detalle", "deta_paqu_id = paqu_id");
		}else if($tipo == "comp"){
			$filtros = array("YEAR(vent_fecha)" => $anio,
								"vent_moneda" => $moneda
			);
			if($mes != "")
				$filtros["MONTH(vent_fecha)"] = $mes;
			$this->db->select("SUM(vent_cobrado) total");
			$this->db->from("venta");
		}else{
			$filtros = array("YEAR(DATE(liqu_fechareg))" => $anio,
								"liqu_moneda" => $moneda
			);
			if($mes != "")
				$filtros["MONTH(DATE(liqu_fechareg))"] = $mes;
			$this->db->select("SUM(liqu_cobrado) total");
			$this->db->from("liquidacion");
		}
		$this->db->where($filtros);
		$consulta = $this->db->get();
		if($consulta->num_rows() > 0){
			$consulta = $consulta->row();
			if($consulta->total > 0 && $consulta->total != ""){
				return floatval($consulta->total);
			}else
				return 0;
		}else
			return 0;
    }
	*/
    public function actualiza_resultado(){
        $moneda = $this->input->get('moneda');
        $anio = $this->input->get('anio');
        $mes = $this->input->get('mes');
		$tipo = $this->input->get('tipo');

        $condiciones = array("MONTH(resu_fecha)" => $mes, "YEAR(resu_fecha)" => $anio, "resu_moneda" => $moneda, "resu_tipo" => $tipo);
        $resumen = $this->db->where($condiciones)->get("estado_resultados")->row();
        $datos["resu"] = $resumen;
        $this->load->view('Reporte/form_resultadoGen', $datos);
    }
    public function guardar_resultadoGen($resu_id = ""){
        $otros_ingresos = $this->input->post("otros_ingresos");
        $gastos_administracion = $this->input->post("gastos_administracion");
        $gastos_venta = $this->input->post("gastos_venta");
        $ingresos_financieros = $this->input->post("ingresos_financieros");
        $gastos_financieros = $this->input->post("gastos_financieros");
        $otros_ingresosf = $this->input->post("otros_ingresosf");
        $otros_gastos = $this->input->post("otros_gastos");
        $inflacion = $this->input->post("inflacion");
		$planilla = $this->input->post("planillas");

        $datos = array("resu_otros_ingresos" => $otros_ingresos,
                        "resu_gastos_administracion" => $gastos_administracion,
                        "resu_gastos_venta" => $gastos_venta,
                        "resu_ingresos_financieros" => $ingresos_financieros,
                        "resu_gastos_financieros" => $gastos_financieros,
                        "resu_otros_ingresosf" => $otros_ingresosf,
                        "resu_otros_gastos" => $otros_gastos,
                        "resu_inflacion" => $inflacion,
						"resu_planilla" => $planilla
        );

        $where = array("resu_id" => $resu_id);
        if($this->Model_general->guardar_edit_registro("estado_resultados", $datos, $where)){
            $resp["exito"] = true;
            $resp["mensaje"] = "Datos guardados con exito";
        }else{
            $resp["exito"] = false;
            $resp["mensaje"] = "Ocurrio un error";
        }
        echo json_encode($resp);
    }
}