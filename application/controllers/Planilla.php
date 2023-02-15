<?php 
class Planilla extends CI_Controller
{
    var $configuracion;
    var $titulos;
	function __construct() {
        parent::__construct();
        if(!$this->session->userdata('authorized')){
            redirect(base_url()."login");
        }
        $this->usua_id = $this->session->userdata('authorized');
        $this->configuracion = $this->db->query("SELECT * FROM usuario WHERE usua_id='{$this->usua_id}'")->row();
        $this->permisos  = $this->Model_general->getPermisos($this->usua_id);
    }
    public function plan_listado(){
        $json = isset($_GET['json']) ? $_GET['json'] : false;

        if ($json) {

            $mes = $this->input->post("mes");
            $anio = $this->input->post("anio");
            $condicion = array("peri_mes" => $mes, "peri_anio" => $anio);
            
            $periodo = $this->db->where($condicion)->get("planilla_periodo");

            $this->db->select("emp_id, emp_dni, emp_cuspp, CONCAT(emp_paterno,' ',emp_materno,', ',emp_nombres) nombres, DATE_FORMAT(emp_fechaIngreso, '%d/%m/%Y') emp_fechaIngreso, ocu_descripcion, plan_asigFami, plan_remuBasico, plan_remuAsig, plan_otros, plan_remuTotal, plan_remuNeto, plan_salud, plan_totalAporte, plan_descTotal, plan_id, plan_espagado");
            $this->db->from("planilla");
            $this->db->join("planilla_empleado","emp_id = plan_emp_id");
            $this->db->join("ocupacion","ocu_id = emp_ocu_id");
            if($periodo->num_rows() > 0){
                $peri_id = $periodo->row()->peri_id;
                $this->db->where("plan_peri_id", $peri_id);
            }else{
                $this->db->where("plan_peri_id", 0);
            }
            $empleados = $this->db->get()->result();

            $html = "";
            if(COUNT($empleados) > 0){
                foreach ($empleados as $i => $row) {
                    $oculto = ($row->plan_espagado == '1')?'oculto':'';

                    $btn_eliminar = "<a href='".base_url()."Planilla/plan_eliminar/".$row->plan_id."' class='btn btn-danger btn-xs eliminar ".$oculto."' type='btn'><i class='glyphicon glyphicon-trash'></i></a>";
                    $btn_editar = "<a href='".base_url()."Planilla/plan_crear/".$row->plan_id."' class='btn btn-success btn-xs editar ".$oculto."' type='btn'><i class='glyphicon glyphicon-edit'></i></a>";
                    $btn_pagos = "<a href='".base_url()."Planilla/plan_pagos/".$row->plan_id."' class='btn btn-warning btn-xs ".$oculto."' type='btn'><i class='glyphicon glyphicon-list'></i></a>";
                    $btn_pagar = "<a href='".base_url()."Planilla/plan_pagar/".$row->plan_id."' class='btn btn-primary btn-xs ".$oculto." pagos' type='btn'><i class='glyphicon glyphicon-usd'></i></a>";
                    $btn_cancelpago = "<a href='".base_url()."Planilla/plan_cancelarPago/".$row->plan_id."' class='btn btn-danger btn-xs cancelar' type='btn' title='Cancelar Pago'><i class='glyphicon glyphicon-usd'></i> Cancelar</a>";
                    $btn_imprimir = "<a href='".base_url()."Planilla/plan_imprimir/".$row->plan_id."' class='btn btn-primary btn-xs imprimir' type='btn' title='Imprimir'><i class='glyphicon glyphicon-print'></i></a>";

                    $html .= "<tr ".($row->plan_espagado == 1?"class='success'":"").">";
                    $html .= "<td style='white-space:nowrap'>".$btn_editar." ".$btn_eliminar." ".$btn_pagos." ".$btn_pagar." ".($row->plan_espagado == 1?$btn_cancelpago." ".$btn_imprimir:"")."</td>";
                    $html .= "<td>".$row->emp_dni."</td>";
                    $html .= "<td>".$row->emp_cuspp."</td>";
                    $html .= "<td style='white-space:nowrap'>".$row->nombres."</td>";
                    $html .= "<td style='white-space:nowrap'>".$row->emp_fechaIngreso."</td>";
                    $html .= "<td style='white-space:nowrap'>".$row->ocu_descripcion."</td>";
                    $html .= "<td>".$row->plan_asigFami."</td>";
                    $html .= "<td class='dinero'>".$row->plan_remuBasico."</td>";
                    $html .= "<td class='dinero'>".$row->plan_remuAsig."</td>";
                    $html .= "<td class='dinero'>".$row->plan_otros."</td>";
                    $html .= "<td class='dinero'>".$row->plan_remuTotal."</td>";
                    $html .= "<td class='dinero'>".$row->plan_descTotal."</td>";
                    $html .= "<td class='dinero'>".$row->plan_remuNeto."</td>";
                    $html .= "<td class='dinero'>".$row->plan_salud."</td>";
                    $html .= "<td class='dinero'>".$row->plan_totalAporte."</td>";
                    $html .= "</tr>";
                }
            }else{
                $html = "<tr><th colspan='15' align='center'>No se encontraron registros</th></tr>";
            }

            $datos["html"] = $html;
            echo json_encode($datos);
            exit(0);
        }
        
        $this->load->helper('Funciones');
        $this->load->library('Ssp');
        $this->load->library('Cssjs');
        $datos['titulo'] = "Planilla de remuneraciones";
        $perio = $this->getMesesAnios();
        unset($perio["anios"][""]);
        $datos["meses"] = $perio["meses"];
        $datos["anios"] = $perio["anios"];
        $datos["cur_mes"] = date("m");
        $datos["cur_anio"] = date("Y");
        $this->cssjs->add_js(base_url().'assets/js/Planilla/listado.js?v=1.0',false,false);
        $this->load->view('header');
        $this->load->view('menu');
        $this->load->view($this->router->fetch_class().'/'.$this->router->fetch_method(), $datos);
        $this->load->view('footer');
    }
    public function plan_crear($plan_id = ''){
        if($plan_id != ''){
            $planilla = $this->db->where("plan_id", $plan_id)->get("planilla")->row();
        }else{
            $planilla =  new StdClass;
            $planilla->plan_id = "";
            $planilla->plan_asigFami = "NO";
            $planilla->plan_remuBasico = 0.00;
            $planilla->plan_remuAsig = 0.00;
            $planilla->plan_otros = 0.00;
            $planilla->plan_remuTotal = 0.00;
            $planilla->plan_descTotal = 0.00;
            $planilla->plan_remuNeto = 0.00;
            $planilla->plan_salud = 0.00;
            $planilla->plan_totalAporte = 0.00;
            $planilla->plan_emp_id = "";
            $planilla->plan_peri_id = "";
        }

        $datos["plan"] = $planilla;

        $datos["asignacion"] = $this->Model_general->enum_valores('planilla','plan_asigFami');
        $datos = array_merge($datos, $this->getEmpPeri());

        $this->load->view($this->router->fetch_class().'/plan_form', $datos);
    }
    public function plan_add_emp(){
        $this->load->view($this->router->fetch_class().'/plan_add_emp', $this->getEmpPeri());
    }
    
    public function plan_guardar($plan_id=''){
        $empleado = $this->input->post("empleado");
        $periodo = $this->input->post("periodo");
        $asigFamiliar = $this->input->post("asigFamiliar");
        $remuBasico = $this->input->post("remuBasico");
        $remuAsig = $this->input->post("remuAsig");
        $otros = $this->input->post("otros");
        $remuTotal = $this->input->post("remuTotal");
        $descTotal = $this->input->post("descTotal");
        $remuNeto = $this->input->post("remuNeto");
        $salud = $this->input->post("salud");
        $totalAporte = $this->input->post("totalAporte");
        
        $planilla = array("plan_asigFami" => $asigFamiliar,
                            "plan_remuBasico" => $remuBasico,
                            "plan_remuAsig" => $remuAsig,
                            "plan_otros" => $otros,
                            "plan_remuTotal" => $remuTotal,
                            "plan_descTotal" => $descTotal,
                            "plan_remuNeto" => $remuNeto,
                            "plan_salud" => $salud,
                            "plan_totalAporte" => $totalAporte
                    );


        if($plan_id != ""){
            $condicion = array("plan_id" => $plan_id);
            if($this->Model_general->guardar_edit_registro("planilla", $planilla, $condicion) == TRUE){
                $this->Model_general->add_log("EDITAR",16,"Edicion de planilla");
                $resp["exito"] = true;
                $resp["mensaje"] = "Empleado actualizado con exito";
            }else{
                $resp["exito"] = false;
                $resp["mensaje"] = "Ocurrio un error";
            }
        }else{
            if($this->Model_general->guardar_registro("planilla", $planilla) == TRUE){
                $this->Model_general->add_log("CREAR",16,"Creacion de planilla");
                $resp["exito"] = true;
                $resp["mensaje"] = "Empleado registrado con exito";
            }else{
                $resp["exito"] = false;
                $resp["mensaje"] = "Ocurrio un error";
            }
        }
        echo json_encode($resp);
    }
    public function plan_eliminar($plan_id=''){
        $this->Model_general->borrar(array("plan_id" => $plan_id), "planilla");
        $this->Model_general->add_log("ELIMINAR",16,"Eliminacion de empleado en planilla");
        $resp["exito"] = true;
        $resp["mensaje"] = "Eliminado con exito";
        echo json_encode($resp);
    }
    public function plan_pagos($plan_id){
        $json = isset($_GET['json']) ? $_GET['json'] : false;
        $this->db->select("plan_id id, emp_dni dni, CONCAT(emp_paterno,' ',emp_materno,', ',emp_nombres) nombres, emp_fechaIngreso fechaIngreso, ocu_descripcion cargo, emp_asigFami asigFami, plan_remubasico basico");
        $this->db->from("planilla");
        $this->db->join("planilla_empleado", "emp_id = plan_emp_id");
        $this->db->join("ocupacion", "ocu_id = emp_ocu_id");
        $this->db->where("plan_id", $plan_id);
        $plan = $this->db->get()->row();

        if ($json) {

            $mes = $this->input->post("mes");
            $anio = $this->input->post("anio");
            $condicion = array("peri_mes" => $mes, "peri_anio" => $anio);
            
            $periodo = $this->db->where($condicion)->get("planilla_periodo");


            //$this->db->select("emp_id, emp_dni, emp_cuspp, CONCAT(emp_paterno,' ',emp_materno,', ',emp_nombres) nombres, DATE_FORMAT(emp_fechaIngreso, '%d/%m/%Y') emp_fechaIngreso, ocu_descripcion, plan_asigFami, plan_remuBasico, plan_remuAsig, plan_otros, plan_remuTotal, plan_remuNeto, plan_salud, plan_totalAporte, plan_descTotal, plan_id");
            $this->db->from("planilla_descadic");
            $this->db->where("desa_plan_id", $plan_id);
            $detalles = $this->db->get()->result();

            $html = "";
            $total = $plan->basico;
            if(COUNT($detalles) > 0){
                foreach ($detalles as $i => $row) {
                    $total += $row->desa_total;
                    if($row->desa_tipo == "ADICION"){
                        $adicion = $row->desa_monto;
                        $descuento = "0.00";
                    }else{
                        $descuento = $row->desa_monto;
                        $adicion = "0.00";
                    }

                    $btn_eliminar = "<a href='".base_url()."Planilla/plan_eliminar_adides/".$row->desa_id."' class='btn btn-danger btn-xs eliminar' type='btn'><i class='glyphicon glyphicon-trash'></i></a>";
                    $btn_editar = "<a href='".base_url()."Planilla/plan_adides/".$plan->id."/".$row->desa_id."' class='btn btn-success btn-xs editar' type='btn'><i class='glyphicon glyphicon-edit'></i></a>";

                    $html .= "<tr>";
                    $html .= "<td style='white-space:nowrap'>".$btn_editar." ".$btn_eliminar."</td>";
                    $html .= "<td>".$row->desa_fecha."</td>";
                    $html .= "<td>".$row->desa_concepto."</td>";
                    $html .= "<td class='dinero'>".$row->desa_gastEmpresa."</td>";
                    $html .= "<td class='dinero'>".$adicion."</td>";
                    $html .= "<td class='dinero'>".$descuento."</td>";
                    $html .= "<td class='dinero'>".$row->desa_otros."</td>";
                    $html .= "<td class='dinero'>".$row->desa_total."</td>";
                    $html .= "</tr>";
                }
            }
            $html .= "<tr><th colspan='7' align='center'>MONTO A PAGAR</th><th class='dinero'>".number_format($total,2)."</th></tr>";

            $datos["html"] = $html;
            echo json_encode($datos);
            exit(0);
        }


        $ref = "<table class='table table-striped table-bordered'>";
        $ref .= "<tr><th>DNI</th><td>".$plan->dni."</td><th>Referencia</th><td>".$plan->nombres."</td><th>Fecha Inicio</th><td>".$plan->fechaIngreso."</td></tr>";
        $ref .= "<tr><th>Cargo</th><td>".$plan->cargo."</td><th>Asignacion Familiar</th><td>".$plan->asigFami."</td><th>Sueldo Basico</th><td>".$plan->basico."</td></tr>";
        $ref .= "</table>";
        
        $this->load->helper('Funciones');
        $this->load->library('Ssp');
        $this->load->library('Cssjs');
        $datos['titulo'] = "Descuentos y adiciones del trabajor";
        $datos['ref'] = $ref;
        $datos['plan_id'] = $plan->id;
        
        $this->cssjs->add_js(base_url().'assets/js/Planilla/plan_descadi.js?v=1.0',false,false);
        $this->load->view('header');
        $this->load->view('menu');
        $this->load->view($this->router->fetch_class().'/'.$this->router->fetch_method(), $datos);
        $this->load->view('footer');
    }
    public function plan_adides($plan_id, $desa_id = ""){
        if($desa_id != ""){
            $this->db->select("*, DATE_FORMAT(desa_fecha, '%d/%m/%Y') desa_fecha");
            $desa = $this->db->where("desa_id", $desa_id)->get("planilla_descadic")->row();
        }else{
            $desa = new StdClass;
            $desa->desa_id = "";
            $desa->desa_fecha = date("d/m/Y");
            $desa->desa_concepto = "";
            $desa->desa_gastEmpresa = "0.00";
            $desa->desa_monto = "0.00";
            $desa->desa_otros = "0.00";
            $desa->desa_tipo = "ADICION";
            $desa->desa_plan_id = $plan_id;
        }
        $datos["desa"] = $desa;
        $datos["tipo"] = $this->Model_general->enum_valores('planilla_descadic','desa_tipo');
        $this->load->view($this->router->fetch_class().'/plan_adides_form', $datos);
    }
    public function plan_guardar_adides($desa_id=''){
        $plan_id = $this->input->post("plan_id");
        $fecha = $this->Model_general->fecha_to_mysql($this->input->post("fecha")); 
        $gastEmp = $this->input->post("gastEmp");
        $monto = $this->input->post("monto");
        $tipo = $this->input->post("tipo");
        $otros = $this->input->post("otros");
        $concepto = $this->input->post("concepto");
        if($tipo == "ADICION"){
            $total = $otros + $monto;
        }else{
            $total = $otros - $monto;
        }

        $desa = array("desa_fecha" => $fecha,
                        "desa_concepto" => $concepto,
                        "desa_gastEmpresa" => $gastEmp,
                        "desa_monto" => $monto,
                        "desa_otros" => $otros,
                        "desa_total" => $total,
                        "desa_plan_id" => $plan_id,
                        "desa_tipo" => $tipo
                    );
        $this->db->trans_begin();
        if($desa_id != ""){
            $condicion = array("desa_id" => $desa_id);
            if($this->Model_general->guardar_edit_registro("planilla_descadic", $desa, $condicion) == TRUE){
                $this->actualizaOtro($plan_id);
                $resp["exito"] = true;
                $resp["mensaje"] = "Actualizado con exito";
            }else{
                $this->db->trans_rollback();
                $resp["exito"] = false;
                $resp["mensaje"] = "Ocurrio un error";
            }
        }else{
            if($this->Model_general->guardar_registro("planilla_descadic", $desa) == TRUE){
                $this->actualizaOtro($plan_id);
                $resp["exito"] = true;
                $resp["mensaje"] = "Registrado con exito";
            }else{
                $this->db->trans_rollback();
                $resp["exito"] = false;
                $resp["mensaje"] = "Ocurrio un error";
            }
        }
        $this->db->trans_commit();
        echo json_encode($resp);
    }
    public function actualizaOtro($plan_id){
        $this->db->query("UPDATE planilla SET plan_otros = (SELECT SUM(desa_total) FROM planilla_descadic WHERE desa_plan_id = {$plan_id}) WHERE plan_id = {$plan_id}");
        if ($this->db->trans_status() === FALSE){
            $resp["exito"] = false;
            $resp["mensaje"] = "Error al guardar datos, intentelo mas tarde externo";
            $this->db->trans_rollback();
            $this->Model_general->dieMsg($resp);
        }else{
            $this->db->query("UPDATE planilla SET plan_remuTotal = (plan_remuBasico + plan_remuAsig + plan_otros), plan_remuNeto = (plan_remuTotal - plan_descTotal) WHERE plan_id = {$plan_id}");
            
            if ($this->db->trans_status() === FALSE){
                $resp["exito"] = false;
                $resp["mensaje"] = "Error al guardar datos, intentelo mas tarde interno";
                $this->db->trans_rollback();
                $this->Model_general->dieMsg($resp);    
            }
        }
    }
    public function plan_eliminar_adides($desa_id=''){
        $where = array("desa_id" => $desa_id);
        $plan_id = $this->db->where($where)->get("planilla_descadic")->row()->desa_plan_id;

        $this->db->trans_begin();

        $this->db->delete('planilla_descadic', $where); 

        $this->actualizaOtro($plan_id);

        $this->db->trans_commit();
        $resp["exito"] = true;
        $resp["mensaje"] = "Eliminado con exito";
        echo json_encode($resp);
    }
    public function plan_pagar($plan_id){
        $this->db->select("CONCAT(emp_paterno,' ',emp_materno,', ',emp_nombres) nombres, plan_remuNeto neto, plan_id");
        $this->db->from("planilla");
        $this->db->join("planilla_empleado","emp_id = plan_emp_id");
        $this->db->where("plan_id", $plan_id);
        $plan = $this->db->get()->row();
        $datos["plan"] = $plan;
        $datos["documentos"] = $this->Model_general->getOptionsWhere("comprobante_tipo",array("tcom_id","tcom_nombre"),array("tcom_id<>"=>'07', "tcom_id<>"=>'08'));
        $datos["cuentas"] = $this->Model_general->getOptions("cuenta",array("cuen_id","cuen_banco"),'* Cuenta');
        $this->load->view($this->router->fetch_class().'/form_pagar', $datos);
    }
    public function plan_guardarPago($plan_id){
        //$this->Model_general->add_log("CREAR",16,"Creación de período ".$mesName." ".$anio);

        $documento = $this->input->post("documento");
        $serie = $this->input->post("serie");
        $numero = $this->input->post("numero");
        $cuenta = $this->input->post("cuenta");
        $codigo = $this->input->post("codigo-cuen");
        $total = $this->input->post("total");
        $obs = $this->input->post("observacion");

        $this->db->select("CONCAT(emp_paterno,' ',emp_materno,', ',emp_nombres) nombres, planilla.*");
        $this->db->from("planilla");
        $this->db->join("planilla_empleado","emp_id = plan_emp_id");
        $this->db->where("plan_id", $plan_id);
        $plan = $this->db->get()->row();
        if($plan->plan_espagado == '1'){
            $resp["exito"] = false;
            $resp["mensaje"] = "Ya esta pagado";
        }else{
            $this->db->trans_start();
        
            $movimiento = $this->Model_general->actualizarCaja(5, 'SALIDA', $documento, $serie, $numero, "Pago de salario a ".$plan->nombres, $total, 'SOLES', $this->usua_id, $plan_id, '', $cuenta,$codigo,date('Y-m-d'),$obs);

            
            $dte = array("plan_espagado" => 1, 
                            "plan_pagofecha" => date('Y-m-d'),
                            "plan_pagodesc" => $obs
                        );

            $this->Model_general->guardar_edit_registro("planilla", $dte, array('plan_id' => $plan_id));
            $this->Model_general->add_log("PAGO",16,"Pago de remuneracion a ".$plan->nombres." ".$total." / SOLES, Código de caja: ".$codigo);

            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE){
                $resp['exito'] = false;
                $resp['mensaje'] = "Error al guardar los datos";
            }else{
                $resp['exito'] = true;  
                $resp['mensaje'] = "Datos guardados con exito";
            }

        }
        echo json_encode($resp);
    }
    public function plan_cancelarPago($plan_id){

        $this->db->select("CONCAT(emp_paterno,' ',emp_materno,', ',emp_nombres) nombres, planilla.*");
        $this->db->from("planilla");
        $this->db->join("planilla_empleado","emp_id = plan_emp_id");
        $this->db->where("plan_id", $plan_id);
        $plan = $this->db->get()->row();

        $mov = $this->db->where(array("movi_ref_id" => $plan_id, "movi_tipo_id" => 5))->get("cuenta_movimiento")->row();

        if($plan->plan_espagado == '0'){
            $resp["exito"] = false;
            $resp["mensaje"] = "Ya esta cancelado";
        }else{
            $this->db->trans_start();
        
            $this->Model_general->actualizarCaja(5, 'INGRESO', '', '', '', "Pago cancelado de".$plan->nombres, $plan->plan_remuNeto, 'SOLES', $this->usua_id, $plan_id, '', $mov->movi_cuen_id,"000000",'',"Anulacion de pago");
            
            $dte = array("plan_espagado" => 0, 
                            "plan_pagofecha" => NULL,
                            "plan_pagodesc" => NULL
                        );

            $this->Model_general->guardar_edit_registro("planilla", $dte, array('plan_id' => $plan_id));
            $this->Model_general->add_log("PAGO",16,"Pago de remuneracion cancelado a ".$plan->nombres." ".$plan->plan_remuNeto." / SOLES");

            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE){
                $resp['exito'] = false;
                $resp['mensaje'] = "Error al guardar los datos";
            }else{
                $resp['exito'] = true;  
                $resp['mensaje'] = "Datos guardados con exito";
            }
        }
        echo json_encode($resp);
    }
    public function plan_imprimir($plan_id){
        $datos["plan_id"] = $plan_id;
        $this->load->view($this->router->fetch_class().'/plan_verPago', $datos);
    }
    public function plan_generaPdfPago($plan_id){
        
        $ruc = "20447819482";
        $empleador = "JUMBO TRAVEL EIRL";

        $this->db->select("UPPER(CONCAT(peri_mesName,' - ',peri_anio)) periodo, emp_dni docnum, CONCAT(emp_paterno,' ',emp_materno,', ',emp_nombres) nombres, DATE_FORMAT(emp_fechaIngreso, '%d/%m/%Y') fingreso, ocu_descripcion cargo, emp_cuspp cuspp, plan_remuBasico basico, plan_remuAsig asigFami, plan_otros otros, plan_descTotal descTotal, plan_remuNeto neto");
        $this->db->from("planilla");
        $this->db->join("planilla_periodo", "peri_id = plan_peri_id");
        $this->db->join("planilla_empleado", "emp_id = plan_emp_id");
        $this->db->join("ocupacion", "ocu_id = emp_ocu_id");
        $this->db->where("plan_id", $plan_id);
        $planilla = $this->db->get()->row();

        $this->load->library('pdf');
        
        $this->pdf = new Pdf();
        $this->pdf->AddPage();
        $this->pdf->AliasNbPages();
        $this->pdf->SetTitle("BOLETA DE PAGO");
        $this->pdf->SetLeftMargin(10);
        
        $this->pdf->SetFont('Arial', '', 8);
        $this->pdf->Image(base_url().'assets/img/logo_jumbo.jpg', 164, 7, 35,0 , 'JPG');
        $html = "<font size='12' color='#34495E'><strong>EMPRESA JUMBO</strong></font><br>";
        $html .= "<font size='12' color='#34495E'><strong>TRAVEL E.I.R.L.</strong></font><br>";
        $this->pdf->SetY(7);
        $this->pdf->WriteHTML(utf8_decode($html));
        $this->pdf->tbr = 3.5;
        $this->pdf->SetY(10);
        $this->pdf->SetFont('Arial', 'B', 16);

        $this->pdf->Cell(190,8,utf8_decode('BOLETA DE PAGO'),0,0,'C');
        $this->pdf->Ln();
        
        $this->pdf->SetFont('Arial', 'B', 8);
        $this->pdf->Cell(30,8,'RUC',0,0,'L');
        $this->pdf->SetFont('');
        $this->pdf->Cell(160,8,": ".$ruc,0,0,'L');
        $this->pdf->Ln(5);
        $this->pdf->SetFont('', 'B', '');
        $this->pdf->Cell(30,8,'EMPLEADOR',0,0,'L');
        $this->pdf->SetFont('');
        $this->pdf->Cell(160,8,": ".$empleador,0,0,'L');
        $this->pdf->Ln(5);
        $this->pdf->SetFont('', 'B', '');
        $this->pdf->Cell(30,8,'PERIODO',0,0,'L');
        $this->pdf->SetFont('');
        $this->pdf->Cell(160,8,": ".$planilla->periodo,0,0,'L');
        $this->pdf->Ln();
        $this->pdf->SetFont('', 'B', '');
        $this->pdf->Cell(30,8,'Doc. Tipo',0,0,'L');
        $this->pdf->SetFont('');
        $this->pdf->Cell(65,8,': DNI',0,0,'L');
        $this->pdf->SetFont('', 'B', '');
        $this->pdf->Cell(30,8,'Doc. Nro.',0,0,'L');
        $this->pdf->SetFont('');
        $this->pdf->Cell(65,8,": ".$planilla->docnum,0,0,'L');

        $this->pdf->Ln(5);
        $this->pdf->SetFont('', 'B', '');
        $this->pdf->Cell(30,8,'Apellidos y nombres',0,0,'L');
        $this->pdf->SetFont('');
        $this->pdf->Cell(65,8,": ".utf8_decode($planilla->nombres),0,0,'L');
        $this->pdf->SetFont('', 'B', '');
        $this->pdf->Cell(30,8,utf8_decode('Situación'),0,0,'L');
        $this->pdf->SetFont('');
        $this->pdf->Cell(65,8,": ACTIVO O SUBSDIADO",0,0,'L');

        $this->pdf->Ln(5);
        $this->pdf->SetFont('', 'B', '');
        $this->pdf->Cell(30,8,'Fecha de Ingreso',0,0,'L');
        $this->pdf->SetFont('');
        $this->pdf->Cell(65,8,": ".utf8_decode($planilla->fingreso),0,0,'L');
        $this->pdf->SetFont('', 'B', '');
        $this->pdf->Cell(30,8,utf8_decode('Cargo del trabajor'),0,0,'L');
        $this->pdf->SetFont('');
        $this->pdf->Cell(65,8,": ".utf8_decode($planilla->cargo),0,0,'L');

        $this->pdf->Ln(5);
        $this->pdf->SetFont('', 'B', '');
        $this->pdf->Cell(30,8,'CUSPP',0,0,'L');
        $this->pdf->SetFont('');
        $this->pdf->Cell(65,8,": ".utf8_decode($planilla->cuspp),0,0,'L');
        $this->pdf->SetFont('', 'B', '');
        $this->pdf->Cell(30,8,'FECHA',0,0,'L');
        $this->pdf->SetFont('');
        $this->pdf->Cell(65,8,": ".utf8_decode(date('d/m/Y')),0,0,'L');

        $this->pdf->Ln();
        $this->pdf->SetFont('', 'B', '');
        $this->pdf->Cell(100,8,'Conceptos',1,0,'C');
        $this->pdf->Cell(30,8,'Ingresos S/.',1,0,'C');
        $this->pdf->Cell(30,8,'Descuentos S/.',1,0,'C');
        $this->pdf->Cell(30,8,'Neto S/.',1,0,'C');
        $this->pdf->Ln();
        $this->pdf->SetFont('');
        $this->pdf->Cell(100,5,utf8_decode('REMUNERACIÓN O JORNAL BÁSICO'),'B,L',0,'L');
        $this->pdf->Cell(30,5,$planilla->basico,'B',0,'R');
        $this->pdf->Cell(30,5,'','B',0,'R');
        $this->pdf->Cell(30,5,'','B,R',0,'R');
        $this->pdf->Ln(5);
        $this->pdf->Cell(100,5,utf8_decode('ASIGNACIÓN FAMILIAR'),'B,L',0,'L');
        $this->pdf->Cell(30,5,$planilla->asigFami,'B',0,'R');
        $this->pdf->Cell(30,5,'','B',0,'R');
        $this->pdf->Cell(30,5,'','B,R',0,'R');
        $this->pdf->Ln(5);
        $this->pdf->Cell(100,5,utf8_decode('OTROS INGRESOS'),'B,L',0,'L');
        $this->pdf->Cell(30,5,$planilla->otros,'B',0,'R');
        $this->pdf->Cell(30,5,'','B',0,'R');
        $this->pdf->Cell(30,5,'','B,R',0,'R');
        $this->pdf->Ln(5);
        $this->pdf->Cell(100,5,utf8_decode('TOTAL DESCUENTOS'),'B,L',0,'L');
        $this->pdf->Cell(30,5,'','B',0,'R');
        $this->pdf->Cell(30,5,$planilla->descTotal,'B',0,'R');
        $this->pdf->Cell(30,5,'','B,R',0,'R');
        $this->pdf->Ln(5);
        $this->pdf->SetFont('', 'B', '');
        $this->pdf->Cell(100,5,utf8_decode('NETO A PAGAR'),'B,L',0,'L');
        $this->pdf->Cell(30,5,'','B',0,'R');
        $this->pdf->Cell(30,5,'','B',0,'R');
        $this->pdf->Cell(30,5,$planilla->neto,'B,R',0,'R');

        $archivo = "boleta de pago".$planilla->nombres.".pdf";
        $this->pdf->Output($archivo,'I');

    }
    public function emp_crear($emp_id = ''){
        if($emp_id != ''){
            $this->db->select("*, DATE_FORMAT(emp_fechaIngreso, '%d/%m/%Y') emp_fechaIngreso");
            $empleado = $this->db->where("emp_id", $emp_id)->get("planilla_empleado")->row();
        }else{
            $empleado =  new StdClass;
            $empleado->emp_id = "";
            $empleado->emp_nombres = "";
            $empleado->emp_paterno = "";
            $empleado->emp_materno = "";
            $empleado->emp_dni = "";
            $empleado->emp_cuspp = "";
            $empleado->emp_fechaIngreso = "";
            $empleado->emp_ocu_id = "";
            $empleado->emp_asigFami = "";
        }

        $datos["emp"] = $empleado;
        $datos["ocupaciones"] = $this->Model_general->getOptions('ocupacion', array("ocu_id", "ocu_descripcion"),'* Ocupaciones');
        $datos["asignacion"] = $this->Model_general->enum_valores('planilla_empleado','emp_asigFami');
        $this->load->view($this->router->fetch_class().'/emp_form', $datos);

    }
    public function plan_guardarEmpPeri(){
        $empleado = $this->input->post("empleado");
        $periodo = $this->input->post("periodo");
        $where = array("plan_emp_id" => $empleado, "plan_peri_id" => $periodo);
        $existe = $this->db->where($where)->get("planilla");

        if($existe->num_rows() > 0){
            $resp["exito"] = false;
            $resp["mensaje"] = "El empleado ya existe en el periodo seleccionado";
        }else{
            $planilla = array("plan_asigFami" => 'NO',
                                "plan_remuBasico" => 0,
                                "plan_remuAsig" => 0,
                                "plan_otros" => 0,
                                "plan_remuTotal" => 0,
                                "plan_descTotal" => 0,
                                "plan_remuNeto" => 0,
                                "plan_salud" => 0,
                                "plan_totalAporte" => 0,
                                "plan_emp_id" => $empleado,
                                "plan_peri_id" => $periodo,
                        );
            if($this->Model_general->guardar_registro("planilla", $planilla) == TRUE){
                $resp["exito"] = true;
                $resp["mensaje"] = "Empleado agregado con exito";
            }else{
                $resp["exito"] = false;
                $resp["mensaje"] = "Ocurrio un error, intentelo mas tarde";
            }
        }
        echo json_encode($resp);
        
    }
    public function emp_guardar($emp_id=''){
        $nombres = $this->input->post("nombres");
        $materno = $this->input->post("materno");
        $paterno = $this->input->post("paterno");
        $dni = $this->input->post("dni");
        $cuspp = $this->input->post("cuspp");
        $fechaIngreso = $this->Model_general->fecha_to_mysql($this->input->post("fechaIngreso"));
        $ocupacion = $this->input->post("ocupacion");
        $empleado = array("emp_nombres" => $nombres,
                            "emp_paterno" => $paterno,
                            "emp_materno" => $materno,
                            "emp_dni" => $dni,
                            "emp_cuspp" => $cuspp,
                            "emp_fechaIngreso" => $fechaIngreso,
                            "emp_ocu_id" => $ocupacion
                    );


        if($emp_id != ""){
            $condicion = array("emp_id" => $emp_id);
            if($this->Model_general->guardar_edit_registro("planilla_empleado", $empleado, $condicion) == TRUE){
                $resp["exito"] = true;
                $resp["mensaje"] = "Empleado actualizado con exito";
            }else{
                $resp["exito"] = false;
                $resp["mensaje"] = "Ocurrio un error";
            }
        }else{
            if($this->Model_general->guardar_registro("planilla_empleado", $empleado) == TRUE){
                $resp["exito"] = true;
                $resp["mensaje"] = "Empleado registrado con exito";
            }else{
                $resp["exito"] = false;
                $resp["mensaje"] = "Ocurrio un error";
            }
        }
        echo json_encode($resp);
    }
    public function plan_listPeriodos() {
        $this->load->helper('Funciones');
        $this->load->library('Ssp');
        $this->load->library('Cssjs');
        $json = isset($_GET['json']) ? $_GET['json'] : false;
        
        $columns = array(
            array('db' => "peri_id",        'dt' => 'ID',       "field" => "peri_id"),
            array('db' => "peri_mesname",   'dt' => 'MES',      "field" => "peri_mesname"),
            array('db' => "peri_anio",      'dt' => 'AÑO',      "field" => "peri_anio"),
            array('db' => "peri_id",        'dt' => 'DT_RowId', "field" => "peri_id")
        );

        if ($json) {

            $json = isset($_GET['json']) ? $_GET['json'] : false;

            $table = 'planilla_periodo';
            $primaryKey = 'peri_id';

            $sql_details = array(
                'user' => $this->db->username,
                'pass' => $this->db->password,
                'db' => $this->db->database,
                'host' => $this->db->hostname
            );

            $condiciones = array();

            $joinQuery = "FROM planilla_periodo";
            $where = "";
            /*
            if (!empty($_POST['moneda']))
                $condiciones[] = "paqu_moneda='".$_POST['moneda']."'";
            if (!empty($_POST['estado']))
            */            
            $where = count($condiciones) > 0 ? implode(' AND ', $condiciones) : "";
            echo json_encode(
                    $this->ssp->simple($_POST, $sql_details, $table, $primaryKey, $columns, $joinQuery, $where)
            );
            exit(0);
        }
        $datos['columns'] = $columns;
        $datos['titulo'] = "Periodos";
        
        $this->cssjs->add_js(base_url().'assets/js/Planilla/peri_listado.js?v=1.0',false,false);
        $this->load->view('header');
        $this->load->view('menu');
        $this->load->view($this->router->fetch_class().'/'.$this->router->fetch_method(), $datos);
        $this->load->view('footer');
    }
    public function peri_crear($peri_id = ''){
        if($peri_id != ''){
            $periodo = $this->db->where("peri_id", $peri_id)->get("planilla_periodo")->row();
        }else{
            $periodo =  new StdClass;
            $periodo->peri_id = "";
            $periodo->peri_mesName = "";
            $periodo->peri_mes = date('m');
            $periodo->peri_anio = date('Y');
        }
        
        $perio = $this->getMesesAnios();
        $datos["peri"] = $periodo;
        $datos["meses"] = $perio["meses"];
        $datos["anios"] = $perio["anios"];
        $this->load->view($this->router->fetch_class().'/peri_form', $datos);

    }
    public function getMesesAnios(){
        $anios[""] = "* Años";
        for ($i=2018; $i <= date('Y')+2; $i++) { 
            $anios[$i] = $i;
        }
        $meses = array(1=>"Enero",2=>"Febrero",3=>"Marzo",4=>"Abril",5=>"Mayo",6=>"Junio",7=>"Julio",8=>"Agosto",9=>"Septiembre",10=>"Octubre",11=>"Niviembre",12=>"Diciembre");
        $resp["anios"] = $anios;
        $resp["meses"] = $meses;
        return $resp;
    }
    public function getEmpPeri(){
        $this->db->select("peri_id, CONCAT(peri_mesName,' - ',peri_anio) as periodo");
        $this->db->order_by("peri_mes","asc");
        $this->db->order_by("peri_anio","asc");
        $peris = $this->db->get("planilla_periodo")->result();
        
        $emps = $this->db->select("emp_id, CONCAT(emp_paterno,' ',emp_materno,', ',emp_nombres) as nombres")->get("planilla_empleado")->result();
        $datos["periodos"] = $this->Model_general->select_options($peris, array("peri_id","periodo"),'* Periodos');
        $datos["empleados"] = $this->Model_general->select_options($emps, array("emp_id","nombres"),'* Empleados');

        return $datos;
    }

    public function peri_guardar($peri_id = ''){
        $mes = $this->input->post('mes');
        $mesName = $this->input->post('mesName');
        $anio = $this->input->post('anio');

        $peri = array("peri_mes" => $mes, "peri_anio" => $anio, "peri_mesName" => $mesName);
        $existe = $this->db->where($peri)->get("planilla_periodo");
        if($existe->num_rows() > 0){
            $resp["exito"] = false;
            $resp["mensaje"] = "El periodo ya existe";
        }else{
            $this->db->trans_begin();
            if($peri_id != ''){
                $where = array("peri_id" => $peri_id);
                if ($this->Model_general->guardar_edit_registro("planilla_periodo", $peri, $where) != TRUE){
                    $resp["exito"] = false;
                    $resp["mensaje"] = "No se pudo guardar el periodo, intentelo mas tarde";
                }else{
                    $this->Model_general->add_log("EDITAR",16,"Edición de período a ".$mesName." ".$anio);
                }
            }else{
                if (($meta = $this->Model_general->guardar_registro("planilla_periodo", $peri)) == TRUE){
                    $this->Model_general->add_log("CREAR",16,"Creación de período ".$mesName." ".$anio);

                    $empleados = $this->db->get("planilla_empleado")->result();
                    foreach ($empleados as $row) {
                        $plan = $this->db->where("plan_emp_id",$row->emp_id)->order_by("plan_id","DESC")->get("planilla");
                        if($plan->num_rows() > 0){
                            $planilla = $plan->row();
                            $planilla->plan_id = NULL;
                            $planilla->plan_peri_id = $meta['id'];
                            $planilla->plan_espagado = 0;
                            $planilla->plan_pagofecha = null;
                            $planilla->plan_pagodesc = null;
                            $planilla->plan_remuTotal = $planilla->plan_remuBasico+$planilla->plan_asigFami;
                            $planilla->plan_otros = 0;
                            $planilla->plan_descTotal = 0;
                            $planilla->plan_remuNeto = $planilla->plan_remuTotal;
                        }else{
                            $planilla = array("plan_asigFami" => 'NO',
                                                "plan_remuBasico" => 0,
                                                "plan_remuAsig" => 0,
                                                "plan_otros" => 0,
                                                "plan_remuTotal" => 0,
                                                "plan_descTotal" => 0,
                                                "plan_remuNeto" => 0,
                                                "plan_salud" => 0,
                                                "plan_totalAporte" => 0,
                                                "plan_emp_id" => $row->emp_id,
                                                "plan_peri_id" => $meta['id'],
                                        );
                        }
                        if ($this->Model_general->guardar_registro("planilla", $planilla) != TRUE){
                            $resp["exito"] = false;
                            $resp["mensaje"] = "Error al guardar datos, intentelo mas tarde";
                            $this->db->trans_rollback();
                            $this->Model_general->dieMsg($resp);
                        }
                    }
                }else{
                    $resp["exito"] = false;
                    $resp["mensaje"] = "No se pudo guardar el periodo, intentelo mas tarde";  
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
    public function plan_listEmpleados() {
        $this->load->helper('Funciones');
        $this->load->library('Ssp');
        $this->load->library('Cssjs');
        $json = isset($_GET['json']) ? $_GET['json'] : false;
        $nombres = "CONCAT(emp_paterno,' ',emp_materno,', ',emp_nombres)";
        $columns = array(
            array('db' => "emp_id",             'dt' => 'ID',           "field" => "emp_id"),
            array('db' => $nombres,             'dt' => 'NOMBRES',      "field" => $nombres),
            array('db' => "emp_dni",            'dt' => 'DNI',          "field" => "emp_dni"),
            array('db' => "emp_cuspp",          'dt' => 'CUSPP',        "field" => "emp_cuspp"),
            array('db' => "emp_fechaIngreso",   'dt' => 'FECHA INGRESO',"field" => "emp_fechaIngreso"),
            array('db' => "ocu_descripcion",    'dt' => 'OCUPACION',    "field" => "ocu_descripcion"),
            array('db' => "emp_id",             'dt' => 'DT_RowId',     "field" => "emp_id")
        );

        if ($json) {

            $json = isset($_GET['json']) ? $_GET['json'] : false;

            $table = 'planilla_empleado';
            $primaryKey = 'emp_id';

            $sql_details = array(
                'user' => $this->db->username,
                'pass' => $this->db->password,
                'db' => $this->db->database,
                'host' => $this->db->hostname
            );

            $condiciones = array();

            $joinQuery = "FROM planilla_empleado LEFT JOIN ocupacion ON ocu_id = emp_ocu_id";
            $where = "";
            /*
            if (!empty($_POST['moneda']))
                $condiciones[] = "paqu_moneda='".$_POST['moneda']."'";
            if (!empty($_POST['estado']))
            */            
            $where = count($condiciones) > 0 ? implode(' AND ', $condiciones) : "";
            echo json_encode(
                    $this->ssp->simple($_POST, $sql_details, $table, $primaryKey, $columns, $joinQuery, $where)
            );
            exit(0);
        }
        $datos['columns'] = $columns;
        $datos['titulo'] = "Empleados";
        
        $this->cssjs->add_js(base_url().'assets/js/Planilla/emp_listado.js?v=1.0',false,false);
        $this->load->view('header');
        $this->load->view('menu');
        $this->load->view($this->router->fetch_class().'/'.$this->router->fetch_method(), $datos);
        $this->load->view('footer');
    }
    public function plan_listOcupaciones(){
        $json = isset($_GET['json']) ? $_GET['json'] : false;
        if ($json) {
            $this->db->from("ocupacion");
            $ocupaciones = $this->db->get()->result();

            $html = "";
            if(COUNT($ocupaciones) > 0){
                foreach ($ocupaciones as $i => $row) {
                    $btn_eliminar = "<a href='".base_url()."Planilla/plan_eliminarOcupacion/".$row->ocu_id."' class='btn btn-danger btn-xs eliminar' type='btn'><i class='glyphicon glyphicon-trash'></i> Eliminar</a>";
                    $btn_editar = "<a href='".base_url()."Planilla/plan_crearOcupacion/".$row->ocu_id."' class='btn btn-success btn-xs editar' type='btn'><i class='glyphicon glyphicon-edit'></i> Editar</a>";

                    $html .= "<tr>";
                    $html .= "<td style='white-space:nowrap'>".$btn_editar." ".$btn_eliminar."</td>";
                    $html .= "<td>".$row->ocu_descripcion."</td>";
                    $html .= "</tr>";
                }
            }else{
                $html = "<tr><th colspan='2' align='center'>No se encontraron registros</th></tr>";
            }

            $datos["html"] = $html;
            echo json_encode($datos);
            exit(0);
        }
        
        $this->load->helper('Funciones');
        $this->load->library('Ssp');
        $this->load->library('Cssjs');
        $datos['titulo'] = "Ocupaciones / Cargos";
        $this->cssjs->add_js(base_url().'assets/js/Planilla/ocu_listado.js?v=1.0',false,false);
        $this->load->view('header');
        $this->load->view('menu');
        $this->load->view($this->router->fetch_class().'/'.$this->router->fetch_method(), $datos);
        $this->load->view('footer');
    }
    public function plan_crearOcupacion($ocu_id = ''){
        if($ocu_id != ''){
            $ocu = $this->db->where("ocu_id", $ocu_id)->get("ocupacion")->row();
        }else{
            $ocu = new StdClass;
            $ocu->ocu_id = "";
            $ocu->ocu_descripcion = "";
        }
        $datos["ocu"] = $ocu;
        $this->load->view($this->router->fetch_class().'/ocu_form', $datos);        
    }
    public function ocu_guardar($ocu_id = ''){
        $descrip = $this->input->post("descrip");
        $ocupacion = array("ocu_descripcion" => $descrip);

        if($ocu_id != ""){
            $condicion = array("ocu_id" => $ocu_id);
            if($this->Model_general->guardar_edit_registro("ocupacion", $ocupacion, $condicion) == TRUE){
                $this->Model_general->add_log("EDITAR",16,"Editado a ".$descrip);
                $resp["exito"] = true;
                $resp["mensaje"] = "ócupación / Cargo actualizado con exito";
            }else{
                $resp["exito"] = false;
                $resp["mensaje"] = "Ocurrio un error";
            }
        }else{
            if($this->Model_general->guardar_registro("ocupacion", $ocupacion) == TRUE){
                $this->Model_general->add_log("CREAR",16,"Crear ocupacion o cargo ".$descrip);
                $resp["exito"] = true;
                $resp["mensaje"] = "ócupación / Cargo registrado con exito";
            }else{
                $resp["exito"] = false;
                $resp["mensaje"] = "Ocurrio un error";
            }
        }
        echo json_encode($resp);                
    }
    public function plan_eliminarOcupacion($ocu_id){
        $ocu = $this->db->where('ocu_id', $ocu_id)->get("ocupacion")->row();
        $existe = $this->db->where('emp_ocu_id', $ocu_id)->get("planilla_empleado");
        if($existe->num_rows() > 0){
            $resp["exito"] = false;
            $resp["mensaje"] = "La ocupacion / cargo que desea eliminar esta registrada en uno o mas empleados";
        }else{
            if($this->Model_general->borrar(array("ocu_id" => $ocu_id), "ocupacion")){
                $this->Model_general->add_log("ELIMINAR",16,"Eliminacion de ocupacion o cargo ".$ocu->ocu_descripcion);
                $resp["exito"] = true;
                $resp["mensaje"] = "Eliminacion exitosa";
            }
        }
        echo json_encode($resp);
    }
}

