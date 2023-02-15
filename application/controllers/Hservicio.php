<?php 
class Hservicio extends CI_Controller
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
        $this->editar = $this->permisos[1]->nivel_acceso;
    }
    public function hser_listado(){
        $this->load->helper('Funciones');
        $this->load->library('Ssp');
        $this->load->library('Cssjs');

        $json = isset($_GET['json']) ? $_GET['json'] : false;
        $this->db->where("serv_tipo_reserv <>","PRIVADO");
        $this->db->where("serv_id <>","19");
        $servicios = $this->db->get("servicio")->result();

        if ($json) {
            $fecha = $this->Model_general->fecha_to_mysql($this->input->post('fecha'));
            $resp = array();
            foreach ($servicios as $i => $serv) {
                $html = "";

                $detas = $this->Model_general->get_DetaServ($serv->serv_id, $fecha);

                if($detas->num_rows() > 0){
                    $pax = 0;
                    foreach ($detas->result() as $det) {
                        $estado = $det->llegada;
                        if($det->llegada == '0'){
                            $conf["llegada"] = "";
                        }else{
                            $conf["llegada"] = "row_llegada";
                        }
                        if($this->editar > 1){
                            $estado = "<button data-id = '".$det->id."' class='btn btn-xs change_estado btn-primary' type='button'><span class='glyphicon glyphicon-edit'></span></button>";    
                        }else{
                            $estado = "";
                        }
                        $pax += $det->pax;
                        
                        switch ($det->prioridad) {
                            case 2: $prio = "<span style='color: #FF8C00' class='glyphicon glyphicon-star-empty'></span>"; break;
                            case 3: $prio = "<span style='color: #FF8C00' class='glyphicon glyphicon-star'></span>"; break;
                            default: $prio = ""; break;
                        }
                        
                        $html .= "<tr class='".$conf["llegada"]."'>";
                        $html .= "<td class='col-sm-1'>".$det->pax."</td>";
                        $html .= "<td>".$prio.$det->nombre."</td>";
                        $html .= "<td>".$det->guia."</td>";
                        $html .= "<td>".$det->hotel."</td>";
                        $html .= "<td class='col-sm-1'>".$det->lunch."</td>";
                        $contacto = ($det->clie_id == 69)?"SR / ".$det->endose:$det->contacto;
                        $html .= "<td>".$contacto."</br><small class='text-danger'>".strtolower($det->descripcion)."</small></td>";
                        $html .= "<td>".$estado."</td>";
                        $html .= "</tr>";
                    }
                    $html .= "<tr>";
                    $html .= "<th>".$pax."</th>";
                    $html .= "<td colspan='5'></td>";
                    $html .= "</tr>";
                }
                $resp[] = array_merge(array("serv_id" => $serv->serv_id, "serv_name" => $serv->serv_descripcion), array("html" => $html));
            }

            $this->db->where("serv_tipo_reserv","PRIVADO");
            $this->db->or_where("serv_id","19");
            $privados = $this->db->get("servicio")->result();
            $htmlp = "";
            $pax = 0;
            foreach ($privados as $i => $serv) {
                $detas = $this->Model_general->get_DetaServ($serv->serv_id, $fecha);
                if($detas->num_rows() > 0){
                    foreach ($detas->result() as $det) {
                        $estado = $det->llegada;
                        if($det->llegada == '0'){
                            $conf["llegada"] = "";
                        }else{
                            $conf["llegada"] = "row_llegada";
                        }
                        $estado = "<button data-id = '".$det->id."' class='btn btn-xs change_estado btn-primary' type='button'><span class='glyphicon glyphicon-edit'></span></button>";
                        $pax += $det->pax;
                        
                        switch ($det->prioridad) {
                            case 2: $prio = "<span style='color: #FF8C00' class='glyphicon glyphicon-star-empty'></span>"; break;
                            case 3: $prio = "<span style='color: #FF8C00' class='glyphicon glyphicon-star'></span>"; break;
                            default: $prio = ""; break;
                        }
                        
                        $htmlp .= "<tr class='".$conf["llegada"]."'>";
                        $htmlp .= "<td class='col-sm-1'>".$det->pax."</td>";
                        $htmlp .= "<td>".$prio.$det->nombre."</td>";
                        $htmlp .= "<td>".$det->guia."</td>";
                        $htmlp .= "<td>".$det->hotel."</td>";
                        $htmlp .= "<td class='col-sm-1'>".$det->lunch."</td>";
                        $contacto = ($det->clie_id == 69)?"SR / ".$det->endose:$det->contacto;
                        $htmlp .= "<td>".$contacto."</td>";
                        $htmlp .= "<td>".$estado."</td>";
                        $htmlp .= "</tr>";
                    }
                }
            }
            if($pax == 0){
                $htmlp .= "<tr><th colspan='7'>No hay reservas para este servicio</th></tr>";
            }else{
                $htmlp .= "<tr>";
                $htmlp .= "<th>".$pax."</th>";
                $htmlp .= "<td colspan='6'></td>";
                $htmlp .= "</tr>";
            }

            $privados = array_merge(array("serv_name" => "PRIVADOS"), array("html" => $htmlp));
            $data["resp"] = $resp;
            $data["privados"] = $privados;

            echo json_encode($data);
            exit(0);
        }
        $datos["servicio"] = $servicios;
        $datos["titulo"] = "Hoja de Servicio";
        $datos["tbl_head"] = array("PAX","NOMBRE","GUIA","HOTEL","ALM","CONTACTO","");
        $fecha = date('Y-m-d');
        $maniana = strtotime ( '+1 day' , strtotime ( $fecha ) ) ;
        $maniana = date ( 'd/m/Y' , $maniana );
        $datos["fecha"] = $maniana;

        $this->cssjs->add_js(base_url().'assets/js/Hservicio/hser_listado.js?v=1.0',false,false);
        $this->cssjs->add_js(base_url().'assets/js/calendar.js',false,false);
        $this->load->view('header');
        $this->load->view('menu');
        $this->load->view($this->router->fetch_class().'/'.$this->router->fetch_method(), $datos);
        $this->load->view('footer');
    }

    public function hser_change_estado(){
        $id = $this->input->post("id");
        $this->db->select("deta_llegada as llegada")->where("deta_id", $id);
        $deta = $this->db->get("paquete_detalle")->row();
        if($deta->llegada == '0'){
            $estado = 1;
        }else{
            $estado = 0;
        }
        $where = array("deta_id" => $id);
        $datas = array("deta_llegada" => $estado);

        if($this->Model_general->guardar_edit_registro("paquete_detalle", $datas, $where)){
            $resp["exito"] = true;
        }else{
            $resp["exito"] = false;
        }
        $resp["estado"] = $estado;
        echo json_encode($resp);
    }
    public function reporte_excel_hojaserv(){

        $fechal = $this->input->get('fecha');

        $this->db->where("serv_tipo_reserv <>","PRIVADO");
        $this->db->where("serv_id <>","19");
        $servicios = $this->db->get("servicio")->result();

        $fecha = $this->Model_general->fecha_to_mysql($fechal);

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
        $bordes = array('borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN,)));

        $fillgray = array(
            'fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID,'color' => array('rgb' => 'E4E7E9'))
        );
        $c1 = array(
            'fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID,'color' => array('rgb' => 'C6EFCE'))
        );
        $c2 = array(
            'fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID,'color' => array('rgb' => 'FFC7CE'))
        );
        $c3 = array(
            'fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID,'color' => array('rgb' => 'FFEB9C'))
        );
        $c4 = array(
            'fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID,'color' => array('rgb' => 'FAC08F'))
        );
        $center = array(
            'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER),
            'font' => array('bold' => true)
        );
        $rojo = array(
            'font'  => array(
                'bold'  => true,
                'color' => array('rgb' => 'FF0000')
        ));
        $naranja = array(
            'font'  => array(
                'bold'  => true,
                'color' => array('rgb' => 'FE9A2E')
        ));
        
        $objPHPExcel->getActiveSheet()
                    ->setCellValue("B2", 'FECHA')
                    ->setCellValue("C2", $fechal);

        $pos = 4;
        $pos_cab = 4;
        foreach ($servicios as $i => $serv) {

            $detas = $this->Model_general->get_DetaServ($serv->serv_id, $fecha)->result();
            
            if($i%2==0){
                $left = count($detas);
                $letras = array("A","B","C","D","E","F");

            }else{
                $right = count($detas);
                $letras = array("I","J","K","L","M","N");
            }
            $objPHPExcel->getActiveSheet()->setCellValue("$letras[0]$pos_cab", $serv->serv_descripcion);
            $objPHPExcel->setActiveSheetIndex(0)->mergeCells("$letras[0]$pos_cab:$letras[5]$pos_cab");
            $color = "c".($i%4+1);
            $objPHPExcel->getActiveSheet()->getStyle("$letras[0]$pos_cab:$letras[5]$pos_cab")->applyFromArray($$color);
            $objPHPExcel->getActiveSheet()->getStyle("$letras[0]$pos_cab:$letras[5]$pos_cab")->applyFromArray($center);

            $pos_cab++;
            $objPHPExcel->getActiveSheet()
                    ->setCellValue("$letras[0]$pos_cab", 'PAX')
                    ->setCellValue("$letras[1]$pos_cab", 'NOMBRE')
                    ->setCellValue("$letras[2]$pos_cab", 'GUIA')
                    ->setCellValue("$letras[3]$pos_cab", 'HOTEL')
                    ->setCellValue("$letras[4]$pos_cab", 'ALM')
                    ->setCellValue("$letras[5]$pos_cab", 'CONTACTO');
            $objPHPExcel->getActiveSheet()->getStyle("$letras[0]$pos_cab:$letras[5]$pos_cab")->applyFromArray($center);
            

            $pos = $pos_cab+1;
            if(count($detas) > 0){
                $pax = 0;
                foreach ($detas as $det) {
                    $from = "$letras[0]$pos";
                    $to = "$letras[5]$pos";
                    if($det->prioridad > 1 || strlen ($det->guia) > 1){
                        $objPHPExcel->getActiveSheet()->getStyle("$from:$to")->getFont()->setBold( true );
                        $objPHPExcel->getActiveSheet()->getStyle("$from:$to")->applyFromArray($rojo);
                    }
                    $pax += $det->pax;
                    $objPHPExcel->getActiveSheet()
                        ->setCellValue("$letras[0]$pos", $det->pax)
                        ->setCellValue("$letras[1]$pos", $det->nombre)
                        ->setCellValue("$letras[2]$pos", $det->guia)
                        ->setCellValue("$letras[3]$pos", $det->hotel)
                        ->setCellValue("$letras[4]$pos", $det->lunch);
						
						$objRichText = new PHPExcel_RichText();
                        $run1 = $objRichText->createTextRun(($det->clie_id == 69?"SR / ".$det->endose:$det->contacto));
                        $run1->getFont()->setColor( new PHPExcel_Style_Color( PHPExcel_Style_Color::COLOR_BLACK ) );

                        $run2 = $objRichText->createTextRun(($det->descripcion != ""?"\r".strtolower($det->descripcion):""));
                        $run2->getFont()->setColor( new PHPExcel_Style_Color( PHPExcel_Style_Color::COLOR_RED ) );

                        $objPHPExcel->setActiveSheetIndex(0)->setCellValue("$letras[5]$pos", $objRichText)->getStyle("$letras[5]$pos")->getAlignment()->setWrapText(true);
						/*
                        ->setCellValue("$letras[5]$pos", ($det->clie_id == 69?"SR / ".$det->endose:$det->contacto).($det->descripcion != ""?"\r".strtolower($det->descripcion):""))
                         ->getStyle("$letras[5]$pos")->getAlignment()->setWrapText(true);
						 */
                         /*
                    if($det->prioridad == 2)
                        $objPHPExcel->getActiveSheet()->getStyle("$from:$to")->applyFromArray($naranja);
                    else if($det->prioridad == 3)
                        $objPHPExcel->getActiveSheet()->getStyle("$from:$to")->applyFromArray($rojo);
                        */
                    $pos++;
                }
                $objPHPExcel->getActiveSheet()->setCellValue("$letras[0]$pos", $pax);
                $objPHPExcel->getActiveSheet()->getStyle("$letras[0]$pos:$letras[5]$pos")->applyFromArray($fillgray);
            }else{
                $objPHPExcel->getActiveSheet()->setCellValue("$letras[0]$pos", "No hay reservas para esta fecha");
                $objPHPExcel->setActiveSheetIndex(0)->mergeCells("$letras[0]$pos:$letras[5]$pos");
                $objPHPExcel->getActiveSheet()->getStyle("$letras[0]$pos:$letras[5]$pos")->applyFromArray($fillgray);
            }
            if($i%2!=0){
                if($left > $right) $new_pos = $left+2;
                else $new_pos = $right+2;
                $pos_cab += $new_pos;
            }

            foreach(range('A','N') as $nro){
                $objPHPExcel->getActiveSheet()->getColumnDimension($nro)->setAutoSize(true);
            }
        }

        

        $this->db->where("serv_tipo_reserv","PRIVADO");
        $this->db->or_where("serv_id","19");
        $privados = $this->db->get("servicio")->result();
        
        $pos = $pos_cab;

        
        $letras = array("A","B","C","D","E","F");

        $objPHPExcel->getActiveSheet()->setCellValue("$letras[0]$pos_cab", "PRIVADOS");
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells("$letras[0]$pos_cab:$letras[5]$pos_cab");
        $color = "c4";
        $objPHPExcel->getActiveSheet()->getStyle("$letras[0]$pos_cab:$letras[5]$pos_cab")->applyFromArray($$color);
        $objPHPExcel->getActiveSheet()->getStyle("$letras[0]$pos_cab:$letras[5]$pos_cab")->applyFromArray($center);

        $pos_cab++;
        $objPHPExcel->getActiveSheet()
                ->setCellValue("$letras[0]$pos_cab", 'PAX')
                ->setCellValue("$letras[1]$pos_cab", 'NOMBRE')
                ->setCellValue("$letras[2]$pos_cab", 'GUIA')
                ->setCellValue("$letras[3]$pos_cab", 'HOTEL')
                ->setCellValue("$letras[4]$pos_cab", 'ALM')
                ->setCellValue("$letras[5]$pos_cab", 'CONTACTO');
        $objPHPExcel->getActiveSheet()->getStyle("$letras[0]$pos_cab:$letras[5]$pos_cab")->applyFromArray($center);
        $pax = 0;
        $pos = $pos_cab+1;
        foreach ($privados as $i => $serv) {
            $detas = $this->Model_general->get_DetaServ($serv->serv_id, $fecha)->result();
            
            if(count($detas) > 0){
                foreach ($detas as $det) {
                    $from = "$letras[0]$pos";
                    $to = "$letras[5]$pos";
                    if($det->prioridad > 1 || strlen ($det->guia) > 1){
                        $objPHPExcel->getActiveSheet()->getStyle("$from:$to")->getFont()->setBold( true );
                    }
                    $pax += $det->pax;
                    $objPHPExcel->getActiveSheet()
                        ->setCellValue("$letras[0]$pos", $det->pax)
                        ->setCellValue("$letras[1]$pos", $det->nombre)
                        ->setCellValue("$letras[2]$pos", $det->guia)
                        ->setCellValue("$letras[3]$pos", $det->hotel)
                        ->setCellValue("$letras[4]$pos", $det->lunch);
						$objRichText = new PHPExcel_RichText();
                        $run1 = $objRichText->createTextRun(($det->clie_id == 69?"SR / ".$det->endose:$det->contacto));
                        $run1->getFont()->setColor( new PHPExcel_Style_Color( PHPExcel_Style_Color::COLOR_BLACK ) );

                        $run2 = $objRichText->createTextRun(($det->descripcion != ""?"\r".strtolower($det->descripcion):""));
                        $run2->getFont()->setColor( new PHPExcel_Style_Color( PHPExcel_Style_Color::COLOR_RED ) );

                        $objPHPExcel->setActiveSheetIndex(0)->setCellValue("$letras[5]$pos", $objRichText)->getStyle("$letras[5]$pos")->getAlignment()->setWrapText(true);
                        //->setCellValue("$letras[5]$pos", ($det->clie_id == 69)?"SR / ".$det->endose:$det->contacto);
                    $pos++;
                }    
            }
        }
        if($pax == 0){
            $objPHPExcel->getActiveSheet()->setCellValue("$letras[0]$pos", "No hay reservas para esta fecha");
            $objPHPExcel->setActiveSheetIndex(0)->mergeCells("$letras[0]$pos:$letras[5]$pos");
        }else{
            $objPHPExcel->getActiveSheet()->setCellValue("$letras[0]$pos", $pax);
        }
        $objPHPExcel->getActiveSheet()->getStyle("$letras[0]$pos:$letras[5]$pos")->applyFromArray($fillgray);
        
        $fin = $pos_cab-1;
        
        $objPHPExcel->getActiveSheet()->getStyle("I$pos_cab:I$fin")->getNumberFormat()->setFormatCode('#,##0.00'); 
        $objPHPExcel->getActiveSheet()->getStyle("A$pos_cab:A$fin")->getNumberFormat()->setFormatCode('dd/mm/yyyy');
        
        $excel->excel_output($objPHPExcel, 'Hoja de servicios '.$fechal);
    }
    
}