<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Ordenserv extends CI_Controller {

    function __construct() {
        parent::__construct();
        if(!$this->session->userdata('authorized')){
            redirect(base_url()."login");
        }
        $this->usua_id = $this->session->userdata('authorized');
        $this->configuracion = $this->db->query("SELECT * FROM usuario WHERE usua_id='{$this->usua_id}'")->row();
            $this->load->model("Model_general");
        $this->permisos  = $this->Model_general->getPermisos($this->usua_id);
        $this->editarop = $this->permisos[3]->nivel_acceso;
        $this->editar = $this->permisos[2]->nivel_acceso;
        $this->load->library('Cssjs');
        $this->load->model("Model_general");
        $this->load->helper('Form');
    }
    public function ord_listado() {
        $this->load->helper('Funciones');
        $this->load->library('Ssp');
        $this->load->library('Cssjs');

        $json = isset($_GET['json']) ? $_GET['json'] : false;

        $file = 'CONCAT("ORD","-",orde_numero)';
        $fecha = 'DATE_FORMAT(orde_fecha, "%d/%m/%Y")';
        $total = '(SELECT SUM(sepr_total) FROM servicio_proveedor WHERE sepr_orde_id = orde_id GROUP BY orde_id)';
        $columns = array(
            array('db' => 'orde_id',        'dt' => 'ID',           "field" => "orde_id"),
            array('db' => $file,            'dt' => 'FILE',         "field" => $file),
            array('db' => $fecha,           'dt' => 'FECHA',        "field" => $fecha),
            array('db' => 'orde_servicio',  'dt' => 'SERVICIO',     "field" => "orde_servicio"),
            array('db' => 'orde_pagado',    'dt' => 'Pago',         "field" => "orde_pagado"),
            array('db' => $total,           'dt' => 'TOTAL',        "field" => $total),
            array('db' => 'orde_observacion','dt' => 'Observaciones',"field" => "orde_observacion"),
            array('db' => 'orde_id',        'dt' => 'DT_RowId',     "field" => "orde_id"),
            array('db' => 'orde_pagado',    'dt' => 'DT_Pagado',    "field" => "orde_pagado"),
            array('db' => $this->editar,    'dt' => 'DT_Permisos',  "field" => $this->editar)
        );

        if ($json) {

            $json = isset($_GET['json']) ? $_GET['json'] : false;

            $table = 'ordenserv';
            $primaryKey = 'orde_id';

            $sql_details = array(
                'user' => $this->db->username,
                'pass' => $this->db->password,
                'db' => $this->db->database,
                'host' => $this->db->hostname
            );

            $condiciones = array();

            $joinQuery = "FROM ordenserv LEFT JOIN ordserv_detalle ON (orde_id = deta_orde_id)";
            $where = "";
            
            if (!empty($_POST['desde'])&&!empty($_POST['hasta'])){
                $condiciones[] = "orde_fecha >='".$_POST['desde']."' AND orde_fecha <='".$_POST['hasta']."'";
            }
            /*
            if (!empty($_POST['usuario']))
                $condiciones[] = "paqu_usua_id='".$_POST['usuario']."'";

            if (!empty($_POST['moneda']))
                $condiciones[] = "paqu_moneda='".$_POST['moneda']."'";

            if (!empty($_POST['estado']))
                $condiciones[] = "paqu_estado='".$_POST['estado']."'";
            */
            
            $where = count($condiciones) > 0 ? implode(' AND ', $condiciones) : "";
            echo json_encode(
                    $this->ssp->simple($_POST, $sql_details, $table, $primaryKey, $columns, $joinQuery, $where, 'orde_id')
            );
            exit(0);
        }
        $datos["comprobantes"] = $this->Model_general->getOptions('usuario', array("usua_id", "usua_nombres"),'* Usuario');
        $datos["moneda"] = array_merge(array(''=>'* Monedas'),$this->Model_general->enum_valores('paquete','paqu_moneda'));
        $datos["estado"] = array_merge(array(''=>'* Estado'),$this->Model_general->enum_valores('paquete','paqu_estado'));
        $datos['columns'] = $columns;
        $datos['poststr'] = isset($_SESSION['poststr'])?unserialize($_SESSION['poststr']):array(
            'usuario'=>'',
            'contacto'=>'',
            'scontacto'=>'',
            'estado'=>'',
            'usuario'=>'',
            'det_orde'=>'',
            'det_comp'=>'',
            'det_liqu'=>'',
            'search'=>array('value'=>''),
            'tipo'=>$usua_tipo,
            'serv_ids'=>'',
            'desde'=>date('Y-m-d',time()-24*60*60*7),
            'hasta'=>date('Y-m-d'),
        );

        $this->cssjs->add_js(base_url().'assets/js/Ordenserv/list_ord.js?v=1.0',false,false);
        $this->cssjs->add_js(base_url().'assets/js/calendar.js',false,false);
        $this->load->view('header');
        $this->load->view('menu');
        $this->load->view($this->router->fetch_class().'/'.$this->router->fetch_method(), $datos);
        $this->load->view('footer');
    }
    public function ordp_listadoPago() {
        $this->load->helper('Funciones');
        $this->load->library('Ssp');
        $this->load->library('Cssjs');

        $json = isset($_GET['json']) ? $_GET['json'] : false;

        $file = 'CONCAT("OP","-",orde_numero)';
        $fecha = 'DATE_FORMAT(orde_fechareg, "%d/%m/%Y")';
        $total = 'FORMAT(orde_total,2)';
		$tipo = 'IF(orde_flota = "1","<b>UNID","<b>PROV")';
        $estado = "IF(orde_espagado = '0','<font class=red><strong>PENDIENTE','<font class=green><b>PAGADO')";
        $columns = array(
            array('db' => 'orde_id',        'dt' => 'ID',           "field" => "orde_id"),
            array('db' => $file,            'dt' => 'FILE',         "field" => $file),
            array('db' => $fecha,           'dt' => 'FECHA',        "field" => $fecha),
            array('db' => 'orde_prov_name', 'dt' => 'PROVEEDOR',    "field" => "orde_prov_name"),
            array('db' => $estado,          'dt' => 'ESTADO',       "field" => $estado),
            array('db' => $total,           'dt' => 'TOTAL',        "field" => $total),
			array('db' => $tipo,            'dt' => 'TIPO',         "field" => $tipo),
			array('db' => 'orde_fechapago', 'dt' => 'PAGO FECHA',   "field" => 'orde_fechapago'),
			array('db' => 'orde_pagodesc',  'dt' => 'PAGO OBS',     "field" => 'orde_pagodesc'),
            array('db' => 'orde_id',        'dt' => 'DT_RowId',     "field" => "orde_id"),
            array('db' => 'orde_espagado',  'dt' => 'DT_esPagado',  "field" => "orde_espagado"),
            array('db' => 'orde_pagado',  	'dt' => 'DT_Pagado',    "field" => "orde_pagado"),
            array('db' => $this->editarop,  'dt' => 'DT_Permisos',  "field" => $this->editarop),
            array('db' =>  $this->usua_id,  'dt' => 'DT_UsuaId',    "field" => $this->usua_id)
        );

        if ($json) {

            $json = isset($_GET['json']) ? $_GET['json'] : false;

            $table = 'ordenpago';
            $primaryKey = 'orde_id';

            $sql_details = array(
                'user' => $this->db->username,
                'pass' => $this->db->password,
                'db' => $this->db->database,
                'host' => $this->db->hostname
            );

            $condiciones = array();

            $joinQuery = "FROM ordenpago";
            $where = "";
            
            if (!empty($_POST['desde'])&&!empty($_POST['hasta'])){
                $condiciones[] = "orde_fechareg >='".$_POST['desde']."' AND orde_fechareg <='".$_POST['hasta']."'";
            }
            /*
            if (!empty($_POST['usuario']))
                $condiciones[] = "paqu_usua_id='".$_POST['usuario']."'";
            */
			if (!empty($_POST['moneda']))
                $condiciones[] = "orde_moneda='".$_POST['moneda']."'";
            if (!empty($_POST['estado'])){
                $est = $_POST['estado'] == 'PA' ? '1' : '0';
                $condiciones[] = "orde_espagado='".$est."'";
            }
            
            $where = count($condiciones) > 0 ? implode(' AND ', $condiciones) : "";
            echo json_encode(
                    $this->ssp->simple($_POST, $sql_details, $table, $primaryKey, $columns, $joinQuery, $where, 'orde_id')
            );
            exit(0);
        }
        $datos["moneda"] = array_merge(array(''=>'* Monedas'),$this->Model_general->enum_valores('paquete','paqu_moneda'));
        $datos['columns'] = $columns;
        $datos['poststr'] = isset($_SESSION['poststr'])?unserialize($_SESSION['poststr']):array(
            'usuario'=>'',
            'contacto'=>'',
            'scontacto'=>'',
            'estado'=>'',
            'usuario'=>'',
            'det_orde'=>'',
            'det_comp'=>'',
            'det_liqu'=>'',
            'search'=>array('value'=>''),
            'tipo'=>$usua_tipo,
            'serv_ids'=>'',
            'desde'=>date('Y-m-d',time()-24*60*60*7),
            'hasta'=>date('Y-m-d'),
        );

        $this->cssjs->add_js(base_url().'assets/js/Ordenserv/list_ordPago.js?v=1.2',false,false);
        $this->cssjs->add_js(base_url().'assets/js/calendar.js',false,false);
        $this->load->view('header');
        $this->load->view('menu');
        $this->load->view($this->router->fetch_class().'/'.$this->router->fetch_method(), $datos);
        $this->load->view('footer');
    }

    public function nextnum(){
        $this->db->select('MAX(orde_numero) as max');
        $this->db->from('ordenserv');
        $query = $this->db->get();    
        $row = $query->row();
        $numero = $row->max+1;
        return $numero;
    }
    public function ord_gen($id_orden = ''){

        if($id_orden != ''){
            /*
            $detas = $this->Model_general->getData('ordserv_detalle', array("deta_id","deta_servicio", "deta_orde_id", "DATE_FORMAT(deta_fecha, '%d/%m/%Y') as deta_fecha", "DATE_FORMAT(deta_fecha, '%h:%i %p')  as deta_hora", "deta_pdet_id", "deta_file", "deta_pax", "deta_nombres", "deta_hotel", "deta_hora", "deta_lunch" ,"deta_contacto", "deta_endose", "deta_obs"), array("deta_orde_id" => $id_orden));
            */
            $this->db->select("O.deta_id, O.deta_servicio, O.deta_orde_id, DATE_FORMAT(O.deta_fecha, '%d/%m/%Y') as deta_fecha, DATE_FORMAT(O.deta_fecha, '%h:%i %p')  as deta_hora, O.deta_pdet_id, O.deta_file, O.deta_pax, O.deta_nombres, O.deta_hotel, O.deta_hora, O.deta_lunch ,O.deta_contacto, O.deta_endose, O.deta_obs, P.deta_guia");
            $this->db->from("ordserv_detalle as O");
            $this->db->join("paquete_detalle as P", "P.deta_id = O.deta_pdet_id", "LEFT");
            $this->db->where("O.deta_orde_id", $id_orden);
            $detas = $this->db->get()->result();
            $orden = $this->db->select("orde_id, orde_paqu_id, orde_observacion, orde_serv_id, orde_servicio, orde_estado, orde_pagado, orde_fechareg, orde_numero, orde_moneda, orde_total, DATE_FORMAT(orde_fecha, '%d/%m/%Y') as orde_fecha")->where("orde_id", $id_orden)->get('ordenserv')->row();
            $adic = $this->Model_general->getOrdenAdicionales($id_orden);
        }else{
            $seleccionados = $this->input->get('sel');
            /*
            $paquetes = $this->db->query("SELECT * FROM paquete_detalle JOIN paquete ON paqu_id = deta_paqu_id JOIN cliente ON clie_id = paqu_clie_id JOIN servicio ON deta_serv_id = serv_id WHERE deta_id IN (".$seleccionados.") ORDER BY 'deta_serv_id' ASC")->result();
            */
            $this->db->from("paquete_detalle");
            $this->db->join("paquete", "paqu_id = deta_paqu_id");
            $this->db->join("cliente", "clie_id = paqu_clie_id");
            $this->db->join("servicio", "deta_serv_id = serv_id");
            $this->db->where_in("deta_id", explode(",",$seleccionados));
            //$this->db->order_by("deta_serv_id");
            $this->db->order_by("deta_fechaserv");
            $paquetes = $this->db->get()->result();

            $this->db->select("DATE_FORMAT(paqu_fecha, '%d/%m/%Y') as fecha, paqu_id, CONCAT(paqu_prefijo,'-',paqu_numero) as file");
            $this->db->where("paqu_id", $paquetes[0]->deta_paqu_id);
            $paquete = $this->db->get("paquete")->row();

            $detas = array();
            $adic = array();
            $total = 0;

            foreach ($paquetes as $i => $val) {
                //$adic[] = $this->Model_general->getOrdenAdicionales($val->deta_id,true);
                $adicionales = $this->Model_general->getOrdenAdicionales($val->deta_id,true);
                if($adicionales) $adic = array_merge($adic, $adicionales);
                $hora = explode(" ", $val->deta_fechaserv);
                $hres = date('h:i A', strtotime($val->deta_fechaserv));
                $dt = new stdClass();
                $dt->deta_id = '';
                $dt->deta_pdet_id = $val->deta_id;
                $dt->deta_fecha = date('d/m/Y', strtotime($val->deta_fechaserv));
                $dt->deta_file = $val->paqu_prefijo."-".$val->paqu_numero;
                $dt->deta_pax = $val->deta_pax;
                $dt->deta_nombres = $val->paqu_nombre;
                $dt->deta_guia = $val->deta_guia;
                $dt->deta_servicio = $val->serv_abrev;
                $dt->deta_hotel = $val->deta_hotel;
                //$dt->deta_hora = date('h:i A', strtotime($val->deta_fechaserv));
                $dt->deta_hora = ($hora[1] != '00:00:00'?$hres:'');
                $dt->deta_lunch = $val->deta_lunch;
                $dt->deta_contacto = $val->clie_rsocial;
                $dt->deta_endose = $val->paqu_endose;
                $dt->deta_obs = $val->deta_descripcion;
                $detas[$i] = $dt;
                $total += $val->paqu_total;
            }

            $detas = (object)$detas;

            $serv_nombre = $this->db->select("serv_descripcion as serv")->where("serv_id", $paquetes[0]->deta_serv_id)->get("servicio")->row()->serv;
            $orden = new stdClass();
            $orden->orde_id = '';
            $orden->orde_fecha = date('d/m/Y');
            $orden->orde_serv_id = $paquetes[0]->deta_serv_id;
            $orden->orde_servicio = $serv_nombre;
            $orden->orde_moneda = 'SOLES';
            $orden->orde_observacion = '';
            $orden->orde_paqu_id = $paquete->paqu_id;
            $orden->orde_fechareg = date('d/m/Y');
            $orden->orde_numero = $this->nextnum();
            $orden->orde_total = $total;
        }
        $datos["monedas"] = $this->Model_general->enum_valores('servicio_proveedor','sepr_moneda');
        //$datos["estados"] = $this->Model_general->enum_valores('ordv_serv_adicional','ordv_adic_pagado');
        $datos["estados"] = array("0" => "PENDIENTE", "1" => "PAGADO", "2" => "DEPOSITO");
        $datos["detas"] = $detas;
        $datos["orden"] = $orden;
        $datos["adic"] = json_encode($adic); 
        $datos["titulo"] = "Generar orden de servicio";

        $this->load->helper('Funciones');
        $this->load->model("Model_general");
        $this->load->library('Ssp');
        $this->load->library('Cssjs');
        $this->cssjs->add_js(base_url().'assets/js/Ordenserv/form_genOrden.js',false,false);
        $script['js'] = $this->cssjs->generate_js();
        $this->load->view('header', $script);
        $this->load->view('menu');
        $this->load->view($this->router->fetch_class().'/'.$this->router->fetch_method(), $datos);
        $this->load->view('footer');
        
    }
    public function orde_validar(){
        $this->load->library('Form_validation');
        $this->form_validation->set_rules('tservicio', 'Servicio', 'required');
        $this->form_validation->set_rules('proveedor', 'Proveedor', 'required');
        $this->form_validation->set_rules('adicional_cant', 'Cantidad', 'required');
        $this->form_validation->set_rules('adicional_precio', 'Costo', 'required');
        //$this->form_validation->set_rules('deta_nombres[]', 'Nombre del pasajero / Grupo', 'required');
        //$this->form_validation->set_rules('deta_pax[]', 'Pax', 'required');
        if ($this->form_validation->run() == FALSE){        
            $this->Model_general->dieMsg(array('exito'=>false,'mensaje'=>validation_errors()));
        }
    }
    public function guardar_orden($id=''){
        //$this->orde_validar();
         
        $numero = $this->input->post('numero');
        $fecha_emi = $this->Model_general->fecha_to_mysql($this->input->post('fecha_emi'));
        $servicio = $this->input->post('serv_name');
        $serv_id = $this->input->post('serv_id');
        $paqu_id = $this->input->post('paqu_id');
        $total= $this->input->post('total_total');
        $obs= $this->input->post('observacion');

        $datos = array("orde_fecha" => $fecha_emi,
            "orde_servicio" => $servicio,
            "orde_serv_id" => $serv_id,
            "orde_numero" => $numero,
            "orde_usua" => $this->usua_id,
            "orde_paqu_id" => $paqu_id,
            "orde_total" => $total,
            "orde_observacion" => $obs
        );

        $adic_tservicio = $this->input->post('tservicio');
        $adic_proveedor = $this->input->post('proveedor');
        $adic_cant = $this->input->post('adicional_cant');
        $adic_precio = $this->input->post('adicional_precio');
        $adic_deta = $this->input->post('adicional_deta');
        $adic_id = $this->input->post('adic_id');
        $adic_estado = $this->input->post('estado');
        $adic_moneda = $this->input->post('moneda');
        $adic_guia = $this->input->post('add_guia');
        $adic_fecha = $this->input->post('add_fecha');
        $adic_hora = $this->input->post('add_hora');
        
        //time_to_mysql        

        $paqu_deta_id = $this->input->post('paqu_deta_id');
        $this->db->trans_begin();
        if(empty($id)){
            $pendientes = array("orde_estado" => "PENDIENTE", "orde_pagado" => "PENDIENTE","orde_fechareg" => date('Y-m-d H:i:s'));
            $datos = array_merge($datos, $pendientes);
            if (($meta = $this->Model_general->guardar_registro("ordenserv", $datos)) == TRUE):

                $this->db->where('sepr_orde_id',$meta["id"]);
                $this->db->where_not_in('sepr_id',$adic_id);
                $this->db->delete('servicio_proveedor');

                $referencia = '';
                for ($i=0; $i < count((array)$paqu_deta_id); $i++) { 

                    $paqu = $this->db->query("SELECT * FROM paquete_detalle as PD JOIN paquete as P ON paqu_id = deta_paqu_id JOIN cliente as C ON clie_id = paqu_clie_id JOIN servicio as S ON deta_serv_id = serv_id WHERE deta_id = {$paqu_deta_id[$i]}")->row();
                    
                    $espacio = ($i == 0?'':', ');
                    if($paqu->paqu_tipo == "LOCAL"){
                        $referencia .= $espacio."TL-".$paqu->paqu_numero;
                        $file = "TL-".$paqu->paqu_numero;
                    }else if($paqu->paqu_tipo == "RECEPTIVO"){
                        $referencia .= $espacio."TR-".$paqu->paqu_numero;
                        $file = "TR-".$paqu->paqu_numero;
                    }else{
                        $referencia .= $espacio."P-".$paqu->paqu_numero;
                        $file = "P-".$paqu->paqu_numero;
                    }
                    
                    $item = array("deta_orde_id" => $meta['id'],
                                "deta_pdet_id" => $paqu->deta_id,
                                "deta_fecha" => date('Y-m-d', strtotime($paqu->deta_fechaserv)),
                                "deta_file" => $file,
                                "deta_pax" => $paqu->deta_pax,
                                "deta_nombres" => $paqu->paqu_nombre,
                                "deta_hotel" => $paqu->deta_hotel,
                                "deta_hora" => $this->Model_general->time_to_mysql($paqu->deta_fechaserv),
                                "deta_lunch" => $paqu->deta_lunch,
                                "deta_contacto" => $paqu->clie_rcomercial,
                                "deta_endose" => $paqu->paqu_endose,
                                "deta_obs" => $paqu->deta_descripcion,
                                "deta_servicio" => $paqu->serv_abrev,
                                "deta_serv_id" => $paqu->serv_id
                    );
                    if($this->Model_general->guardar_registro("ordserv_detalle", $item)==FALSE){
                        $this->db->trans_rollback();
                        $this->Model_general->dieMsg(array('exito'=>false,'mensaje'=>'Error al guardar los datos'));
                    }else{
                        //$this->actualizaPaquDeta($paqu->deta_id, '1');
                        $orden = array("deta_esorden" => 1);
                        if($this->Model_general->guardar_edit_registro("paquete_detalle", $orden, array("deta_id" => $paqu->deta_id))==FALSE){
                            $this->db->trans_rollback();
                            $this->Model_general->dieMsg(array('exito'=>false,'mensaje'=>'Error al guardar los datos'));
                        }
                    }
                }
                $this->Model_general->guardar_edit_registro("ordenserv", array("orde_referencia" => $referencia), array("orde_id" => $meta['id']));
                for ($i=0; $i < count((array)$adic_deta); $i++) { 
                    
                    $item = array("sepr_orde_id" => $meta['id'],
                                "sepr_tipo" => $adic_tservicio[$i],
                                "sepr_servicio" => $adic_deta[$i],
                                "sepr_precio" => $adic_precio[$i],
                                "sepr_moneda" => $adic_moneda[$i],
                                "sepr_total" => $adic_cant[$i] * $adic_precio[$i],
                                "sepr_prov_id" => $adic_proveedor[$i],
                                "sepr_cantidad" => $adic_cant[$i],
                                "sepr_guia" => $adic_guia[$i],
                                "sepr_fecha" => $this->Model_general->fecha_to_mysql($adic_fecha[$i]),
                                "sepr_hora" => $this->Model_general->time_to_mysql($adic_hora[$i])
                        );
                    if($this->verificaTipoProveedor($adic_tservicio[$i], $adic_proveedor[$i])){
						if(empty($adic_id[$i])){
							if(!$this->Model_general->guardar_registro("servicio_proveedor", $item)){
								$this->db->trans_rollback();
								$this->Model_general->dieMsg(array('exito'=>false,'mensaje'=>'Error al guardar los datos'));
							}
						}else{
							$cndc = array("sepr_id" => $adic_id[$i]);
							if(!$this->Model_general->guardar_edit_registro("servicio_proveedor", $item, $cndc)){
								$this->db->trans_rollback();
								$this->Model_general->dieMsg(array('exito'=>false,'mensaje'=>'Error al guardar los datos'));
							}else{
								//$this->actualizaServProv($)
							}
						}
					}else{
						$this->db->trans_rollback();
						$this->Model_general->dieMsg(array('exito'=>false,'mensaje'=>'Uno o más proveedores no pertenesen a los servicios seleccionados'));
					}
                    
                }
                //$this->verificaPagoOrden($meta['id']);
                $this->Model_general->add_log("CREAR",3,"Creación de Orden de servicio ORD-".str_pad($numero,8,"0", STR_PAD_LEFT));
            else:
                $this->db->trans_rollback();
                $this->Model_general->dieMsg(array('exito'=>false,'mensaje'=>'Error al guardar los datos'));
            endif;
            $this->db->trans_commit();
            $id = $meta['id'];           
        }else{
            $condicion = "orde_id = ".$id;

            if ($this->Model_general->guardar_edit_registro("ordenserv", $datos, $condicion) == TRUE):
                
                // ELIMINA LOS DETALLES

                $this->db->where('sepr_orde_id',$id);
                $this->db->where_not_in('sepr_id',$adic_id);
                $this->db->delete('servicio_proveedor');
                //////////////////////////////////////////////////////

                for ($i=0; $i < count((array)$adic_deta); $i++) { 
                    $condicion_items = "sepr_id = ".$adic_id[$i];
                    $item = array("sepr_orde_id" => $id,
                                "sepr_tipo" => $adic_tservicio[$i],
                                "sepr_servicio" => $adic_deta[$i],
                                "sepr_precio" => $adic_precio[$i],
                                "sepr_moneda" => $adic_moneda[$i],
                                "sepr_total" => $adic_cant[$i] * $adic_precio[$i],
                                "sepr_prov_id" => $adic_proveedor[$i],
                                "sepr_cantidad" => $adic_cant[$i],
                                "sepr_guia" => $adic_guia[$i],
                                "sepr_fecha" => $this->Model_general->fecha_to_mysql($adic_fecha[$i]),
                                "sepr_hora" => $this->Model_general->time_to_mysql($adic_hora[$i])
                        );
					if($this->verificaTipoProveedor($adic_tservicio[$i], $adic_proveedor[$i])){
						if(empty($adic_id[$i])){
							if($this->Model_general->guardar_registro("servicio_proveedor", $item) == false){
								$this->db->trans_rollback();
								$this->Model_general->dieMsg(array('exito'=>false,'mensaje'=>'Error al guardar los datos'));
							}
						}else{
							if($this->Model_general->guardar_edit_registro("servicio_proveedor", $item, $condicion_items) == false){
								$this->db->trans_rollback();
								$this->Model_general->dieMsg(array('exito'=>false,'mensaje'=>'Error al guardar los datos'));
							}
						}   
					}else{
						$this->db->trans_rollback();
						$this->Model_general->dieMsg(array('exito'=>false,'mensaje'=>'Uno o más proveedores no pertenesen a los servicios seleccionados'));
					}
                    
                }
                $this->Model_general->add_log("EDITAR",3,"Edición de Orden de servicio ORD-".str_pad($numero,8,"0", STR_PAD_LEFT));
            else:
               $this->Model_general->dieMsg(array('exito'=>false,'mensaje'=>'Error al guardar los datos'));
            endif;
        }
        $this->db->trans_commit();
        
        $this->Model_general->dieMsg(array('url'=> base_url()."Ordenserv/ord_listado", 'exito'=>true,'mensaje'=>'Datos guardados con exito','id'=>$id));
    }
	public function verificaTipoProveedor($servicio, $proveedor){
		$this->db->where("pprov_id", $proveedor);
		$this->db->where("pserv_id", $servicio);
		$consulta = $this->db->get("provserv");
		if($consulta->num_rows() > 0)
			return true;
		else
			return false;
	}
    public function actualizaPaquDeta($deta_id, $estado){
        $this->Model_general->guardar_edit_registro("paquete_detalle", array("deta_esorden" => $estado), array("deta_id" => $deta_id));
    }
    /*
    public function verificaPagoOrden($id){
        $this->db->select("COUNT(sepr_id) as total");
        $this->db->where("sepr_orde_id", $id);
        $total = $this->db->get("servicio_proveedor")->row()->total;

        $this->db->select("COUNT(sepr_id) as pagado");
        $this->db->where("sepr_estado", '1');
        $this->db->where("sepr_orde_id", $id);
        $pagado = $this->db->get("servicio_proveedor")->row()->pagado;

        if($total == $pagado){
            $this->Model_general->guardar_edit_registro("ordenserv", array("orde_pagado" => 'PAGADO'), array('orde_id' => $id));
        }
    }
    */
    public function cambiarEstadoServicioProv($id,$desc,$fecha, $estado = 1){
        /*
        $detas = $this->db->where("deta_orde_id", $id)->get("ordenpago_detalle")->result();
        foreach ($detas as $i => $det) {
            $this->db->query("UPDATE servicio_proveedor SET sepr_espagado = '1', sepr_pagado = sepr_total, sepr_pagodesc = '{$desc}', sepr_pagofecha = '{$fecha}' WHERE sepr_id = {$det->deta_sepr_id}");
        } 
        */
        if($estado == 0){
            $this->db->query("UPDATE servicio_proveedor SET sepr_espagado = {$estado}, sepr_pagado = 0, sepr_pagodesc = null, sepr_pagofecha = null WHERE sepr_id IN (SELECT deta_sepr_id FROM ordenpago_detalle WHERE deta_orde_id = {$id})");
        }else{
            $this->db->query("UPDATE servicio_proveedor SET sepr_espagado = {$estado}, sepr_pagado = sepr_total, sepr_pagodesc = '{$desc}', sepr_pagofecha = '{$fecha}' WHERE sepr_id IN (SELECT deta_sepr_id FROM ordenpago_detalle WHERE deta_orde_id = {$id})");
        }
        
    }
    public function guardar_pago($id = ''){

        $moneda = $this->input->post('moneda');
        $documento = $this->input->post('documento');
        $serie = $this->input->post('serie');
        $numero = $this->input->post('numero');
        $total = $this->input->post('total');
        $cancelado = $this->input->post('cancelado');
        $pagado = $this->input->post('pagado');
        $saldo = $this->input->post('saldo');
        $obs = $this->input->post('observacion');

        $cuenta = $this->input->post('cuenta');
        $codigo_cuen = $this->input->post('codigo_cuen');
        
        $orden = $this->db->where("orde_id", $id)->get("ordenpago")->row();
        if($orden->orde_espagado == 1){
            $json['exito'] = false;
            $json['mensaje'] = "La orden ya esta pagada";
        }else{
            $this->db->trans_start();

            if($pagado != '' && $pagado > 0 && $cuenta != 0 && $codigo_cuen != ""){

                $this->Model_general->actualizarCaja(6, "SALIDA", $documento, $serie, $numero, 'Pago de: OP-'.$orden->orde_numero, $pagado,$moneda, $this->usua_id, $id, '', $cuenta,$codigo_cuen,'',$obs);

                $dte = array("orde_pagado" => ($cancelado + $pagado), "orde_fechapago" => date("Y-m-d"));
                if(($cancelado + $pagado) >= $total){
                    $dte["orde_espagado"] = '1';
                    $this->cambiarEstadoServicioProv($id, 'Pagado en de: OP-'.$orden->orde_numero, date("Y-m-d"));
                }
                $desc = ($orden->orde_pagodesc != "") ? $orden->orde_pagodesc." | ".$obs : $obs;
                $dte["orde_pagodesc"] = $desc;
                $this->Model_general->guardar_edit_registro("ordenpago", $dte, array('orde_id' => $id));
            }
            $this->Model_general->add_log("PAGO",4,"Pago de la orden de pago OP-".$orden->orde_numero." ".$pagado." ".$moneda." / ".$orden->orde_prov_name);
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE){
                $json['exito'] = false;
                $json['mensaje'] = "Error al guardar los datos";
            }else{
                $json['exito'] = true;  
                $json['mensaje'] = "Datos guardados con exito";
            }
        }
        echo json_encode($json);
    }
	public function ord_anularPago($id = ""){
        $orden = $this->db->where("orde_id", $id)->get("ordenpago")->row();
        $mov = $this->db->where(array("movi_ref_id" => $id, "movi_tipo_id" => 6))->get("cuenta_movimiento")->row();
        if($orden->orde_pagado == "0"){
            $resp["exito"] = false;
            $resp["mensaje"] = "No hay pagos registrados";
        }else{
            $this->db->trans_start();

            $this->Model_general->actualizarCaja(6, 'INGRESO', "", "", "", $orden->orde_prov_name." / ".$orden->orde_fechapago, $orden->orde_pagado, $orden->orde_moneda, $this->usua_id, $id, '', $mov->movi_cuen_id,"000000",date("Y-m-d"),"pago anulado");

            $dte = array("orde_espagado" => '0', "orde_pagado" => "0", "orde_fechapago" => NULL, "orde_pagodesc" => null);
            $this->Model_general->guardar_edit_registro("ordenpago", $dte, array('orde_id' => $id));
            $this->cambiarEstadoServicioProv($id, '', '',0);
            $this->Model_general->guardar_edit_registro("cuenta_movimiento", array("movi_file" => ""), array('movi_id' => $mov->movi_id));

            $this->Model_general->add_log("PAGO",4,"Anulación de pago ".$orden->orde_prov_name." ".$orden->orde_pagado." ".$orden->orde_moneda.", Código de caja: ANULADO");

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
    public function ordPdf($id){
        $datos['id'] = $id;
        $this->load->view('Ordenserv/verOrden',$datos);
    }
    public function ordPagoPdf($id){
        $datos['id'] = $id;
        $this->load->view('Ordenserv/verOrdenPago',$datos);
    }
    public function genera_ordenPDF($orde_id = 0, $file=false){

        $this->db->select("orde_servicio as servicio, DATE_FORMAT(orde_fecha, '%d/%m/%Y') as fecha, orde_observacion as obs, orde_usua");
        $this->db->from('ordenserv');
        $this->db->where("orde_id", $orde_id);
        $orden = $this->db->get()->row();

        $detalle = $this->Model_general->getDetaOrd($orde_id);
        $adicionales = $this->Model_general->getOrdenAdicionales($orde_id);
        
        $user = explode(" ",$this->Model_general->getUsuarios($orden->orde_usua)->nombres);

        $this->load->library('pdf');
        $this->pdf = new Pdf();
        $this->pdf->Orientation('L');
        $this->pdf->AddPage();
        $this->pdf->AliasNbPages();
        $this->pdf->SetTitle("ORDEN DE SERVICIO");
        $this->pdf->set_user($user[0]);

        $this->pdf->SetLeftMargin(10);
        
        $this->pdf->SetFont('Arial', 'B', 20);

        $this->pdf->Cell(139,8,utf8_decode($orden->servicio),0,0,'L');
        $this->pdf->SetFont('', '', 7);
        $this->pdf->Cell(139,8,utf8_decode($orden->fecha),0,0,'R');
        $header = array('FECHA', 'FILE','SERV', 'PAX', 'NOMBRES / GUIA', 'HOTEL', 'LUNCH', 'CONTACTO', 'ENDOSE', 'Observaciones');
        $w =      array(15,       15,  10, 10,    55,        49,      10,      47,         30,       35);
        $this->pdf->Ln(8);
        $this->pdf->SetFont('','B','');
        $this->pdf->SetFillColor('70','130','180'); 
        $this->pdf->SetTextColor(255);
        for($i = 0; $i < count($header); $i++)
            $this->pdf->Cell($w[$i],6,utf8_decode($header[$i]),0,0,'C',true);
        $this->pdf->Ln();
        $this->pdf->SetFont('');
        $this->pdf->SetTextColor(0);
        $indice = 0;
        $lineas = 0;
        $total_pax = 0;
        $total_lunch = 0;
        foreach ($detalle as $num => $det) {
            $total_pax += $det->deta_pax;
            $total_lunch += $det->deta_lunch;

            $numero = 0;
            preg_match_all("/.{1,15}[^ ]*/",$det->deta_obs,$arra);
            $det->deta_obs = implode("\r\n",$arra[0]);

            $hline = 7;
            $dess = array();
            
            if(preg_match("/\n/",$det->deta_obs)){ ///  para saltos de linea
                $dess = explode("\n",$det->deta_obs);
                $det->deta_obs = $dess[0];
                $hline = 3;
                $this->pdf->Ln(2);
            }

            $this->pdf->Cell($w[0],$hline,$det->deta_fecha,'',0,'C');
            $this->pdf->Cell($w[1],$hline,$det->deta_file,'',0,'C');
            $this->pdf->Cell($w[2],$hline,$det->deta_pax,'',0,'C');
            $this->pdf->Cell($w[3],$hline,$det->deta_servicio,'',0,'C');
            $this->pdf->Cell($w[4],$hline,utf8_decode($det->deta_nombres),'',0,'L');
            $this->pdf->Cell($w[5],$hline,utf8_decode($det->deta_hotel),'',0,'L');
            //$this->pdf->Cell($w[5],$hline,($det->deta_hora),'',0,'C');
            $this->pdf->Cell($w[6],$hline,($det->deta_lunch),'',0,'C');
            $this->pdf->Cell($w[7],$hline,($det->deta_contacto),'',0,'L');
            $this->pdf->Cell($w[8],$hline,($det->deta_endose),'',0,'L');
            $this->pdf->Cell($w[9],$hline,($det->deta_obs),'',0,'L');
            $this->pdf->Ln();
            $lineas++;
            
            if(count($dess)>0){
                unset($dess[0]);
                foreach($dess as $desc){
                    $this->pdf->Cell($w[0],$hline,'','',0,'C');
                    $this->pdf->Cell($w[1],$hline,'','',0,'C');
                    $this->pdf->Cell($w[2],$hline,'','',0,'C');
                    $this->pdf->Cell($w[3],$hline,'','',0,'L');
                    $this->pdf->Cell($w[4],$hline,'','',0,'C');
                    $this->pdf->Cell($w[5],$hline,'','',0,'C');
                    $this->pdf->Cell($w[6],$hline,'','',0,'C');
                    $this->pdf->Cell($w[7],$hline,'','',0,'C');
                    $this->pdf->Cell($w[8],$hline,'','',0,'C');
                    $this->pdf->Cell($w[9],$hline,utf8_decode($desc),'',0,'L');
                    $this->pdf->Ln();
                    $lineas++;
                }
                $this->pdf->Ln(2);
             }
            $this->pdf->line(10,$this->pdf->GetY(),286,$this->pdf->GetY());
            $indice++;
        }
        $this->pdf->SetFont('','B','');
        $this->pdf->Cell($w[0],7,'TOTAL','',0,'C');
        $this->pdf->Cell($w[1],7,'','',0,'C');
        $this->pdf->Cell($w[2],7,$total_pax,'',0,'C');
        $this->pdf->Cell($w[3],7,'','',0,'L');
        $this->pdf->Cell($w[4],7,'','',0,'L');
        $this->pdf->Cell($w[5],7,$total_lunch,'',0,'C');
        $this->pdf->Cell($w[6],7,'','',0,'L');
        $this->pdf->Cell($w[7],7,'','',0,'L');
        $this->pdf->Cell($w[8],7,'','',0,'L');
        $this->pdf->Ln();

        $this->pdf->Cell(30,8,'Servicios Adicionales',0,0,'L');
        $header = array('Servico', 'Proveedor', 'Descripción', 'Moneda', 'Cant', 'Precio', 'Total');
        $w = array(30, 30, 60, 10, 20, 15, 15);
        $this->pdf->Ln(8);
        $this->pdf->SetFont('','B','');
        $this->pdf->SetFillColor('70','130','180'); 
        $this->pdf->SetTextColor(255);
        for($i = 0; $i < count($header); $i++)
            $this->pdf->Cell($w[$i],6,utf8_decode($header[$i]),0,0,'C',true);
        $this->pdf->Ln();
        $this->pdf->SetFont('');
        $this->pdf->SetTextColor(0);
        $indice = 0;
        $lineas = 0;
        
        if($adicionales != ""):
        foreach ($adicionales as $num => $det) {
            $numero = 0;
            preg_match_all("/.{1,30}[^ ]*/",$det->sepr_servicio,$arra);
            $det->sepr_servicio = implode("\r\n",$arra[0]);

            $hline = 7;
            $dess = array();
            
            if(preg_match("/\n/",$det->sepr_servicio)){ ///  para saltos de linea
                $dess = explode("\n",$det->sepr_servicio);
                $det->sepr_servicio = $dess[0];
                $hline = 3;
                $this->pdf->Ln(2);
            }

            $this->pdf->Cell($w[0],$hline,$det->tipo_denom,'',0,'L');
            $this->pdf->Cell($w[1],$hline,$det->prov_rsocial,'',0,'L');
            $this->pdf->Cell($w[2],$hline,utf8_decode($det->sepr_servicio),'',0,'L');
            $this->pdf->Cell($w[3],$hline,($det->sepr_moneda == 'SOLES')?"S/":"$",'',0,'L');
            $this->pdf->Cell($w[4],$hline,($det->sepr_cantidad),'',0,'C');
            $this->pdf->Cell($w[5],$hline,($det->sepr_precio),'',0,'C');
            $this->pdf->Cell($w[6],$hline,($det->sepr_total),'',0,'R');
            $this->pdf->Ln();
            $lineas++;
            
            if(count($dess)>0){
                unset($dess[0]);
                foreach($dess as $desc){
                    $this->pdf->Cell($w[0],$hline,'','',0,'C');
                    $this->pdf->Cell($w[1],$hline,'','',0,'C');
                    $this->pdf->Cell($w[2],$hline,utf8_decode($desc),'',0,'L');
                    $this->pdf->Cell($w[3],$hline,'','',0,'C');
                    $this->pdf->Cell($w[4],$hline,'','',0,'C');
                    $this->pdf->Ln();
                    $lineas++;
                }
                $this->pdf->Ln(2);
             }
            $this->pdf->line(10,$this->pdf->GetY(),189,$this->pdf->GetY());
            $indice++;
        }
        endif;
        $this->pdf->Ln();
        $this->pdf->SetFillColor('255','255','255'); 
        $this->pdf->MultiCell(190,6,utf8_decode("Observaciones: ".$orden->obs),0,1,'L',0);
        $this->pdf->Ln();
        
        $archivo = "Orden de servicio_".DATE("d/m/Y").".pdf";

        $this->pdf->Output($archivo,'I');
    }
    public function genera_ordenPagoPDF($orde_id = 0, $file=false){

        $this->db->from('ordenpago');
        $this->db->where("orde_id", $orde_id);
        $orden = $this->db->get()->row();
        $detalle = $this->Model_general->getDetaOrdPago($orde_id);

        $this->load->library('pdf');
        $this->pdf = new Pdf();
        $this->pdf->AddPage();
        $this->pdf->AliasNbPages();
        $this->pdf->SetTitle("LIQUIDACION DE SERVICIOS");

        $this->pdf->SetLeftMargin(10);
        
        $this->pdf->SetFont('Arial', 'B', 16);

        $this->pdf->Cell(190,8,utf8_decode("LIQUIDACION DE SERVICIOS PARA ".$orden->orde_prov_name),0,0,'C');
        $this->pdf->SetFont('', '', 8);
        if($orden->orde_flota == "0"){
            $header = array('N°', 'FECHA', 'HORA', 'SERVICIO', 'GUIA', 'MONTO');
            $w = array(10,20,20,80,40,20);
        }else{
            $header = array('N°', 'FECHA', 'SERVICIO', 'CAPITAN / RESPONSABLE', 'GALS', 'GAL/U','TOTAL');
            $w = array(10,20,75,40,15,15,15);
        }
        
        $this->pdf->Ln(8);
        $this->pdf->SetFont('','B','');
        $this->pdf->SetFillColor('70','130','180'); 
        $this->pdf->SetTextColor(255);
        for($i = 0; $i < count($header); $i++)
            $this->pdf->Cell($w[$i],6,utf8_decode($header[$i]),0,0,'C',true);
        $this->pdf->Ln();
        $this->pdf->SetFont('');
        $this->pdf->SetTextColor(0);
        $indice = 0;
        $lineas = 0;
        $total = 0;
        foreach ($detalle as $num => $det) {
            $total += $det->deta_total;
            $numero = 0;
            preg_match_all("/.{1,50}[^ ]*/",$det->deta_servicio,$arra);
            $det->deta_servicio = implode("\r\n",$arra[0]);
            $hline = 7;
            $dess = array();
            
            if(preg_match("/\n/",$det->deta_servicio)){ ///  para saltos de linea
                $dess = explode("\n",$det->deta_servicio);
                $det->deta_servicio = $dess[0];
                $hline = 4;
                $this->pdf->Ln(0);
            }
            if($orden->orde_flota == "1"){
                

                $this->pdf->Cell($w[0],$hline,($num+1),'R,L',0,'C');
                $this->pdf->Cell($w[1],$hline,$det->deta_fecha,'R',0,'C');
                $this->pdf->Cell($w[2],$hline,utf8_decode($det->deta_servicio),'R',0,'L');
                $this->pdf->Cell($w[3],$hline,utf8_decode($det->deta_guia),'R',0,'L');
                $this->pdf->Cell($w[4],$hline,($det->deta_cantidad),'R',0,'R');
                $this->pdf->Cell($w[5],$hline,($det->deta_precio),'R',0,'R');
                $this->pdf->Cell($w[6],$hline,($det->deta_total),'R',0,'R');
                $this->pdf->Ln();
                $lineas++;
                
                if(count($dess)>0){
                    unset($dess[0]);
                    foreach($dess as $desc){
                        $this->pdf->Cell($w[0],$hline,'','R,L',0,'C');
                        $this->pdf->Cell($w[1],$hline,'','R',0,'C');
                        $this->pdf->Cell($w[2],$hline,utf8_decode($desc),'R',0,'L');
                        $this->pdf->Cell($w[3],$hline,'','R',0,'C');
                        $this->pdf->Cell($w[4],$hline,'','R',0,'C');
                        $this->pdf->Cell($w[5],$hline,'','R',0,'C');
                        $this->pdf->Cell($w[6],$hline,'','R',0,'C');
                        $this->pdf->Ln();
                        $lineas++;
                    }
                    $this->pdf->Ln(0);
                 }
             }else{

                $this->pdf->Cell($w[0],$hline,($num+1),'R,L',0,'C');
                $this->pdf->Cell($w[1],$hline,$det->deta_fecha,'R',0,'C');
                $this->pdf->Cell($w[2],$hline,$det->deta_hora,'R',0,'C');
                $this->pdf->Cell($w[3],$hline,utf8_decode($det->deta_servicio),'R',0,'L');
                $this->pdf->Cell($w[4],$hline,utf8_decode($det->deta_guia),'R',0,'L');
                $this->pdf->Cell($w[5],$hline,($det->deta_total),'R',0,'C');
                $this->pdf->Ln();
                $lineas++;
                
                if(count($dess)>0){
                    unset($dess[0]);
                    foreach($dess as $desc){
                        $this->pdf->Cell($w[0],$hline,'','R,L',0,'C');
                        $this->pdf->Cell($w[1],$hline,'','R',0,'C');
                        $this->pdf->Cell($w[2],$hline,'','R',0,'C');
                        $this->pdf->Cell($w[3],$hline,utf8_decode($desc),'R',0,'L');
                        $this->pdf->Cell($w[4],$hline,'','R',0,'C');
                        $this->pdf->Cell($w[5],$hline,'','R',0,'C');
                        $this->pdf->Ln();
                        $lineas++;
                    }
                    $this->pdf->Ln(0);
                 }
             }

            $this->pdf->line(10,$this->pdf->GetY(),200,$this->pdf->GetY());
            $indice++;
        }
        if($orden->orde_flota == "1"){
            $this->pdf->SetFont('','B','');
            $this->pdf->Cell($w[0],7,'','',0,'C');
            $this->pdf->Cell($w[1],7,'','',0,'C');
            $this->pdf->Cell($w[2],7,'','',0,'C');
            $this->pdf->Cell($w[3],7,'','',0,'L');
            $this->pdf->Cell($w[4],7,'','',0,'L');
            $this->pdf->Cell($w[5],7,'TOTAL','L,B,',0,'L');
            $this->pdf->Cell($w[6],7,$total,'L,B,R',0,'R');
        }else{
            $this->pdf->SetFont('','B','');
            $this->pdf->Cell($w[0],7,'','',0,'C');
            $this->pdf->Cell($w[1],7,'','',0,'C');
            $this->pdf->Cell($w[2],7,'','',0,'C');
            $this->pdf->Cell($w[3],7,'','',0,'L');
            $this->pdf->Cell($w[4],7,'TOTAL','L,B,',0,'L');
            $this->pdf->Cell($w[5],7,$total,'L,B,R',0,'R');
        }
        
        
        $this->pdf->Ln();
        
        $archivo = "ordenPago.pdf";
        if($file==false){
            $doc = $this->pdf->Output($archivo,'S');
            return $doc;        
        }else{
            $this->pdf->Output($archivo,'I');
        }
    }
    public function elim_orden($orde_id=''){
        $consulta = $this->db->where("deta_orde_id", $orde_id)->get("ordserv_detalle");
        $orden = $this->db->where("orde_id", $orde_id)->get("ordenserv")->row();

        $this->db->trans_start();
        if($consulta->num_rows() > 0){
            foreach ($consulta->result() as $key => $val) {
                //$this->actualizaPaquDeta($val->deta_pdet_id, '0');
                $ddelim = array("deta_esorden" => 0);
                $this->Model_general->guardar_edit_registro("paquete_detalle", $ddelim, array("deta_id" => $val->deta_pdet_id));
            }
        }
        $adicionales = $this->db->where("sepr_orde_id", $orde_id)->get("servicio_proveedor");

        if($adicionales->num_rows() > 0){
            foreach ($adicionales->result() as $key => $val) {
                if(is_null($val->sepr_pdet_id))
                    $this->Model_general->borrar(array("sepr_id" => $val->sepr_id), "servicio_proveedor");
                else
                    $this->Model_general->guardar_edit_registro("servicio_proveedor", array("sepr_orde_id" => null), array("sepr_id" => $val->sepr_id));
            }
        }

        //$this->Model_general->borrar(array("ordv_adic_orde_id" => $orde_id), "ordv_serv_adicional");
        $this->Model_general->add_log("ELIMINAR",3,"Eliminación de Orden de servicio ORD-".$orden->orde_numero);
        $this->Model_general->borrar(array("orde_id" => $orde_id), "ordenserv");
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE){
            $json["exito"] = false;
            $json["mensaje"] = "Ocurrio un error";
        }else{
            $json["exito"] = true;
            $json["mensaje"] = "Datos borrados con exito";
        }
        echo json_encode($json);
    }
    public function ord_pagar($id){
        $this->load->helper('Funciones');
        /*
        $this->db->select("orde_espagado as pagado, orde_moneda as moneda, orde_id as id, orde_total as total, (SELECT SUM(movi_monto) FROM cuenta_movimiento WHERE movi_ref_id = {$id} AND movi_tipo_id = 6 AND movi_tipo = 'SALIDA') as cancelado, orde_prov_name as prov_name, orde_numero as numero");
        $this->db->from("ordenpago");
        $this->db->where("orde_id", $id);
        */
        $orde = $this->Model_general->getOrdenPagoTotal($id);

        $this->db->select("DATE_FORMAT(deta_fecha, '%d/%m/%Y') as fecha, DATE_FORMAT(deta_hora, '%h:%i %p') as hora, deta_servicio as servicio, deta_cantidad as cantidad, deta_precio as precio, deta_total as total, deta_moneda as moneda, deta_guia as guia");
        $this->db->from("ordenpago_detalle");
        $this->db->where("deta_orde_id", $id);
        $detas = $this->db->get()->result();

        $arr_doc = array(0,1,2,3,9,11);
        $this->db->where_in("tcom_id", $arr_doc);
        $doc = $this->db->get("comprobante_tipo")->result();

        $documentos = array();
        foreach ($doc as $i => $d) {
            $documentos[$d->tcom_id] = $d->tcom_nombre;
        }
        $datos["cuentas"] = $this->Model_general->getOptions("cuenta",array("cuen_id","cuen_banco"),'* Cuenta');
        $datos['documentos'] = $documentos;
        $datos['orde'] = $orde;
        $datos['detas'] = $detas;
        $this->load->view('Ordenserv/form_pagar', $datos);
    }
    public function ord_proveedores() {
        $this->load->helper('Funciones');
        $this->load->library('Ssp');
        $this->load->library('Cssjs');
        $json = isset($_GET['json']) ? $_GET['json'] : false;

        
        $fecha = 'DATE_FORMAT(sepr_fecha,"%d/%m/%Y")';
        $hora = 'DATE_FORMAT(sepr_hora,"%h:%i %p")';
        $estado = "IF(sepr_estado = '0','<font class=red><strong>PENDIENTE','<font class=green><b>PAGADO')";

        $columns = array(
            array('db' => 'sepr_id',        'dt' => 'ID',           "field" => "sepr_id"),
            array('db' => $fecha,           'dt' => 'FECHA',        "field" => $fecha),
            array('db' => $hora,            'dt' => 'HORA',         "field" => $hora),
            array('db' => 'prov_rsocial',   'dt' => 'PROVEEDOR',    "field" => "prov_rsocial"),
            array('db' => 'sepr_servicio',  'dt' => 'SERVICIO',     "field" => "sepr_servicio"),
            array('db' => 'sepr_guia',      'dt' => 'GUIA',         "field" => "sepr_guia"),
            array('db' => 'sepr_total',     'dt' => 'TOTAL',        "field" => "sepr_total"),
            array('db' => 'sepr_moneda',    'dt' => 'MONEDA',       "field" => "sepr_moneda"),
            array('db' => $estado,          'dt' => 'ESTADO',       "field" => $estado),
            array('db' => 'sepr_id',        'dt' => 'DT_RowId',     "field" => "sepr_id"),
            array('db' => 'sepr_estado',    'dt' => 'DT_RowEstado', "field" => "sepr_estado"),
            array('db' => 'sepr_esorden',   'dt' => 'DT_RowOrden',  "field" => "sepr_esorden")
        );

        if ($json) {

            $json = isset($_GET['json']) ? $_GET['json'] : false;

            $table = 'servicio_proveedor';
            $primaryKey = 'sepr_id';

            $sql_details = array(
                'user' => $this->db->username,
                'pass' => $this->db->password,
                'db' => $this->db->database,
                'host' => $this->db->hostname
            );

            $condiciones = array();

            $joinQuery = "FROM servicio_proveedor JOIN proveedor ON prov_id = sepr_prov_id";
            $where = "";
            $condiciones[] = "sepr_orde_id IS NOT NULL";
            if (!empty($_POST['desde'])&&!empty($_POST['hasta'])){
                $condiciones[] = "sepr_fecha >='".$_POST['desde']."' AND sepr_fecha <='".$_POST['hasta']."'";
            }
            if (!empty($_POST['moneda']))
                $condiciones[] = "sepr_moneda='".$_POST['moneda']."'";
            if (!empty($_POST['estado'])){
                $ind = ($_POST['estado'] == '-1')?0:1;
                $condiciones[] = "sepr_estado='".$ind."'";
            }
            if (!empty($_POST['proveedor']))
                $condiciones[] = "sepr_prov_id='".$_POST['proveedor']."'";

            $where = count($condiciones) > 0 ? implode(' AND ', $condiciones) : "";
            echo json_encode(
                    $this->ssp->simple($_POST, $sql_details, $table, $primaryKey, $columns, $joinQuery, $where)
            );
            exit(0);
        }
        
        
        //$datos["guia"] = $this->Model_general->getOptions('guia', array("guia_id", "guia_nombres"),'* Guia');
        $datos["proveedor"] = $this->Model_general->getOptions('proveedor', array("prov_id", "prov_rsocial"),'* Proveedor');
        $datos["moneda"] = array_merge(array(''=>'* Monedas'),$this->Model_general->enum_valores('paquete','paqu_moneda'));
        $datos["estado"] = array("" => "* Estado", "-1" => "Pendiente", "1" => "Pagado");
        $datos['columns'] = $columns;
        
        $usua_tipo = $this->db->where("usua_id", $this->usua_id)->get("usuario")->row()->usua_tipo;

        $this->cssjs->add_js(base_url().'assets/js/Ordenserv/listado_pagos.js?v=1.0',false,false);
        $this->load->view('header');
        $this->load->view('menu');
        $this->load->view($this->router->fetch_class().'/'.$this->router->fetch_method(), $datos);
        $this->load->view('footer');
    }
    public function nextnumOrdPago(){
        $this->db->select('MAX(orde_numero) as max');
        $this->db->from('ordenpago');
        $query = $this->db->get();    
        $row = $query->row();
        $numero = $row->max+1;
        return $numero;
    }
    public function ord_genOrdenPago(){
        

        $this->load->helper('Funciones');
        $this->load->library('Ssp');
        $this->load->library('Cssjs');
        $json = isset($_GET['json']) ? $_GET['json'] : false;

        //$servicios = $this->db->query("SELECT * FROM servicio_proveedor JOIN proveedor ON prov_id = sepr_prov_id WHERE sepr_id IN (".$seleccionados.")")->result();

        $seleccionados = $this->input->get('sel');
        $servicios = $this->Model_general->get_detasOrd($seleccionados);
        $datos["titulo"] = "Generar Orden de Pago";
        $datos["servicios"] = $servicios;
        $datos["moneda"] = $servicios[0]->moneda;
        $datos["numero"] = $this->nextnumOrdPago();
        $datos["fecha"] = date("Y-m-d");

        $this->load->view('header');
        $this->load->view('menu');
        $this->load->view($this->router->fetch_class().'/'.$this->router->fetch_method(), $datos);
        $this->load->view('footer');
    }

    public function guardar_ordenPago($id_ord=''){
        if($id_ord != ''){

        }else{
            $prov_id = $this->input->post("prov_id");
            $prov_name = $this->input->post("prov_name");
            $numero = $this->input->post("numero");
            $fecha = $this->input->post("fecha");
            $moneda = $this->input->post("moneda");
            $total = $this->input->post("total");
            $deta_ids = $this->input->post("deta_id");
            
            $ordPago = array("orde_numero" => $numero,
                                "orde_fechareg" => $fecha,
                                "orde_prov_id" => $prov_id,
                                "orde_prov_name" => $prov_name,
                                "orde_total" => $total,
                                "orde_moneda" => $moneda,
                                "orde_usua" => $this->usua_id
            );
            $this->db->trans_begin();
            if (($meta = $this->Model_general->guardar_registro("ordenpago", $ordPago)) == TRUE){
                $detas = $this->Model_general->get_detasOrd($deta_ids,true);

                foreach ($detas as $i => $val) {
                    $item = array("deta_orde_id" => $meta["id"],
                                    "deta_sepr_id" => $val->id,
                                    "deta_prov_id" => $val->prov_id,
                                    "deta_fecha" => $this->Model_general->fecha_to_mysql($val->fecha),
                                    "deta_hora" => $this->Model_general->time_to_mysql($val->hora),
                                    "deta_tipo" => $val->tipo_id,
									"deta_tipo_name" => $val->tipo,
                                    "deta_referencia" => $val->referencia,
                                    "deta_servicio" => $val->servicio,
                                    "deta_precio" => $val->precio,
                                    "deta_cantidad" => $val->cantidad,
                                    "deta_total" => $val->total,
                                    "deta_moneda" => $val->moneda,
                                    "deta_guia" => $val->guia
                    );
                    if (!$this->Model_general->guardar_registro("ordenpago_detalle", $item)){
                        $resp["exito"] = false;
                        $resp["mensaje"] = "Algo salio mal, intentelo más tarde";
                        $this->Model_general->dieMsg($resp);
                    }else{
                        if(!$this->actualizaEstadosepr($val->id,"1")){
                            $resp["exito"] = false;
                            $resp["mensaje"] = "Algo salio mal, intentelo más tarde";
                            $this->Model_general->dieMsg($resp);
                        }
                    }
                }
                $this->Model_general->add_log("CREAR",4,"Creación de Orden de pago OP-".str_pad($numero,8,"0", STR_PAD_LEFT));
            }else{
                $this->db->trans_rollback();
                $resp["exito"] = false;
                $resp["mensaje"] = "Algo salio mal, intentelo más tarde";
                $this->Model_general->dieMsg($resp);
            }
            $this->db->trans_commit();
            $resp["exito"] = true;
            $resp["mensaje"] = "Guardado con exito";
            $this->Model_general->dieMsg($resp);
        }
    }
    public function actualizaEstadosepr($id='',$estado){
        $resp = true;
        $datas = array("sepr_esorden" => $estado);
        $condicion = array("sepr_id" => $id);
        if(!$this->Model_general->guardar_edit_registro("servicio_proveedor", $datas, $condicion))
            $resp = false;
        return $resp;
    }
    public function elim_ordenPago($id=''){
        $detas = $this->db->where("deta_orde_id", $id)->get("ordenpago_detalle")->result();
        $orde = $this->db->where("orde_id", $id)->get("ordenpago")->row();
        if($orde->orde_espagado == 1 || $orde->orde_pagado > 0){
            $json["exito"] = false;
            $json["mensaje"] = "No es posible eliminar, la orden esta pagada o tiene pagos registrados";
        }else{
            $this->db->trans_start();
            foreach ($detas as $i => $det) {
                $this->actualizaEstadosepr($det->deta_sepr_id,"0");
            }
            $this->Model_general->add_log("ELIMINAR",4,"Eliminación de Orden de pago OP-".$orde->orde_numero);
            $this->Model_general->borrar(array("orde_id" => $id), "ordenpago");
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE){
                $json["exito"] = false;
                $json["mensaje"] = "Ocurrio un error";
            }else{
                $json["exito"] = true;
                $json["mensaje"] = "Datos borrados con exito";
            }
        }
        echo json_encode($json);
    }
	public function reporte_excelOrdenes(){
        
        $desde = $this->input->post("desde");
        $hasta = $this->input->post("hasta");
        $search = $this->input->post("search")["value"];

        $this->db->select("CONCAT('ORD','-',orde_numero) file, DATE_FORMAT(orde_fecha, '%d/%m/%Y') fecha, (SELECT SUM(sepr_total) FROM servicio_proveedor WHERE sepr_orde_id = orde_id GROUP BY orde_id) total, SUM(deta_lunch) lunch, SUM(deta_pax) pax, orde_observacion obs, orde_servicio servicio");
        $this->db->from("ordenserv");
        $this->db->join("ordserv_detalle", "orde_id = deta_orde_id","LEFT");
        if ($desde != "" && $hasta != ""){
            $this->db->where("orde_fecha >=",$desde);
            $this->db->where("orde_fecha <=",$hasta);
        }
        $this->db->group_by("orde_id");
        $detalle = $this->db->get()->result();
        
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

        $verde = array(
            'font'  => array(
                'bold'  => true,
                'color' => array('rgb' => '00B050')
        ));
        $rojo = array(
            'font'  => array(
                'bold'  => true,
                'color' => array('rgb' => 'FF0000')
        ));
        

        $objPHPExcel->getActiveSheet()->getStyle('D')->applyFromArray($fillgray);
        $objPHPExcel->getActiveSheet()->getStyle('H')->applyFromArray($fillgray);

        $objPHPExcel->getActiveSheet()
                ->setCellValue('A1', 'FILE')
                ->setCellValue('B1', 'FECHA')
                ->setCellValue('C1', 'SERVICIO')
                ->setCellValue('D1', 'PAX')
                ->setCellValue('E1', 'LUNCH')
                ->setCellValue('F1', 'OBSERVACIONES');
        
        $objPHPExcel->getActiveSheet()->getStyle('F')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
        $objPHPExcel->getActiveSheet()->getStyle('D')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);      
        $objPHPExcel->getActiveSheet()->freezePane('A2');
        
        $ini = 3;
        $index = 0;
        
        foreach($detalle as $fila){
            $nro = $index+$ini;
            $index++;
            $objPHPExcel->getActiveSheet()
                        ->setCellValue("A$nro", $fila->file)
                        ->setCellValue("B$nro", $fila->fecha)
                        ->setCellValue("C$nro", $fila->servicio)
                        ->setCellValue("D$nro", $fila->pax)
                        ->setCellValue("E$nro", $fila->lunch)
                        ->setCellValue("F$nro", $fila->obs);
        }

        foreach(range('A','F') as $nro)
            $objPHPExcel->getActiveSheet()->getColumnDimension($nro)->setAutoSize(true);
        
        $fin = $index+$ini-1; 
        $objPHPExcel->getActiveSheet()->getStyle("B$ini:B$fin")->getNumberFormat()->setFormatCode('dd/mm/yyyy');
        
        
        $excel->excel_output($objPHPExcel, 'ORDENES DE SERVICIO '.$desde." - ".$hasta);
    }
}

