<?php 
/**
* 
*/
class Consultas extends CI_Controller
{
    var $configuracion;
	function __construct() {
        parent::__construct();
        $this->load->database();
        $this->configuracion = $this->db->query("SELECT * FROM configuracion")->row();
        $this->permisos  = $this->Model_general->getPermisos($this->usua_id);
        $this->load->model("Model_general");
    }

    public function index(){
        $this->load->view('header');
        $this->load->view('Consultas/consulta');
        $this->load->view('footer');
    }
	public function consultar(){
		$this->load->helper('Funciones');
		$comprobante = $this->input->get('comprobante');
		$serie = $this->input->get('serie');
		$numero = $this->input->get('numero');
		$fecha = dateToMysql($this->input->get('fecha'));
		$total = $this->input->get('total');
		$venta = $this->db->query("SELECT * from venta where vent_total='{$total}' AND vent_serie='{$serie}' AND vent_numero='$numero' AND vent_fecha='{$fecha}'");	
	        if($venta->num_rows() > 0 ){
	        	$venta = $venta->row();
	     			//echo "SELECT * from venta where vent_total='{$total}' AND vent_serie='{$serie}' AND vent_numero='$numero' AND vent_fecha='{$fecha}'";
			$cadena = md5($this->configuracion->conf_ruc.$venta->vent_id."MCFACT");
       			header("location:".base_url()."Consultas/visor/{$venta->vent_id}/{$cadena}");
	        }else{
	        	header("location:".base_url()."Consultas");
	        }

	}
	
	public function visor($id,$cadena){
		if(md5($this->configuracion->conf_ruc.$id."MCFACT")!=$cadena)die("error");
		//$venta = $this->db->query("SELECT * from venta where vent_id='{$id}'")->row();
		$datos['id'] = $id;
		$datos['cadena'] = $cadena;
		
		$this->load->view('header');
        $this->load->view('Consultas/visor',$datos);
        $this->load->view('footer');
	}
	
	public function pdf($id,$cadena){
		if(md5($this->configuracion->conf_ruc.$id."MCFACT")!=$cadena)die("error");
		$venta = $this->db->query("SELECT * FROM venta WHERE vent_id='{$id}'")->row();
        $file = "files/REPO/{$venta->vent_file}.pdf";
        if(!file_exists($file)) die("No se ha encontrado el arhivo digital.");
        header('Content-type: application/pdf');
		header('Content-Disposition: inline; filename="'.$venta->vent_file.'.pdf"');
        readfile($file);
	}
	
	public function getpdf($id,$cadena){
		if(md5($this->configuracion->conf_ruc.$id."MCFACT")!=$cadena)die("error");
		$venta = $this->db->query("SELECT * FROM venta WHERE vent_id='{$id}'")->row();
        $file = "files/REPO/{$venta->vent_file}.pdf";
        if(!file_exists($file)) die("No se ha encontrado el arhivo digital.");
        header('Content-Type: application/octet-stream');
        header("Content-Transfer-Encoding: Binary"); 
        header("Content-disposition: attachment; filename=\"" . basename($file) . "\""); 
        readfile($file); // do the double-download-dance (dirty but worky)
	}
	
	function xml($id,$cadena){
		if(md5($this->configuracion->conf_ruc.$id."MCFACT")!=$cadena)die("error");
        $venta = $this->db->query("SELECT * FROM venta WHERE vent_id='{$id}'")->row();
        $file = "files/FIRMA/{$venta->vent_file}.xml";
		//$file = "files/FIRMA/20447981581-01-F001-00001446.xml";
		//echo $file;
        if(!file_exists($file)) die("No se ha encontrado el arhivo digital.");
        header('Content-Type: application/octet-stream');
        header("Content-Transfer-Encoding: Binary"); 
        header("Content-disposition: attachment; filename=\"" . basename($file) . "\""); 
        readfile($file); // do the double-download-dance (dirty but worky)
    }
    function cdr($id,$cadena){
		if(md5($this->configuracion->conf_ruc.$id."MCFACT")!=$cadena)die("error");
        $venta = $this->db->query("SELECT * FROM venta WHERE vent_id='{$id}'")->row();
        $file = "files/RPTA/R{$venta->vent_file}.zip";

        if(!file_exists($file)){
          die("No se ha encontrado el arhivo digital.".$file);  
        } 
        header('Content-Type: application/octet-stream');
        header("Content-Transfer-Encoding: Binary"); 
        header("Content-disposition: attachment; filename=\"" . basename($file) . "\""); 
        readfile($file); // do the double-download-dance (dirty but worky)
    }
	
	function comprobar(){
		$ruc = $this->input->post('ruc');
		$password = $this->input->post('password');
		
		if(empty($ruc)||empty($password)){
			header("location:".base_url()."Consultas");
			exit(0);
		}
		
		$cliente = $this->db->query("SELECT * FROM cliente WHERE clie_docnum='{$ruc}' AND clie_password='{$password}'")->row();
		if(!isset($cliente->clie_rsocial)){
			header("location:".base_url()."Consultas");
			exit(0);
		}
		
		$_SESSION['user'] = $cliente->clie_docnum;
		$_SESSION['name'] = $cliente->clie_rsocial;
		redirect(base_url().'Consultas/listado');
		
	}
	
	public function listado() {
		$clieruc = isset($_SESSION['user'])?$_SESSION['user']:'';
        if(empty($clieruc)){
			redirect(base_url().'Consultas');
			exit(0);
		}
        $this->load->helper('Funciones');
        $this->load->database();
        $this->load->library('Ssp');
        $this->load->library('Cssjs');

        $json = isset($_GET['json']) ? $_GET['json'] : false;

        $columns = array(
            array('db' => 'vent_id',    						'dt' => 'ID',          		"field" => "vent_id"),
            array('db' => 'comp_nombre',   						'dt' => 'Comprobante',     	"field" => "comp_nombre"),
            array('db' => 'vent_fecha',   						'dt' => 'Fecha',     		"field" => "vent_fecha"),
			array('db' => "CONCAT(vent_serie,'-',vent_numero)", 'dt' => 'Número',      		"field" => "CONCAT(vent_serie,'-',vent_numero)"),
            array('db' => 'vent_moneda',						'dt' => 'Moneda',       	"field" => "vent_moneda"),
            array('db' => 'vent_total',          				'dt' => 'Total',       		"field" => "vent_total"),
            array('db' => 'vent_id',             				'dt' => 'DT_RowId',    		"field" => "vent_id"),
            array('db' => 'vent_fact_situ',             		'dt' => 'DT_Estado',    	"field" => "vent_fact_situ"),
            array('db' => "MD5(CONCAT(".$this->configuracion->conf_ruc.",vent_id,'MCFACT'))",'dt' => 'DT_Resumen',		"field" => "MD5(CONCAT(".$this->configuracion->conf_ruc.",vent_id,'MCFACT'))")
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
            $joinQuery = "FROM venta JOIN maestra_comprobantes ON comp_id=vent_comp_id LEFT JOIN factura_situacion ON situ_id=vent_fact_situ";
            $where = "";
			
			$condiciones[] = "vent_clie_num_documento='".$clieruc."' AND vent_fact_situ in (3,4)";
			
            if (!empty($_POST['desde'])&&!empty($_POST['hasta'])){
                $condiciones[] = "vent_fecha >='".$_POST['desde']."' AND vent_fecha <='".$_POST['hasta']."'";
            }
            if (!empty($_POST['comprobantes']))
                $condiciones[] = "vent_comp_id='".$_POST['comprobantes']."'";
            if (!empty($_POST['moneda']))
                $condiciones[] = "vent_moneda='".$_POST['moneda']."'";
            if (!empty($_POST['estado']))
                $condiciones[] = "vent_fact_situ='".$_POST['estado']."'";

            $where = count($condiciones) > 0 ? implode(' AND ', $condiciones) : "";
            echo json_encode(
                    $this->ssp->simple($_POST, $sql_details, $table, $primaryKey, $columns, $joinQuery, $where)
            );
            exit(0);
        }

		$datos['columns'] = $columns;
		$datos['rsocial'] = $_SESSION['name'];

        $this->cssjs->set_path_js(base_url() . "assets/js/Consultas/");
        $this->cssjs->add_js('listado');
        $script['js'] = $this->cssjs->generate_js();
        $this->load->view('header', $script);
        $this->load->view('Consultas/listado', $datos);
        $this->load->view('footer');
    }

}