<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Servicio extends CI_Controller {

    function __construct() {
        parent::__construct();
        if(!$this->session->userdata('authorized')){
            redirect(base_url()."login");
        }
        $this->usua_id = $this->session->userdata('authorized');
        $this->configuracion = $this->db->query("SELECT * FROM usuario WHERE usua_id='{$this->usua_id}'")->row();
        $this->permisos  = $this->Model_general->getPermisos($this->usua_id);
        $this->editar = $this->permisos[10]->nivel_acceso;
    }
    public function serv_listado() {
        $this->load->helper('Funciones');
        $this->load->database();
        //$this->load->library('Framework');
        $this->load->library('Ssp');
        $this->load->library('Cssjs');
        
        $servicios = $this->Model_general->getServicios();
        $datos['servicios'] = $servicios;
        $datos["titulo"] = "Servicios";


        $this->cssjs->set_path_js(base_url() . "assets/js/Servicio/");
        $this->cssjs->add_js('listado');
        $script['js'] = $this->cssjs->generate_js();
        $this->load->view('header', $script);
        $this->load->view('menu');
        $this->load->view($this->router->fetch_class().'/'.$this->router->fetch_method(), $datos);
        $this->load->view('footer');
    }
    public function categorias() {
        $this->load->helper('Funciones');
        $this->load->database();
        //$this->load->library('Framework');
        $this->load->library('Ssp');
        $this->load->library('Cssjs');
        
        $json = isset($_GET['json']) ? $_GET['json'] : false;

        $columns = array(
            array('db' => 'cate_id',        'dt' => 'ID',           "field" => "cate_id"),
            array('db' => 'cate_nombre',    'dt' => 'Categoria',    "field" => "cate_nombre"),
            array('db' => 'cate_id',        'dt' => 'DT_RowId',     "field" => "cate_id")
        );

        if ($json) {

            $json = isset($_GET['json']) ? $_GET['json'] : false;

            $table = 'producto_categoria';
            $primaryKey = 'cate_id';

            $sql_details = array(
                'user' => $this->db->username,
                'pass' => $this->db->password,
                'db' => $this->db->database,
                'host' => $this->db->hostname
            );

            $condiciones = array();
            $joinQuery = "FROM producto_categoria";
            $where = "";
            if (!empty($_POST['categoria']))
                $condiciones[] = "cate_id='".$_POST['categoria']."'";

            $where = count($condiciones) > 0 ? implode(' AND ', $condiciones) : "";
            echo json_encode(
                    $this->ssp->simple($_POST, $sql_details, $table, $primaryKey, $columns, $joinQuery, $where)
            );
            exit(0);
        }
        $datos["categorias"] = $this->Model_general->getOptions('producto_categoria', array("cate_id", "cate_nombre"),'* Categoría');
        $datos['columns'] = $columns;
        $datos['titulo'] = 'Categorias';
        $datos['direccion'] = '_cat';
        

        $this->cssjs->set_path_js(base_url() . "assets/js/Producto/");
        $this->cssjs->add_js('listado_cat');
        $script['js'] = $this->cssjs->generate_js();
        $this->load->view('header', $script);
        $this->load->view('menu');
        $this->load->view('producto/listado', $datos);
        $this->load->view('footer');
    }
    public function buscar() {
        $responese = new StdClass;
        $search = isset($_GET['q']) ? $_GET["q"] : '';
        $datos = array();
        //$producto = $this->Model_general->select2("producto", array("prod_nombre" => $search));
        $this->db->like("CONCAT(prod_codigo,' ',prod_nombre)", $search);
        $producto = $this->db->get('producto')->result();
        foreach ($producto as $value) {
            $unid_nombre = $this->db->query("SELECT unid_nombre,unid_sigla FROM unidad WHERE unid_sigla = '{$value->prod_unidad}'");
            
            if($unid_nombre->num_rows() > 0) $unid_nombre = $unid_nombre->row();
            else $unid_nombre = false;
            $datos[] = array("id" => $value->prod_id, "text" => $value->prod_nombre, "unidad" => $value->prod_unidad, "precio" => $value->prod_precio, "valor" => $value->prod_valor, "tipo" => $value->prod_tipo, "igvtipo" => $value->prod_igvtipo, "unid_nombre" => $unid_nombre->unid_sigla , "codigo" => $value->prod_codigo);
            
        }
        $responese->total_count = count($producto);
        $responese->items = $datos;

        echo json_encode($responese);
    }
    
    public function crear($id=0) {
        $this->load->helper('Funciones');
        $producto = new stdClass();
        if($id == 0){
            $producto->prod_id = 0;
            $producto->prod_nombre = "";
            $producto->prod_tipo = "";
            $producto->prod_codigo = "";
            $producto->prod_costo = "";
            $producto->prod_precio = "";
            $producto->prod_valor = "";
            $producto->prod_igvtipo = "";
            $producto->prod_unidad = "";
            $producto->prod_cate_id = "";
        }else{
            $this->db->select('prod_id, prod_cate_id, prod_nombre, prod_tipo, prod_codigo, prod_precio, prod_valor, prod_unidad, prod_igvtipo');
            $this->db->where('prod_id',$id);
            $this->db->from('producto');
            $prod = $this->db->get()->row();
            $producto->prod_id = $prod->prod_id;
            $producto->prod_nombre = $prod->prod_nombre;
            $producto->prod_tipo = $prod->prod_tipo;
            $producto->prod_codigo = $prod->prod_codigo;
            $producto->prod_costo = "";
            $producto->prod_precio = $prod->prod_precio;
            $producto->prod_valor = $prod->prod_valor;
            $producto->prod_igvtipo = $prod->prod_igvtipo;
            $producto->prod_unidad = $prod->prod_unidad;
            $producto->prod_cate_id = $prod->prod_cate_id;
        }
        $producto->unidad_options = $this->Model_general->getOptions('unidad',array('unid_sigla','unid_nombre'));
        $producto->cate_options = $this->Model_general->getOptions('producto_categoria',array('cate_id','cate_nombre'));
        $datos['producto'] = $producto;
        $this->load->view('producto/form_crear', $datos);
    }
    public function crear_cat($id=0) {
        $this->load->helper('Funciones');
        $categoria = new stdClass();
        if($id == 0){
            $categoria->cat_id = 0;
            $categoria->cat_nombre = "";
        }else{
            $this->db->select('cate_id, cate_nombre');
            $this->db->where('cate_id',$id);
            $this->db->from('producto_categoria');
            $cat = $this->db->get()->row();
            $categoria->cat_id = $cat->cate_id;
            $categoria->cat_nombre = $cat->cate_nombre;
        }
        $datos['categoria'] = $categoria;
        $this->load->view('producto/form_crear_cat', $datos);
    }

    function guardar($prod_id=0) {
        $nombre = $this->input->post('nombre');
        $tipo = $this->input->post('tipo');
        $codigo = $this->input->post('codigo');
        $precio = $this->input->post('precio');
        $valor = $this->input->post('valor');
        $categoria = $this->input->post('categoria');
        $igvtipo = $this->input->post('igvtipo');
        $descripcion = $this->input->post('descripcion');
        $unid = $this->input->post('unid_id');
        $datos = array("prod_nombre" => $nombre,
            "prod_tipo" => $tipo,
            "prod_codigo" => $codigo,
            "prod_precio" => $precio,
            "prod_valor" => $valor,
            "prod_igvtipo" => $igvtipo,
            "prod_cate_id" => $categoria,
            "prod_descripcion" => $descripcion,
            "prod_unidad" => $unid);
        if ($prod_id!='0') {
            $this->load->database();
            if ($this->Model_general->guardar_edit_registro("producto", $datos, array("prod_id" => $prod_id)) == TRUE):
                $json['exito'] = true;
            else:
                $json['exito'] = false;
                $json['mensaje'] = "Error al actualizar los datos";
            endif;
        }
        else {
            if (($meta = $this->Model_general->guardar_registro("producto", $datos)) == TRUE):
                $json['exito'] = true;
                $json['datos'] = array_merge(array('clie_id'=>$meta['id']),$datos);
            else:
                $json['exito'] = false;
                $json['mensaje'] = "Error al guardar los datos";
            endif;
        }
        echo json_encode($json);
    }
    function guardar_cat($prod_id=0) {
        $nombre = $this->input->post('nombre');
        $datos = array("cate_nombre" => $nombre);
        if ($prod_id!='0') {
            $this->load->database();
            if ($this->Model_general->guardar_edit_registro("producto_categoria", $datos, array("cate_id" => $prod_id)) == TRUE):
                $json['exito'] = true;
            else:
                $json['exito'] = false;
                $json['mensaje'] = "Error al actualizar los datos";
            endif;
        }
        else {
            if (($meta = $this->Model_general->guardar_registro("producto_categoria", $datos)) == TRUE):
                $json['exito'] = true;
                $json['datos'] = array_merge(array('cate_id'=>$meta['id']),$datos);
            else:
                $json['exito'] = false;
                $json['mensaje'] = "Error al guardar los datos";
            endif;
        }
        echo json_encode($json);
    }
    function eliminar($id){
        $serv = $this->db->where("serv_id",$id)->get("servicio")->row();

        $serv_paqu = $this->db->where("deta_serv_id",$id)->get("paquete_detalle");
        if($serv_paqu->num_rows() > 0){
            $json["exito"] = false;
            $json["mensaje"] = "No es posible eliminar el servicio debido a que esta registrado en uno o más reservas";
        }else{
            $this->db->query("DELETE FROM servicio WHERE serv_id = {$id}");
            $this->Model_general->add_log("ELIMINAR",11,"Eliminación de servicio: ".$serv->serv_descripcion);
            $json["exito"] = true;
            $json["mensaje"] = "Eliminado con exito";
        }
        die(json_encode($json));
    }
    function reporte_excel(){
        $categoria = $this->input->get('categoria');
        $search = $this->input->get('search');
        
        $this->db->select("P.prod_nombre as nombre, P.prod_codigo as codigo, P.prod_costo as costo, P.prod_precio as precio, P.prod_valor as valor, P.prod_desc1 as desc, U.unid_nombre as unidad, C.cate_nombre categoria");
        $this->db->from("producto P");
        $this->db->join("producto_categoria C","C.cate_id = P.prod_cate_id");
        $this->db->join("unidad U","U.unid_sigla = P.prod_unidad");
        if($categoria != '')
            $this->db->where("C.cate_id", $categoria);
        if($search != '')
            $this->db->like("P.prod_nombre", $search);
        $this->db->order_by('C.cate_nombre');
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
                ->setCellValue('A1', 'CATEGORIA')
                ->setCellValue('B1', 'PRODUCTO')
                ->setCellValue('C1', 'CODIGO')
                ->setCellValue('D1', 'PRECIO')
                ->setCellValue('E1', 'VALOR')
                ->setCellValue('F1', 'UNIDAD');
        $ini = 3;
        $index = 0;
        
        foreach($productos as $fila){
            $nro = $index+$ini;
            $index++;
            $objPHPExcel->getActiveSheet()
                        ->setCellValue("A$nro", $fila->categoria)
                        ->setCellValue("B$nro", $fila->nombre)
                        ->setCellValue("C$nro", $fila->codigo)
                        ->setCellValue("D$nro", $fila->precio)
                        ->setCellValue("E$nro", $fila->valor)
                        ->setCellValue("F$nro", $fila->unidad);
            }
            foreach(range('A','Q') as $nro)
            {
                $objPHPExcel->getActiveSheet()->getColumnDimension($nro)->setAutoSize(true);
            }
        
        $fin = $index+$ini-1;
        $objPHPExcel->getActiveSheet()->getStyle("D$ini:E$fin")->getNumberFormat()->setFormatCode('#,##0.00'); 
        //$objPHPExcel->getActiveSheet()->getStyle("A$ini:A$fin")->getNumberFormat()->setFormatCode('dd/mm/yyyy');
        
        $excel->excel_output($objPHPExcel, 'PRODUCTOS '.date('d/m/Y'));
    }
    function reporte_categoria(){
        $categoria = $this->input->get('categoria');
        $search = $this->input->get('search');
        
        $this->db->from("producto_categoria");
        if($categoria != '')
            $this->db->where("cate_id", $categoria);
        if($search != '')
            $this->db->like("cate_nombre", $search);
        $categorias = $this->db->get()->result();
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
                ->setCellValue('A1', 'ID')
                ->setCellValue('B1', 'CATEGORIA');
        $ini = 3;
        $index = 0;
        
        foreach($categorias as $fila){
            $nro = $index+$ini;
            $index++;
            $objPHPExcel->getActiveSheet()
                        ->setCellValue("A$nro", $fila->cate_id)
                        ->setCellValue("B$nro", $fila->cate_nombre);
            }
            foreach(range('A','Q') as $nro)
            {
                $objPHPExcel->getActiveSheet()->getColumnDimension($nro)->setAutoSize(true);
            }
        
        $fin = $index+$ini-1;
        $excel->excel_output($objPHPExcel, 'CATEGORIAS '.date('d/m/Y'));
    }

}
