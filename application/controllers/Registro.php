<?php
class Registro extends CI_Controller
{
    var $configuracion;
    var $titulos;
    function __construct()
    {
        parent::__construct();
        if (!$this->session->userdata('authorized')) {
            redirect(base_url() . "login");
        }
        $this->usua_id = $this->session->userdata('authorized');
        $this->configuracion = $this->db->query("SELECT * FROM usuario WHERE usua_id='{$this->usua_id}'")->row();
        $this->permisos  = $this->Model_general->getPermisos($this->usua_id);
        $this->editar = $this->permisos[0]->nivel_acceso ?: 0;
        $this->editarconf = $this->permisos[12]->nivel_acceso ?: 0;
        $this->mod_modulosTR = $this->permisos[16]->nivel_acceso ?: 0;
    }


    public function paq_listado()
    {
        $this->load->helper('Funciones');
        $this->load->library('Ssp');
        $this->load->library('Cssjs');
        $json = isset($_GET['json']) ? $_GET['json'] : false;

        $fecha_salida = 'DATE_FORMAT(deta_fechaserv,"%d/%m/%Y")';
        $hora_salida = 'IF(DATE_FORMAT(deta_fechaserv,"%h:%i %p") = "12:00 AM","--:-- --",DATE_FORMAT(deta_fechaserv,"%h:%i %p"))';
        $salida = 'CONCAT(' . $fecha_salida . ',"</br>",' . $hora_salida . ')';
        $fechas = 'CONCAT(' . $salida . '," </br><strong> ",IF(paqu_tipo = "LOCAL","",DATE_FORMAT(deta_fecha_llegada,"%d/%m/%Y")))';

        $grupo = 'CONCAT(paqu_nombre,"</br><strong>",IF(paqu_tipo = "LOCAL","",deta_bus))';

        $servicio = 'CONCAT(serv_descripcion,"</br><strong>",IF(paqu_tipo = "PRIVADO",deta_emba_name,""))';
        $file = 'CONCAT(paqu_prefijo,"-",CHAR(paqu_letra),paqu_numero)';

        $columns = array(
            //array('db' => "paqu_id",            'dt' => 'ID',               "field" => "paqu_id"),
            array('db' => $file,                'dt' => 'FILE',             "field" => $file),
            array('db' => $fechas,              'dt' => 'FECHA',            "field" => $fechas),
            array('db' => $servicio,            'dt' => 'SERVICIO',         "field" => $servicio),
            array('db' => 'deta_pax',           'dt' => 'PAX',              "field" => "deta_pax"),
            array('db' => $grupo,               'dt' => 'GRUPO / NOMBRE',   "field" => $grupo),
            array('db' => 'deta_hotel',        'dt' => 'HOTEL',            "field" => "deta_hotel"),
            array('db' => 'deta_guia',          'dt' => 'GUIA',             "field" => "deta_guia"),
            //array('db' => 'paqu_file',        'dt' => 'File/H',           "field" => "paqu_file"),
            array('db' => 'deta_lunch',         'dt' => 'LUNCH',            "field" => "deta_lunch"),
            array('db' => 'clie_rsocial',       'dt' => 'CLIENTE',          "field" => "clie_rsocial"),
            array('db' => 'paqu_endose',        'dt' => 'ENDOSE',           "field" => "paqu_endose"),
            array('db' => 'deta_descripcion',   'dt' => 'OBSERVACION',      "field" => "deta_descripcion"),
            array('db' => 'deta_escomprobante', 'dt' => 'DT_RowComp',       "field" => "deta_escomprobante"),
            array('db' => 'deta_esliquidacion', 'dt' => 'DT_RowLiqu',       "field" => "deta_esliquidacion"),
            array('db' => 'deta_prioridad',     'dt' => 'DT_RowPrio',       "field" => "deta_prioridad"),
            array('db' => 'deta_esorden',       'dt' => 'DT_RowOrde',       "field" => "deta_esorden"),
            array('db' => 'deta_id',            'dt' => 'DT_RowId',         "field" => "deta_id"),
            array('db' => 'paqu_estado',        'dt' => 'DT_RowEsta',       "field" => "paqu_estado"),
            array('db' => 'paqu_tipo',          'dt' => 'DT_RowTipo',       "field" => "paqu_tipo"),
            array('db' => 'deta_color',         'dt' => 'DT_RowColor',      "field" => "deta_color"),
            array('db' => 'paqu_id',            'dt' => 'DT_PaquId',        "field" => "paqu_id"),
            array('db' => 'deta_hotelchk',      'dt' => 'DT_htl',        "field" => "deta_hotelchk"),
            array('db' =>  $this->editar,       'dt' => 'DT_Permisos',        "field" => $this->editar),
            array('db' =>  $this->mod_modulosTR, 'dt' => 'mod_modulosTR',        "field" =>  $this->mod_modulosTR),
            array('db' =>  'deta_fechaserv',       'dt' => 'fechaSimple',        "field" => 'deta_fechaserv'),
            array('db' =>  'paqu_habilitado',       'dt' => 'paqu_habilitado',        "field" => 'paqu_habilitado'),

        );

        if ($json) {

            $json = isset($_GET['json']) ? $_GET['json'] : false;

            $table = 'paquete_detalle';
            $primaryKey = 'deta_id';

            $sql_details = array(
                'user' => $this->db->username,
                'pass' => $this->db->password,
                'db' => $this->db->database,
                'host' => $this->db->hostname
            );

            $condiciones = array();

            $joinQuery = "FROM paquete_detalle LEFT JOIN paquete ON deta_paqu_id = paqu_id LEFT JOIN cliente ON clie_id = paqu_clie_id JOIN servicio on deta_serv_id = serv_id LEFT JOIN hotel ON hote_id = deta_hote_id";
            $where = "";
            if (!empty($_POST['desde']) && !empty($_POST['hasta'])) {
                //$condiciones[] = "(deta_fechaserv >='".$_POST['desde']." 00:00:00"."' AND deta_fechaserv <='".$_POST['hasta']." 23:59:00"."' OR deta_fecha_llegada >='".$_POST['desde']."' AND deta_fecha_llegada <='".$_POST['hasta']."')";
                $condiciones[] = "deta_fechaserv >='" . $_POST['desde'] . " 00:00:00" . "' AND deta_fechaserv <='" . $_POST['hasta'] . " 23:59:00" . "'";
            }
            /*
            if (!empty($_POST['search']))
                $condiciones[] = "paqu_nombre LIKE '%".$_POST['search']."%'";
                */

            if (!empty($_POST['tipo']))
                $condiciones[] = "paqu_tipo='" . $_POST['tipo'] . "'";
            if (!empty($_POST['moneda']))
                $condiciones[] = "paqu_moneda='" . $_POST['moneda'] . "'";
            if (!empty($_POST['estado']))
                $condiciones[] = "paqu_estado='" . $_POST['estado'] . "'";
            if (!empty($_POST['contacto']))
                $condiciones[] = "paqu_clie_id='" . $_POST['contacto'] . "'";
            if (!empty($_POST['paqu_cobrado']))
                $condiciones[] = "paqu_escobrado='" . $_POST['paqu_cobrado'] . "'";
            /*
            if (!empty($_POST['guia']))
                $condiciones[] = "deta_guia_id='".$_POST['guia']."'";            
            */
            if (!empty($_POST['serv_ids'])) {
                $array = implode(',', array_map('intval', explode(',', $_POST['serv_ids'])));
                $condiciones[] = "deta_serv_id IN (" . $array . ")";
            }
            if (!empty($_POST['usuario']))
                $condiciones[] = "paqu_usuario='" . $_POST['usuario'] . "'";
            if (!empty($_POST['det_orde']))
                if ($_POST['det_orde'] == "SI") $condiciones[] = "deta_esorden='1'";
                else $condiciones[] = "deta_esorden='0'";
            if (!empty($_POST['det_comp']))
                if ($_POST['det_comp'] == "SI") $condiciones[] = "deta_escomprobante='1'";
                else $condiciones[] = "deta_escomprobante='0'";
            if (!empty($_POST['det_liqu']))
                if ($_POST['det_liqu'] == "SI") $condiciones[] = "deta_esliquidacion='1'";
                else $condiciones[] = "deta_esliquidacion='0'";

            $poststr = serialize($_POST);
            $_SESSION['poststr'] = $poststr;


            $where = count($condiciones) > 0 ? implode(' AND ', $condiciones) : "";
            echo json_encode(
                $this->ssp->simple($_POST, $sql_details, $table, $primaryKey, $columns, $joinQuery, $where)
            );
            exit(0);
        }



        //print_r($datos['poststr']);

        $datos["usuario"] = $this->Model_general->getOptions('usuario', array("usua_id", "usua_nombres"), '* Usuario');
        $datos["contacto"] = $this->Model_general->getOptions('cliente', array("clie_id", "clie_rsocial"), '* Contacto');
        //$datos["guia"] = $this->Model_general->getOptions('guia', array("guia_id", "guia_nombres"),'* Guia');
        $datos["servicio"] = $this->Model_general->getOptions('servicio', array("serv_id", "serv_descripcion"), '* Servicio');
        $datos["moneda"] = array_merge(array('' => '* Monedas'), $this->Model_general->enum_valores('paquete', 'paqu_moneda'));
        $datos["tipo"] = array_merge(array('' => '* Tipo reserva'), $this->Model_general->enum_valores('paquete', 'paqu_tipo'));
        $datos["estado"] = array_merge(array('' => '* Estado'), $this->Model_general->enum_valores('paquete', 'paqu_estado'));
        $datos['columns'] = $columns;

        $usua_tipo = $this->db->where("usua_id", $this->usua_id)->get("usuario")->row()->usua_tipo;
        if ($usua_tipo == '1')
            $datos['usua_tipo'] = "LOCAL";
        else if ($usua_tipo == '2')
            $datos['usua_tipo'] = "RECEPTIVO";
        else if ($usua_tipo == '0')
            $datos['usua_tipo'] = "";
        //$datos["servicios"] = $this->Model_general->getData("servicio", array("serv_id","serv_abrev"));

        $nabrev = array("TQV", "TQN", "AMN", "URA", "URM", "URP", "SLL", "KYC");
        $servs = $this->db->select("serv_id, serv_abrev")->where_in("serv_abrev", $nabrev)->get("servicio")->result();
        $datos["servicios"] = $servs;
        $datos["servicios"] = $servs;
        $datos["servicios"] = $servs;
        $datos["gen_orden"] = array("" => "* Gen. Orden", "SI" => "SI", "NO" => "NO");
        $datos["gen_liqu"] = array("" => "* Gen. Liquidacion", "SI" => "SI", "NO" => "NO");
        $datos["gen_comp"] = array("" => "* Gen. Comprobante", "SI" => "SI", "NO" => "NO");


        $datos['poststr'] = isset($_SESSION['poststr']) ? unserialize($_SESSION['poststr']) : array(
            'usuario' => '',
            'contacto' => '',
            'scontacto' => '',
            'estado' => '',
            'usuario' => '',
            'det_orde' => '',
            'det_comp' => '',
            'det_liqu' => '',
            'search' => array('value' => ''),
            'tipo' => $usua_tipo,
            'serv_ids' => '',
            'desde' => date('Y-m-d', time() - 24 * 60 * 60 * 7),
            'hasta' => date('Y-m-d'),
        );

        $this->cssjs->add_js(base_url() . 'assets/js/Registro/listado.js?v=1.11', false, false);
        $this->cssjs->add_js(base_url() . 'assets/js/calendar.js?v=2', false, false);
        $this->load->view('header');
        $this->load->view('menu');
        $this->load->view($this->router->fetch_class() . '/' . $this->router->fetch_method(), $datos);
        $this->load->view('footer');
    }
    public function ejemplosnoma()
    {
    }
    public function reg_auxiliar()
    {
        $this->load->helper('Funciones');
        $this->load->library('Ssp');
        $this->load->library('Cssjs');
        $json = isset($_GET['json']) ? $_GET['json'] : false;


        $fecha = 'DATE_FORMAT(deta_fechaserv,"%Y-%m-%d")';
        $fechacobro = 'DATE_FORMAT(paqu_cobrofecha,"%d/%m/%Y")';
        $pax = '(SELECT GROUP_CONCAT(deta_pax) FROM paquete_detalle WHERE deta_paqu_id=paqu_id)';
        $grupo = 'paqu_nombre';
        $servicio = '(SELECT GROUP_CONCAT(deta_servicio) FROM paquete_detalle WHERE deta_paqu_id=paqu_id)';
        $hotel = '(SELECT GROUP_CONCAT(deta_hotel) FROM paquete_detalle WHERE deta_paqu_id=paqu_id)';
        $guia = '(SELECT GROUP_CONCAT(deta_guia) FROM paquete_detalle WHERE deta_paqu_id=paqu_id)';
        $file = 'CONCAT(paqu_prefijo,"-",paqu_numero)';
        $cobrado = "IF(paqu_escobrado = '0','<font class=red><b>PENDIENTE','<font class=green><b>COBRADO')";
        $pagado = "IF(paqu_espagado = '0','<font class=red><b>PENDIENTE','<font class=green><b>PAGADO')";
        $obs = '(SELECT GROUP_CONCAT(deta_descripcion) FROM paquete_detalle WHERE deta_paqu_id=paqu_id)';
        //$saldo = "IF(paqu_escobrado = '0',CONCAT('<font class=red><b>',(paqu_total - paqu_cobrado)),'<font class=green><b>COBRADO')";
        $saldo = "IF(paqu_escobrado = '0',CONCAT('<font class=red><b>',(paqu_total - paqu_cobrado)),CONCAT('<font class=green><b>',IF((paqu_total - paqu_cobrado) < 0,(paqu_total - paqu_cobrado),'COBRADO')))";

        $liqui = "(SELECT CONCAT('LIQ-',liqu_numero) from liquidacion_paqu join liquidacion on lpaq_liqu_id = liqu_id where lpaq_paqu_id = paqu_id)";
        $comp = "(SELECT CONCAT(V.vent_serie,'-',V.vent_numero) from venta_paquete VP join venta V on V.vent_id = VP.vent_id where VP.paqu_id = paqu_id)";
        $llevado_a = "IF(paqu_esliquidacion = 1," . $liqui . ", IF(paqu_escomprobante = 1," . $comp . ",''))";

        $columns = array(
            array('db' => 'paqu_id',            'dt' => 'ID',               "field" => "paqu_id"),
            array('db' => $file,                'dt' => 'FILE',             "field" => $file),
            array('db' => $fecha,                 'dt' => 'FECHA',            "field" => $fecha),
            array('db' => $servicio,            'dt' => 'SERVICIO',         "field" => $servicio),
            array('db' => $pax,                 'dt' => 'PAX',              "field" => $pax),
            array('db' => $grupo,               'dt' => 'GRUPO / NOMBRE',   "field" => $grupo),
            array('db' => $hotel,               'dt' => 'HOTEL',            "field" => $hotel),
            array('db' => $guia,                'dt' => 'GUIA',             "field" => $guia),
            array('db' => 'paqu_file',          'dt' => 'FILE/R',             "field" => 'paqu_file'),
            array('db' => 'paqu_clie_rsocial',  'dt' => 'CONTACTO',           "field" => 'paqu_clie_rsocial'),
            array('db' => 'paqu_endose',        'dt' => 'ENDOSE',           "field" => "paqu_endose"),
            array('db' => 'paqu_moneda',        'dt' => 'MONEDA',           "field" => "paqu_moneda"),
            array('db' => 'paqu_total',         'dt' => 'TOTAL',            "field" => "paqu_total"),
            array('db' => 'paqu_cobrado',       'dt' => 'COBRADO',          "field" => 'paqu_cobrado'),
            array('db' => $saldo,               'dt' => 'SALDO',            "field" => $saldo),
            array('db' => $obs,                  'dt' => 'OBSERVACIONES',    "field" => $obs),
            array('db' => $fechacobro,          'dt' => 'C/FECHA',    "field" => $fechacobro),
            array('db' => "paqu_cobrodesc",     'dt' => 'C/OBS',    "field" => "paqu_cobrodesc"),
            //array('db' => $llevado_a,              'dt' => 'ENVIADO A',             "field" => $llevado_a),
            array('db' => "LOWER(usua_nombres)",     'dt' => 'USUARIO',    "field" => "LOWER(usua_nombres)"),
            array('db' => 'paqu_escobrado',     'dt' => 'DT_Cobro',         "field" => "paqu_escobrado"),
            array('db' => 'paqu_espagado',      'dt' => 'DT_Pago',          "field" => "paqu_espagado"),
            array('db' => 'paqu_escomprobante', 'dt' => 'DT_RowComp',       "field" => "paqu_escomprobante"),
            array('db' => 'paqu_esliquidacion', 'dt' => 'DT_RowLiqu',       "field" => "paqu_esliquidacion"),
            array('db' => 'deta_esorden',       'dt' => 'DT_RowOrde',       "field" => "deta_esorden"),
            array('db' => 'paqu_id',            'dt' => 'DT_RowId',         "field" => "paqu_id"),
            array('db' => 'paqu_tipo',          'dt' => 'DT_RowTipo',       "field" => "paqu_tipo"),
            array('db' => 'paqu_estado',        'dt' => 'DT_PEstado',       "field" => "paqu_estado"),
            array('db' => 'paqu_id',            'dt' => 'DT_PaquId',        "field" => "paqu_id"),
            array('db' => $this->editar,        'dt' => 'DT_Permisos',      "field" => $this->editar),
            array('db' =>  $this->usua_id,       'dt' => 'DT_UsuaId',        "field" => $this->usua_id)
        );

        if ($json) {

            $json = isset($_GET['json']) ? $_GET['json'] : false;

            $table = 'paquete_detalle';
            $primaryKey = 'deta_id';

            $sql_details = array(
                'user' => $this->db->username,
                'pass' => $this->db->password,
                'db' => $this->db->database,
                'host' => $this->db->hostname
            );

            $condiciones = array();

            //$joinQuery = "FROM paquete_detalle LEFT JOIN paquete ON deta_paqu_id = paqu_id LEFT JOIN cliente ON clie_id = paqu_clie_id JOIN servicio on deta_serv_id = serv_id LEFT JOIN hotel ON hote_id = deta_hote_id LEFT JOIN usuario ON usua_id = paqu_usuario";
            $joinQuery = "FROM paquete_detalle 
							LEFT JOIN paquete ON deta_paqu_id = paqu_id 
							LEFT JOIN cliente ON clie_id = paqu_clie_id 
							JOIN servicio on deta_serv_id = serv_id 
							LEFT JOIN hotel ON hote_id = deta_hote_id 
							LEFT JOIN usuario ON paqu_usuario = usua_id";
            $where = "";
            if (!empty($_POST['desde']) && !empty($_POST['hasta'])) {
                $condiciones[] = "deta_fechaserv >='" . $_POST['desde'] . " 00:00:00" . "' AND deta_fechaserv <='" . $_POST['hasta'] . " 23:59:00" . "'";
            }
            /*
            if (!empty($_POST['busqueda'])){
                $condiciones[] = "paqu_nombre LIKE '%".$_POST['busqueda']."%' OR ".$file." LIKE '%".$_POST['busqueda']."%' OR paqu_file LIKE '%".$_POST['busqueda']."%' OR paqu_clie_rsocial LIKE '%".$_POST['busqueda']."%' OR paqu_endose LIKE '%".$_POST['busqueda']."%'";
            }
			*/
            if (!empty($_POST['tipo']))
                $condiciones[] = "paqu_tipo='" . $_POST['tipo'] . "'";
            if (!empty($_POST['moneda']))
                $condiciones[] = "paqu_moneda='" . $_POST['moneda'] . "'";
            if (!empty($_POST['estado']))
                $condiciones[] = "paqu_estado='" . $_POST['estado'] . "'";
            if (!empty($_POST['contacto']))
                $condiciones[] = "paqu_clie_id='" . $_POST['contacto'] . "'";
            /*
            if (!empty($_POST['guia']))
                $condiciones[] = "deta_guia_id='".$_POST['guia']."'";            
            */
            if (!empty($_POST['serv_ids'])) {
                $array = implode(',', array_map('intval', explode(',', $_POST['serv_ids'])));
                $condiciones[] = "deta_serv_id IN (" . $array . ")";
            }

            if (!empty($_POST['paqu_cobrado']))
                if ($_POST['paqu_cobrado'] == "SI") $condiciones[] = "paqu_escobrado='1'";
                else $condiciones[] = "paqu_escobrado='0'";
            if (!empty($_POST['det_comp']))
                if ($_POST['det_comp'] == "SI") $condiciones[] = "paqu_escomprobante='1'";
                else $condiciones[] = "paqu_escomprobante='0'";
            if (!empty($_POST['det_liqu']))
                if ($_POST['det_liqu'] == "SI") $condiciones[] = "paqu_esliquidacion='1'";
                else $condiciones[] = "paqu_esliquidacion='0'";

            $where = count($condiciones) > 0 ? implode(' AND ', $condiciones) : "";
            echo json_encode(
                $this->ssp->simple($_POST, $sql_details, $table, $primaryKey, $columns, $joinQuery, $where, "paqu_id")
            );
            exit(0);
        }
        $datos["usuario"] = $this->Model_general->getOptions('usuario', array("usua_id", "usua_nombres"), '* Usuario');
        $datos["contacto"] = $this->Model_general->getOptions('cliente', array("clie_id", "clie_rcomercial"), '* Contacto');
        //$datos["guia"] = $this->Model_general->getOptions('guia', array("guia_id", "guia_nombres"),'* Guia');
        $datos["servicio"] = $this->Model_general->getOptions('servicio', array("serv_id", "serv_descripcion"));
        $datos["moneda"] = array_merge(array('' => '* Monedas'), $this->Model_general->enum_valores('paquete', 'paqu_moneda'));
        $datos["tipo"] = array_merge(array('' => '* Tipo reserva'), $this->Model_general->enum_valores('paquete', 'paqu_tipo'));
        $datos["estado"] = array_merge(array('' => '* Estado'), $this->Model_general->enum_valores('paquete', 'paqu_estado'));
        $datos["servicios"] = $this->Model_general->getData("servicio", array("serv_id", "serv_abrev"));
        $datos['columns'] = $columns;

        $usua_tipo = $this->db->where("usua_id", $this->usua_id)->get("usuario")->row()->usua_tipo;
        $datos["gen_liqu"] = array("" => "* Gen. Liquidacion", "SI" => "SI", "NO" => "NO");
        $datos["gen_comp"] = array("" => "* Gen. Comprobante", "SI" => "SI", "NO" => "NO");
        $datos["gen_cobrado"] = array("" => "* Cobrado", "SI" => "SI", "NO" => "NO");
        if ($usua_tipo == '1')
            $datos['usua_tipo'] = "LOCAL";
        else if ($usua_tipo == '2')
            $datos['usua_tipo'] = "RECEPTIVO";
        else if ($usua_tipo == '0')
            $datos['usua_tipo'] = "";
        $datos['poststr'] = isset($_SESSION['poststr']) ? unserialize($_SESSION['poststr']) : array(
            'usuario' => '',
            'contacto' => '',
            'scontacto' => '',
            'estado' => '',
            'usuario' => '',
            'det_orde' => '',
            'det_comp' => '',
            'det_liqu' => '',
            'search' => array('value' => ''),
            'tipo' => $usua_tipo,
            'serv_ids' => '',
            'desde' => date('Y-m-d', time() - 24 * 60 * 60 * 7),
            'hasta' => date('Y-m-d'),
        );

        $this->cssjs->add_js(base_url() . 'assets/js/Registro/listado_aux.js?v=2.4', false, false);
        $this->load->view('header');
        $this->load->view('menu');
        $this->load->view($this->router->fetch_class() . '/' . $this->router->fetch_method(), $datos);
        $this->load->view('footer');
    }
    public function reg_pagos()
    {
        $this->load->helper('Funciones');
        $this->load->library('Ssp');
        $this->load->library('Cssjs');
        $json = isset($_GET['json']) ? $_GET['json'] : false;


        $fecha = 'DATE_FORMAT(sepr_fecha,"%d/%m/%Y")';
        $hora = 'DATE_FORMAT(sepr_hora,"%h:%i %p")';
        $estado = "IF(sepr_espagado = '0','<font class=red><strong>PENDIENTE','<font class=green><b>PAGADO')";
        $servicio = 'IF(sepr_orde_id IS NOT NULL,orde_servicio,deta_servicio)';
        //$proveedor = "CONCAT(emp_rsocial,' - ',prov_rsocial)";
        $saldo = "IF(sepr_espagado = '0',CONCAT('<font class=red><b>',(sepr_total - sepr_pagado)),CONCAT('<font class=green><b>',IF((sepr_total - sepr_pagado) < 0,(sepr_total - sepr_pagado),'PAGADO')))";
        $fecha_pago = 'DATE_FORMAT(sepr_pagofecha,"%d/%m/%Y")';
        $file = 'IF(sepr_orde_id IS NOT NULL,CONCAT("ORD-",orde_numero),CONCAT(paqu_prefijo,"-",paqu_numero))';

        $columns = array(
            array('db' => 'sepr_id',        'dt' => 'ID',           "field" => "sepr_id"),
            array('db' => $file,            'dt' => 'FILE',         "field" => $file),
            array('db' => $fecha,           'dt' => 'FECHA',        "field" => $fecha),
            array('db' => $hora,            'dt' => 'HORA',         "field" => $hora),
            array('db' => "emp_rsocial",    'dt' => 'PROVEEDOR',    "field" => "emp_rsocial"),
            array('db' => "prov_rsocial",   'dt' => 'CONTACTO',     "field" => "prov_rsocial"),
            array('db' => $servicio,          'dt' => 'SERVICIO',     "field" => $servicio),
            array('db' => 'sepr_servicio',  'dt' => 'OBSERVACION',  "field" => "sepr_servicio"),
            array('db' => 'sepr_guia',      'dt' => 'GUIA',         "field" => "sepr_guia"),
            array('db' => 'sepr_precio',    'dt' => 'PREC',        "field" => "sepr_precio"),
            array('db' => 'sepr_cantidad',  'dt' => 'CANT',        "field" => "sepr_cantidad"),
            array('db' => 'sepr_total',     'dt' => 'TOTAL',        "field" => "sepr_total"),
            array('db' => $saldo,             'dt' => 'SALDO',        "field" => $saldo),
            array('db' => 'sepr_moneda',    'dt' => 'MONEDA',       "field" => "sepr_moneda"),
            //array('db' => $estado,        'dt' => 'ESTADO',       "field" => $estado),
            array('db' => $fecha_pago,      'dt' => 'FECHA P',         "field" => $fecha_pago),
            array('db' => "sepr_pagodesc",  'dt' => 'PAGO DESC',    "field" => "sepr_pagodesc"),
            array('db' => 'sepr_id',        'dt' => 'DT_RowId',     "field" => "sepr_id"),
            array('db' => 'sepr_espagado',  'dt' => 'DT_RowEstado', "field" => "sepr_espagado"),
            array('db' => 'sepr_esorden',   'dt' => 'DT_RowOrden',  "field" => "sepr_esorden"),
            array('db' => 'sepr_pagado',    'dt' => 'DT_RowPagado', "field" => "sepr_pagado"),
            array('db' => $this->editar,    'dt' => 'DT_Permisos',  "field" => $this->editar),
            array('db' =>  $this->usua_id,  'dt' => 'DT_UsuaId',    "field" => $this->usua_id)
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

            $joinQuery = "FROM servicio_proveedor 
							JOIN proveedor ON prov_id = sepr_prov_id AND prov_combustible = 'NO' 
							JOIN proveedor_empresa ON emp_id = prov_emp_id 
							LEFT JOIN ordenserv ON orde_id = sepr_orde_id 
							LEFT JOIN paquete_detalle ON deta_id = sepr_pdet_id 
							LEFT JOIN paquete ON paqu_id = deta_paqu_id";
            $where = "";
            if (!empty($_POST['desde']) && !empty($_POST['hasta'])) {
                $condiciones[] = "sepr_fecha >='" . $_POST['desde'] . "' AND sepr_fecha <='" . $_POST['hasta'] . "'";
            }
            if (!empty($_POST['moneda']))
                $condiciones[] = "sepr_moneda='" . $_POST['moneda'] . "'";
            if (!empty($_POST['estado'])) {
                $ind = ($_POST['estado'] == '-1') ? 0 : 1;
                $condiciones[] = "sepr_espagado='" . $ind . "'";
            }
            if (!empty($_POST['proveedor']))
                $condiciones[] = "emp_id='" . $_POST['proveedor'] . "'";
            if (!empty($_POST['contacto']))
                $condiciones[] = "prov_id='" . $_POST['contacto'] . "'";

            $where = count($condiciones) > 0 ? implode(' AND ', $condiciones) : "";
            echo json_encode(
                $this->ssp->simple($_POST, $sql_details, $table, $primaryKey, $columns, $joinQuery, $where)
            );
            exit(0);
        }


        //$datos["guia"] = $this->Model_general->getOptions('guia', array("guia_id", "guia_nombres"),'* Guia');
        $datos["proveedor"] = $this->Model_general->getOptions('proveedor_empresa', array("emp_id", "emp_rsocial"), '* Proveedor');
        $datos["moneda"] = array_merge(array('' => '* Monedas'), $this->Model_general->enum_valores('paquete', 'paqu_moneda'));
        $datos["estado"] = array("" => "* Estado", "-1" => "Pendiente", "1" => "Pagado");
        $datos['columns'] = $columns;

        $usua_tipo = $this->db->where("usua_id", $this->usua_id)->get("usuario")->row()->usua_tipo;

        $this->cssjs->add_js(base_url() . 'assets/js/Registro/listado_pagos.js?v=1.6', false, false);
        //$this->cssjs->add_js(base_url().'assets/js/calendar.js',false,false);
        $this->load->view('header');
        $this->load->view('menu');
        $this->load->view($this->router->fetch_class() . '/' . $this->router->fetch_method(), $datos);
        $this->load->view('footer');
    }
    public function getProveedor($emp_id = "")
    {
        $datos = $this->db->where("prov_emp_id", $emp_id)->get("proveedor")->result();
        $html = "<option value=''>* Contactos</option>";
        if (COUNT($datos) > 0) {
            foreach ($datos as $row) {
                $html .= "<option value='" . $row->prov_id . "'>" . $row->prov_rsocial . "</option>";
            }
        }
        $datos["html"] = $html;
        echo json_encode($datos);
    }

    public function paq_crear($local = false)
    {
        $this->load->helper('Funciones');
        $this->load->model("Model_general");

        $paquete = array(
            'paqu_id' => '',
            'paqu_tipo' => 'RECEPTIVO',
            'paqu_clie_id' => '',
            'paqu_clie_rsocial' => '',
            'paqu_clie_codigo' => '',
            'paqu_nombre' => '',
            'paqu_fecha' => date('d/m/Y'),
            'paqu_estado' => 'CONFIRMADO',
            'paqu_moneda' => "",
            'paqu_file' => "",
            'paqu_adic' => "",
            'paqu_total' => "0.00",
            'paqu_subtotal' => "0.00",
            'paqu_igv' => "0.00",
            'paqu_adic_val' => "0.00",
            'paqu_desc' => "",
            'paqu_igvafect' => "NO",
            'paqu_endose_id' => '',
            'paqu_endose' => '',
            'paqu_pax' => '',
            'paqu_cobrado_pax' => '0.00',
            'paqu_adic' => ""
        );
        $datos["moneda"] = $this->Model_general->enum_valores('paquete', 'paqu_moneda');
        $datos["estado"] = $this->Model_general->enum_valores('paquete', 'paqu_estado');
        $datos["tipo"] = $this->Model_general->enum_valores('paquete', 'paqu_tipo');
        $datos["prioridad"] = array("1" => "Normal", "2" => "Media", "3" => "Alta");
        $datos["tipo_serv"] = $this->Model_general->enum_valores('paquete_detalle', 'deta_xcta');
        $datos["hotel"] = $this->Model_general->getOptions('hotel', array("hote_id", "hote_nombre"), '* Hotel');
        $datos["tipo_prov"] = $this->Model_general->getOptions('proveedor_tipo', array("tipo_id", "tipo_denom"), '* Tipo');
        $datos["contacto"] = $this->Model_general->getOptionsWhereOr('cliente', array("clie_id", "clie_rsocial"), array("clie_reserv_tipo" => 'RECEPTIVO'), array("clie_reserv_tipo" => 'AMBOS'), '* Contacto');
        $datos["servicio"] = $this->Model_general->getOptionsWhereOr('servicio', array("serv_id", "serv_descripcion"), array("serv_tipo_reserv" => 'RECEPTIVO'), array("serv_tipo_reserv" => 'AMBOS'), '* Servicio');

        $datos["paquete"] = (object)$paquete;
        $datos["detas"] = json_encode(array());
        $datos["imagenes"] = array();
        /*
        for ($i=65; $i <= 90; $i++) { 
            echo chr($i)."<br>";    
        }
        exit(0);
        */

        $datos['titulo'] = "Registrar paquete";

        if ($local != false) {
            return $datos;
        } else {
            $this->load->library('Ssp');
            $this->load->library('Cssjs');
            $this->cssjs->add_js(base_url() . 'assets/js/Registro/form.js?v=2.8', false, false);
            $this->load->view('header');
            $this->load->view('menu');
            $this->load->view('Registro/formulario', $datos);
            $this->load->view('footer');
        }
    }
    public function paq_crear_local($id = '')
    {
        if ($id != '') {
            $paquete = $this->db->where("paqu_id", $id)->get("paquete")->row();
            $paquete->paqu_fecha = date('d/m/Y', strtotime($paquete->paqu_fecha));
            $datos["paquete"] = $paquete;

            $datos["moneda"] = $this->Model_general->enum_valores('paquete', 'paqu_moneda');
            $datos["estado"] = $this->Model_general->enum_valores('paquete', 'paqu_estado');
            $datos["tipo"] = $this->Model_general->enum_valores('paquete', 'paqu_tipo');
            $datos["prioridad"] = array("1" => "Normal", "2" => "Media", "3" => "Alta");
            $datos["tipo_serv"] = $this->Model_general->enum_valores('paquete_detalle', 'deta_xcta');
            $datos["detas"] = json_encode($this->Model_general->getDetaPaqu2($id));
            $datos["imagenes"] = $this->db->where("paim_paqu", $id)->get("paquete_imagen")->result();
            $datos["titulo"] = "Editar reserva: " . $paquete->paqu_prefijo . "-" . $paquete->paqu_numero;
        } else {
            $datos = $this->paq_crear(true);
            $datos["paquete"]->paqu_tipo = 'LOCAL';
            $datos["titulo"] = "Crear reserva local";
        }
        $datos["paqu_id"] = $id;
        $datos["contacto"] = $this->Model_general->getOptionsWhereOr('cliente', array("clie_id", "clie_rsocial"), array("clie_reserv_tipo" => 'LOCAL'), array("clie_reserv_tipo" => 'AMBOS'), '* Contacto');
        $this->load->view('Registro/formulario_local', $datos);
    }
    public function reserva_rapida()
    {
        $datos = $this->paq_crear(true);
        $datos["paquete"]->paqu_tipo = 'LOCAL';
        $datos["paqu_id"] = "";
        $datos["contacto"] = $this->Model_general->getOptions('cliente', array("clie_id", "clie_rsocial"), '* Contacto');
        $this->load->view('Registro/formulario_rapido', $datos);
    }
    public function paq_crear_privado($id = '')
    {
        $this->load->helper('Funciones');
        if ($id != '') {
            $paquete = $this->db->where("paqu_id", $id)->get("paquete")->row();
            $paquete->paqu_fecha = date('d/m/Y', strtotime($paquete->paqu_fecha));
            $datos["paquete"] = $paquete;
            $datos["moneda"] = $this->Model_general->enum_valores('paquete', 'paqu_moneda');
            $datos["estado"] = $this->Model_general->enum_valores('paquete', 'paqu_estado');
            $datos["tipo"] = $this->Model_general->enum_valores('paquete', 'paqu_tipo');
            $datos["prioridad"] = array("1" => "Normal", "2" => "Media", "3" => "Alta");
            $datos["tipo_serv"] = $this->Model_general->enum_valores('paquete_detalle', 'deta_xcta');
            $datos["detas"] = json_encode($this->Model_general->getDetaPaqu2($id));
            $datos["imagenes"] = $this->db->where("paim_paqu", $id)->get("paquete_imagen")->result();
            $datos['titulo'] = "Editar Servicio Privado: " . $paquete->paqu_prefijo . "-" . $paquete->paqu_numero;
        } else {
            $datos = $this->paq_crear(true);
            $datos["paquete"]->paqu_tipo = 'PRIVADO';
            $datos['titulo'] = "Registrar Servicio Privado";
        }
        $serv_privado = $this->db->where("serv_tipo_id", 2)->get("servicio")->row();

        $datos["servicio"] = $serv_privado->serv_descripcion;
        $datos["embarcaciones"] = $this->Model_general->getOptions("tipo_transporte", array("tipo_id", "tipo_nombre"), "* Embarcacion");
        $datos["serv_id"] = $serv_privado->serv_id;

        $this->load->library('Ssp');
        $this->load->library('Cssjs');
        $this->cssjs->add_js(base_url() . 'assets/js/Registro/form_privado.js?v=3.0', false, false);

        $this->load->view('header');
        $this->load->view('menu');
        $this->load->view('Registro/formulario_privado', $datos);
        $this->load->view('footer');
    }
    public function paq_change_estado()
    {
        $estado = $this->input->post("estado");
        $sel = $this->input->post("sel");

        $this->db->select("paquete.paqu_id");
        $this->db->from("paquete_detalle");
        $this->db->join("paquete", "paqu_id = deta_paqu_id");
        $this->db->where_in("deta_id", $sel);
        $paquetes = $this->db->get()->result();

        $this->db->trans_begin();
        foreach ($paquetes as $val) {
            $datas = array("paqu_estado" => $estado);
            $where = array("paqu_id" => $val->paqu_id);
            if (!$this->Model_general->guardar_edit_registro("paquete", $datas, $where)) {
                $this->db->trans_rollback();
                $resp["exito"] = false;
                $resp["mensaje"] = "Ocurrio un error";
            }
        }
        $this->db->trans_commit();
        $resp["exito"] = true;
        $resp["mensaje"] = "Cambios realizados con exito";
        echo json_encode($resp);
    }

    public function paq_edit($id = 0)
    {
        $this->load->helper('Funciones');
        $this->load->model("Model_general");
        $this->load->library('Ssp');
        $this->load->library('Cssjs');
        $this->cssjs->add_js(base_url() . 'assets/js/Registro/form.js?v=2.7', false, false);

        $datos["moneda"] = $this->Model_general->enum_valores('paquete', 'paqu_moneda');
        $datos["estado"] = $this->Model_general->enum_valores('paquete', 'paqu_estado');
        $datos["tipo"] = $this->Model_general->enum_valores('paquete', 'paqu_tipo');
        $datos["tipo_serv"] = $this->Model_general->enum_valores('paquete_detalle', 'deta_xcta');

        $datos["hotel"] = $this->Model_general->getOptions('hotel', array("hote_id", "hote_nombre"), '* Hotel');
        $datos["contacto"] = $this->Model_general->getOptionsWhereOr('cliente', array("clie_id", "clie_rsocial"), array("clie_reserv_tipo" => 'RECEPTIVO'), array("clie_reserv_tipo" => 'AMBOS'), '* Contacto');
        $datos["servicio"] = $this->Model_general->getOptionsWhereOr('servicio', array("serv_id", "serv_descripcion"), array("serv_tipo_reserv" => 'RECEPTIVO'), array("serv_tipo_reserv" => 'AMBOS'), '* Servicio');


        $paquete = $this->db->where("paqu_id", $id)->get("paquete")->row();
        $fecha = date_create($paquete->paqu_fecha);
        $paquete->paqu_fecha = date_format($fecha, 'd/m/Y');

        $datos["paquete"] = $paquete;
        $datos["prioridad"] = array("1" => "Normal", "2" => "Media", "3" => "Alta");
        $detas = $this->Model_general->getDetaPaqu2($id);
        $datos["detas"] = json_encode($detas);
        $datos["imagenes"] = $this->db->where("paim_paqu", $id)->get("paquete_imagen")->result();

        $datos['titulo'] = "Editar Paquete: " . $paquete->paqu_prefijo . "-" . $paquete->paqu_numero;
        $script['js'] = $this->cssjs->generate_js();
        $this->load->view('header', $script);
        $this->load->view('menu');
        $this->load->view('Registro/formulario', $datos);
        $this->load->view('footer');
    }

    private function paq_validarComprobante()
    {
        $this->load->library('Form_validation');
        $this->form_validation->set_rules('cliente', 'Nombre / Razón social', 'required');
        $this->form_validation->set_rules('grupo', 'Grupo', 'required');
        $this->form_validation->set_rules('servicio[]', 'Servicio', 'required');
        $this->form_validation->set_rules('hotel[0]', 'Hotel', 'required');
        $this->form_validation->set_rules('deta_fecha[]', 'Fecha', 'required');

        if ($this->form_validation->run() == FALSE) {
            $this->Model_general->dieMsg(array('exito' => false, 'mensaje' => validation_errors()));
        }
    }
    public function paq_next($tipo)
    {
        $this->db->select('MAX(paqu_numero) as max');
        $this->db->from('paquete');
        $this->db->where('paqu_tipo', $tipo);
        $query = $this->db->get();
        $row = $query->row();
        $numero = $row->max + 1;
        return $numero;
    }
    public function paq_next2()
    {
        $this->db->select('MAX(paqu_numero) as max, paqu_letra as letra');
        $this->db->from('paquete');
        $query = $this->db->get();
        $row = $query;
        if ($row->num_rows() > 0) {
            $row = $row->row();
            if ($row->max == 9999) {
                $resp["numero"] = 0;
                $resp["letra"] = ++$row->letra;
            } else {
                $resp["numero"] = ++$row->max;
                $resp["letra"] = $row->letra;
            }
        } else {
            $resp["numero"] = 1;
            $resp["letra"] = 65;
        }
        return $resp;
    }
    public function eliminar_imagen($id)
    {
        $imagen = $this->db->where("paim_id", $id)->get("paquete_imagen")->row();
        if ($this->Model_general->borrar(array('paim_id' => $id), "paquete_imagen")) {
            $resp["exito"] = true;
            $path_to_file = './assets/img/files/' . $imagen->paim_imagen;
            unlink($path_to_file);
        } else {
            $resp["exito"] = false;
            $resp["mensaje"] = "No se pudo eliminar.";
        }
        $resp["exito"] = true;
        echo json_encode($resp);
    }
    public function upload_files()
    {

        $name_imagen = array();

        if (!empty($_FILES['imagen0'])) {
            $config = [
                "upload_path" => "./assets/img/files",
                "allowed_types" => "png|jpg",
                "file_name" => "img_reserva"
            ];
            $this->load->library("upload", $config);

            $numero = $this->input->post("num_images");
            for ($i = 0; $i < $numero; $i++) {

                $nombre = "imagen" . $i;
                if ($this->upload->do_upload($nombre)) {
                    $data = array("upload_data" => $this->upload->data());
                    $name_imagen[] = $data['upload_data']['file_name'];
                }
            }
        }

        return $name_imagen;
    }

    public function paq_guardar($id = '')
    {
        $this->load->helper('Funciones');

        $this->paq_validarComprobante();
        $cliente = $this->input->post('cliente');
        $clie_rsocial = $this->input->post('clie_rsocial');
        $clie_codigo = $this->input->post('clie_abrev');
        $grupo = $this->input->post('grupo');
        $cobrado_pax = $this->input->post('cobrado_pax');

        $estado = $this->input->post('estado');
        $tipo = $this->input->post('tipop');
        if ($tipo != 'RECEPTIVO') $moneda = $this->input->post('moneda_local');
        else $moneda = $this->input->post('moneda');
        $file = $this->input->post('file');
        $endose = $this->input->post('cont_nombres');

        $subtotal = $this->input->post('total_sub');
        $total = $this->input->post('total_total');
        $igv = $this->input->post('total_igv');
        if ($this->input->post('cls_reserv')) $cls_reserv = "SI";
        else $cls_reserv = "NO";

        $igvafect = ($this->input->post('cons_igv') == 'SI') ? 'SI' : 'NO';

        $fecha = $this->Model_general->fecha_to_mysql($this->input->post('fecha'));

        $paquete = array(
            "paqu_clie_id" => $cliente,
            "paqu_clie_rsocial" => $clie_rsocial,
            "paqu_clie_codigo" => $clie_codigo,
            "paqu_nombre" => $grupo,
            "paqu_moneda" => $moneda,
            "paqu_estado" => $estado,
            "paqu_subtotal" => $subtotal,
            "paqu_total" => $total,
            "paqu_igv" => $igv,
            "paqu_tipo" => $tipo,
            "paqu_file" => $file,
            "paqu_fecha" => $fecha,
            "paqu_endose" => $endose,
            "paqu_igvafect" => $igvafect,
            "paqu_usuario" => $this->usua_id,
            "paqu_rapido" => $cls_reserv,
            "paqu_cobrado_pax" => $cobrado_pax
        );
        $servicio = $this->input->post('servicio');
        $serv_nombre = $this->input->post('serv_nombre');
        $hotel_chk = (isset($_POST["chk_hotel"])) ? $this->input->post('chk_hotel') : "";
        $hotel = $this->input->post('hotel');
        $hotel_nombre = $this->input->post('hotel_nombre');
        $feta_fecha = $this->input->post('deta_fecha');
        if ($tipo == 'LOCAL')
            $deta_fecha_llegada = $this->input->post('deta_fecha');
        else
            $deta_fecha_llegada = $this->input->post('deta_fecha_llegada');

        $feta_hora = $this->input->post('deta_hora');
        $pax = $this->input->post('pax');
        $tipo_serv = $this->input->post('tipo_serv');
        $precio = $this->input->post('precio');
        $deta_total = $this->input->post('importe');
        $detalle = $this->input->post('detalle');



        $lunch = $this->input->post('deta_lunch');
        $lunch_pre = $this->input->post('deta_lunch_pre');
        $adiciones = $this->input->post('adicion');
        $adiciones_val = $this->input->post('adicion_val');
        $descuentos = $this->input->post('descuento');
        $prioridad = $this->input->post('prioridad');
        $descuentos_val = $this->input->post('descuento_val');
        $bus = $this->input->post('bus');
        $bus_salida = $this->input->post('bus_salida');
        $color = $this->input->post('color');

        $t_nombre = $this->input->post('t_nombre');
        $t_monto = $this->input->post('t_monto');

        $posicion = $this->input->post('posicion');
        $adic_nombre = $this->input->post('adic_nombre');
        $adic_precio = $this->input->post('adic_precio');
        $desc_nombre = $this->input->post('desc_nombre');
        $desc_precio = $this->input->post('desc_precio');
        $adic_id = $this->input->post('adic_id');
        $desc_id = $this->input->post('desc_id');

        $prov_paqu_id = $this->input->post('prov_paqu_id');
        $prov_id = $this->input->post('prov_id');
        $prov_cantidad = $this->input->post('prov_cantidad');
        $prov_precio = $this->input->post('prov_precio');
        $prov_tipo = $this->input->post('prov_tipo');
        $prov_moneda = $this->input->post('prov_moneda');
        $prov_descripcion = $this->input->post('prov_descripcion');

        $guia = $this->input->post('deta_guia');
        $emba_id = $this->input->post('embarcacion');
        $emba_name = $this->input->post('emba_name');
        $ruta = $this->input->post('deta_ruta');
        $lugar = $this->input->post('deta_lugar');

        $subserv_id = $this->input->post('sub_servicio');
        $subserv_name = $this->input->post('sub_servname');


        $pa = "";
        $this->db->trans_begin();
        if (empty($id)) {
            $nrop = $this->paq_next($tipo);
            if ($tipo == 'LOCAL')
                $prefijo = "TL";
            else if ($tipo == 'RECEPTIVO')
                $prefijo = "TR";
            else
                $prefijo = "P";

            $addi = array("paqu_numero" => $nrop, "paqu_prefijo" => $prefijo);
            $pa = $prefijo . "-" . $nrop;
            $paquete = array_merge($paquete, $addi);
            if (($meta = $this->Model_general->guardar_registro("paquete", $paquete)) == TRUE) :

                $imagenes = $this->upload_files();

                if (!$this->Model_general->asignar_imagenes($imagenes, $meta["id"])) {
                    $this->db->trans_rollback();
                    $this->Model_general->dieMsg(array('exito' => false, 'mensaje' => 'Error al guardar los datos'));
                }
                foreach ($servicio as $i => $serv) {
                    $datetime = $feta_fecha[$i] . " " . ($feta_hora[$i] != '' ? $feta_hora[$i] : '00:00 AM');
                    $item = array(
                        "deta_paqu_id" => $meta['id'],
                        "deta_subserv_id" => $subserv_id[$i],
                        "deta_subserv_name" => $subserv_name[$i],
                        "deta_hote_id" => ($hotel[$i]) ? $hotel[$i] : null,
                        "deta_hotel" => ($hotel_nombre[$i]) ? $hotel_nombre[$i] : null,
                        "deta_hotelchk" => (isset($hotel_chk[$i])) ? "SI" : "NO",
                        "deta_descripcion" => $detalle[$i],
                        "deta_serv_id" => $serv,
                        "deta_servicio" => $serv_nombre[$i],
                        "deta_xcta" => $tipo_serv[$i],
                        "deta_precio" => $precio[$i],
                        "deta_terc_nombre" => $t_nombre[$i],
                        "deta_terc_monto" => $t_monto[$i],
                        "deta_pax" => $pax[$i],
                        "deta_lunch" => $lunch[$i],
                        "deta_lunch_pre" => $lunch_pre[$i],
                        "deta_total" => $deta_total[$i],
                        "deta_prioridad" => $prioridad[$i],
                        "deta_bus" => $bus[$i],
                        "deta_bus_salida" => $bus_salida[$i],
                        "deta_fecha_llegada" => $this->Model_general->fecha_to_mysql($deta_fecha_llegada[$i]),
                        "deta_fechaserv" => $this->Model_general->datetime_to_mysql($datetime),
                        "deta_guia" => $guia[$i],
                        "deta_emba_id" => (isset($emba_id[$i])) ? $emba_id[$i] : "",
                        "deta_emba_name" => $emba_name[$i],
                        "deta_ruta" => $ruta[$i],
                        "deta_color" => ($color[$i]) ? $color[$i] : '0',
                        "deta_lugar" => $lugar[$i]
                    );

                    if (($det = $this->Model_general->guardar_registro("paquete_detalle", $item)) == TRUE) {

                        if (isset($adic_precio[$i])) {
                            if (!$this->addAdiciones($adic_precio[$i], $adic_nombre[$i], 'ADICION', $det['id'])) {
                                $this->db->trans_rollback();
                                $this->Model_general->dieMsg(array('exito' => false, 'mensaje' => 'Error al guardar los datos'));
                            }
                        }
                        if (isset($desc_precio[$i])) {
                            if (!$this->addAdiciones($desc_precio[$i], $desc_nombre[$i], 'DESCUENTO', $det['id'])) {
                                $this->db->trans_rollback();
                                $this->Model_general->dieMsg(array('exito' => false, 'mensaje' => 'Error al guardar los datos'));
                            }
                        }
                        if (isset($prov_id[$i])) {
                            if (!$this->addProveedores($prov_moneda[$i], $prov_paqu_id[$i], $prov_id[$i], $prov_cantidad[$i], $prov_precio[$i], $prov_tipo[$i], $det['id'], $moneda, $prov_descripcion[$i], $this->Model_general->time_to_mysql($feta_hora[$i]), $this->Model_general->fecha_to_mysql($feta_fecha[$i]), $guia[$i])) {
                                $this->db->trans_rollback();
                                $this->Model_general->dieMsg(array('exito' => false, 'mensaje' => 'Error al guardar los datos'));
                            }
                        }
                    } else {
                        $this->db->trans_rollback();
                        $this->Model_general->dieMsg(array('exito' => false, 'mensaje' => 'Error al guardar los datos'));
                    }
                }
            else :
                $this->db->trans_rollback();
                $this->Model_general->dieMsg(array('exito' => false, 'mensaje' => 'Error al guardar los datos'));
            endif;
            $this->Model_general->add_log("CREAR", 1, "Creación de reserva " . $prefijo . "-" . str_pad($nrop, 6, "0", STR_PAD_LEFT));
            $this->db->trans_commit();
            $id = $meta['id'];
        } else {
            $pa = $this->db->select("CONCAT(paqu_prefijo,'-',paqu_numero) as file")->where("paqu_id", $id)->get("paquete")->row()->file;

            $condicion = "paqu_id = " . $id;
            $detalle_id = $this->input->post('deta_id');
            if ($this->Model_general->guardar_edit_registro("paquete", $paquete, $condicion) == TRUE) :

                $imagenes = $this->upload_files();

                if (!$this->Model_general->asignar_imagenes($imagenes, $id)) {
                    $this->db->trans_rollback();
                    $this->Model_general->dieMsg(array('exito' => false, 'mensaje' => 'Error al guardar los datos'));
                }

                $actuales = $this->db->where('deta_paqu_id', $id)->get("paquete_detalle")->result();

                if (count($actuales) > 0) {
                    foreach ($actuales as $val) {
                        if (!in_array($val->deta_id, $detalle_id)) {
                            $b_cond = array('deta_id' => $val->deta_id);
                            $b_from = "paquete_detalle";
                            if ($this->Model_general->borrar($b_cond, $b_from)) {
                                $this->db->where('padi_pdet_id', $val->deta_id);
                                $this->db->delete('paquete_adicion');
                            }
                        }
                    }
                }

                //////////////////////////////////////////////////////
                foreach ($servicio as $i => $serv) {
                    $condicion_items = "deta_id = " . $detalle_id[$i];
                    $datetime = $feta_fecha[$i] . " " . ($feta_hora[$i] != '' ? $feta_hora[$i] : '00:00 AM');
                    $item = array(
                        "deta_paqu_id" => $id,
                        "deta_subserv_id" => $subserv_id[$i],
                        "deta_subserv_name" => $subserv_name[$i],
                        "deta_hote_id" => ($hotel[$i]) ? $hotel[$i] : null,
                        "deta_hotel" => ($hotel_nombre[$i]) ? $hotel_nombre[$i] : null,
                        "deta_hotelchk" => (isset($hotel_chk[$i])) ? "SI" : "NO",
                        "deta_descripcion" => $detalle[$i],
                        "deta_serv_id" => $serv,
                        "deta_servicio" => $serv_nombre[$i],
                        "deta_xcta" => $tipo_serv[$i],
                        "deta_precio" => $precio[$i],
                        "deta_terc_nombre" => $t_nombre[$i],
                        "deta_terc_monto" => $t_monto[$i],
                        "deta_pax" => $pax[$i],
                        "deta_lunch" => $lunch[$i],
                        "deta_lunch_pre" => $lunch_pre[$i],
                        "deta_total" => $deta_total[$i],
                        "deta_prioridad" => $prioridad[$i],
                        "deta_bus" => $bus[$i],
                        "deta_bus_salida" => $bus_salida[$i],
                        "deta_fecha_llegada" => $this->Model_general->fecha_to_mysql($deta_fecha_llegada[$i]),
                        "deta_fechaserv" => $this->Model_general->datetime_to_mysql($datetime),
                        "deta_guia" => $guia[$i],
                        "deta_emba_id" => (isset($emba_id[$i])) ? $emba_id[$i] : "",
                        "deta_emba_name" => $emba_name[$i],
                        "deta_ruta" => $ruta[$i],
                        "deta_color" => ($color[$i]) ? $color[$i] : '0',
                        "deta_lugar" => $lugar[$i]
                    );
                    if (empty($detalle_id[$i])) {
                        if (($det = $this->Model_general->guardar_registro("paquete_detalle", $item)) == TRUE) {
                            if (isset($adic_precio[$i])) {
                                if (!$this->addAdiciones($adic_precio[$i], $adic_nombre[$i], 'ADICION', $det["id"])) {
                                    $this->db->trans_rollback();
                                    $this->Model_general->dieMsg(array('exito' => false, 'mensaje' => 'Error al guardar los datos'));
                                }
                            }
                            if (isset($desc_precio[$i])) {
                                if (!$this->addAdiciones($desc_precio[$i], $desc_nombre[$i], 'DESCUENTO', $det["id"])) {
                                    $this->db->trans_rollback();
                                    $this->Model_general->dieMsg(array('exito' => false, 'mensaje' => 'Error al guardar los datos'));
                                }
                            }
                            if (isset($prov_id[$i])) {
                                if (!$this->addProveedores($prov_moneda[$i], $prov_paqu_id[$i], $prov_id[$i], $prov_cantidad[$i], $prov_precio[$i], $prov_tipo[$i], $det["id"], $moneda, $prov_descripcion[$i], $this->Model_general->time_to_mysql($feta_hora[$i]), $this->Model_general->fecha_to_mysql($feta_fecha[$i]), $guia[$i])) {
                                    $this->db->trans_rollback();
                                    $this->Model_general->dieMsg(array('exito' => false, 'mensaje' => 'Error al guardar los datos'));
                                }
                            }
                        } else {
                            $this->db->trans_rollback();
                            $this->Model_general->dieMsg(array('exito' => false, 'mensaje' => 'Error al guardar los datos'));
                        }
                    } else {
                        if ($this->Model_general->guardar_edit_registro("paquete_detalle", $item, $condicion_items) == true) {


                            $this->db->where('padi_pdet_id', $detalle_id[$i]);
                            if (isset($adic_id[$i]))
                                $this->db->where_not_in('padi_id', $adic_id[$i]);
                            if (isset($desc_id[$i]))
                                $this->db->where_not_in('padi_id', $desc_id[$i]);
                            $this->db->delete('paquete_adicion');

                            if (isset($prov_paqu_id[$i])) {
                                $this->db->where('sepr_pdet_id', $detalle_id[$i]);
                                $this->db->where_not_in('sepr_id', $prov_paqu_id[$i]);
                                $this->db->delete('servicio_proveedor');
                            }

                            if (isset($adic_precio[$i])) {
                                if (!$this->addAdiciones($adic_precio[$i], $adic_nombre[$i], 'ADICION', $detalle_id[$i], $adic_id[$i])) {
                                    $this->db->trans_rollback();
                                    $this->Model_general->dieMsg(array('exito' => false, 'mensaje' => 'Error al guardar los datos'));
                                }
                            }
                            if (isset($desc_precio[$i])) {
                                if (!$this->addAdiciones($desc_precio[$i], $desc_nombre[$i], 'DESCUENTO', $detalle_id[$i], $desc_id[$i])) {
                                    $this->db->trans_rollback();
                                    $this->Model_general->dieMsg(array('exito' => false, 'mensaje' => 'Error al guardar los datos'));
                                }
                            }
                            if (isset($prov_id[$i])) {
                                if (!$this->addProveedores($prov_moneda[$i], $prov_paqu_id[$i], $prov_id[$i], $prov_cantidad[$i], $prov_precio[$i], $prov_tipo[$i], $detalle_id[$i], $moneda, $prov_descripcion[$i], $this->Model_general->time_to_mysql($feta_hora[$i]), $this->Model_general->fecha_to_mysql($feta_fecha[$i]), $guia[$i])) {
                                    $this->db->trans_rollback();
                                    $this->Model_general->dieMsg(array('exito' => false, 'mensaje' => 'Error al guardar los datos'));
                                }
                            }
                        } else {
                            $this->db->trans_rollback();
                            $this->Model_general->dieMsg(array('exito' => false, 'mensaje' => 'Error al guardar los datos'));
                        }
                    }
                }

            else :
                $this->Model_general->dieMsg(array('exito' => false, 'mensaje' => 'Error al guardar los datos'));
            endif;

            $this->Model_general->add_log("EDITAR", 1, "Edición de reserva " . $pa);
        }

        $this->db->trans_commit();

        $this->Model_general->dieMsg(array('exito' => true, 'mensaje' => 'Guardado con exito con FILE: ' . $pa, 'id' => $id));
    }
    public function get_cod_cuen($cuen_id = '')
    {
        $cuenta = $this->db->where("cuen_id", $cuen_id)->get("cuenta")->row();
        $resp["codigo"] = $cuenta->cuen_codigo;
        echo json_encode($resp);
    }
    public function paq_cobrar($id)
    {
        $this->load->helper('Funciones');
        $datos["moneda"] = $this->Model_general->enum_valores('paquete', 'paqu_moneda');
        $datos["paquete"] = $this->Model_general->getPaqTotal($id);
        $datos["documentos"] = $this->Model_general->getOptionsWhere("comprobante_tipo", array("tcom_id", "tcom_nombre"), array("tcom_id<>" => '07', "tcom_id<>" => '08'));
        $datos["cuentas"] = $this->Model_general->getOptions("cuenta", array("cuen_id", "cuen_banco"), '* Cuenta');

        $this->load->view('Registro/form_cobrar', $datos);
    }
    public function paq_cancelarCobro($id)
    {
        $paq = $this->db->where("paqu_id", $id)->get("paquete")->row();
        $mov = $this->db->where(array("movi_ref_id" => $id, "movi_tipo_id" => 4))->get("cuenta_movimiento")->row();
        if ($paq->paqu_cobrado == 0) {
            $resp["exito"] = false;
            $resp["mensaje"] = "El File no tiene pagos";
        } else {
            $this->db->trans_start();

            $this->Model_general->actualizarCaja(4, 'SALIDA', "", "", "", $paq->paqu_clie_rsocial . " / " . $paq->paqu_nombre . " / " . $paq->paqu_fecha, $paq->paqu_total, $paq->paqu_moneda, $this->usua_id, $id, '', $mov->movi_cuen_id, "000000", date("Y-m-d"), "Cobro cancelado");

            $dte = array("paqu_escobrado" => '0', "paqu_cobrado" => "0", "paqu_cobrofecha" => NULL, "paqu_cobrodesc" => null);
            $this->Model_general->actualizaPaqueteDetalle($id, 0);

            $this->Model_general->guardar_edit_registro("paquete", $dte, array('paqu_id' => $id));
            $this->Model_general->guardar_edit_registro("cuenta_movimiento", array("movi_file" => ""), array('movi_id' => $mov->movi_id));

            $this->Model_general->add_log("EDITAR", 1, "Anulación de cobro " . $paq->paqu_prefijo . "-" . $paq->paqu_numero . " " . $paq->paqu_total . " " . $paq->paqu_moneda . ", Código de caja: ANULADO");

            $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE) {
                $resp['exito'] = false;
                $resp['mensaje'] = "Error al guardar los datos";
            } else {
                $resp['exito'] = true;
                $resp['mensaje'] = "Datos guardados con exito";
            }
        }
        echo json_encode($resp);
    }
    public function pagar_prov($id)
    {
        $this->load->helper('Funciones');
        $this->db->where("sepr_id", $id);
        $orde = $this->db->get("servicio_proveedor")->row();
        $datos['orde'] = $orde;
        $arr_doc = array(0, 1, 2, 3, 9, 11);
        $this->db->where_in("tcom_id", $arr_doc);
        $doc = $this->db->get("comprobante_tipo")->result();

        $documentos = array();
        foreach ($doc as $i => $d) {
            $documentos[$d->tcom_id] = $d->tcom_nombre;
        }
        $datos["moneda"] = $this->Model_general->enum_valores('servicio_proveedor', 'sepr_moneda');
        $datos['documentos'] = $documentos;
        $datos["cuentas"] = $this->Model_general->getOptions("cuenta", array("cuen_id", "cuen_banco"), '* Cuenta');
        $this->load->view('Registro/form_pagarProv', $datos);
    }
    public function editar_prov($id = '')
    {
        $this->load->helper('Funciones');
        $this->db->join("proveedor", "sepr_prov_id = prov_id");
        $this->db->join("proveedor_empresa", "prov_emp_id = emp_id");
        $this->db->where("sepr_id", $id);
        $orde = $this->db->get("servicio_proveedor")->row();
        $datos['orde'] = $orde;
        $this->load->view('Registro/form_editServ', $datos);
    }
    public function guardar_editServicio($id)
    {
        $precio = $this->input->post("precio");
        $cantidad = $this->input->post("cantidad");
        $total = $this->input->post("total");
        $desc = $this->input->post("descripcion");

        $data = array(
            "sepr_precio" => $precio,
            "sepr_cantidad" => $cantidad,
            "sepr_total" => $total,
            "sepr_servicio" => $desc
        );
        $where = array("sepr_id" => $id);
        if ($this->Model_general->guardar_edit_registro("servicio_proveedor", $data, $where)) {
            $resp["exito"] = true;
            $resp["mensaje"] = "Datos guardados cone exito";
        } else {
            $resp["exito"] = false;
            $resp["mensaje"] = "Algo salio mal";
        }
        echo json_encode($resp);
    }
    public function guardar_pagoProv($id = '')
    {
        $this->load->helper('Funciones');

        $this->db->from("servicio_proveedor");
        $this->db->join("proveedor", "prov_id = sepr_prov_id", "LEFT");
        $this->db->where("sepr_id", $id);
        $orde = $this->db->get()->row();
        if ($orde->sepr_total == '') {
            $json['exito'] = false;
            $json['mensaje'] = "No tiene un total asignado, primero edite este servicio";
            echo json_encode($json);
            exit(0);
        }
        if ($orde->sepr_espagado == 1) {
            $json["exito"] = false;
            $json["mensaje"] = "Ya esta pagado";
        } else {
            $documento = $this->input->post("documento");
            $serie = $this->input->post("serie");
            $numero = $this->input->post("numero");
            $cuenta = $this->input->post("cuenta");
            $codigo_cuen = $this->input->post("codigo_cuen");
            $moneda = $this->input->post("moneda");
            $total = $this->input->post("total");
            $cancelado = $this->input->post("cancelado");
            $pagado = $this->input->post("pagado");
            $saldo = $this->input->post("saldo");
            $obs = $this->input->post("observacion");
            $fecha = $this->Model_general->fecha_to_mysql($this->input->post('fecha'));

            if ($cuenta == '' || $codigo_cuen == '' || $pagado == '') {
                $json['exito'] = false;
                $json['mensaje'] = "Cuenta, Código y Pagado son obligatorios";
                echo json_encode($json);
                exit(0);
            }

            $this->db->trans_start();

            $this->Model_general->actualizarCaja(7, "SALIDA", $documento, $serie, $numero, "Pago a proveedor: " . $orde->prov_rsocial, $pagado, $moneda, $this->usua_id, $id, '', $cuenta, $codigo_cuen, $fecha, $obs);

            //$desc = $obs.", Código de caja: ".$codigo_cuen." / ".$orde->sepr_pagodesc;
            $desc = ($orde->sepr_pagodesc != "") ? $orde->sepr_pagodesc . " | " . $obs : $obs;

            $prev = array("sepr_pagofecha" => $fecha, "sepr_pagodesc" => $desc);
            if (($cancelado + $pagado) >= $total) {
                $dte = array("sepr_espagado" => '1', "sepr_pagado" => ($cancelado + $pagado));
            } else {
                $dte = array("sepr_pagado" => ($cancelado + $pagado));
            }
            $dte = array_merge($dte, $prev);
            $this->Model_general->guardar_edit_registro("servicio_proveedor", $dte, array('sepr_id' => $id));

            $this->Model_general->add_log("PAGO", 16, "Pago a proveedor: " . $orde->prov_rsocial . " " . $pagado . " " . $moneda . ", Código de caja: " . $codigo_cuen);
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $json['exito'] = false;
                $json['mensaje'] = "Error al guardar los datos";
            } else {
                $json['exito'] = true;
                $json['mensaje'] = "Datos guardados con exito";
            }
        }
        echo json_encode($json);
    }
    public function valida_preOrdenPago()
    {
        $seleccionados = implode(",", $this->input->post("sel"));
        $resp = "";
        $proveedores = $this->db->query("SELECT sepr_prov_id from servicio_proveedor where sepr_id IN ({$seleccionados}) group by sepr_prov_id")->result();
        $monedas = $this->db->query("SELECT sepr_moneda from servicio_proveedor where sepr_id IN ({$seleccionados}) group by sepr_moneda")->result();
        if (COUNT($proveedores) > 1)
            $resp = "No es posible generar una orden para diferentes proveedores";
        if (COUNT($monedas) > 1)
            $resp = "No es posible generar una orden con diferentes monedas";
        echo $resp;
    }
    public function anular_provPago($id)
    {
        $prov = $this->db->where("sepr_id", $id)->join("proveedor", "prov_id = sepr_prov_id")->get("servicio_proveedor")->row();
        $mov = $this->db->where(array("movi_ref_id" => $id, "movi_tipo_id" => 7))->get("cuenta_movimiento")->row();
        if ($prov->sepr_esorden == "1") {
            $resp["exito"] = false;
            $resp["mensaje"] = "No es posible anulado pago por que esta en una orden";
        } else {
            if ($prov->sepr_pagado == "0") {
                $resp["exito"] = false;
                $resp["mensaje"] = "No hay pagos registrados";
            } else {
                $this->db->trans_start();

                $this->Model_general->actualizarCaja(7, 'INGRESO', "", "", "", $prov->prov_rsocial . " / " . $prov->sepr_fecha, $prov->sepr_pagado, $prov->sepr_moneda, $this->usua_id, $id, '', $mov->movi_cuen_id, "000000", date("Y-m-d"), "pago cancelado");

                $dte = array("sepr_espagado" => '0', "sepr_pagado" => "0", "sepr_pagofecha" => NULL, "sepr_pagodesc" => null);
                $this->Model_general->guardar_edit_registro("servicio_proveedor", $dte, array('sepr_id' => $id));

                $this->Model_general->guardar_edit_registro("cuenta_movimiento", array("movi_file" => ""), array('movi_id' => $mov->movi_id));

                $this->Model_general->add_log("PAGO", 16, "Anulación de pago " . $prov->prov_rsocial . " " . $prov->sepr_pagado . " " . $prov->sepr_moneda . ", Código de caja: ANULADO");

                $this->db->trans_complete();

                if ($this->db->trans_status() === FALSE) {
                    $resp['exito'] = false;
                    $resp['mensaje'] = "Error al guardar los datos";
                } else {
                    $resp['exito'] = true;
                    $resp['mensaje'] = "Datos guardados con exito";
                }
            }
        }

        echo json_encode($resp);
    }
    public function paq_guardarCobro($id = '')
    {
        $pagado = $this->input->post('pagado');
        $cancelado = $this->input->post('cancelado');
        $saldo = $this->input->post('saldo');
        $obs = $this->input->post('descripcion');
        $moneda = $this->input->post('moneda');
        $total = $this->input->post('total');

        $documento = $this->input->post('documento');
        $serie = $this->input->post('serie');
        $numero = $this->input->post('numero');
        $fecha = $this->Model_general->fecha_to_mysql($this->input->post('fecha'));

        $cuenta = $this->input->post('cuenta');
        $codigo_cuen = $this->input->post('codigo-cuen');

        $paq = $this->db->where("paqu_id", $id)->get("paquete")->row();
        if ($paq->paqu_escobrado == 1) {
            $json['exito'] = false;
            $json['mensaje'] = "El file ya esta cancelado";
            echo json_encode($json);
            exit(0);
        }
        if ($cuenta == '' || $codigo_cuen == '' || $pagado == '') {
            $json['exito'] = false;
            $json['mensaje'] = "Cuenta, Código y Pagado son obligatorios";
            echo json_encode($json);
            exit(0);
        }

        $consulta = $this->db->where(array("movi_cuen_id" => $cuenta, "movi_file" => $codigo_cuen))->get("cuenta_movimiento");
        if ($consulta->num_rows() > 0) {
            $json['exito'] = false;
            $json['mensaje'] = "El codigo ya existe";
            echo json_encode($json);
            exit(0);
        }
        $this->db->trans_start();
        $this->Model_general->actualizarCaja(4, 'INGRESO', $documento, $serie, $numero, $paq->paqu_clie_rsocial . " / " . $paq->paqu_nombre . " / " . $paq->paqu_fecha, $pagado, $moneda, $this->usua_id, $id, '', $cuenta, $codigo_cuen, $fecha, $obs);

        $desc = ($paq->paqu_cobrodesc != "") ? $paq->paqu_cobrodesc . " / " . $obs : $obs;
        $prev = array("paqu_cobrofecha" => $fecha, "paqu_cobrodesc" => $desc);
        if (($cancelado + $pagado) >= $total) {
            $dte = array("paqu_escobrado" => '1', "paqu_cobrado" => ($cancelado + $pagado));
            $this->Model_general->actualizaPaqueteDetalle($id, 0);
        } else $dte = array("paqu_cobrado" => $pagado + $cancelado);
        $dte = array_merge($dte, $prev);
        $this->Model_general->guardar_edit_registro("paquete", $dte, array('paqu_id' => $id));

        $this->Model_general->add_log("COBRO", 1, "Cobro de Reserva " . $paq->paqu_prefijo . "-" . $paq->paqu_numero . " " . $pagado . " " . $moneda . ", Código de caja: " . $codigo_cuen);

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $json['exito'] = false;
            $json['mensaje'] = "Error al guardar los datos";
        } else {
            $json['exito'] = true;
            $json['mensaje'] = "Datos guardados con exito";
        }
        echo json_encode($json);
    }

    public function addProveedores($prov_moneda, $prov_paqu_id, $prov_id, $prov_cantidad, $prov_precio, $prov_tipo, $det_id, $moneda, $descripcion, $hora, $fecha, $guia)
    {
        $resp = true;
        for ($j = 0; $j < count($prov_id); $j++) {
            $prov_adic = array(
                "sepr_pdet_id" => $det_id,
                "sepr_precio" => $prov_precio[$j],
                "sepr_cantidad" => $prov_cantidad[$j],
                "sepr_total" => $prov_precio[$j] * $prov_cantidad[$j],
                "sepr_prov_id" => $prov_id[$j],
                "sepr_tipo" => $prov_tipo[$j],
                "sepr_moneda" => $prov_moneda[$j],
                "sepr_servicio" => $descripcion[$j],
                "sepr_hora" => $hora,
                "sepr_fecha" => $fecha,
                "sepr_guia" => $guia
            );

            if (empty($prov_paqu_id[$j])) {
                if ($this->Model_general->guardar_registro("servicio_proveedor", $prov_adic) == FALSE) {
                    $resp = false;
                }
            } else {
                if ($this->Model_general->guardar_edit_registro("servicio_proveedor", $prov_adic, array("sepr_id" => $prov_paqu_id[$j])) == FALSE) {
                    $resp = false;
                }
            }
        }
        return $resp;
    }
    public function addAdiciones($precio = '', $nombre, $tipo, $pdet, $adic_id = '')
    {
        $resp = true;
        for ($j = 0; $j < count($precio); $j++) {
            $item_adic = array(
                "padi_pdet_id" => $pdet,
                "padi_descripcion" => $nombre[$j],
                "padi_monto" => $precio[$j],
                "padi_tipo" => $tipo
            );
            if ($adic_id != '') {
                if (empty($adic_id[$j])) {
                    if ($this->Model_general->guardar_registro("paquete_adicion", $item_adic) == FALSE) {
                        $resp = false;
                    }
                } else {
                    if ($this->Model_general->guardar_edit_registro("paquete_adicion", $item_adic, array("padi_id" => $adic_id[$j])) == FALSE) {
                        $resp = false;
                    }
                }
            } else {
                if ($this->Model_general->guardar_registro("paquete_adicion", $item_adic) == FALSE) {
                    $resp = false;
                }
            }
        }
        return $resp;
    }
    function paq_gen_orden($paqu_id = '')
    {
        $this->load->helper('Funciones');
        $this->load->model("Model_general");
        $this->load->library('Ssp');
        $this->load->library('Cssjs');
        $this->cssjs->add_js(base_url() . 'assets/js/Registro/ord_serv.js', false, false);

        $this->db->select("clie_rsocial as cliente, paqu_id, paqu_nombre as grupo, paqu_pax as pax, DATE_FORMAT(paqu_fecha, '%d/%m/%Y') as fecha, paqu_moneda as moneda, paqu_observacion as observacion,paqu_pax as pax,paqu_estado as estado");
        $this->db->from("paquete");
        $this->db->join("cliente", "paqu_clie_id = clie_id", "left");
        $this->db->where("paqu_id", $paqu_id);
        $paquete = $this->db->get()->row();

        $detalle = $this->Model_general->getDetaPaqu($paqu_id);

        $datos["paquete"] = $paquete;
        $datos["detas"] = $detalle;
        $datos['titulo'] = "Generar Ordenes de servicio";
        $script['js'] = $this->cssjs->generate_js();
        $this->load->view('header', $script);
        $this->load->view('menu');
        $this->load->view('Registro/paq_gen_orden', $datos);
        $this->load->view('footer');
    }








    public function cambiar_pdet($pdet_id = '')
    {
        $this->db->select("deta_id as id, deta_lunch as lunch, deta_lunch_pre as lunch_pre, deta_serv_id as serv_id, deta_servicio as serv, deta_precio as precio, DATE_FORMAT(deta_fechaserv, '%d/%m/%Y') as fecha, deta_pax as pax, deta_total as total");
        $pdet = $this->db->where("deta_id", $pdet_id)->get("paquete_detalle")->row();

        $this->db->select("padi_id as id, padi_pdet_id as pdet_id, padi_descripcion as descripcion, padi_monto as monto, padi_tipo as tipo");
        $adiciones =  $this->db->where("padi_pdet_id", $pdet_id)->get("paquete_adicion")->result();
        $servicio = $this->Model_general->getOptions("servicio", array("serv_id", "serv_descripcion"));

        $data["pdet"] = $pdet;
        $data["adic"] = json_encode($adiciones);
        $data["servicios"] = $servicio;
        $this->load->view($this->router->fetch_class() . '/' . $this->router->fetch_method(), $data);
    }
    public function pdet_guardar($deta_id = '')
    {

        $fecha = $this->Model_general->fecha_to_mysql($this->input->post("fecha"));
        $serv_name = $this->input->post("serv_name");
        $serv_id = $this->input->post("serv_id");
        $pax = $this->input->post("pax");
        $precio = $this->input->post("precio");
        $total = $this->input->post("total");
        $lunch = $this->input->post("lunch");
        $lunch_pre = $this->input->post("lunch_pre");

        $adic_name = $this->input->post("adic_nombre");
        $adic_id = $this->input->post("adic_id");
        $adic_precio = $this->input->post("adic_precio");
        $desc_name = $this->input->post("desc_nombre");
        $desc_id = $this->input->post("desc_id");
        $desc_precio = $this->input->post("desc_precio");


        $datas = array(
            "deta_fechaserv" => $fecha . " 00:00:00",
            "deta_servicio" => $serv_name,
            "deta_serv_id" => $serv_id,
            "deta_pax" => $pax,
            "deta_precio" => $precio,
            "deta_total" => $total,
            "deta_lunch" => $lunch,
            "deta_lunch_pre" => $lunch_pre
        );
        $where = array("deta_id" => $deta_id);
        $this->db->trans_begin();
        if ($this->Model_general->guardar_edit_registro("paquete_detalle", $datas, $where)) {
            $this->actualiza_totalPaqu($deta_id);
            if (isset($adic_id) || isset($desc_id)) {
                $this->db->where('padi_pdet_id', $deta_id);
                if (isset($adic_id))
                    $this->db->where_not_in('padi_id', $adic_id);
                if (isset($desc_id))
                    $this->db->where_not_in('padi_id', $desc_id);
                $this->db->delete('paquete_adicion');
            }


            if (isset($adic_precio)) {
                if (!$this->addAdiciones($adic_precio, $adic_name, "ADICION", $deta_id, $adic_id)) {
                    $resp["exito"] = false;
                    $resp["mensaje"] = "Ocurrio un error, intentelo más tarde.";
                }
            }
            if (isset($desc_precio)) {
                if ($this->addAdiciones($desc_precio, $desc_name, "DESCUENTO", $deta_id, $desc_id)) {
                    $resp["exito"] = false;
                    $resp["mensaje"] = "Ocurrio un error, intentelo más tarde.";
                }
            }
        } else {
            $this->db->trans_rollback();
            $resp["exito"] = false;
            $resp["mensaje"] = "Ocurrio un error, intentelo más tarde.";
        }
        $this->db->trans_commit();
        $resp["exito"] = true;
        $resp["mensaje"] = "Reserva actualizada con exito.";
        echo json_encode($resp);
    }
    public function actualiza_totalPaqu($deta_id = '')
    {
        $paqu_id = $this->db->select("deta_paqu_id as id")->where("deta_id", $deta_id)->get("paquete_detalle")->row()->id;

        $this->db->query("UPDATE paquete set paqu_total = (SELECT SUM(deta_total) as suma from paquete_detalle where deta_paqu_id = {$paqu_id}) where paqu_id = {$paqu_id}");
    }
    /**************************  ANTES DE LOS CAMBIOS **************************************/
    /*
    public function ordPago_gen($id_orden = ''){

        if($id_orden != ''){
            //db->where("deta_orde_id", $id_orden)->get('ordserv_detalle')->result();
            $detas = $this->Model_general->getData('ordserv_detalle', array("deta_id", "deta_orde_id", "DATE_FORMAT(deta_fecha, '%d/%m/%Y') as deta_fecha", "DATE_FORMAT(deta_fecha, '%h:%i %p')  as deta_hora", "deta_pdet_id", "deta_file", "deta_pax", "deta_nombres", "deta_hotel", "deta_hora", "deta_lunch" ,"deta_contacto", "deta_endose", "deta_obs"), array("deta_orde_id" => $id_orden));
            $orden = $this->db->select("orde_id, orde_paqu_id, orde_serv_id, orde_servicio, orde_estado, orde_pagado, orde_fechareg, orde_numero, orde_moneda, DATE_FORMAT(orde_fecha, '%d/%m/%Y') as orde_fecha")->where("orde_id", $id_orden)->get('ordenserv')->row();
            $adic = $this->Model_general->getOrdenAdicionales($id_orden);
        }else{
            $seleccionados = $this->input->get('sel');
            $paquetes = $this->db->query("SELECT * FROM paquete_detalle JOIN paquete ON paqu_id = deta_paqu_id JOIN cliente ON clie_id = paqu_clie_id JOIN servicio ON deta_serv_id = serv_id WHERE deta_id IN (".$seleccionados.")")->result();

            $this->db->select("DATE_FORMAT(paqu_fecha, '%d/%m/%Y') as fecha, paqu_id");
            $this->db->where("paqu_id", $paquetes[0]->deta_paqu_id);
            $paquete = $this->db->get("paquete")->row();

            $detas = array();
            $adic = array();
            foreach ($paquetes as $i => $val) {
                $hora = explode(" ", $val->deta_fechaserv);
                $hres = date('h:i A', strtotime($val->deta_fechaserv));
                $dt = new stdClass();
                $dt->deta_id = '';
                $dt->deta_pdet_id = $val->deta_id;
                $dt->deta_fecha = date('d/m/Y', strtotime($val->deta_fechaserv));
                $dt->deta_fileL = ($val->paqu_tipo == 'LOCAL'?$this->inis->conf_ini_paquL:$this->inis->conf_ini_paquR)." - ".$val->paqu_numero;
                $dt->deta_file = $val->paqu_file;
                $dt->deta_pax = $val->deta_pax;
                $dt->deta_total = $val->deta_total;
                $dt->deta_precio = $val->deta_precio;
                $dt->deta_servicio = $val->deta_servicio;
                $dt->deta_nombres = $val->paqu_nombre;
                $dt->deta_hotel = $val->deta_hotel;
                //$dt->deta_hora = date('h:i A', strtotime($val->deta_fechaserv));
                $dt->deta_hora = ($hora[1] != '00:00:00'?$hres:'');
                $dt->deta_lunch = $val->deta_lunch;
                $dt->deta_lunch_pre = $val->deta_lunch_pre;
                $dt->deta_contacto = $val->clie_rsocial;
                $dt->deta_endose = $val->paqu_endose;
                $dt->deta_obs = $val->deta_descripcion;

                $this->db->where("padi_pdet_id", $val->deta_id);
                $adicionales = $this->db->get("paquete_adicion")->result();

                $detas[$i] = $dt;
                $detas[$i]->adicionales = $adicionales;
            }
            $detas = (object)$detas;
            $serv_nombre = $this->db->select("serv_descripcion as serv")->where("serv_id", $paquetes[0]->deta_serv_id)->get("servicio")->row()->serv;
            $orden = new stdClass();
            $orden->orde_id = '';
            $orden->orde_fecha = $paquete->fecha;
            $orden->orde_serv_id = $paquetes[0]->deta_serv_id;
            $orden->orde_servicio = $serv_nombre;
            $orden->orde_moneda = 'SOLES';
            $orden->orde_paqu_id = $paquete->paqu_id;
            $orden->orde_fechareg = date('d/m/Y');
            $orden->orde_numero = $this->nextnum();
        }
        
        $datos["monedas"] = $this->Model_general->enum_valores('ordv_serv_adicional','ordv_adic_moneda');
        $datos["estados"] = $this->Model_general->enum_valores('ordv_serv_adicional','ordv_adic_pagado');
        
        $datos["detas"] = $detas;
        $datos["orden"] = $orden;
        $datos["titulo"] = "Generar Orden de Pago";

        $this->load->helper('Funciones');
        $this->load->model("Model_general");
        $this->load->library('Ssp');
        $this->load->library('Cssjs');
                
        $script['js'] = $this->cssjs->generate_js();
        $this->load->view('header', $script);
        $this->load->view('menu');
        $this->load->view($this->router->fetch_class().'/'.$this->router->fetch_method(), $datos);
        $this->load->view('footer');
        
    }
    */
    //METODO ANTIGUO FUNCIONAL CON INPUTS EN LUGAR DE LA TABLA EN GENERAR ORDEN
    /* 
    public function guardar_orden($id=''){
        $this->orde_validar();
        $numero = $this->input->post('numero');
        $moneda = $this->input->post('moneda');
        $fecha_emi = $this->Model_general->fecha_to_mysql($this->input->post('fecha_emi'));
        $servicio = $this->input->post('serv_name');
        $serv_id = $this->input->post('serv_id');
        $paqu_id = $this->input->post('paqu_id');
        

        $datos = array("orde_fecha" => $fecha_emi,
            "orde_servicio" => $servicio,
            "orde_serv_id" => $serv_id,
            "orde_nro" => $numero,
            "orde_moneda" => $moneda,
            "orde_paqu_id" => $paqu_id
        );

        $deta_pdet_id = $this->input->post('deta_pdet_id');
        $deta_endose = $this->input->post('deta_endose');
        $deta_file = $this->input->post('deta_file');
        $deta_hora = $this->input->post('deta_hora');
        $deta_fecha = $this->input->post('deta_fecha');
        $deta_lunch = $this->input->post('deta_lunch');
        $deta_pax = $this->input->post('deta_pax');
        $deta_nombres = $this->input->post('deta_nombres');
        $deta_hotel = $this->input->post('deta_hotel');
        $deta_contacto = $this->input->post('deta_contacto');
        $deta_obs = $this->input->post('deta_obs');
        
        //time_to_mysql
        

        $adic_tservicio = $this->input->post('tservicio');
        $adic_proveedor = $this->input->post('proveedor');
        $adic_cant = $this->input->post('adicional_cant');
        $adic_precio = $this->input->post('adicional_precio');
        $adic_deta = $this->input->post('adicional_deta');
        $adic_id = $this->input->post('adic_id');


        $this->db->trans_begin();
        if(empty($id)){
            $pendientes = array("orde_estado" => "PENDIENTE", "orde_pagado" => "PENDIENTE");
            $datos = array_merge($datos, $pendientes);
            if (($meta = $this->Model_general->guardar_registro("ordenserv", $datos)) == TRUE):
                for ($i=0; $i < count($deta_nombres); $i++) { 
                    $item = array("deta_orde_id" => $meta['id'],
                                "deta_pdet_id" => $deta_pdet_id[$i],
                                "deta_fecha" => $this->Model_general->fecha_to_mysql($deta_fecha[$i]),
                                "deta_file" => $deta_file[$i],
                                "deta_pax" => $deta_pax[$i],
                                "deta_nombres" => $deta_nombres[$i],
                                "deta_hotel" => $deta_hotel[$i],
                                "deta_hora" => $this->Model_general->time_to_mysql($deta_hora[$i]),
                                "deta_lunch" => $deta_lunch[$i],
                                "deta_contacto" => $deta_contacto[$i],
                                "deta_endose" => $deta_endose[$i],
                                "deta_obs" => $deta_obs[$i]
                        );
                    
                    if($this->Model_general->guardar_registro("ordserv_detalle", $item)==FALSE){
                        $this->db->trans_rollback();
                        $this->Model_general->dieMsg(array('exito'=>false,'mensaje'=>'Error al guardar los datos'));
                    }
                }
                for ($i=0; $i < count($adic_deta); $i++) { 
                    $item = array("ordv_adic_orde_id" => $meta['id'],
                                "ordv_adic_tipo" => $adic_tservicio[$i],
                                "ordv_adic_servicio" => $adic_deta[$i],
                                "ordv_adic_precio" => $adic_precio[$i],
                                "ordv_adic_cant" => $adic_cant[$i],
                                "ordv_adic_total" => $adic_cant[$i] * $adic_precio[$i],
                                "ordv_adic_prov_id" => $adic_proveedor[$i]
                        );
                    if($this->Model_general->guardar_registro("ordv_serv_adicional", $item) == false){
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
            $condicion = "orde_id = ".$id;

            $detalle_id = $this->input->post('deta_id');

            if ($this->Model_general->guardar_edit_registro("ordenserv", $datos, $condicion) == TRUE):
                
                // ELIMINA LOS DETALLES
                $this->db->where('deta_orde_id',$id);
                $this->db->where_not_in('deta_id',$detalle_id);
                $this->db->delete('ordserv_detalle');

                $this->db->where('ordv_adic_orde_id',$id);
                $this->db->where_not_in('ordv_adic_id',$adic_id);
                $this->db->delete('ordv_serv_adicional');
                //////////////////////////////////////////////////////
              
                for ($i=0; $i < count($deta_nombres); $i++) { 
                    $condicion_items = "deta_id = ".$detalle_id[$i];
                    $item = array("deta_orde_id" => $id,
                                "deta_pdet_id" => $deta_pdet_id[$i],
                                "deta_fecha" => $this->Model_general->fecha_to_mysql($deta_fecha[$i]),
                                "deta_file" => $deta_file[$i],
                                "deta_pax" => $deta_pax[$i],
                                "deta_nombres" => $deta_nombres[$i],
                                "deta_hotel" => $deta_hotel[$i],
                                "deta_hora" => $this->Model_general->time_to_mysql($deta_hora[$i]),
                                "deta_lunch" => $deta_lunch[$i],
                                "deta_contacto" => $deta_contacto[$i],
                                "deta_endose" => $deta_endose[$i],
                                "deta_obs" => $deta_obs[$i]
                        );
                    if(empty($detalle_id[$i])){
                        if($this->Model_general->guardar_registro("ordserv_detalle", $item) == false){
                            $this->db->trans_rollback();
                            $this->Model_general->dieMsg(array('exito'=>false,'mensaje'=>'Error al guardar los datos'));
                        }
                    }else{
                        if($this->Model_general->guardar_edit_registro("ordserv_detalle", $item, $condicion_items) == false){
                            $this->db->trans_rollback();
                            $this->Model_general->dieMsg(array('exito'=>false,'mensaje'=>'Error al guardar los datos'));
                        }
                    }   
                }

                for ($i=0; $i < count($adic_deta); $i++) { 
                    $condicion_items = "ordv_adic_id = ".$adic_id[$i];
                    $item = array("ordv_adic_orde_id" => $id,
                                "ordv_adic_tipo" => $adic_tservicio[$i],
                                "ordv_adic_servicio" => $adic_deta[$i],
                                "ordv_adic_precio" => $adic_precio[$i],
                                "ordv_adic_cant" => $adic_cant[$i],
                                "ordv_adic_total" => $adic_cant[$i] * $adic_precio[$i],
                                "ordv_adic_prov_id" => $adic_proveedor[$i]
                        );
                    if(empty($adic_id[$i])){
                        if($this->Model_general->guardar_registro("ordv_serv_adicional", $item) == false){
                            $this->db->trans_rollback();
                            $this->Model_general->dieMsg(array('exito'=>false,'mensaje'=>'Error al guardar los datos'));
                        }
                    }else{
                        if($this->Model_general->guardar_edit_registro("ordv_serv_adicional", $item, $condicion_items) == false){
                            $this->db->trans_rollback();
                            $this->Model_general->dieMsg(array('exito'=>false,'mensaje'=>'Error al guardar los datos'));
                        }
                    }   
                }
            else:
               $this->Model_general->dieMsg(array('exito'=>false,'mensaje'=>'Error al guardar los datos'));
            endif;
        }
        $this->db->trans_commit();
        
        $this->Model_general->dieMsg(array('url'=> base_url()."Registro/ord_listado", 'exito'=>true,'mensaje'=>'Datos guardados con exito','id'=>$id));
    }
    */
    public function paq_eliminar($id = '')
    {
        if ($id != '') {
            $paqu = $this->db->where("paqu_id", $id)->get("paquete")->row();

            $liquidaciones = $this->Model_general->verif_exist($id, "l");
            $comprobantes = $this->Model_general->verif_exist($id, "c");
            if ($paqu->paqu_escobrado == 1 || $paqu->paqu_cobrado > 0) {
                $resp["exito"] = false;
                $resp["mensaje"] = "EL file esta cancelado o hay registros de cobro que lo afectan";
            } else {
                if ($liquidaciones->num_rows() > 0 || $comprobantes->num_rows() > 0) {
                    $mensaje = "";
                    if ($liquidaciones->num_rows() > 0) {
                        $liquidaciones = $liquidaciones->row()->file;
                        $mensaje .= "File relacionado en " . $liquidaciones . "\n";
                    }
                    if ($comprobantes->num_rows() > 0) {
                        $comprobantes = $comprobantes->row()->file;
                        $mensaje .= "File relacionado en " . $comprobantes;
                    }
                    $resp["exito"] = false;
                    $resp["mensaje"] = $mensaje;
                } else {
                    $this->Model_general->add_log("ELIMINAR", 1, "Eliminación de Reserva " . $paqu->paqu_prefijo . "-" . $paqu->paqu_numero);
                    $this->Model_general->borrar(array("paqu_id" => $id), "paquete");
                    $resp["exito"] = true;
                    $resp["mensaje"] = "Eliminado con exito";
                }
            }
            $this->Model_general->dieMsg($resp);
        }
    }
    public function paq_habilitar($id = '')
    {
        if ($id != '') {
            $this->db->query(
                "UPDATE paquete 
                    SET 
                        paqu_habilitado = ! paqu_habilitado
                    WHERE
                        paqu_id = $id;"
            );
            if ($this->db->affected_rows() > 0) {
                $resp["exito"] = true;
                $resp["mensaje"] = "habillitado con exito";
            } else {
                $resp["exito"] = false;
                $resp["mensaje"] = "no se prosesaron los cambios";
            }

            $this->Model_general->dieMsg($resp);
        }
    }
    public function eliminar_orden($id = '')
    {
        if ($id != '') {
            $json['exito'] = $this->Model_general->borrar(array("orde_id" => $id), "ordenserv");
            echo json_encode($json);
        }
    }

    public function getOrdenes($id = '')
    {
        if ($id != '') {
            $this->db->select("orde_id as id, prov_rsocial as proveedor,FORMAT(SUM(deta_precio), 2) as total, orde_nro as numero, GROUP_CONCAT(deta_descripcion SEPARATOR '</br>') as detalle, orde_estado as estado");
            $this->db->from("ordenserv");
            $this->db->join("proveedor", "prov_id = orde_prov_id", "left");
            $this->db->join("ordserv_detalle", "deta_orde_id = orde_id");
            $this->db->where("orde_paqu_id", $id);
            $this->db->order_by("deta_id", "ASC");
            $this->db->group_by("orde_id");
            $consulta = $this->db->get()->result();
            $html = "";
            foreach ($consulta as $val) {
                $html .= "<tr class='item-ord'>";
                $html .= "<input type='hidden' value='" . $val->id . "'>";
                $html .= "<td>" . $val->numero . "</td>";
                $html .= "<td><strong>" . $val->proveedor . "</strong></br>" . $val->detalle . "</td>";
                $html .= "<td>" . $val->total . "</td>";
                $html .= "<td>" . $val->estado . "</td>";
                $html .= "<td><button onclick='editar_ord(" . $val->id . ")' class='btn btn-info editar btn-sm'><span class='glyphicon glyphicon-edit'></span></button> ";
                $html .= " <button onclick='eliminar_ord(" . $val->id . ")'  class='btn btn-danger eliminar btn-sm'><span class='glyphicon glyphicon-trash'></span></button></td>";
                $html .= "</tr>";
            }
        }
        $json["html"] = $html;
        echo json_encode($json);
    }

    public function crear_servicio($serv_id = 0)
    {
        $this->load->helper('Funciones');
        $servicio = new stdClass();
        if ($serv_id == 0) {
            $servicio->id = 0;
            $servicio->tipo = 'AMBOS';
            $servicio->nombre = '';
            $servicio->abrev = '';
            $servicio->hora = '';
        } else {
            $servicio = $this->Model_general->getServicios($serv_id);
        }
        $datos["tipo"] = $this->Model_general->enum_valores('servicio', 'serv_tipo_reserv');
        $datos['servicio'] = $servicio;
        $this->load->view('Registro/form_crear_serv', $datos);
    }
    public function guardar_servicio($serv_id = 0)
    {
        $descripcion = $this->input->post('descripcion');
        $tipo = $this->input->post('tipo');
        $hora = $this->Model_general->time_to_mysql($this->input->post('hora'));
        $abrev = $this->input->post('abrev');

        $datos = array(
            "serv_tipo_reserv" => $tipo,
            "serv_descripcion" => $descripcion,
            "serv_hora" => $hora,
            "serv_abrev" => $abrev,
            "serv_tipo_id" => 1,
            "serv_habilitado" => "1"
        );
        if ($serv_id != 0) {
            $condicion = array("serv_id" => $serv_id);
            if ($this->Model_general->guardar_edit_registro("servicio", $datos, $condicion) == TRUE) :
                $this->Model_general->add_log("EDITAR", 11, "Edición de servicio: " . $descripcion);
                $json['exito'] = true;
                $json['mensaje'] = "Servicio actualizado con exito";
            else :
                $json['exito'] = false;
                $json['mensaje'] = "Error al actualizar los datos";
            endif;
        } else {
            if (($meta = $this->Model_general->guardar_registro("servicio", $datos)) == TRUE) :
                $this->Model_general->add_log("CREAR", 11, "Registro de servicio: " . $descripcion);
                $json['exito'] = true;
                $json['datos'] = array_merge(array('serv_id' => $meta['id']), $datos);
                $json['mensaje'] = "Servicio agregado con exito";
            else :
                $json['exito'] = false;
                $json['mensaje'] = "Error al guardar los datos";
            endif;
        }
        echo json_encode($json);
    }
    public function verificaTarifa($clie, $serv)
    {
        $this->db->where("clse_clie_id", $clie);
        $this->db->where("clse_serv_id", $serv);
        $consulta = $this->db->get("cliente_servicio");
        return $consulta;
    }
    /*
    public function tarifas_servicio($serv_id=''){
        $servicio = $this->Model_general->getServicios($serv_id);
        $clientes = $this->Model_general->getData("cliente", array("clie_id", "clie_rsocial"), array("clie_activo" => 1));
        $tabla = '';
        foreach ($clientes as $i => $val) {
            $consulta = $this->verificaTarifa($val->clie_id, $serv_id);
            if($consulta->num_rows() > 0) $precio = $consulta->row()->clse_precio;
            else $precio = '0.00';
            $tabla .= "<tr>";
            $tabla .= "<td>".($i+1)."</td>";
            $tabla .= "<td>".$servicio->nombre."</td>";
            $tabla .= "<td>".$val->clie_rsocial."</td>";
            $tabla .= "<td>".$precio."</td>";
            $tabla .= "<td><a title='Editar tarifa' class='btn btn-primary btn-xs adit_precio' href='".base_url()."registro/tarifa_editar/".$val->clie_id."/".$serv_id."'><span class='glyphicon glyphicon-pencil'></span></a></td>";
            $tabla .= "</tr>";
            
        }
        $resp["exito"] = true;
        $resp["tabla"] = $tabla;
        echo json_encode($resp);
    }
    */
    public function tarifas_cliente($clie_id = '', $fact = 'NO')
    {
        $servicios = $this->Model_general->getServicios();
        $cliente = $this->Model_general->getData("cliente", array("clie_id", "clie_rsocial"), array("clie_activo" => 1, "clie_id" => $clie_id));
        $tabla = '';
        foreach ($servicios as $i => $val) {
            $consulta = $this->verificaTarifa($cliente[0]->clie_id, $val->id);
            if ($this->editarconf > 1) {
                $btn_tar = "<a title='Editar tarifa' class='btn btn-primary btn-xs adit_precio' href='" . base_url() . "registro/tarifa_editar/" . $cliente[0]->clie_id . "/" . $val->id . "'><span class='glyphicon glyphicon-pencil'></span> Tarifa</a>";
                $btn_add = ($fact == 'SI') ? "<a title='Editar sub servicios' class='btn btn-success btn-xs adit_precio' href='" . base_url() . "registro/sub_servicio_editar/" . $cliente[0]->clie_id . "/" . $val->id . "'><span class='glyphicon glyphicon-pencil'></span> Sub servicios</a>" : "";
            } else {
                $btn_tar = "";
                $btn_add = "";
            }


            if ($consulta->num_rows() > 0) {
                $tarifa = $consulta->row();
                $simb = ($tarifa->clse_moneda == 'SOLES') ? "S/ " : "$ ";
                $lunch = $simb . $tarifa->clse_lunch;

                $this->db->where("clsu_clse_servid", $tarifa->clse_id);
                $sub_serv = $this->db->get("cliente_subservicio");

                if ($fact == 'NO' || $sub_serv->num_rows() < 1) {
                    $precio = $simb . $tarifa->clse_precio;
                } else {
                    $precio = $simb . $tarifa->clse_precio;
                    $sub_serv = $sub_serv->result();


                    $sub_html = "<table class='table table-striped table-bordered'>";
                    $sub_html .= "<tr><th>Sub Servicio</th><th>Tarifa</th></tr>";
                    foreach ($sub_serv as $sub) {
                        $sub_simb = ($sub->clsu_moneda == 'SOLES') ? 'S/ ' : '$ ';
                        $sub_html .= "<tr>";
                        $sub_html .= "<td>" . $sub->clsu_descripcion . "</td>";
                        $sub_html .= "<td>" . $sub_simb . $sub->clsu_monto . "</td>";
                        $sub_html .= "</tr>";
                    }
                    $sub_html .= "</table>";

                    $precio = "<strong>Tarifa general " . $precio . "</strong></br>" . $sub_html;
                }
            } else {
                $precio = 'S/ 0.00';
                $lunch = 'S/ 0.00';
            }
            $tabla .= "<tr>";
            $tabla .= "<td>" . ($i + 1) . "</td>";
            $tabla .= "<td>" . $cliente[0]->clie_rsocial . "</td>";
            $tabla .= "<td>" . $val->nombre . "</td>";
            $tabla .= "<td>" . $precio . "</td>";
            $tabla .= "<td>" . $lunch . "</td>";
            $tabla .= "<td class='col-sm-1'>" . $btn_tar . $btn_add . "</td>";
            $tabla .= "</tr>";
        }
        $resp["exito"] = true;
        $resp["tabla"] = $tabla;
        echo json_encode($resp);
    }

    public function sub_servicio_editar($clie_id = 0, $serv_id = 0)
    {
        $servicio = $this->Model_general->getServicios($serv_id);
        $cliente = $this->db->where(array("clie_activo" => "1", "clie_id" => $clie_id))->get("cliente")->row();
        $sub_serv = array();
        $consulta = $this->verificaTarifa($clie_id, $serv_id);
        if ($consulta->num_rows() > 0) {
            $consulta = $consulta->row();
            $this->db->where("clsu_clse_servid", $consulta->clse_id);
            $sub_serv = $this->db->get("cliente_subservicio")->result();
            $data["sub_serv"] = json_encode($sub_serv);
            $data["clse_id"] = $consulta->clse_id;
        } else {
            $datas = array("clse_clie_id" => $clie_id, "clse_serv_id" => $serv_id, "clse_precio" => "0.00", "clse_lunch" => "0.00");
            $clse = $this->Model_general->guardar_registro("cliente_servicio", $datas);
            $data["clse_id"] = $clse["id"];
            $data["sub_serv"] = json_encode($sub_serv);
        }

        $data["serv_name"] = $servicio->nombre;
        $data["monedas"] = $this->Model_general->enum_valores('cliente_subservicio', 'clsu_moneda');
        $data["serv_id"] = $serv_id;
        $data["clie_rsocial"] = $cliente->clie_rsocial;
        $data["clie_id"] = $clie_id;
        $this->load->view('Registro/form_editar_subserv', $data);
    }
    public function guardar_subserv($clse_id = '', $clie_id)
    {
        if ($clse_id != '') {
            $ids = $this->input->post('sub_id');
            $nombres = $this->input->post('sub_name');
            $precios = $this->input->post('sub_prec');
            $moneda = $this->input->post('sub_moneda');
            $this->db->trans_begin();

            $this->db->where('clsu_clse_servid', $clse_id);
            $this->db->where_not_in("clsu_id", $ids);
            $this->db->delete('cliente_subservicio');

            foreach ($ids as $i => $id) {
                $data = array(
                    "clsu_clse_servid" => $clse_id,
                    "clsu_descripcion" => $nombres[$i],
                    "clsu_monto" => $precios[$i],
                    "clsu_moneda" => $moneda[$i]
                );
                if (empty($id)) {
                    if (!$this->Model_general->guardar_registro("cliente_subservicio", $data)) {
                        $this->db->trans_rollback();
                        $json["exito"] = false;
                        $json["mensaje"] = "Ocurrio un error";
                        $this->Model_general->dieMsg($json);
                    }
                } else {
                    $condicion = array("clsu_id" => $id);
                    if (!$this->Model_general->guardar_edit_registro("cliente_subservicio", $data, $condicion)) {
                        $this->db->trans_rollback();
                        $json["exito"] = false;
                        $json["mensaje"] = "Ocurrio un error";
                        $this->Model_general->dieMsg($json);
                    }
                }
            }
            $this->db->trans_commit();
            $json["exito"] = true;
            $json["mensaje"] = "Datos guardados con exito";
            $json["id"] = $clie_id;
            $this->Model_general->dieMsg($json);
        } else {
            $json["exito"] = false;
            $json["mensaje"] = "Ocurrio un error";
        }
        echo json_encode($json);
    }
    public function tarifa_editar($clie_id = 0, $serv_id = 0)
    {
        $servicio = $this->Model_general->getServicios($serv_id);
        $cliente = $this->db->where(array("clie_activo" => "1", "clie_id" => $clie_id))->get("cliente")->row();
        $consulta = $this->verificaTarifa($clie_id, $serv_id);
        if ($consulta->num_rows() > 0) {
            $tarifa = $consulta->row();
            $precio = $tarifa->clse_precio;
            $lunch = $tarifa->clse_lunch;
            $moneda = $tarifa->clse_moneda;
        } else {
            $precio = "0.00";
            $lunch = "0.00";
            $moneda = 'SOLES';
        }

        $data["precio"] = $precio;
        $data["lunch"] = $lunch;
        $data["serv_name"] = $servicio->nombre;
        $data["serv_id"] = $serv_id;
        $data["serv_moneda"] = $moneda;
        $data["monedas"] = $this->Model_general->enum_valores('cliente_servicio', 'clse_moneda');
        $data["clie_rsocial"] = $cliente->clie_rsocial;
        $data["clie_id"] = $clie_id;

        $this->load->view('Registro/form_editar_tarifa', $data);
    }
    public function guardar_tarifa($serv_id = 0, $clie_id = 0)
    {
        if ($serv_id != 0 && $clie_id != 0) {
            $precio = $this->input->post("serv_prec");
            $lunch = $this->input->post("serv_lunch");
            $moneda = $this->input->post("moneda");

            $datos = array(
                "clse_clie_id" => $clie_id,
                "clse_serv_id" => $serv_id,
                "clse_precio" => $precio,
                "clse_lunch" => $lunch,
                "clse_moneda" => $moneda
            );

            $consulta = $this->verificaTarifa($clie_id, $serv_id);

            $clie = $this->db->where("clie_id", $clie_id)->get("cliente")->row()->clie_rsocial;
            $serv = $this->db->where("serv_id", $serv_id)->get("servicio")->row()->serv_descripcion;
            if ($consulta->num_rows() > 0) {
                $precio = $consulta->row()->clse_precio;
                $condicion = array("clse_clie_id" => $clie_id, "clse_serv_id" => $serv_id);
                if ($this->Model_general->guardar_edit_registro("cliente_servicio", $datos, $condicion)) {
                    $this->Model_general->add_log("EDITAR", 13, "Edición de Tarifa: " . $clie . " - " . $serv . " / " . $precio . " " . $moneda);
                    $resp["exito"] = true;
                    $resp["mensaje"] = "Tarifa editada con exito";
                    $resp["id"] = $clie_id;
                } else {
                    $resp["exito"] = false;
                    $resp["mensaje"] = "Ocurrio un error";
                }
            } else {
                if ($this->Model_general->guardar_registro("cliente_servicio", $datos)) {
                    $this->Model_general->add_log("CREAR", 13, "Registro de Tarifa: " . $clie . " - " . $serv . " / " . $precio . " " . $moneda);
                    $resp["exito"] = true;
                    $resp["mensaje"] = "Tarifa editada con exito";
                    $resp["id"] = $clie_id;
                } else {
                    $resp["exito"] = false;
                    $resp["mensaje"] = "Ocurrio un error";
                }
            }
        } else {
            $resp["exito"] = false;
            $resp["mensaje"] = "algo salio mal";
        }
        echo json_encode($resp);
    }
    public function buscar_serv()
    {
        $responese = new StdClass;
        $tipo = isset($_GET['t']) ? $_GET["t"] : '';
        $clie = isset($_GET['c']) ? $_GET["c"] : '';
        $this->db->select("serv_id, serv_descripcion");
        $this->db->from("servicio");
        if ($clie != '' && $tipo == "LOCAL")
            $this->db->join("cliente_servicio", "clse_clie_id = '{$clie}' AND clse_serv_id = serv_id");
        $this->db->where("serv_tipo_reserv", $tipo);
        $this->db->or_where("serv_tipo_reserv", 'AMBOS');
        $this->db->order_by("serv_abrev");
        $servicio = $this->db->get()->result();
        /*
        echo $this->db->last_query();
        print_r($servicio);
        exit(0);
        */
        $datos = array();
        foreach ($servicio as $value) {
            $datos[] = array("id" => $value->serv_id, "text" => $value->serv_descripcion);
        }

        $responese->total_count = count($servicio);
        $responese->incomplete_results = false;
        $responese->items = $datos;
        echo json_encode($responese);
    }
    public function buscar_servPrivado()
    {
        $clie = isset($_GET['c']) ? $_GET["c"] : '';
        $search = isset($_GET['q']) ? $_GET["q"] : '';
        $responese = new StdClass;
        $this->db->select("serv_id, serv_descripcion, DATE_FORMAT(serv_hora, '%h:%i %p') as serv_hora, clse_precio as precio");
        $this->db->from("servicio");
        $this->db->join("cliente_servicio", "clse_clie_id = '{$clie}' AND clse_serv_id = serv_id", "LEFT");
        $this->db->like("serv_descripcion", $search);
        $this->db->where("serv_tipo_reserv", "PRIVADO");
        //$this->db->or_where("serv_tipo_reserv", 'AMBOS');
        $this->db->order_by("serv_id", 'DESC');
        $servicio = $this->db->get()->result();
        $datos = array();
        foreach ($servicio as $value) {
            $datos[] = array("id" => $value->serv_id, "text" => $value->serv_descripcion, "precio" => $value->precio, "hora" => $value->serv_hora);
        }

        $responese->total_count = count($servicio);
        $responese->incomplete_results = false;
        $responese->items = $datos;
        echo json_encode($responese);
    }
    public function getServHora($id = '')
    {
        $this->db->select("DATE_FORMAT(serv_hora, '%h:%i %p') as hora");
        $this->db->from("servicio");
        $this->db->where("serv_id", $id);
        echo json_encode($this->db->get()->row());
    }

    public function getServPrecio()
    {
        $cliente = $this->input->post('cliente');
        $servicio = $this->input->post('servicio');

        $clie = $this->db->select("clie_facturacion as fact")->where("clie_id", $cliente)->get("cliente")->row();

        $consulta = $this->verificaTarifa($cliente, $servicio);
        if ($consulta->num_rows() > 0) {
            $consulta = $consulta->row();
            $json['moneda'] = $consulta->clse_moneda;
            $json['precio'] = $consulta->clse_precio;
            $json['lunch_prec'] = $consulta->clse_lunch;
        } else {
            $json['moneda'] = 'SOLES';
            $json['precio'] = 0.00;
            $json['lunch_prec'] = 0.00;
        }
        $json['factu'] = $clie->fact;
        echo json_encode($json);
    }
    public function getSubServOptions()
    {
        $cliente = $this->input->post('cliente');
        $servicio = $this->input->post('servicio');

        $clie = $this->db->select("clie_facturacion as fact")->where("clie_id", $cliente)->get("cliente")->row();

        $consulta = $this->verificaTarifa($cliente, $servicio);

        if ($consulta->num_rows() > 0) {
            $consulta = $consulta->row();

            $this->db->select("clsu_id as id, clsu_descripcion as descripcion, clsu_monto as monto, clsu_moneda as moneda");
            $this->db->where("clsu_clse_servid", $consulta->clse_id);
            $clsu = $this->db->get("cliente_subservicio");
            $html = "";
            if ($clsu->num_rows() > 0) {
                $html .= "<option value=''>* Seleccione sub servicio</option>";
                foreach ($clsu->result() as $val) {
                    $html .= "<option value='" . $val->id . "'>" . $val->descripcion . "</option>";
                }
            }
            $json['sub_serv'] = $html;
        } else {
            $json['sub_serv'] = "";
        }
        $json['factu'] = $clie->fact;
        echo json_encode($json);
    }
    public function getSubServ()
    {
        $id = $this->input->post('sub_servid');
        if ($id != '') {
            $this->db->select("clsu_monto as precio, clsu_descripcion as descripcion, clsu_moneda as moneda");
            $consulta = $this->db->where("clsu_id", $id)->get("cliente_subservicio")->row();
        } else {
            $consulta = false;
        }
        echo json_encode($consulta);
    }
    public function buscar_tserv($value = '')
    {
        $responese = new StdClass;
        $search = isset($_GET['q']) ? $_GET["q"] : '';
        $cliente = isset($_GET['c']) ? $_GET["c"] : '';
        $datos = array();
        $servicio = $this->Model_general->select2("proveedor_tipo", array("tipo_denom" => $search));
        foreach ($servicio["items"] as $value) {
            $datos[] = array("id" => $value->tipo_id, "text" => $value->tipo_denom);
        }

        $responese->total_count = $servicio["total_count"];
        $responese->incomplete_results = false;
        $responese->items = $datos;
        echo json_encode($responese);
    }
    public function buscar_emba($value = '')
    {
        $responese = new StdClass;
        $search = isset($_GET['q']) ? $_GET["q"] : '';
        $datos = array();
        $embarcacion = $this->Model_general->select2("embarcacion", array("emba_nombre" => $search));
        foreach ($embarcacion["items"] as $value) {
            $datos[] = array("id" => $value->emba_id, "text" => $value->emba_nombre);
        }

        $responese->total_count = $embarcacion["total_count"];
        $responese->incomplete_results = false;
        $responese->items = $datos;
        echo json_encode($responese);
    }


    /*
    public function guardar_pago($id = ''){

        $pagado = $this->input->post('pago');
        $total = $this->input->post('total');
        $cancelado = $this->input->post('cancelado');
        $saldo = $this->input->post('saldo');
        $descripcion = $this->input->post('obs');
        $moneda = $this->input->post('moneda');
        $adic_id = $this->input->post('adic_id');
        
        $this->db->trans_start();
        for ($i=0; $i < count($adic_id); $i++) { 
            if($pagado[$i] != ''){
                if($pagado[$i] > $saldo[$i])
                    $pagado[$i] = $saldo[$i] ;
                $datos = array('pago_orde_id' => $id,
                            'pago_orde_adic_id' => $adic_id[$i],
                            'pago_monto' => $pagado[$i],
                            'pago_moneda' => $moneda[$i],
                            'pago_usua' => $this->usua_id,
                            'pago_descripcion' => $descripcion[$i],
                            'pago_fechareg' => date('Y-m-d H:i:s')
                );
                if($this->Model_general->guardar_registro("pago", $datos) != FALSE){
                    if(($cancelado[$i] + $pagado[$i]) >= $total[$i]){
                        if($this->Model_general->guardar_edit_registro("ordv_serv_adicional", array("ordv_adic_pagado" => 'PAGADO'), array('ordv_adic_id' => $adic_id[$i]))){
                            $this->verificaPagoOrden($id);
                        }
                    }
                }
            }
        }
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE){
            $json['exito'] = false;
            $json['mensaje'] = "Error al guardar los datos";
        }else{
            $json['exito'] = true;  
            $json['mensaje'] = "Datos guardados con exito";
        }
        echo json_encode($json);
    }
    */


    public function ver_hojaLiquidacion($id)
    {
        $datos['id'] = $id;
        $this->load->view('Registro/hoja_liquidacion', $datos);
    }
    public function genera_hojaLiquidacion($paqu_id = 0, $file = false)
    {

        $this->db->select("clie_rsocial as cliente, paqu_moneda, paqu_nombre as nombre, DATE_FORMAT(paqu_fecha, '%m-%Y') as fecha, paqu_pax as pax");
        $this->db->from('paquete');
        $this->db->join('cliente', 'clie_id = paqu_clie_id', 'left');
        $this->db->where("paqu_id", $paqu_id);
        $paquete = $this->db->get()->row();
        $simb = ($paquete->paqu_moneda == 'SOLES') ? 'S/ ' : '$ ';
        $detalle = $this->Model_general->getDetaPaqu($paqu_id);

        $this->load->library('pdf');

        $this->pdf = new Pdf();
        $this->pdf->AddPage();
        $this->pdf->AliasNbPages();
        $this->pdf->SetTitle("HOJA DE LIQUIDACIÓN");

        $this->pdf->SetLeftMargin(10);

        $this->pdf->SetFont('Arial', 'B', 8);

        $this->pdf->Cell(35, 8, utf8_decode('HOJA DE LIQUIDACIÓN:'), 0, 0, 'L');
        $this->pdf->Cell(105, 8, utf8_decode(''), 0, 0, 'L');
        $this->pdf->line(45, $this->pdf->GetY() + 5, 147, $this->pdf->GetY() + 5);
        $this->pdf->Cell(15, 8, 'CODIGO:', 0, 0, 'L');
        $this->pdf->line(165, $this->pdf->GetY() + 5, 195, $this->pdf->GetY() + 5);
        $this->pdf->Cell(30, 8, '', 0, 0, 'R');
        $this->pdf->Ln();


        $this->pdf->Cell(20, 8, 'Cliente', 0, 0, 'L');
        $this->pdf->SetFont('');
        $this->pdf->Cell(170, 8, $paquete->cliente, 0, 0, 'L');
        $this->pdf->Ln();
        $this->pdf->SetFont('', 'B', '');
        $this->pdf->Cell(20, 8, 'Nombre', 0, 0, 'L');
        $this->pdf->SetFont('');
        $this->pdf->Cell(120, 8, $paquete->nombre, 0, 0, 'L');
        $this->pdf->SetFont('', 'B', '');
        $this->pdf->Cell(15, 8, 'Firma', 0, 0, 'L');
        $this->pdf->SetFont('');
        $this->pdf->line(165, $this->pdf->GetY() + 5, 195, $this->pdf->GetY() + 5);

        $this->pdf->Ln();
        $this->pdf->SetFont('', 'B', '');
        $this->pdf->Cell(20, 8, '#Pax', 0, 0, 'L');
        $this->pdf->SetFont('');
        $this->pdf->Cell(120, 8, $paquete->pax, 0, 0, 'L');
        $this->pdf->SetFont('', 'B', '');
        $this->pdf->Cell(15, 8, 'Aprobado', 0, 0, 'L');
        $this->pdf->SetFont('');
        $this->pdf->line(165, $this->pdf->GetY() + 5, 195, $this->pdf->GetY() + 5);

        $this->pdf->Ln();
        $this->pdf->SetFont('', 'B', '');
        $this->pdf->Cell(20, 8, utf8_decode('Mes / Año'), 0, 0, 'L');
        $this->pdf->SetFont('');
        $this->pdf->Cell(120, 8, $paquete->fecha, 0, 0, 'L');

        $header = array('Fecha Hora', 'Servicios', 'IMPROTE');
        $w = array(40, 120, 30);
        $this->pdf->Ln(8);
        $this->pdf->SetFont('', 'B', '');
        $this->pdf->SetFillColor('70', '130', '180');
        $this->pdf->SetTextColor(255);
        for ($i = 0; $i < count($header); $i++)
            $this->pdf->Cell($w[$i], 6, $header[$i], 0, 0, 'C', true);
        $this->pdf->Ln();
        $this->pdf->SetFont('');
        $this->pdf->SetTextColor(0);
        $indice = 0;
        $lineas = 0;
        $total = 0;
        foreach ($detalle as $num => $det) {
            $numero = 0;
            preg_match_all("/.{1,90}[^ ]*/", $det->deta_descripcion, $arra);
            $det->deta_descripcion = implode("\r\n", $arra[0]);


            $hline = 7;
            $dess = array();

            if (preg_match("/\n/", $det->deta_descripcion)) { ///  para saltos de linea
                $dess = explode("\n", $det->deta_descripcion);
                $det->deta_descripcion = $dess[0];
                $hline = 3;
                $this->pdf->Ln(2);
            }
            $total += $det->deta_precio = empty($det->deta_precio) ? 0.00 : number_format($det->deta_precio, 2, '.', '');

            $this->pdf->Cell($w[0], $hline, $det->deta_fechaserv, '', 0, 'C');
            $this->pdf->Cell($w[1], $hline, utf8_decode($det->deta_descripcion), '', 0, 'L');
            $this->pdf->Cell($w[2], $hline, $simb . " " . $det->deta_precio, '', 0, 'R');
            $this->pdf->Ln();
            $lineas++;

            if (count($dess) > 0) {
                unset($dess[0]);
                foreach ($dess as $desc) {
                    $this->pdf->Cell($w[0], $hline, '', '', 0, 'C');
                    $this->pdf->Cell($w[1], $hline, utf8_decode($desc), '', 0, 'L');
                    $this->pdf->Cell($w[2], $hline, '', '', 0, 'C');
                    $this->pdf->Ln();
                    $lineas++;
                }
                $this->pdf->Ln(2);
            }
            $this->pdf->line(10, $this->pdf->GetY(), 200, $this->pdf->GetY());
            $indice++;
        }
        $this->pdf->SetTextColor(29, 112, 183);
        $this->pdf->SetFont('', 'B', '');
        $this->pdf->Cell(160, 6, 'TOTAL A PAGAR', 0, 0, 'R');
        $this->pdf->SetFont('');
        $this->pdf->Cell(30, 6, $simb . " " . number_format($total + $liquidacion->saldo, 2, '.', ','), 0, 0, 'R');
        $this->pdf->Ln();

        $archivo = "nombre.pdf";
        if ($file == false) {
            $doc = $this->pdf->Output($archivo, 'S');
            return $doc;
        } else {
            $this->pdf->Output($archivo, 'I');
        }
    }
    function reporte_excelReservas()
    {
        $contacto = $this->input->post("contacto");
        $estado = $this->input->post("estado");
        $usuario = $this->input->post("usuario");
        $esorden = $this->input->post("det_orde");
        $escomprobante = $this->input->post("det_comp");
        $esliquidacion = $this->input->post("det_liqu");
        $escobrado = $this->input->post("paqu_cobrado");
        $search = $this->input->post("search")["value"];
        $tipo = $this->input->post("tipo");
        $serv_ids = $this->input->post("serv_ids");
        $desde = $this->input->post("desde");
        $hasta = $this->input->post("hasta");


        $this->db->select("CONCAT(P.paqu_prefijo,' - ',P.paqu_numero) file, DATE_FORMAT(D.deta_fechaserv,'%d/%m/%Y  %h:%i %p') AS fecha, S.serv_descripcion as servicio, D.deta_pax AS pax, P.paqu_nombre nombre, D.deta_hotel as hotel, D.deta_guia as guia, D.deta_lunch, D.deta_descripcion AS descripcion, C.clie_rsocial as cliente, P.paqu_moneda as moneda, P.paqu_estado as estado, D.deta_precio as precio, P.paqu_endose endose");
        $this->db->from("paquete_detalle D");
        $this->db->join("paquete P", "P.paqu_id = D.deta_paqu_id", "left");
        $this->db->join("cliente C", "C.clie_id = P.paqu_clie_id", "left");
        $this->db->join("servicio S", "S.serv_id = D.deta_serv_id", "left");
        if ($desde != "" && $hasta != "") {
            $this->db->where("D.deta_fechaserv >=", $desde . " 00:00:00");
            $this->db->where("D.deta_fechaserv <=", $hasta . " 23:59:00");
        }
        if ($tipo != "")
            $this->db->where("P.paqu_tipo", $tipo);
        if ($estado != "")
            $this->db->where("P.paqu_estado", $estado);
        if ($contacto != "")
            $this->db->where("P.paqu_clie_id", $contacto);
        if ($escobrado != "")
            $this->db->where("P.paqu_escobrado", $escobrado);
        if ($serv_ids != "") {
            $serv_ids = explode(',', $serv_ids);
            $this->db->where_in("D.deta_serv_id", $serv_ids);
        }
        if ($usuario != "")
            $this->db->where("P.paqu_usuario", $usuario);
        if ($esorden != "") {
            if ($esorden == "SI") $this->db->where("D.deta_esorden", '1');
            else $this->db->where("D.deta_esorden", '0');
        }
        if ($escomprobante != "") {
            if ($escomprobante == "SI") $this->db->where("D.deta_escomprobante", '1');
            else $this->db->where("D.deta_escomprobante", '0');
        }
        if ($esliquidacion != "") {
            if ($esliquidacion == "SI") $this->db->where("D.deta_esliquidacion", '1');
            else $this->db->where("D.deta_esliquidacion", '0');
        }

        $this->db->order_by("file", "DESC");
        $this->db->order_by("D.deta_fechaserv", "ASC");
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
            )
        );

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


        $objPHPExcel->getActiveSheet()->getStyle('D')->applyFromArray($fillgray);
        $objPHPExcel->getActiveSheet()->getStyle('H')->applyFromArray($fillgray);

        $objPHPExcel->getActiveSheet()
            ->setCellValue('A1', 'FILE')
            ->setCellValue('B1', 'FECHA')
            ->setCellValue('C1', 'SERVICIO')
            ->setCellValue('D1', 'PAX')
            ->setCellValue('E1', 'GRUPO / NOMBRE')
            ->setCellValue('F1', 'HOTEL')
            ->setCellValue('G1', 'GUIA')
            ->setCellValue('H1', 'LUNCH')
            ->setCellValue('I1', 'CLIENTE')
            ->setCellValue('J1', 'ENDOSE')
            ->setCellValue('K1', 'OBSERVACION');

        $objPHPExcel->getActiveSheet()->getStyle('F')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
        $objPHPExcel->getActiveSheet()->getStyle('D')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
        $objPHPExcel->getActiveSheet()->freezePane('A2');

        $ini = 3;
        $index = 0;

        foreach ($detalle as $fila) {
            $nro = $index + $ini;
            $index++;
            $objPHPExcel->getActiveSheet()
                ->setCellValue("A$nro", $fila->file)
                ->setCellValue("B$nro", $fila->fecha)
                ->setCellValue("C$nro", $fila->servicio)
                ->setCellValue("D$nro", $fila->pax)
                ->setCellValue("E$nro", $fila->nombre)
                ->setCellValue("F$nro", $fila->hotel)
                ->setCellValue("G$nro", $fila->guia)
                ->setCellValue("H$nro", $fila->deta_lunch)
                ->setCellValue("I$nro", $fila->cliente)
                ->setCellValue("J$nro", $fila->endose)
                ->setCellValue("K$nro", $fila->descripcion);

            if ($fila->estado == "CONFIRMADO")
                $objPHPExcel->getActiveSheet()->getStyle("A$nro")->applyFromArray($bien);
            else if ($fila->estado == "ANULADO")
                $objPHPExcel->getActiveSheet()->getStyle("A$nro")->applyFromArray($mal);
        }


        foreach (range('A', 'K') as $nro)
            $objPHPExcel->getActiveSheet()->getColumnDimension($nro)->setAutoSize(true);

        $fin = $index + $ini - 1;
        //$objPHPExcel->getActiveSheet()->getStyle("F$ini:F$fin")->getNumberFormat()->setFormatCode('#,##0.00'); 
        $objPHPExcel->getActiveSheet()->getStyle("A$ini:A$fin")->getNumberFormat()->setFormatCode('dd/mm/yyyy');


        $excel->excel_output($objPHPExcel, 'RESERVAS ' . $desde . " - " . $hasta);
    }
    function reporte_excelPrivados()
    {

        $contacto = $this->input->post("contacto");
        $desde = $this->input->post("desde");
        $hasta = $this->input->post("hasta");

        $this->db->select("CONCAT(P.paqu_prefijo,' - ',P.paqu_numero) file, DATE_FORMAT(D.deta_fechaserv,'%d/%m/%Y %h:%i %p') AS fecha, S.serv_descripcion as servicio, D.deta_pax AS pax, P.paqu_nombre nombre, D.deta_hotel as hotel, D.deta_guia as guia, D.deta_ruta ruta, D.deta_lugar lugar, D.deta_lunch lunch, D.deta_descripcion AS descripcion, C.clie_rsocial as cliente, P.paqu_moneda as moneda, P.paqu_estado as estado, D.deta_precio as precio, P.paqu_endose endose, D.deta_emba_name embarcacion, (SELECT GROUP_CONCAT(prov_rsocial) FROM servicio_proveedor JOIN proveedor ON prov_id = sepr_prov_id WHERE sepr_pdet_id = deta_id) proveedor");
        $this->db->from("paquete_detalle D");
        $this->db->join("paquete P", "P.paqu_id = D.deta_paqu_id", "left");
        $this->db->join("cliente C", "C.clie_id = P.paqu_clie_id", "left");
        $this->db->join("servicio S", "S.serv_id = D.deta_serv_id", "left");
        if ($desde != "" && $hasta != "") {
            $this->db->where("D.deta_fechaserv >=", $desde . " 00:00:00");
            $this->db->where("D.deta_fechaserv <=", $hasta . " 23:59:00");
        }
        $this->db->where("P.paqu_tipo", "PRIVADO");
        if ($contacto != "")
            $this->db->where("P.paqu_clie_id", $contacto);
        $this->db->order_by("file", "DESC");
        $this->db->order_by("D.deta_fechaserv", "ASC");
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
            )
        );

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


        $objPHPExcel->getActiveSheet()->getStyle('D')->applyFromArray($fillgray);
        $objPHPExcel->getActiveSheet()->getStyle('H')->applyFromArray($fillgray);

        $objPHPExcel->getActiveSheet()
            ->setCellValue('A1', 'FILE')
            ->setCellValue('B1', 'FECHA Y HORA')
            ->setCellValue('C1', 'PAX')
            ->setCellValue('D1', 'CONTACTO')
            ->setCellValue('E1', 'GRUPO / NOMBRE')
            ->setCellValue('F1', 'PROVEEDOR')
            ->setCellValue('G1', 'SERVICIO')
            ->setCellValue('H1', 'EMBERCACION')
            ->setCellValue('I1', 'GUIA')
            ->setCellValue('J1', 'RUTA')
            ->setCellValue('K1', 'LUNCH')
            ->setCellValue('L1', 'LUGAR')
            ->setCellValue('M1', 'HOTEL')
            ->setCellValue('N1', 'OBSERVACION');

        $objPHPExcel->getActiveSheet()->getStyle('F')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
        $objPHPExcel->getActiveSheet()->getStyle('D')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
        $objPHPExcel->getActiveSheet()->freezePane('A2');

        $ini = 3;
        $index = 0;

        foreach ($detalle as $fila) {
            $nro = $index + $ini;
            $index++;
            $objPHPExcel->getActiveSheet()
                ->setCellValue("A$nro", $fila->file)
                ->setCellValue("B$nro", $fila->fecha)
                ->setCellValue("C$nro", $fila->pax)
                ->setCellValue("D$nro", $fila->cliente)
                ->setCellValue("E$nro", $fila->nombre)
                ->setCellValue("F$nro", $fila->proveedor)
                ->setCellValue("G$nro", $fila->servicio)
                ->setCellValue("H$nro", $fila->embarcacion)
                ->setCellValue("I$nro", $fila->guia)
                ->setCellValue("J$nro", $fila->ruta)
                ->setCellValue("K$nro", $fila->lunch)
                ->setCellValue("L$nro", $fila->lugar)
                ->setCellValue("M$nro", $fila->hotel)
                ->setCellValue("N$nro", $fila->descripcion);

            if ($fila->estado == "CONFIRMADO")
                $objPHPExcel->getActiveSheet()->getStyle("A$nro")->applyFromArray($bien);
            else if ($fila->estado == "ANULADO")
                $objPHPExcel->getActiveSheet()->getStyle("A$nro")->applyFromArray($mal);
            //$objPHPExcel->getActiveSheet()->getStyle("A$nro:F$nro")->getAlignment()->setWrapText(true);
        }


        foreach (range('A', 'N') as $nro)
            $objPHPExcel->getActiveSheet()->getColumnDimension($nro)->setAutoSize(true);

        $fin = $index + $ini - 1;
        //$objPHPExcel->getActiveSheet()->getStyle("F$ini:F$fin")->getNumberFormat()->setFormatCode('#,##0.00'); 
        $objPHPExcel->getActiveSheet()->getStyle("A$ini:A$fin")->getNumberFormat()->setFormatCode('dd/mm/yyyy');


        $excel->excel_output($objPHPExcel, 'RESERVAS PRIVADOS ' . $desde . " - " . $hasta);
    }
    function reporte_excelAuxiliar()
    {

        $escomprobante = $this->input->get("det_comp");
        $esliquidacion = $this->input->get("det_liqu");
        $escobrado = $this->input->get("paqu_cobrado");
        $search = $this->input->get("busqueda");
        $tipo = $this->input->get("tipo");
        $contacto = $this->input->get("contacto");
        $desde = $this->input->get("desde");
        $hasta = $this->input->get("hasta");
        $serv_ids = $this->input->get("serv_ids");

        $this->db->select("CONCAT(P.paqu_prefijo,' - ',P.paqu_numero) file, DATE_FORMAT(D.deta_fechaserv,'%d/%m/%Y') AS fecha, S.serv_descripcion as servicio, D.deta_pax AS pax, P.paqu_nombre nombre, D.deta_hotel as hotel, D.deta_guia as guia, P.paqu_file filer, D.deta_lunch lunch, D.deta_descripcion AS descripcion, C.clie_rsocial as cliente, P.paqu_moneda as moneda, P.paqu_estado as estado, D.deta_precio as precio, P.paqu_endose endose, P.paqu_total total, P.paqu_cobrado cobrado, P.paqu_escobrado, DATE_FORMAT(P.paqu_cobrofecha,'%d/%m/%Y') cfecha, P.paqu_cobrodesc cdesc");
        $this->db->from("paquete_detalle D");
        $this->db->join("paquete P", "P.paqu_id = D.deta_paqu_id", "left");
        $this->db->join("cliente C", "C.clie_id = P.paqu_clie_id", "left");
        $this->db->join("servicio S", "S.serv_id = D.deta_serv_id", "left");
        if ($desde != "" && $hasta != "") {
            $this->db->where("D.deta_fechaserv >=", $desde . " 00:00:00");
            $this->db->where("D.deta_fechaserv <=", $hasta . " 23:59:00");
        }
        /*
        if ($search != ""){
            $like = array("P.paqu_nombre" => $search, "file" => $search)
            $this->db->like("P.paqu_tipo",$tipo);
        }
        */
        if ($tipo != "")
            $this->db->where("P.paqu_tipo", $tipo);
        if ($contacto != "")
            $this->db->where("P.paqu_clie_id", $contacto);
        if ($escobrado != "")
            $this->db->where("P.paqu_escobrado", $escobrado);
        if ($serv_ids != "") {
            $serv_ids = explode(',', $serv_ids);
            $this->db->where_in("D.deta_serv_id", $serv_ids);
        }
        if ($escomprobante != "") {
            if ($escomprobante == "SI") $this->db->where("D.deta_escomprobante", '1');
            else $this->db->where("D.deta_escomprobante", '0');
        }
        if ($esliquidacion != "") {
            if ($esliquidacion == "SI") $this->db->where("D.deta_esliquidacion", '1');
            else $this->db->where("D.deta_esliquidacion", '0');
        }

        $this->db->order_by("file", "DESC");
        $this->db->order_by("D.deta_fechaserv", "ASC");
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
            )
        );

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
            )
        );
        $rojo = array(
            'font'  => array(
                'bold'  => true,
                'color' => array('rgb' => 'FF0000')
            )
        );


        $objPHPExcel->getActiveSheet()->getStyle('D')->applyFromArray($fillgray);
        $objPHPExcel->getActiveSheet()->getStyle('H')->applyFromArray($fillgray);

        $objPHPExcel->getActiveSheet()
            ->setCellValue('A1', 'FILE')
            ->setCellValue('B1', 'FECHA')
            ->setCellValue('C1', 'SERVICIO')
            ->setCellValue('D1', 'PAX')
            ->setCellValue('E1', 'GRUPO / NOMBRE')
            ->setCellValue('F1', 'HOTEL')
            ->setCellValue('G1', 'GUIA')
            ->setCellValue('H1', 'LUNCH')
            ->setCellValue('I1', 'FILE/R')
            ->setCellValue('J1', 'CONTACTO')
            ->setCellValue('K1', 'ENDOSE')
            ->setCellValue('L1', 'OBSERVACION')
            ->setCellValue('M1', 'MONEDA')
            ->setCellValue('N1', 'TOTAL')
            ->setCellValue('O1', 'COBRADO')
            ->setCellValue('P1', 'SALDO')
            ->setCellValue('Q1', 'C/FECHA')
            ->setCellValue('R1', 'C/OBS');

        $objPHPExcel->getActiveSheet()->getStyle('F')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
        $objPHPExcel->getActiveSheet()->getStyle('D')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
        $objPHPExcel->getActiveSheet()->freezePane('A2');

        $ini = 3;
        $index = 0;

        foreach ($detalle as $fila) {
            $nro = $index + $ini;
            $index++;
            if ($fila->paqu_escobrado == 0) {
                $saldo = $fila->total - $fila->cobrado;
                $color = $rojo;
            } else {
                $saldo = (($fila->total - $fila->cobrado) < 0) ? $fila->total - $fila->cobrado : "COBRADO";
                $color = $verde;
            }


            $objPHPExcel->getActiveSheet()
                ->setCellValue("A$nro", $fila->file)
                ->setCellValue("B$nro", $fila->fecha)
                ->setCellValue("C$nro", $fila->servicio)
                ->setCellValue("D$nro", $fila->pax)
                ->setCellValue("E$nro", $fila->nombre)
                ->setCellValue("F$nro", $fila->hotel)
                ->setCellValue("G$nro", $fila->guia)
                ->setCellValue("H$nro", $fila->lunch)
                ->setCellValue("I$nro", $fila->filer)
                ->setCellValue("J$nro", $fila->cliente)
                ->setCellValue("K$nro", $fila->endose)
                ->setCellValue("L$nro", $fila->descripcion)
                ->setCellValue("M$nro", $fila->moneda)
                ->setCellValue("N$nro", $fila->total)
                ->setCellValue("O$nro", $fila->cobrado)
                ->setCellValue("P$nro", $saldo)
                ->setCellValue("Q$nro", $fila->cfecha)
                ->setCellValue("R$nro", $fila->cdesc);

            $objPHPExcel->getActiveSheet()->getStyle("P$nro")->applyFromArray($color);

            if ($fila->estado == "CONFIRMADO")
                $objPHPExcel->getActiveSheet()->getStyle("A$nro")->applyFromArray($bien);
            else if ($fila->estado == "ANULADO")
                $objPHPExcel->getActiveSheet()->getStyle("A$nro")->applyFromArray($mal);
        }

        foreach (range('A', 'R') as $nro)
            $objPHPExcel->getActiveSheet()->getColumnDimension($nro)->setAutoSize(true);

        $fin = $index + $ini - 1;
        $objPHPExcel->getActiveSheet()->getStyle("M$ini:O$fin")->getNumberFormat()->setFormatCode('#,##0.00');
        $objPHPExcel->getActiveSheet()->getStyle("B$ini:B$fin")->getNumberFormat()->setFormatCode('dd/mm/yyyy');
        $objPHPExcel->getActiveSheet()->getStyle("Q$ini:Q$fin")->getNumberFormat()->setFormatCode('dd/mm/yyyy');


        $excel->excel_output($objPHPExcel, 'REGISTRO AUXILIAR ' . $desde . " - " . $hasta);
    }
    function reporte_excelCobros()
    {

        $escomprobante = $this->input->get("det_comp");
        $esliquidacion = $this->input->get("det_liqu");
        $escobrado = $this->input->get("paqu_cobrado");
        $search = $this->input->get("busqueda");
        $tipo = $this->input->get("tipo");
        $contacto = $this->input->get("contacto");
        $desde = $this->input->get("desde");
        $hasta = $this->input->get("hasta");
        $serv_ids = $this->input->get("serv_ids");

        /*
        $this->db->select("CONCAT(P.paqu_prefijo,' - ',P.paqu_numero) file, DATE_FORMAT(D.deta_fechaserv,'%d/%m/%Y') AS fecha, 
							S.serv_descripcion as servicio, D.deta_pax AS pax, P.paqu_nombre nombre, D.deta_hotel as hotel, 
							D.deta_guia as guia, P.paqu_file filer, D.deta_lunch lunch, D.deta_descripcion AS descripcion, 
							C.clie_rsocial as cliente, P.paqu_moneda as moneda, P.paqu_estado as estado, D.deta_precio as precio, 
							P.paqu_endose endose, P.paqu_total total, P.paqu_cobrado cobrado, P.paqu_escobrado, 
							DATE_FORMAT(P.paqu_cobrofecha,'%d/%m/%Y') cfecha, P.paqu_cobrodesc cdesc");
							*/
        $this->db->select("P.paqu_id, C.clie_rsocial as cliente, CONCAT(P.paqu_prefijo,' - ',P.paqu_numero) file, P.paqu_endose endose,
							P.paqu_nombre nombre, P.paqu_total total, P.paqu_estado as estado, P.paqu_cobrado cobrado,
							DATE_FORMAT(P.paqu_cobrofecha,'%d/%m/%Y') cfecha, P.paqu_cobrodesc cdesc, P.paqu_escobrado");
        $this->db->from("paquete_detalle D");
        $this->db->join("paquete P", "P.paqu_id = D.deta_paqu_id", "left");
        $this->db->join("cliente C", "C.clie_id = P.paqu_clie_id", "left");
        $this->db->join("servicio S", "S.serv_id = D.deta_serv_id", "left");
        if ($desde != "" && $hasta != "") {
            $this->db->where("D.deta_fechaserv >=", $desde . " 00:00:00");
            $this->db->where("D.deta_fechaserv <=", $hasta . " 23:59:00");
        }
        if ($tipo != "")
            $this->db->where("P.paqu_tipo", $tipo);
        if ($contacto != "")
            $this->db->where("P.paqu_clie_id", $contacto);
        if ($escobrado != "")
            $this->db->where("P.paqu_escobrado", $escobrado);
        if ($serv_ids != "") {
            $serv_ids = explode(',', $serv_ids);
            $this->db->where_in("D.deta_serv_id", $serv_ids);
        }
        if ($escomprobante != "") {
            if ($escomprobante == "SI") $this->db->where("D.deta_escomprobante", '1');
            else $this->db->where("D.deta_escomprobante", '0');
        }
        if ($esliquidacion != "") {
            if ($esliquidacion == "SI") $this->db->where("D.deta_esliquidacion", '1');
            else $this->db->where("D.deta_esliquidacion", '0');
        }
        $this->db->order_by("D.deta_fechaserv", "ASC");
        $this->db->group_by("P.paqu_id");
        $paquetes = $this->db->get()->result();

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
            )
        );

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
            )
        );
        $rojo = array(
            'font'  => array(
                'bold'  => true,
                'color' => array('rgb' => 'FF0000')
            )
        );


        $objPHPExcel->getActiveSheet()->getStyle('D')->applyFromArray($fillgray);
        $objPHPExcel->getActiveSheet()->getStyle('H')->applyFromArray($fillgray);

        $objPHPExcel->getActiveSheet()
            ->setCellValue('A1', 'CONCATO')
            ->setCellValue('B1', 'ENDOSE')
            ->setCellValue('C1', 'FILE')
            ->setCellValue('D1', 'NOMBRE')
            ->setCellValue('E1', 'FECHA')
            ->setCellValue('F1', 'PAX')
            ->setCellValue('G1', 'SERVICIO')
            ->setCellValue('H1', 'TOTAL')
            ->setCellValue('I1', 'TOTAL SUMA')
            ->setCellValue('J1', 'COBRADO')
            ->setCellValue('K1', 'SALDO')
            ->setCellValue('L1', 'COBRO FECHA')
            ->setCellValue('M1', 'COBRO OBS');
        /*
        $objPHPExcel->getActiveSheet()->getStyle('F')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
        $objPHPExcel->getActiveSheet()->getStyle('D')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);      
		*/
        $objPHPExcel->getActiveSheet()->freezePane('A2');

        $ini = 3;
        $index = 0;

        foreach ($paquetes as $fila) {

            $nro = $index + $ini;
            $index++;
            if ($fila->paqu_escobrado == 0) {
                $saldo = $fila->total - $fila->cobrado;
                $color = $rojo;
            } else {
                $saldo = (($fila->total - $fila->cobrado) < 0) ? $fila->total - $fila->cobrado : "COBRADO";
                $color = $verde;
            }

            $this->db->select("DATE_FORMAT(deta_fechaserv, '%d/%m/%Y') fecha, deta_pax pax, deta_servicio servicio, deta_total");
            $this->db->from("paquete_detalle");
            $this->db->where("deta_paqu_id", $fila->paqu_id);
            $detalles = $this->db->get()->result();
            $fecha = "";
            $pax = "";
            $servicio = "";
            $total = "";
            $cant = COUNT($detalles);
            if ($cant > 0) {
                foreach ($detalles as $i => $row) {
                    if (($cant - 1) == $i) $salto = "";
                    else $salto = "\r";
                    $fecha .= $row->fecha . $salto;
                    $pax .= $row->pax . $salto;
                    $servicio .= $row->servicio . $salto;
                    $total .= $row->deta_total . $salto;
                }
            }

            $objPHPExcel->getActiveSheet()
                ->setCellValue("A$nro", $fila->cliente)
                ->setCellValue("B$nro", $fila->endose)
                ->setCellValue("C$nro", $fila->file)
                ->setCellValue("D$nro", $fila->nombre)
                ->setCellValue("E$nro", $fecha)
                ->setCellValue("F$nro", $pax)
                ->setCellValue("G$nro", $servicio)
                ->setCellValue("H$nro", $total)
                ->setCellValue("I$nro", $fila->total)
                ->setCellValue("J$nro", $fila->cobrado)
                ->setCellValue("K$nro", $saldo)
                ->setCellValue("L$nro", $fila->cfecha)
                ->setCellValue("M$nro", $fila->cdesc);
            $objPHPExcel->getActiveSheet()->getStyle("E$nro:H$nro")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->getStyle("J$nro")->applyFromArray($color);

            if ($fila->estado == "CONFIRMADO")
                $objPHPExcel->getActiveSheet()->getStyle("A$nro")->applyFromArray($bien);
            else if ($fila->estado == "ANULADO")
                $objPHPExcel->getActiveSheet()->getStyle("A$nro")->applyFromArray($mal);
        }

        foreach (range('A', 'L') as $nro)
            $objPHPExcel->getActiveSheet()->getColumnDimension($nro)->setAutoSize(true);

        $fin = $index + $ini - 1;
        $objPHPExcel->getActiveSheet()->getStyle("H$ini:K$fin")->getNumberFormat()->setFormatCode('#,##0.00');
        $objPHPExcel->getActiveSheet()->getStyle("E$ini:E$fin")->getNumberFormat()->setFormatCode('dd/mm/yyyy');
        //$objPHPExcel->getActiveSheet()->getStyle("Q$ini:Q$fin")->getNumberFormat()->setFormatCode('dd/mm/yyyy');


        $excel->excel_output($objPHPExcel, 'COBROS ' . $desde . " - " . $hasta);
    }
    public function pago_listado()
    {
        $this->load->helper('Funciones');
        $this->load->library('Ssp');
        $this->load->library('Cssjs');

        $json = isset($_GET['json']) ? $_GET['json'] : false;

        $columns = array(
            array('db' => 'pago_id',            'dt' => 'ID',           "field" => "pago_id"),
            array('db' => 'prov_rsocial',       'dt' => 'Proveedor',    "field" => "prov_rsocial"),
            array('db' => 'ordv_adic_servicio', 'dt' => 'Servicio',     "field" => "ordv_adic_servicio"),
            array('db' => 'DATE_FORMAT(pago_fechareg, "%d/%m/%Y %h:%i %p")',     'dt' => 'Fecha', "field" => 'DATE_FORMAT(pago_fechareg, "%d/%m/%Y %h:%i %p")'),
            array('db' => 'pago_monto',         'dt' => 'Monto',        "field" => "pago_monto"),
            array('db' => 'pago_moneda',        'dt' => 'Moneda',       "field" => "pago_moneda"),
            array('db' => 'pago_descripcion',   'dt' => 'Observacion',  "field" => "pago_descripcion"),
            array('db' => 'pago_id',            'dt' => 'DT_RowId',     "field" => "pago_id")
        );

        if ($json) {

            $json = isset($_GET['json']) ? $_GET['json'] : false;

            $table = 'pago';
            $primaryKey = 'pago_id';

            $sql_details = array(
                'user' => $this->db->username,
                'pass' => $this->db->password,
                'db' => $this->db->database,
                'host' => $this->db->hostname
            );

            $condiciones = array();

            $joinQuery = "FROM pago JOIN ordv_serv_adicional ON ordv_adic_id = pago_orde_adic_id JOIN proveedor ON prov_id = ordv_adic_prov_id";
            $where = "";
            if (!empty($_POST['desde']) && !empty($_POST['hasta'])) {
                $desde = $_POST['desde'] . " 00:00:00";
                $hasta = $_POST['hasta'] . " 23:59:00";
                $condiciones[] = "pago_fechareg BETWEEN  '" . $desde . "' AND '" . $hasta . "'";
            }
            if (!empty($_POST['proveedor']))
                $condiciones[] = "ordv_adic_prov_id='" . $_POST['proveedor'] . "'";
            /*
            if (!empty($_POST['moneda']))
                $condiciones[] = "paqu_moneda='".$_POST['moneda']."'";

            if (!empty($_POST['estado']))
                $condiciones[] = "paqu_estado='".$_POST['estado']."'";
            */

            $where = count($condiciones) > 0 ? implode(' AND ', $condiciones) : "";
            echo json_encode(
                $this->ssp->simple($_POST, $sql_details, $table, $primaryKey, $columns, $joinQuery, $where)
            );
            exit(0);
        }

        $datos["moneda"] = array_merge(array('' => '* Monedas'), $this->Model_general->enum_valores('venta', 'vent_moneda'));
        $datos['columns'] = $columns;
        $datos["contacto"] = $this->Model_general->getOptions('cliente', array("clie_id", "clie_rsocial"), '* Contacto');

        $this->cssjs->add_js(base_url() . 'assets/js/Registro/pago_listado.js?v=1.11', false, false);
        $this->cssjs->add_js(base_url() . 'assets/js/calendar.js', false, false);
        $this->load->view('header');
        $this->load->view('menu');
        $this->load->view($this->router->fetch_class() . '/' . $this->router->fetch_method(), $datos);
        $this->load->view('footer');
    }


    public function detalles_paquete($paqu_id = '')
    {
        $this->db->select("CONCAT(paqu_prefijo,'-',paqu_numero) as file, paqu_clie_rsocial as cliente, paqu_nombre as nombre, 
							DATE_FORMAT(paqu_fecha, '%d/%m/%Y') as fecha, paqu_estado as estado, paqu_total as total, paqu_observacion obs,
							paqu_moneda as moneda, paqu_endose as endose, (paqu_total - paqu_cobrado) saldo, usua_nombres usuario,
							paqu_escomprobante comprobante, paqu_esliquidacion liquidacion, paqu_cobrofecha cobrofecha, paqu_cobrodesc cobrodesc");
        $this->db->from("paquete");
        $this->db->join("usuario", "usua_id = paqu_usuario");
        $this->db->where("paqu_id", $paqu_id);
        $paquete = $this->db->get()->row();

        $simb = ($paquete->moneda == 'SOLES' ? 'S/ ' : '$ ');
        $html = "<div class='col-sm-6'>";

        $html .= "<table class='table table-striped table-bordered tbl-padd'>";
        $html .= "<tr><th>FILE:</th><td>" . $paquete->file . "</td></tr>";
        $html .= "<tr><th style='white-space: nowrap;'>Grupo / Nombre:</th><td>" . $paquete->nombre . "</td></tr>";
        $html .= "<tr><th>Contacto:</th><td>" . $paquete->cliente . "</td></tr>";
        $html .= "<tr><th>Endose:</th><td>" . $paquete->endose . "</td></tr>";
        $html .= "<tr><th>Fecha Registro:</th><td>" . $paquete->fecha . "</td></tr>";
        $html .= "<tr><th>Observación:</th><td>" . $paquete->obs . "</td></tr>";
        $html .= "<tr><th>Registrado por:</th><td>" . ucwords(strtolower($paquete->usuario)) . "</td></tr>";
        $html .= "<tr><th>Estado:</th><td>" . $paquete->estado . "</td></tr>";
        $html .= "<tr><th>Total:</th><td>" . $simb . $paquete->total . "</td></tr>";
        $html .= "<tr><th>Saldo:</th><td>" . $simb . $paquete->saldo . "</td></tr>";
        //$html .= "</table>";

        if ($paquete->comprobante == 1 || $paquete->liquidacion == 1) {
            if ($paquete->comprobante == 1) {
                $cobro = $this->Model_general->getCobros(3, $paqu_id, "comp");
                $html .= "<tr><th>Referencia:</th><td>" . $cobro["ref"] . "</td></tr>";
                if (count($cobro["cobro"]) > 0) {
                    /*
						$html .= "<h5><b>Cobro</b></h5>";
						$html .= "<table class='table table-striped table-bordered'>";
						$html .= "<tr><th>FECHA</th><th>BANCO</th><th>OBSERVACION</th></tr>";
						$html .= "<tr><td>".$paquete->cobrofecha."</td><td>".$cobro->banco."</td><td>".$paquete->cobrodesc."</td></tr>";
						$html .= "</table>";
						*/

                    $html .= "<tr><th colspan='2' class='text-center'>COBRO</th></tr>";
                    $html .= "<tr><th>Fecha:</th><td>" . $paquete->cobrofecha . "</td></tr>";
                    $html .= "<tr><th>Banco:</th><td>" . $cobro["cobro"]->banco . "</td></tr>";
                    //$html .= "<tr><th>Observacion:</th><td>".$paquete->cobrodesc."</td></tr>";
                    $html .= "<tr><th>Observacion:</th><td>" . $cobro["desc"] . "</td></tr>";
                }
                $html .= "</table>";
                $html .= "</div>";
            } else {
                $cobro = $this->Model_general->getCobros(1, $paqu_id, "liqu");
                $html .= "<tr><th>Referencia:</th><td>" . $cobro["ref"] . "</td></tr>";
                if (count($cobro["cobro"]) > 0) {
                    /*
						$html .= "<h5><b>Cobro</b></h5>";
						$html .= "<table class='table table-striped table-bordered'>";
						$html .= "<tr><th>FECHA</th><th>BANCO</th><th>OBSERVACION</th></tr>";
						$html .= "<tr><td>".$paquete->cobrofecha."</td><td>".$cobro->banco."</td><td>".$paquete->cobrodesc."</td></tr>";
						$html .= "</table>";
						*/

                    $html .= "<tr><th colspan='2' class='text-center'>COBRO</th></tr>";
                    $html .= "<tr><th>Fecha:</th><td>" . $paquete->cobrofecha . "</td></tr>";
                    $html .= "<tr><th>Banco:</th><td>" . $cobro["cobro"]->banco . "</td></tr>";
                    //$html .= "<tr><th>Observacion:</th><td>".$paquete->cobrodesc."</td></tr>";
                    $html .= "<tr><th>Observacion:</th><td>" . $cobro["desc"] . "</td></tr>";
                }
                $html .= "</table>";
                $html .= "</div>";
            }
        } else {
            $cobros = $this->Model_general->getCobros(4, $paqu_id);
            $html .= "</table>";
            if (count($cobros) > 0) {
                $html .= "<h5><b>Cobros</b></h5>";
                $html .= "<table class='table table-striped table-bordered'>";
                $html .= "<tr><th>Fecha</th><th>Monto</th><th>Banco</th><th>Obs.</th></tr>";
                foreach ($cobros as $cobro) {
                    $html .= "<tr><td>" . $cobro->fecha . "</td><td>" . $cobro->monto . "</td><td>" . $cobro->banco . "</td><td>" . $cobro->obs . "</td></tr>";
                }
                $html .= "</table>";
            }
            $html .= "</div>";
        }
        $imagenes = $this->Model_general->getImagenes($paqu_id);



        if (count($imagenes) > 0) {
            $html .= "<div class='col-sm-6'>";
            foreach ($imagenes as $img) {
                $direccion = base_url() . 'assets/img/files/' . $img->direccion;
                $img = '<a title="Ver en tamaño completo" target="_blank" href="' . $direccion . '"><img style="width: 100%;" src="' . $direccion . '" class="img-fluid"></a>';
                $html .= $img;
            }
            $html .= "</div>";
        }
        /*
        $detas = $this->Model_general->getDetaPaqu2($paqu_id);
        $html = "";

        foreach ($detas as $i => $det) {
            $html .= "<tr>";
            $html .= "<td>".($i+1)."</td>";
            $html .= "<td>".$det->deta_serv_name."</td>";
            $html .= "<td>".$det->deta_pax."</td>";
            $html .= "<td>".$det->deta_hotel."</td>";
            $html .= "<td>".$det->deta_guia."</td>";
            $html .= "<td>".$det->deta_lunch."</td>";
            $html .= "</tr>";
        }
        */
        $data["html"] = $html;
        $this->load->view("Registro/deta_paqu", $data);
    }
    public function paq_listPrivados($value = '')
    {
        $this->load->helper('Funciones');
        $this->load->library('Ssp');
        $this->load->library('Cssjs');
        $json = isset($_GET['json']) ? $_GET['json'] : false;


        //$fecha = 'DATE_FORMAT(deta_fechaserv,"%d/%m/%Y")';
        $fecha = 'DATE_FORMAT(deta_fechaserv,"%Y-%m-%d")';
        $hora = 'DATE_FORMAT(deta_fechaserv,"%h:%i %p")';
        $grupo = 'paqu_nombre';
        /*
        $servicio = 'GROUP_CONCAT(deta_servicio)';
        $hotel = 'GROUP_CONCAT(deta_hotel)';
        $guia = 'GROUP_CONCAT(deta_guia)';
        */
        $file = 'CONCAT(paqu_prefijo,"-",paqu_numero)';
        $cobrado = "IF(paqu_escobrado = '0','<font class=red><b>PENDIENTE','<font class=green><b>COBRADO')";
        $pagado = "IF(paqu_espagado = '0','<font class=red><b>PENDIENTE','<font class=green><b>PAGADO')";
        $obs = 'GROUP_CONCAT(deta_descripcion)';
        $saldo = "IF(paqu_escobrado = '0',CONCAT('<font class=red><b>',(paqu_total - paqu_cobrado)),CONCAT('<font class=green><b>',IF((paqu_total - paqu_cobrado) < 0,(paqu_total - paqu_cobrado),'COBRADO')))";
        $servicio = "CONCAT(deta_servicio,'</br><strong>',deta_emba_name)";
        $proveedor = "(SELECT GROUP_CONCAT(prov_rsocial) FROM servicio_proveedor JOIN proveedor ON prov_id = sepr_prov_id WHERE sepr_pdet_id = deta_id)";

        $columns = array(
            array('db' => 'deta_id',            'dt' => 'ID',               "field" => "deta_id"),
            array('db' => $fecha,               'dt' => 'FECHA',            "field" => $fecha),
            array('db' => $file,                'dt' => 'FILE',             "field" => $file),
            array('db' => 'deta_pax',           'dt' => 'PAX',              "field" => "deta_pax"),
            array('db' => 'paqu_clie_rsocial',  'dt' => 'CONTACTO',         "field" => 'paqu_clie_rsocial'),
            array('db' => $grupo,               'dt' => 'GRUPO / NOMBRE',   "field" => $grupo),
            array('db' => $proveedor,           'dt' => 'PROVEEDOR',        "field" => $proveedor),
            array('db' => $servicio,            'dt' => 'SERVICIO',         "field" => $servicio),
            array('db' => "deta_guia",          'dt' => 'GUIA',             "field" => "deta_guia"),
            array('db' => "deta_ruta",          'dt' => 'RUTA',             "field" => "deta_ruta"),
            array('db' => "deta_lunch",         'dt' => 'LUNCH',            "field" => "deta_lunch"),
            array('db' => $hora,                'dt' => 'HORA',             "field" => $hora),
            array('db' => "deta_lugar",         'dt' => 'LUGAR',            "field" => "deta_lugar"),
            array('db' => "deta_hotel",         'dt' => 'HOTEL',            "field" => "deta_hotel"),
            array('db' => "deta_descripcion",   'dt' => 'OBS',              "field" => "deta_descripcion"),
            array('db' => 'paqu_escobrado',     'dt' => 'DT_Cobro',         "field" => "paqu_escobrado"),
            array('db' => 'paqu_espagado',      'dt' => 'DT_Pago',          "field" => "paqu_espagado"),
            array('db' => 'deta_escomprobante', 'dt' => 'DT_RowComp',       "field" => "deta_escomprobante"),
            array('db' => 'deta_esliquidacion', 'dt' => 'DT_RowLiqu',       "field" => "deta_esliquidacion"),
            array('db' => 'deta_esorden',       'dt' => 'DT_RowOrde',       "field" => "deta_esorden"),
            array('db' => 'paqu_id',            'dt' => 'DT_RowId',         "field" => "paqu_id"),
            array('db' => 'paqu_tipo',          'dt' => 'DT_RowTipo',       "field" => "paqu_tipo"),
            array('db' => 'paqu_estado',        'dt' => 'DT_RowEsta',       "field" => "paqu_estado"),
            array('db' => 'paqu_id',            'dt' => 'DT_PaquId',        "field" => "paqu_id")
        );

        if ($json) {

            $json = isset($_GET['json']) ? $_GET['json'] : false;

            $table = 'paquete_detalle';
            $primaryKey = 'deta_id';

            $sql_details = array(
                'user' => $this->db->username,
                'pass' => $this->db->password,
                'db' => $this->db->database,
                'host' => $this->db->hostname
            );

            $condiciones = array();

            $joinQuery = "FROM paquete_detalle LEFT JOIN paquete ON deta_paqu_id = paqu_id";
            $where = "";
            if (!empty($_POST['desde']) && !empty($_POST['hasta'])) {
                $condiciones[] = "deta_fechaserv >='" . $_POST['desde'] . " 00:00:00" . "' AND deta_fechaserv <='" . $_POST['hasta'] . " 23:59:00" . "'";
            }
            if (!empty($_POST['moneda']))
                $condiciones[] = "paqu_moneda='" . $_POST['moneda'] . "'";
            if (!empty($_POST['contacto']))
                $condiciones[] = "paqu_clie_id='" . $_POST['contacto'] . "'";
            if (!empty($_POST['usuario']))
                $condiciones[] = "paqu_usua_id='" . $_POST['usuario'] . "'";
            if (!empty($_POST['det_orde']))
                $condiciones[] = "deta_esorden='" . $_POST['det_orde'] . "'";
            if (!empty($_POST['det_comp']))
                $condiciones[] = "deta_escomprobante='" . $_POST['det_comp'] . "'";
            if (!empty($_POST['det_liqu']))
                $condiciones[] = "deta_esliquidacion='" . $_POST['det_liqu'] . "'";
            $condiciones[] = "paqu_tipo = 'PRIVADO'";

            $where = count($condiciones) > 0 ? implode(' AND ', $condiciones) : "";
            echo json_encode(
                $this->ssp->simple($_POST, $sql_details, $table, $primaryKey, $columns, $joinQuery, $where)
            );
            exit(0);
        }
        $datos["usuario"] = $this->Model_general->getOptions('usuario', array("usua_id", "usua_nombres"), '* Usuario');
        $datos["contacto"] = $this->Model_general->getOptions('cliente', array("clie_id", "clie_rsocial"), '* Contacto');
        //$datos["guia"] = $this->Model_general->getOptions('guia', array("guia_id", "guia_nombres"),'* Guia');
        $datos["servicio"] = $this->Model_general->getOptions('servicio', array("serv_id", "serv_descripcion"), '* Servicio');
        $datos["moneda"] = array_merge(array('' => '* Monedas'), $this->Model_general->enum_valores('paquete', 'paqu_moneda'));
        $datos["tipo"] = array_merge(array('' => '* Tipo reserva'), $this->Model_general->enum_valores('paquete', 'paqu_tipo'));
        $datos["estado"] = array_merge(array('' => '* Estado'), $this->Model_general->enum_valores('paquete', 'paqu_estado'));
        $datos["servicios"] = $this->Model_general->getData("servicio", array("serv_id", "serv_abrev"));
        $datos['columns'] = $columns;
        $datos['poststr'] = isset($_SESSION['poststr']) ? unserialize($_SESSION['poststr']) : array(
            'usuario' => '',
            'contacto' => '',
            'scontacto' => '',
            'estado' => '',
            'usuario' => '',
            'det_orde' => '',
            'det_comp' => '',
            'det_liqu' => '',
            'search' => array('value' => ''),
            'tipo' => $usua_tipo,
            'serv_ids' => '',
            'desde' => date('Y-m-d', time() - 24 * 60 * 60 * 7),
            'hasta' => date('Y-m-d'),
        );
        $this->cssjs->add_js(base_url() . 'assets/js/Registro/list_privado.js', false, false);
        $this->cssjs->add_js(base_url() . 'assets/js/calendar.js', false, false);
        $this->load->view('header');
        $this->load->view('menu');
        $this->load->view($this->router->fetch_class() . '/' . $this->router->fetch_method(), $datos);
        $this->load->view('footer');
    }
    function actualiza_ordenes()
    {
        /*
        $this->db->select("PD.deta_id as pdet_id, CONCAT(P.paqu_prefijo,'-',P.paqu_numero) as file, P.paqu_id as paqu_id, OD.deta_id as odet_id");
        $this->db->from("paquete_detalle PD");
        $this->db->join("paquete P","P.paqu_id = PD.deta_paqu_id");
        $this->db->join("ordserv_detalle OD","OD.deta_pdet_id = PD.deta_id");
        $this->db->where("PD.deta_esorden","0");
        $detalles = $this->db->get()->result();
        foreach($detalles as $row){
            $condicion = array("deta_id" => $row->pdet_id);
            $datas = array("deta_esorden" => 1);
            if($this->Model_general->guardar_edit_registro("paquete_detalle", $datas, $condicion))
                echo "actualizado con exito";
            else
                echo "no se pudo actualizar we";
            echo "</br>";
        }
        */
    }
    public function verificaFechas()
    {
        $paquetes = $this->db->query("SELECT concat(paqu_prefijo,'-',paqu_numero) as file, paqu_numero, deta_servicio, deta_fechaserv FROM paquete_detalle INNER JOIN paquete ON paqu_id = deta_paqu_id where deta_fechaserv IS NULL")->result();
        $html = "<table border=1 cellspacing=0 cellpadding=2 bordercolor='666633'>";
        $html .= "<tr><th>USUARIO</th><th>ACCION</th><th>FECHA</th><th>FILE</th><th>DESCRIPCION</th></tr>";
        foreach ($paquetes as $row) {
            $this->db->select("usua_nombres nombre, log_accion acc, log_fecha fecha, log_descripcion desc");
            $this->db->from("log");
            $this->db->join("usuario", "usua_id = log_user_id");
            $this->db->like("log_descripcion", $row->paqu_numero);
            $log = $this->db->get()->row();
            $html .= "<tr>";
            $html .= "<td>" . $log->nombre . "</td>";
            $html .= "<td>" . $log->acc . "</td>";
            $html .= "<td>" . $log->fecha . "</td>";
            $html .= "<td>" . $row->file . "</td>";
            $html .= "<td>" . $log->desc . "</td>";
            $html .= "</tr>";
            //echo $log->nombre." : ".$log->acc." : ".$log->fecha." : ".$log->desc."</br>";
        }
        echo $html;
    }
}
