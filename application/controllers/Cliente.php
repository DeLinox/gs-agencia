<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Cliente extends CI_Controller {

    function __construct() {
        parent::__construct();
        
        if(!$this->session->userdata('authorized')){
            redirect(base_url()."login");
        }
        $this->load->database();
        $this->configuracion = $this->db->query("SELECT * FROM configuracion")->row();
        $this->permisos  = $this->Model_general->getPermisos($this->usua_id);
        $this->load->model("Model_general");
    }
    public function listado() {
    	if($this->session->userdata('authorizedadmin') == 3){
    		$this->load->view('header');
	        $this->load->view('menu');
	        $this->load->view('Venta/permisos');
	        $this->load->view('footer');
	        
    	}else{
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

        $this->cssjs->set_path_js(base_url() . "assets/js/Cliente/");
        $this->cssjs->add_js('listado');
        $script['js'] = $this->cssjs->generate_js();
        $this->load->view('header', $script);
        $this->load->view('menu');
        $this->load->view('cliente/listado', $datos);
        $this->load->view('footer');
        }
    }
    

    public function buscar() {
        $responese = new StdClass;
        $search = isset($_GET['q']) ? $_GET["q"] : '';
        $tipo = $_GET['t'];
        $datos = array();
        
        if($search == 'num'){
            $doc_num = $_GET['num'];
            $producto = $this->Model_general->select2("cliente", array("clie_docnum" => $doc_num));
        }else{
            //$producto = $this->Model_general->select2("cliente", array("clie_rsocial" => $search, "clie_abrev" => $search));
            $tipo = ($tipo == 'LOCAL')?'RECEPTIVO':'LOCAL';
            $producto = $this->db->like("CONCAT(clie_rsocial,clie_abrev)", $search)->where("clie_reserv_tipo <>", $tipo)->get("cliente")->result();
        }
        foreach ($producto as $value) {
            $datos[] = array("id" => $value->clie_id, "text" => $value->clie_rsocial, "direccion" => $value->clie_direccion, "docnum" => $value->clie_doc_nro, "docu" => $value->clie_tdoc_id, "email" => $value->clie_email, "codigo" => $value->clie_abrev, "lunch_prec" => $value->clie_lunch_prec);
        }
        
        $responese->total_count = count($producto);
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
            $cliente->clie_abrev = "";
            $cliente->clie_tdoc_id = "";
            $cliente->clie_doc_nro = "";
            $cliente->clie_tipo = "";
            $cliente->clie_activo = "";
            $cliente->clie_direccion = "";
            $cliente->clie_email = "";
        }else{
            $this->db->where('clie_id',$clie_id);
            $this->db->from('cliente');
            $cliente = $this->db->get()->row();
        }
        $datos["docu_options"] = $this->Model_general->getOptions('documento_tipo',array('tdoc_id','tdoc_nombre'));
        $datos["tipo"] = $this->Model_general->enum_valores('cliente','clie_tipo');
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

    function guardar($clie_id = 0) {
        $rsocial = $this->input->post('rsocial');
        $documento = $this->input->post('documento');
        $docnum = $this->input->post('docnum');
        $tipo = $this->input->post('tipo');
        $email = $this->input->post('email');
        $abrev = $this->input->post('abrev');
        $estado = $this->input->post('estado');
        $direccion = $this->input->post('direccion');

        $datos = array("clie_tdoc_id" => $documento,
            "clie_rsocial" => $rsocial,
            "clie_abrev" => $abrev,
            "clie_doc_nro" => $docnum,
            "clie_direccion" => $direccion,
            "clie_tipo" => $tipo,
            "clie_activo" => $estado,
            "clie_email" => $email,
        );
        if ($clie_id!='0') {
            $condicion = array("clie_id" => $clie_id);
            if ($this->Model_general->guardar_edit_registro("cliente", $datos, $condicion) == TRUE):
                $json['exito'] = true;
            else:
                $json['exito'] = false;
                $json['mensaje'] = "Error al actualizar los datos";
            endif;
        }
        else {
            $cons = $this->db->from('cliente')->where('clie_doc_nro', $docnum)->get();
            if($cons->num_rows() > 0){
                $json['exito'] = false;
                $json['mensaje'] = "El cliente ya existe";
            }else{
                if (($meta = $this->Model_general->guardar_registro("cliente", $datos)) == TRUE):
                    $json['exito'] = true;
                    $json['datos'] = array_merge(array('clie_id'=>$meta['id']),$datos);
                    $json['mensaje'] = "Cliente agregado con exito";
                else:
                    $json['exito'] = false;
                    $json['mensaje'] = "Error al guardar los datos";
                endif;
            }
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
    function consulta_sunat(){
        $doc_num = $this->input->post('nruc');
        $this->load->library('curl');
        $this->load->library('sunat');
        
        $cliente = new Sunat();
        echo $cliente->search( $doc_num, true );
    }
    function consulta_reniec(){
        $doc_num = $this->input->post('nruc');
        $this->load->library('solver');
        $this->load->library('curl');
        $this->load->library('reniec');
        
        $cliente = new Reniec();
        echo $cliente->search( $doc_num, true );
    }
    function reporte_excel(){
        $search = $this->input->get('search');
        
        $this->db->select("C.clie_rsocial as rsocial, C.clie_docnum as docnum, D.docu_nombre as documento, C.clie_direccion as direccion, C.clie_email as email");
        $this->db->from("cliente C");
        $this->db->join("maestra_documentos D","D.docu_id = C.clie_docu_id");
        if($search != '')
            $this->db->like("C.clie_rsocial", $search);
        $clientes = $this->db->get()->result();
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
                ->setCellValue('A1', 'CLIENTE')
                ->setCellValue('B1', 'DOCUMENTO')
                ->setCellValue('C1', 'NUMERO')
                ->setCellValue('D1', 'DIRECCION')
                ->setCellValue('E1', 'EMAIL');
        
        $ini = 3;
        $index = 0;
        $objPHPExcel->getActiveSheet()->getStyle('C')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
        
        foreach($clientes as $fila){
            $nro = $index+$ini;
            $index++;
            $objPHPExcel->getActiveSheet()
                        ->setCellValue("A$nro", $fila->rsocial)
                        ->setCellValue("B$nro", $fila->documento)
                        ->setCellValue("C$nro", $fila->docnum)
                        ->setCellValue("D$nro", $fila->direccion)
                        ->setCellValue("E$nro", $fila->email);
            }
            foreach(range('A','Q') as $nro)
            {
                $objPHPExcel->getActiveSheet()->getColumnDimension($nro)->setAutoSize(true);
            }
        
        $fin = $index+$ini-1;
        //$objPHPExcel->getActiveSheet()->getStyle("I$ini:I$fin")->getNumberFormat()->setFormatCode('#,##0.00'); 
        //$objPHPExcel->getActiveSheet()->getStyle("A$ini:A$fin")->getNumberFormat()->setFormatCode('dd/mm/yyyy');

        
        $excel->excel_output($objPHPExcel, 'CLIENTES '.date('d/m/Y'));
    }
}

