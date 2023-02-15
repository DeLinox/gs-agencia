<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Usuario extends CI_Controller {
	var $configuracion;
	function __construct() {
		parent::__construct();
        if(!$this->session->userdata('authorized')){
            redirect(base_url()."login");
        }
        $this->usua_id = $this->session->userdata('authorized');
        $this->configuracion = $this->db->query("SELECT * FROM usuario WHERE usua_id='{$this->usua_id}'")->row();
        $this->permisos  = $this->Model_general->getPermisos($this->usua_id);
	}

	function inicio() {
		if(!$this->session->userdata('authorizedadmin'))
			redirect(base_url());
		$this->load->helper('Funciones');
		$this->load->library('Ssp');
		$this->load->database();
		// var_dump($this->db->hostname);
		$this->cssjs->set_path_js(base_url()."assets/js/");
		$this->cssjs->add_js('Usuario/dt');

		$json = isset($_GET['json']) ? $_GET['json'] : false;
		$excel = isset($_GET['excel']) ? $_GET['excel'] : false;

		$columns = array(
			//array('db' => 'usua_id', 'dt' => 'Id',"field"=>'usua_id'),
			array('db' => 'grup_nombre', 'dt' => 'Tipo de usuario',"field"=>'grup_nombre'),
			array('db' => 'sucu_nombre', 'dt' => 'Sucursal',"field"=>'sucu_nombre'),
			array('db' => 'usua_nombres', 'dt' => 'Nombres',"field"=>'usua_nombres'),
			array('db' => 'usua_apellidos', 'dt' => 'Apellidos',"field"=>'usua_apellidos'),
			array('db' => 'usua_habilitado', 'dt' => 'Activo',"field"=>'usua_habilitado','formatter'=> function ($d, $row) {
																	if($d==1)
                                                                     return "SI";
                                                                     return "NO";
																	
                                                                 }),

			array('db' => 'usua_id', 'dt' => 'DT_RowId',"field"=>'usua_id')
		);


		if ($json||$excel) {

			$json = isset($_GET['json']) ? $_GET['json'] : false;
			$excel = isset($_GET['excel']) ? $_GET['excel'] : false;

			$table = 'usuario';
			$primaryKey = 'usua_id';

			$sql_details = array(
					'user' => $this->db->username,
					'pass' => $this->db->password,
					'db' => $this->db->database,
					'host' => $this->db->hostname
				);

			$condiciones = array();
			$joinQuery = "FROM usuario LEFT JOIN usuagrupo ON (usua_tipo_id=grup_id) JOIN sucursal_usuario ON (suus_usua_id=usua_id) LEFT JOIN sucursal ON (sucu_id=suus_suco_id)";

			if (!empty($_POST['tipo']))
				$condiciones[] = "usua_tipo='".$_POST['tipo']."'";
			$where = count($condiciones) > 0 ? implode(' AND ', $condiciones) : "";
			$data=$this->ssp->simple($_POST, $sql_details, $table, $primaryKey, $columns,$joinQuery,$where);			
			if($excel){
				echo $this->exportarXLS("Usuarios", '', '', $data['data']);
			}else{
				echo json_encode($data);
			}
			exit(0);
		}

		$datos["tipo"] = $this->Model_general->enum_valores("usuario","usua_tipo");
		$script['js'] = $this->cssjs->generate_js();
		$datos['columns'] = $columns;
		$this->load->view('header',$script);
		$this->load->view('menu');
		$this->load->view('usuario/inicio',$datos);
		$this->load->view('footer');
	}
	function exportarXLS($titulo, $termino, $fecha, $json) {
		$this->load->library('Excel');
		$excel = new Excel($this);
		$objPHPExcel = $excel->excel_init();

		$objPHPExcel->getDefaultStyle()->getFont()->setName('Arial');
		$objPHPExcel->getDefaultStyle()->getFont()->setSize(10);

		$objPHPExcel->setActiveSheetIndex(0);

		$objPHPExcel->getActiveSheet()->getStyle('A1:B6')->getFont()->setBold(true);
		$row = 0;
		$objPHPExcel->getActiveSheet()->mergeCells("A1:F1");
		$objPHPExcel->getActiveSheet()->setCellValue("A1", "REPORTE : " . $titulo);
		$row = 3;
		$objPHPExcel->getActiveSheet()->mergeCells("A{$row}:D{$row}");
		$objPHPExcel->getActiveSheet()->setCellValue("A{$row}", "FECHA CONSULTA:");
		$objPHPExcel->getActiveSheet()->setCellValue("E{$row}", date('d/m/Y H:i'));
		$row++;
		if(!empty($fecha)){
			$objPHPExcel->getActiveSheet()->mergeCells("A{$row}:D{$row}");
			$objPHPExcel->getActiveSheet()->setCellValue("A{$row}", "FILTRO FECHA:");
			$objPHPExcel->getActiveSheet()->setCellValue("E{$row}", $fecha);
			$row++;
		}
		if(!empty($termino)){
			$objPHPExcel->getActiveSheet()->mergeCells("A{$row}:D{$row}");
			$objPHPExcel->getActiveSheet()->setCellValue("A{$row}", "FILTRO TERMINO:");
			$objPHPExcel->getActiveSheet()->setCellValue("E{$row}", "$termino");
			$row++;
		}
		$objPHPExcel->getActiveSheet()->mergeCells("A{$row}:D{$row}");

		PHPExcel_Cell::setValueBinder(new PHPExcel_Cell_AdvancedValueBinder());

		$row += 2;

		$end = $excel->write_table($objPHPExcel, $json, $row-1);

		$objPHPExcel->getActiveSheet()->freezePane("A{$row}");
		$objPHPExcel->getActiveSheet()->getSheetView()->setZoomScale(90);
		$objPHPExcel->getActiveSheet()->setTitle(substr($titulo, 0, 30));
		$objPHPExcel->setActiveSheetIndex(0);
		setcookie("fileDownload", 'true');
		$excel->excel_output($objPHPExcel, $titulo);

	}
	function form($id=0) {
		if(!$this->session->userdata('authorizedadmin'))
			redirect(base_url());
		$this->load->helper('Funciones');
		//$this->load->model("Model_oficina");
		$usuario["options"] = $this->Model_general->getOptions("usuagrupo",array('grup_id','grup_nombre'));
		$usuario["sucursal"] = $this->Model_general->getOptions("sucursal",array('sucu_id','sucu_nombre'));
		if($id!=0){
			$this->db->from("usuario");
			$this->db->join("sucursal_usuario", "suus_usua_id = usua_id");
			$this->db->where("usua_id",$id);
			$usuario["usuario"] = $this->db->get()->row();
			/*
			print_r($usuario["usuario"]);
			exit(0);
			*/

			$this->load->view('usuario/form',$usuario);
		}
		else{
			//$usuario["oficina"]=select_options($this->Model_oficina->getoficina(),"ofic_id","ofic_nombre","- Oficina -");
			$this->load->view('usuario/new_form',$usuario);
		}
	}
	private function varlida_usuario(){
        $this->load->library('Form_validation');
        $this->form_validation->set_rules('nombres', 'Nombres', 'required');
        $this->form_validation->set_rules('email', 'Email', 'required');
        $this->form_validation->set_rules('movil', 'movil', 'required');
        $this->form_validation->set_rules('login', 'Login', 'required');
  		if($this->input->post('active'))
  			$this->form_validation->set_rules('password', 'Password', 'required');
  		
        if ($this->form_validation->run() == FALSE){        
            $this->Model_general->dieMsg(array('exito'=>false,'mensaje'=>validation_errors()));
        }
        
    }
	function guardar(){

		$this->varlida_usuario();

		$active = isset($_POST['active'])?"password":"";

		$usuario = $this->input->post('usuario');
		$login = $this->input->post('login');
		$email = $this->input->post('email');
		$movil = $this->input->post('movil');
		$nombres = $this->input->post('nombres');
		$tipo = $this->input->post('tipo');
		$habilitado = $this->input->post('habilitado');

		$datos = array("usua_nombres" => strtoupper($nombres),
				"usua_user" => $login,
				"usua_email" => $email,
				"usua_movil" => $movil,
				"usua_habilitado" =>$habilitado,
				"usua_tipo" => $tipo
		);

		if($active!=""){
			$password = $this->input->post('password');
			$datos = array_merge($datos,array("usua_password" =>md5($password)));
		}
		$this->db->trans_start();
		if($usuario != ''){
			$this->Model_general->guardar_edit_registro("usuario",$datos,array("usua_id"=>$usuario));
			$this->Model_general->add_log("EDITAR",13,"Edici�n de usuario: ".$nombres);
		}else{
			$usuario_id = $this->Model_general->guardar_registro("usuario",$datos);
			$this->Model_general->add_log("CREAR",13,"Registro de usuario: ".$nombres);
		}
		$this->db->trans_complete();
		if ($this->db->trans_status() === FALSE){
			$json['exito'] = false;
			$json['mensaje'] = "Error al guardar los datos";
		}else{
			$json['exito'] = true;	
			$json['mensaje'] = "Guardado con exito";
		}
		echo json_encode($json);
	}
	public function asignarPermisos($usu_id='', $permisos){
        $this->db->where('usua_id',$usu_id);
        $this->db->where_not_in('mod_id',$permisos);
        $this->db->delete('modulo_usuario');
        if(isset($permisos)){
            foreach ($permisos as $i => $mod_id) {
                $data = array("usua_id" => $usu_id, "mod_id" => $mod_id);
                $consulta = $this->db->where($data)->get("modulo_usuario");
                if($consulta->num_rows() < 1)
                    $this->Model_general->guardar_registro("modulo_usuario", $data);
            }
        }
    }
	function perfil(){
		$this->load->helper('Funciones');
		
		$where = array("usua_id"=>$this->session->userdata('authorized'));
		$datos["usuario"] = $this->Model_usuario->perfil($where);

		$script['js'] = $this->cssjs->generate_js();
		$this->load->view('usuario/perfil',$datos);
	}
	function guardar_perfil(){
		$active = isset($_POST['active'])?"active":"";

		$usuario = $this->session->userdata('authorized');
		$email = $this->input->post('email');
		$movil = $this->input->post('movil');
		$datos = array(
						"usua_email" => $email,
						"usua_movil" => $movil
						);
		if($active!=""){
			// echo "marcado";
			$old_password = $this->input->post('old_password');
			$new_password = $this->input->post('new_password');
			$new2_password = $this->input->post('new2_password');
			$user = $this->Model_usuario->getUsuario(array("usua_id"=>$usuario));
			$actual_password = $user->usua_password;
			if(md5($old_password) == $actual_password){
				if(empty($new_password) || empty($new2_password) || $new_password != $new2_password ){
					$json['exito'] = false;
					$json['mensaje'] = "Error en la nueva contraseña";
					echo json_encode($json);
					exit;
				}
				$datos = array(
					"usua_email" => $email,
					"usua_movil" => $movil,
					"usua_password" => md5($new_password)
					);
			}
			else{
				$json['exito'] = false;
				$json['mensaje'] = "La contraseña anterior no coincide";
				echo json_encode($json);
				exit;
			}


		}
		if(isset($usuario)){
			if($this->Model_general->guardar_edit_registro("usuario",$datos,array("usua_id"=>$usuario))==TRUE):
				$json['exito'] = true;
			else:
				$json['exito'] = false;
				$json['mensaje'] = "Error al actualizar los datos";
			endif;
		}
		echo json_encode($json);
	}
	public function borrar($id)
	{

		if(!$this->session->userdata('authorizedadmin'))
			redirect(base_url());
		/*
		$delete = $_POST['data'];
		$table = 'usuario';
					
		foreach ($delete as $id) {
			$where = array(
				'usua_id' => $id
				);
			if($this->Model_general->borrar($where,$table)!=TRUE){
				$json['exito'] = false;
				$json['mensaje'] = "No se pudo borrar algunos usuarios por que estan siendo usados";
			}else{
				$json['exito'] = true;
				$json['mensaje'] = false;
			}
		}
		*/
		$where = array(
			'usua_id' => $id
			);
		if($this->Model_general->borrar($where,"usuario")!=TRUE){
			$json['exito'] = false;
			$json['mensaje'] = "No se pudo borrar algunos usuarios por que estan siendo usados";
		}else{
			$json['exito'] = true;
			$json['mensaje'] = false;
		}
		echo json_encode($json);
	}
}

?>
