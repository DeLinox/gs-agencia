<?php 
/**
* 
*/
class Remision extends CI_Controller
{
    var $configuracion;
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

    public function index(){
        $this->load->view('header');
        $this->load->view('menu');
        $this->load->view('footer');
    }

    public function listado() {
        $this->load->helper('Funciones');
        $this->load->database();
        $this->load->library('Ssp');
        $this->load->library('Cssjs');

        $json = isset($_GET['json']) ? $_GET['json'] : false;

        $columns = array(
            array('db' => 'remi_id',            'dt' => 'ID',           "field" => "remi_id"),
            array('db' => 'remi_clie_rsocial',  'dt' => 'Cliente',      "field" => "remi_clie_rsocial"),
            array('db' => 'remi_fecha',         'dt' => 'Fecha',        "field" => "remi_fecha"),
            array('db' => "CONCAT(remi_serie,'-',remi_numero)", 'dt' => 'Número', "field" => "CONCAT(remi_serie,'-',remi_numero)"),
            array('db' => 'grup_nombre',        'dt' => 'Situación',    "field" => "grup_nombre"),
            array('db' => 'remi_id',            'dt' => 'DT_RowId',     "field" => "remi_id"),
            array('db' => 'remi_fact_situ',     'dt' => 'DT_Estado',    "field" => "remi_fact_situ"),
            array('db' => 'remi_email_send',    'dt' => 'DT_EmailSend', "field" => "remi_email_send")
        );

        if ($json) {

            $json = isset($_GET['json']) ? $_GET['json'] : false;

            $table = 'remision';
            $primaryKey = 'remi_id';

            $sql_details = array(
                'user' => $this->db->username,
                'pass' => $this->db->password,
                'db' => $this->db->database,
                'host' => $this->db->hostname
            );

            $condiciones = array();

            $joinQuery = "FROM remision LEFT JOIN factura_situacion_grupo ON grup_id=remi_fact_gsitu";
            $where = "";
            if (!empty($_POST['desde'])&&!empty($_POST['hasta'])){
                $condiciones[] = "remi_fecha >='".$_POST['desde']."' AND remi_fecha <='".$_POST['hasta']."'";
            }
            if (!empty($_POST['estado']))
                $condiciones[] = "remi_fact_gsitu='".$_POST['estado']."'";

            $where = count($condiciones) > 0 ? implode(' AND ', $condiciones) : "";
            echo json_encode(
                    $this->ssp->simple($_POST, $sql_details, $table, $primaryKey, $columns, $joinQuery, $where)
            );
            exit(0);
        }
        $datos["estado"] = $this->Model_general->getOptions('factura_situacion_grupo', array("grup_id", "grup_nombre"),'* Estados');
        $datos['columns'] = $columns;

        $datos['titulo'] = "Guias de Remisión";
        //$datos['idcomp'] = $idcomp;

        $this->cssjs->set_path_js(base_url() . "assets/js/Remision/");
        $this->cssjs->add_js('listado');
        $script['js'] = $this->cssjs->generate_js();
        $this->load->view('header', $script);
        $this->load->view('menu');
        $this->load->view('Remision/listado', $datos);
        $this->load->view('footer');
    }
    
    public function pdf($id){
        $remision = $this->db->query("SELECT remi_file FROM remision WHERE remi_id='{$id}'")->row();
        $file = "files/REPO/{$remision->remi_file}.pdf";
        if(!file_exists($file)) die("No se ha encontrado el arhivo digital.");
        header('Content-type: application/pdf');
        header('Content-Disposition: inline; filename="'.$remision->remi_file.'.pdf"');
        readfile($file);
    }

    public function ver($id,$bool=false){

        $datos['id'] = $id;
        $datos['bool'] = $bool;
        if($bool){
            $this->load->helper('Funciones');
            $this->load->database();
            $this->load->library('Ssp');
            $this->load->library('Cssjs');
            $this->cssjs->set_path_js(base_url() . "assets/js/Venta/");
            $this->cssjs->add_js('listado');
            $datos['js'] = $this->cssjs->generate_js();
            $this->load->view('header', $datos);
        }
        $remision = $this->db->query("SELECT * FROM remision WHERE remi_id='{$id}'")->row();
        $datos['remision'] = $remision;
        $this->load->view('Venta/ver',$datos);
        if($bool)$this->load->view('footer');
    }

    public function vercomp($id){
        $datos['id'] = $id;
        $this->load->view('Remision/vercomp',$datos);

    }
    
    
    public function nextnum($serie){
        $this->db->select('MAX(remi_numero) as max');
        $this->db->from('remision');
        $this->db->where("remi_serie='{$serie}'");
        $query = $this->db->get();
        $row = $query->row();
        $numero = $row->max+1;
        return $numero;
    }
    
    public function getnext($serie){
        echo json_encode(array('numero'=>$this->nextnum($serie)));
    }
    public function serieDefault(){
        return "T001";
    }
    public function getSerie(){
        return $this->serieDefault();
    }
    
    public function crear() {
        $this->load->database();
        $this->load->helper('Funciones');
        $this->load->model("Model_general");

        $serie = $this->getSerie();
    
        $clie_docu_id = '0';

        $remi = array(
                        
                         'remi_serie' => $serie,
                         'remi_numero' => $this->nextnum($serie),
                         'remi_fecha' => date('d/m/Y'),
                         'remi_fecha_initras' => date('d/m/Y'),
                         'remi_clie_docu_id' => $clie_docu_id,
                         'remi_clie_num_documento' => '',
                         'remi_clie_rsocial' => '',
                         'remi_clie_direccion' => '',
                         'remi_clie_id' => '',
                         'remi_clie_email' => '',
                         'remi_descripcion' => '',
                         'remi_exterior' => 'NO',
                         'remi_genera_archivo' => 'NO',
                         'remi_peso_bruto' => '',
                         'remi_doc_trans' => '',
                         'remi_docnum_trans' => '',
                         'remi_denom_trans' => '',
                         'remi_placa_trans' => '',
                         'remi_doc_conduc' => '',
                         'remi_docnum_conduc' => '',
                         'remi_nombre_conduc' => '',
                         'remi_dir_ini' => '',
                         'remi_dir_fin' => '',
                        'remi_bultos' => '',
                        'remi_id_trans' => '',
                        'remi_id_conduc' => '',
                        'remi_deta_inicio' => '',
                        'remi_deta_fin' => '',
                        "remi_inicio" => '',
                        "remi_fin" => '',
                        'remi_trans_bultos' => '');

        $datos["motivo_tras"] = $this->Model_general->getOptions('motivo_traslado', array("tras_id", "tras_denom"));
        $datos["documentos"] = $this->Model_general->getOptions('maestra_documentos', array("docu_id", "docu_nombre"));
        $datos["tipo_detalle"] = $this->Model_general->getOptions('maestra_afectacion', array("afec_id", "afec_nombre"));
        
        
        $datos["remi"] = (object)$remi;
        $datos["productos"] = json_encode(array());
        $datos["id_remi"] = '';
        $datos["id"] = "";

        $datos['titulo'] = "Registrar Guia de Remisión";
        $this->load->library('Ssp');
        $this->load->library('Cssjs');
        $this->cssjs->set_path_js(base_url() . "assets/js/Remision/");
        $this->cssjs->add_js('form');
        $script['js'] = $this->cssjs->generate_js();
        $this->load->view('header', $script);
        $this->load->view('menu');
        $this->load->view('Remision/formulario', $datos);
        $this->load->view('footer');
    }
    public function edit($id=0){
        $this->load->database();
        $this->load->helper('Funciones');
        $this->load->model("Model_general");
        $this->load->library('Ssp');
        $this->load->library('Cssjs');
        $this->cssjs->set_path_js(base_url() . "assets/js/Remision/");
        $this->cssjs->add_js('form');
        $idcomp = '09';
        $datos["comprobantes"] = $idcomp;
        $datos["debito_tipo"] = $this->Model_general->getOptions('maestra_debitos', array("debi_id", "debi_nombre"));
        $datos["motivo_tras"] = $this->Model_general->getOptions('motivo_traslado', array("tras_id", "tras_denom"));
        $datos["documentos"] = $this->Model_general->getOptions('maestra_documentos', array("docu_id", "docu_nombre"));
        
        $remi = $this->Model_general->getRemisionById($id);
        $fecha = date_create($remi->remi_fecha);
        $remi->remi_fecha = date_format($fecha, 'd/m/Y');
        $fecha = date_create($remi->remi_fecha_initras);
        $remi->remi_fecha_initras = date_format($fecha, 'd/m/Y');

        $datos["remi"] = $remi;
        $datos["id_remi"] = $id;
        $datos["id"] = "";
        $productos = $this->Model_general->getProductosByRemision($id);

        $datos["productos"] = json_encode($productos);

        $datos['titulo'] = "Editar Guia de Remisión";
        $script['js'] = $this->cssjs->generate_js();
        $this->load->view('header', $script);
        $this->load->view('menu');
        $this->load->view('Remision/formulario', $datos);
        $this->load->view('footer');
    }

   
    public function buscar_ubigeo($value=''){
        $responese = new StdClass;
        $search = isset($_GET['q']) ? $_GET["q"] : '';
        $datos = array();
        $this->db->select('DE.dep_denominacion as departamento,P.prov_denominacion as provincia, DI.dist_id, DI.dist_denominacion');
        $this->db->from('distrito DI');
        $this->db->join('provincia P', 'DI.dist_id_prov = P.prov_id');
        $this->db->join('departamento DE', 'P.prov_id_dep = DE.dep_id');
        if($value != ''){
            $this->db->where('DI.dist_id', $value);
            $ubigeo = $this->db->get()->row();
            return "(".$ubigeo->dist_id.")"." ".$ubigeo->dist_denominacion." - ".$ubigeo->provincia." - ".$ubigeo->departamento;
        }else{
            $this->db->like('DI.dist_denominacion', $search);
            $ubigeo = $this->db->get()->result();
            foreach ($ubigeo as $val) {
                
                $datos[] = array("id" => $val->dist_id, "text" => "(".$val->dist_id.")"." ".$val->dist_denominacion." - ".$val->provincia." - ".$val->departamento);
            }
            $responese->total_count = count($ubigeo);
            $responese->items = $datos;
            echo json_encode($responese);
        }
        
    }

    public function buscar_transportista(){
        $responese = new StdClass;
        $search = isset($_GET['q']) ? $_GET["q"] : '';
        $datos = array();
        $this->db->select("trans_denom, trans_id, docu_nombre, trans_doc_id, trans_docnum, trans_placa");
        $this->db->from("transportista");
        $this->db->join("maestra_documentos", "docu_id = trans_doc_id");
        $this->db->like('trans_denom', $search);
        $transportista = $this->db->get()->result();
        foreach ($transportista as $val) {
            $datos[] = array("id" => $val->trans_id, "text" => $val->trans_denom, "documento" => $val->docu_nombre, "docnum" => $val->trans_docnum, "tipo" => "Transportista", "placa" => $val->trans_placa, "docu" => $val->trans_doc_id);
        }
        $responese->total_count = count($transportista);
        $responese->items = $datos;

        echo json_encode($responese);
    }

    public function buscar_conductor(){
        $responese = new StdClass;
        $search = isset($_GET['q']) ? $_GET["q"] : '';
        $datos = array();
        $this->db->select("cond_nombre, cond_id, cond_doc_id, docu_nombre, cond_docnum");
        $this->db->from("conductor");
        $this->db->join("maestra_documentos", "docu_id = cond_doc_id");
        $this->db->like('cond_nombre', $search);
        $conductor = $this->db->get()->result();
        foreach ($conductor as $val) {
            $datos[] = array("id" => $val->cond_id, "text" => $val->cond_nombre, "documento" => $val->docu_nombre, "docnum" => $val->cond_docnum, "tipo" => "Conductor", "doc" => $val->cond_doc_id);
        }
        $responese->total_count = count($conductor);
        $responese->items = $datos;

        echo json_encode($responese);
    }

    private function validarComprobante(){
        $this->load->library('Form_validation');
        $this->form_validation->set_rules('serie', 'Serie', 'required');
        $this->form_validation->set_rules('numero', 'Número', 'required');

        $this->form_validation->set_rules('rsocial', 'Razon Social', 'required');
        
        $this->form_validation->set_rules('rsocial', 'Razon Social', 'required');
        $this->form_validation->set_rules('fecha', 'Fecha', 'required');
        $this->form_validation->set_rules('docnum', 'Número de documento', 'required');
        $this->form_validation->set_rules('num_bultos', 'Número de bultos', 'required');
        $this->form_validation->set_rules('peso_bruto', 'Peso bruto', 'required');
        $this->form_validation->set_rules('cond_id', 'Conductor', 'required');
        $this->form_validation->set_rules('trans_id', 'Transportista', 'required');
        $this->form_validation->set_rules('placa_trans', 'Placa', 'required');
        $this->form_validation->set_rules('inicio', 'Unigeo de inicio', 'required');
        $this->form_validation->set_rules('fin', 'Ubigeo final', 'required');
        $this->form_validation->set_rules('dir_ini', 'Direccion inicial', 'required');
        $this->form_validation->set_rules('dir_fin', 'Direccion final', 'required');
        if($this->input->post('comprobante')=="01"){
            $this->form_validation->set_rules('docnum', 'Número de documento', 'required|exact_length[11]');
            $this->form_validation->set_rules('documento', 'Tipo de documento', 'regex_match[/6/]',array('regex_match'=>"El tipo de documento tiene que ser RUC"));
        }
         $this->form_validation->set_rules('detalle[]', 'Detalle del comprobante', 'required');

        if(in_array($this->input->post('comprobante'),array('07','08'))){
            $this->form_validation->set_rules('descripcion', 'Descripción', 'required');
        }
        
        if ($this->form_validation->run() == FALSE){        
            $this->Model_general->dieMsg(array('exito'=>false,'mensaje'=>validation_errors()));
        }
    }

    

    public function guardar($id=''){
                
        $this->load->helper('Funciones');

        $this->validarComprobante();        
        
        $serie = $this->input->post('serie');
        $numero = str_pad($this->input->post('numero'), 8, "0", STR_PAD_LEFT);
        $fecha = dateToMysql($this->input->post('fecha'));
        $documento = $this->input->post('documento');
        $docnum = $this->input->post('docnum');

        $rsocial = $this->input->post('rsocial');
        $id_cliente = $this->input->post('clie_id');
        $descripcion = $this->input->post('descripcion');
        $motivo_trans = $this->input->post('motivo_trans');
        $peso_bruto = $this->input->post('peso_bruto');
        $num_bultos = $this->input->post('num_bultos');
        $fecha_trans = dateToMysql($this->input->post('fecha_trans'));

        $trans_id = $this->input->post('trans_id');
        $docnum_trans = $this->input->post('docnum_trans');
        $doc_trans = $this->input->post('doc_trans');
        $denom_trans = $this->input->post('denom_trans');
        $placa_trans = $this->input->post('placa_trans');
        $cond_id = $this->input->post('cond_id');
        $doc_conduc = $this->input->post('doc_conduc');
        $docnum_conduc = $this->input->post('docnum_conduc');
        $nombre_conduc = $this->input->post('nombre_conduc');


        $inicio = $this->input->post('inicio');
        $dir_ini = $this->input->post('dir_ini');
        $fin = $this->input->post('fin');
        $dir_fin = $this->input->post('dir_fin');
        $fecha_tras = dateToMysql($this->input->post('fecha_tras'));
        $motivo_tras = $this->input->post('motivo_tras');
        $deta_inicio = $this->input->post('deta_inicio');
        $deta_fin = $this->input->post('deta_fin');


        $nc_comprobante = in_array($this->input->post('comprobante'),array('07','08'))?$this->input->post('nc_comprobante'):null ;
        $nc_serie = in_array($this->input->post('comprobante'),array('07','08'))?$this->input->post('nc_serie'):null ;
        $nc_numero = in_array($this->input->post('comprobante'),array('07','08'))?$this->input->post('nc_numero'):null ;
        $credito_tipo = $this->input->post('comprobante') == '07'?$this->input->post('credito_tipo'):null ;
        $debito_tipo = $this->input->post('comprobante') == '08'?$this->input->post('debito_tipo'):null ;

        $file = "{$this->configuracion->conf_ruc}-09-{$serie}-{$numero}";
        //Verifica si hay conflicto
        $exist = $this->db->query("
            SELECT date_format(remi_fecha,'%d/%m/%Y') remi_fecha,
            remi_serie,remi_numero 
            FROM remision 
            WHERE 
            remi_serie='{$serie}' 
            AND (
                    remi_numero='{$numero}' 
                    OR (remi_numero>'{$numero}' AND remi_fecha<'{$fecha}') 
                    OR (remi_numero<'{$numero}' AND remi_fecha>'{$fecha}')
                )
            ".(empty($id)?"":"AND remi_id!=$id")."
            ORDER BY remi_fecha DESC LIMIT 1")->result();
        if(count($exist)>0){
            $text = "";
            foreach($exist as $row){
                $text .= "[{$row->remi_serie}-{$row->remi_numero}-{$row->remi_fecha}] ";
            }
            $this->Model_general->dieMsg(array('exito'=>false,'mensaje'=>'Hay conflictos con documentos: '.$text));
        }    
 

        $factura = array("remi_clie_rsocial" => $rsocial,
                        "remi_fecha"=> $fecha,
                        "remi_serie"=> $serie,
                        "remi_numero" => $numero,
                        "remi_clie_docu_id"=> $documento,
                        "remi_file"=> $file,
                        "remi_clie_num_documento" => $docnum,
                        "remi_sucu_id" => 1,
                        "remi_fecha_initras" => $fecha_tras,
                        "remi_motivo_tras" => $motivo_tras,
                        "remi_peso_bruto" => $peso_bruto,
                        "remi_bultos" => $num_bultos,
                        "remi_inicio" => $inicio,
                        "remi_fin" => $fin,
                        "remi_dir_ini" => $dir_ini,
                        "remi_dir_fin" => $dir_fin,
                        "remi_id_trans" => $trans_id,
                        'remi_doc_trans' => $doc_trans,
                        'remi_docnum_trans' => $docnum_trans,
                        'remi_denom_trans' => $denom_trans,
                        'remi_placa_trans' => $placa_trans,
                        'remi_id_conduc' => $cond_id,
                        'remi_doc_conduc' => $doc_conduc,
                        'remi_docnum_conduc' => $docnum_conduc,
                        'remi_nombre_conduc' => $nombre_conduc,
                        'remi_deta_inicio' => $deta_inicio,
                        'remi_deta_fin' => $deta_fin,
                        "remi_usu_id" => $this->session->userdata('authorized'));
        $detalle = $this->input->post('detalle');
        $cantidad = $this->input->post('cantidad');
        $unidad = $this->input->post('unidad');
        $prod_id = $this->input->post('producto');
        $prod_codigo = $this->input->post('codigo');
        if(empty($id)){
            $this->db->trans_begin();

            if (($meta = $this->Model_general->guardar_registro("remision", $factura)) == TRUE):
                for ($i=0; $i < count($prod_id); $i++) { 
                    $item = array("deta_remi_id" => $meta['id'],
                                "deta_descripcion" => $detalle[$i],
                                "deta_cantidad" => $cantidad[$i],
                                "deta_unidad"=>$unidad[$i],
                                "deta_prod_id" => $prod_id[$i],
                                "deta_codigo" => $prod_codigo[$i]
                        );
                    if($this->Model_general->guardar_registro("remision_detalle", $item) == false){
                        $this->db->trans_rollback();
                        $this->Model_general->dieMsg(array('exito'=>false,'mensaje'=>'Error al guardar los datos'));
                    }
                }
            else:
                $this->db->trans_rollback();
                $this->Model_general->dieMsg(array('exito'=>false,'mensaje'=>'Error al guardar los datos'));
            endif;
            $this->db->trans_commit();
            $id = $meta['id'];           
        }else{
            $condicion = "remi_id = ".$id;
            $detalle_id = $this->input->post('deta_id');

            if (($meta = $this->Model_general->guardar_edit_registro("remision", $factura, $condicion)) == TRUE):
                // ELIMINA LOS DETALLES QUE NO EXISTEN DE LA VENTA
                $lexists = array();
                foreach($detalle_id as $i=>$det) if(!empty($det))$lexists[] = $det;
                $exists = implode(",",$lexists);
                $result = $this->db->query("SELECT deta_id FROM remision_detalle WHERE deta_remi_id='{$id}' AND deta_id not in({$exists})")->result();
                $nexists = array();
                foreach($result as $r) $nexists[] = $r->deta_id;
                if(count($nexists)>0){
                    $noexists = implode(",", $nexists);
                    $this->db->query("DELETE FROM remision_detalle WHERE deta_id in({$noexists})");
                }
                //////////////////////////////////////////////////////
                for ($i=0; $i < count($detalle); $i++) { 
                    $condicion_items = "deta_id = ".$detalle_id[$i];
                    $item = array("deta_remi_id " => $id,
                                "deta_descripcion" => $detalle[$i],
                                "deta_cantidad" => $cantidad[$i],
                                "deta_unidad"=>$unidad[$i],
                                "deta_prod_id" => $prod_id[$i],
                                "deta_codigo" => $prod_codigo[$i]
                    );
                          
                    if(empty($detalle_id[$i])){
                        $this->Model_general->guardar_registro("remision_detalle", $item);
                    }else{
                        $this->Model_general->guardar_edit_registro("remision_detalle", $item, $condicion_items);
                    }
                }
            else:
               $this->Model_general->dieMsg(array('exito'=>false,'mensaje'=>'Error al guardar los datos'));
            endif;
        }
        $this->genera_xml($id);
        $this->genera_pdf($id);
        $this->Model_general->dieMsg(array_merge(array('exito'=>true,'mensaje'=>'','id'=>$id)));
    }
    
    function enviarSunat($id){
        $this->load->helper('Funciones');
        $remision = $this->Model_general->getRemisionById($id);

        if(in_array($remision->remi_fact_gsitu, array(2,3))) die(json_encode(array('exito'=>true,'mensaje'=>'')));

        if($remision->remi_fact_gsitu==1){
            $this->enviarServidor($id);
        }

        die(json_encode(array('exito'=>true,'mensaje'=>'')));
    }

    public function genera_xml($id){
        $remision = $this->Model_general->getRemisionById($id);
        if(in_array($remision->remi_fact_gsitu,array(1,4))){
            $this->crearArchivo($id);
            $this->firmarArchivo($id);
        }
    }

    public function enviarServidor($id){
        $this->load->helper('firmar');
        $remision = $this->Model_general->getRemisionById($id);
        $file = "files/FIRMA/{$remision->remi_file}.xml";
        if(!file_exists($file)) die(json_encode(array('exito'=>false,'mensaje'=>'No hay archivo')));
        $str_xml = file_get_contents($file);
        $bin_zip = generarZip(array("{$remision->remi_file}.xml"=>$str_xml));
        file_put_contents("files/ENVIO/{$remision->remi_file}.zip",$bin_zip);
        $params = array('fileName' => "{$remision->remi_file}.zip", 'contentFile' => $bin_zip);

        $tipo = $this->configuracion->conf_sunat_tipo;
        $servidor = "https://e-beta.sunat.gob.pe/ol-ti-itemision-guia-gem-beta/billService?wsdl";
        if($tipo=='PRODUCCION')$servidor = "https://e-guiaremision.sunat.gob.pe/ol-ti-itemision-guia-gem/billService?wsdl";
        if($tipo=='HOMOLOGACION')$servidor = "https://e-beta.sunat.gob.pe/ol-ti-itemision-guia-gem-beta/billService?wsdl";
        
        $result = sendBill(
            $servidor,
            $this->configuracion->conf_ruc.$this->configuracion->conf_sunat_usuario,
            $this->configuracion->conf_sunat_password,
            $params);
        
        if($result->error==0){
            file_put_contents("files/RPTA/R{$remision->remi_file}.zip", $result->response->applicationResponse);
            $res = getResponse("files/RPTA/R{$remision->remi_file}.zip");
            $code = $res['cbc:ResponseCode'];
            $value = $res['cbc:Description'];
            $notes = $res['notes'];

            if($code==0&&count($notes)>0){ $codigo = 4; $gcodigo = 2;}
            if($code==0&&count($notes)==0){ $codigo = 3; $gcodigo = 2; }
            if($code>0){ $codigo = 10;  $gcodigo = 3; }

            $this->Model_general->guardar_edit_registro("remision",array('remi_fact_situ'=>$codigo,'remi_fact_gsitu'=>$gcodigo,'remi_fact_envi'=>date('Y-m-d H:i:s'),'remi_fact_obse'=>$value),"remi_id = '{$id}'");
   
        }else{

            if(is_numeric($result->code)){
                if($result->code>=0100&&$result->code<=1999)$codigo = 6;
                else $codigo = 5;
                $this->Model_general->guardar_edit_registro("remision",array('remi_fact_situ'=>$codigo,'remi_fact_gsitu'=>$gcodigo,'remi_fact_envi'=>date('Y-m-d H:i:s'),'remi_fact_obse'=>$result->code.":".$result->value),"remi_id = '{$id}'");
                echo json_encode(array('exito'=>false,'mensaje'=>$result->code.": ".$result->value));
                exit(0);
            }else{
                echo json_encode(array('exito'=>false,'mensaje'=>$result->code.": ".$result->value));
                exit(0);
            }
        }
    }
    public function firmarArchivo($id){
        $this->load->helper('firmar');
        $remision = $this->Model_general->getRemisionById($id);
        $file = "files/TEMP/{$remision->remi_file}.xml";

        $file_pfx = "{$this->configuracion->conf_sunat_certificado}";
        $dom = formatoXML($file);
        $str_xml = firmarPFX($dom,$file_pfx,$this->configuracion->conf_sunat_certi_password);

        $data = file_get_contents($file);
        preg_match('/<ds:DigestValue>(.+?)<\/ds:DigestValue>/',$str_xml,$arr);
        $digestvalue = $arr[1];
        $this->Model_general->guardar_edit_registro("remision",array('remi_digestvalue'=>$digestvalue),"remi_id = '{$id}'");

        file_put_contents("files/FIRMA/{$remision->remi_file}.xml", $str_xml);
        $this->Model_general->guardar_edit_registro("remision",array('remi_fact_situ'=>2,'remi_fact_gsitu'=>1,'remi_fact_gene'=>date('Y-m-d H:i:s')),"remi_id = '$id'");
    }


    public function crearArchivo($id){
        $remision = $this->Model_general->getRemisionById($id);
        $file = "files/TEMP/{$remision->remi_file}.xml";

        $tipoOperacion = ($remision->remi_exterior == 'NO')?'01':'02';
        $remisionJSON = array();
        $motivo = $this->db->query("SELECT tras_denom FROM motivo_traslado WHERE tras_id='{$remision->remi_motivo_tras}'")->row()->tras_denom;

        $datos = array(
            'ublVersionIdSwf'=>'2.1',
            'CustomizationIdSwf'=>'1.0',
            'codTraslado'=>$remision->remi_motivo_tras,
            'desTraslado'=>$motivo,
            'pesoBruto'=>$remision->remi_peso_bruto,
            'bultos'=>$remision->remi_bultos,

            'nroCdpSwf'=>"{$remision->remi_serie}-{$remision->remi_numero}",
            'fechaEmision'=>$remision->remi_fecha,//Fecha de emisión 
            'nroRucEmisorSwf'=>$this->configuracion->conf_ruc,
            'nombreComercialSwf'=>htmlspecialchars($this->configuracion->conf_ncomercial),
            'razonSocialSwf'=>htmlspecialchars($this->configuracion->conf_rsocial),
            'tipDocuEmisorSwf'=>'6',
            'nroDocumento'=>empty($remision->remi_clie_num_documento)?' ':$remision->remi_clie_num_documento,//Número de documento de identidad del adquirente o usuario
            'tipoDocumento'=>$remision->remi_clie_docu_id,//Tipo de documento de identidad del adquirente o usuario
            'razonSocialUsuario'=>htmlspecialchars($remision->remi_clie_rsocial),//Apellidos y nombres, denominación o razón social del adquirente o usuario 

            'tdocConductor'=>$remision->remi_doc_conduc,//Tipo de operación   //// 01 Venta normal *
            'docnroConductor'=>$remision->remi_docnum_conduc,
            'fechaIniTras'=>$remision->remi_fecha_initras,//Fecha de emisión 
            'placa'=>$remision->remi_placa_trans,
            'ubigeoFin'=>$remision->remi_fin,
            'direccionFin'=>$remision->remi_dir_fin,
            'ubigeoInicio'=>$remision->remi_inicio,
            'direccionInicio'=>$remision->remi_dir_ini,
        );

        $detalles = $this->Model_general->getProductosByRemision($id);
        
        $listaDetalle = array();
        foreach($detalles as $cod=>$deta){

            $listaDetalle[] = array(
                    'lineaSwf'=>$cod+1,
                    'unidadMedida'=>$deta->deta_unidad,//Código de unidad de medida por ítem
                    'cantItem'=>$deta->deta_cantidad,//Cantidad de unidades por ítem
                    'codiSunat'=>'',//Codigo producto SUNAT
                    'desItem'=>str_replace(array("\r","\n")," ",$deta->deta_descripcion),//Descripción detallada del servicio prestado, bien vendido o cedido en uso, indicando las características.
                );
        }

        $datos['listaDetalle'] = $listaDetalle;

        $datos['plantilla'] = "ConvertirGuiaRemisionXML";

        ob_start();
        $this->load->view('Venta/ComprobantesXML', $datos);
        $result = ob_get_contents();
        ob_end_clean();
        file_put_contents($file, $result);
    }

    public function getNumTextual($total,$moneda){
        $this->load->library('numl');
        $readnumber = $this->numl->NUML(floor($total));
        $nada = explode('.',number_format($total,2, '.', ''));
        $nada = $nada[1];
        $total_textual = strtoupper($readnumber) . ' CON ' . $nada . '/100 ' . (($moneda == "SOLES") ? " SOLES" : "DOLARES AMERICANOS");
        return $total_textual;
    }

 
    public function genera_pdf($id=0){
        $remision = $this->Model_general->getRemisionById($id);
        $usuario = $this->Model_general->getData('usuario', array("usua_nombres"), array("usua_id" => $remision->remi_usu_id));
        $fecha = date_create($remision->remi_fecha);
        $remision->remi_fecha = date_format($fecha, 'd/m/Y');
        $fecha = date_create($remision->remi_fecha_initras);
        $remision->remi_fecha_initras = date_format($fecha, 'd/m/Y');
        $remision->remi_comp_nombre = "GUÍA DE REMISION REMITENTE";
        
        $productos = $this->Model_general->getProductosByRemision($id);

        
        $this->load->library('pdf');
        
        $this->pdf = new Pdf();
        $this->pdf->AddPage();
        $this->pdf->AliasNbPages();
        $this->pdf->SetTitle($remision->remi_file);

        $this->pdf->SetFont('Arial', 'B', 7);

        $this->pdf->Image(base_url().'assets/img/global.png', 10, 07, 30,0 , 'PNG');
        $this->pdf->SetLeftMargin(40);
        /*$html = "<font face='helvetica' color='#777777'>{$this->configuracion->conf_rsocial}</font><br>";*/
        $html = "<font color='#ff0000' size='14' color='#333366'>     {$this->configuracion->conf_ncomercial}</font><br>";
        //$html .= "<font size='10' color='#777777'>                {$this->configuracion->conf_rsocial}</font><br>";
        $html .= "<font size='8' color='#777777'>  IMPORTACION - EXPORTACION - COMERCIALIZACION</font><br>";
        //$html .= "<font size='9' color='#777777'>     Ofrece: Menestras, Abarrotes, Bebidas, Pañales y Papeles Desechables entre otros.</font><br>";
        $html .= "<font size='8' color='#777777'>{$this->configuracion->conf_impr_direccion}</font><br>";
        $this->pdf->WriteHTML(utf8_decode($html));
        $this->pdf->SetFillColor(255);

        $this->pdf->SetLeftMargin(40);
        $this->pdf->tbr = 3.5;
        $html = "<br><font color='#777777' size='7'>          {$this->configuracion->conf_impr_contactos}<br>";
        $html .= "                                {$this->configuracion->conf_impr_telefonos}<br>";
        $html .= "                                      {$this->configuracion->conf_impr_web}</font>";
        $this->pdf->WriteHTML(utf8_decode($html));
        $this->pdf->SetFillColor(255);

        /*$this->pdf->SetY(10);
        $this->pdf->SetLeftMargin(120);
        $html = "<font face='helvetica' size='14' color='#777777'>   R.U.C. {$this->configuracion->conf_ruc}</font><br>";
        $html .= "<font color='#ff0000' size='10' color='#1d70b7'>{$remision->comp_nombre} ELECTRÓNICA</font><br>";
        $html .= "<font size='10' color='#777777'>{$remision->remi_serie} - {$remision->remi_numero}</font>";
        $this->pdf->WriteHTML(utf8_decode($html));*/
        
        $this->pdf->SetTextColor(30,30,30);
        $this->pdf->SetY(10);
        $this->pdf->SetLeftMargin(125);
        $this->pdf->SetFont('Arial', 'B', 8);
        
        $this->pdf->Cell(75,8,'R.U.C. '.$this->configuracion->conf_ruc,'',1,'C');
        $this->pdf->SetFillColor('240','240','240'); 
        $this->pdf->Cell(75,8,"GUÍA DE REMISIÓN ELECTRÓNICA",'',1,'C',true);
        $this->pdf->Cell(75,8,utf8_decode($remision->remi_serie." - ".$remision->remi_numero),'',1,'C');
        $this->pdf->RoundedRect(125, 10, 75,25, 1, '1234', 'B');

        $this->pdf->SetLeftMargin(10);
        
        $this->pdf->SetFont('Arial', 'B', 8);
        $this->pdf->Ln(5);
        $this->pdf->SetFillColor('200','200','200'); 
        $this->pdf->Cell(190,5,'DESTINATARIO',0,1,'L', true);
        $this->pdf->Cell(60,5,utf8_decode($remision->docu_nombre).":",0,0,'R');
        $this->pdf->SetFont('');
        $this->pdf->Cell(60,5,utf8_decode($remision->remi_clie_num_documento),0,0,'L');
        $this->pdf->Ln();
        $this->pdf->SetFont('Arial', 'B', 8);
        $this->pdf->Cell(60,5,utf8_decode("DIRECCIÓN").":",0,0,'R');
        $this->pdf->SetFont('');
        $this->pdf->Cell(60,5,utf8_decode($remision->remi_clie_rsocial),0,0,'L');

        $this->pdf->SetFont('Arial', 'B', 8);
        $this->pdf->Ln(5);
        $this->pdf->SetFillColor('200','200','200'); 
        $this->pdf->Cell(190,5,'DATOS DEL TRASLADO',0,1,'L', true);
        $this->pdf->Cell(60,5,utf8_decode('FECHA EMISIÓN').":",0,0,'R');
        $this->pdf->SetFont('');
        $this->pdf->Cell(60,5,utf8_decode($remision->remi_fecha),0,0,'L');
        $this->pdf->Ln();
        $this->pdf->SetFont('Arial', 'B', 8);
        $this->pdf->Cell(60,5,"FECHA INICIO DE TRASLADO".":",0,0,'R');
        $this->pdf->SetFont('');
        $this->pdf->Cell(60,5,utf8_decode($remision->remi_fecha_initras),0,0,'L');
        $this->pdf->Ln();
        $this->pdf->SetFont('Arial', 'B', 8);
        $this->pdf->Cell(60,5,"MOTIVO DE TRASLADO".":",0,0,'R');
        $this->pdf->SetFont('');
        $this->pdf->Cell(60,5,strtoupper(utf8_decode($remision->tras_denom)),0,0,'L');
        $this->pdf->Ln();
        $this->pdf->SetFont('Arial', 'B', 8);
        $this->pdf->Cell(60,5,"MODALIDAD DE TRANSPORTE".":",0,0,'R');
        $this->pdf->SetFont('');
        $this->pdf->Cell(60,5,"TRANSPORTE ".utf8_decode($remision->remi_modalidad),0,0,'L');
        $this->pdf->Ln();
        $this->pdf->SetFont('Arial', 'B', 8);
        $this->pdf->Cell(60,5,"PESO BRUTO TOTAL (KGM)".":",0,0,'R');
        $this->pdf->SetFont('');
        $this->pdf->Cell(60,5,$remision->remi_peso_bruto,0,0,'L');
        $this->pdf->Ln();
        $this->pdf->SetFont('Arial', 'B', 8);
        $this->pdf->Cell(60,5,utf8_decode("NÚMERO DE BULTOS").":",0,0,'R');
        $this->pdf->SetFont('');
        $this->pdf->Cell(60,5,$remision->remi_bultos,0,0,'L');

        $partida = $this->Model_general->getDataFromUbigeo($remision->remi_inicio);
        $partida = "(".$remision->remi_inicio.") ".$partida->dist_denominacion." - ".$partida->provincia." - ".$partida->departamento." - ".$remision->remi_dir_ini;
        $llegada = $this->Model_general->getDataFromUbigeo($remision->remi_fin);
        $llegada = "(".$remision->remi_fin.") ".$llegada->dist_denominacion." - ".$llegada->provincia." - ".$llegada->departamento." - ".$remision->remi_dir_fin;

        $this->pdf->SetFont('Arial', 'B', 8);
        $this->pdf->Ln(5);
        $this->pdf->SetFillColor('200','200','200'); 
        $this->pdf->Cell(190,5,'DATOS DEL PUNTO DE PARTID Y PUNTO DE LLEGADA',0,1,'L', true);
        $this->pdf->Cell(60,5,'PUNTO DE PARTIDA'.":",0,0,'R');
        $this->pdf->SetFont('');
        $this->pdf->Cell(60,5,utf8_decode($remision->remi_deta_inicio),0,0,'L');
        $this->pdf->Ln();
        $this->pdf->SetFont('Arial', 'B', 8);
        $this->pdf->Cell(60,5,utf8_decode("PUNTO DE LLEGADA").":",0,0,'R');
        $this->pdf->SetFont('');
        $this->pdf->Cell(60,5,utf8_decode($remision->remi_deta_inicio),0,0,'L');
        $doc_transport = $this->db->select("docu_nombre")->where("docu_id", $remision->remi_doc_trans)->get("maestra_documentos")->row();
        $doc_conduc = $this->db->select("docu_nombre")->where("docu_id", $remision->remi_doc_conduc)->get("maestra_documentos")->row();
        $transportista = $doc_transport->docu_nombre." ".$remision->remi_docnum_trans." - ".$remision->remi_denom_trans;
        $conductor = $doc_conduc->docu_nombre." ".$remision->remi_docnum_conduc." - ".$remision->remi_nombre_conduc;
        $this->pdf->SetFont('Arial', 'B', 8);
        $this->pdf->Ln(5);
        $this->pdf->SetFillColor('200','200','200'); 
        $this->pdf->Cell(190,5,'DATOS DEL TRANSPORTE',0,1,'L', true);
        $this->pdf->Cell(60,5,'TRANSPORTISTA'.":",0,0,'R');
        $this->pdf->SetFont('');
        $this->pdf->Cell(60,5,utf8_decode($transportista),0,0,'L');
        $this->pdf->Ln();
        $this->pdf->SetFont('Arial', 'B', 8);
        $this->pdf->Cell(60,5,"PLACA".":",0,0,'R');
        $this->pdf->SetFont('');
        $this->pdf->Cell(60,5,$remision->remi_placa_trans,0,0,'L');
        $this->pdf->Ln();
        $this->pdf->SetFont('Arial', 'B', 8);
        $this->pdf->Cell(60,5,"CONDUCTOR".":",0,0,'R');
        $this->pdf->SetFont('');
        $this->pdf->Cell(60,5,utf8_decode($conductor),0,0,'L');

        $header = array('NRO.', 'COD.', 'DESCRIPCION', 'UNIDAD','CANTIDAD');
        $w = array(15, 15, 105, 25, 30);
        $this->pdf->Ln(8);
        $this->pdf->SetFont('','B','');
        $this->pdf->SetFillColor('200','200','200'); 
        for($i = 0; $i < count($header); $i++)
            $this->pdf->Cell($w[$i],5,$header[$i],0,0,'C',true);
        $this->pdf->Ln();
        $this->pdf->SetFont('');

        $indice = 0;
 //print_r($productos);
        if(!empty($remision->remi_descripcion)){
            $tmp_producto[] = (object)array(
                'deta_descripcion'=>($remision->remi_descripcion),
                'deta_cantidad'=>'',
                'deta_prod_codigo'=>'',
                'deta_unidad'=>''
                );
            $productos = array_merge((array) $productos, $tmp_producto);
        }
        $lineas = 0;
        foreach ($productos as $num => $det) {
            $numero = 0;        
            preg_match_all("/.{1,60}[^ ]*/",$det->deta_descripcion,$arra);
            $det->deta_descripcion = implode("\r\n",$arra[0]);
            
            $hline = 7;
            $dess = array();
            
            if(preg_match("/\n/",$det->deta_descripcion)){ ///  para saltos de linea
                $dess = explode("\n",$det->deta_descripcion);
                $det->deta_descripcion = $dess[0];
                $hline = 3;
                $this->pdf->Ln(2);
            }            

            $det->deta_cantidad = empty($det->deta_cantidad)?'':ROUND($det->deta_cantidad);
            $this->pdf->Cell($w[0],$hline,$num+1,'',0,'C');
            $this->pdf->Cell($w[1],$hline,$det->deta_codigo,'',0,'C');
            $this->pdf->Cell($w[2],$hline,utf8_decode($det->deta_descripcion),'',0,'L');
            $this->pdf->Cell($w[3],$hline,$det->deta_unidad,'',0,'C');
            $this->pdf->Cell($w[4],$hline,$det->deta_cantidad,'',0,'C');
            $this->pdf->Ln();
            $lineas++;
            
            if(count($dess)>0){
                unset($dess[0]);
                foreach($dess as $desc){
                    $this->pdf->Cell($w[0],$hline,'',0,0,'C');
                    $this->pdf->Cell($w[1],$hline,'',0,0,'C');
                    $this->pdf->Cell($w[2],$hline,utf8_decode($desc),0,0,'L');
                    $this->pdf->Cell($w[3],$hline,'',0,0,'C');
                    $this->pdf->Cell($w[4],$hline,'',0,0,'C');
                    $this->pdf->Ln();
                    $lineas++;
                }
                //$this->pdf->Ln(1);
            }
            
            $indice++;
        }
        
        $this->pdf->Cell(170,5,$remision->remi_digestvalue,0,1,'C');
        $this->pdf->Cell(170,5,utf8_decode('Representación impresa de la Guía de Remisión').utf8_decode(' electrónica'),'',1,'C');
        
        $this->pdf->Cell(170,5,utf8_decode('Puede verificarla www.punored.com'),'',1,'C');
        
        $this->pdf->Output("files/REPO/{$remision->remi_file}.pdf",'F');
    }
    

    function getXML($id){
        $remision = $this->db->query("SELECT * FROM remision WHERE remi_id='{$id}'")->row();
        $file = "files/FIRMA/{$remision->remi_file}.xml";
        if(!file_exists($file)) die("No se ha encontrado el arhivo digital.");
        header('Content-Type: application/octet-stream');
        header("Content-Transfer-Encoding: Binary"); 
        header("Content-disposition: attachment; filename=\"" . basename($file) . "\""); 
        readfile($file); // do the double-download-dance (dirty but worky)
    }
    function getCDR($id){
        $remision = $this->db->query("SELECT * FROM remision WHERE remi_id='{$id}'")->row();
        $file = "files/RPTA/R{$remision->remi_file}.zip";

        if(!file_exists($file)){
          die("No se ha encontrado el arhivo digital.".$file);  
        } 
        header('Content-Type: application/octet-stream');
        header("Content-Transfer-Encoding: Binary"); 
        header("Content-disposition: attachment; filename=\"" . basename($file) . "\""); 
        readfile($file); // do the double-download-dance (dirty but worky)
    }
    function reporte_excel(){
        $hasta = $this->input->get('hasta');
        $desde = $this->input->get('desde');
        /*
        $moneda = $this->input->get('moneda');
        $tipo = $this->input->get('comprobantes');
        */
        $search = $this->input->get('search');
        $situacion = $this->input->get('situacion');
        
        $this->db->select("DATE_FORMAT(R.remi_fecha,'%d/%m/%Y') AS fecha, 'GUIA REMISION' AS ctipo, SIT.situ_grup_nombre as situacion, R.remi_inicio, R.remi_fin,R.remi_serie AS serie, R.remi_numero AS numero, M.tras_denom as m_tras, R.remi_peso_bruto as peso, R.remi_bultos as bultos, DATE_FORMAT(R.remi_fecha_initras,'%d/%m/%Y') as f_inicio, R.remi_clie_num_documento as docid_nro, R.remi_clie_rsocial AS rsocial, R.remi_clie_docu_id AS clie_docu, R.remi_denom_trans as denom_trans,R.remi_docnum_trans as docnum_trans, R.remi_doc_trans as doc_trans, R.remi_placa_trans as placa, R.remi_doc_conduc as doc_conduc, R.remi_dir_ini as dir_ini, R.remi_dir_fin as dir_fin,R.remi_docnum_conduc as docnum_conduc, R.remi_nombre_conduc as nombre_conduc,R.remi_descripcion as vdesc, GROUP_CONCAT(DISTINCT VD.deta_descripcion ORDER BY VD.deta_id ASC) AS detalle");
        $this->db->from("remision R");
        $this->db->join("remision_detalle VD","VD.deta_remi_id = R.remi_id");
        $this->db->join("motivo_traslado M","M.tras_id = R.remi_motivo_tras");
        $this->db->join("factura_situacion SIT","SIT.situ_id = R.remi_fact_gsitu");
        $this->db->where("R.remi_fecha BETWEEN '$desde' AND '$hasta'".($situacion != ''?"AND SIT.situ_id = '$situacion'":"")." ".($search != ""? " AND (R.remi_clie_rsocial LIKE '%$search%' OR R.remi_serie LIKE '%$search%' OR R.remi_numero LIKE '%$search%')":""));
        $this->db->group_by('R.remi_id');
        $this->db->order_by("R.remi_serie","ASC");
        $this->db->order_by("R.remi_numero","ASC");
        $documentos = $this->db->get()->result();

        foreach ($documentos as $val) {
            $val->ubi_inicio = $this->buscar_ubigeo($val->remi_inicio);
            $val->ubi_fin = $this->buscar_ubigeo($val->remi_fin);
            $val->doc_conduc = $this->db->where('docu_id', $val->doc_conduc)->get("maestra_documentos")->row()->docu_nombre;
            $val->doc_trans = $this->db->where('docu_id', $val->doc_trans)->get("maestra_documentos")->row()->docu_nombre;
            $val->clie_docu = $this->db->where('docu_id', $val->clie_docu)->get("maestra_documentos")->row()->docu_nombre;
        }
        
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
                ->setCellValue('A1', 'FECHA')
                ->setCellValue('B1', 'TIPO')
                ->setCellValue('C1', 'SERIE')
                ->setCellValue('D1', 'NUMERO')
                ->setCellValue('E1', 'DOCUMENTO')
                ->setCellValue('F1', 'NUMERO')
                ->setCellValue('G1', 'RAZON SOCIAL')
                ->setCellValue('H1', 'MOTIVO')
                ->setCellValue('I1', 'PESO')
                ->setCellValue('J1', 'BULTOS')
                ->setCellValue('K1', 'FECHA INICIO')
                ->setCellValue('L1', 'T. DOCUMENTO')
                ->setCellValue('M1', 'T. NUMERO')
                ->setCellValue('N1', 'T. DENOMINACION')
                ->setCellValue('O1', 'T. PLACA')
                ->setCellValue('P1', 'C. DOCUMENTO')
                ->setCellValue('Q1', 'C. NUMERO')
                ->setCellValue('R1', 'C. NOMBRE')
                ->setCellValue('S1', 'PUNTO DE PARTIDA')
                ->setCellValue('T1', 'PARTIDA DIRECCION')
                ->setCellValue('U1', 'PUNTO DE LLEGADA')
                ->setCellValue('V1', 'LLEGADA DIRECCION')
                ->setCellValue('W1', 'DETALLE');
        
        $objPHPExcel->getActiveSheet()->getStyle('F')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
        $objPHPExcel->getActiveSheet()->getStyle('D')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);      
        $objPHPExcel->getActiveSheet()->getStyle('M')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);      
        $objPHPExcel->getActiveSheet()->getStyle('Q')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);      
        $objPHPExcel->getActiveSheet()->freezePane('A2');
        
        $ini = 3;
        $index = 0;
        
        foreach($documentos as $fila){
            $nro = $index+$ini;
            $index++;
            $objPHPExcel->getActiveSheet()
                        ->setCellValue("A$nro", $fila->fecha)
                        ->setCellValue("B$nro", $fila->ctipo)
                        ->setCellValue("C$nro", $fila->serie)
                        ->setCellValue("D$nro", $fila->numero)
                        ->setCellValue("E$nro", $fila->clie_docu)
                        ->setCellValue("F$nro", $fila->docid_nro)
                        ->setCellValue("G$nro", $fila->rsocial)
                        ->setCellValue("H$nro", $fila->m_tras)
                        ->setCellValue("I$nro", $fila->peso)
                        ->setCellValue("J$nro", $fila->bultos)
                        ->setCellValue("K$nro", $fila->f_inicio)
                        ->setCellValue("L$nro", $fila->doc_trans)
                        ->setCellValue("M$nro", $fila->docnum_trans)
                        ->setCellValue("N$nro", $fila->denom_trans)
                        ->setCellValue("O$nro", $fila->placa)
                        ->setCellValue("P$nro", $fila->doc_conduc)
                        ->setCellValue("Q$nro", $fila->docnum_conduc)
                        ->setCellValue("R$nro", $fila->nombre_conduc)
                        ->setCellValue("S$nro", $fila->ubi_inicio)
                        ->setCellValue("T$nro", $fila->dir_ini)
                        ->setCellValue("U$nro", $fila->ubi_fin)
                        ->setCellValue("V$nro", $fila->dir_fin)
                        ->setCellValue("W$nro", $fila->detalle);
            }
            foreach(range('A','W') as $nro)
            {
                $objPHPExcel->getActiveSheet()->getColumnDimension($nro)->setAutoSize(true);
            }
        
        $fin = $index+$ini-1;
        $objPHPExcel->getActiveSheet()->getStyle("I$ini:I$fin")->getNumberFormat()->setFormatCode('#,##0.00'); 
        $objPHPExcel->getActiveSheet()->getStyle("A$ini:A$fin")->getNumberFormat()->setFormatCode('dd/mm/yyyy');
        
        $excel->excel_output($objPHPExcel, 'GUIAS DE REMISION '.$desde." - ".$hasta);
    }
    
}