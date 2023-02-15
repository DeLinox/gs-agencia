<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Cuenta extends CI_Controller {

    function __construct() {
        parent::__construct();
        if(!$this->session->userdata('authorized')){
            redirect(base_url()."login");
        }
        $this->usua_id = $this->session->userdata('authorized');
        $this->configuracion = $this->db->query("SELECT * FROM usuario WHERE usua_id='{$this->usua_id}'")->row();
        $this->permisos  = $this->Model_general->getPermisos($this->usua_id);
        $this->editar = $this->permisos[6]->nivel_acceso;
        $this->load->model("Model_general");
        $this->load->library('Cssjs');
        $this->load->model("Model_general");
        $this->load->helper('Form');
    }
    public function listado() {
        $this->load->helper('Funciones');
        $this->load->database();
        //$this->load->library('Framework');
        $this->load->library('Ssp');
        $this->load->library('Cssjs');
        
        $json = isset($_GET['json']) ? $_GET['json'] : false;

        $columns = array(
            array('db' => 'cuen_id',            'dt' => 'ID',           "field" => "cuen_id"),
            array('db' => 'cuen_banco',         'dt' => 'Banco',        "field" => "cuen_banco"),
            array('db' => 'cuen_titular',       'dt' => 'Titular',      "field" => "cuen_titular"),
            array('db' => 'cuen_numero',        'dt' => 'Numero',       "field" => "cuen_numero"),
            array('db' => 'cuen_cci',           'dt' => 'CCI',          "field" => "cuen_cci"),
            array('db' => 'cuen_monto',         'dt' => 'Monto',        "field" => "cuen_monto"),
            array('db' => 'cuen_activo',        'dt' => 'Activo',       "field" => "cuen_activo"),
            array('db' => 'cuen_moneda',        'dt' => 'Moneda',       "field" => "cuen_moneda"),
            array('db' => 'sucu_nombre',        'dt' => 'Sucursal',     "field" => "sucu_nombre"),
            array('db' => 'cuen_id',            'dt' => 'DT_RowId',     "field" => "cuen_id")
        );

        if ($json) {

            $json = isset($_GET['json']) ? $_GET['json'] : false;

            $table = 'cuenta';
            $primaryKey = 'cuen_id';

            $sql_details = array(
                'user' => $this->db->username,
                'pass' => $this->db->password,
                'db' => $this->db->database,
                'host' => $this->db->hostname
            );

            $condiciones = array();
            $joinQuery = "FROM cuenta INNER JOIN sucursal ON sucu_id=cuen_sucu_id";
            $where = "";
            $condiciones[]="";

            $where = count($condiciones) > 0 ? implode(' AND ', $condiciones) : "";
            echo json_encode(
                    $this->ssp->simple($_POST, $sql_details, $table, $primaryKey, $columns, $joinQuery, $where)
            );
            exit(0);
        }
        $datos['columns'] = $columns;

        $this->cssjs->set_path_js(base_url() . "assets/js/Cuenta/");
        $this->cssjs->add_js('listado');
        $script['js'] = $this->cssjs->generate_js();
        $this->load->view('header', $script);
        $this->load->view('menu');
        $this->load->view('cuenta/listado', $datos);
        $this->load->view('footer');
    }
    public function cuen_movimiento() {
        $this->load->helper('Funciones');
        $this->load->database();
        //$this->load->library('Framework');
        $this->load->library('Ssp');
        $this->load->library('Cssjs');
        
        $json = isset($_GET['json']) ? $_GET['json'] : false;
        $fecha = 'DATE_FORMAT(movi_fechareg,"%d/%m/%Y %h:%i %p")';
        $file = 'CONCAT(cuen_codigo,"-",movi_file)';
        $columns = array(
            array('db' => 'movi_id',            'dt' => 'ID',           "field" => "movi_id"),
            array('db' => $fecha,               'dt' => 'FECHA',        "field" => $fecha),
            array('db' => $file,                'dt' => 'FILE',         "field" => $file),
            //array('db' => 'cuen_banco',         'dt' => 'CUENTA',       "field" => "cuen_banco"),
            array('db' => 'movi_tipo',          'dt' => 'TIPO',         "field" => "movi_tipo"),
            array('db' => 'movi_moneda',        'dt' => 'MONEDA',       "field" => "movi_moneda"),
            array('db' => 'movi_ingreso',       'dt' => 'INGRESO',      "field" => "movi_ingreso"),
            array('db' => 'movi_egreso',        'dt' => 'EGRESO',       "field" => "movi_egreso"),
            //array('db' => 'movi_saldo',         'dt' => 'SALDO',        "field" => "movi_saldo"),
            array('db' => 'movi_descripcion',   'dt' => 'DESCRIPCION',  "field" => "movi_descripcion"),
            array('db' => 'movi_obs',           'dt' => 'OBSERVACION',  "field" => "movi_obs"),
            array('db' => 'movi_id',            'dt' => 'DT_RowId',     "field" => "movi_id")
        );

        if ($json) {

            $json = isset($_GET['json']) ? $_GET['json'] : false;

            $table = 'cuenta_movimiento';
            $primaryKey = 'movi_id';

            $sql_details = array(
                'user' => $this->db->username,
                'pass' => $this->db->password,
                'db' => $this->db->database,
                'host' => $this->db->hostname
            );

            $condiciones = array();
            $joinQuery = "FROM cuenta_movimiento LEFT JOIN cuenta_movimiento_tipo ON movi_tipo_id = tipo_id INNER JOIN cuenta ON cuen_id = movi_cuen_id";
            $where = "";
            if (!empty($_POST['desde'])&&!empty($_POST['hasta'])){
                $condiciones[] = "DATE(movi_fechareg) >='".$_POST['desde']."' AND DATE(movi_fechareg) <='".$_POST['hasta']."'";
            }
            if (!empty($_POST['tipo'])){
                $condiciones[] = "movi_tipo ='".$_POST['tipo']."'";
            }
            if (!empty($_POST['cuenta'])){
                $condiciones[] = "movi_cuen_id ='".$_POST['cuenta']."'";
            }
            $where = count($condiciones) > 0 ? implode(' AND ', $condiciones) : "";
            echo json_encode(
                    $this->ssp->simple($_POST, $sql_details, $table, $primaryKey, $columns, $joinQuery, $where)
            );
            exit(0);
        }
        $datos['columns'] = $columns;
        $datos["tipo"] = array_merge(array(''=>'* Tipo'),$this->Model_general->enum_valores('cuenta_movimiento','movi_tipo'));
        $datos["cuentas"] = $this->Model_general->getData("cuenta", array("cuen_id","cuen_codigo"));

        $this->cssjs->add_js(base_url().'assets/js/Cuenta/listado.js',false,false);
        $this->load->view('header');
        $this->load->view('menu');
        $this->load->view($this->router->fetch_class().'/'.$this->router->fetch_method(), $datos);
        $this->load->view('footer');
    }
    public function getCuenta(){
        $cuen_id = $this->input->post('cuen_id');
        
        if($cuen_id != ''){
            $consulta = $this->db->where('cuen_id', $cuen_id)->from('cuenta')->get();
            if($consulta->num_rows() > 0){
                $json['exito'] = true;
                $json['data'] = $consulta->row();
            }else{
                $json['exito'] = false;
                $json['data'] = "algo salio mal";
            }
        }else{
            $json['exito'] = false;
            $json['data'] = "algo salio mal";
        }
        echo json_encode($json);
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

    public function crear($cuen_id=0) {
        $this->load->helper('Funciones');
        $cuenta = new stdClass();
        if ($cuen_id == 0) {
            $cuenta->cuen_id = 0;
            $cuenta->cuen_banco = "";
            $cuenta->cuen_titular = "";
            $cuenta->cuen_numero = "";
            $cuenta->cuen_cci = "";
            $cuenta->cuen_codigo = "";
            $cuenta->cuen_moneda = "SOLES";
        }else{
            $this->db->select('cuen_id, cuen_banco, cuen_codigo, cuen_titular, cuen_numero, cuen_cci, cuen_moneda');
            $this->db->where('cuen_id',$cuen_id);
            $this->db->from('cuenta');
            $cuent = $this->db->get()->row();
            $cuenta->cuen_id = $cuent->cuen_id;
            $cuenta->cuen_banco = $cuent->cuen_banco;
            $cuenta->cuen_titular = $cuent->cuen_titular;
            $cuenta->cuen_numero = $cuent->cuen_numero;
            $cuenta->cuen_cci = $cuent->cuen_cci;
            $cuenta->cuen_codigo = $cuent->cuen_codigo;
            $cuenta->cuen_moneda = $cuent->cuen_moneda;
        }
        $datos['cuenta'] = $cuenta;
        $this->load->view('Cuenta/form_crear', $datos);
    }

    public function cambios($id_cuen='', $tipo){
        $this->load->helper('Funciones');
        $cuenta = new stdClass();
        if($id_cuen != ''){
            $cuent = $this->db->select(array('cuen_id', 'cuen_banco', 'cuen_moneda', 'cuen_monto'))->from('cuenta')->where('cuen_id',$id_cuen)->get();

            $cuent = ($cuent->num_rows() > 0 ) ? $cuent->row() : '';
            $cuenta->cuen_id = $cuent->cuen_id;
            $cuenta->cuen_banco = $cuent->cuen_banco;
            $cuenta->cuen_moneda = $cuent->cuen_moneda;
            $cuenta->cuen_monto = $cuent->cuen_monto;

            $datos['cuenta'] = $cuenta;
            $datos["tipo_mov"] = $this->Model_general->getOptionsWhere('kardex_dinero_tipo', array("tipo_id", "tipo_nombre"),array("tipo_tipo" => (int)$tipo));
            $datos["comprobantes"] = $this->Model_general->getOptions('maestra_comprobantes', array("comp_id", "comp_nombre"));
            $datos['tipo'] = $tipo;
            $datos['titulo'] = $tipo == 1 ? 'Agregar' : 'Retirar';
            
            $this->load->view('cuenta/form_update', $datos);
        } 
    }
    public function ajustar($id_cuen=''){
        $this->load->helper('Funciones');
        $cuenta = new stdClass();
        if($id_cuen != ''){
            $cuent = $this->db->select(array('cuen_id', 'cuen_banco', 'cuen_moneda', 'cuen_monto'))->from('cuenta')->where('cuen_id',$id_cuen)->get();
            $cuent = ($cuent->num_rows() > 0 ) ? $cuent->row() : '';
            $cuenta->cuen_id = $cuent->cuen_id;
            $cuenta->cuen_banco = $cuent->cuen_banco;
            $cuenta->cuen_moneda = $cuent->cuen_moneda;
            $cuenta->cuen_monto = $cuent->cuen_monto;

            $datos['cuenta'] = $cuenta;
            $datos['titulo'] = 'Ajustar';
            
            $this->load->view('cuenta/form_ajustar', $datos);
        } 
    }
    public function transferir($id_cuen=''){
        $this->load->helper('Funciones');
        $cuenta = new stdClass();
        if($id_cuen != ''){
            $cuent = $this->db->select(array('cuen_id', 'cuen_banco', 'cuen_moneda', 'cuen_monto'))->from('cuenta')->where('cuen_id',$id_cuen)->get()->row();
            $cuenta->cuen_id = $cuent->cuen_id;
            $cuenta->cuen_banco = $cuent->cuen_banco;
            $cuenta->cuen_moneda = $cuent->cuen_moneda;
            $cuenta->cuen_monto = $cuent->cuen_monto;
            $datos["cuentas"] = $this->Model_general->getOptionsWhere('cuenta', array("cuen_id", "cuen_banco"),array("cuen_moneda" => $cuent->cuen_moneda, "cuen_id <>" => "{$id_cuen}"));
            //$datos["cuentas"] = array_merge(array("0" => "-- Seleccione --"),$datos["cuentas"]);
            $datos['cuenta'] = $cuenta;
            $datos['titulo'] = 'Ajustar';
            
            $this->load->view('cuenta/form_transferir', $datos);
        } 
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
    private function validarCuenta(){
        $this->load->library('Form_validation');
        $this->form_validation->set_rules('banco', 'Banco', 'required');
        $this->form_validation->set_rules('titular', 'Titular', 'required');
        $this->form_validation->set_rules('numero', 'Numero', 'required');
        if ($this->form_validation->run() == FALSE){        
            $this->Model_general->dieMsg(array('exito'=>false,'mensaje'=>validation_errors()));
        }
        
    }
    function guardar($cuen_id=0) {

        $this->validarCuenta();
        $banco = $this->input->post('banco');
        $titular = $this->input->post('titular');
        $numero = $this->input->post('numero');
        $cci = $this->input->post('cci');
        $codigo = $this->input->post('codigo');
        $moneda = $this->input->post('moneda');

        $datos = array("cuen_banco" => $banco,
            "cuen_titular" => $titular,
            "cuen_numero" => $numero,
            "cuen_cci" => $cci,
            "cuen_codigo" => $codigo,
            "cuen_moneda" => $moneda);
        if ($cuen_id!='0') {
            $this->load->database();
            if ($this->Model_general->guardar_edit_registro("cuenta", $datos, array("cuen_id" => $cuen_id)) == TRUE):
                $json['exito'] = true;
                $json['mensaje'] = "Datos guardados con exito";
            else:
                $json['exito'] = false;
                $json['mensaje'] = "Error al actualizar los datos";
            endif;
        }
        else {
            if (($meta = $this->Model_general->guardar_registro("cuenta", $datos)) == TRUE):
                $json['exito'] = true;
                $json['datos'] = array_merge(array('cuen_id'=>$meta['id']),$datos);
                $json['mensaje'] = "Datos guardados con exito";
            else:
                $json['exito'] = false;
                $json['mensaje'] = "Error al guardar los datos";
            endif;
        }
        echo json_encode($json);
    }
    public function guardar_upd($id_cuen){
        $tipo = $this->input->post('tipo'); //1 agregar, 2 retirar
        $banco = $this->input->post('banco');
        $moneda = $this->input->post('moneda');
        $montoa = $this->input->post('montoa');
        $montou = $this->input->post('montou');
        $tipo_mov = $this->input->post('tipo_mov');
        $comprobante = $this->input->post('comprobante');
        $descripcion = $this->input->post('descripcion');

        $this->db->trans_start();

        $this->db->query("CALL encaja('{$tipo}',{$tipo_mov},'{$moneda}',{$montou},{$id_cuen},'{$descripcion}',{$this->session->userdata('authorized')})");

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE){
            $json['exito'] = false;
            $json['mensaje'] = "Hubo un error al guardar los datos";        
        }else{
            $json['exito'] = true;
            $json['mensaje'] = "Datos actualizados con exito";        
        }        
        echo json_encode($json);
    }
    public function guardar_ajust($id_cuen){
        $cuenta = $this->input->post('cuenta');
        $moneda = $this->input->post('moneda');
        $caja_sistema = $this->input->post('caja_sistema');
        $caja_real = $this->input->post('caja_real');
        $caja_retirar = $this->input->post('caja_retirar');
        $caja_agregar = $this->input->post('caja_agregar');
        $descripcion = $this->input->post('descripcion');

        $this->db->trans_start();

        if($caja_agregar!=0){
            $tipo = 1;
            $monto = $caja_agregar;
        }else{
            $tipo = 2;
            $monto = $caja_retirar;
        }

        $this->db->query("CALL encaja('{$tipo}',3,'{$moneda}',{$monto},{$id_cuen},'{$descripcion}',{$this->session->userdata('authorized')})");

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE){
            $json['exito'] = false;
            $json['mensaje'] = "Hubo un error al guardar los datos";        
        }else{
            $json['exito'] = true;
            $json['mensaje'] = "Datos actualizados con exito";        
        }        
        echo json_encode($json);
    }
    
    public function guardar_trans($id_cuen){
        $cuentaorigen = $id_cuen;
        $cuentadestino = $this->input->post('cuenta_destino');
        $montotrans = $this->input->post('montotrans');
        $ori_moneda = $this->input->post('ori_moneda');
        $dest_moneda = $this->input->post('dest_moneda');
        $descripcion = $this->input->post('descripcion');
        $usuario = $this->session->userdata('authorized');

        if($montotrans != '' && $montotrans > 0){
            $this->db->trans_start();
            
            $this->db->query("CALL encaja('2','11','{$ori_moneda}','{$montotrans}','{$cuentaorigen}','TRANSFERENCIA ENTRE CUENTAS(ORIGEN) {$descripcion}','{$usuario}')");
            $this->db->query("CALL encaja('1','10','{$dest_moneda}','{$montotrans}','{$cuentadestino}','TRANSDERENCIA ENTRE CUENTAS(DESTINO) {$descripcion}','{$usuario}')");            

            $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE){
                $json['exito'] = false;
                $json['mensaje'] = "Hubo un error al guardar los datos";
            }else{
                $json['exito'] = true;
                $json['mensaje'] = "Datos actualizados con exito";
            }        
        }else{
            $json['exito'] = false;
            $json['mensaje'] = "Ingrese un monto para la tranferencia";
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
        $this->db->query("DELETE FROM cuenta WHERE cuen_id={$id}");
        die(json_encode(array('exito'=>true,'mensaje'=>'Eliminado con exito')));
    }
    public function bank_cuentas(){
        $this->load->helper('Funciones');
        $this->load->database();
        //$this->load->library('Framework');
        $this->load->library('Ssp');
        $this->load->library('Cssjs');


        $this->cssjs->set_path_js(base_url() . "assets/js/Cuenta/");
        $this->cssjs->add_js('cuen_bank');
        $script['js'] = $this->cssjs->generate_js();
        $this->load->view('header', $script);
        $this->load->view('menu');
        $this->load->view($this->router->fetch_class().'/'.$this->router->fetch_method());
        $this->load->view('footer');        
    }

    public function get_cuentas(){
        $cuentas = $this->db->get("cuenta")->result();
        $html = "";
        foreach ($cuentas as $i => $cuen) {
            $editar = '<a type="button" title="Editar Cuenta" href="'.base_url().'Cuenta/crear/{id}" class="btn btn-primary btn-sm editar">
                <span class="glyphicon glyphicon-edit" aria-hidden="true"></span>
            </a>';
            $eliminar = '<button data-id="{id}" type="button" class="btn btn-danger btn-sm eliminar">
                <span class="glyphicon glyphicon-trash" aria-hidden="true"></span>
            </button>';
            if($this->editar > 1)
                $editar = str_replace("{id}", $cuen->cuen_id, $editar);
            else
                $editar = "";
            $eliminar = str_replace("{id}", $cuen->cuen_id, $eliminar);
            $simb = ($cuen->cuen_moneda == 'SOLES')?'S/ ':'$ ';
            $html .= "<tr>";
            $html .= "<td>".$cuen->cuen_codigo."</td>";
            $html .= "<td>".$cuen->cuen_banco."</td>";
            $html .= "<td>".$cuen->cuen_titular."</td>";
            $html .= "<td>".$cuen->cuen_numero."</td>";
            $html .= "<td>".$cuen->cuen_cci."</td>";
            $html .= "<td>".$cuen->cuen_moneda."</td>";
            $html .= "<td>".$simb.number_format($cuen->cuen_monto, 2, ',', ' ')."</td>";
            $html .= "<td>".$this->Model_general->datetime_from_mysql($cuen->cuen_fechaupd)."</td>";
            $html .= "<td>".$editar."</td>";
            $html .= "</tr>";
        }
        $resp["html"] = $html;
        echo json_encode($resp);
    }

    public function test_subida(){

        $config['upload_path'] = './application';
        $config['allowed_types'] = 'xls|xlsx';
        $config['max_size'] = 1024 * 8;
        $config['file_name'] = "file";

        $this->load->library('upload', $config);
 
        if (!$this->upload->do_upload("archivo")){
            $resp["exito"] = false;
            $resp["mensaje"] = $this->upload->display_errors('', '');
            $this->Model_general->dieMsg($resp);
        }else{
            $data = $this->upload->data();
            $file_name = $data['file_name'];
            $this->load->library("Excel");
            
            $file =  APPPATH.$file_name;
            
            $inputFileType = PHPExcel_IOFactory::identify($file);
            $objReader = PHPExcel_IOFactory::createReader($inputFileType);

            $objPHPExcel = $objReader->load($file);

            

            // lista de folios del archivo excel.
            $worksheetList = $objReader->listWorksheetNames($file);

            $cantidadHojas = count($worksheetList);
            $html = "<h3>Respuesta</h3>";
            
            for ($hojaExcel = 0; $hojaExcel < $cantidadHojas-1; $hojaExcel++) {
                // Aquí le estoy pasando el valor de "$hojaExcel".
                $objPHPExcel->setActiveSheetIndex($hojaExcel);
                $rows = $objPHPExcel->getActiveSheet()->getHighestRow(); 
                if($hojaExcel == 0 || $hojaExcel == 1){    
                    if($hojaExcel == 0) $html .= "<h4>Caja Soles</h4>";
                    else $html .= "<h4>Caja Dolares</h4>";
                    for($i = 8;$i <= $rows; $i++){

                        $fecha =        $objPHPExcel->getActiveSheet()->getCell("B".$i)->getCalculatedValue();
                        $filecuen =     $objPHPExcel->getActiveSheet()->getCell("C".$i)->getCalculatedValue();
                        $codigo =       $objPHPExcel->getActiveSheet()->getCell("D".$i)->getCalculatedValue();
                        $descripcion =  $objPHPExcel->getActiveSheet()->getCell("H".$i)->getCalculatedValue();
                        $comp =         $objPHPExcel->getActiveSheet()->getCell("J".$i)->getCalculatedValue();
                        $serie =        $objPHPExcel->getActiveSheet()->getCell("K".$i)->getCalculatedValue();
                        //$comp_num =     $objPHPExcel->getActiveSheet()->getCell("L".$i)->getCalculatedValue();
                        $monto_s =      $objPHPExcel->getActiveSheet()->getCell("M".$i)->getCalculatedValue();
                        $monto_i =      $objPHPExcel->getActiveSheet()->getCell("L".$i)->getCalculatedValue();

                        $timestamp = PHPExcel_Shared_Date::ExcelToPHP($fecha);
                        $fecha_php = date("Y-m-d",$timestamp);
                        $fecha_php = strtotime ( '+1 day' , strtotime ( $fecha_php ) ) ;
                        $fecha_php = date ( 'Y-m-d ' , $fecha_php );
                        if($codigo != ""):
                        $filecuen = explode("-", $filecuen);
                        $codigo = explode("-", $codigo);
                        
                        
                        if($hojaExcel == 0){
                            $moneda = "SOLES";
                            $cuenta = 1;
                        }else{
                            $moneda = "DOLARES";
                            $cuenta = 2;
                        }
                        if($monto_s != ''){
                            $monto = floatval($monto_s);
                            $accion = "SALIDA";
                        }else{
                            $monto = floatval($monto_i);
                            $accion = "INGRESO";
                        }
                        
                        $monto = abs($monto);
			            $comprobante = 0;

                        if($codigo[0] != ""){
                            
                            if($codigo[0] == "TL" || $codigo[0] == "TR" || $codigo[0] == "P"){
                                $tipo_id = 4;
                                $html .=  $this->guardarCobroPaquete($codigo[0], $codigo[1],$monto,$tipo_id,$accion,$comprobante,$comp, $serie,$descripcion, $cuenta, $filecuen[1],$fecha_php,$moneda);  
                            }else if ($codigo[0] == "LIQ") {
                                $tipo_id = 1;
                                $html .=  $this->guardarCobroLiquidacion($codigo[0], $codigo[1],$monto,$tipo_id,$accion,$comprobante,$comp, $serie,$descripcion, $cuenta, $filecuen[1],$fecha_php,$moneda);
                            }else if ($codigo[0] == "OP") {
                                $tipo_id = 6;
                                $html .=  $this->guardarPagoOrden($codigo[0], $codigo[1],$monto,$tipo_id,$accion,$comprobante,$comp, $serie,$descripcion, $cuenta, $filecuen[1],$fecha_php,$moneda);
                            }else{
                                $tipo_id = 3;
                                $html .=  $this->guardarCobroComp($codigo[0], $codigo[1],$monto,$tipo_id,$accion,$comprobante,$comp, $serie,$descripcion, $cuenta, $filecuen[1],$fecha_php,$moneda);
                            }
                        }
                        endif;
                    }
                }else{
                    $cuenta_cod = $objPHPExcel->getActiveSheet()->getCell("E5")->getCalculatedValue();
                    $cuenta_cod = explode("-", $cuenta_cod);
                    $cuenta = $this->db->where("cuen_codigo",$cuenta_cod[0])->get("cuenta");
                    if($cuenta->num_rows() > 0){
                        $cuenta = $cuenta->row();
                        $html .= "<h4>".$objPHPExcel->getActiveSheet()->getCell("D5")->getCalculatedValue()."</h4>";
                        $registros = false;
                        for($i = 12;$i <= $rows; $i++){
                            $filecuen =     preg_replace('[\s+]','', $objPHPExcel->getActiveSheet()->getCell("A".$i)->getCalculatedValue());
                            $fecha =        $objPHPExcel->getActiveSheet()->getCell("B".$i)->getCalculatedValue();
                            $debe =         $objPHPExcel->getActiveSheet()->getCell("E".$i)->getCalculatedValue();
                            $haber =        $objPHPExcel->getActiveSheet()->getCell("F".$i)->getCalculatedValue();
                            $codigo =       $objPHPExcel->getActiveSheet()->getCell("G".$i)->getCalculatedValue();
                            $descripcion =  $objPHPExcel->getActiveSheet()->getCell("H".$i)->getCalculatedValue();
                            

                            $ccd = $filecuen;
                            $timestamp = PHPExcel_Shared_Date::ExcelToPHP($fecha);
                            $fecha_php = date("Y-m-d",$timestamp);
                            $fecha_php = strtotime ( '+1 day' , strtotime ( $fecha_php ) ) ;
                            $fecha_php = date ( 'Y-m-d ' , $fecha_php );

                            $filecuen = explode("-", $filecuen);

                            $debe = abs(floatval($debe));
                            $haber = abs(floatval($haber));

                            if($codigo != ''){

                                $codigo = explode("-", $codigo);

                                if($debe > 0 || $haber > 0){
                                    if($debe > $haber){
                                        $monto = $debe;
                                        $accion = "INGRESO";
                                    }else{
                                        $monto = $haber;
                                        $accion = "SALIDA";
                                    }
                                    $comprobante = 0;
                                    if($codigo[0] != ""){
                                        $registros = true;
                                        if($codigo[0] == "TL" || $codigo[0] == "TR" || $codigo[0] == "P"){
                                            $tipo_id = 4;
                                            $html .=  $this->guardarCobroPaquete($codigo[0], $codigo[1],$monto,$tipo_id,$accion,$comprobante,'', '',$descripcion, $cuenta->cuen_id, $filecuen[1],$fecha_php,$cuenta->cuen_moneda);  
                                        }else if ($codigo[0] == "LIQ") {
                                            $tipo_id = 1;
                                            $html .=  $this->guardarCobroLiquidacion($codigo[0], $codigo[1],$monto,$tipo_id,$accion,$comprobante,'', '',$descripcion, $cuenta->cuen_id, $filecuen[1],$fecha_php,$cuenta->cuen_moneda);
                                        }else{
                                            $tipo_id = 3;
                                            $html .=  $this->guardarCobroComp($codigo[0], $codigo[1],$monto,$tipo_id,$accion,$comprobante,'', '',$descripcion, $cuenta->cuen_id, $filecuen[1],$fecha_php,$cuenta->cuen_moneda);
                                        }
                                    }
                                }else{
                                    $html .= "<p class='red'>".$ccd." <strong>no se encontraron datos para registrar</strong></p>";
                                }
                            }
                        }
                        if(!$registros){
                            $html .= "<p class='red'><strong>no se encontraron registros para subir</strong></p>";
                        }
                    }else{
                        $html .= "<p class='red'>".$cuenta_cod." <strong>no se encontro la cuenta</strong></p>";
                    }
                }
            }
            
            unset($objPHPExcel);
            unlink($file);
            $resp["exito"] = true;
            $resp["html"] = $html;
        }
        echo json_encode($resp);
    }

/*
actualizarCaja(5,        $accion, ''    , ''   , ''     , $rsocial." ".$denominacion, $monto  , $cuenta->cuen_moneda, $this->usua_id, ''  , ''      , $cuenta->cuen_id, $filecuen[1],$fecha_php);

actualizarCaja($tipo_id, $tipo, $tcom_id, $serie, $numero, $descripcion              , $monto=0, $moneda            , $usuario      , $ref, $refdeta, $cuenta         , $file=''    ,$fecha=''  ,$obs=''){
    */
    public function guardarCobroLiquidacion($pref, $numero,$pagado = 0,$tipo_id,$accion,$comprobante,$comp , $comp_num, $descripcion, $cuenta, $filecuen,$fecha_php,$moneda){

        $this->db->where(array("liqu_numero" => $numero));
        $liq = $this->db->get("liquidacion");
        
        if($liq->num_rows() > 0){
            $liq = $liq->row();
            $liquidacion = $this->Model_general->getLiquTotal($liq->liqu_id);
            if($liquidacion->pagado == 'PENDIENTE'){
                $saldo = $liquidacion->total - $liquidacion->cancelado;
                /*
                if($pagado > $saldo)
                    $pagado = $saldo;
                */
                if($moneda == $liquidacion->moneda){
                    $this->db->trans_start();
                    $this->Model_general->actualizarCaja($tipo_id, $accion, $comprobante, $comp, $comp_num, $descripcion, $pagado, $moneda, $this->usua_id, $liquidacion->id, '', $cuenta, $filecuen,$fecha_php);
                    if(($liquidacion->cancelado + $pagado) >= $liquidacion->total){
                        $dte = array("liqu_estado" => 'PAGADO', "liqu_cobrado" => ($liquidacion->cancelado + $pagado));
                    }else{
                        $dte = array("liqu_cobrado" => ($liquidacion->cancelado + $pagado));
                    }
                    $this->Model_general->guardar_edit_registro("liquidacion", $dte, array('liqu_id' => $liquidacion->id));

                    $this->db->trans_complete();
                    if ($this->db->trans_status() === FALSE){
                        return "<p class='red'>".$pref."-".$liquidacion->numero.": <strong>No se pudo registrar</strong></p>";
                    }else{
                        return "<p class='green'>".$pref."-".$liquidacion->numero.": <strong>Registrado con exito</strong></p>";
                    }
                }else{
                    return "<p class='red'>".$pref."-".$liquidacion->numero.": <strong>Las monedas no coninciden</strong></p>";
                }
            }else{
                return "<p class='red'>".$pref."-".$liquidacion->numero.": <strong> No fue registrado (el file ya esta cancelado)</strong></p>";
            }
        }else{
            return "<p class='red'>".$pref."-".$numero.": <strong> No fue encontrado</strong></p>";
        }
    }
    public function guardarCobroPaquete($pref, $numero,$pagado = 0,$tipo_id,$accion,$comprobante,$comp , $comp_num, $descripcion, $cuenta, $filecuen,$fecha_php,$moneda){

        $this->db->where(array("paqu_prefijo" => $pref, "paqu_numero" => $numero));
        $paq = $this->db->get("paquete");
        
        if($paq->num_rows() > 0){
            $paq = $paq->row();
            $paquete = $this->Model_general->getPaqTotal($paq->paqu_id);
            
            if($paquete->cobrado == 0){
                $saldo = $paquete->total - $paquete->cancelado;
                /*
                if($pagado > $saldo)
                    $pagado = $saldo;
                */
                if($moneda == $paquete->moneda){
                    $this->db->trans_start();
                    
                    $this->Model_general->actualizarCaja($tipo_id, $accion, $comprobante, $comp, $comp_num, $descripcion, $pagado, $paquete->moneda, $this->usua_id, $paquete->id, '', $cuenta, $filecuen,$fecha_php);
                    
                    if(($paquete->cancelado + $pagado) >= $paquete->total){
                        $dte = array("paqu_escobrado" => '1', "paqu_cobrado" => ($paquete->cancelado + $pagado));
                    }else{
                        $dte = array("paqu_cobrado" => ($paquete->cancelado + $pagado));
                    }
                    $this->Model_general->guardar_edit_registro("paquete", $dte, array('paqu_id' => $paq->paqu_id));

                    $this->db->trans_complete();
                    if ($this->db->trans_status() === FALSE){
                        return "<p class='red'>".$paquete->prefijo."-".$paquete->numero.": <strong>No se pudo registrar</strong></p>";
                    }else{
                        return "<p class='green'>".$paquete->prefijo."-".$paquete->numero.": <strong>Registrado con exito</strong></p>";
                    }
                }else{
                    return "<p class='red'>".$paquete->prefijo."-".$paquete->numero.": <strong>Las monedas no coninciden</strong></p>";
                }
            }else{
                return "<p class='red'>".$paquete->prefijo."-".$paquete->numero.": <strong>No fue registrado (el file ya esta cancelado)</strong></p>";
            }
        }else{
            return "<p class='red'>".$pref."-".$numero.": <strong>No fue encontrado</strong></p>";
        }
    }
    public function guardarPagoOrden($pref, $numero,$pagado = 0,$tipo_id,$accion,$comprobante,$comp , $comp_num, $descripcion, $cuenta, $filecuen,$fecha_php,$moneda){

        $this->db->where(array("orde_numero" => $numero));
        $orde = $this->db->get("ordenpago");
        
        if($orde->num_rows() > 0){
            $orde = $orde->row();
            $orden = $this->Model_general->getOrdenPagoTotal($orde->orde_id);
            if($orden->pagado == '0'){
                $saldo = $orden->total - $orden->cancelado;
                if($pagado > $saldo)
                    $pagado = $saldo;
                if($moneda == $orden->moneda){
                    $this->db->trans_start();

                    $this->Model_general->actualizarCaja($tipo_id, $accion, $comprobante, $comp, $comp_num, $descripcion, $pagado, $moneda, $this->usua_id, $orden->id, '', $cuenta, $filecuen,$fecha_php);
                    if(($orden->cancelado + $pagado) >= $orden->total){
                        $dte = array("orde_espagado" => '1');
                        $this->Model_general->guardar_edit_registro("ordenpago", $dte, array('orde_id' => $orden->id));

                        $this->Model_general->guardar_edit_registro("ordenpago_detalle", array("deta_espagado" => '1'), array('deta_orde_id' => $orden->id));

                        $detas = $this->db->where("deta_orde_id", $orden->id)->get("ordenpago_detalle")->result();
                        foreach ($detas as $i => $det) {
                            $this->Model_general->guardar_edit_registro("servicio_proveedor", array("sepr_estado" => '1'), array('sepr_id' => $det->deta_sepr_id));
                        }
                    }

                    $this->db->trans_complete();
                    if ($this->db->trans_status() === FALSE){
                        return "<p class='red'>".$pref."-".$orden->numero.": <strong>No se pudo registrar</strong></p>";
                    }else{
                        return "<p class='green'>".$pref."-".$orden->numero.": <strong>Registrado con exito</strong></p>";
                    }
                }else{
                    return "<p class='red'>".$pref."-".$orden->numero.": <strong>Las monedas no coninciden</strong></p>";
                }
            }else{
                return "<p class='red'>".$pref."-".$orden->numero.": <strong> No fue registrado (el file ya esta cancelado)</strong></p>";
            }
        }else{
            return "<p class='red'>".$pref."-".$numero.": <strong> No fue encontrado</strong></p>";
        }
    }
    public function guardarCobroComp($pref, $numero,$pagado = 0,$tipo_id,$accion,$cmbt,$comp , $comp_num, $descripcion, $cuenta, $filecuen,$fecha_php,$moneda){
        /*
        echo "esto es comprobante : ".$comprobante;
        exit(0);
        */
        $this->db->where(array("vent_serie" => $pref, "vent_numero" => $numero));
        $vent = $this->db->get("venta");
        
        if($vent->num_rows() > 0){
            $vent = $vent->row();
            $comprobante = $this->Model_general->getCompTotal($vent->vent_id);
            if($comprobante->cobrado == '0'){
                $saldo = $comprobante->total - $comprobante->cancelado;
                /*
                if($pagado > $saldo)
                    $pagado = $saldo;
                */
                if($moneda == $comprobante->moneda){
                    $this->db->trans_start();

                    $this->Model_general->actualizarCaja($tipo_id, $accion, $cmbt, $comp, $comp_num, $descripcion, $pagado, $moneda, $this->usua_id, $comprobante->id, '', $cuenta, $filecuen,$fecha_php);
                    if(($comprobante->cancelado + $pagado) >= $comprobante->total){
                        $dte = array("vent_escobrado" => '1', "vent_cobrado" => ($comprobante->cancelado + $pagado));
                    }else{
                        $dte = array("vent_cobrado" => ($comprobante->cancelado + $pagado));
                    }
                    $this->Model_general->guardar_edit_registro("venta", $dte, array('vent_id' => $comprobante->id));

                    $this->db->trans_complete();
                    if ($this->db->trans_status() === FALSE){
                        return "<p class='red'>".$pref."-".$comprobante->numero.": <strong>No se pudo registrar</strong></p>";
                    }else{
                        return "<p class='green'>".$pref."-".$comprobante->numero.": <strong>Registrado con exito</strong></p>";
                    }
                }else{
                    return "<p class='red'>".$pref."-".$comprobante->numero.": <strong>Las monedas no coninciden</strong></p>";
                }
            }else{
                return "<p class='red'>".$pref."-".$comprobante->numero.": <strong> No fue registrado (el file ya esta cancelado)</strong></p>";
            }
        }else{
            return "<p class='red'>".$pref."-".$numero.": <strong> No fue encontrado</strong></p>";
        }
    }
	public function reporte_excelMovimientos(){
		
        $search = $this->input->post("search")["value"];
        $tipo = $this->input->post("tipo");
        $desde = $this->input->post("desde");
        $hasta = $this->input->post("hasta");
		$cuenta = $this->input->post("cuenta");

		
        $this->db->select("DATE_FORMAT(movi_fechareg,'%d/%m/%Ys') AS fecha, CONCAT(cuen_codigo,'-',movi_file) file, movi_tipo tipo, movi_moneda moneda, movi_ingreso ingreso, movi_egreso egreso, movi_descripcion desc, movi_obs obs, cuen_banco cuenta");
        $this->db->from("cuenta_movimiento");
		$this->db->join("cuenta","cuen_id = movi_cuen_id");
        if ($desde != "" && $hasta != ""){
                $this->db->where("movi_fechareg >=",$desde." 00:00:00");
                $this->db->where("movi_fechareg <=",$hasta." 23:59:00");
            }
        if ($tipo != "")
            $this->db->where("movi_tipo",$tipo);
        if ($cuenta != "") 
            $this->db->where("movi_cuen_id",$cuenta);
        $this->db->order_by("movi_id","DESC");
        $detalle = $this->db->get()->result();
		
        /*
        $this->db->where("D.deta_fechaserv BETWEEN '$desde' AND '$hasta' ".($estado != ''?"AND P.paqu_estado = '$estado'":"")." ".($search != ""? " AND (C.clie_rsocial LIKE '%$search%' OR D.deta_guia LIKE '%$search%' OR S.serv_descripcion LIKE '%$search%')":""));
        */
        
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
        /*
        $objPHPExcel->getActiveSheet()->getStyle('D')->applyFromArray($fillgray);
        $objPHPExcel->getActiveSheet()->getStyle('H')->applyFromArray($fillgray);
		*/
        $objPHPExcel->getActiveSheet()
                ->setCellValue('A1', 'FECHA')
                ->setCellValue('B1', 'FILE')
				->setCellValue('C1', 'CUENTA')
                ->setCellValue('D1', 'TIPO')
                ->setCellValue('E1', 'MONEDA')
                ->setCellValue('F1', 'INGRESO')
                ->setCellValue('G1', 'EGRESO')
                ->setCellValue('H1', 'DESCRIPCION')
                ->setCellValue('I1', 'OBSERVACION');
        
        $objPHPExcel->getActiveSheet()->getStyle('B:D')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
		$objPHPExcel->getActiveSheet()->getStyle('G:H')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
        $objPHPExcel->getActiveSheet()->freezePane('A2');
        
        $ini = 3;
        $index = 0;
        foreach($detalle as $fila){
            $nro = $index+$ini;
            $index++;
            $objPHPExcel->getActiveSheet()
                        ->setCellValue("A$nro", $fila->fecha)
                        ->setCellValue("B$nro", $fila->file)
						->setCellValue("C$nro", $fila->cuenta)
						->setCellValue("D$nro", $fila->tipo)
                        ->setCellValue("E$nro", $fila->moneda)
                        ->setCellValue("F$nro", $fila->ingreso)
                        ->setCellValue("G$nro", $fila->egreso)
                        ->setCellValue("H$nro", $fila->desc)
                        ->setCellValue("I$nro", $fila->obs);
            }

            foreach(range('A','H') as $nro)
                $objPHPExcel->getActiveSheet()->getColumnDimension($nro)->setAutoSize(true);
        
        $fin = $index+$ini-1;
        $objPHPExcel->getActiveSheet()->getStyle("F$ini:G$fin")->getNumberFormat()->setFormatCode('#,##0.00'); 
        $objPHPExcel->getActiveSheet()->getStyle("A$ini:A$fin")->getNumberFormat()->setFormatCode('dd/mm/yyyy');
        
        
        $excel->excel_output($objPHPExcel, 'MOVIMIENTO EN CUENTA '.$desde." - ".$hasta);
	}
}

