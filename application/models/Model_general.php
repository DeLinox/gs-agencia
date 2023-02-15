<?php

class Model_general extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    function guardar_edit_registro($tabla, $datas, $where) {
        $this->db->where($where);
        $this->db->update($tabla, $datas);
        if ($this->db->trans_status() === FALSE) {
            return FALSE;
        } else {
            return TRUE;
        }
    }

    function dieMsg($json){
        echo json_encode($json);
        exit;
    }

    function guardar_registro($tabla, $datas) {
        if (isset($datas)) {
            $this->db->set($datas);
            $this->db->insert($tabla);
            $id = $this->db->insert_id();
            if ($this->db->trans_status() === FALSE) {
                return FALSE;
            } else {
                return $datos = array("id" => $id);
            }
        } else {
            return FALSE;
        }
    }

    function select2($tabla, $search,$order=null) {
        $this->db->select("sql_calc_found_rows *", FALSE);
        if (!empty($search))
            $this->db->like($search);
        $this->db->from($tabla);
        if($order!=null)$this->db->order_by($order);
        $consulta = $this->db->get();
        //if($consulta->num_rows()> 0){
        $query = $this->db->query('SELECT FOUND_ROWS() AS total_count');
        $total_count = $query->row()->total_count;
        $response = array("total_count" => $total_count, "items" => $consulta->result());
        return $response;
    }

    public function borrar($where, $tabla) {
        $this->db->trans_begin();
        $this->db->where($where);
        $this->db->delete($tabla);
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return FALSE;
        } else {
            $this->db->trans_commit();
            return TRUE;
        }
    }

    function enum_valores($tabla, $campo) {
        $consulta = $this->db->query("SHOW COLUMNS FROM $tabla LIKE '$campo'");
        if ($consulta->num_rows() > 0) {
            $consulta = $consulta->row();
            $array = explode(",", str_replace(array("enum", "'", "(", ")"), "", $consulta->Type));
            foreach ($array as $key) {
                $array2[$key] = $key;
            }
            return $array2;
        } else {
            return FALSE;
        }
    }


    function getDocumentos($id = 0) {
        if ($id != 0) {
            $this->db->where($id);
            $consulta = $this->db->get('maestra_documentos');
            if ($consulta->num_rows() > 0) {
                $consulta = $consulta->row();
                return $consulta;
            } else {
                return FALSE;
            }
        } else {
            $this->db->select("docu_id,docu_nombre");
            $this->db->from("maestra_documentos");
            $consulta = $this->db->get();
            if ($consulta->num_rows() > 0) {
                return $consulta->result();
            } else {
                return FALSE;
            }
        }
    }

    function getData($table, $datos, $where=null) {
        $this->db->select(implode(",", $datos));
        if($where != null)
            $this->db->where($where);
        $this->db->from($table);
        $consulta = $this->db->get();
        return $consulta->result();
    }
    function getDataOr($table, $datos, $where=null, $orwhere=null) {
        $this->db->select(implode(",", $datos));
        if($where != null)
            $this->db->where($where);
        if($orwhere != null)
            $this->db->or_where($orwhere);
        $this->db->from($table);
        $consulta = $this->db->get();
        return $consulta->result();
    }

    function select_options($datos, $opts, $vacio = FALSE) {
        $options = ($vacio != FALSE) ? array("" => $vacio) : array();
        $id = $opts[0];
         $nombre = $opts[1];
        foreach ($datos as $value) {
            $options[$value->$id] = $value->$nombre;
        }
        return $options;
    }
    
    function getOptions($table,$datos,$vacio=FALSE){
        return $this->select_options($this->getData($table,$datos),$datos,$vacio);
    }
    function getOptionsWhere($table,$datos,$where,$vacio=FALSE){
        return $this->select_options($this->getData($table,$datos,$where),$datos,$vacio);
    }
    function getOptionsWhereOr($table,$datos,$where,$orwhere,$vacio=FALSE){
        return $this->select_options($this->getDataOr($table,$datos,$where,$orwhere),$datos,$vacio);
    }


    
    function check_captcha($where){
      $this->db->where($where);
      $this->db->limit(1); 
      $consulta=$this->db->get('captcha');

      if($consulta->num_rows()> 0){
        return TRUE;
      }
      else
      {
        return FALSE;
      }
    }
    function fecha_to_mysql($fecha) {
        if (empty($fecha))
            return NULL;
        if(preg_match('#([0-9]{1,2})/([0-9]{1,2})/([0-9]{2,4})?#', $fecha, $mifecha)){
                    return $mifecha[3] . "-" . $mifecha[2] . "-" . $mifecha[1];
                }
        return NULL;
    }
    function mysql_to_fecha($fecha) {
        if (empty($fecha))
            return NULL;
        if(preg_match('#([0-9]{1,2})-([0-9]{1,2})-([0-9]{2,4})?#', $fecha, $mifecha)){
                    return $mifecha[3] . "-" . $mifecha[2] . "-" . $mifecha[1];
                }
        return NULL;
    }
    /**
    * De formato '23/06/2012 05:25 PM' a mysql ( 2012-06-23 17:25:00 ) 
    * @param type $fechahora
    * @return String Mysql datetime or NULL if not match
    */
    function datetime_to_mysql($fechahora) {
        if (preg_match('#([0-9]{1,2})/([0-9]{1,2})/([0-9]{2,4}) ([0-9]{1,2}):([0-9]{1,2}) ([A|P][M])#', $fechahora, $buf)){
            $hora = ($buf[6]=='PM' && $buf[4]<12) ? $buf[4]+12 : ($buf[4]==12 && $buf[6]=='AM'? 0:$buf[4]);
            return $buf[3] . '-' . $buf[2] . '-' . $buf[1] . ' ' . $hora . ':' . $buf[5] . ':00';
        }else{
            return NULL;
        }
    }

    function time_to_mysql($fechahora) {
        if (preg_match('#([0-9]{1,2}):([0-9]{1,2}) ([A|P][M])#', $fechahora, $buf)){
            $hora = ($buf[3]=='PM' && $buf[1]<12) ? $buf[1]+12 : ($buf[1]==12 && $buf[3]=='AM'? 0:$buf[1]);
            return $hora . ':' . $buf[2] . ':00';
        }else{
            return NULL;
        }
    }

    /**
    * De formato mysql ( 2012-06-23 17:25:00 ) a  '23/06/2012 05:25 PM' 
    * @param type $fechahora
    * @return null 
    */
    function datetime_from_mysql($fechahora) {
        if (preg_match('#([0-9]{2,4})-([0-9]{1,2})-([0-9]{1,2}) ([0-9]{1,2}):([0-9]{1,2}):([0-9]{1,2})#', $fechahora, $buf)){
            return $buf[3].'/'.$buf[2].'/'.$buf[1].' '.($buf[4]>12 ? $buf[4]-12 :  ($buf[4] == 0? 12 :$buf[4])) . ':' . $buf[5] . ' '.($buf[4]>=12 ? 'PM' : 'AM');
        }else{
            return NULL;
        }
    }
    public function getDetaPaqu($id=''){
        $this->db->select("deta_id, deta_paqu_id, deta_hote_id,deta_descripcion,deta_hotel,deta_serv_id,deta_xcta, deta_precio, deta_escobrado, deta_escomprobante, deta_espagado, deta_guia_id, deta_guia_nombre, deta_almuerzo, deta_pax,date_format(deta_fechaserv,'%d/%m/%Y') as  deta_fechaserv,date_format(deta_fechaserv,'%h:%i %p') as  deta_hora, deta_esendose, deta_taxi, hote_nombre, guia_nombres, serv_descripcion, deta_ruta, deta_prioridad, deta_emba, emba_nombre, deta_total, deta_lunch, deta_lunch_pre");
        $this->db->from("paquete_detalle");
        $this->db->where("deta_paqu_id", $id);
        $this->db->join("guia", "guia_id = deta_guia_id", 'left');
        $this->db->join("servicio", "serv_id = deta_serv_id", 'left');
        $this->db->join("hotel", "hote_id = deta_hote_id", 'left');
        $this->db->join("embarcacion", "emba_id = deta_emba", 'left');
        $this->db->order_by("deta_id", 'ASC');
        $consulta = $this->db->get();
        return $consulta->result();
    }
    public function getDetaPaqu2($id=''){
        $this->db->select("deta_id, deta_paqu_id, deta_hote_id,deta_descripcion,deta_hotel,deta_serv_id, 
							deta_servicio as deta_serv_name,deta_xcta, deta_precio, deta_escobrado, 
							deta_escomprobante, deta_espagado, deta_almuerzo, deta_pax,
							date_format(deta_fechaserv,'%d/%m/%Y') as  deta_fechaserv,
							date_format(deta_fechaserv,'%h:%i %p') as  deta_hora, hote_nombre, serv_descripcion, 
							deta_prioridad, deta_total, deta_lunch, deta_lunch_pre, deta_bus, 
							date_format(deta_fecha_llegada,'%d/%m/%Y') as  deta_fecha_llegada, deta_bus_salida, 
							deta_terc_nombre, deta_terc_monto, deta_subserv_id, deta_subserv_name, deta_lugar, 
							deta_emba_name, deta_emba_id, deta_ruta, deta_guia, deta_color, deta_hotelchk");
        $this->db->from("paquete_detalle");
        $this->db->where("deta_paqu_id", $id);
        $this->db->join("servicio", "serv_id = deta_serv_id", 'left');
        $this->db->join("hotel", "hote_id = deta_hote_id", 'left');
        $this->db->order_by("deta_id", 'ASC');
        $consulta = $this->db->get();
        $consulta = $consulta->result();

        foreach ($consulta as $val) {
            $adic = $this->db->where(array("padi_pdet_id" => $val->deta_id, "padi_tipo" => 'ADICION'))->get("paquete_adicion")->result();
            $desc = $this->db->where(array("padi_pdet_id" => $val->deta_id, "padi_tipo" => 'DESCUENTO'))->get("paquete_adicion")->result();
            $this->db->where(array("sepr_pdet_id" => $val->deta_id));
            $this->db->join("proveedor", "sepr_prov_id = prov_id","LEFT");
            $this->db->join("proveedor_tipo", "sepr_tipo = tipo_id","LEFT");
            $prov = $this->db->get("servicio_proveedor")->result();
            
            $val->adiciones = $adic;
            $val->descuentos = $desc;
            $val->proveedores = $prov;
        }
        return $consulta;
    }
    public function getDetaOrd($id=''){
        $this->db->select("OD.deta_id, OD.deta_orde_id, OD.deta_pdet_id, DATE_FORMAT(OD.deta_fecha,'%d/%m/%Y') as deta_fecha, OD.deta_file, OD.deta_pax, IF(PD.deta_guia <> '',CONCAT(OD.deta_nombres,' / ',PD.deta_guia),OD.deta_nombres) as deta_nombres, OD.deta_hotel, DATE_FORMAT(OD.deta_hora,'%l:%i %p') as deta_hora, OD.deta_lunch, OD.deta_contacto, OD.deta_endose, OD.deta_obs, OD.deta_servicio");
        $this->db->from("ordserv_detalle as OD");
        $this->db->join("paquete_detalle as PD", "OD.deta_pdet_id = PD.deta_id");
        $this->db->where("OD.deta_orde_id", $id);
        $this->db->order_by("OD.deta_id", 'ASC');
        $consulta = $this->db->get();
        return $consulta->result();
    }
    public function getDetaLiqu($id=''){
        $this->db->select("LD.deta_id as id, LD.deta_liqu_id as liqu_id, LD.deta_pdet_id as pdet_id, 
							LD.deta_serv_id as serv_id, LD.deta_serv_name as serv_name, LD.deta_serv_prec as serv_prec, 
							LD.deta_pax as pax, DATE_FORMAT(LD.deta_fecha,'%d/%m/%Y') as fecha, LD.deta_guia as guia, 
							LD.deta_hotel as hotel, LD.deta_nombre as nombre, LD.deta_lunch_efect as lunch_efect, 
							LD.deta_lunch as lunch, LD.deta_lunch_prec as lunch_prec, LD.deta_total as total, 
							P.paqu_endose endose, P.paqu_estado, LD.deta_tipo tipo, LD.deta_adicion deta_adic, , LD.deta_adicion_val deta_adic_val,
							LD.deta_descuento deta_desc, LD.deta_descuento_val deta_desc_val");
        $this->db->from("liquidacion_detalle LD");
		$this->db->join("paquete_detalle PD","PD.deta_id = LD.deta_pdet_id");
		$this->db->join("paquete P","P.paqu_id = PD.deta_paqu_id");
        $this->db->where("LD.deta_liqu_id", $id);
        $this->db->order_by("LD.deta_id", 'ASC');
        $consulta = $this->db->get();
        return $consulta->result();
    }
    function getOrdenTotal($id=''){
        $this->db->select('orde_nombre, orde_id, IF(SUM(deta_precio)<>"",SUM(deta_precio),0.00) as total, orde_moneda');
        $this->db->from('ordenserv');
        $this->db->where('orde_id',$id);
        $this->db->join('ordserv_detalle','deta_orde_id = orde_id','left');
        $this->db->group_by('orde_id'); 
        $query = $this->db->get();
        if($query->num_rows() > 0 ){
            return $query->row();
        }else{
            return false;
        }
    }
    function getOrdenPagado($id=''){
        $this->db->select('IF(SUM(pago_monto)<>"",SUM(pago_monto),0.00) as total');
        $this->db->from('pago');
        $this->db->where('pago_orde_id',$id);
        $this->db->group_by('pago_orde_id'); 
        $query = $this->db->get();
        if($query->num_rows() > 0 ){
            return $query->row()->total;
        }else{
            return false;
        }
    }
    function getVenta($id = ''){
        $this->db->select("vent_id, vent_tipo, DATE_FORMAT(vent_fecha, '%d/%m/%Y') as vent_fecha, vent_tcom_id, vent_serie, vent_numero, vent_clie_id, vent_clie_direccion, vent_clie_rsocial, vent_clie_tdoc_id, vent_clie_tdoc_nro, vent_clie_email, vent_moneda, vent_subtotal, vent_igv, vent_total, vent_obs");
        $this->db->from('venta');
        $this->db->where('vent_id', $id);
        $consulta = $this->db->get();
        if($consulta->num_rows() > 0)
            return $consulta->row();
        else
            return false;
    }
    function getDetaVenta($id = ''){
        $this->db->select('deta_serv_name, deta_serv_id, deta_id, deta_vent_id, deta_pdet_id, deta_igv, 
							deta_lunch, deta_adic, deta_adic_val, deta_desc, deta_desc_val, 
							DATE_FORMAT(deta_fechaserv, "%d/%m/%Y") as deta_fechaserv, deta_descripcion, deta_precio, 
							deta_pax, deta_lunch_efect, deta_lunch_prec, deta_fprecio');
        $this->db->from('venta_detalle');
        $this->db->where('deta_vent_id', $id);
        $this->db->join('servicio', "serv_id = deta_serv_id");
		$this->db->order_by('deta_fechaserv', 'asc');
        $consulta = $this->db->get();
        if($consulta->num_rows() > 0)
            return $consulta->result();
        else
            return false;
    }
    function getOrdenAdicionales($id = '',$paqu = false){
        $this->db->select("SP.sepr_id, SP.sepr_orde_id, SP.sepr_pdet_id, SP.sepr_prov_id, SP.sepr_prov_id, DATE_FORMAT(SP.sepr_fecha, '%d/%m/%Y') as sepr_fecha , DATE_FORMAT(SP.sepr_hora, '%h:%i %p') as sepr_hora, SP.sepr_tipo, SP.sepr_servicio, SP.sepr_precio, SP.sepr_cantidad, SP.sepr_total, SP.sepr_moneda, SP.sepr_espagado, SP.sepr_esorden, SP.sepr_guia, PT.*, P.*");
        $this->db->from("servicio_proveedor as SP");
        $this->db->join('proveedor_tipo as PT', 'PT.tipo_id = SP.sepr_tipo');
        $this->db->join('proveedor as P', 'P.prov_id = SP.sepr_prov_id');
        if($paqu) $this->db->where('SP.sepr_pdet_id', $id);    
        else $this->db->where('SP.sepr_orde_id', $id);
        $this->db->order_by("SP.sepr_id", "ASC");
        $consulta = $this->db->get();
        if($consulta->num_rows() > 0)
            return $consulta->result();
        else
            return false;
    }
    function getLiquTotal($id=''){
        //$this->db->select('liqu_id as id, liqu_numero as numero, liqu_clie_rsocial as cliente, liqu_total as total, liqu_moneda as moneda, FORMAT((SELECT SUM(movi_monto) FROM cuenta_movimiento WHERE movi_ref_id = '.$id.' AND movi_tipo_id = 1),2) as cancelado, liqu_estado as pagado');
		$this->db->select('liqu_id as id, liqu_numero as numero, liqu_clie_rsocial as cliente, liqu_total as total, liqu_moneda as moneda, 
						liqu_cobrado as cancelado, liqu_estado as pagado');
        $this->db->from('liquidacion');
        $this->db->where('liqu_id',$id);
        $query = $this->db->get();
        if($query->num_rows() > 0 ){
            return $query->row();
        }else{
            return false;
        }
    }
    function getPaqTotal($id=''){
        //$this->db->select('paqu_id as id, paqu_prefijo as prefijo, paqu_numero as numero, paqu_clie_rsocial as cliente, paqu_total as total, paqu_moneda as moneda, FORMAT((SELECT SUM(movi_monto) FROM cuenta_movimiento WHERE movi_ref_id = '.$id.' AND movi_tipo_id = 4),2) as cancelado, paqu_escobrado as cobrado');
		$this->db->select('paqu_id as id, paqu_prefijo as prefijo, paqu_numero as numero, paqu_clie_rsocial as cliente, 
							paqu_total as total, paqu_moneda as moneda, paqu_cobrado as cancelado, 
							paqu_escobrado as cobrado');
        $this->db->from('paquete');
        $this->db->where('paqu_id',$id);
        $query = $this->db->get();
        if($query->num_rows() > 0 ){
            return $query->row();
        }else{
            return false;
        }
    }
    function getOrdenPagoTotal($id=''){
       $this->db->select("orde_espagado as pagado, orde_moneda as moneda, orde_id as id, orde_total as total, orde_pagado as cancelado, orde_prov_name as prov_name, orde_numero as numero");
        $this->db->from("ordenpago");
        $this->db->where("orde_id", $id);
        $query = $this->db->get();
        if($query->num_rows() > 0 ){
            return $query->row();
        }else{
            return false;
        }
    }
    function getPaqProv($id=''){
        $this->db->select('P.paqu_id as id, P.paqu_prefijo as prefijo, P.paqu_numero as numero, P.paqu_clie_rsocial as cliente, P.paqu_moneda as moneda, FORMAT((SELECT SUM(movi_monto) FROM cuenta_movimiento WHERE movi_ref_id = '.$id.' AND movi_tipo_id = 4 AND movi_tipo="SALIDA"),2) as cancelado, P.paqu_espagado as pagado');
        $this->db->from('paquete as P');
        $this->db->where('P.paqu_id',$id);
        $query = $this->db->get()->row();

        $this->db->select("SUM(sepr_total)");
        $this->db->from("paquete_detalle");
        $this->db->join("servicio_proveedor","sepr_pdet_id = deta_id","LEFT");
        $this->db->where("deta_paqu_id",$id);
        //$this->db->group_by("ordv_adic_pdet_id");
        $detas = $this->db->get()->result();
        print_r($detas);
        exit(0);
    }
    function getCompTotal($id=''){
        //$this->db->select('vent_id as id, CONCAT(vent_serie," - ", vent_numero) as numero, vent_clie_rsocial as cliente, vent_total as total, vent_moneda as moneda, (SELECT SUM(movi_monto) FROM cuenta_movimiento WHERE movi_ref_id = vent_id AND movi_tipo_id = 3) as cancelado, vent_escobrado as cobrado');
		$this->db->select('vent_id as id, CONCAT(vent_serie," - ", vent_numero) as numero, vent_clie_rsocial as cliente, vent_total as total, vent_moneda as moneda, vent_cobrado as cancelado, vent_escobrado as cobrado');
        $this->db->from('venta');
        $this->db->where('vent_id',$id);

        $query = $this->db->get();
        if($query->num_rows() > 0 ){
            return $query->row();
        }else{
            return false;
        }
    }
    function actualizarCaja($tipo_id, $tipo, $tcom_id, $serie, $numero, $descripcion, $monto=0, $moneda, $usuario, $ref, $refdeta, $cuenta,$file='',$fecha='',$obs=''){
        $ingreso = 0;
        $egreso = 0;

        if($tipo == 'INGRESO'){
            $ingreso = $monto;
            $this->db->query("UPDATE cuenta SET cuen_monto=cuen_monto+{$monto},cuen_fechaupd=NOW() WHERE cuen_id={$cuenta}");
        }else{
            $egreso = $monto;
            $this->db->query("UPDATE cuenta SET cuen_monto=cuen_monto-{$monto},cuen_fechaupd=NOW() WHERE cuen_id={$cuenta}");
        }
        $saldo = $this->db->query('SELECT cuen_monto FROM cuenta WHERE cuen_id="'.$cuenta.'"')->row()->cuen_monto;

        if($file == ''){
                $row = $this->db->select('MAX(movi_file) as max')->from('cuenta_movimiento')->where("movi_cuen_id", $cuenta)->get()->row();
                $file = $row->max+1;
        }
        if($fecha == '') $fecha = date('Y-m-d H:i:s');
        else $fecha = $fecha." 00:00:00";

        $datos = array('movi_cuen_id' => $cuenta,
            'movi_tipo_id' => $tipo_id,
            'movi_tipo' => $tipo,
            'movi_tcom_id' => $tcom_id,
            'movi_comp_serie' => $serie,
            'movi_comp_numero' => $numero,
            'movi_descripcion' => $descripcion,
            'movi_monto' => $monto,
            'movi_ingreso' => $ingreso,
            'movi_egreso' => $egreso,
            'movi_saldo' => $saldo,
            'movi_moneda' => $moneda,
            'movi_fechareg' => $fecha,
            'movi_usua_id' => $usuario,
            'movi_ref_id' => $ref,
            'movi_refdeta_id' => $refdeta,
            'movi_file' => $file,
            'movi_obs' => $obs
        );
        $this->db->set($datos);
        $this->db->insert("cuenta_movimiento");
        $id = $this->db->insert_id();
        return $id;
    } 
    function getServicios($id=''){
        $this->db->select("serv_id as id, serv_descripcion as nombre, serv_abrev as abrev, serv_tipo_reserv as tipo, DATE_FORMAT(serv_hora, '%h:%i %p') as hora, serv_habilitado as habilitado");
        $this->db->from("servicio");
        $this->db->where("serv_habilitado", "1");
        if($id != '')
            $this->db->where("serv_id", $id);
        $this->db->order_by("serv_id","ASC");
        $consulta = $this->db->get();
        if($id != '') $consulta = $consulta->row();
        else $consulta = $consulta->result();
        return $consulta;
    }
    function getUsuarios($id = ''){
        $this->db->select("usua_id as id, usua_nombres as nombres, usua_user as user, usua_email as email, usua_fechanac as nacimiento, usua_movil as cel, usua_habilitado as habilitado, usua_tipo as tipo");
        $this->db->from("usuario");
        if($id != '')
            $this->db->where("usua_id", $id);
        $consulta = $this->db->get();
        if($id != ''){
            $consulta = $consulta->row();
            return $consulta;
        } 
        else return $consulta->result();
    }
    public function adiciones_pagoGen($deta='', $tabla,$adicion,$monto,$tipo,$key){
        $this->db->select("{$adicion} as adicc, {$monto} as precc, {$tipo} as tipo");
        $this->db->where("{$key}", $deta);
        $this->db->order_by("{$tipo}", "ASC");
        $adides = $this->db->get($tabla);
        $adiciones["desc"] = '';
        $adiciones["monto"] = '';    
        $adiciones["sum_desc"] = '0';
        if($adides->num_rows() > 0){
            $suma = 0;
            foreach ($adides->result() as $val) {
                $adiciones["desc"] .= "</br>".$val->adicc;
                if($val->tipo == 'ADICION'){
                    $simb = '';
                    $suma += $val->precc;
                }else{
                    $simb = '-';
                    $suma -= $val->precc;
                }
                $adiciones["monto"] .= "</br>".$simb.$val->precc;
            }    
            $adiciones["sum_desc"] = $suma;
        }
        return $adiciones;
    }
	public function get_adidesDeta($deta='', $tabla,$adicion,$monto,$tipo,$key){
        $this->db->select("{$adicion} as adicc, {$monto} as precc, {$tipo} as tipo");
        $this->db->where("{$key}", $deta);
        $this->db->order_by("{$tipo}", "ASC");
        $adides = $this->db->get($tabla);
        $adiciones["desc"] = '';
        $adiciones["monto"] = '';    
        $adiciones["sum_desc"] = '0';
        if($adides->num_rows() > 0){
            $suma = 0;
            foreach ($adides->result() as $i => $val) {
                if($i == 0)
                    $adiciones["desc"] .= $val->adicc;
                else
                    $adiciones["desc"] .= ", ".$val->adicc;
                if($val->tipo == 'ADICION'){
                    $simb = '';
                    $suma += $val->precc;
                }else{
                    $simb = '-';
                    $suma -= $val->precc;
                }
                $adiciones["monto"] .= $simb.$val->precc;
            }    
            $adiciones["sum_desc"] = $suma;
        }
        return $adiciones;
    }
    public function asignar_imagenes($imagenes, $paqu_id){
        $resp = true;
        if(count($imagenes) > 0){
            foreach ($imagenes as $val) {
                $resp = $this->guardar_registro("paquete_imagen", array("paim_imagen" => $val, "paim_paqu" => $paqu_id));
            }
            /*
            for ($i=0; $i < count($imagenes); $i++) { 
                $resp = $this->guardar_registro("paquete_imagen", array("paim_imagen" => $imagenes[$i], "paim_paqu" => $paqu_id));   
            }
            */
        }
        return $resp;
    }
    /*
    public function encaja($_vTipo, $_vTipoId, $_vMoneda, $_vMonto, $_vCuenta, $_vDescripcion, $_vUsuaId){
        
        $vIngreso = 0.00;
        $vEgreso = 0.00;
        
        if($_vTipo='1'){
            $vIngreso = $_vMonto;
            $this->db->query("UPDATE cuenta SET cuen_monto=cuen_monto+{$_vMonto},cuen_fechaupd=NOW() WHERE cuen_id={$_vCuenta}");
        }else{
            $vEgreso = $_vMonto;
            $this->db->query("UPDATE cuenta SET cuen_monto=cuen_monto-{$_vMonto},cuen_fechaupd=NOW() WHERE cuen_id={$_vCuenta}");
        }
        
        $consulta = $this->db->query("SELECT cuen_monto,cuen_moneda FROM cuenta WHERE cuen_id=$_vCuenta")->row();
        $vSaldo = $consulta->cuen_monto,
        $vMoneda = $consulta->cuen_moneda;

        $data = array("movi_cuen_id" => $_vCuenta,
                        "movi_tipo_id" => $_vTipoId,
                        "movi_tipo" => $_vTipo,
                        "movi_fechareg" => date('Y-m-d H:i:s'),
                        "movi_descripcion" => $_vDescripcion,
                        "movi_ingreso" => $vIngreso,
                        "movi_egreso" => $vEgreso,
                        "movi_saldo" => $vSaldo,
                        "movi_usua_id" => $

    );

        INSERT kardex_dinero(
            kard_cuen_id,
            kard_tipo,
            kard_tipo_id,
            kard_fechareg,
            kard_descripcion,
            kard_ingreso,
            kard_egreso,

            kard_saldo,
            kard_usua_id,
            kard_moneda
        )
        VALUES(
            _vCuenta,
            _vTipo,
            _vTipoId,
            NOW(),
            _vDescripcion,
            vIngreso,
            vEgreso,
            vSaldo,
            _vUsuaId,
            vMoneda
        );
    
    }
    */
    public function get_servProveedor($id=''){
        
        $this->db->select("SP.sepr_id, SP.sepr_orde_id, SP.sepr_pdet_id, SP.sepr_prov_id, SP.sepr_prov_id, DATE_FORMAT(SP.sepr_fecha, '%d/%m/%Y') as sepr_fecha , DATE_FORMAT(SP.sepr_hora, '%h:%i') as sepr_hora, SP.sepr_tipo, SP.sepr_servicio, SP.sepr_precio, SP.sepr_cantidad, SP.sepr_total, SP.sepr_moneda, SP.sepr_espagado, SP.sepr_esorden, SP.sepr_guia, PT.*, P.*");
        $this->db->from("servicio_proveedor as SP");
        $this->db->join('proveedor_tipo as PT', 'PT.tipo_id = SP.sepr_tipo');
        $this->db->join('proveedor as P', 'P.prov_id = SP.sepr_prov_id');
        $this->db->where("SP.sepr_id", $id);
        return $this->db->get()->row();
    }
    public function get_detasOrd($seleccionados,$save = false){
        $this->db->select("sepr_id as id, sepr_prov_id as prov_id, prov_rsocial as prov_name, DATE_FORMAT(sepr_fecha, '%d/%m/%Y') as fecha, DATE_FORMAT(sepr_hora, '%h:%i %p') as hora, tipo_denom as tipo, sepr_tipo as tipo_id, sepr_servicio as servicio, sepr_cantidad as cantidad, sepr_precio as precio, sepr_total as total, sepr_moneda as moneda, sepr_guia as guia, IF(sepr_orde_id IS NOT NULL,CONCAT('ORD-',orde_numero),CONCAT(paqu_prefijo,'-',paqu_numero)) referencia");
        $this->db->from("servicio_proveedor");
        $this->db->join("proveedor", "prov_id = sepr_prov_id");
        $this->db->join("proveedor_tipo", "tipo_id = sepr_tipo");
        $this->db->join("paquete_detalle", "deta_id = sepr_pdet_id","LEFT");
        $this->db->join("paquete", "paqu_id = deta_paqu_id","LEFT");
        $this->db->join("ordenserv", "orde_id = sepr_orde_id","LEFT");
        if(!$save){
            $this->db->where_in("sepr_id", explode(',', $seleccionados));
            $this->db->where("sepr_espagado","0");
        }else{
            $this->db->where_in("sepr_id", $seleccionados);
        }
        return $this->db->get()->result();
    }
    public function getDetaOrdPago($id=''){
        $this->db->select("deta_id, deta_orde_id, deta_sepr_id, deta_prov_id, DATE_FORMAT(deta_fecha,'%d/%m/%Y') as deta_fecha, 
							DATE_FORMAT(deta_hora,'%h:%i %p') as deta_hora, deta_tipo, deta_servicio, deta_precio, deta_cantidad, 
							deta_total, deta_moneda, deta_espagado, deta_guia, deta_referencia, deta_tipo_name");
        $this->db->from("ordenpago_detalle");
        $this->db->where("deta_orde_id", $id);
        $this->db->order_by("deta_id", 'ASC');
        $consulta = $this->db->get();
        return $consulta->result();
    }
    public function get_DetaServ($serv_id, $fecha){
        $this->db->select("deta_pax as pax, deta_hotel as hotel, deta_lunch as lunch, paqu_clie_rsocial as contacto, paqu_nombre as nombre, deta_llegada as llegada, deta_id as id, paqu_clie_id as clie_id, paqu_endose as endose, deta_guia as guia, deta_prioridad as prioridad, deta_descripcion as descripcion");
        $this->db->from("paquete_detalle");
        $this->db->join("paquete", "paqu_id = deta_paqu_id AND paqu_estado = 'CONFIRMADO'");
        $this->db->where("deta_serv_id", $serv_id);
        $this->db->where("DATE(deta_fechaserv)", $fecha);
        return $this->db->get();
    }
    public function add_log($accion,$modulo,$descripcion){
        $datas = array("Log_user_id" => $this->session->userdata('authorized'),
                        "log_accion" => $accion,
                        "log_modulo" => $modulo,
                        "log_fecha" => date('Y-m-d H:i:s'),
                        "log_descripcion" => $descripcion
        );
        $this->db->set($datas);
        $this->db->insert("log");
    }
    public function verif_exist($paqu_id,$tipo){
        
        if($tipo == 'l'){
            $busca = "CONCAT('LIQ-',liqu_numero)";
            $from = "liquidacion_paqu";
            $join = "liquidacion";
            $on = "liqu_id = lpaq_liqu_id";
            $where = array("lpaq_paqu_id" => $paqu_id);
        }else{
            $busca = "CONCAT(V.vent_serie,'-',V.vent_numero)";
            $from = "venta_paquete as VP";
            $join = "venta as V";
            $on = "V.vent_id = VP.vent_id";
            $where = array("VP.paqu_id" => $paqu_id);
        }
        $this->db->select($busca." as file");
        $this->db->from($from);
        $this->db->join($join, $on);
        $this->db->where($where);
        return $this->db->get();
    }
    public function getPermisos($usuario=''){
        $this->db->select("M.mod_nombre, M.mod_id, MU.nivel_acceso");
        $this->db->from("modulo M");
        $this->db->join("modulo_usuario MU", "MU.mod_id = M.mod_id AND MU.usua_id = '{$usuario}'","left");
        $this->db->order_by("M.mod_id");
        return $this->db->get()->result();
    }
    public function getLiquCompTotal($id=''){
        $this->db->select('liqu_id as id, CONCAT("LV - ", liqu_numero) as numero, clie_rsocial as cliente, liqu_total as total, liqu_moneda as moneda, liqu_cobrado as cancelado, liqu_escobrado as cobrado');
        $this->db->from('venta_liquidacion');
        $this->db->join('cliente',"clie_id = liqu_clie_id");
        $this->db->where('liqu_id',$id);
        $query = $this->db->get();
        if($query->num_rows() > 0 ){
            return $query->row();
        }else{
            return false;
        }
    }
	public function actualizaPaqueteDetalle($paqu_id,$estado){
        $this->db->query("UPDATE paquete_detalle SET deta_escobrado = {$estado} WHERE deta_paqu_id = {$paqu_id}");
    }
	public function get_serv_exist($tipo,$tipor, $mes, $anio){
		if($tipo == "AGENCIAS"){
			$this->db->select("serv_id id, serv_descripcion nombre");
			$this->db->from("servicio");
			$this->db->join("paquete_detalle", "deta_serv_id = serv_id");
			$this->db->join("paquete", "paqu_id = deta_paqu_id");
			$this->db->where(array("YEAR(DATE(deta_fechaserv))" => $anio,));
			if($tipor != "")
				$this->db->where("paqu_tipo", $tipor);
			if($mes != "")
				$this->db->where("MONTH(DATE(deta_fechaserv))", $mes);
			$this->db->group_by("deta_serv_id");
			$consulta = $this->db->get()->result();
			$seleccion = "<option value=''>* Todos los servicios<option>";
		}else{
			$this->db->select("paqu_clie_id id, paqu_clie_rsocial nombre");
			$this->db->from("paquete");
			$this->db->join("paquete_detalle", "deta_paqu_id = paqu_id");
			$this->db->where(array("YEAR(DATE(deta_fechaserv))" => $anio));
			if($tipor != "")
				$this->db->where("paqu_tipo", $tipor);
			if($mes != "")
				$this->db->where("MONTH(DATE(deta_fechaserv))", $mes);
			$this->db->group_by("paqu_clie_id");
			$consulta = $this->db->get()->result();
			$seleccion = "<option value=''>* Todas las agencias<option>";
		}
		
		if(count($consulta) > 0){
			foreach($consulta as $i => $row){
				if($row->id == 8)
					$seleccion .= "<option value='".$row->id."' selected='selected'>".$row->nombre."</option>";
				else
					$seleccion .= "<option value='".$row->id."'>".$row->nombre."</option>";
			}
		}
		return $seleccion;
	}
	public function getCobros($tipo, $id, $otro = ""){
		if($otro == ""){
			$this->db->select("cuen_banco banco, movi_monto monto, DATE_FORMAT(movi_fechareg, '%d/%m/%Y') fecha, movi_obs obs");
			$this->db->from("cuenta_movimiento");
			$this->db->join("cuenta","cuen_id = movi_cuen_id");
			$this->db->where(array("movi_ref_id" => $id, "movi_tipo_id" => $tipo));
			$consulta = $this->db->get()->result();
		}else{
			if($otro == "liqu"){
				$liqu = $this->db->select("lpaq_liqu_id")->where("lpaq_paqu_id", $id)->get("liquidacion_paqu")->row()->lpaq_liqu_id;
				
				$liquidacion = $this->db->select("CONCAT('LIQ-',liqu_numero) numero, liqu_cobrodesc desc")->where("liqu_id", $liqu)->get("liquidacion")->row();
				$consulta["ref"] = $liquidacion->numero;
				$consulta["desc"] = $liquidacion->desc;
				
				$this->db->select("cuen_banco banco");
				$this->db->from("cuenta_movimiento");
				$this->db->join("cuenta","cuen_id = movi_cuen_id");
				$this->db->where(array("movi_ref_id" => $liqu, "movi_tipo_id" => $tipo));
				$this->db->order_by("movi_id","DESC");
				$consulta["cobro"] = $this->db->get()->row();
				
			}else{
				$vent_id = $this->db->select("vent_id")->where("paqu_id", $id)->get("venta_paquete")->row()->vent_id;
				
				$venta = $this->db->select("vent_esliquidacion liquidacion, CONCAT(vent_serie,'-',vent_numero) numero, vent_cobrodesc desc")->where("vent_id", $vent_id)->get("venta")->row();
				$consulta["ref"] = $venta->numero;
				$consulta["desc"] = $venta->desc;
				if($venta->liquidacion == 0){
					$this->db->select("cuen_banco banco");
					$this->db->from("cuenta_movimiento");
					$this->db->join("cuenta","cuen_id = movi_cuen_id");
					$this->db->where(array("movi_ref_id" => $vent_id, "movi_tipo_id" => $tipo));
					$this->db->order_by("movi_id","DESC");
					$consulta["cobro"] = $this->db->get()->row();
				}else{
					$liqu_id = $this->db->select("deta_liqu_id")->where("deta_comp_id", $vent_id)->get("venta_liquidaciondetalle")->row()->deta_liqu_id;
					$this->db->select("cuen_banco banco");
					$this->db->from("cuenta_movimiento");
					$this->db->join("cuenta","cuen_id = movi_cuen_id");
					$this->db->where(array("movi_ref_id" => $liqu_id, "movi_tipo_id" => 8));
					$this->db->order_by("movi_id","DESC");
					$consulta["cobro"] = $this->db->get()->row();	
				}
			}
		}
		return $consulta;
	}
	public function getImagenes($paqu_id){
		$this->db->select("paim_imagen direccion");
		$this->db->from("paquete_imagen");
		$this->db->where("paim_paqu", $paqu_id);
		$consulta = $this->db->get()->result();
		return $consulta;
	}
	public function get_detasOrdFlot($seleccionados,$save = false){
        $this->db->select("sepr_id as id, sepr_prov_id as prov_id, prov_rsocial as prov_name, DATE_FORMAT(sepr_fecha, '%d/%m/%Y') as fecha, DATE_FORMAT(sepr_hora, '%h:%i %p') as hora, tipo_denom as tipo, sepr_tipo as tipo_id, IF(sepr_orde_id IS NOT NULL,orde_servicio,deta_servicio) as servicio, sepr_combu_galones as cantidad, sepr_combu_precio as precio, sepr_combu_total as total, sepr_moneda as moneda, sepr_responsable as responsable, IF(sepr_orde_id IS NOT NULL,CONCAT('ORD-',orde_numero),CONCAT(paqu_prefijo,'-',paqu_numero)) referencia");
        $this->db->from("servicio_proveedor");
        $this->db->join("proveedor", "prov_id = sepr_prov_id");
        $this->db->join("proveedor_tipo", "tipo_id = sepr_tipo");
        $this->db->join("paquete_detalle", "deta_id = sepr_pdet_id","LEFT");
        $this->db->join("paquete", "paqu_id = deta_paqu_id","LEFT");
        $this->db->join("ordenserv", "orde_id = sepr_orde_id","LEFT");
        if(!$save){
            $this->db->where_in("sepr_id", explode(',', $seleccionados));
            $this->db->where("sepr_espagado","0");
        }else{
            $this->db->where_in("sepr_id", $seleccionados);
        }
        return $this->db->get()->result();
    }
}
