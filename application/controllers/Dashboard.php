<?php 
class Dashboard extends CI_Controller
{
    var $user = array();
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
        $this->load->helper('Form');
    }
    public function index() {
        header('location: '.base_url().'Registro/paq_listado');
	$this->load->helper('Funciones');
        $this->load->database();
        //$this->load->library('Framework');
        $this->load->library('Ssp');
        $datos["htbl1"] = array("#","Nombre Contacto","Debe","Haber","...");
        
        $this->cssjs->set_path_js(base_url() . "assets/js/Dashboard/");
        $this->cssjs->add_js('panel');
        $script['js'] = $this->cssjs->generate_js();
        $this->load->view('header', $script);
        $this->load->view('menu');
        $this->load->view($this->router->fetch_class().'/panel', $datos);
        $this->load->view('footer'); 
    }
    public function get_dataCobros(){
        $moneda = $this->input->post("moneda");
        $clientes = $this->Model_general->getData("cliente", array("clie_id","clie_rsocial"), array("clie_activo" => 1));
        $html = "";
        foreach ($clientes as $k => $cl) {
            
            $this->db->select("SUM(PD.paqu_total) as deudas, COUNT(*) as reservas, (SELECT SUM(PP.paqu_total) FROM paquete as PP WHERE PP.paqu_escobrado = '1' AND PP.paqu_clie_id = '{$cl->clie_id}') as pagado");
            $this->db->where(array("PD.paqu_escobrado" => "0", "PD.paqu_clie_id" => $cl->clie_id, "paqu_moneda" => $moneda));
            $this->db->group_by("PD.paqu_clie_id");
            $deudas = $this->db->get("paquete as PD");

            if($deudas->num_rows() > 0){
                if($deudas->num_rows()){
                    $deudas = $deudas->row();
                    $cl->deudas = $deudas->deudas;
                    $cl->cant_deudas = $deudas->reservas;
                    $cl->pagos = $deudas->pagado;
                }
            }else{
                $cl->deudas = 0.00;
                $cl->cant_deudas = 0;
                $cl->pagos = 0;
            }
            $html .= "<tr>";
            $html .= "<td>".($k+1)."</td>";
            $html .= "<td>".$cl->clie_rsocial."</td>";
            $html .= "<td>".number_format($cl->deudas, 2, '.', ' ')."</td>";
            $html .= "<td>".number_format($cl->pagos, 2, '.', ' ')."</td>";
            $html .= "<td>".number_format($cl->deudas - $cl->pagos, 2, '.', ' ')."</td>";
            $html .= "</tr>";
        }
        $resp["html"] = $html;
        $resp["ini"] = ($moneda == "SOLES")?"S":"D";
        echo json_encode($resp);
    }
}
 ?>